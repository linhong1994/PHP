<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------


// 应用公共文件

// 绑定当前访问到index模块的index控制器
define('BIND_MODULE','index');

//获取网页内容  效率比较高
function curl_file_get_contents($durl){
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
function curl_post($url,$param){
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
	//\Think\Log::write('到curl','WARN');
  return $data;
}

//TODO:微信充值
function wxpay($arr){
  vendor('wxpay.lib.Api');
  vendor('wxpay.lib.JsApiPay');
	$tools = new \JsApiPay();
	//①、获取用户openid
	$openId=session('openid') ;
	//②、统一下单
	$input = new \WxPayUnifiedOrder();
	$input->SetBody($arr['body']); //商品描述
	$input->SetAttach($arr['attach']);//附加数据
	$input->SetOut_trade_no($arr['oid']);//商户订单号
	$input->SetTotal_fee($arr['fee']*100);//总金额
	$input->SetTime_start(date('YmdHis'));//交易起始时间
	$input->SetTime_expire(date('YmdHis', time() + 600));//交易结束时间
	$input->SetGoods_tag($arr['tag']);//商品标记
	$input->SetNotify_url($arr['url']);//通知地址
	$input->SetTrade_type('JSAPI');//交易类型
	$input->SetOpenid($openId);//用户$openId标识
	$wxpayapi=new \WxPayApi();
	$order = $wxpayapi->unifiedOrder($input);
	$jsApiParameters = $tools->GetJsApiParameters($order);
	return $jsApiParameters;
}

//TODO:记录当前地址 
function into(){
	$url=request()->url();
	//以null结尾的url不记录
	if(strpos($url,'null')+4 == strlen($url)){
		exit;
	}
	if(session('?url')){
		$urlist=session('url');
	}else{
		$urlist=array('/index.html');
	}


	//列表页搜索关键词编码
	if(strpos($url,'goodslist')!==FALSE){
		if(strpos($url,'/s/1')!==FALSE || strpos($url,'/k/1')!==FALSE || strpos($url,'/i/1')!==FALSE){
			$url=mb_convert_encoding($url, 'UTF-8', 'EUC-CN');
		}		
	}
	if($url == '/index.html' || $url == '/index' || $url == '/'){
		//返回首页清空
		$urlist=array('/index.html');
	}else{
		//特殊规则
		//完全相同的删除后面所有
		$key=array_search($url,$urlist);
		if($key != false){
			array_splice($urlist,$key);
		}
		//匹配 xxx.thml
		

		
		//添加当前url
		$urlist[]=$url;
	}

	session('url',$urlist);
}

//TODO:取出最后记录地址  
function outo(){
	if(session('?url')){
		$url=session('url');
	}else{
		$url=array('/index');
	}
	return $url[count($url)-2];
}

function synum($a,$b)
{
if ($a['synum']==$b['synum']) return 0;
return ($a['synum']<$b['synum'])?-1:1;
}

function mmamount($a,$b)
{
if ($a['mmamount']==$b['mmamount']) return 0;
return ($a['mmamount']<$b['mmamount'])?1:-1;
}

function cxtype($a,$b)
{
if ($a['cxtype']==$b['cxtype']) return 0;
return ($a['cxtype']<$b['cxtype'])?-1:1;
}

function mmnum($a,$b)
{
if ($a['mmnum']==$b['mmnum']) return 0;
return ($a['mmnum']<$b['mmnum'])?1:-1;
}

function sx($var){
	if($var['cxtype']==session('cxtype')){
		return TRUE;
	}else{
		return FALSE;
	}
}

function fsx($var){
	if($var['cxtype']!=session('cxtype')){
		return TRUE;
	}else{
		return FALSE;
	}
}