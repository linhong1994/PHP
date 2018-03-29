<?php
namespace app\controller;
use think\Controller;
use think\Db;
use think\Session;
use app\model\Userinfo;
use app\model\Sysset;
use app\model\Wx_config;
class Common extends Controller
{
//初始化
public function _initialize()
{

	
	//$map['userid']='U000000';
	//$user = Db::name('userinfo')->where($map)->find();
  //session(null);
  $url=request()->url();
  if($url != '/notify.html' && $url != '/qrcode.html'){
		if(session('openid') == null)
		{
			$this->reget_web_access_token();
		}
		$map['wxopenid']=session('openid');
		$user= new Userinfo();
		if($user->where($map)->count() == 0){//是否关注  如果关注  更新用户信息
			$this->set_web_user_info();//如果没关注 获取设置用户信息
		}
		$user=$user->where($map)->field('password',true)->find();
		session('user',$user);
		$sys=new Sysset();
		$sys=$sys->where('rid',1)->find();
		session('sys',$sys);
	}
/*	//设置此页面的过期时间(用格林威治时间表示)，只要是已经过去的日期即可。 
	header("Expires: Mon, 26 Jul 1970 05:00:00 GMT"); 
	
	 //设置此页面的最后更新日期(用格林威治时间表示)为当天，可以强制浏览器获取最新资料 
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	
	 //告诉客户端浏览器不使用缓存，HTTP 1.1 协议 
	header("Cache-Control: no-cache, must-revalidate"); 
	
	 //告诉客户端浏览器不使用缓存，兼容HTTP 1.0 协议 
	header("Pragma: no-cache");*/
}

//
//是否关注
function is_subscribe(){
	$i=0;
	while(1){
		while($i<10){
			$param ['access_token'] = $this->get_user_access_token();	
			$param ['openid'] = session("openid");
			$param ['lang'] = "zh_CN";
			$url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query ( $param );			
			$content = curl_file_get_contents ( $url );
			$content = json_decode ( $content, true );
			if (! empty ( $content ['errmsg'] )) {
				//\Think\Log::write('获取关注信息失败，重试','WARN');
				$i++;
				//var_dump($content);
				//exit ("错误代码：3");
			}else{
				break;
			}
		}
		if (! empty ( $content ['errmsg'] )) {
			//\Think\Log::write('获取关注信息失败10次，重新获取accesstoken','WARN');
			$i=0;
			$map["stype"]="access_token";
			$config["uptime"]=date("Y-m-d H:i:s", time()-100000);//使access过期
			$jg=new Wx_config();
			$jg=$jg->save($config,$map);
		}else{
			break;
		}
	}
	if($content ['subscribe'] == 1){
		//\Think\Log::write("获取成功，写入用户基本信息",'WARN');
		$this->renew_user($content);
		return TRUE;
	}else{
		return FALSE;
	}
}
//重新获取网页授权access_token 
function reget_web_access_token(){
	$param ['appid'] = config('wx.appid');
	$redirect_uri='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$state=session('state');
	//\Think\Log::write('存在state：'.$state,'WARN');
	//\Think\Log::write('收到state：'.input('state'),'WARN');
	if($state == null || input('state') != $state) {
		$state=date('mdHis', time()).rand(10000,99999);
		session('state',$state);
		//\Think\Log::write('生成并缓存state：'.$state,'WARN');
		$param ['redirect_uri'] =$redirect_uri;//U ( 'index');
		$param ['response_type'] = 'code';
		$param ['scope'] = 'snsapi_userinfo';
		$param ['state'] = $state;
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';
		//\Think\Log::write('网页授权第一步，回调'.$url,'WARN');
		$this->redirect($url);
		exit;
	}elseif($_GET ['state'] == $state) {
		session('state',null);
		//\Think\Log::write('网页授权第二步，接收code，获取access_token','WARN');
		if (empty ( $_GET ['code'] )) {
			//\Think\Log::write('获取code失败，回调'.$redirect_uri,'WARN');
			$this->redirect($redirect_uri);
			//exit ( 'code获取失败' );
		}
		$param ['secret'] = config('wx.secret');
		$param ['code'] = input ( 'code' );
		$param ['grant_type'] = 'authorization_code';
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );			
		$content = curl_file_get_contents ( $url );
		$content = json_decode ( $content, true );
		if (! empty ( $content ['errmsg'] )) {
			//\Think\Log::write('获取access_token失败，回调'.$redirect_uri,'WARN');
			$this->redirect($redirect_uri);
			//exit ( $content ['errmsg'] );
		}
		//\Think\Log::write('获取access_token成功，缓存openid，web_access_token','WARN');
		session('openid',$content ['openid']);
		session('web_access_token',$content ['access_token']);
	}
}


//获取access_token
public function get_user_access_token(){
	$map["stype"]="access_token";
	$wxpz=new Wx_config();
	$config=$wxpz->where($map)->find();
	if($config["lock"] == 1){//判断是否锁定access_token,防止在其他人刷新时 刷新
		while(1){
			//\Think\Log::write("等待access_token刷新....",'WARN');
			$config=$wxpz->where($map)->find();
			if($config["lock"] == 0){
				//\Think\Log::write("等待access_token刷新完成，进入下一步",'WARN');
				 break;
			}
		}
	}
	if($config["uptime"]==null || $config["svalue"]==null || time() > strtotime($config["uptime"])+3600){//access_token过期  减5m 提前过期  刷新access_token
		//	锁定
		$data["lock"]=1;
		$wxpz->save($data,$map);
		//获取access_token
		$param ['grant_type'] = "client_credential";
		$param ['appid'] = config('wx.appid');
		$param ['secret'] = config('wx.secret');
		$url = 'https://api.weixin.qq.com/cgi-bin/token?' . http_build_query ( $param );			
		$content = curl_file_get_contents ( $url );
		$content = json_decode ( $content, true );
		$wxpz=new Wx_config();
		if (! empty ( $content ['errmsg'] )) {
			//	解锁
			unset($data);
			$data["lock"]=0;
			$wxpz->save($data,$map);
			exit ( $content ['errmsg']."错误代码：1" );
		}	else{
			//保存到session 和数据库
			unset($data);
			$data["lock"]=0;
			$data["svalue"]=$content ['access_token'];
			$data["uptime"]=date("Y-m-d H:i:s", time());
			$data["expires_in"]=$content ['expires_in'];
			if(!$wxpz->save($data,$map)){
				$this->error("access_token储存出错！");
				exit;
			}
			return $content['access_token'];
		}
	}else{//未过期 全局缓存 access_token
		return $config["svalue"];
	}
}
//获取jsapi_ticket
public function get_jsapi_ticket(){
	$map["stype"]="jsapi_ticket";
	$wxpz=new Wx_config();
	$config=$wxpz->where($map)->find();
	if($config["lock"] == 1){//判断是否锁定jsapi_ticket,防止在其他刷新是同时刷新
		while(1){
			$config=$wxpz->where($map)->find();
			if($config["lock"] == 0) break;
		}
	}
	if($config["uptime"]==null || $config["svalue"]==null || time() > strtotime($config["uptime"])+7000){//jsapi_ticket过期  3  刷新jsapi_ticket
		//	锁定
		$data["lock"]=1;
		$wxpz->save($data,$map);
		//获取jsapi_ticket
		$param ['access_token'] = $this->get_user_access_token();
		$param ['type'] = "jsapi";
		$url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?' . http_build_query ( $param );			
		$content = curl_file_get_contents ( $url );
		$content = json_decode ( $content, true );
		$wxpz=new Wx_config();
		if ($content ['errmsg'] == "ok") {
			//保存到数据库
			unset($data);
			$data["lock"]=0;
			$data["svalue"]=$content ['ticket'];
			$data["uptime"]=date("Y-m-d H:i:s", time());
			$data["expires_in"]=$content ['expires_in'];
			if(!$wxpz->save($data,$map)){
				$this->error("ticket储存出错！");
				exit;
			}
			return $content['ticket'];
		}	else{
			//	解锁
			unset($data);
			$data["lock"]=0;
			$wxpz->save($data,$map);;
			exit ( $content ['errmsg']."错误代码：2");
		}
	}else{//未过期 
		return $config["svalue"];
	}
	
}





//网页授权获取微信用户信息
function set_web_user_info(){
	//\Think\Log::write('网页授权获取用户基本信息','WARN');
	$param ['access_token'] = session('web_access_token');
	$param ['openid'] = session('openid');
	$param ['lang'] = 'zh_CN';
	$url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query ( $param );			
	$content = curl_file_get_contents ( $url );
	$content = json_decode ( $content, true );
	if (! empty ( $content ['errmsg'] )) {
		//\Think\Log::write('获取失败，重新网页授权','WARN');
		session('openid',null);
		$this->reget_web_access_token();
	}else{
		//\Think\Log::write('获取成功，写入用户基本信息','WARN');
		$this->renew_user($content);
	}
}


//添加 更新用户信息
function renew_user($content){
	$map['wxopenid']=session('openid');
	//添加
	$user= new Userinfo();
	$userinfo=$user->where($map)->field('password',true)->find();
	if($userinfo == null){
		$data['userid']=Db::query('CALL SP_GetReqID ("User")')[0][0]['CurID'];
		$data['username']=$content ['nickname'];
		$data['wxnickname']=$content ['nickname'];
		$data['status']=1;
		$data['roleid']=34;
		$data['wxopenid']=$content ['openid'];
		$data['headimg']=$content ['headimgurl'];
		$data['sponsor']=input('sponsor');
		if(!$user->save($data)){
			$this->error('创建账户失败!');
			exit;
		}
	}else{//修改
		if($content ['nickname'] != $userinfo['wxnickname'] || $content ['headimgurl']!=$userinfo['headimg']){
			$data['wxnickname']=$content ['nickname'];
			$data['headimg']=$content ['headimgurl'];
			if(!$user->save($data,$map)){
				$this->error('更新账户信息失败!');
				exit;
			}
		}
	}

}


//发送模板消息
function send_template($touser,$template_id,$url,$topcolor,$data){
	$template = array(
	  'touser' => $touser,
	  'template_id' => $template_id,
	  'url' => $url,
	  'topcolor' => $topcolor,
	  'data' => $data
	);
  $json_template = json_encode($template);
  $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $this->get_user_access_token();
	$i=0;
	while($i != 3){
	  $dataRes = $this->curl_post($url, $json_template);
	  if ($dataRes['errcode'] == 0) {
	  	break;
	  }
		++$i;
	}
  if ($dataRes['errcode'] == 0) {
    return true;
  } else {
    return false;
  }
}

//微信js
public function jsapi(){
	$appid=config('wx.appid');
	$secret=config('wx.secret');
	$timestamp=time();
	$nonceStr="muying";
	$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$jsapi_ticket=$this->get_jsapi_ticket();

	$string="jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;
	$signature=sha1($string);
	$this->assign("appid",$appid);
	$this->assign("timestamp",$timestamp);
	$this->assign("nonceStr",$nonceStr);
	$this->assign("signature",$signature);
}



}