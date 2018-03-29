<?php
namespace Addons\Bll\Controller;

class AdminController extends CommonController{
//首页 localhost:81/index.php?s=/addon/Bll/Index/index.html
public function _initialize(){
//		if(! isWeixinBrowser()){
//			$this->error("请在微信客户端访问!",addons_url("Bll://Login/login"),10000);
//			exit;
//		}
//		if(get_token()==-1){
//			session("token", "gh_cb632ea03362");//配置token为公众号原始ID
//		}
//		if(get_openid()==-1){
//			$this->error("获取openid失败!",addons_url("Bll://Login/login"),10000);
//			exit;
//		}
	//判断是否登录并刷新登录过期时间
	if(!$this->is_logout()){
		$this->error("请登录!",addons_url("Bll://Login/login"));
	}
}
public function index(){
	$this->display();
}

//门店登记
public function shop_register(){
	$admininfo = session("admininfo");
	$map["userid"] = $admininfo["userid"];
	$set_pic=false;
	$shop=M("bll_shop")->where($map)->find();
	if(I("shopname")!=null||I("longgitude")!=null||I("latitude")!=null||I("shopdaddr")!=null||I("lxfs")!=null||I("shopmark")!=null){
		//判断输入
		if(I("shopname")==null){
			$this->error("请输入门店名称！");exit;
		}
		if(I("longgitude")==null || I("latitude")==null){
			$this->error("请输入在地图上选择位置！");exit;
		}
		if(I("shopdaddr")==null){
			$this->error("请输入门店地址！");exit;
		}
		if(I("lxfs")==null){
			$this->error("请输入联系方式！");exit;
		}
		if(I("shopmark")==null){
			$this->error("请输入门店简介！");exit;
		}
		
		$data = I("post.");
		//判断门店是否已经存在
		if($shop){//修改
			if($shop["status"]>0){
				$this->error("已经审核通过，无法修改！");exit;
			}
			if($_FILES["shopimg"]["size"]>0){
				$shopimg = $this->upload_file(array('jpg','gif','png','jpeg'),".".ADDON_PUBLIC_PATH."/img/","shopimg",$shop["shopid"],$_FILES["shopimg"]);
				if(!$shopimg){
					$this->error("上传图片失败，请重试！");exit;
				}
				$shopimg="http://192.168.0.20:81".ADDON_PUBLIC_PATH."/img/".$shop["shopid"]."/".$shopimg['savename'];
				//$data["shopimg"]=$shopimg;
				$set_pic = $this->set_pic("shop",$shop["shopid"],$shopimg);
			}
			
			if(M("bll_shop")->where($map)->save($data) || $set_pic){
				$this->success("修改成功！");exit;
			}else{
				$this->error("修改失败或无改动！");exit;
			}
			
		}else{//添加
			if($_FILES["shopimg"]["size"]<=0){
				$this->error("请上传图片！");exit;
			}
			$shopid = M()->query("CALL SP_GetReqID ('Shop')")[0]["CurID"];
			$shopimg = $this->upload_file(array('jpg','gif','png','jpeg'),".".ADDON_PUBLIC_PATH."/img/","shopimg",$shopid,$_FILES["shopimg"]);
			if(!$shopimg){
				$this->error("上传图片失败，请重试！");exit;
			}
			$shopimg="http://192.168.0.20:81".ADDON_PUBLIC_PATH."/img/".$shopid."/".$shopimg['savename'];
			//$data["shopimg"] = $shopimg;
			$set_pic = $this->set_pic("shop",$shopid,$shopimg);
			$data["userid"] = $admininfo["userid"];
			$data["shopid"] = $shopid;
			if(M("bll_shop")->data($data)->add() && $set_pic){
				$this->success("登记成功！");exit;
			}else{
				$this->error("登记失败！");exit;
			}
		}
	}
	if($shop){
		$shop["shopimg"]=$this->get_pic("shop",$shop["shopid"]);
		$this->assign("shop",$shop);
		$this->assign("empty",0);
	}else{
		$this->assign("empty",1);
	}
	$this->display();
}

//项目列表
public function project_list(){
	$map["userid"] = session("admininfo")["userid"];
	$shop=M("bll_shop")->where($map)->find();
	if(!$shop){
		$this->error("请先登记门店！",U("shop_register"));exit;
	}
	if(!$shop["status"]){
		$this->error("请等待门店登记审核通过！",U("shop_register"));exit;
	}
	$map1["shopid"]=$shop["shopid"];
	$project=M("bll_project")->where($map1)->select();
	foreach ($project as $key => $value){
		$value["pimg"]=$this->get_pic("project",$value["pid"]);
		$project[$key]=$value;
	}
	$this->assign("project",$project);
	$this->display();
}

//项目编辑
public function edit_project(){
	$map["userid"] = session("admininfo")["userid"];
	$set_pic=false;
	$shop=M("bll_shop")->where($map)->find();
	if(!$shop){
		$this->error("请先登记门店！",U("shop_register"));exit;
	}
	if(!$shop["status"]){
		$this->error("请等待门店登记审核通过！",U("shop_register"));exit;
	}
	if(I("pname")!=null||I("premark")!=null||I("pamount")!=null){
		//判断输入
		if(I("pname")==null){
			$this->error("请输入项目名称！");exit;
		}
		if(I("premark")==null){
			$this->error("请输入项目简介！");exit;
		}
		if(I("pamount")==null){
			$this->error("请输入项目价格！");exit;
		}
		
		$data = I("post.");
		if(I("pid")){//修改
			$map1["pid"]=I("pid");
			$map1["shopid"]=$shop["shopid"];
			$project=M("bll_project");
			if(!$project->where($map1)->find()){
				$this->error("项目不存在！",U("project_list"));exit;
			}
			if($project->where($map1)->find()["status"]>0){
				$this->error("已经审核通过，无法修改！",U("project_list"));exit;
			}
			//上传图片
			if($_FILES["pimg"]["size"]>0){
				$pimg = $this->upload_file(array('jpg','gif','png','jpeg'),".".ADDON_PUBLIC_PATH."/img/","pimg",$shop["shopid"],$_FILES["pimg"]);
				if(!$pimg){
					$this->error("上传图片失败，请重试！");exit;
				}
				$pimg="http://192.168.0.20:81".ADDON_PUBLIC_PATH."/img/".$shop["shopid"]."/".$pimg['savename'];
				//$data["pimg"]=$pimg;
				$set_pic = $this->set_pic("project",I("pid"),$pimg);
			}
			//上传数据
			if($project->where($map1)->save($data) || $set_pic){
				$this->success("修改成功！",U("project_list"));exit;
			}else{
				$this->error("修改失败或无改动！",U("edit_project",array("pid"=>I("pid"))));exit;
			}
		}else{//添加
			if($_FILES["pimg"]["size"]<=0){
				$this->error("请上传图片！");exit;
			}
			$pimg = $this->upload_file(array('jpg','gif','png','jpeg'),".".ADDON_PUBLIC_PATH."/img/","pimg",$shop["shopid"],$_FILES["pimg"]);
			if(!$pimg){
				$this->error("上传图片失败，请重试！");exit;
			}
			$pimg="http://192.168.0.20:81".ADDON_PUBLIC_PATH."/img/".$shop["shopid"]."/".$pimg['savename'];
			$pid = M()->query("CALL SP_GetReqID ('Project')")[0]["CurID"];
			//$data["pimg"] = $pimg;
			$set_pic = $this->set_pic("project",$pid,$pimg);
			$data["pid"] = $pid;
			$data["shopid"] = $shop["shopid"];
			if(M("bll_project")->data($data)->add() && $set_pic){
				$this->success("登记成功！",U("project_list"));exit;
			}else{
				$this->error("登记失败！",U("edit_project",array("pid"=>I("pid"))));exit;
			}
		}
	}

	$empty = 1;
	if(I("pid")){//浏览项目
		$map1["pid"]=I("pid");
		$map1["shopid"]=$shop["shopid"];
		$project=M("bll_project")->where($map1)->find();
		if(!$project){
			$this->error("项目不存在！",U("project_list"));exit;
		}
		$empty = 0;
		$project["pimg"]=$this->get_pic("project".$project["pid"]);
		$this->assign("project",$project);
	}
	$this->assign("empty",$empty);
	//1 空白添加模板
	$this->display();
}


//技师列表
public function technician_list(){
	$map["userid"] = session("admininfo")["userid"];
	$shop=M("bll_shop")->where($map)->find();
	if(!$shop){
		$this->error("请先登记门店！",U("shop_register"));exit;
	}
	if(!$shop["status"]){
		$this->error("请等待门店登记审核通过！",U("shop_register"));exit;
	}
	$map1["shopid"]=$shop["shopid"];
	$technician=M("bll_technician")->where($map1)->select();
	foreach ($technician as $key => $value){
		$value["techheadimg"]=$this->get_pic("technician",$value["tid"]);
		$technician[$key]=$value;
	}
	$this->assign("technician",$technician);
	$this->display();	

}




//技师编辑
public function edit_technician(){
	$map["userid"] = session("admininfo")["userid"];
	$shop=M("bll_shop")->where($map)->find();
	if(!$shop){
		$this->error("请先登记门店！",U("shop_register"));exit;
	}
	if(!$shop["status"]){
		$this->error("请等待门店登记审核通过！",U("shop_register"));exit;
	}
	if(I("techname")!=null||I("techcode")!=null||I("techsex")!=null||I("tremark")!=null){
		//判断输入
		if(I("techname")==null){
			$this->error("请输入技师姓名！");exit;
		}

		if(I("tremark")==null){
			$this->error("请输入技师简介！");exit;
		}
		
		$data = I("post.");
		if(I("tid")){//修改
			$map1["tid"]=I("tid");
			$map1["shopid"]=$shop["shopid"];
			$technician=M("bll_technician");
			if(!$technician->where($map1)->find()){
				$this->error("技师不存在！",U("technician_list"));exit;
			}
			if($technician->where($map1)->find()["status"]>0){
				$this->error("已经审核通过，无法修改！",U("technician_list"));exit;
			}
			//上传图片
			if($_FILES["techheadimg"]["size"]>0){
				$techheadimg = $this->upload_file(array('jpg','gif','png','jpeg'),".".ADDON_PUBLIC_PATH."/img/","techheadimg",$shop["shopid"],$_FILES["techheadimg"]);
				if(!$techheadimg){
					$this->error("上传图片失败，请重试！");exit;
				}
				$techheadimg="http://192.168.0.20:81".ADDON_PUBLIC_PATH."/img/".$shop["shopid"]."/".$techheadimg['savename'];
				//$data["techheadimg"]=$techheadimg;
				$set_pic = $this->set_pic("technician",I("tid"),$techheadimg);
			}
			//上传数据
			if($technician->where($map1)->save($data) || $set_pic){
				$this->success("修改成功！",U("technician_list"));exit;
			}else{
				$this->error("修改失败或无改动！",U("edit_technician",array("tid"=>I("tid"))));exit;
			}
		}else{//添加
			if($_FILES["techheadimg"]["size"]<=0){
				$this->error("请上传图片！");exit;
			}
			$techheadimg = $this->upload_file(array('jpg','gif','png','jpeg'),".".ADDON_PUBLIC_PATH."/img/","techheadimg",$shop["shopid"],$_FILES["techheadimg"]);
			if(!$techheadimg){
				$this->error("上传图片失败，请重试！");exit;
			}
			$techheadimg="http://192.168.0.20:81".ADDON_PUBLIC_PATH."/img/".$shop["shopid"]."/".$techheadimg['savename'];
			
			$tid = M()->query("CALL SP_GetReqID ('Technician')")[0]["CurID"];
			//$data["techheadimg"] = $techheadimg;
			$set_pic = $this->set_pic("technician",$tid,$techheadimg);
			$data["tid"] = $tid;
			$data["shopid"] = $shop["shopid"];
			if(M("bll_technician")->data($data)->add() && $set_pic){
				$this->success("登记成功！",U("technician_list"));exit;
			}else{
				$this->error("登记失败！",U("edit_technician",array("tid"=>I("tid"))));exit;
			}
		}
	}

	$empty = 1;
	if(I("tid")){//浏览技师
		$map1["tid"]=I("tid");
		$map1["shopid"]=$shop["shopid"];
		$technician=M("bll_technician")->where($map1)->find();
		if(!$technician){
			$this->error("技师不存在！",U("technician_list"));exit;
		}
		$empty = 0;
		$technician["techheadimg"]=$this->get_pic("technician",$technician["tid"]);
		$this->assign("technician",$technician);
	}
	$this->assign("empty",$empty);
	//全FALSE则为空白添加模板
	$this->display();
}















}