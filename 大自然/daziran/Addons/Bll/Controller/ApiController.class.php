<?php
namespace Addons\Bll\Controller;

class ApiController extends CommonController{
	
public function _initialize(){

}

//获取用户信息 判断是否关注
public function isSubscribe(){
	//\Think\Log::write("isSubscribeisSubscribeisSubscribeisSubscribeisSubscribe",'WARN');
	if(!IS_AJAX){
		exit;
	}
	if(!$this->is_subscribe()){//是否关注  如果关注  更新用户信息
		$this->set_web_user_info();//如果没关注 获取设置用户信息
		$this->ajaxReturn(0);
	}else{
		$this->ajaxReturn(1);
	}
}



//获取附近商铺信息
public function getShopList(){
	if(!IS_AJAX){
		exit;
	}
	//$jd=118.593938;
	//$wd=24.89539;
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}
	$map["status"]=1;//已审核门店
	if(I("wd")!=null && I("jd")!=null){//I("wd")&&I("jd")
		$shop_list=M("bll_shop")->where($map)->select();
		//将距离加入商铺列表，并通过距离数组和商铺数组排序
		foreach($shop_list as $key=>$value){
			$distance = $this->getDistance(I("wd"),I("jd"),$value["latitude"],$value["longgitude"]);
			$value["distance"] = $distance;
			$distance_list[] = $distance;
			$shop_list[$key] = $value;
		}
		array_multisort($distance_list,$shop_list);//排序
		$totalCount = count($shop_list);//总数
		$pageTotal = Math.ceil($totalCount / $size);//总页数
		$starNum = $size*($page - 1);//起始数
		if($page*$size <= $totalCount) $endNum = $page*$size;
		else $endNum = $totalCount;
		$shop_list_d = array();
		$ii=0;//新建数组从0开始添加
		for($i=$starNum; $i<$endNum; $i++){
			$shop_list_d[$ii] = $shop_list[$i];
			$ii++;
		}
		$shop_list = $shop_list_d;
	}else{
		$shop_list=M("bll_shop")->where($map)->limit($size)->page($page)->select();
	}
	foreach($shop_list as $key => $value){
		$value["shopimg"]=$this->get_pic("shop",$value["shopid"]);
		$shop_list[$key]=$value;
	}
	$this->ajaxReturn($shop_list);
}


//获取店铺项目
public function getShopProject(){
	if(!IS_AJAX){
		exit;
	}
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}
	$map["status"]=1;//已审核
	if(I("shopid") != null){
		$map["shopid"]=I("shopid");
		$project_list=M("bll_project")->where($map)->limit($size)->page($page)->select();
		if(count($project_list)>0){
			foreach ($project_list as $key => $value) {
				$value["pimg"]=$this->get_pic("project",$value["pid"]);
				$project_list[$key]=$value;
			}
			$this->ajaxReturn($project_list);
		}else{
			$this->ajaxReturn();
		}
	}
}
	
//获取验证码
public function getVerify(){//可配置参数 验证码条数
	if(!IS_AJAX){
		exit;
	}
	$msg=array();
	$msg["state"]=0;
	
	if(I("mobile")!=null && strlen(I("mobile"))==11){
		
		$map1["userid"]=I("userid");
		if(M("bll_userinfo")->where($map1)->count()==0){
			$msg["msg"]="用户信息错误！";
			$this->ajaxReturn($msg);
			exit;			
		}
		$map2["mobile"]=I("mobile");
		if(M("bll_userinfo")->where($map2)->count()!=0){
			$msg["msg"]="手机号已被绑定！";
			$this->ajaxReturn($msg);
			exit;			
		}
		$verify_list = M("bll_verify")->where($map1)->order("createtime desc")->select();
		if((time()-$verify_list[0]["timestamp"])<60)
		{
			$msg["state"]=2;
			$msg["time"]=($verify_list[0]["timestamp"]+60)-time();
			$msg["msg"]="等待".$msg["time"]."秒后重新发送！";
			$this->ajaxReturn($msg);
			exit;
		}
		//单个用户每天次数
		$num=0;
		for($i=0;$i<3;$i++) {
			if(strtotime(date("Y-m-d"))>$verify_list[$i]["timestamp"]){
				//今天的时间戳大于记录最大时间错——今天没有发送
				break;
			}else{
				//今天发送
				$num++;
				if($num>=3){
					$msg["state"]=1;
					$msg["msg"]="今天次数已用完！";
					$this->ajaxReturn($msg);
					exit;
				}
			}
		}
		//单个手机号每天次数
		$verify_list = M("bll_verify")->where($map2)->order("createtime desc")->select();
		$num=0;
		for($i=0;$i<3;$i++) {
			if(strtotime(date("Y-m-d"))>$verify_list[$i]["timestamp"]){
				//今天的时间戳大于记录最大时间错——今天没有发送
				break;
			}else{
				//今天发送
				$num++;
				if($num>=3){
					$msg["state"]=1;
					$msg["msg"]="今天次数已用完！";
					$this->ajaxReturn($msg);
					exit;
				}
			}
		}
		$verify=rand(100000,999999);
		$map["stype"]="SendMsg";
		$url=M("bll_wx_config")->where($map)->find()["svalue"];
		libxml_disable_entity_loader(false);//防止failed to load external entity 出错误
		$client = new \SoapClient($url);
		$systime = new \DateTime;
		$username="Fjjjgov";
		$datetime = new \DateTime;
		$datetime = $datetime->format('YmdHis');
		$password=md5($username . "3592B873A85A23A14DE7E39F48B94708F1622FE09F9D6E14");
		$Message='您的手机验证码为：'.$verify;
		$headerbody = array('SystTime' => strtotime($datetime),
												'Username' => $username,
												'Password' => $password);
		$header = new \SoapHeader("dsmp.greatwall.net.cn", 'AuthHeader', $headerbody,true);
		$client->__setSoapHeaders($header);
		$param = array('Mobile' => I("mobile"),
									 'Message' => $Message,
									 "ExNumber" => "");
		$p = $client->__soapCall('SendMsgPhp',array('parameters'=>$param));
		if($p->SendMsgPhpResult)//$p->SendMsgPhpResult
		{
			$data=array();
			$data["userid"]=I("userid");
			$data["msg"]=$Message;
			$data["mobile"]=I("mobile");
			$data["timestamp"]=time();
			M("bll_verify")->data($data)->add();
			session("verify_mobile",I("mobile"));
			session("verify_code",$verify);
			session("verify_time",time());
			$msg["state"]=3;
			$msg["msg"]="发送成功！";
			$this->ajaxReturn($msg);
		}else{
			$msg["msg"]="发送失败，请重试！";
			$this->ajaxReturn($msg);
		}
	}else{
		$msg["msg"]="手机号错误";
		$this->ajaxReturn($msg);
	}
}
	
	
//获取用户预约订单
public function getApptOrder(){
	if(!IS_AJAX){
		exit;
	}
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}
	if(I("type") != 1) $type=0;//状态类型 所有
	else $type = 1;
	if(I("userid") != null) $userid = I("userid");//用户ID
	else{
		$this->ajaxReturn();
		exit;
	}
	$map["userid"]=$userid;
	if(I("orderno") == null)
	{
		if($type){
			$map["evaluate"] = 0;
			$map["status"] = array('in','1,2');	//到店
		}
	}else{
		$map["orderno"]=I("orderno");
	}
	$orderlist=M("bll_yyorder")->where($map)->order('evaluate,yytime desc')->limit($size)->page($page)->select();
	foreach($orderlist as $key=>$value){
		unset($map);
		$map["itemid"]=$value["pid"];
		$map["itemtype"]="project";
		if(M("bll_menupic")->where($map)->count() > 0){
			$value["pimg"]=M("bll_menupic")->where($map)->find()["menuimg"];
		}else{
			$value["pimg"]="./Addons/Bll/View/default/Public/images/1.jpg";
		}
		unset($map);
		$map["pid"]=$value["pid"];
		$p=M("bll_project")->where($map)->find();
		$value["pname"]=$p["pname"];
		$value["lxfs"]=$p["tel"];
    if(I("orderno")!=null){
			unset($map);
			$map["shopid"]=$value["shopid"];
			$value["shopname"]=M("bll_shop")->where($map)->find()["shopname"];
			
			unset($map);
			$tia=explode(",", $value["tid"]);
			$tca=array();
			foreach($tia as $tid){
				$map["tid"]=$tid;
				$tcd=M("bll_technician")->where($map)->find()["techcode"];
				$tca[]=$tcd;
			}
			$tca=implode ( '　', array_unique ( $tca) );//去重  转字符
			$value["tca"]=$tca;
    }

		$orderlist[$key]=$value;
	}
	$this->ajaxReturn($orderlist);
}
	
	
//获取用户结算订单
public function getJsOrder(){
	if(!IS_AJAX){
		exit;
	}
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}
	if(I("type") != 2) $type=1;//状态类型
	else $type = 2;
	if(I("userid") != null) $userid = I("userid");//用户ID
	else{
		$this->ajaxReturn();
		exit;
	}
	$map["userid"]=$userid;
	$map["status"] = $type;
	$orderlist=M("bll_jsorder")->where($map)->order('status,orderdate desc')->limit($size)->page($page)->select();
  foreach($orderlist as $key=>$value){
		$map1["itemid"]=$value["shopid"];
		$map1["itemtype"]="shop";
		if(M("bll_menupic")->where($map1)->count() > 0){
			$value["shopimg"]=M("bll_menupic")->where($map1)->find()["menuimg"];
		}else{
			$value["shopimg"]="./Addons/Bll/View/default/Public/images/1.jpg";
		}
		$map2["shopid"]=$value["shopid"];
		$value["shopname"]=M("bll_shop")->where($map2)->find()["shopname"];
		$orderlist[$key]=$value;
  }
	$this->ajaxReturn($orderlist);
}	
//获取用户资金明细
public function getZj(){
	if(!IS_AJAX){
		exit;
	}
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}

	if(I("userid") != null) $userid = I("userid");//用户ID
	else{
		$this->ajaxReturn();
		exit;
	}
	$map["userid"]=$userid;
	$zjlist=M("bll_balanceupdate")->where($map)->order('insertdate desc')->limit($size)->page($page)->select();
	
	unset($map);
	$map["itemid"]=$userid;
	$map["itemtype"]="user";
	$userimg=M("bll_menupic")->where($map)->find()["menuimg"];
  foreach($zjlist as $key=>$value){
		$value["userimg"]=$userimg;
		$zjlist[$key]=$value;
  }
	$this->ajaxReturn($zjlist);
}		
//获取优惠券明细
public function getYhq(){
	if(!IS_AJAX){
		exit;
	}
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}

	if(I("rid") != null) $rid = I("rid");//用户ID
	else{
		$this->ajaxReturn();
		exit;
	}
	$map["couponid"]=$rid;
	$map["payway"]=2;
	$map["status"]=1;
	$zjlist=M("viewjsorder")->where($map)->order('orderdate desc')->limit($size)->page($page)->select();

	$this->ajaxReturn($zjlist);
}		
//获取用户充值记录
public function getCz(){
	if(!IS_AJAX){
		exit;
	}
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}
	if(I("type") == 1) $type=1;//状态类型
	else $type = 0;
	if(I("userid") != null) $userid = I("userid");//用户ID
	else{
		$this->ajaxReturn();
		exit;
	}
	$map["userid"]=$userid;
	$map["orderstate"] = $type;
	$czlist=M("bll_cz")->where($map)->order('dotime desc')->limit($size)->page($page)->select();
	unset($map);
	$map["itemid"]=$userid;
	$map["itemtype"]="user";
	$userimg=M("bll_menupic")->where($map)->find()["menuimg"];
  foreach($czlist as $key=>$value){
		$value["userimg"]=$userimg;
		$czlist[$key]=$value;
  }
	$this->ajaxReturn($czlist);
}	
	
//获取用户卡券
public function getCard(){
	if(!IS_AJAX){
		exit;
	}
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}
	if(I("type") == 1) $map["usestatus"] = 1;
	elseif(I("type") ==2 ) $map["usestatus"] = array(2,3,'or');
	else $map["usestatus"] = 0;
	if(I("userid") != null) $userid = I("userid");//用户ID
	else{
		$this->ajaxReturn();
		exit;
	}
	
	$map["userid"]=$userid;
	$couponlist=M("view_couponuser")->where($map)->order('ytype desc,lqtime asc')->limit($size)->page($page)->select();
  foreach($couponlist as $key=>$value){
		$value["yue"]=$value["yamount"]-$value["useje"];
		$couponlist[$key]=$value;
  }
	$this->ajaxReturn($couponlist);
}		
//预约订单评价
public function getApptEvaluate(){
	if(!IS_AJAX){
		$this->ajaxReturn();
		exit;
	}
	
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}

	if(I("pid") != null) $pid = I("pid");//项目ID
	else{
		$this->ajaxReturn();
		exit;
	}
	$map["pid"]=$pid;
	$map["status"]=1;//已审核
	$evaluatelist=M("bll_yyorder_evaluate")->where($map)->order('evaltime desc')->limit($size)->page($page)->select();
	foreach($evaluatelist as $key=>$value){
		//技师ID列表转编号列表
		$map3["orderno"]=$value["orderno"];
		$tia=explode(",", M("bll_yyorder")->where($map3)->find()["tid"]);
		$tca=array();
		foreach($tia as $tid){
			$map1["tid"]=$tid;
			$tcd=M("bll_technician")->where($map1)->find()["techcode"];
			$tca[]=$tcd;
		}
		$tca=implode ( ',', array_unique ( $tca) );//去重  转字符
		$value["tca"]=$tca;	
		//用户名
		$map2["userid"]=$value["userid"];
		$value["username"]=M("bll_userinfo")->where($map2)->find()["username"];
		//头像
		$value["headimg"]=$this->get_pic("user", $value["userid"]);
		//时间
		$value["evaltime"]=$this->getHowLong($value["evaltime"]);
		$evaluatelist[$key]=$value;
	}
	$this->ajaxReturn($evaluatelist);
}
	
	
public function getTechEvaluate(){
	if(!IS_AJAX){
		$this->ajaxReturn();
		exit;
	}
	
	if(I("page")!=null){//页数
		$page=I("page");
	}else{
		$page=1;
	}
	if(I("size")!=null){//每页数量
		$size=I("size");
	}else{
		$size=10;
	}

	if(I("tid") != null) $tid = I("tid");//技师ID
	else{
		$this->ajaxReturn();
		exit;
	}
	$map["tid"]=$tid;
	$map["status"]=1;//已审核
	$evaluatelist=M("bll_tech_evaluate")->where($map)->order('evaltime desc')->limit($size)->page($page)->select();
	foreach($evaluatelist as $key=>$value){
		//用户名
		$map2["userid"]=$value["userid"];
		$value["username"]=M("bll_userinfo")->where($map2)->find()["username"];
		//头像
		$value["headimg"]=$this->get_pic("user", $value["userid"]);
		//时间
		$value["evaltime"]=$this->getHowLong($value["evaltime"]);
		$evaluatelist[$key]=$value;
	}
	$this->ajaxReturn($evaluatelist);	
}
	

	
//微信支付接口
public function wxpay(){
	if(!IS_AJAX){
		$this->ajaxReturn();
		exit;
	}
	
	$cxtcid=I('rid');
	$SetTotal_fee=I("fee");
	$map['rid']=$cxtcid;
	$cxtc=M("bll_recharge")->where($map)->find();
	unset($map);
	if($cxtc != null){
		if($SetTotal_fee != $cxtc["czje"]){
			$map['msg']="发起微信支付失败,欲充值金额与优惠充值套餐金额不符合,请重试!";
			$map['signType']='error';
			$this->ajaxReturn($map);
			exit;
		}
		if($cxtc["cznum"] != 0){
			$map['userid']=session("userinfo.userid");
			$map['orderstate']=1;
			$map['cztcid']=$cxtcid;
			$map['insertdate']=array(array('egt',$cxtc['yx_start']),array('elt',$cxtc['yx_end']));
			$tccount=M('bll_cz')->where($map)->count();
			unset($map);
			if($tccount >= $cxtc["cznum"]){
				$map['msg']="充值". $cxtc["czje"] ."送" . $cxtc["zsje"] ."只限". $cxtc["cznum"] ."次,请选择其他金额充值!";
				$map['signType']='error';
				$this->ajaxReturn($map);
				exit;
			}
		}
	}
	
	
	unset($map);
	
	

	if($SetTotal_fee==null || !is_numeric($SetTotal_fee) || $SetTotal_fee*100<1){
		$SetTotal_fee=0;
	}
	$shopid=I("shopid");
	$map["shopid"]=$shopid;
	if($shopid!=null){
		if(M("bll_shop")->where($map)->find()==null){
			$shopid=M("bll_shop")->find()["shopid"];
		}
	}
  vendor('WXpay.lib.Api');
  vendor('WXpay.lib.JsApiPay');
	$tools = new \JsApiPay();
	//①、获取用户openid
	$openId=session("openid") ;
	//②、统一下单
	$input = new \WxPayUnifiedOrder();
	$input->SetBody("大自然会所余额充值"); //商品描述
	$input->SetAttach("微信钱包余额充值");//附加数据
	$Out_trade_no=session("userinfo.userid").date("YmdHis").rand(1000,9999);
	session("orderno",$Out_trade_no);
	$input->SetOut_trade_no($Out_trade_no);//商户订单号
	$input->SetTotal_fee($SetTotal_fee*100);//总金额
	$input->SetTime_start(date("YmdHis"));//交易起始时间
	$input->SetTime_expire(date("YmdHis", time() + 600));//交易结束时间
	$input->SetGoods_tag("大自然余额");//商品标记
	$input->SetNotify_url("http://weixin.dzrspa.cn/index.php/addon/Bll/Api/notify.html");//通知地址
	$input->SetTrade_type("JSAPI");//交易类型
	$input->SetOpenid($openId);//用户$openId标识
	$wxpayapi=new \WxPayApi();
	$order = $wxpayapi->unifiedOrder($input);
	//var_dump($input);
	$jsApiParameters = $tools->GetJsApiParameters($order);
	//生成充值记录
	$data["orderno"]=$Out_trade_no;
	$data["ordermoney"]=$SetTotal_fee;
	$data["userid"]=session("userinfo.userid");
	$data["msg"]="发起充值请求";
	$data["shopid"]=$shopid;
	$data["tjr"]= I("tjr");
	$data["cztcid"]=I("rid");
	$data["dotime"]=date("Y-m-d H:i:s", time());
	M("bll_cz")->add($data);
	$this->ajaxReturn($jsApiParameters);

}	
	
//微信支付通知 notify_url
public function notify(){
	$log=M("bll_cz");
	//\Think\Log::write('回调','WARN');
	vendor('WXpay.lib.Api');
	vendor('WXpay.lib.Notify');
	vendor('WXpay.lib.NotifyCallBack');
  $notify = new \WxPayNotifyCallBack();
  $notify->Handle(true);
  $res = $notify->GetValues();
	$raw_xml = file_get_contents("php://input");
  libxml_disable_entity_loader(true);
  $ret = json_decode(json_encode(simplexml_load_string($raw_xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	$openid = $ret["openid"];
	

	$map["orderno"]=$ret["out_trade_no"];
	if($res['return_code'] ==="SUCCESS" && $res['return_msg'] ==="OK"){
		if($log->where($map)->count()  == 0){
			//\Think\Log::write('订单不存在','WARN');
			exit;
		}
		$map1["wxopenid"]=$openid;
		$user=M("bll_userinfo");
		if($user->where($map1)->count() == 0){
			//\Think\Log::write('用户不存在','WARN');
			exit;
		}
		//现金充值数
		$xj=$ret["total_fee"]/100;
		$data=$log->where($map)->find();
		if($data["orderstate"] == 0){
			$user=$user->where($map1)->find();
			if($xj >= 30000){
				//赠送
				$zs=round($xj*(2/3),2);//20000;
				//总额
				$ze=$xj + $zs;
				$user["grade"]=5;
			}elseif($xj >= 10000){
				//赠送
				$zs=round($xj*0.45,2);//4500;
				//总额
				$ze=$xj + $zs;	
				$user["grade"]=4;	
			}elseif($xj >= 5000){
				//赠送
				$zs=round($xj*0.36,2);//1800;
				//总额
				$ze=$xj + $zs;
				$user["grade"]=3;		
			}elseif($xj >= 3000){
				//赠送
				$zs=round($xj*(4/15),2);//800;
				//总额
				$ze=$xj + $zs;
				$user["grade"]=2;		
			}elseif($xj >= 1000){
				//赠送
				$zs=round($xj*0.2,2);//200;
				//总额
				$ze=$xj + $zs;	
				$user["grade"]=1;	
			}else{
				//赠送
				$zs=0;
				//总额
				$ze=$xj + $zs;				
			}
			//新增充值套餐,覆盖以上部分赠送规则
			if($data['cztcid'] != null){
				$cztc=M('bll_recharge')->where(['rid'=>$data['cztcid']])->find();
				if($cztc != null){
					if($cztc['czje'] == $xj){
						$zs=$cztc['zsje'] + 0;
						$ze=$xj + $zs;
					}
					//充值与套餐充值规定不符合处理
					else{
						
					}
				}
			}
			
			$user["BALANCE"] = $user["BALANCE"] + $ze;
			if(!M("bll_userinfo")->where($map1)->save($user)){
		  	$data["msg"]="支付成功,但增加用户金额失败！";
				$data["dotime"]=date("Y-m-d H:i:s", time());
				$data["noticeordermoney"]=$xj;
				$log->where($map)->save($data);
				//\Think\Log::write('支付成功,但增加用户金额失败！','WARN');
				exit;
			}
	  	$data["msg"]="支付成功！";
			$data["orderstate"]=1;
			$data["dotime"]=date("Y-m-d H:i:s", time());
			$data["noticeordermoney"]=$xj;//现金
			$data["givemoney"]=$zs;//赠送
			$data["Usedmoney"]=$ze;//总额  =现金+赠送
			$data["cardlx"]=$xj."_".$zs;
			$log->where($map)->save($data);
			//余额变动记录
			$data1["rid"]=$data["orderno"];
			$data1["userid"]=$user["userid"];
			$data1["orderno"]=$data["orderno"];
			$data1["orderamount"]=$ze;
			$data1["userbalance"]=$user["BALANCE"];//充值后余额
			$data1["cardlx"]=$xj."_".$zs;
			$data1["shopid"]=$data["shopid"];
			M("bll_balanceupdate")->add($data1);
			//下发模板消息
			$touser=$user["wxopenid"];
			$template_id="dMCdjN_B-Qp-OsVzKw4R7rWjDBFIDHMKmdg0eHU_ByY";
			$url=$this->get_link("cz");
			$topcolor="#FF0000";

			$data=array(
				'first' => array('value'=>"您好！您于".$data["dotime"]."在本会所成功充值。",'color'=>"#743A3A"),
				'keyword1' =>array('value'=>$ze."元",'color'=>"#743A3A"),
				'keyword2' =>array('value'=>$xj."元",'color'=>"#743A3A"),
				'keyword3' =>array('value'=>$zs."元",'color'=>"#743A3A"),
				'keyword4' =>array('value'=>$user["BALANCE"]."元",'color'=>"#743A3A"),
				'remark' =>array('value'=>"感谢您的使用。",'color'=>"#743A3A"),
			);
			$this->send_template($touser, $template_id, $url, $topcolor, $data);
		}
		//\Think\Log::write('支付成功！','WARN');
  }else{
  	$data["msg"]="支付失败！";
		$data["dotime"]=date("Y-m-d H:i:s", time());
		$log->where($map)->save($data);
		//\Think\Log::write('支付失败！','WARN');
  }
}


//微信支付接口
public function wxpay2(){
	if(!IS_AJAX){
		$this->ajaxReturn(0);
		exit;
	}
	$pid=I("pid");
	if($pid==null){
		$this->ajaxReturn(0);
		exit;
	}	
	$map['pid']=$pid;
	$map['status']=1;
	$project=M('bll_project')->where($map)->find();
	if($project==null){
		$this->ajaxReturn(0);
		exit;
	}		
	
	
	
	$SetTotal_fee=$project['pamount']*I('num');

  vendor('WXpay.lib.Api');
  vendor('WXpay.lib.JsApiPay');
	$tools = new \JsApiPay();
	//①、获取用户openid
	$openId=session("openid") ;
	//②、统一下单
	$input = new \WxPayUnifiedOrder();
	$input->SetBody("大自然会所团购支付"); //商品描述
	$input->SetAttach($project['pname']."团购券*".I('num'));//附加数据
	unset($map);
	while(true){
		$Out_trade_no=session("userinfo.userid").date("YmdHis").rand(1000,9999);
		$map['rid']=$Out_trade_no;
		if(M('bll_coupon_list')->where($map)->find() == null){
			break;
		}
	}
	
	$input->SetOut_trade_no($Out_trade_no);//商户订单号
	$input->SetTotal_fee($SetTotal_fee*100);//总金额
	$input->SetTime_start(date("YmdHis"));//交易起始时间
	$input->SetTime_expire(date("YmdHis", time() + 600));//交易结束时间
	$input->SetGoods_tag("大自然团购");//商品标记
	$input->SetNotify_url("http://weixin.dzrspa.cn/index.php/addon/Bll/Api/notify2.html");//通知地址
	$input->SetTrade_type("JSAPI");//交易类型
	$input->SetOpenid($openId);//用户$openId标识
	$wxpayapi=new \WxPayApi();
	$order = $wxpayapi->unifiedOrder($input);
	//var_dump($input);
	$jsApiParameters = $tools->GetJsApiParameters($order);
	//生成充值记录
	$data["rid"]=$Out_trade_no;
	$data["pay"]=$SetTotal_fee;
	$data["userid"]=session("userinfo.userid");
	$data["pid"]=$pid;
	$data["log"]=date("Y-m-d H:i:s", time()).":发起购买团购券请求,pid:".$pid."\r\n";
	$data["shopid"]=$project['shopid'];
	$data["num"]=I('num');
	M("bll_refundlog")->add($data);
	$this->ajaxReturn($jsApiParameters);

}	
	
//微信支付通知 notify_url
public function notify2(){
	$log=M("bll_refundlog");
	//\Think\Log::write('回调','WARN');
	vendor('WXpay.lib.Api');
	vendor('WXpay.lib.Notify');
	vendor('WXpay.lib.NotifyCallBack');
  $notify = new \WxPayNotifyCallBack();
  $notify->Handle(true);
  $res = $notify->GetValues();
	$raw_xml = file_get_contents("php://input");
  libxml_disable_entity_loader(true);
  $ret = json_decode(json_encode(simplexml_load_string($raw_xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	$openid = $ret["openid"];

	$map["rid"]=$ret["out_trade_no"];
	$loginfo=$log->where($map)->find();
	if($loginfo  == null){
		//\Think\Log::write('订单不存在','WARN');
		exit;
	}
	if($loginfo['status']!=0){
		//支付完成
		exit;
	}

	if($res['return_code'] ==="SUCCESS" && $res['return_msg'] ==="OK"){
		$map1["wxopenid"]=$openid;
		$user=M("bll_userinfo")->where($map1)->find();
		if($user == null){
			//\Think\Log::write('用户不存在','WARN');
			exit;
		}
  	$data["log"]=$loginfo['log'] . date("Y-m-d H:i:s", time()) . "支付成功！\r\n";
		$data["status"]=1;
		$log->where($map)->save($data);
		unset($map);
		$map['pid']=$loginfo['pid'];
		$project=M('viewproject')->where($map)->find();
		for($i=0;$i<$loginfo['num'];$i++){
			unset($data);
			$yid=M()->query("CALL SP_GetReqID ('Coupon')")[0]["CurID"];
			$data['yid']=$yid;
			$data['ytype']=2;
			$data['yname']=$project['pname'];
			$data['yamount']=$project['originalPrice'];
			$data['ypayamount']=$project['pamount'];
			$data['ybegindate']=date("Y-m-d H:i:s", time());
			$endtime=date("Y-m-d H:i:s", strtotime("+1 year"));
			$data['yenddate']=$endtime;
			$data['yimg']=$this->get_pic('project',$loginfo['pid']);
			$data['userid']=$user['userid'];
			$data['status']=1;
			$data['shopid']=$loginfo['shopid'];
			$data['pid']=$loginfo['pid'];
			M("bll_coupon")->add($data);
			unset($map);
			while(true){
				$tgqid='12'.rand(10000,99999).rand(10000,99999);
				$map['rid']=$tgqid;
				if(M('bll_coupon_list')->where($map)->find() == null){
					break;
				}
			} 
			unset($data);
			$data['rid']=$tgqid;
			$data['yid']=$yid;
			$data['oid']=$ret["out_trade_no"];
			$data['userid']=$user['userid'];
			M("bll_coupon_list")->add($data);
		}
		
		//下发模板消息
		$touser=$user["wxopenid"];
		$template_id="ezztaHsPt0vJENeO6yw0vIXSvWFKL_3xe9e-hUqlBhs";
		$url=$this->get_link("tg");
		$topcolor="#FF0000";
		unset($data);
		$data=array(
			'first' => array('value'=>"您好！您于".$data["dotime"]."在".$project['shopname']."团购成功。",'color'=>"#743A3A"),
			'keyword1' =>array('value'=>$project["pname"]." 团购券*".$loginfo["num"],'color'=>"#743A3A"),
			'keyword2' =>array('value'=>"点击详情查看!",'color'=>"#743A3A"),
			'keyword3' =>array('value'=>$loginfo['pay']."元",'color'=>"#743A3A"),
			'keyword4' =>array('value'=>$endtime,'color'=>"#743A3A"),
			'remark' =>array('value'=>"付款时向服务员兑换码或二维码,祝您消费愉快!",'color'=>"#743A3A"),
		);
		unset($map);
		$map["rid"]=$ret["out_trade_no"];
		if($this->send_template($touser, $template_id, $url, $topcolor, $data)){
			unset($data);
	  	$data["log"]=$loginfo['log'] . date("Y-m-d H:i:s", time()) . "发送模板消息成功！\r\n";
			$data["status"]=1;
			$log->where($map)->save($data);
		}else{
			unset($data);
	  	$data["log"]=$loginfo['log'] . date("Y-m-d H:i:s", time()) . "发送模板消息失败！\r\n";
			$data["status"]=2;
			$log->where($map)->save($data);
		}
	
		//\Think\Log::write('支付成功！','WARN');
  }else{
  	$data["log"]=$loginfo['log'] . date("Y-m-d H:i:s", time()) . "支付失败！\r\n";
		$log->where($map)->save($data);
  }
}
//微信退款接口
public function refund(){
	//if(!IS_AJAX){
	//	$this->ajaxReturn();
	//	exit;
	//}
	$out_trade_no=I("out_trade_no");
	if($out_trade_no==null || !is_numeric($out_trade_no) || strlen($out_trade_no)<12){
		$this->ajaxReturn(array('err_code_des'=>'订单号错误!','result_code'=>'FAIL'));
		exit;
	}
	
	$map['rid']=$out_trade_no;
	$tgq=M('viewcouponlist')->where($map)->find();
	if($tgq==null){
		$this->ajaxReturn(array('err_code_des'=>'订单号不存在!','result_code'=>'FAIL'));
		exit;	
	}
	if($tgq['userid'] != session('userinfo.userid')){
		$this->ajaxReturn(array('err_code_des'=>'跨用户操作!','result_code'=>'FAIL'));
		exit;		
	}
	$map1['rid']=$tgq['oid'];
	$log=M('bll_refundlog')->where($map1)->find();
	if($log==null){
		$this->ajaxReturn(array('err_code_des'=>'支付记录不存在!','result_code'=>'FAIL'));
		exit;	
	}
	//$out_trade_no = 'U000041201704201158599861';	
	$total_fee = $log['pay']*100;//$tgq['ypayamount'];//'100';
	$refund_fee = ($log['pay']*100)/$log['num'];//$tgq['ypayamount'];//'100';

	vendor('WXpay.lib.Api');
	$input = new \WxPayRefund();
	$input->SetOut_trade_no($tgq['oid']);
	$input->SetTotal_fee($total_fee);
	$input->SetRefund_fee($refund_fee);
  $input->SetOut_refund_no(\WxPayConfig::MCHID.date("YmdHis"));
  $input->SetOp_user_id(\WxPayConfig::MCHID);
	$result=\WxPayApi::refund($input);

	if($result['result_code'] == 'FAIL'){
		//申请退款失败
		$data['log']=$log['log'] . date("Y-m-d H:i:s", time()) . "申请退款失败:". $result['err_code_des'] ."\r\n";
		M('bll_refundlog')->where($map1)->save($data);
	}else{
		//申请成功
		$data['status']=3;
		M('bll_coupon_list')->where($map)->save($data);
		unset($data);
		$data['log']=$log['log'] . date("Y-m-d H:i:s", time()) . "申请退款成功！\r\n";
		M('bll_refundlog')->where($map1)->save($data);
		//下发模板消息
		$touser=session('userinfo.wxopenid');
		$template_id="gcgN8VZCJQD8p9DdpZCkiOPebMP9-0bZ7u9rXNECgtc";
		$url=$this->get_link("tg");
		$topcolor="#FF0000";
		unset($data);
		$data=array(
			'first' => array('value'=>"您好！团购券:".$tgq['yname']."*1退款申请已受理!",'color'=>"#743A3A"),
			'keyword1' =>array('value'=>"￥".$tgq["ypayamount"],'color'=>"#743A3A"),
			'keyword2' =>array('value'=>$tgq['rid'],'color'=>"#743A3A"),
			'keyword3' =>array('value'=>date("Y年m月d日  H:i"),'color'=>"#743A3A"),
			'remark' =>array('value'=>"资金将原路返回(微信余额支付即时到账,银行卡等支付在三个工作日之内).",'color'=>"#743A3A"),
		);
		$this->send_template($touser, $template_id, $url, $topcolor, $data);
	}
	
	
	$this->ajaxReturn($result);
}





 






public function makeCode(){
	$paycode=$this->get_code();
	session("paycode",$paycode);
	$map["barcode"]=$paycode;
	$countdown=strtotime(M("bll_paybar")->where($map)->find()["intime"])+60-time();
	$data["countdown"]=$countdown;
	$paycode=substr($paycode,0,4) . "　" . substr($paycode,4,4) . "　" . substr($paycode,8,4) . "　" . substr($paycode,12,6);
	$data["paycode"]=$paycode;
	$this->ajaxReturn($data);
}
public function isPay(){
	$paycode=session("paycode");
	$map["barcode"]=$paycode;
	$ispay=M("bll_paybar")->where($map)->find();
	$this->ajaxReturn($ispay);
}

public function qrCode(){
	vendor('phpqrcode.phpqrcode');
	\QRcode::png(session("paycode"),false,"H","5");

}
public function qrCode2(){
	vendor('phpqrcode.phpqrcode');
	\QRcode::png(I('code'),false,"H","5");

}
public function barCode(){

	vendor('barcode.class.BCGFontFile');
	vendor('barcode.class.BCGColor');
	vendor('barcode.class.BCGDrawing');
	vendor('barcode.class.BCGcode128','','.barcode.php');
	
	$font = 0;//0-5
	$colorFront = new \BCGColor(0, 0, 0);
	$colorBack = new \BCGColor(255, 255, 255);
	
	// Barcode Part
	$code = new \BCGcode128();
	$code->setScale(2);
	$code->setThickness(30);
	$code->setForegroundColor($colorFront);
	$code->setBackgroundColor($colorBack);
	$code->setFont($font);
	$code->setStart(NULL);
	$code->setTilde(true);
	$code->parse(session("paycode"));//内容
	
	// Drawing Part
	$drawing = new \BCGDrawing('', $colorBack);
	$drawing->setBarcode($code);
	$drawing->draw();
	
	header('Content-Type: image/png');
	
	$drawing->finish(\BCGDrawing::IMG_FORMAT_PNG);
}
public function barCode2(){

	vendor('barcode.class.BCGFontFile');
	vendor('barcode.class.BCGColor');
	vendor('barcode.class.BCGDrawing');
	vendor('barcode.class.BCGcode128','','.barcode.php');
	
	$font = 0;//0-5
	$colorFront = new \BCGColor(0, 0, 0);
	$colorBack = new \BCGColor(255, 255, 255);
	
	// Barcode Part
	$code = new \BCGcode128();
	$code->setScale(2);
	$code->setThickness(30);
	$code->setForegroundColor($colorFront);
	$code->setBackgroundColor($colorBack);
	$code->setFont($font);
	$code->setStart(NULL);
	$code->setTilde(true);
	$code->parse(I('code'));//内容
	
	// Drawing Part
	$drawing = new \BCGDrawing('', $colorBack);
	$drawing->setBarcode($code);
	$drawing->draw();
	
	header('Content-Type: image/png');
	
	$drawing->finish(\BCGDrawing::IMG_FORMAT_PNG);
}





function test(){
echo date("Y年m月d日 H:i");
}











}