<?php
namespace Addons\Bll\Controller;

class ApptController extends CommonController{

public function index2(){
	$this->display();
}
public function index3(){
	$this->display();
}	
//预约 获取地点距离 选择时间
public function index(){
	$this->display();
}

//门店列表	
public function shop_list(){
	if(IS_POST){
		if(I("jwd")==null || strlen(I("jwd"))<5){
			$this->error("获取位置信息失败，请重新选择！",U("index"));
			exit;	
		}
		if(I("yytime")==null || strlen(I("yytime"))<5){
			$this->error("获取到店时间失败,请重新选择！",U("index"));
			exit;	
		}	
		session("yytime",I("yytime"));
		session("Lng",explode(",", I("jwd"))[0]);
		session("Lat",explode(",", I("jwd"))[1]);
	}
	
	if(session("yytime")==null || strlen(session("yytime")) <5){
		$this->error("获取到店时间失败！",U("index"));
		exit;
	}		

	$this->assign("Lng",session("Lng"));
	$this->assign("Lat",session("Lat"));
	$this->display();
}
//门店列表	
public function shop_list2(){
	if(IS_AJAX){
		if(I("jwd")==null || strlen(I("jwd"))<5){
			exit;	
		}
		session("Lng",explode(",", I("jwd"))[0]);
		session("Lat",explode(",", I("jwd"))[1]);
	}	

	$this->assign("Lng",session("Lng"));
	$this->assign("Lat",session("Lat"));
	$this->display();
}
//门店列表	
public function shop_list3(){
	if(IS_AJAX){
		if(I("jwd")==null || strlen(I("jwd"))<5){
			exit;	
		}
		session("Lng",explode(",", I("jwd"))[0]);
		session("Lat",explode(",", I("jwd"))[1]);
	}	

	$this->assign("Lng",session("Lng"));
	$this->assign("Lat",session("Lat"));
	$this->display();
}
//门店详情 提交预约订单
public function shop_details(){
	if(I("shopid")==null){
		$this->error("获取商铺信息失败！",U("shop_list"));
		exit;
	}
	$map["shopid"]=I("shopid");
	$map["status"]=1;//已审核
	$shop=M("bll_shop");
	if($shop->where($map)->count()==0){
		$this->error("数据错误！",U("shop_list"));
		exit;
	}
	$shop=$shop->where($map)->find();
	$shop["shopimg"]=$this->get_pic("shop",$shop["shopid"]);

	$projectlist=M("bll_project")->where($map)->order('sorts')->select();
	foreach ($projectlist as $key => $value) {
		$value["pimg"]=$this->get_pic("project",$value["pid"]);
		$projectlist[$key]=$value;
	}

	$this->assign("shop",$shop);
	$this->assign("projectlist",$projectlist);
	$this->display();
}

//门店详情 提交预约订单
public function shop_details2(){
	if(IS_AJAX){
		$result['code']=0;
		if(I("shopid")==null){
			$result['msg']="获取商铺信息失败！";
			$this->ajaxReturn($result);
			exit;
		}
		$shop=M("bll_shop")->where(["shopid"=>I("shopid")])->find();
		if($shop==null){
			$result['msg']="获取商铺信息失败！";
			$this->ajaxReturn($result);
			exit;	
		}
		
		$mobile=session("userinfo")["mobile"];
		if($mobile==null||strlen($mobile)<11){
			$result['code']=2;
			$result['msg']=U("bind_mobile",array("shopid"=>I("shopid")));
			$this->ajaxReturn($result);
			exit;
		}
		
		$kid=I("kid");
		$uid=session("userinfo")["userid"];
		$gj=M("bll_userinfo")->where(["userid"=>$kid])->find();
		if($gj==null){
			$result['msg']="数据出错!";
			$this->ajaxReturn($result);
			exit;
		}
		$yylist=M("bll_kfyyorder");
		$yyjl=$yylist->where(['memberUserid'=>$uid,'shopid'=>I('shopid')])->order("insertdate desc")->find();
		if($yyjl!=null && (time()-strtotime($yyjl['insertdate']))<=(60*10)){
			$result['msg']="预约过于频繁,请稍候重试！";
			$this->ajaxReturn($result);
			exit;
		}
		$orderno=date("YmdHis").rand(10000,99999);
		$data["orderno"]=$orderno;
		$data["memberUserid"]=$uid;
		$data["shopid"]=I("shopid");
		$data["kfUserid"]=$kid;
		if($yylist->add($data)==1){
			$result['code']=1;
			$result['shopname']=$shop['shopname'];
			$result['gjname']=$gj['username'];
			$result['gjtel']=$gj['mobile'];
			$result['msg']="预约成功！";
		}else{
			$result['msg']="预约失败,请稍候重试！";
		}
		$this->ajaxReturn($result);
		exit;
	}
	if(I("shopid")==null){
		$this->error("获取商铺信息失败！",U("shop_list2"));
		exit;
	}
	$map["shopid"]=I("shopid");
	$map["status"]=1;//已审核
	$shop=M("bll_shop");
	if($shop->where($map)->count()==0){
		$this->error("数据错误！",U("shop_list2"));
		exit;
	}
	$shop=$shop->where($map)->find();
	$shop["shopimg"]=$this->get_pic("shop",$shop["shopid"]);

	unset($map);
	$map["shopid"]=I("shopid");
	$map["state"]=1;//已审核
	$map["roleid"]=4;//已审核
	$gjialist=M("bll_userinfo")->where($map)->order('sorts')->select();
	foreach ($gjialist as $key => $value) {
		$value["pimg"]=$this->get_pic("user",$value["userid"]);
		$gjialist[$key]=$value;
	}

	$this->assign("shop",$shop);
	$this->assign("gjialist",$gjialist);
	$this->display();
}
//门店详情 提交预约订单
public function shop_details3(){
	if(I("shopid")==null){
		$this->error("获取商铺信息失败！",U("shop_list"));
		exit;
	}
	$map["shopid"]=I("shopid");
	$map["status"]=1;//已审核
	$shop=M("bll_shop");
	if($shop->where($map)->count()==0){
		$this->error("数据错误！",U("shop_list"));
		exit;
	}
	$shop=$shop->where($map)->find();
	$shop["shopimg"]=$this->get_pic("shop",$shop["shopid"]);

	$projectlist=M("bll_project")->where($map)->order('sorts')->select();
	foreach ($projectlist as $key => $value) {
		$value["pimg"]=$this->get_pic("project",$value["pid"]);
		$projectlist[$key]=$value;
	}

	$this->assign("shop",$shop);
	$this->assign("projectlist",$projectlist);
	$this->display();
}
//门店详情 提交预约订单
public function shop_details4(){
	if(I("shopid")==null){
		$this->error("获取商铺信息失败！",U("shop_list"));
		exit;
	}
	$map["shopid"]=I("shopid");
	$map["status"]=1;//已审核
	$shop=M("bll_shop");
	if($shop->where($map)->count()==0){
		$this->error("数据错误！",U("shop_list"));
		exit;
	}
	$shop=$shop->where($map)->find();
	$shop["shopimg"]=$this->get_pic("shop",$shop["shopid"]);

	$projectlist=M("bll_project")->where($map)->order('sorts')->select();
	foreach ($projectlist as $key => $value) {
		$value["pimg"]=$this->get_pic("project",$value["pid"]);
		$projectlist[$key]=$value;
	}

	$this->assign("shop",$shop);
	$this->assign("projectlist",$projectlist);
	$this->display();
}
//项目预约
public function appt_order(){
	if(I("pid")==null){
		$this->error("获取项目信息失败！",U("shop_list"));
		exit;
	}
	if(session("yytime")==null || strlen(session("yytime")) <5){
		$this->error("获取到店时间失败！",U("index"));
		exit;
	}
	$map["pid"]=I("pid");
	$map["status"]=1;//已审核
	$project=M("bll_project");
	if($project->where($map)->count()==0){
		$this->error("数据错误！",U("shop_list"));
		exit;
	}
	$project=$project->where($map)->find();
	
	if(I("tid")!=null || I("pamount")!=null || I("yytime")!=null || I("num")!=null){
		$mobile=session("userinfo")["mobile"];
		if($mobile==null||strlen($mobile)<11){
			$this->error("请先绑定手机号！",U("bind_mobile",array("pid"=>$project["pid"])));
			exit;
		}
		if(I("tid")==null ||  I("pamount")==null || I("num")==null){
			$this->error("提交信息有误！",U("appt_order",array("pid"=>$project["pid"])));
			exit;					
		}
		
		$data["orderno"]=M()->query("CALL SP_GetReqID ('Yyorder')")[0]["CurID"];
		$data["userid"]=session("userinfo")["userid"];
		$data["shopid"]=$project["shopid"];
		$data["tid"]=I("tid");
		$data["pid"]=I("pid");
		$data["pamount"]=I("pamount");
		$data["yytime"]=session("yytime");
		$data["num"]=I("num");
		if(M("bll_yyorder")->add($data)){
			$this->redirect('redirect_success',array("pid"=>$project["pid"],"orderno"=>$data["orderno"]));
			exit;				
		}else{
				$this->redirect('redirect_error',array("pid"=>$project["pid"],"orderno"=>$data["orderno"]));
			exit;
		}
	}
	//获取项目的技师ID列表
	$ptia = array();
	$map1["pid"]=I("pid");
	foreach(M("bll_item_tech")->where($map1)->select() as $key => $value){
		$ptia[]=$value["tid"];
	}
	
	//获取可预约技师ID列表
	//$tca = array();
	foreach($ptia as $value){
		$map2["tid"] = $value;
		$map2["status"]=1;
		$tech=M("bll_technician")->where($map2)->find();
		if($tech!=null){
			$map3["status"]=array('neq',3);
			$map3["tid"]=array('like','%'.$tech['tid'].'%');
			//$map3["userid"]=session("userinfo")["userid"];
			$yyorder=M("bll_yyorder")->where($map3)->select();
			$yytype=1;//为1技师可预约
			if($yyorder != null){
				foreach($yyorder as $key=>$value1)
				{
					$byytime=strtotime($value1["yytime"]);
					$yytime=strtotime(session("yytime"));
					if($byytime-4800<$yytime && $yytime<$byytime+4800 ){
						//被预约
						$yytype=0;
						continue;
					}
				}
			}
			if($yytype){
				//可预约
				$tia[]=$value;
			}else{
				$tia[]= $value."x";
			}
		}
	}
	//获取技师编号列表
	$tca=array();
	foreach($tia as $value){
		$map2["tid"]=$value;
		if(strpos($value,"x")){
			$map2["tid"]=substr($value,0,-1);
		}
		$tca[]=M("bll_technician")->where($map2)->find()["techcode"];
	}
	//添加技师ID
	//$tia = implode ( ',', array_unique ( $tia) );//去重  转字符
	//$project["tia"] = $tia;
	//添加技师编号
	//$tca = implode ( ',', array_unique ( $tca) );//去重  转字符
	//$project["tca"] = $tca;
	$project["idcd"]=array_combine($tia,$tca);//组合
	asort($project["idcd"]);//排序
	$project["pimg"]=$this->get_pic("project",$project["pid"]);
	$this->assign("project",$project);
	$this->display();	
}




//绑定手机号
public function bind_mobile(){
	if(I("pid")==null && I("shopid")==null){
		$this->error("获取项目信息失败！",U("index2"));
		exit;
	}
	$mobile=session("userinfo")["mobile"];
	if($mobile!=null && strlen($mobile)==11){
		if(I("pid")!=null){
			$this->error("已经绑定过手机号！",U("appt_order",array("pid"=>I("pid"))));
		}
		if(I("shopid")!=null){
			$this->error("已经绑定过手机号！",U("shop_details2",array("shopid"=>I("shopid"))));
		}
		exit;
	}
	
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
			if(I("pid")!=null){
				$this->success("绑定成功！",U("appt_order",array("pid"=>I("pid"))));
			}
			if(I("shopid")!=null){
				$this->success("绑定成功！",U("shop_details2",array("shopid"=>I("shopid"))));
			}
			exit;
		}else{
			$this->error("绑定失败，请重试！");
			exit;	
		}
	}
	$this->assign("pid",I("pid"));
	$this->assign("shopid",I("shopid"));
	$this->assign("userid",session("userinfo")["userid"]);
	$this->display();
}

//项目详情
public function project_details(){
	if(I("shopid")==null){
		$this->error("获取商铺信息失败！",U("shop_list"));
		exit;
	}
	if(I("pid")==null){
		$this->error("获取项目信息失败！",U("shop_details",array("shopid"=>I("shopid"))));
		exit;
	}

	$shopid=I("shopid");
	$pid=I("pid");

	$map["pid"]=$pid;
	$map["status"]=1;//已审核
	$project=M("bll_project")->where($map)->find();
	$project["pimg"]=$this->get_pic("project",$pid);
	
	$evnum=M("bll_yyorder_evaluate")->where($map)->count();
	$this->assign("evnum",$evnum);
	$this->assign("project",$project);
	$this->assign("shopid",$shopid);
	$this->display();
}

//项目详情3
public function project_details3(){
	if(I("shopid")==null){
		$this->error("获取商铺信息失败！",U("shop_list"));
		exit;
	}
	if(I("pid")==null){
		$this->error("获取项目信息失败！",U("shop_details",array("shopid"=>I("shopid"))));
		exit;
	}

	$shopid=I("shopid");
	$pid=I("pid");

	$map["pid"]=$pid;
	$map["status"]=1;//已审核
	$project=M("bll_project")->where($map)->find();
	$project["pimg"]=$this->get_pic("project",$pid);
	$this->assign("project",$project);
	$this->assign("shopid",$shopid);
	$this->display();
}

//管家详情
public function gjia_details(){
	if(I("shopid")==null){
		$this->error("获取商铺信息失败！",U("shop_list2"));
		exit;
	}
	if(I("uid")==null){
		$this->error("获取管家信息失败！",U("shop_details2",array("shopid"=>I("shopid"))));
		exit;
	}

	$shopid=I("shopid");
	$uid=I("uid");

	$map["userid"]=$uid;
	$map["state"]=1;//已审核
	$gjia=M("bll_userinfo")->where($map)->find();
	$gjia["pimg"]=$this->get_pic("user",$uid);
	
	$this->assign("gjia",$gjia);
	$this->assign("shopid",$shopid);
	$this->display();	
}

//技师评价
public function tech_evaluate(){
	if(I("pid")==null){
		$this->error("获取项目信息失败！",U("shop_list"));
		exit;
	}
	if(I("tid")==null ){
		$this->error("获取技师信息失败！",U("shop_details",array("shopid"=>I("shopid"))));
		exit;
	}
	$pid=I("pid");
	$tid=I("tid");

	$map["tid"]=$tid;
	$technician=M("bll_technician")->where($map)->find();
	if($technician["techsex"] == 1) $technician["timg"] = "http://wx.dzrspa.cn/Files/tech/male.jpg";
	elseif($technician["techsex"] == 0) $technician["timg"] = "http://wx.dzrspa.cn/Files/tech/female.jpg";
	$map1["tid"]=$tid;
	$map1["status"]=1;//已审核
	$evnum=M("bll_tech_evaluate")->where($map1)->count();
	$this->assign("evnum",$evnum);
	$this->assign("technician",$technician);
	$this->assign("pid",$pid);
	$this->display();	
}



//预约成功跳转
public function redirect_success(){
	if(I("pid")==null){
		$this->error("获取项目信息失败！",U("shop_list"));
		exit;
	}
	$map["pid"]=I("pid");
	$map["status"]=1;//已审核
	$project=M("bll_project");
	if($project->where($map)->count()==0){
		$this->error("项目信息错误！",U("shop_list"));
		exit;
	}
	$project=$project->where($map)->find();
	$this->assign("project",$project);
	
	if(I("orderno")==null){
		$this->error("获取预约订单信息失败！",U("appt_order",array("pid"=>I("pid"))));
		exit;
	}	
	
	unset($map);
	$map["orderno"]=I("orderno");
	$order=M("bll_yyorder");
	if($order->where($map)->count()==0){
		$this->error("预约订单信息错误！",U("appt_order",array("pid"=>I("pid"))));
		exit;
	}
	$order=$order->where($map)->find();
	$this->assign("order",$order);
	
	unset($map);
	$map["shopid"]=$order["shopid"];
	$shopname=M("bll_shop")->where($map)->find()["shopname"];
	$this->assign("shopname",$shopname);
	
	unset($map);
	$map["pid"]=$order["pid"];
	$p=M("bll_project")->where($map)->find();
	$pname=$p["pname"];
	$this->assign("pname",$pname);
	$lxfs=$p["tel"];
	$this->assign("lxfs",$lxfs);
	
	unset($map);
	$tia=explode(",", $order["tid"]);
	$tca=array();
	foreach($tia as $tid){
		$map["tid"]=$tid;
		$tcd=M("bll_technician")->where($map)->find()["techcode"];
		$tca[]=$tcd;
	}
	$tca=implode ( '　', array_unique ( $tca) );//去重  转字符
	$this->assign("tca",$tca);
	$this->display();
}
//预约失败跳转
public function redirect_error(){
	if(I("pid")==null){
		$this->error("获取项目信息失败！",U("shop_list"));
		exit;
	}
	$map["pid"]=I("pid");
	$map["status"]=1;//已审核
	$project=M("bll_project");
	if($project->where($map)->count()==0){
		$this->error("数据错误！",U("shop_list"));
		exit;
	}
	$project=$project->where($map)->find();
	$this->assign("project",$project);
	$this->display();
}












}