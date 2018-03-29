<?php
namespace Addons\Bll\Controller;

class LoginController extends CommonController{
	//首页 localhost:81/index.php?s=/addon/Bll/Index/index.html
public function _initialize(){
	
}

//登录
public function login(){
	cookie('loginExpire',0);
	$configargs = array("config" => "D:/php/extras/ssl/openssl.cnf"); 
	$res = openssl_pkey_new($configargs);  
	// Get private key 私钥  
	openssl_pkey_export($res, $privkey, null, $configargs);  
	// Get public key  公钥
	$pubkey =   openssl_pkey_get_details($res); 
	$pubkey =   $pubkey["key"];
	$pubkey = str_replace("\n", "", $pubkey);
	$pubkey = str_replace("\r", "", $pubkey);
	//echo $pubkey;
	//echo $privkey;
	if(I("username")!=null||I("userpwd")!=null){
		if(I("username")!=null){
			if(I("userpwd")!=null){
				$map["loginname"]=safe(I("username"),"text");
				if(M("bll_userinfo")->where($map)->count() > 0){
					$userpwd = base64_decode(I("userpwd"));  
					openssl_private_decrypt($userpwd, $userpwd, session("privkey"));
					$map["password"]=md5(md5($userpwd));
					if(M("bll_userinfo")->where($map)->count() > 0){
						$admininfo=M("bll_userinfo")->where($map)->field("userid")->find();
						session("admininfo",$admininfo);
						cookie("loginExpire",1,1800);
						$this->success("登录成功！",addons_url("Bll://Admin/index"));
						exit;
					}else{$this->error("密码错误！");exit;}
				}else{$this->error("账号不存在！");exit;}
			}else{$this->error("请输入密码！");exit;}
		}else{$this->error("请输入账号！");exit;}
	}

	session("privkey",$privkey);//每次进入登录页面都会重新获取公钥密钥，储存私钥用于提交表单验证
	$this->assign("pubkey",$pubkey);
	$this->display();
}


//安全退出
public function logout(){
	cookie('loginExpire',0);
	session('admininfo',null);
	$this->success("退出成功！",addons_url("Bll://Login/login"));

}
}
