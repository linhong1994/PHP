<?php
namespace Addons\Bll\Controller;
use Home\Controller\AddonsController;
class CommonController extends AddonsController{
public function _initialize(){
	// 默认错误跳转对应的模板文件
	C ( 'TMPL_ACTION_ERROR', 'Addons:dispatch_jump_mobile' );
	// 默认成功跳转对应的模板文件
	C ( 'TMPL_ACTION_SUCCESS', 'Addons:dispatch_jump_mobile' );
	if(! isWeixinBrowser()){
		$this->error("请在微信客户端访问!");
		exit;
	}
	session("token", "gh_32431325cf75");//配置token为公众号原始ID
	//\Think\Log::write("开始登录",'WARN');
	//\Think\Log::write("开始判断用户cookie存在与否",'WARN');
	//if(cookie("user") != 1){//登录过期 重新获取
	//	session("openid",null);
	//	session("userinfo",null);
	//	\Think\Log::write("用户cookie不存在，删除openid、用户信息",'WARN');
	//}else{
	//	cookie("user",1,60*60);//刷新登录状态 5分钟
	//	\Think\Log::write("用户cookie存在，刷新cookie",'WARN');
	//}
	//\Think\Log::write("判断openid存在与否",'WARN');
	if(session("openid") == null)
	{
		//\Think\Log::write("openid不存在，重新网页授权",'WARN');
		$this->reget_web_access_token();
		
	}
	//\Think\Log::write("openid存在，开始获取用户基本信息",'WARN');
	

	$map["wxopenid"]=session("openid");
	$userinfo = M("bll_userinfo");
	if($userinfo->where($map)->count() == 0){//是否关注  如果关注  更新用户信息
		$this->set_web_user_info();//如果没关注 获取设置用户信息
	}
	$userinfo = M("bll_userinfo")->where($map)->field("password",true)->find();
	session("userinfo",$userinfo);
	//\Think\Log::write("结束登录",'WARN');

}


//获取网页内容  效率比较高
public function curl_file_get_contents($durl){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $durl);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  //curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

//post
public function curl_post($url,$param){
  if (empty($url) || empty($param)) {
      return false;
  }
  $ch = curl_init(); //初始化curl
  curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
  curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
  curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  $data = curl_exec($ch); //运行curl
  curl_close($ch);
	//\Think\Log::write("到curl",'WARN');
  return $data;
}

//获取access_token
public function get_user_access_token(){
	//\Think\Log::write("开始获取用户access_token",'WARN');
	$map["stype"]="access_token";
	$config=M("bll_wx_config")->where($map)->find();
	if($config["lock"] == 1){//判断是否锁定access_token,防止在其他人刷新时 刷新
		//\Think\Log::write("access_token被刷新中",'WARN');
		while(1){
			//\Think\Log::write("等待access_token刷新....",'WARN');
			$config=M("bll_wx_config")->where($map)->find();
			if($config["lock"] == 0){
				//\Think\Log::write("等待access_token刷新完成，进入下一步",'WARN');
				 break;
			}
		}
	}
	if($config["uptime"]==null || $config["svalue"]==null || time() > strtotime($config["uptime"])+3600){//access_token过期  减5m 提前过期  刷新access_token
		//\Think\Log::write("access_token过期 重新获取",'WARN');
		//	锁定
		$config["lock"]=1;
		M("bll_wx_config")->where($map)->save($config);
		//获取access_token
		$param ['grant_type'] = "client_credential";
		$info = get_token_appinfo ();
		$param ['appid'] = $info ['appid'];
		$param ['secret'] = $info ['secret'];
		$url = 'https://api.weixin.qq.com/cgi-bin/token?' . http_build_query ( $param );			
		$content = $this->curl_file_get_contents ( $url );
		$content = json_decode ( $content, true );
		if (! empty ( $content ['errmsg'] )) {
			//	解锁
			$config["lock"]=0;
			M("bll_wx_config")->where($map)->save($config);
			//$this->get_user_access_token();
			exit ( $content ['errmsg']."错误代码：1" );
		}	else{
			//保存到session 和数据库
			$config["lock"]=0;
			$config["svalue"]=$content ['access_token'];
			$config["uptime"]=date("Y-m-d H:i:s", time());
			$config["expires_in"]=$content ['expires_in'];
			if(!M("bll_wx_config")->where($map)->save($config)){
				$this->error("access_token储存出错！");
				exit;
			}
			//\Think\Log::write("access_token获取成功",'WARN');
			session("access_token",$content['access_token']);
		}
	}else{//未过期 全局缓存 access_token
		//\Think\Log::write("access_token未过期，直接获取数据库中的",'WARN');
		session("access_token",$config["svalue"]);
	}
	return session("access_token");
}
//获取jsapi_ticket
public function get_jsapi_ticket(){
	$map["stype"]="jsapi_ticket";
	$config=M("bll_wx_config")->where($map)->find();
	if($config["lock"] == 1){//判断是否锁定jsapi_ticket,防止在其他刷新是同时刷新
		while(1){
			$config=M("bll_wx_config")->where($map)->find();
			if($config["lock"] == 0) break;
		}
	}
	if($config["uptime"]==null || $config["svalue"]==null || time() > strtotime($config["uptime"])+7000){//jsapi_ticket过期  3  刷新jsapi_ticket
		//	锁定
		$config["lock"]=1;
		M("bll_wx_config")->where($map)->save($config);
		//获取jsapi_ticket
		$param ['access_token'] = $this->get_user_access_token();
		$param ['type'] = "jsapi";
		$url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?' . http_build_query ( $param );			
		$content = $this->curl_file_get_contents ( $url );
		$content = json_decode ( $content, true );
		if ($content ['errmsg'] == "ok") {
			//保存到数据库
			$config["lock"]=0;
			$config["svalue"]=$content ['ticket'];
			$config["uptime"]=date("Y-m-d H:i:s", time());
			$config["expires_in"]=$content ['expires_in'];
			if(!M("bll_wx_config")->where($map)->save($config)){
				$this->error("ticket储存出错！");
				exit;
			}
			return $content['ticket'];
		}	else{
			//	解锁
			$config["lock"]=0;
			M("bll_wx_config")->where($map)->save($config);
			exit ( $content ['errmsg']."错误代码：2");
		}
	}else{//未过期 
		return $config["svalue"];
	}
	
}

//是否关注
public function is_subscribe(){
	$i=0;
	while(1){
		while($i<10){
			$param ['access_token'] = $this->get_user_access_token();	
			$param ['openid'] = session("openid");
			$param ['lang'] = "zh_CN";
			$url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query ( $param );			
			$content = $this->curl_file_get_contents ( $url );
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
			$jg=M("bll_wx_config")->where($map)->save($config);
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
public function reget_web_access_token(){
	$info = get_token_appinfo ();
	$param ['appid'] = $info ['appid'];
	$redirect_uri='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$state=session("state");
	
	//\Think\Log::write("存在state：".$state,'WARN');
	//\Think\Log::write("收到state：".I('state'),'WARN');
	if($state == null || I('state') != $state) {
		$state=date("mdHis", time()).rand(10000,99999);
		session("state",$state);
		//\Think\Log::write("生成并缓存state：".$state,'WARN');
		$param ['redirect_uri'] =$redirect_uri;//U ( 'index');
		$param ['response_type'] = 'code';
		$param ['scope'] = 'snsapi_userinfo';
		$param ['state'] = $state;
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';
		//\Think\Log::write("网页授权第一步，回调".$url,'WARN');
		redirect ( $url );
		exit;
	}elseif($_GET ['state'] == $state) {
		session("state",null);
		//\Think\Log::write("网页授权第二步，接收code，获取access_token",'WARN');
		if (empty ( $_GET ['code'] )) {
			//\Think\Log::write("获取code失败，回调".$redirect_uri,'WARN');
			redirect($redirect_uri);
			//exit ( 'code获取失败' );
		}
		$param ['secret'] = $info ['secret'];
		$param ['code'] = I ( 'code' );
		$param ['grant_type'] = 'authorization_code';
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );			
		$content = $this->curl_file_get_contents ( $url );
		$content = json_decode ( $content, true );
		if (! empty ( $content ['errmsg'] )) {
			//\Think\Log::write("获取access_token失败，回调".$redirect_uri,'WARN');
			redirect($redirect_uri);
			//exit ( $content ['errmsg'] );
		}
		//\Think\Log::write("获取access_token成功，缓存openid，web_access_token",'WARN');
		session("openid",$content ['openid']);
		session("web_access_token",$content ['access_token']);
	}
}






////刷新access_token 获取并设置openid
//public function refresh_web_access_token(){
//
//	$map["stype"]="web_refresh_token";
//	$config=M("bll_wx_config")->where($map)->find();
//	if(strtotime($config["uptime"])+60*60*24*29 < time() || $config["svalue"]==null){//过期 重新授权
//		$this->reget_web_access_token();
//	}else{
//		$info = get_token_appinfo ();
//		$param ['appid'] = $info ['appid'];	
//		$param ['grant_type'] = "refresh_token";
//		$param ['refresh_token'] = $config["svalue"];
//		$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?' . http_build_query ( $param );			
//		$content = $this->curl_file_get_contents ( $url );
//		$content = json_decode ( $content, true );
//		if (! empty ( $content ['errmsg'] ) || empty ( $content ['access_token'] )) {//
//			$this->reget_web_access_token();//刷新错误 重新授权
//		}else{
//			var_dump($content ['openid']);
//			$this->set_web_access_token($content ['access_token'], $content ['expires_in']);
//			session("openid",$content ['openid']);
//			session("web_access_token",$content ['access_token']);
//		}
//	}
//
//}
//
//
//
//
//缓存并返回网页授权access_token
//public function get_web_access_token(){
//	$map["stype"]="web_access_token";
//	$config=M("bll_wx_config")->where($map)->find();
//	if($config["uptime"]==null || $config["svalue"]==null || time() > strtotime($config["uptime"])+$config["expires_in"]-5){//access_token过期  减5秒 提前过期  刷新access_token
//		$this->refresh_web_access_token();
//	}else{//未过期 全局缓存 access_token
//		session("web_access_token",$config["svalue"]);
//	}
//	return session("web_access_token");
//}
//
//
//
//
////保存web_access_token到数据库
//public function set_web_access_token($access_token,$expires_in){
//	$map["stype"]="web_access_token";
//	$data=M("bll_wx_config")->where($map)->find();
//	$data["svalue"]=$access_token;
//	$data["uptime"]=date("Y-m-d H:i:s", time());
//	$data["expires_in"]=$expires_in;
//	if(!M("bll_wx_config")->where($map)->save($data)){
//		$this->error("web_access_token储存出错！");
//		exit;
//	}
//}
////保存web_refresh_token到数据库
//public function set_web_refresh_token($refresh_token){
//	$map["stype"]="web_refresh_token";
//	$data=M("bll_wx_config")->where($map)->find();
//	if($data["svalue"]!=$refresh_token){
//		$data["svalue"]=$refresh_token;
//		if(!M("bll_wx_config")->where($map)->save($data)){
//			$this->error("web_refresh_token储存出错！");
//			exit;
//		}	
//	}
//}

//网页授权获取微信用户信息
public function set_web_user_info(){
	//\Think\Log::write("网页授权获取用户基本信息",'WARN');
	$param ['access_token'] = session("web_access_token");
	$param ['openid'] = session("openid");
	$param ['lang'] = 'zh_CN';
	$url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query ( $param );			
	$content = $this->curl_file_get_contents ( $url );
	$content = json_decode ( $content, true );
	if (! empty ( $content ['errmsg'] )) {
		//\Think\Log::write("获取失败，重新网页授权",'WARN');
		session("openid",null);
		$this->reget_web_access_token();
	}else{
		//\Think\Log::write("获取成功，写入用户基本信息",'WARN');
		$this->renew_user($content);
	}
}


//添加 更新用户信息
public function renew_user($content){
	$map["wxopenid"]=session("openid");
	//添加
	if(M("bll_userinfo")->where($map)->count() == 0){
		$data["userid"]=M()->query("CALL SP_GetReqID ('User')")[0]["CurID"];
		$data["Userno"]=null;
		$data["username"]=$content ['nickname'];
		$data["wxnickname"]=$content ['nickname'];
		$data["state"]=1;
		$data["roleid"]=34;
		$data["wxopenid"]=$content ["openid"];
		if(!M("bll_userinfo")->data($data)->add()){
			$this->error("创建账户失败!");
			exit;
		}
		if(!$this->set_pic("user",$data["userid"],$content ["headimgurl"])){
			$this->error("创建账户失败!");
			exit;
		}
	}else{//修改
		$data=M("bll_userinfo")->where($map)->field("password",true)->find();
		if($content ['nickname'] != $data["wxnickname"]){
			$data["wxnickname"]=$content ['nickname'];
			if(!M("bll_userinfo")->save($data)){
				$this->error("更新账户信息失败!");
				exit;
			}
		}
		if($this->get_pic("user", $data["userid"]) != $content ["headimgurl"]){
			if(!$this->set_pic("user",$data["userid"],$content ["headimgurl"])){
				$this->error("更新账户信息失败!");
				exit;
			}
		}
	}

}


//发送模板消息
public function send_template($touser,$template_id,$url,$topcolor,$data){
	$template = array(
	  'touser' => $touser,
	  'template_id' => $template_id,
	  'url' => $url,
	  'topcolor' => $topcolor,
	  'data' => $data
	);
  $json_template = json_encode($template);
  $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->get_user_access_token();
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














//判断登录
public function is_login(){
	
}
//判断是否登录并刷新登录过期时间
public function is_logout(){
	if(cookie("loginExpire")){
		cookie("loginExpire",1,1800);
		return true;
	}else{
		cookie('loginExpire',0);
		return false;
	}
}
//计算两个经纬度坐标的距离
function getDistance($lat1, $lng1, $lat2, $lng2) { 
	$earthRadius = 6367000;//地球半径 米
	$lat1 = ($lat1 * pi() ) / 180; //纬度
	$lng1 = ($lng1 * pi() ) / 180; //经度 （大的那个）

	$lat2 = ($lat2 * pi() ) / 180; 
	$lng2 = ($lng2 * pi() ) / 180; 

	$calcLongitude = $lng2 - $lng1; 
	$calcLatitude = $lat2 - $lat1; 
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2); 
	$stepTwo = 2 * asin(min(1, sqrt($stepOne))); 
	$calculatedDistance = $earthRadius * $stepTwo; 
	return round($calculatedDistance); 
} 
//计算时间距离现在多久
function getHowLong($time){
	$t=time()-strtotime($time);
	$f=array(
		'31536000'=>'年',
		'2592000'=>'个月',
		'604800'=>'星期',
		'86400'=>'天',
		'3600'=>'小时',
		'60'=>'分钟',
		'1'=>'秒'
	);
	foreach ($f as $k=>$v){
		if (0 !=$c=floor($t/(int)$k)) {
			return $c.$v.'前';
		}
	}
}	
//上传文件
public function upload_file($exts,$rootPath,$saveNmae,$subName,$file){
	$upload = new \Think\Upload();// 实例化上传类
	$upload->exts = $exts;// 设置附件上传类型
	$upload->rootPath = $rootPath;
	$upload->savePath = ''; // 设置附件上传（子）目录
	$upload->saveName = $saveNmae."_".date("YmdHis",time())."_".rand(0,100);
	$upload->subName = $subName;
	$upload->replace = true;
	return $upload->uploadOne($file);
}	
	
	
//添加图片字段
public function set_pic($ptype, $typeid, $purl){
	$map["itemid"]=safe($typeid);
	$map["itemtype"]=safe($ptype);
	if(M("bll_menupic")->where($map)->count()>0){
		//修改
		$data["menuid"]=M("bll_menupic")->where($map)->find()["menuid"];
		$data["menuimg"]=safe($purl);
		$data["itemid"]=safe($typeid);
		$data["itemtype"]=safe($ptype);
		if(M("bll_menupic")->save($data)){
			return true;
		}else{
			return false;
		}
	}else{
		//增加
		$data["menuid"]=M()->query("CALL SP_GetReqID ('Menu')")[0]["CurID"];
		$data["menuimg"]=safe($purl);
		$data["itemid"]=safe($typeid);
		$data["itemtype"]=safe($ptype);
		if(M("bll_menupic")->add($data)){
			return true;
		}else{
			return false;
		}		
	}
	

}

//获取图片地址
public function get_pic($ptype, $typeid){
	$map["itemid"]=safe($typeid);
	$map["itemtype"]=safe($ptype);
	return M("bll_menupic")->where($map)->find()["menuimg"];
}
//获取模板消息跳转url
public function get_link($rtype){
	$map["rtype"]=safe($rtype);
	return M("bll_wxmoban_links")->where($map)->find()["rlinks"];
}	
	
//生成18位支付扫码
public function get_code(){
	$userid=session("userinfo")["userid"];
	if($userid == null || strlen($userid)<1){
		if(session("openid") == null)
		{
			$this->reget_web_access_token();
		}
		$map["wxopenid"]=session("openid");
		$userinfo = M("bll_userinfo")->where($map)->field("password",true)->find();
		session("userinfo",$userinfo);
		$userid=session("userinfo")["userid"];
	}
	//生成二维码数据
	$q1="18".rand(0,9).rand(0,9);
	while(1){
		$q2=rand(0,9).rand(0,9).rand(0,9).rand(0,9);
		if((int)$q2 < 8191) break;
	}
	$q2=sprintf("%04d", $q2);
	$q3=(int)$q2 ^ 3871;
	$q3=sprintf("%04d", $q3);
	$intime=time();
	$q4=(int)date("His",$intime) ^ 471896;
	$q4=sprintf("%06d", $q4);
	$barcode=$q1.$q2.$q3.$q4;
	$paycode=M("bll_paybar");
	$map["userid"]=$userid;
	$map["isused"]=0;
	$data=$paycode->where($map)->find();
	if($paycode->where($map)->count() != 0 ){
		//&& time()-strtotime($data["intime"]) < 60
		$data["barcode"]=$barcode;
		$data["intime"]=date("Y-m-d H:i:s", $intime);
		if($paycode->where($map)->save($data)){
			return $barcode;
		}else{
			return false;
		}
	}else{
		$data["barcode"]=$barcode;
		$data["userid"]=$userid;
		$data["intime"]=date("Y-m-d H:i:s", $intime);
		if($paycode->add($data)){
			return $barcode;
		}else{
			return false;
		}
	}


}
	
	
//获取门店名称
public function get_shop_name($id){
	$map["shopid"]=$id;
	return M("bll_shop")->where($map)->find();
}
//获取项目名称
public function get_project_name($id){
	$map["pid"]=$id;
	return M("bll_project")->where($map)->find();	
}
	
	
	
	
	
	
	
	
	
}
