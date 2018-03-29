<?php
namespace Addons\Bll\Controller;

class UserController extends CommonController{


	
//用户中心首页	
public function index(){
	$userinfo = session("userinfo");
	//添加用户头像字段
	$map["itemtype"]="user";
	$map["itemid"]=$userinfo["userid"];
	if(M("bll_menupic")->where($map)->count() > 0){
		$userinfo["headimg"]=M("bll_menupic")->where($map)->find()["menuimg"];
	}else{
		$userinfo["headimg"]="./Addons/Bll/View/default/Public/images/user_logo.jpg";
	}
	$this->assign("userinfo",$userinfo);
	unset($map);
	$map["itemtype"]="ad";
	$adlist=M("bll_menupic")->where($map)->order("menusort asc")->select();
	$this->assign("adlist",$adlist);
	unset($map);
	$map["userid"]=$userinfo["userid"];
	$apptnum=M("bll_yyorder")->where($map)->count();
	$this->assign("apptnum",$apptnum);
	
	$jsnum=M("bll_jsorder")->where($map)->count();
	$this->assign("jsnum",$jsnum);
	
	$cznum=M("bll_cz")->where($map)->count();
	$this->assign("cznum",$cznum);
	
	$map["status"]=0;
	$kqnum=M("bll_coupon_list")->where($map)->count();
	$this->assign("kqnum",$kqnum);
	
	unset($map["status"]);
	$zjnum=M("bll_balanceupdate")->where($map)->count();
	$this->assign("zjnum",$zjnum);

	$this->display();
}


//绑定手机号
public function bind_mobile(){
	//$mobile=session("userinfo")["mobile"];
	//if($mobile!=null && strlen($mobile)==11){
	//	$this->error("已经绑定过手机号！",U("index"));
	//	exit;
	//}
	
	if(IS_POST){
		$map1["mobile"]=I("mobile");
		if(M("bll_userinfo")->where($map1)->count() != 0){
			$this->error("手机号已被绑定！");
			exit;
		}
		if(session("verify_mobile") != I("mobile")){
			$this->error("获取验证码手机号与提交手机号不一致！");
			exit;
		}
		if(session("verify_code") != I("verify")){
			$this->error("验证码错误！");
			exit;
		}
		if(time()-session("verify_time") > 36000){//10分钟提交有效
			$this->error("验证码过期，请重新获取！");
			exit;
		}
		$map["userid"]=session("userinfo")["userid"];
		$data["mobile"]=I("mobile");
		$data["loginname"]=I("mobile");
		if(M("bll_userinfo")->where($map)->save($data)){
			unset($data);
			$data["oldmobile"]=session("userinfo")["mobile"];
			$data["newmobile"]=I("mobile");
			$data["userIP"]=get_client_ip();
			$data["userID"]=session("userinfo")["userid"];
			$data["remark"]="变更手机号";
			M("bll_mobilechange")->add($data);
			$this->success("绑定成功！",U("seting"));
			exit;
		}else{
			$this->error("绑定失败，请重试！");
			exit;	
		}
	}
	$this->assign("shopid",I("shopid"));
	$this->assign("distance",I("distance"));
	$this->assign("userid",session("userinfo")["userid"]);
	$this->display();
}

//解绑手机号
public function unbind_mobile(){
	$mobile=session("userinfo")["mobile"];
	if($mobile==null || strlen($mobile)!=11){
		$this->error("未绑定过手机号！",U("bind_mobile"));
		exit;
	}
	
	if(IS_POST){
		if($mobile != I("mobile")){
			$this->error("输入的手机号不是绑定号码！");
			exit;
		}
		if(session("verify_mobile") != I("mobile")){
			$this->error("获取验证码手机号与提交手机号不一致！");
			exit;
		}
		if(session("verify_code") != I("verify")){
			$this->error("验证码错误！");
			exit;
		}
		if(time()-session("verify_time") > 36000){//10分钟提交有效
			$this->error("验证码过期，请重新获取！");
			exit;
		}
		$map["userid"]=session("userinfo")["userid"];
		$data["mobile"]="";
		$data["loginname"]="";
		if(M("bll_userinfo")->where($map)->save($data)){
			$this->success("解绑成功！",U("seting"));
			exit;
		}else{
			$this->error("解绑失败，请重试！");
			exit;	
		}
	}
	$this->assign("shopid",I("shopid"));
	$this->assign("mobile",$mobile);
	$this->assign("userid",session("userinfo")["userid"]);
	$this->display();
}

//会员卡
public function vip_card(){
	$this->display();
}



//我的预约
public function my_appt(){
	if(I("type") != 1) $type=0;
	else $type = 1;
	
	$userinfo=session("userinfo");
	
	$this->assign("userinfo",$userinfo);
	$this->assign("type",$type);
	$this->display();
}

//预约评价
public function appt_evaluate(){
	if(I("orderno")==null){
		$this->error("获取订单信息失败！",U("my_appt"));
		exit;
	}
	
	$map["orderno"]=I("orderno");
	$order=M("bll_yyorder");
	if($order->where($map)->count() < 1){
		$this->error("订单不存在！",U("shop_list"));
		exit;		
	}
	$order= $order->where($map)->find();
	if(IS_POST){
		\Think\Log::write("收到post",'WARN');
		$data["yoeid"]=M()->query("CALL SP_GetReqID ('YyordEval')")[0]["CurID"];
		$data["orderno"]=I("orderno");
		$data["zgrade"]=I("zgrade");
		$data["fwgrade"]=I("fwgrade");
		$data["hjgrade"]=I("hjgrade");
		$data["evalremark"]=I("evalremark");
		$data["userid"]=$order["userid"];
		$data["shopid"]=$order["shopid"];
		$data["pid"]=$order["pid"];
		if(M("bll_yyorder_evaluate")->add($data)){
			\Think\Log::write("评价成功",'WARN');
			foreach(I("evalgrade") as $key=>$value){
				$data1["teid"]=M()->query("CALL SP_GetReqID ('TechEval')")[0]["CurID"];
				$data1["tid"]=I("tia")[$key];
				$data1["evalgrade"]=I("evalgrade")[$key];
				$data1["evalremark"]=I("jsevalremark")[$key];
				$data1["userid"]=$order["userid"];
				$data1["orderno"]=I("orderno");
				if(!M("bll_tech_evaluate")->add($data1)){
					$this->error("评价失败！",U("my_appt"));
					exit;
				}
			}
			$data2["evaluate"]=1;
			if(M("bll_yyorder")->where($map)->save($data2)){
				\Think\Log::write("订单评价状态更新完成，开始下发模板消息",'WARN');
				//下发模板消息
				$shopname=$this->get_shop_name($order["shopid"])["shopname"];
				$pname=$this->get_project_name($order["pid"])["pname"];
				$touser=session("openid");
				$template_id="l2rATVELQni83WA0BbCDcv0eHCco9NW-tFYJNAato_w";
				$url=$this->get_link("pj");
				$topcolor="#FF0000";
				$data=array(
					'first' => array('value'=>"您好！您的订单评价成功！",'color'=>"#743A3A"),
					'keyword1' =>array('value'=>$shopname,'color'=>"#743A3A"),
					'keyword2' =>array('value'=>$pname,'color'=>"#743A3A"),
					'keyword3' =>array('value'=>I("evalremark"),'color'=>"#743A3A"),
					'keyword4' =>array('value'=>session("userinfo")["wxnickname"],'color'=>"#743A3A"),
					'remark' =>array('value'=>"感谢您的支持与厚爱，点击领取VIP现金抵用券。",'color'=>"#743A3A"),
				);
				$this->send_template($touser, $template_id, $url, $topcolor, $data);
				\Think\Log::write("下发完成，评价成功跳转",'WARN');	
				$this->success("评价成功！",U("my_appt"));	
				exit;
			}
		}else{
			$this->error("评价失败！",U("my_appt"));
			exit;
		}
	}
	$tia=explode(",", $order["tid"]);
	$tca=array();
	foreach($tia as $tid){
		$map1["tid"]=$tid;
		$tcd=M("bll_technician")->where($map1)->find()["techcode"];
		$tca[]=$tcd;
	}
	for($i=0;$i<count($tia);$i++){
		$idcd[$i]["tid"]=$tia[$i];
		$idcd[$i]["tcd"]=$tca[$i];
	}	
	$this->assign("idcd",$idcd);
	//$tca=implode ( ',', array_unique ( $tca) );//去重  转字符
	$this->assign("tca",$tca);
	//$this->assign("tia",$order["tid"]);
	$this->assign("orderno",I("orderno"));
	$this->display();
}


//结算记录
public function my_order(){
	if(I("type") != 2) $type=1;
	else $type = 2;
	
	$userinfo=session("userinfo");
	
	$this->assign("userinfo",$userinfo);
	$this->assign("type",$type);
	$this->display();
}
//充值记录
public function my_cz(){
	if(I("type")==null || I("type")== 1) $type=1;
	else $type = 0;
	
	$userinfo=session("userinfo");
	
	$this->assign("userinfo",$userinfo);
	$this->assign("type",$type);
	$this->display();
}
//我的卡卷
public function my_card(){

	if(I("type") == 1) $type=1;
	elseif(I("type") ==2 )$type=2;
	else $type = 0;
	$userinfo=session("userinfo");
	
	//处理过期卡券
//	$map["userid"]=$userinfo["userid"];
//	$map["status"] = 0;
//	
//	$couponlist=M("bll_coupon_list");
//foreach($couponlist->where($map)->select() as $key=>$value){
//		$map1["yid"]=$value["yid"];
//		$coupon=M("bll_coupon")->where($map1)->find();
//		if($coupon || $coupon !=null){
//			$yenddate=strtotime($coupon["yenddate"]);
//			if(time()>$yenddate){
//				$value["status"]=2;
//				$couponlist->data($value)->save();
//			}
//		}
//}
	
	$this->assign("userinfo",$userinfo);
	$this->assign("type",$type);
	$this->display();
}


//开票
public function kp(){
	$orderno=I("orderno");
	$map["orderno"]=$orderno;
	if(IS_POST){
		if(strlen($orderno)!=25){
			$this->error("提交信息出错，申请失败，请重试！");		
			exit;
		}
		$lx=I("lx");
		$sbh=I("sbh");
		$qc=I("qc");
		$xm=I("xm");
		$address=I("address");
		$name=I("name");
		$tel=I("tel");
		if($qc==null || $address==null || $name==null || $tel==null){
			$this->error("信息填写不完整，申请失败，请重试！");		
			exit;
		}
		
		
		$data["kptype"]=1;
		$data["lx"]=$lx;
		$data["sbh"]=$sbh;
		$data["qc"]=$qc;
		$data["xm"]=$xm;
		$data["address"]=$address;
		$data["name"]=$name;
		$data["tel"]=$tel;
		if(M("bll_cz")->where($map)->save($data)){
			$this->success("申请成功！",U("my_cz"));		
			exit;
		}else{
			$this->error("申请失败或信息无改动，请重试！");		
			exit;
		}
	}
	$kpinfo=M("bll_cz")->where($map)->find();
	$this->assign("kp",$kpinfo);
	$this->assign("orderno",$orderno);
	$this->display();
}

//个人设置
public function seting(){
	$userinfo=session("userinfo");
	if(IS_POST){
		$username=I("username");
		$sex=I("sex");
		$birthday=I("birthday");
		if($username==$userinfo["username"]){		
			$this->success("保存成功！");		
			exit;
		}
		$map["userid"]=$userinfo["userid"];
		$userinfo["username"]=I("username");
		//$userinfo["sex"]=I("sex");
		//$userinfo["birthday"]=I("birthday");
		if(M("bll_userinfo")->where($map)->save($userinfo)){
			$this->success("保存成功！");		
			exit;
		}else{
			$this->error("保存失败！");		
			exit;
		}
	}
	$mobile=$userinfo["mobile"];
	//$mobile=substr($mobile,0,3)."****".substr($mobile,7,4);
	$this->assign("userinfo",$userinfo);
	$this->assign("mobile",$mobile);
	$this->display();
}

//资金明细
public function funds_details(){
	$userinfo=session("userinfo");
	$this->assign("userinfo",$userinfo);
	$this->display();
}

//优惠券明细
public function coupon_details(){
	$rid=I("rid");
	$map["rid"]=$rid;
	$map['userid']=session('userinfo.userid');
	if(M("bll_coupon_list")->where($map)->count() == 0){
		$this->error("参数错误，请重试！");		
		exit;		
	}
	$this->assign("rid",$rid);
	$this->display();
}

//团购券详情
public function tgq_details(){
	$rid=I("rid");
	$map["rid"]=$rid;
	$map['userid']=session('userinfo.userid');
	if(M("view_couponuser")->where($map)->count() == 0){
		$this->error("参数错误，请重试！");		
		exit;		
	}
	$tgq=M("view_couponuser")->where($map)->find();
	unset($map);
	$map["pid"]=$tgq['pid'];
	$map["status"]=1;//已审核
	$project=M("bll_project")->where($map)->find();
	$project["pimg"]=$this->get_pic("project",$tgq['pid']);
	$this->assign("project",$project);
	$this->assign("shopid",$shopid);
	$this->assign("tgq",$tgq);
	$this->display();
}

//微信js
public function jsapi(){
	$info = get_token_appinfo ();
	$appid=$info["appid"];
	$secret=$info["secret"];
	$timestamp=time();
	$nonceStr="daziran";
	$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$jsapi_ticket=$this->get_jsapi_ticket();

	$string="jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;
	$signature=sha1($string);
	$this->assign("appid",$appid);
	$this->assign("timestamp",$timestamp);
	$this->assign("nonceStr",$nonceStr);
	$this->assign("signature",$signature);
}

/*
//微信支付
public function wxpay($SetTotal_fee,$SetOut_trade_no){
	vendor('WXpay.lib.Exception');
  vendor('WXpay.lib.Api');
  vendor('WXpay.lib.JsApiPay');
  vendor('WXpay.lib.Notify');
  vendor('WXpay.lib.Submit');
	$tools = new \JsApiPay();
	//①、获取用户openid
	$openId=session("openid") ;
	//②、统一下单
	$input = new \WxPayUnifiedOrder();
	$input->SetBody("大自然会所余额充值"); //商品描述
	$input->SetAttach("微信钱包余额充值");//附加数据
	$input->SetOut_trade_no($SetOut_trade_no);//商户订单号
	$input->SetTotal_fee($SetTotal_fee*100);//总金额
//		$input->SetTotal_fee(1);//总金额
	$input->SetTime_start(date("YmdHis"));//交易起始时间
	$input->SetTime_expire(date("YmdHis", time() + 600));//交易结束时间
	$input->SetGoods_tag("大自然余额");//商品标记
	$input->SetNotify_url("http://fjhuiye.com/xiaoqu/index.php/addon/Village/Index/notify.html");//通知地址
	$input->SetTrade_type("JSAPI");//交易类型
	$input->SetOpenid($openId);//用户$openId标识
	$wxpayapi=new \WxPayApi();
	$order = $wxpayapi->unifiedOrder($input);
	$jsApiParameters = $tools->GetJsApiParameters($order);
	return $jsApiParameters;
}
*/




//微信充值
public function pay_online(){
	$mobile=session("userinfo")["mobile"];
	if($mobile==null||strlen($mobile)<11){
		$this->error("请先绑定手机号！",U("bind_mobile"));
		exit;
	}
	$map["status"]=1;
	$shoplist=M("bll_shop")->where($map)->select();
	$this->assign("shoplist",$shoplist);
	$map['yx_start']=array('elt',date("Y-m-d H:i:s"));
	$map['yx_end']=array('egt',date("Y-m-d H:i:s"));
	$cztclist=M("bll_recharge")->where($map)->order('rtype desc,czje')->select();
	$this->assign("cztclist",$cztclist);
	$this->display();

}
//微信充值成功
public function cz_success(){
	$order=session("orderno");
	$map["orderno"]=$order;
	$order=M("bll_balanceupdate")->where($map)->find();
	
	$this->assign("order",$order);
	$this->display();
}

//领取优惠券
public function active(){
	if(I("yid")==null || strlen(I("yid"))<1)
	{
		$this->error("优惠券参数错误！",U("index"));
		exit;
	}
	$map["yid"]=I("yid");
	$coupon=M("bll_coupon");
	if($coupon->where($map)->count() == 0){
		$this->error("没有该优惠券！",U("index"));
		exit;
	}
	$coupon=$coupon->where($map)->find();
	if(IS_POST){
		$mobile=session("userinfo")["mobile"];
		if($mobile==null && strlen($mobile)!=11){
			if(I("mobile")!=null &&I("verify")!=null){
				if(session("verify_mobile") != I("mobile")){
					$this->error("获取验证码手机号与提交手机号不一致！");
					exit;
				}
				if(session("verify_code") != I("verify")){
					$this->error("验证码错误！");
					exit;
				}
				if(time()-session("verify_time") > 36000){//10分钟提交有效
					$this->error("验证码过期，请重新获取！");
					exit;
				}
				$map1["userid"]=session("userinfo")["userid"];
				$data["mobile"]=I("mobile");
				$data["loginname"]=I("mobile");
				if(M("bll_userinfo")->where($map1)->save($data)){
					$mobile=I("mobile");
				}else{
					$this->error("绑定失败，请重试！");
					exit;	
				}
			}
		}
		$map["userid"]=session("userinfo")["userid"];
		$couponlist=M("bll_coupon_list");
		if($couponlist->where($map)->count() != 0){
			$this->error("已领取过该优惠券！",U("my_card"));
			exit;
		}
		//存入优惠券
		$itime = date("Y-m-d H:i:s");
		$data1["rid"]=M()->query("CALL SP_GetReqID ('CouID')")[0]["CurID"];
		$data1["yid"]=I("yid");
		$data1["userid"]=session("userinfo")["userid"];
		$data1["lqtime"] = $itime;
		if(M("bll_coupon_list")->add($data1)){
			//下发模板消息
			$shopname=$this->get_shop_name($order["shopid"])["shopname"];
			$pname=$this->get_project_name($order["pid"])["pname"];
			$touser=session("openid");
			$template_id="nZt82R6rFvIx07HusmzMMlC9z9nwbs9ON20fuU4IyB0";
			$url=$this->get_link("lq");
			$topcolor="#FF0000";
			$data=array(
				'first' => array('value'=>"您好！您已成功领取优惠券！",'color'=>"#743A3A"),
				'keyword1' =>array('value'=>$coupon["yname"],'color'=>"#743A3A"),
				'keyword2' =>array('value'=>"满减优惠券",'color'=>"#743A3A"),
				'keyword3' =>array('value'=>session("userinfo")["mobile"],'color'=>"#743A3A"),
				'keyword4' =>array('value'=>$itime,'color'=>"#743A3A"),
				'remark' =>array('value'=>"感谢您的使用！",'color'=>"#743A3A"),
			);
			$this->send_template($touser, $template_id, $url, $topcolor, $data);
			$this->success("领取成功！",U("my_card"));
			exit;
		}else{
			$this->error("领取失败，请重试！");
			exit;
		}
	}

	$mobile=session("userinfo")["mobile"];
	if($mobile==null||strlen($mobile)<11){
		$mbind=0;
	}else{
		$mbind=1;
	}
	$map["userid"]=session("userinfo")["userid"];
	$couponlist=M("bll_coupon_list");
	if($couponlist->where($map)->count() != 0){
		$cbind=1;
	}else{
		$cbind=0;
	}
	
	$this->jsapi();
	$pic = $this->get_pic("coupon",I("yid"));
	$this->assign("pic",$pic);
	$this->assign("userid",session("userinfo.userid"));
	$this->assign("coupon",$coupon);
	$this->assign("cbind",$cbind);
	$this->assign("mbind",$mbind);
	$this->assign("mobile",$mobile);
	$this->display();
}































}

