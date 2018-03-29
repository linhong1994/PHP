<?php
namespace app\controller;
use think\Request;
use think\Session;
use think\Db;
use think\Loader;
use app\model\Balanceupdate;
use app\model\Category;
use app\model\Coupon_list;
use app\model\Coupon;
use app\model\Cxdp;
use app\model\Cxmaster;
use app\model\Cz;
use app\model\Fullcar;
use app\model\Fullcx;
use app\model\Fulldhjl;
use app\model\Fullfous;
use app\model\Fullpdt;
use app\model\Fullpe;
use app\model\Fullsale;
use app\model\Fullyhq;
use app\model\Fullzp;
use app\model\Integralupdate;
use app\model\Jsorder;
use app\model\Menupic;
use app\model\News;
use app\model\Plistnew;
use app\model\Plistprice;
use app\model\Product;
use app\model\Productevaluate;
use app\model\Productspec;
use app\model\Productstock;
use app\model\Productunit;
use app\model\Saleorder;
use app\model\Searchword;
use app\model\Shop;
use app\model\Shopcar;
use app\model\Sysset;
use app\model\Useraddr;
use app\model\Userfous;
use app\model\Userinfo;
use app\model\Verify;
use app\model\Wlcompany;
use app\model\Wx_config;
use app\model\Zndxx;

class Index extends Common
{
//TODO:测试
function test(){

	$idstr='';
	strlen($idstr);
	var_dump(strlen($idstr));
	$like= new Fullzp();
	$like=$like->where('zid','in',$idstr)->select();
	var_dump($like);
	//var_dump($fhyhq);
	//var_dump($cxfa->sort('cxtype')->sort('mmnum')->filter('sx')->toArray());
	exit;
}

//页面按字母排序
//TODO:A 新增收货地址
function address(){
	$uid=session('user.userid');
	$map['userid']=$uid;

	$useraddr=new Useraddr();
	if(request()->isAjax()){//提交  新增 编辑
		$postArr=request()->only(['province','city','county','address','lxr','lxfs','ifmr'],'post');
		$validate = Loader::validate('Address');
		if($validate->check($postArr)){
			if(array_key_exists('ifmr',$postArr) && $postArr['ifmr']==1){//默认地址
				$useraddr->undefault();
			}
			$postArr['userid']=$uid;
			if(input('rid')==null){
				$useraddr=$useraddr->save($postArr);
			}else{
				$useraddr=$useraddr->save($postArr,['rid'=>input('rid')]);
			}
			if($useraddr){
				$this->success('操作成功', outo());
			}else{

				$this->error('操作失败');
			}
		}else{
			$this->error($validate->getError());
		}

	}else{//空页面 编辑页面
		if(input('?rid')){
			$map['rid']=input('rid');
			$useraddr=$useraddr->where($map)->find();
			if($useraddr!=null){
				$this->assign('useraddr',$useraddr);
			}else{
				$this->error('参数错误!');
			}
			$this->assign('titele','编辑收货地址');
		}else{
			$this->assign('useraddr',0);
			$this->assign('titele','新增收货地址');
		}
		into();
	return $this->fetch();
	}


}

//TODO:A 所以订单
function allorder(){
	$uid=session('user.userid');
	if(request()->isAjax()){
		//分页
		if(input('?size') && input('?page')){
			$postArr=request()->only(['page','size'],'post');
			$validate = Loader::validate('Basic');
			if($validate->check($postArr)){
				$map['userid']=$uid;
				if(input('?type')){
					$type=input('type');
					if($type<=4 && $type>=1){
						$map['status']=$type;
					}
				}
				$list = new Jsorder();
				$list = $list->where($map)->order('status,insertdate desc')->limit($postArr['size'])->page($postArr['page'])->select();
				return json($list);
			}else{
				$result['error']=$validate->getError();
				return $result;
			}
			exit;
		}

		//取消订单
		if(!input('?oid')){
			$this->error('参数错误,取消失败,请重试!');
			exit;
		}
		$oid=input('oid');
		$map['orderno']=$oid;
		$map['status']=1;
		$js=new Jsorder();
		$order=$js->where($map)->find();
		if($order==null){
			$this->error('取消失败,请重试!');
			exit;
		}
		//返还优惠券
		$yhq=new Coupon_list();
		$fhyhq=$yhq->where('rid',$order['syyhq'])->find();
		if($yhq!=null){
			$fhyhq['status']=0;
			$yhq->save(['status'=>0],['rid'=>$order['syyhq']]);
		}

		foreach(explode(',',$order['flyhq']) as $key=>$value){
			$yhq=new Coupon_list();
			$fhyhq=$yhq->where('rid',$value)->find();
			if($yhq!=null){
				$fhyhq['status']=0;
				$yhq->save(['status'=>0],['rid'=>$value]);
			}
		}

		//返还库存

		$sale=new Saleorder();
		$sale=$sale->where('orderno',$oid)->select();
		$spec=new Productspec();
		foreach($sale as $key=>$value){
			$spec->where('pspecid',$value['pspecid'])->setInc('stocknum',$value['num']);
			$spec->where('pspecid',$value['pspecid'])->setDec('SaleNum',$value['num']);
		}
		$js->save(['status'=>9],['orderno'=>$oid]);
		$this->success('取消成功!','/orderdetail/oid/'.$oid.'.html');
		exit;
	}


	if(!input('?type')){
		$type=0;
	}else{
		$type=input('type');
		if($type>4 || $type<1){
			$type = 0;
		}
	}

	$this->assign('type',$type);
	into();
	return $this->fetch();

}

//TODO:B 绑定新手机号
function boundphone(){
	$user=new Userinfo();
	if(request()->isAjax()){
		//提交
		if(input('?verify')){
			if(session('verify_mobile') != input('mobile')){
				$this->error('获取验证码手机号与提交手机号不一致！');
				exit;
			}
			if(session('verify_code') != input('verify')){
				$this->error('验证码错误！');
				exit;
			}
			if(time()-session('verify_time') > 36000){//10分钟提交有效
				$this->error('验证码过期，请重新获取！');
				exit;
			}
			$map['userid']=session('user.userid');
			$data['mobile']=input('mobile');
			$data['loginname']=input('mobile');
			if($user->save($data,$map)){
				$this->success('绑定成功！','/saftystep.html');
				exit;
			}else{
				$this->error('绑定失败，请重试！');
				exit;
			}
			exit;
		}


		//获取验证码
		$msg=array();
		$msg['state']=0;
		if($user->where(['mobile'=>input('mobile')])->find() != null){
			$msg['msg']='手机号已被绑定！';
			return json($msg);
		}
		$verify = new Verify();
		$verify_list = $verify->where('userid',session('user.userid'))->order('createtime desc')->select();
		if(count($verify_list) != 0){
			if((time()-$verify_list[0]['timestamp'])<60)
			{
				$msg['state']=2;
				$msg['time']=($verify_list[0]['timestamp']+60)-time();
				$msg['msg']='等待'.$msg['time'].'秒后重新发送！';
				return json($msg);
			}
		}
		//单个用户每天次数
		if(count($verify_list) >= 3){
			$num=0;
			for($i=0;$i<3;$i++) {
				if(strtotime(date('Y-m-d'))>$verify_list[$i]['timestamp']){
					//今天的时间戳大于记录最大时间错——今天没有发送
					break;
				}else{
					//今天发送
					$num++;
					if($num>=3){
						$msg['state']=1;
						$msg['msg']='今天次数已用完！';
						return json($msg);
					}
				}
			}
		}
		//单个手机号每天次数
		$verify_list = $verify->where('mobile',input('mobile'))->order('createtime desc')->select();
		if(count($verify_list) >= 3){
			$num=0;
			for($i=0;$i<3;$i++) {
				if(strtotime(date('Y-m-d'))>$verify_list[$i]['timestamp']){
					//今天的时间戳大于记录最大时间错——今天没有发送
					break;
				}else{
					//今天发送
					$num++;
					if($num>=3){
						$msg['state']=1;
						$msg['msg']='今天次数已用完！';
						return json($msg);
					}
				}
			}
		}
		$yzm=rand(100000,999999);
		$map['stype']='SendMsg';
		$wx_config = new Wx_config();
		$url=$wx_config->where($map)->find()['svalue'];
		libxml_disable_entity_loader(false);//防止failed to load external entity 出错误
		$client = new \SoapClient($url);
		$systime = new \DateTime;
		$username='Fjjjgov';
		$datetime = new \DateTime;
		$datetime = $datetime->format('YmdHis');
		$password=md5($username . '3592B873A85A23A14DE7E39F48B94708F1622FE09F9D6E14');
		$Message='您的手机验证码为：'.$yzm;
		$headerbody = array('SystTime' => strtotime($datetime),
												'Username' => $username,
												'Password' => $password);
		$header = new \SoapHeader('dsmp.greatwall.net.cn', 'AuthHeader', $headerbody,true);
		$client->__setSoapHeaders($header);
		$param = array('Mobile' => input('mobile'),
									 'Message' => $Message,
									 'ExNumber' => '');
		$p = $client->__soapCall('SendMsgPhp',array('parameters'=>$param));
		if($p->SendMsgPhpResult)//$p->SendMsgPhpResult
		{
			$data=array();
			$data['userid']=session('user.userid');
			$data['msg']=$Message;
			$data['mobile']=input('mobile');
			$data['timestamp']=time();
			$verify->save($data);
			session('verify_mobile',input('mobile'));
			session('verify_code',$yzm);
			session('verify_time',time());
			$msg['state']=3;
			$msg['msg']='发送成功！';
			return json($msg);
		}else{
			$msg['msg']='发送失败，请重试！';
			return json($msg);
		}
	}
	if(session('user.mobile')!=null){
		$this->error('已绑定手机号!');
		exit;
	}




	into();
	return $this->fetch();
}

//TODO:C 分类
function category(){
	if(input('?tab')){
		$tab=input('tab');
	}else{
		$tab=0;
	}
	$this->assign('tab',$tab);

	$category = new Category();
	$list = $category->where(['parentcatid'=>0,'cattype'=>1,'ifshow'=>1])->order('catsort,catid')->select();//获取1级
	foreach($list as $key=>$value){//获取2级
		$l2=$category->where(['parentcatid'=>$value['catid'],'ifshow'=>1])->order('catsort,catid')->select();
		foreach($l2 as $key1=>$value1){//获取3级
			$l3=$category->where(['parentcatid'=>$value1['catid'],'ifshow'=>1])->order('catsort,catid')->select();
			$value1['l3']=$l3;
			$l2[$key1]=$value1;//将3级添加进2级
		}
		$value['l2']=$l2;
		$list[$key]=$value;//将2级添加进1级
	}

	$this->assign('list',$list);

	into();
	return $this->fetch();
}

//TODO:C 我的收藏
function collect(){
	if(request()->isAjax()){
		$sc=new Userfous();
		$rid=input('rid/a');
		foreach($rid as $key=>$value){
			$map['rid']=$value;
			$sc->where($map)->delete();
		}
		$this->success('保存成功!');
		exit;
	}



	$uid=session('user.userid');
	$sc=new Fullfous();
	$map['userid']=$uid;
	$sc=$sc->where($map)->select();
	$this->assign('list',$sc);
	into();
	return $this->fetch();
}

//TODO:C 我的收藏编辑 //没用到!!!
function collectedit(){
	$uid=session('user.userid');
	$map['userid']=$uid;

	if(request()->isAjax()){
		$idlist=input('rid/a');
		$fous = new Userfous();
		$fous->destroy($idlist);
		$rid=input('rid/a');
		foreach($rid as $key=>$value){
			$map['rid']=$value;
			$car->where($map)->delete();
		}
		$this->success('保存成功!','/collect.html');
		exit;
	}



	$sc=new Fullfous();
	$sc=$sc->where($map)->select();
	$this->assign('list',$sc);
	into();
	return $this->fetch();
}

//TODO:C 佣金明细
function commission(){
	into();
	return $this->fetch();
}

//TODO:C 联系方式
function contact(){
	into();
	return $this->fetch();
}

//TODO:D 商品详情
function detail(){
	$uid=session('user.userid');
	if(request()->isAjax()){//aj提交
		$validate = Loader::validate('Detail');
		$postArr=request()->post();
		if($validate->check($postArr)){
			$type=$postArr['type'];
			if($type=='sc'){//收藏商品
				$p=new Product();
				if($p->where(['productid'=>$postArr['pid']])->find()==null){
					$result['msg']='商品不存在!';
				}else{
					$fous=new Userfous();
					$data['userid']=$uid;
					$data['pid']=$postArr['pid'];
					if($fous->where($data)->find()==null){
						$fous->save($data);
					}
					$result['msg']='收藏成功!';
				}
			}elseif($type=='qx'){//取消收藏
				$fous=new Userfous();
				$data['userid']=$uid;
				$data['pid']=$postArr['pid'];
				$fous->where($data)->delete();
				$result['msg']='取消成功!';
			}elseif($type=='jr'){//加入购物车
				$car=new Shopcar();
				$map['userid']=$uid;
				$map['pspecid']=$postArr['psid'];
				$scar=$car->where($map)->find();
				if($scar==null){
					$map['num']=$postArr['num'];
					$car->save($map);
				}else{
					$map['num']=$scar['num']+$postArr['num'];
					$car->save($map,['id'=>$scar['id']]);
				}
				$result['msg']='加入成功!';
			}elseif($type=='pj'){
				$pid=input('pid');
				$page=input('page');
				$size=input('size');
				$pe=new Fullpe();
				$result=$pe->where('productid',$pid)->order('evaltime desc')->limit($size)->page($page)->select();
			}else{
				$result['msg']='未知操作类型!';
			}
		}else{
				$result['msg']=$validate->getError();
		}
		return $result;
		exit;
	}
	//post提交订单信息
	if(request()->isPost()){
		$postArr=request()->post();
		session('order',$postArr);
		$this->redirect('order');
		exit;
	}
	//直接访问
	if(!input('?pid')){
		$this->error('参数错误!','/category.html');
		exit;
	}
	$pid=input('pid');
	$sort=input('sort');
	if($sort!=1 && $sort!=2){
		$sort='0';
	}
	$product=new Plistnew();
	$product=$product->where(['productid'=>$pid])->find();
	if($product==null){
		$this->error('未找到该商品!','/category.html');
		exit;
	}

	$fous=new Userfous();
	if($fous->where(['userid'=>$uid,'pid'=>$pid])->find()==null){
		$sc=0;
	}else{
		$sc=1;
	}

	$spec=new Fullpdt();
	$spec=$spec->where(['productid'=>$pid])->order('price,insertdate desc')->select();
	//判断促销
	$cx=new Fullcx();
	$cxlist=array();
	$i=0;
	$spec=new Fullpdt();
	$spec=$spec->where('productid',$pid)->select();
	//限时特价
	unset($map);
	$map['cxtype']=0;
	foreach($spec as $key=>$value){
		$map['pspecid']=$value['pspecid'];
		$cxfa=$cx->where($map)->where('`synum` > 0')->find();
		if($cxfa != null){
			$yhxx=$cxfa['cxtitle'].':购买'.$value['spgg'].'享受 '.$cxfa['cxprice'].'元特价!';
			$cxlist[$i]['jj']=$value['spgg'].':'.$cxfa['cxtitle'];
			$cxlist[$i]['xq']=$yhxx.'</br>('.substr($cxfa['yxbegindate'],0,10).'--'.substr($cxfa['yxenddate'],0,10).')';
			++$i;
			$value['price']=$cxfa['cxprice'];
			$spec[$key]=$value;
		}
	}
	unset($map);
	$map['cxlb']=2;
	$map['cxtype']=array('neq',0);
	foreach($spec as $value){
		$map['pspecid']=$value['pspecid'];
		$cxfa=$cx->where($map)->where('`synum` > 0')->select();
		foreach($cxfa as $cxfa){
			$cxlx=$cxfa['cxtype'];
			switch($cxlx){
				case 1:
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 立减 '.$cxfa['ljamount'].'!';
					break;
				case 2:
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 送 '.$cxfa['yname'].'!';
					break;
				case 3:
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 送 '.$cxfa['jfnum'].'积分!';
					break;
				case 4:
					$zp=new Fullzp();
					$zp=$zp->where('zid',$cxfa['cxid'])->select();
					$zpxx='';
					foreach($zp as $value){
						$zpxx .= $value['spmc'].' x '.$value['num'].'；';
					}
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 送 '.$zpxx;
					break;
				case 11:
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].' 立减 '.$cxfa['ljamount'].'!';
					break;
				case 12:
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].' 送 '.$cxfa['yname'].'!';
					break;
				case 13:
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].' 送 '.$cxfa['jfnum'].'积分!';
					break;
				case 14:
					$zp=new Fullzp();
					$zp=$zp->where('zid',$cxfa['cxid'])->select();
					$zpxx='';
					foreach($zp as $value){
						$zpxx .= $value['spmc'].' x '.$value['num'].'；';
					}
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].' 送 '.$zpxx;
					break;
				case 15:
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].' 送 '.$cxfa['zsnum'].'!';
					break;
				default:
					$yhxx=$cxfa['cxtitle'].':暂无优惠!';
					break;
			}
			$cxlist[$i]['jj']=$value['spgg'].':'.$cxfa['cxtitle'];
			$cxlist[$i]['xq']=$yhxx.'</br>('.substr($cxfa['yxbegindate'],0,10).'--'.substr($cxfa['yxenddate'],0,10).')';
			++$i;
		}
	}
	unset($map);
	$map['cxlb']=3;
	$map['productid']=$pid;
	$cxfa=$cx->where($map)->select();
	foreach($cxfa as $cxfa){
		$cxlx=$cxfa['cxtype'];
		switch($cxlx){
			case 1:
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 立减 '.$cxfa['ljamount'].'!';
				break;
			case 2:
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 送 '.$cxfa['yname'].'!';
				break;
			case 3:
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 送 '.$cxfa['jfnum'].'积分!';
				break;
			case 4:
				$zp=new Fullzp();
				$zp=$zp->where('zid',$cxfa['cxid'])->select();
				$zpxx='';
				foreach($zp as $value){
					$zpxx .= $value['spmc'].' x '.$value['num'].'；';
				}
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 送 '.$zpxx;
				break;
			case 11:
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].'件 立减 '.$cxfa['ljamount'].'!';
				break;
			case 12:
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].'件 送 '.$cxfa['yname'].'!';
				break;
			case 13:
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].'件 送 '.$cxfa['jfnum'].'积分!';
				break;
			case 14:
				$zp=new Fullzp();
				$zp=$zp->where('zid',$cxfa['cxid'])->select();
				$zpxx='';
				foreach($zp as $value){
					$zpxx .= $value['spmc'].' x '.$value['num'].'；';
				}
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].'件 送 '.$zpxx;
				break;
			case 15:
				$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmnum'].'件 送 '.$cxfa['zsnum'].'!';
				break;
			default:
				$yhxx='暂无优惠!';
				break;
		}
		$cxlist[$i]['jj']=$cxfa['cxtitle'];
		$cxlist[$i]['xq']=$yhxx.'</br>('.substr($cxfa['yxbegindate'],0,10).'--'.substr($cxfa['yxenddate'],0,10).')';
		++$i;
	}
	//分类促销
	if(count($cxlist)==0){
		$catid=$product['catid'];
		$category = new Category();
		$cat=$category->where('catid',$catid)->find();
		$cxfa=array();
		if($cat!=null){//判断是否为普通分类
			$cxzq=new Fullcx();
			$cxfa=$cxzq->where('status',1)->where('category3',$catid)->select();
			if(count($cxfa)==0){
				$cat=$category->where('catid',$cat['parentcatid'])->find();
				$cafa=$cxzq->where('category2',$cat['catid'])->select();
				if(count($cxfa)!=0){
					$cat=$category->where('catid',$cat['parentcatid'])->find();
					$cafa=$cxzq->where('category1',$cat['catid'])->select();
				}
			}
		}
		foreach($cxfa as $cxfa){
			$cxlx=$cxfa['cxtype'];
			switch($cxlx){
				case 4:
					$zp=new Fullzp();
					$zp=$zp->where('zid',$cxfa['cxid'])->select();
					$zpxx='';
					foreach($zp as $value){
						$zpxx .= $value['spmc'].' x '.$value['num'].'；';
					}
					$yhxx=$cxfa['cxtitle'].':满'.$cxfa['mmamount'].'元 送 '.$zpxx;
					break;
				default:
					$yhxx=$cxfa['cxtitle'];
					break;
			}
			$cxlist[$i]['jj']=$cxfa['cxtitle'];
			$cxlist[$i]['xq']=$yhxx.'</br>('.substr($cxfa['yxbegindate'],0,10).'--'.substr($cxfa['yxenddate'],0,10).')';
			++$i;
		}

	}

	//var_dump($cxlist);
	//exit;
	$img = new Menupic();
	$img=$img->where(['itemid'=>$pid,'itemtype'=>'product'])->order('menusort, insertdate desc')->select();
	$this->assign('cxlist',$cxlist);
	$this->assign('img',$img);
	$this->assign('spec',$spec);
	$this->assign('sc',$sc);
	$this->assign('product',$product);
	$this->assign('sort',$sort);
	//猜你喜欢(部分与分类促销处代码重复,可待优化)
	$catid=$product['catid'];
	$category = new Category();
	$cat=$category->where('catid',$catid)->find();
	if(strlen($cat['loveproductid']) == 0){
		$cat=$category->where('catid',$cat['parentcatid'])->find();
		if(strlen($cat['loveproductid']) == 0){
			$cat=$category->where('catid',$cat['parentcatid'])->find();
		}
	}
	if(strlen($cat['loveproductid']) == 0){
		$islike=0;
	}else{
		$islike=1;
		$likenum=session('sys.loveproductnum');
		$idstr=$cat['loveproductid'];
		$idstr=array_reverse(explode(';',substr($idstr,0,strlen($idstr)-1)));
		$pdt= new Fullpdt();
		$like=array();
		$i=0;
		foreach($idstr as $value){
			$like[$i]=$pdt->where('pspecid',$value)->find();
			++$i;
			if($i>=$likenum) break;
		}
		$this->assign('like',$like);
	}
	$this->assign('islike',$islike);

	//组合列表
	$this->assign('iszh',$product['ptype']);
	if($product['ptype'] == 1){
		$zh=new Fullzp();
		$zh=$zh->where('zid',$product['productid'])->select();
		$this->assign('zh',$zh);
	}


	$shopcar=new Shopcar();
	$shopcar=$shopcar->where('userid',$uid)->select();
	$shopcarnum=0;
	foreach($shopcar as $key=>$value){
		$shopcarnum+=$value['num'];
	}

	$this->assign('shopcar',$shopcarnum);
	//var_dump(session('url'));
	into();
	//var_dump(session('url'));
	return $this->fetch();
}


//TODO:E 提交兑换
function exchange(){
	if(session('user.mobile')===null){
		$this->error('请先完善手机信息!','/reg.html');
		exit;
	}
	if(session('user.babysex')===null || session('user.birthday')===null){
		$this->error('请先完善宝宝信息!','/reg.html');
		exit;
	}
	//post提交订单信息
	if(request()->isPost()){
		$postArr=request()->post();
		session('exchange',$postArr);
		$this->redirect('/tureexchange/eid/'.session('eid'));
		exit;
	}


	if(!input('?spid')){
		$this->error('获取商品信息失败!','/integralexchange2.html');
		exit;
	}
	$uid=session('user.userid');
	$spid=input('spid');
	$p = new Fullpdt();
	$map['ptype']=2;
	$map['pspecid']=$spid;
	$p = $p->where($map)->find();
	if($p==null){
		$this->error('没有找到该商品!','/integralexchange2.html');
		exit;
	}
	$this->assign('p',$p);

	//收货地址信息
	$useraddr=new Useraddr();
	$addrlist=$useraddr->where(['userid'=>$uid])->order('ifmr desc,insertdate')->select();
	$mrdz=$useraddr->where(['userid'=>$uid,'ifmr'=>1])->order('ifmr desc,insertdate')->find();
	$this->assign('addrlist',$addrlist);
	$this->assign('mrdz',$mrdz);
	//门店信息
	$shop=new Shop();
	$shop=$shop->where(['status'=>1])->select();
	$this->assign('shop',$shop);


	//生成订单号,缓存订单号 用于防止重复提交订单
	$eid=$uid.date('YmdHis').rand(1000,9999);
	session('eid',$eid);
	into();
	return $this->fetch();
}


//TODO:F 忘记密码
function forgetpassword(){
	into();
	return $this->fetch();
}

//TODO:G 管理收货地址
function gladdress(){
	$map['userid']=Session::get('user')['userid'];
	$useraddr = new Useraddr();

	if(input('?mr')){//设置默认
		$result=$useraddr->setdefault(input('mr'));
		if($result==null){
			$this->success('操作成功', '/gladdress.html');
		}else{
			$this->error(request(), '/gladdress.html');
		}
	}elseif(input('?del')){//删除
		$result=$useraddr->del(input('del'));
		if($result==null){
			$this->success('操作成功', '/gladdress.html');
		}else{
			$this->error(request(), '/gladdress.html');
		}
	}else{//列表
		$useraddr=$useraddr->where($map)->order('ifmr desc')->select();
		$this->assign('useraddr',$useraddr);
	into();
	return $this->fetch();
	}
}

//TODO:G 商品列表
function goodslist(){
	//var_dump(session('url'));
	$category = new Category();
	$catid = input('catid');
	if(input('?s')){//搜索
		$s='/s/1';
		//if(mb_detect_encoding($catid) != 'UTF-8'){
			$ygjc=$catid;//未处理关键词
			$catid = mb_convert_encoding($catid, 'UTF-8', 'EUC-CN');
		//}
		$cate['catid']=$catid;
		$cate['catname']='"'.$catid.'"搜索结果';
		$map['spmc']=['like','%'.input('catid').'%'];
	}
	elseif(input('?i')){//促销
		$s='/i/'.input('i');
		$catid = mb_convert_encoding($catid, 'UTF-8', 'EUC-CN');
		$cate['catid']=$catid;
		$cate['catname']=$catid;
		$map['indexcxzq']=input('i');
	}
	elseif(input('?k')){//单品 捆绑
		$s='/k/'.input('k');
		$catid = mb_convert_encoding($catid, 'UTF-8', 'EUC-CN');
		$cate['catid']=$catid;
		$cate['catname']=$catid;
		$map['indexcxzq']=input('k');
	}
	else{
		$s='';
		$cate = $category->where(['catid'=>$catid])->find();
		if($cate == null){
			$cate = $category->where(['level'=>3])->find();
			$catid=$cate['catid'];
		}
		$map['catid']=$catid;
	}
	$sort = input('sort');
	if($sort!=1 && $sort!=2){
		$sort='0';
	}
	if(request()->isAjax()){//aj访问
		if(input('?size') && input('?page')){
			$validate = Loader::validate('Basic');
			$postArr=request()->only(['page','size'],'post');
			if(!$validate->check($postArr)){//验证数据
				$result['error']=$validate->getError();
				return $result;
				exit;
			}
		}else{
			$postArr['size']=10;
			$postArr['page']=1;
		}
		//验证通过

		//促销列表处理
		if(input('?i')){
			$map['status']=1;
			$map['synum']=['>',0];
			$map['yxbegindate']=['<',date("Y-m-d H:i:s")];
			$map['yxenddate']=['>',date("Y-m-d H:i:s")];
			$list = new Cxdp();
			switch ($sort) {
				case 0://新品
					$result=$list->where($map)->order('insertdate desc')->limit($postArr['size'])->page($postArr['page'])->select();
					break;
				case 1://价格升序
					$result=$list->where($map)->order('price')->limit($postArr['size'])->page($postArr['page'])->select();
					break;
				case 2://价格降序
					$result=$list->where($map)->order('price desc')->limit($postArr['size'])->page($postArr['page'])->select();
					break;
				default:
					$result['error']='参数错误!';
					break;
			}
			return $result;
			exit;
		}


		//单品 捆绑列表处理
		if(input('?k')){
			$datetime=date("Y-m-d H:i:s");
			$k=input('k');
			$list = new Plistnew();
			$sql="`IndexCxzq` = $k AND (`ptype` = 0 OR `ptype` = 1) AND ((`yxbegindate` IS NULL AND `yxenddate` IS NULL) OR (`synum` > 0 AND `yxbegindate` < '$datetime' AND `yxenddate` > '$datetime') OR (`yxbegindate` = '' AND `yxenddate` = ''))";
			switch ($sort) {
				case 0://新品
					$result=$list->where($sql)->order('insertdate desc')->limit($postArr['size'])->page($postArr['page'])->select();
					break;
				case 1://价格升序
					$result=$list->where($sql)->order('price')->limit($postArr['size'])->page($postArr['page'])->select();
					break;
				case 2://价格降序
					$result=$list->where($sql)->order('price desc')->limit($postArr['size'])->page($postArr['page'])->select();
					break;
				default:
					$result['error']='参数错误!';
					break;
			}
			return $result;
			exit;
		}


		//普通 搜索处理
		$map['ptype']=0;
		$list=new Plistnew();
		switch ($sort) {
			case 0://新品
				$result=$list->where($map)->limit($postArr['size'])->page($postArr['page'])->select();
				break;
			case 1://价格升序
				$result=$list->where($map)->order('price')->limit($postArr['size'])->page($postArr['page'])->select();
				break;
			case 2://价格降序
				$result=$list->where($map)->order('price desc')->limit($postArr['size'])->page($postArr['page'])->select();
				break;
			default:
				$result['error']='参数错误!';
				break;
		}
		//有效搜索关键字收录处理 && count($result)!=0
		if(input('?s') ){
			$uid=session('user.userid');
			$sw = new Searchword();
			unset($map);
			$map['userid']='0';
			$map['keyword']=$ygjc;
			$uk=$sw->where($map)->select();
			if(count($uk) !=0 ){
				if(time()-strtotime($uk[0]['insertdate']) >0){
					$sw->where($map)->setInc('num', 1);
				}
			}

			$sw = new Searchword();
			$map['userid']=$uid;
			$num=$sw->where('userid',$uid)->select();
			$uk=$sw->where($map)->order('insertdate desc')->find();
			if($uk ==null ){
				if(count($num) >=10){
					//删除最后一个
					$sw->destroy('id',$num[9]['id']);
				}
				//新增
				$sw = new Searchword();
				$data['userid']=$uid;
				$data['keyword']=$ygjc;
				$sw->save($data);
			}else{
				$data['insertdate']=date("Y-m-d H:i:s");
				$sw->save($data,['id'=> $uk['id']]);
			}
		}

		return $result;
		exit;

	}

	//直接访问
	//判断分类促销
	$cx=0;
	$cat=$category->where('catid',$catid)->find();
	if($cat!=null){//判断是否为普通分类
		$cxzq=new Fullcx();
		$cxfa=$cxzq->where('status',1)->where('category3',$catid)->select();
		if(count($cxfa)!=0){
			$cx=1;
		}else{
			$cat=$category->where('catid',$cat['parentcatid'])->find();
			$cafa=$cxzq->where('category2',$cat['catid'])->select();
			if(count($cxfa)!=0){
				$cx=1;
			}else{
				$cat=$category->where('catid',$cat['parentcatid'])->find();
				$cafa=$cxzq->where('category1',$cat['catid'])->select();
				if(count($cxfa)!=0){
					$cx=1;
				}
			}
		}
	}
	if($cx){
		$cxlist=array();
		foreach($cxfa as $key=>$value){
			$cxlx=$value['cxtype'];
			switch($cxlx){
				case 4:
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value['cxid'])->select();
					$zpxx='';
					foreach($zp as $value1){
						$zpxx .= $value1['spmc'].' x '.$value1['num'].'；';
					}
					$cxlist[$key]['yhxx']=$value['cxtitle'].':满'.$value['mmamount'].'元 送 '.$zpxx;
					break;
				default:
					$cxlist[$key]['yhxx']=$value['cxtitle'];
					break;
			}


		}
		$this->assign('cxlist',$cxlist);
	}
	$this->assign('cx',$cx);

	$this->assign('s',$s);
	$this->assign('sort',$sort);
	$this->assign('cate',$cate);
	//var_dump(session('url'));
	into();
	//var_dump(session('url'));
	return $this->fetch();


}

//TODO:I 首页
function index()
{
	if(request()->isAjax()){
		if(!$this->is_subscribe()){//是否关注  如果关注  更新用户信息
			$this->error('未关注!');
		}else{
			$this->success('已关注!');
		}
	}
	$uid=session('user.userid');
	//未读短消息数
	$dxx=new Zndxx();
	$dxx=$dxx->where(['userid'=>$uid,'isRead'=>0])->count();
	$this->assign('dxx',$dxx);
	//首页轮播
	$map['menutype']='10';
	$pic= new Menupic();
	$hdp=$pic->where($map)->order('menusort, insertdate desc')->select();
	$this->assign('hdp',$hdp);
	//首页宽屏
	$map['menutype']='11';
	$kpt=$pic->where($map)->order('menusort, insertdate desc')->find();
	$this->assign('kpt',$kpt);
	//首页图标
	$map['menutype']='12';
	$flt=$pic->where($map)->order('menusort, insertdate desc')->select();
	$this->assign('flt',$flt);
	//分区名称
	for($i=1;$i<7;++$i){
		if(session('sys.indexzqtitle'.$i)==null){
			continue;//
		}
		//以下获取专区数据
		$cxzq[$i-1]['id']=$i;
		$cxzq[$i-1]['name']=session('sys.indexzqtitle'.$i);
		unset($map);
		$map['menutype']='2'.$i;
		$zqt=$pic->where($map)->order('menusort, insertdate desc')->find();//专区图
		if($zqt!=null) {
			$cxzq[$i-1]['img']=$zqt['menuimg'];
			$cxzq[$i-1]['url']=$zqt['linkurl'];
		}else{
			$cxzq[$i-1]['img']='';
			$cxzq[$i-1]['url']='';
		}

		$cxdp=new Cxdp();
		$cxdp=$cxdp->where(['indexcxzq'=>$i,'status'=>1])
					->where('synum','>',0)
					->where('yxbegindate','<',date("Y-m-d H:i:s"))
					->where('yxenddate','>',date("Y-m-d H:i:s"))
					->order('sorts,synum desc')->limit(10)->select()->toArray();
		$cxdplist=array();
		if(count($cxdp) !=0 ){
			$cxzq[$i-1]['s']='i';
			foreach($cxdp as $key=>$value){
				if($value['cxlb']==2 && $value['cxtype']==0){
					$value['price']=$value['cxprice'];
				}
				$cxdplist[$key]=$value;
			}
		}else{//获取单品或捆绑
			$cxzq[$i-1]['s']='k';
			$cxdp=new Product();
			$datetime=date("Y-m-d H:i:s");
			$cxdp=$cxdp->where("`IndexCxzq` = $i AND `status` = 1 AND (`ptype` = 0 OR `ptype` = 1 ) AND ((`yxbegindate` IS NULL AND `yxenddate` IS NULL) OR (`yxbegindate` < '$datetime' AND `yxenddate` > '$datetime' AND `synum`>0) OR (`yxbegindate` = '' AND `yxenddate` = ''))")->order('sorts')->limit(10)->select()->toArray();
			foreach($cxdp as $key=>$value){
				unset($map);
				$value['cxtitle']=$value['spmc'];
				$map['itemtype']='product';
				$map['itemid']=$value['productid'];
				$img=new Menupic();
				$img=$img->where($map)->order('menusort, insertdate desc')->find();
				$value['menuimg']=$img['menuimg'];
				if($value['ptype']==0){
					$spec=new Fullpdt();
					$spec=$spec->where('productid',$value['productid'])->find();
					$value['price']=$spec['price'];
					$value['origprice']=$spec['origprice'];

				}
				$cxdplist[$key]=$value;
			}
		}
		$cxzq[$i-1]['cxdp']=$cxdplist;
	}
	$this->assign('cxzq',$cxzq);
  into();
	return $this->fetch();
}

//TODO:I 个人资料
function infor(){
	if(request()->isAjax()){
		$data['babyname']=input('babyname');
		$data['babysex']=input('babysex');
		$data['birthday']=input('birthday');
		$user=new Userinfo();
		$user->save($data,['userid'=>session('user.userid')]);
		$this->success('提交成功!');
	}
	into();
	return $this->fetch();
}

//TODO:I 积分 签到
function integral(){
	$map['userid']=session('user.userid');
	$jf=new Integralupdate();

	if(request()->isAjax()){
		if(input('?size') && input('?page')){
			$postArr=request()->only(['page','size'],'post');
			$validate = Loader::validate('Basic');
			if($validate->check($postArr)){
				$list=$jf->where($map)->order('insertdate desc')->limit($postArr['size'])->page($postArr['page'])->select();
				return json($list);
			}else{
				$result['error']=$validate->getError();
				return $result;
			}

		}else{
			$result=$jf->sign();
			if(!array_key_exists('error',$result)){
				$user=Userinfo::get($map);
				$user->integral += $result['orderintegral'];
				if(!$user->save()){
					$result['error']='累加用户积分失败！';
				}
			}
			return json($result);
		}
	}else{
		$map['ordertype']=2;
		$time=$jf->where($map)->order('insertdate desc')->find();
		if($time==null){
			$time='2017-08-08';
		}else{
			$time=$time['insertdate'];
			$time=date('Y-m-d',strtotime($time));//获取到天单位
		}

		$time=strtotime(date('Y-m-d'))-strtotime($time);
		if($time==0){
			$qd=0;//已签到
		}elseif($time==86400){//连续签到
			$qd=2;
		}else{//多天未签到
			$qd=1;
		}
		$this->assign('qd',$qd);
		$this->assign('integral',session('user.integral'));
	into();
	return $this->fetch();
	}

}

//TODO:I 积分兑换
function integralexchange(){
	$c = new Coupon();
	$map['status']=1;
	$map['ytype']=0;
	$c=$c->where($map)->order('yinsertdate desc')->select();
	$this->assign('list',$c);
	into();
	return $this->fetch();
}

//TODO:I 积分兑换2
function integralexchange2(){
	$p = new Fullpdt();
	$map['ptype']=2;
	$p=$p->where($map)->order('price')->select();
	$this->assign('list',$p);
	into();
	return $this->fetch();
}

//TODO:I 兑换记录
function integralrecords(){
	if(request()->isAjax()){
		$dhjl=new Fulldhjl();
		$map['userid']=session('user.userid');
		if(input('?size') && input('?page')){
			$postArr=request()->only(['page','size'],'post');
			$validate = Loader::validate('Basic');
			if($validate->check($postArr)){
				$dhjl=$dhjl->where($map)->limit($postArr['size'])->page($postArr['page'])->select();
				return json($dhjl);
				exit;
			}else{
				$result['error']=$validate->getError();
				return $result;
				exit;
			}
		}
	}

	into();
	return $this->fetch();
}


//TODO:I 邀请记录
function invitelist(){
	$uid=session('user.userid');
	$user=new Userinfo();
	$user=$user->where('sponsor',$uid)->order('createtime desc')->select();
	$this->assign('user',$user);
	into();
	return $this->fetch();
}
//TODO:L 登录
function login(){
	into();
	return $this->fetch();
}

//TODO:M 个人中心
function member(){
	$this->assign('user',session('user'));

	$map['userid']=session('user.userid');

	$sc=new Fullfous();
	$sc=$sc->where($map)->count();
	$this->assign('sc',$sc);

	$map['status']=0;
	$yhq=new Coupon_list();
	$yhq=$yhq->where($map)->count();
	$this->assign('yhq',$yhq);

	$js=new Jsorder();
	$map['status']=1;
	$dfk=$js->where($map)->count();
	$this->assign('dfk',$dfk);

	$map['status']=2;
	$dfh=$js->where($map)->count();
	$this->assign('dfh',$dfh);

	$map['status']=3;
	$dsh=$js->where($map)->count();
	$this->assign('dsh',$dsh);

	$map['status']=4;
	$dpj=$js->where($map)->count();
	$this->assign('dpj',$dpj);

	into();
	return $this->fetch();
}

//TODO:M 提现记录
function mentionList(){
	into();
	return $this->fetch();
}
//TODO:M messdetail动态详情
function messdetail(){
	into();
	return $this->fetch();
}

//TODO:M messdetailactive动态详情已完成
function messdetailactive(){
	into();
	return $this->fetch();
}

//TODO:M money我的钱包
function money(){

	$map['userid']=session('user.userid');
	$map['status']=0;
	$yhq=new Coupon_list();
	$yhq=$yhq->where($map)->count();
	$this->assign('yhq',$yhq);
	$this->assign('user',session('user'));
	into();
	return $this->fetch();
}

//TODO:M myassess评论
function myassess(){
	if(!input('?oid')){
		$this->error('参数错误');
		exit;
	}
	$map['orderno']=input('oid');
	$order = new Jsorder();
	$order = $order->where($map)->find();
	if($order == null){
		$this->error('订单不存在');
		exit;
	}
	if($order['status'] != 4){
		$this->error('订单当前状态无法评价!');
		exit;
	}
	if(request()->isPost()){
		$pid=input('pid/a');
		$psid=input('psid/a');
		$remark=input('remark/a');
		$star=input('star/a');
		$order = new Productevaluate();
		foreach($pid as $key=>$value){
			if($remark[$key]==null) $remark[$key]='好评!';
			$list[]=['productid'=>$pid[$key],
							 'pspecid'=>$psid[$key],
							 'evalremark'=>$remark[$key],
							 'evalgrade'=>$star[$key],
							 'userid'=>session('user.userid')];
		}
		$order->saveAll($list);
		$order = new Jsorder();
		$order->save(['status'=>5],['orderno'=>input('oid')]);
		$this->success('评论成功!',outo());
		exit;
	}

	$map['userid']=session('user.userid');
	$order = new Fullsale();
	$order = $order->where($map)->select();
	$this->assign('order',$order);
	$this->assign('orderno',input('oid'));
	into();
	return $this->fetch();
}

//TODO:M 我的动态
function mymessage(){
	into();
	return $this->fetch();
}

//TODO:M 我的推荐
function myrecommend(){
	into();
	return $this->fetch();
}

//TODO:N 消息详情
function newsdetail(){
	if(!input('?rid')){
		$this->error('参数错误!','/tidings.html');
		exit;
	}
	$uid=session('user.userid');
	$rid=input('rid');
	$zndxx=new Zndxx();
	$dxx=$zndxx->where(['userid'=>$uid,'rid'=>$rid])->find();
	if($dxx==null){
		$this->error('消息不存在!','/tidings.html');
		exit;
	}
	$this->assign('dxx',$dxx);

	$zndxx->where(['userid'=>$uid,'rid'=>$rid])->setField('isRead',1);




	into();
	return $this->fetch();
}

//TODO:N 微信支付回调notify
function notify(){
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
	$oid=$ret["out_trade_no"];
	$map['orderno']=$oid;
	$map['orderstate']=0;
	if($openid == null || strlen($openid)<10){
		exit;
	}

	if($res['return_code'] !=="SUCCESS" || $res['return_msg'] !=="OK"){
		$cz=new Cz();
  	$data["msg"]="支付失败！";
		$data["dotime"]=date("Y-m-d H:i:s");
		$cz->save($data,$map);
		exit;
	}

	$user=new Userinfo();
	if($user->where(['wxopenid'=>$openid])->count() == 0){
		exit;
	}
	$userinfo=$user->where(['wxopenid'=>$openid])->find();
	$cz=new Cz();
	$js=new Jsorder();
	//现金支付数
	$xj=$ret["total_fee"]/100;
	if($cz->where($map)->count()  != 0){//充值
		$data['balance']=$userinfo['balance']+$xj;
		$userinfo['balance']=$data['balance'];
		if(!$user->save($data,['wxopenid'=>$openid])){
			unset($data);
	  	$data["msg"]="充值成功,但增加用户金额失败！";
			$data["dotime"]=date("Y-m-d H:i:s", time());
			$data["noticeordermoney"]=$xj;
			$cz->save($data,$map);
			exit;
		}
		unset($data);
		$data["msg"]="充值成功！";
		$data["orderstate"]=1;
		$data["dotime"]=date("Y-m-d H:i:s");
		$data["noticeordermoney"]=$xj;//现金
		$cz->save($data,$map);
		//余额变动记录
		unset($data);
		$data["userid"]=$userinfo["userid"];
		$data["orderno"]=$ret["out_trade_no"];
		$data["orderamount"]=$xj;
		$data["userbalance"]=$userinfo["balance"];//充值后余额
		$bala=new Balanceupdate();
		$bala->save($data);
	}else{//支付
		unset($map);
		$map['orderno']=$oid;
		$map['status']=1;
		if($js->where($map)->count()  != 0){//支付
			$jsorder=$js->where($map)->find();
			if($jsorder['jsamount'] == $xj){
				unset($data);
				$data['payway']=0;
				$data['status']=2;
				$js->save($data,$map);
				//赠送积分
				$jf=new Integralupdate();
				$sys=Sysset::get(1);
				$xcjf=$jsorder['jsamount']*$sys->integralBL;
				if($xcjf>0){
					unset($data);
					$data['userid']=$jsorder['userid'];
					$data['orderno']=$oid;
					$data['orderintegral']=$xcjf;
					$data['ordertype']=1;
					$jf->save($data);
				}
				$cxjf=$jsorder['zsjf'];
				if($cxjf>0){
					unset($data);
					$data['userid']=$jsorder['userid'];
					$data['orderno']=$oid;
					$data['orderintegral']=$cxjf;
					$data['ordertype']=4;
					$jf->save($data);
				}
				//增加用户积分
				unset($data);
				$data['integral']=$userinfo['integral']+$xcjf+$cxjf;
				$user->save($data,['wxopenid'=>$openid]);
				//赠送优惠券
				if($jsorder['zsyhq']!=null){
					$yhqlist=explode(',', $jsorder['zsyhq']);
					foreach($yhqlist as $value){
						$cl=new Coupon_list();
						unset($data);
						$data['yid']=$value;
						$data['lqlx']=2;
						$data['userid']=$jsorder['userid'];
						$cl->save($data);
					}
				}

			}
		}
	}







}

//TODO:O 提交订单
function order(){
	if(session('user.mobile')===null){
		$this->error('请先完善手机信息!','/reg.html');
	}

	if(session('user.babysex')===null || session('user.birthday')===null){
		$this->error('请先完善宝宝信息!','/reg.html');
	}
	//post提交订单信息
	if(request()->isPost()){
		$postArr=request()->post();
		session('order',$postArr);
		$this->redirect('/tureorder/oid/'.session('oid'));
		exit;
	}
	$uid=session('user.userid');

	if(!session('?order')){
		$this->error('获取商品数据失败','/shopcart.html');
	}

	//计算价格

	$postArr=session('order');
	$splist = new Fullpdt();
	$splist = $splist->where('pspecid','in',$postArr['spid'])->select()->toArray();
	$postArr = array_combine($postArr['spid'],$postArr['num']);
	$price=0;
	$yhje=0;
	$list=array();
	$cx=new Fullcx();
	$newkey=0;

	foreach($splist as $key=>$value){//数量用从客户端传过来的
		$value['num']=$postArr[$value['pspecid']];
		$value['cx']=array();
		//判断捆绑
		if($value['ptype']==1){
			if($value['yxbegindate']>date("Y-m-d H:i:s") || $value['yxenddate']<date("Y-m-d H:i:s")){
				$this->error('捆绑商品不在活动期!','/shopcart.html');
				exit;
			}
			if($value['synum']<$value['num']){
				$this->error('捆绑商品购买数超过剩余量!','/shopcart.html');
				exit;
			}

			$zp=new Fullzp();
			$zp=$zp->where('zid',$value['productid'])->select()->toArray();
			foreach($zp as $key1=>$value1){
				$value1['num']=$value1['num']*$value['num'];//赠送数*购买数
				$zp[$key1]=$value1;
			}
			$price += $value['price']*$value['num'];
			$value['cx'][0]['zp']=$zp;
			$value['cx'][0]['kw']='组合';
			$list[$newkey]=$value;
			++$newkey;
			unset($splist[$key]);
			continue;
		}
	}

	//判断限时促销
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		$sl=$postArr[$value['pspecid']];
		$cxfa=$cx->where(['cxlb'=>2,'pspecid'=>$value['pspecid'],'cxtype'=>0])->where('`synum` >= '.$sl)->find();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$value['cx']=array();
		$value['cx'][0]['kw']='特价';
		$value['cx'][0]['zp']=$cxfa['cxtitle'];
		$value['price']=$cxfa['cxprice'];
		$price+=$value['price']*$sl;
		$value['num']=$sl;
		$list[$newkey]=$value;
		++$newkey;
		unset($splist[$key]);
	}

	//判断规格促销
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		$sl=$postArr[$value['pspecid']];
		$cxkey=0;
		$value['cx']=array();
		$je=$value['price']*$sl;
		$cxfa=$cx->where(['cxlb'=>2,'pspecid'=>$value['pspecid'],'cxtype'=>array('neq',0)])->where('`synum` >= '.$sl)->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			if($cxlx==1 || $cxlx==2 || $cxlx==3 || $cxlx==4){
				$mjlx='mmamount';//满减类型 金额 数量
				$cjse=$je;//参减数额
			}elseif($cxlx==11 || $cxlx==12 || $cxlx==13 || $cxlx==14 || $cxlx==15){
				$mjlx='mmnum';
				$cjse=$sl;
			}
			$tlcxfa=$cxfa->filter('sx')->sort($mjlx);//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $cjse<$value2[$mjlx]){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1 || $cxlx==11) $yhje+=$value2['ljamount'];

				if($cxlx==4 || $cxlx==14){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}elseif($cxlx==15){
					$zp=new Fullpdt();
					$zp=$zp->where('pspecid',$value['pspecid'])->select()->toArray();
					$zp[0]['num']=$value2['zsnum'];
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}
		if($iscx){
			$price+=$je;
			$value['num']=$sl;
			$list[$newkey]=$value;
			++$newkey;
			unset($splist[$key]);
		}
	}


	//判断 单品 促销
	$tlsp=array();//同类商品
	foreach($splist as $key=>$value){
		if(in_array($value['productid'], $tlsp)) continue;//同类商品跳过,不删除,以便参与后续促销(在具体促销中删除)
		//计算总数量 与 总金额
		$sl=0;
		$je=0;
		$tlsp=array();//清空同类商品
		foreach($splist as $key1=>$value1){
			if($value1['productid']==$value['productid']){
				$tlsp[]=$value1['productid'];
				$sl+=$postArr[$value1['pspecid']];
				$je+=$value1['price']*$postArr[$value1['pspecid']];
			}
		}
		$cxkey=0;
		$value['cx']=array();
		$cxfa=$cx->where(['cxlb'=>3,'productid'=>$value['productid']])->where('`synum` >= '.$sl)->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			if($cxlx==1 || $cxlx==2 || $cxlx==3 || $cxlx==4){
				$mjlx='mmamount';//满减类型 金额 数量
				$cjse=$je;
			}elseif($cxlx==11 || $cxlx==12 || $cxlx==13 || $cxlx==14 || $cxlx==15){
				$mjlx='mmnum';
				$cjse=$sl;
			}
			$tlcxfa=$cxfa->filter('sx')->sort($mjlx);//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $cjse<$value2[$mjlx]){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1 || $cxlx==11) $yhje+=$value2['ljamount'];

				if($cxlx==4 || $cxlx==14){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}elseif($cxlx==15){
					$zp=new Fullpdt();
					$zp=$zp->where('pspecid',$value['pspecid'])->select()->toArray();
					$zp[0]['num']=$value2['zsnum'];
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}
		if($iscx){
			foreach($splist as $key1=>$value1){
				if($value1['productid']==$value['productid']){
					$value1['num']=$postArr[$value1['pspecid']];
					$value1['cx']=$value['cx'];
					$list[$newkey]=$value1;
					++$newkey;
					unset($splist[$key1]);
				}
			}
			$price+=$je;
		}
	}

	//判断 分类3 促销
	$tlsp=array();//同类商品
	foreach($splist as $key=>$value){
		if(in_array($value['productid'], $tlsp)) continue;//同类商品跳过,不删除,以便参与后续促销(在具体促销中删除)
		//计算 总金额
		$je=0;
		$tlsp=array();//清空同类商品
		foreach($splist as $key1=>$value1){
			if($value1['catid3']==$value['catid3']){
				$tlsp[]=$value1['productid'];
				$je+=$value1['price']*$postArr[$value1['pspecid']];
			}
		}
		$cxkey=0;
		$value['cx']=array();
		$cxfa=$cx->where(['cxlb'=>1,'category3'=>$value['catid3']])->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			$tlcxfa=$cxfa->filter('sx')->sort('mmamount');//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $je<$value2['mmamount']){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1) $yhje+=$value2['ljamount'];

				if($cxlx==4){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}

		if($iscx){
			foreach($splist as $key1=>$value1){
				if($value1['catid3']==$value['catid3']){
					$value1['num']=$postArr[$value1['pspecid']];
					$value1['cx']=$value['cx'];
					$list[$newkey]=$value1;
					++$newkey;
					unset($splist[$key1]);
				}
			}
			$price+=$je;
		}
	}

	//判断 分类2 促销
	$tlsp=array();//同类商品
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		if(in_array($value['productid'], $tlsp)) continue;//同类商品跳过,不删除,以便参与后续促销(在具体促销中删除)
		//计算 总金额
		$je=0;
		$tlsp=array();//清空同类商品
		foreach($splist as $key1=>$value1){
			if($value1['catid2']==$value['catid2']){
				$tlsp[]=$value1['productid'];
				$je+=$value1['price']*$postArr[$value1['pspecid']];
			}
		}
		$cxkey=0;
		$value['cx']=array();
		$cxfa=$cx->where(['cxlb'=>1,'category2'=>$value['catid2']])->where('category3',null)->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			$tlcxfa=$cxfa->filter('sx')->sort('mmamount');//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $je<$value2['mmamount']){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1) $yhje+=$value2['ljamount'];

				if($cxlx==4){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}
		if($iscx){
			foreach($splist as $key1=>$value1){
				if($value1['catid2']==$value['catid2']){
					$value1['num']=$postArr[$value1['pspecid']];
					$value1['cx']=$value['cx'];
					$list[$newkey]=$value1;
					++$newkey;
					unset($splist[$key1]);
				}
			}
			$price+=$je;
		}
	}


	//判断 分类1 促销
	$tlsp=array();//同类商品
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		if(in_array($value['productid'], $tlsp)) continue;//同类商品跳过,不删除,以便参与后续促销(在具体促销中删除)
		//计算 总金额
		$je=0;
		$tlsp=array();//清空同类商品
		foreach($splist as $key1=>$value1){
			if($value1['catid1']==$value['catid1']){
				$tlsp[]=$value1['productid'];
				$je+=$value1['price']*$postArr[$value1['pspecid']];
			}
		}
		$cxkey=0;
		$value['cx']=array();
		$cxfa=$cx->where(['cxlb'=>1,'category1'=>$value['catid1']])->where('category2',null)->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			$tlcxfa=$cxfa->filter('sx')->sort('mmamount');//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $je<$value2['mmamount']){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1) $yhje+=$value2['ljamount'];

				if($cxlx==4){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}

		if($iscx){
			foreach($splist as $key1=>$value1){
				if($value1['catid1']==$value['catid1']){
					$value1['num']=$postArr[$value1['pspecid']];
					$value1['cx']=$value['cx'];
					$list[$newkey]=$value1;
					++$newkey;
					unset($splist[$key1]);
				}
			}
			$price+=$je;
		}
	}

	//正常结算
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		$value['num']=$postArr[$value['pspecid']];
		$price += $value['price']*$value['num'];
		$list[$newkey]=$value;
		++$newkey;
	}
	//首次下单
	$jod=new Jsorder();
	//订单总数=取消订单总数 视为首单
	if($jod->where('userid',$uid)->count() == $jod->where(['userid'=>$uid,'status'=>9])->count()){
		$sd=array();
		$newkey=0;
		if(session('sys.firstpayjfnum') != 0){
			$sd[$newkey]['zp']='送积分'.session('sys.firstpayjfnum');
			++$newkey;
		}
		if(session('sys.firstpaycoupon') != null){
			$yhq=new Coupon();
			$yhq=$yhq->where('yid',session('sys.firstpaycoupon'))->find();
			if($yhq!=null){
				$sd[$newkey]['zp']='送'.$yhq['yname'];
				++$newkey;
			}
		}
		$zp=new Fullzp();
		$zp=$zp->where('zid','firstpay')->select()->toArray();
		if(count($zp)!=0){
			$sd[$newkey]['zp']=$zp;
			++$newkey;
		}
		$this->assign('sd',$sd);
	}

  //运费
    $freightamount=session('sys.freightamount');
	if(($price-$yhje) < $freightamount){
		$freight=session('sys.freightmin');

	}else{
		$freight=session('sys.freightmax');
	}

	$this->assign('freight',$freight);
	$this->assign('freightamount',$freightamount);
	$this->assign('price',$price);
	$this->assign('yhje',$yhje);
	$this->assign('list',$list);




	//收货地址信息
	$useraddr=new Useraddr();
	$addrlist=$useraddr->where(['userid'=>$uid])->order('ifmr desc,insertdate')->select();
	$mrdz=$useraddr->where(['userid'=>$uid,'ifmr'=>1])->order('ifmr desc,insertdate')->find();
	$this->assign('addrlist',$addrlist);
	$this->assign('mrdz',$mrdz);
	//门店信息
	$shop=new Shop();
	$shop=$shop->where(['status'=>1])->select();
	$this->assign('shop',$shop);
	//优惠券信息
	$yhq=new Fullyhq();
	$yhqArr=$yhq->where(['status'=>0,'userid'=>$uid])
		->where('ymintotal','<=',$price)
		->where('ytype','<>',3)
		->where('ybegindate','<=',date('Y-m-d H:i:s'))
		->where('yenddate','>=',date('Y-m-d H:i:s'))->select()->toArray();
	$flyhq=$yhq->where(['status'=>0,'userid'=>$uid])
		->where('ytype',3)
		->where('ybegindate','<=',date('Y-m-d H:i:s'))
		->where('yenddate','>=',date('Y-m-d H:i:s'))->select()->toArray();
	$yhqtmp=array();
	foreach($flyhq as $key=>$value){
		if(in_array($value['yid'], $yhqtmp)){
			unset($flyhq[$key]);
			continue;
		}else{
			$yhqtmp[]=$value['yid'];
		}
		if($value['ycatid3']!=null){
			$price=0;
			foreach($list as $key1=>$value1){
				if($value1['catid3'] == $value['ycatid3']){
					$price += $value1['price'] * $postArr[$value1['pspecid']];
				}
			}
			if($price==0 || $price<$value['ymintotal']){
				unset($flyhq[$key]);
			}
		}else{
			if($value['ycatid2']!=null){
				$price=0;
				foreach($list as $key1=>$value1){
					if($value1['catid2'] == $value['ycatid2']){
						$price += $value1['price'] * $postArr[$value1['pspecid']];
					}
				}
				if($price<$value['ymintotal']){
					unset($flyhq[$key]);
				}
			}else{
				if($value['ycatid1']!=null){
					$price=0;
					foreach($list as $key1=>$value1){
						if($value1['catid1'] == $value['ycatid1']){
							$price += $value1['price'] * $postArr[$value1['pspecid']];
						}
					}
					if($price<$value['ymintotal']){
						unset($flyhq[$key]);
					}
				}
			}
		}
	}
	$this->assign('yhq',$yhqArr);
	$this->assign('flyhq',$flyhq);

	//生成订单号,缓存订单号 用于防止重复提交订单
	$oid=$uid.date('YmdHis').rand(1000,9999);
	session('oid',$oid);
	$this->assign('oid',$oid);
	into();
	return $this->fetch();
}


//TODO:O 订单详情
function orderdetail(){
	$uid=session('user.userid');

	if(!input('?oid')){
		$this->error('参数错误','/allorder.html');
		exit;
	}
	$oid=input('oid');
	$map['userid']=$uid;
	$map['orderno']=$oid;
	$js=new Jsorder();

	if(request()->isAjax()){//确认收货
		$js=$js->save(['status'=>4],['orderno'=>$oid]);
		if($js!=0){
			$this->success('确认成功!','/orderdetail/oid/'.$oid.'.html');
		}else{
			$this->error('确认失败,请重试!');
		}
		exit;
	}


	$js=$js->where($map)->find();
	if($js==null){
		$this->error('参数错误','/allorder.html');
		exit;
	}
	$this->assign('js',$js);
	$addr=new Useraddr();
	$addr=$addr->where(['rid'=>$js['addrid']])->find();
	$this->assign('addr',$addr);
	$sale=new Fullsale();
	$cx=new Fullcx();
	$list=$sale->where('orderno',$oid)->where('type',['=',0],['=',2],'or')->select()->toArray();
	foreach($list as $key=>$value){
		if($value['cxid']==null) continue;
		if($value['cxid']=='zuhe'){
			$zp=$sale->where(['orderno'=>$oid,'type'=>1,'ssgg'=>$value['pspecid']])->select()->toArray();
			$value['cx'][0]['zp']=$zp;
			$value['cx'][0]['kw']='组合';
			$list[$key]=$value;
		}else{
			foreach(explode(',',$value['cxid']) as $key1=>$value1){
				$cxfa=$cx->where('cxid',$value1)->find();
				$cxlx=$cxfa['cxtype'];
				$value['cx'][$key1]['kw']='优惠';
				if($cxlx==4||$cxlx==14){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$cxfa['cxid'])->select()->toArray();
					$value['cx'][$key1]['zp']=$zp;
					$value['cx'][$key1]['kw']='满送';
				}
				elseif($cxlx==15){
					$zp[$key1]=$value;
					$zp[$key1]['num']=$cxfa['zsnum'];
					$value['cx'][$key1]['zp']=$zp;
					$value['cx'][$key1]['kw']='满送';
				}else{
					$value['cx'][$key1]['zp']=$cxfa['cxtitle'];
				}
			}
			$list[$key]=$value;
		}
	}
	//首次下单
	if($js['first']){
		$sd=array();
		$newkey=0;
		if(session('sys.firstpayjfnum') != 0){
			$sd[$newkey]['zp']='送积分'.session('sys.firstpayjfnum');
			++$newkey;
		}
		if(session('sys.firstpaycoupon') != 0){
			$yhq=new Coupon();
			$yhq=$yhq->where('yid',session('sys.firstpaycoupon'))->find();
			$sd[$newkey]['zp']='送'.$yhq['yname'];
			++$newkey;
		}
		$zp=new Fullzp();
		$zp=$zp->where('zid','firstpay')->select()->toArray();
		if(count($zp)!=0){
			$sd[$newkey]['zp']=$zp;
			++$newkey;
		}
		$this->assign('sd',$sd);
	}
	$this->assign('list',$list);

	into();
	return $this->fetch();
}

//TODO:P 修改密码
function password(){
	into();
	return $this->fetch();
}

//TODO:P 完成支付
function pay(){
	if(!input('?oid')){
		$this->error('参数错误!','/member.html');
		exit;
	}
	$oid=input('oid');
	$jsorder=new Jsorder();
	$order=$jsorder->where('orderno',$oid)->find()->toArray();
	if($order==null){
		$this->error('订单不存在!','/allorder.html');
		exit;
	}
	$ispay=0;
	if($order['status']!=1){
		$ispay=1;
	}
	if($order['zsjf']<0){
		$this->error('该订单为积分订单!','/allorder.html');
		exit;
	}
	if(input('?yue') && $ispay==0){//余额支付
		if($order['jsamount'] > session('user.balance')){
			$this->error('余额不足,正在前往充值页面!','/recharge.html');
			exit;
		}
		//修改订单状态
		$jsorder->save(['status'=>2],['orderno'=>$oid]);
		//扣除用户余额
		$user = new Userinfo();
		session('user.balance',session('user.balance')-$order['jsamount']);
		$user->save(['balance'=>session('user.balance')],['userid'=>session('user.userid')]);
		//消费记录
		$bala = new Balanceupdate();
		$data['userid']=session('user.userid');
		$data['orderno']=$oid;
		$data['orderamount']=$order['jsamount'];
		$data['userbalance']=session('user.balance');
		$data['ordertype']=2;
		$bala->save($data);

		//赠送积分
		$jf=new Integralupdate();
		$xcjf=$order['jsamount']*session('sys.integralBL');
		if($xcjf>0){
			unset($data);
			$data['userid']=session('user.userid');
			$data['orderno']=$oid;
			$data['orderintegral']=$xcjf;
			$data['ordertype']=1;
			$jf->save($data);
		}
		$cxjf=$order['zsjf'];
		if($cxjf>0){
			unset($data);
			$data['userid']=session('user.userid');
			$data['orderno']=$oid;
			$data['orderintegral']=$cxjf;
			$data['ordertype']=4;
			$jf->save($data);
		}
		//增加用户积分
		$user = new Userinfo();
		session('user.integral',session('user.integral')+$xcjf+$cxjf);
		$user->save(['integral'=>session('user.integral')],['userid'=>session('user.userid')]);
		//赠送优惠券
		if($order['zsyhq']!=null){
			$yhqlist=explode(',', $order['zsyhq']);
			foreach($yhqlist as $value){
				$cl=new Coupon_list();
				unset($data);
				$data['yid']=$value;
				$data['lqlx']=2;
				$data['userid']=session('user.userid');
				$cl->save($data);
			}
		}
		$this->success('支付完成!','/orderdetail/oid/'.$oid);
		exit;
	}
	if($ispay==0){
		//配置微信支付
		$arr['body']=session('sys.webtitle').'订单支付';//商品描述
		$arr['attach']='微信钱包订单支付';//附加数据
		$arr['oid']=$oid;//商户订单号
		$arr['fee']=$order['jsamount'];//总金额

		$arr['tag']=session('sys.webtitle').'订单';//商品标记
		$arr['url']='http://beywx.qzzs.cn/notify.html';//通知地址
		$wxzf = wxpay($arr);
		$this->assign('wxzf',$wxzf);
	}
	$this->assign('order',$order);
	$this->assign('ispay',$ispay);
	into();
	return $this->fetch();
}

//TODO:P 设置支付密码
function payment(){
	into();
	return $this->fetch();
}

//TODO:Q 二维码
function qrcode(){
	vendor('phpqrcode.phpqrcode');
	\QRcode::png('http://beywx.qzzs.cn/index/sponsor/'.session('user.userid').'.html',false,'H','5');

}
//TODO:R 充值
function recharge(){
	if(request()->isAjax()){
		$fee=input('fee');
		if($fee==null || !is_numeric($fee) || $fee*100<1){
			$fee=0;
		}
		$oid=session('user.userid').date('YmdHis').rand(1000,9999);
		//发起充值
		$data["orderno"]=$oid;
		$data["ordermoney"]=$fee;
		$data["userid"]=session("user.userid");
		$data["msg"]="发起充值请求";
		$data["dotime"]=date("Y-m-d H:i:s");
		$cz=new Cz();
		$cz->save($data);
		//配置微信支付
		$arr['body']=session('sys.webtitle').'余额充值';//商品描述
		$arr['attach']='微信钱余额充值';//附加数据
		$arr['oid']=$oid;//商户订单号
		$arr['fee']=$fee;//总金额

		$arr['tag']=session('sys.webtitle').'余额';//商品标记
		$arr['url']='http://beywx.qzzs.cn/notify.html';//通知地址
		$wxzf = wxpay($arr);
		return $wxzf;
		exit;
	}
	into();
	return $this->fetch();
}

//TODO:R 推荐有奖
function recommend(){
	into();
	return $this->fetch();
}

//TODO:R 账户余额
function records(){

	if(input('?type') && input('type')==2){
		$type=2;//消费
	}else{
		$type=1;//充值
	}
	if(request()->isAjax()){
		$postArr=request()->only(['page','size'],'post');
		$validate = Loader::validate('Basic');
		if($validate->check($postArr)){
			$map['userid']=session('user.userid');
			$map['ordertype']=$type;
			$list=new Balanceupdate();
			$list=$list->where($map)->order('insertdate desc')->limit($postArr['size'])->page($postArr['page'])->select();
			return json($list);
		}else{
			$result['error']=$validate->getError();
			return $result;
		}

		exit;
	}
	$this->assign('type',$type);
	$this->assign('user',session('user'));
	into();
	return $this->fetch();
}

//TODO:R 注册
function reg(){
	$uid=session('user.userid');
	if(session('user.mobile')!=null){
		$this->error('已绑定手机号!');
		exit;
	}
	if(request()->isAjax()){//提交手机 验证码

		if(session('verify_mobile') != input('mobile')){
			$this->error('获取验证码手机号与提交手机号不一致！');
			exit;
		}
		if(session('verify_code') != input('verify')){
			$this->error('验证码错误！');
			exit;
		}
		if(time()-session('verify_time') > 36000){//10分钟提交有效
			$this->error('验证码过期，请重新获取！');
			exit;
		}
		$data['babyname']=input('babyname');
		$data['babysex']=input('babysex');
		$data['birthday']=input('birthday');
		$user=new Userinfo();
		$user->save($data,['userid'=>session('user.userid')]);
		unset($data);
		$map['userid']=session('user.userid');
		$data['mobile']=input('mobile');
		$data['loginname']=input('mobile');
		if($user->save($data,$map)){
			if(session('sys.regjfnum') >0){
				$user=new Userinfo();
				$user->where('userid',$uid)->setInc('integral',session('sys.regjfnum'));
				$jf=new Integralupdate();
				unset($data);
				$data['userid']=$uid;
				$data['orderintegral']=session('sys.regjfnum');//增加积分数
				$data['ordertype']=4;//类型;
				$jf->save($data);
			}
			if(session('sys.regcoupon') != null){
				$yhq = new Coupon();
				$yhq=$yhq->where('yid',session('sys.regcoupon'))->find();
				if($yhq!=null){
					$cl=new Coupon_list();
					unset($data);
					$data['yid']=session('sys.regcoupon');
					$data['lqlx']=2;
					$data['userid']=$uid;
					$cl->save($data);
				}
			}
			$this->success('绑定成功！',outo());
			exit;
		}else{
			$this->error('绑定失败，请重试！');
			exit;
		}
		exit;
	}
	into();
	return $this->fetch();
}

//TODO:S 安全设置
function saftystep(){
	into();
	return $this->fetch();
}

//TODO:S 搜索
function search(){
	$uid=session('user.userid');
	$sw=new Searchword();
	$list=$sw->where('userid','0')->order('num desc')->select();
	$this->assign('list',$list);

	$uk=$sw->where('userid',$uid)->order('insertdate desc')->select();
	$this->assign('uk',$uk);
	into();
	return $this->fetch();
}

//TODO:S 分享
function share(){
	$this->jsapi();

	$uid=session('user.userid');
	$num=new Userinfo();
	$num=$num->where('sponsor',$uid)->count();
	$this->assign('num',$num);
	into();
	return $this->fetch();
}
//TODO:S 购物车
function shopcart(){
	$uid=session('user.userid');

	if(request()->isAjax()){
		$map['userid']=$uid;
		$car=new Shopcar();
		if(input('?num')){
			$spid=input('spid');
			$num=input('num');
			$map['pspecid']=$spid;
			if($num==0){
				//删除
				$car->where($map)->delete();
				return 1;
			}else{
				//修改
				$car->where($map)->update(['num' => $num]);
				return 1;
			}
			exit;
		}else{
			$spid=input('spid/a');
			foreach($spid as $key=>$value){
				$map['pspecid']=$value;
				$car->where($map)->delete();
			}
			return 1;
			exit;
		}
	}
	//post提交订单信息
	if(request()->isPost()){
		$postArr=request()->post();
		session('order',$postArr);
		$this->redirect('order');
		exit;
	}
	$car=new Fullcar();
	$car=$car->where(['userid'=>$uid])->select();

	$this->assign('car',$car);


	into();
	return $this->fetch();
}


//TODO:S 系统设置
function step(){
	into();
	return $this->fetch();
}

//TODO:T 消息中心
function tidings(){
	$uid=session('user.userid');

	if(request()->isAjax()){
		if(input('?del')){//删除短消息
			$rid=input('rid');
			$list=new Zndxx();
			$list=$list->where(['rid'=>$rid,'userid'=>$uid])->delete();
			if($list!=0){
				return 1;
			}else{
				return 0;
			}
			exit;
		}

		$postArr=request()->only(['page','size'],'post');
		$validate = Loader::validate('Basic');
		if($validate->check($postArr)){
			$list=new Zndxx();
			$list=$list->where('userid',$uid)->order('isRead,insertdate desc')->limit($postArr['size'])->page($postArr['page'])->select();
			return json($list);
		}else{
			$result['error']=$validate->getError();
			return $result;
		}
		exit;
	}

	into();
	return $this->fetch();
}


//TODO:T 确认兑换
function tureexchange(){
	$uid=session('user.userid');


	if(!input('?eid')){
		$this->error('参数错误');
		exit;
	}
	if(!session('?eid')){
		$this->error('获取订单信息失败!');
		exit;
	}
	$eid=strtoupper(input('eid'));
	if(input('?p')){//支付积分
		$jod=new Jsorder();
		$order=$jod->where('orderno',$eid)->find();
		if($order==null){
			$this->error('订单未生成,请重新兑换!');
			exit;
		}
		if($order['status']!=1){
			$this->error('订单已经支付或已取消!');
			exit;
		}
		if($order['zsjf']>=0 || $order['jsamount']>0){//防止用积分兑换 普通商品订单
			$this->error('请使用余额或者微信支付!');
			exit;
		}

		$jod->where('orderno',$order['orderno'])->setField('status',2);
		$jod->where('orderno',$order['orderno'])->setField('payway',2);
		$user=new Userinfo();
		$userinfo=$user->where('userid',$uid)->find();
		if($userinfo['integral']+$order['zsjf']<0){
			$this->error('当前积分不足!');
			exit;
		}
		$user->where('userid',$uid)->setInc('integral',$order['zsjf']);

		$sod=new Saleorder();
		$sod=$sod->where('orderno',$order['orderno'])->find();
		$jf=new Integralupdate();
		$data['userid']=$uid;
		$data['orderintegral']=$order['zsjf'];//增加积分数
		$data['ordertype']=3;//类型
		$data['orderno']=$sod['pspecid'];
		$jf->save($data);
		$this->success('兑换成功!');
		exit;
	}
	$postArr=session('exchange');

	if(!session('?exchange.addrid')){
		$this->error('地址参数错误!');
		exit;
	}
	$addrid=$postArr['addrid'];
	$addr=new Useraddr();
	$addr=$addr->where(['rid'=>$addrid])->find();
	if($addr==null){
		$this->error('该地址无效');
		exit;
	}
	$this->assign('addr',$addr);

	$spid=session('exchange.spid');
	$p = new Fullpdt();
	$map['ptype']=2;
	$map['pspecid']=$spid;
	$p = $p->where($map)->find();
	if($p==null){
		$this->error('没有找到该商品!');
		exit;
	}
	$this->assign('p',$p);

	$remark=session('exchange.remark');
	$this->assign('remark',$remark);


	$jod=new Jsorder();
	$order=$jod->where('orderno',$eid)->find();
	if($order==null){
		$sod=new Saleorder();
		$data['orderno']=$eid;
		$data['pspecid']=$p['pspecid'];//赠品规格
		$data['price']=$p['price'];
		$data['num']=1;
		$data['type']=2;
		$data['costprice']=$p['costprice'];
		$sod->save($data);
		unset($data);



		//加到结算订单表
		$data['orderno']=$eid;
		$data['userid']=$uid;
		$data['shopid']=session('exchange.shop');
		//$data['pickway']=
		//$data['payway']=
		//$data['fhsj']=
		$data['addrid']=$addrid;
		//$data['wlgs']=
		//$data['wldh']=
		$data['remark']=$remark;
		$data['zsjf']=-$p['price'];
		$jod=$jod->save($data);
		if($jod!=1){
			$sod=new Saleorder();
			$sod->where('orderno',$eid)->delete();
			$this->error('订单添加失败!', session('url'));
			exit;
		}
		unset($data);
	}
	into();
	return $this->fetch();

}
//TODO:确认订单
function tureorder(){
	$uid=session('user.userid');

	//if(!request()->isPost()){
	//	$this->error('错误提交方式!','shopcart');
	//	exit;
	//}
	if(!input('?oid')){
		$this->error('参数错误','/shopcart.html');
		exit;
	}
	if(!session('?oid')){
		$this->error('获取订单信息失败!','/shopcart.html');
		exit;
	}
	$oid=strtoupper(input('oid'));
	$jod=new Jsorder();
	$jod=$jod->where('orderno',$oid)->count();
	$unin=1;
	if($jod!=0){//订单存在
		$unin=0;
	}

	$postArr=session('order');


	if(!session('?order.addrid')){
		$this->error('地址参数错误','/shopcart.html');
		exit;
	}
	$addrid=$postArr['addrid'];
	$addr=new Useraddr();
	$addr=$addr->where(['rid'=>$addrid])->find();
	if($addr==null){
		$this->error('该地址无效','/shopcart.html');
		exit;
	}
	$remark=session('order.remark');
	$splist = new Fullpdt();
	$splist = $splist->where('pspecid','in',$postArr['spid'])->select()->toArray();
	$postArr = array_combine($postArr['spid'],$postArr['num']);
	$price=0;
	$yhje=0;
	$zsjf=0;
	$list=array();
	$cx=new Fullcx();
	$cxmaster=new Cxmaster();
	$newkey=0;
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		$value['num']=$postArr[$value['pspecid']];
		$value['cx']=array();
		//判断捆绑
		if($value['ptype']==1){
			if($value['yxbegindate']>date("Y-m-d H:i:s") || $value['yxenddate']<date("Y-m-d H:i:s")){
				$this->error('捆绑商品不在活动期!','/shopcart.html');
				exit;
			}
			if($value['synum']<$value['num']){
				$this->error('捆绑商品购买数超过剩余量!','/shopcart.html');
				exit;
			}

			$zp=new Fullzp();
			$zp=$zp->where('zid',$value['productid'])->select()->toArray();
			foreach($zp as $key1=>$value1){
				$value1['num']=$value1['num']*$value['num'];//赠送数*购买数
				$zp[$key1]=$value1;
			}
			$price += $value['price']*$value['num'];
			$value['cx'][0]['zp']=$zp;
			$value['cx'][0]['kw']='组合';
			$list[$newkey]=$value;
			++$newkey;
			unset($splist[$key]);
			continue;
		}
	}

	//判断限时促销
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		$sl=$postArr[$value['pspecid']];
		$cxfa=$cx->where(['cxlb'=>2,'pspecid'=>$value['pspecid'],'cxtype'=>0])->where('`synum` >= '.$sl)->find();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$value['cx']=array();
		$value['cx'][0]['kw']='特价';
		$value['cx'][0]['cxfa']=$cxfa;
		$value['cx'][0]['zp']=$cxfa['cxtitle'];
		$value['price']=$cxfa['cxprice'];
		$price+=$value['price']*$sl;
		$value['num']=$sl;
		$list[$newkey]=$value;
		++$newkey;
		unset($splist[$key]);
	}
	//判断规格促销
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		$sl=$postArr[$value['pspecid']];
		$cxkey=0;
		$value['cx']=array();
		$je=$value['price']*$sl;
		$cxfa=$cx->where(['cxlb'=>2,'pspecid'=>$value['pspecid']])->where('`synum` >= '.$sl)->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			if($cxlx==1 || $cxlx==2 || $cxlx==3 || $cxlx==4){
				$mjlx='mmamount';//满减类型 金额 数量
				$cjse=$je;//参减数额
			}elseif($cxlx==11 || $cxlx==12 || $cxlx==13 || $cxlx==14 || $cxlx==15){
				$mjlx='mmnum';
				$cjse=$sl;
			}
			$tlcxfa=$cxfa->filter('sx')->sort($mjlx);//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $cjse<$value2[$mjlx]){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1 || $cxlx==11) $yhje+=$value2['ljamount'];
				if($cxlx==3 || $cxlx==13) $zsjf+=$value2['jfnum'];
				if($cxlx==4 || $cxlx==14){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}elseif($cxlx==15){
					$zp=new Fullpdt();
					$zp=$zp->where('pspecid',$value['pspecid'])->select()->toArray();
					$zp[0]['num']=$value2['zsnum'];
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				$value['cx'][$cxkey]['cxfa']=$value2;
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}
		if($iscx){
			$price+=$je;
			$value['num']=$sl;
			$list[$newkey]=$value;
			++$newkey;
			unset($splist[$key]);
		}
	}


	//判断 单品 促销
	$tlsp=array();//同类商品
	foreach($splist as $key=>$value){
		if(in_array($value['productid'], $tlsp)) continue;//同类商品跳过,不删除,以便参与后续促销(在具体促销中删除)
		//计算总数量 与 总金额
		$sl=0;
		$je=0;
		$tlsp=array();//清空同类商品
		foreach($splist as $key1=>$value1){
			if($value1['productid']==$value['productid']){
				$tlsp[]=$value1['productid'];
				$sl+=$postArr[$value1['pspecid']];
				$je+=$value1['price']*$postArr[$value1['pspecid']];
			}
		}
		$cxkey=0;
		$value['cx']=array();
		$cxfa=$cx->where(['cxlb'=>3,'productid'=>$value['productid']])->where('`synum` >= '.$sl)->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			if($cxlx==1 || $cxlx==2 || $cxlx==3 || $cxlx==4){
				$mjlx='mmamount';//满减类型 金额 数量
				$cjse=$je;
			}elseif($cxlx==11 || $cxlx==12 || $cxlx==13 || $cxlx==14 || $cxlx==15){
				$mjlx='mmnum';
				$cjse=$sl;
			}
			$tlcxfa=$cxfa->filter('sx')->sort($mjlx);//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $cjse<$value2[$mjlx]){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1 || $cxlx==11) $yhje+=$value2['ljamount'];
				if($cxlx==3 || $cxlx==13) $zsjf+=$value2['jfnum'];
				if($cxlx==4 || $cxlx==14){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}elseif($cxlx==15){
					$zp=new Fullpdt();
					$zp=$zp->where('pspecid',$value['pspecid'])->select()->toArray();
					$zp[0]['num']=$value2['zsnum'];
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				$value['cx'][$cxkey]['cxfa']=$value2;
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}

		if($iscx){
			foreach($splist as $key1=>$value1){
				if($value1['productid']==$value['productid']){
					$value1['num']=$postArr[$value1['pspecid']];
					$list[$newkey]=$value1;
					++$newkey;
					unset($splist[$key1]);
				}
			}
			$price+=$je;
			$value['num']=$postArr[$value['pspecid']];
			$list[$newkey-1]=$value;
		}
	}

	//判断 分类3 促销
	$tlsp=array();//同类商品
	foreach($splist as $key=>$value){
		if(in_array($value['productid'], $tlsp)) continue;//同类商品跳过,不删除,以便参与后续促销(在具体促销中删除)
		//计算 总金额
		$je=0;
		$tlsp=array();//清空同类商品
		foreach($splist as $key1=>$value1){
			if($value1['catid3']==$value['catid3']){
				$tlsp[]=$value1['productid'];
				$je+=$value1['price']*$postArr[$value1['pspecid']];
			}
		}
		$cxkey=0;
		$value['cx']=array();
		$cxfa=$cx->where(['cxlb'=>1,'category3'=>$value['catid3']])->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			$tlcxfa=$cxfa->filter('sx')->sort('mmamount');//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $je<$value2['mmamount']){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1) $yhje+=$value2['ljamount'];
				if($cxlx==3) $zsjf+=$value2['jfnum'];
				if($cxlx==4){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				$value['cx'][$cxkey]['cxfa']=$value2;
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}

		if($iscx){
			foreach($splist as $key1=>$value1){
				if($value1['catid3']==$value['catid3']){
					$value1['num']=$postArr[$value1['pspecid']];
					$list[$newkey]=$value1;
					++$newkey;
					unset($splist[$key1]);
				}
			}
			$price+=$je;
			$value['num']=$postArr[$value['pspecid']];
			$list[$newkey-1]=$value;
		}
	}

	//判断 分类2 促销
	$tlsp=array();//同类商品
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		if(in_array($value['productid'], $tlsp)) continue;//同类商品跳过,不删除,以便参与后续促销(在具体促销中删除)
		//计算 总金额
		$je=0;
		$tlsp=array();//清空同类商品
		foreach($splist as $key1=>$value1){
			if($value1['catid2']==$value['catid2']){
				$tlsp[]=$value1['productid'];
				$je+=$value1['price']*$postArr[$value1['pspecid']];
			}
		}
		$cxkey=0;
		$value['cx']=array();
		$cxfa=$cx->where(['cxlb'=>1,'category2'=>$value['catid2']])->where('category3',null)->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			$tlcxfa=$cxfa->filter('sx')->sort('mmamount');//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $je<$value2['mmamount']){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1) $yhje+=$value2['ljamount'];
				if($cxlx==3) $zsjf+=$value2['jfnum'];
				if($cxlx==4){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				$value['cx'][$cxkey]['cxfa']=$value2;
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}

		if($iscx){
			foreach($splist as $key1=>$value1){
				if($value1['catid2']==$value['catid2']){
					$value1['num']=$postArr[$value1['pspecid']];
					$list[$newkey]=$value1;
					++$newkey;
					unset($splist[$key1]);
				}
			}
			$price+=$je;
			$value['num']=$postArr[$value['pspecid']];
			$list[$newkey-1]=$value;
		}
	}


	//判断 分类1 促销
	$tlsp=array();//同类商品
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		if(in_array($value['productid'], $tlsp)) continue;//同类商品跳过,不删除,以便参与后续促销(在具体促销中删除)
		//计算 总金额
		$je=0;
		$tlsp=array();//清空同类商品
		foreach($splist as $key1=>$value1){
			if($value1['catid1']==$value['catid1']){
				$tlsp[]=$value1['productid'];
				$je+=$value1['price']*$postArr[$value1['pspecid']];
			}
		}
		$cxkey=0;
		$value['cx']=array();
		$cxfa=$cx->where(['cxlb'=>1,'category1'=>$value['catid1']])->where('category2',null)->select();
		if(count($cxfa)==0) continue;//判断是否进行促销
		$cxfatmp=array();//存放已处理过的促销方案
		$iscx=0;//是否满足促销
		foreach($cxfa as $key1=>$value1){
			if(in_array($value1['cxid'], $cxfatmp)) continue;
			$cxlx=$value1['cxtype'];
			$ycx=0;
			session('cxtype',$cxlx);
			$tlcxfa=$cxfa->filter('sx')->sort('mmamount');//筛选同类促销方案 排序 common.php中函数
			foreach($tlcxfa as $key2=>$value2){
				if($ycx || $je<$value2['mmamount']){
					$cxfatmp[]=$value2['cxid'];
					continue;
				}
				$iscx=1;//满足
				$value['cx'][$cxkey]['kw']='优惠';
				if($cxlx==1) $yhje+=$value2['ljamount'];
				if($cxlx==3) $zsjf+=$value2['jfnum'];
				if($cxlx==4){
					$zp=new Fullzp();
					$zp=$zp->where('zid',$value2['cxid'])->select()->toArray();
					$value['cx'][$cxkey]['zp']=$zp;
					$value['cx'][$cxkey]['kw']='赠送';
				}
				else	$value['cx'][$cxkey]['zp']=$value2['cxtitle'];
				$value['cx'][$cxkey]['cxfa']=$value2;
				++$cxkey;
				$cxfatmp[]=$value2['cxid'];
				$ycx=1;
			}
		}

		if($iscx){
			foreach($splist as $key1=>$value1){
				if($value1['catid1']==$value['catid1']){
					$value1['num']=$postArr[$value1['pspecid']];
					$list[$newkey]=$value1;
					++$newkey;
					unset($splist[$key1]);
				}
			}
			$price+=$je;
			$value['num']=$postArr[$value['pspecid']];
			$list[$newkey-1]=$value;
		}
	}

	//正常结算
	foreach($splist as $key=>$value){//数量用从客户端传过来的
		$value['num']=$postArr[$value['pspecid']];
		if($value['stocknum']<$value['num']){
			$this->error('商品:'.$value['spmc'].'已售空,请重新下单!','/shopcart.html');
			exit;
		}
		$price += $value['price']*$value['num'];
		$list[$newkey]=$value;
		++$newkey;
	}


	//使用优惠券
	$rid='';
	if(session('?order.yhq') && session('order.yhq')!=null){
		$rid=session('order.yhq');
		$yhq=new Fullyhq();
		$yhq=$yhq->where(['status'=>0,'userid'=>$uid,'rid'=>$rid])->find();
		if($yhq==null){
			$this->error('使用优惠券不存在,请重试!', '/shopcart.html');
			exit;
		}
		if( ($price-$yhje) < $yhq['ymintotal']){
			$this->error('使用优惠券条件不足,请重试!', '/shopcart.html');
			exit;
		}
		$yhje+=$yhq['yamount'];
		$this->assign('yhq',$yhq);
		if($unin){
			$yhq=Coupon_list::get($rid);
			$yhq->status=1;
			$yhq->save();
		}
	}

	//专用 优惠券
	$ridlist=array();
	$flyhq=array();
	if(session('?order.flyhq') && session('order.flyhq')!=null){
		$ridlist=session('order.flyhq');
		foreach($ridlist as $key=>$value){
			$yhq=new Fullyhq();
			$yhq=$yhq->where(['status'=>0,'userid'=>$uid,'rid'=>$value])->find();
			if($yhq==null){
				$this->error('使用优惠券不存在,请重试!', '/shopcart.html');
				exit;
			}
			$flyhq[]=$yhq;
			$yhje+=$yhq['yamount'];
			if($unin){
				$yhq=Coupon_list::get($value);
				$yhq->status=1;
				$yhq->save();
			}
		}
	}
	$ridlist=implode(',',$ridlist);
	$this->assign('flyhq',$flyhq);

	$yhqlist=array();
	if($unin){
		$car=new Shopcar();
		//加到销售订单表  增加销售量 减少库存
		foreach($list as $key=>$value){
			$cxid=null;
			$cxidlist=array();
			$dpyhje=0;
			if(isset($value['cx'])){
				foreach($value['cx'] as $key1=>$value1){
					if(isset($value1['cxfa'])){
						$cxid=$value1['cxfa']['cxid'];
						$cxidlist[]=$cxid;
						$cxlx=$value1['cxfa']['cxtype'];
						if( $cxlx==4 || $cxlx==14 ||$cxlx==15){
							foreach($value1['zp'] as $key2=>$value2){
								$sod=new Saleorder();
								$data['orderno']=$oid;
								$data['pspecid']=$value2['pspecid'];//赠品规格
								$data['price']=$value2['price'];
								$data['num']=$value2['num'];
								$data['type']=1;
								$data['cxid']=$cxid;
								$data['costprice']=$value2['costprice'];
								$data['ssgg']=$value['pspecid'];//所属商品规格
								$sod->save($data);
								unset($data);
								//减赠品库存
								$spec=new Productspec();
								$spec->where('pspecid',$value2['pspecid'])->setInc('SaleNum', $value2['num']);
								$spec->where('pspecid',$value2['pspecid'])->setDec('stocknum', $value2['num']);
							}
						}elseif($cxlx==1 || $cxlx==11){
							$dpyhje+=$value1['cxfa']['ljamount'];
						}elseif($cxlx==2 || $cxlx==12){
							$yhqlist[]=$value1['cxfa']['coupon'];
						}
						$cxmaster->where('cxid',$cxid)->setDec('synum',$value['num']);

					}
					if($value['ptype']==1 ){
						$cxid='zuhe';
						foreach($value['cx'][0]['zp'] as $key2=>$value2){
							$sod=new Saleorder();
							$data['orderno']=$oid;
							$data['pspecid']=$value2['pspecid'];//赠品规格
							$data['price']=$value2['price'];
							$data['num']=$value2['num'];
							$data['type']=1;
							$data['cxid']=$cxid;
							$data['costprice']=$value2['costprice'];
							$data['ssgg']=$value['pspecid'];//所属商品规格
							$sod->save($data);
							unset($data);
							//减赠品库存
							$spec=new Productspec();
							$spec->where('pspecid',$value2['pspecid'])->setInc('SaleNum', $value2['num']);
							$spec->where('pspecid',$value2['pspecid'])->setDec('stocknum', $value2['num']);
						}

						$pdt=new Product();
						$pdt->where('productid',$value['productid'])->setDec('synum',$value['num']);
					}
				}
			}
			$sod=new Saleorder();
			$data['orderno']=$oid;
			$data['pspecid']=$value['pspecid'];
			$data['num']=$value['num'];
			$data['price']=$value['price'];
			$data['amount']=$value['price']*$value['num'];
			$data['costprice']=$value['costprice'];
			$data['yhamount']=$dpyhje;
			$data['jsamount']=$data['amount']-$data['yhamount'];
			$data['cxid']=implode(',',$cxidlist);
			$sod->save($data);
			unset($data);
			$spec=new Productspec();
			$spec->where('pspecid',$value['pspecid'])->setInc('SaleNum', $value['num']);
			$spec->where('pspecid',$value['pspecid'])->setDec('stocknum', $value['num']);




			//删除购物车
			$car->where(['userid'=>$uid,'pspecid'=>$value['pspecid']])->delete();
		}
	}

	//首次下单
	$jod=new Jsorder();
	$first=0;
	//订单总数=取消订单总数 视为首单
	if($jod->where('userid',$uid)->count() == $jod->where(['userid'=>$uid,'status'=>9])->count()){
		$first=1;
		$sd=array();
		$newkey=0;
		if(session('sys.firstpayjfnum') != 0){
			$sd[$newkey]['zp']='送积分'.session('sys.firstpayjfnum');
			++$newkey;

			$zsjf+=session('sys.firstpayjfnum');
		}
		if(session('sys.firstpaycoupon') != null){
			$yhq=new Coupon();
			$yhq=$yhq->where('yid',session('sys.firstpaycoupon'))->find();
			if($yhq!=null){
				$sd[$newkey]['zp']='送'.$yhq['yname'];
				++$newkey;
				$yhqlist[]=$yhq['yid'];
			}
		}
		$zp=new Fullzp();
		$zp=$zp->where('zid','firstpay')->select()->toArray();
		if($unin){
			foreach($zp as $key=>$value){
				$sod=new Saleorder();
				$data['orderno']=$oid;
				$data['pspecid']=$value['pspecid'];//赠品规格
				$data['price']=$value['price'];
				$data['num']=$value['num'];
				$data['type']=1;
				$data['cxid']='first';
				$data['costprice']=$value['costprice'];
				$data['ssgg']=$value['pspecid'];//所属商品规格
				$sod->save($data);
				unset($data);
				//减赠品库存
				$spec=new Productspec();
				$spec->where('pspecid',$value['pspecid'])->setInc('SaleNum', $value['num']);
				$spec->where('pspecid',$value['pspecid'])->setDec('stocknum', $value['num']);
			}
		}
		if(count($zp)!=0){
			$sd[$newkey]['zp']=$zp;
			++$newkey;
		}
		$this->assign('sd',$sd);
	}

  //运费
	if(($price-$yhje) < session('sys.freightamount')){

		if(session("order.selectkuaidi")=='自提'){
			$freight=session('sys.freightmax');
		}else{
			$freight=session('sys.freightmin');
		}
	}else{
		$freight=session('sys.freightmax');
	}

	if($unin){
		$jod=new Jsorder();
		//加到结算订单表
		unset($data);
		$data['orderno']=$oid;
		$data['userid']=$uid;
		$data['shopid']=session('order.shop');
		$data['amount']=$price;
		$data['yhamount']=$yhje;
		$data['jsamount']=$data['amount']-$data['yhamount']+$freight;
		//$data['pickway']=
		//$data['payway']=
		//$data['fhsj']=
		$data['addrid']=$addrid;
		//$data['wlgs']=
		//$data['wldh']=
		$data['remark']=$remark;
		$data['zsjf']=$zsjf;
		$data['zsyhq']=implode(',',$yhqlist);
		$data['syyhq']=$rid;
		$data['flyhq']=$ridlist;
		$data['first']=$first;
		$data['freight']=$freight;
		$jod=$jod->save($data);
		if($jod!=1){
			$sod=new Saleorder();
			$sod->where('orderno',$oid)->delete();
			$this->error('订单添加失败!', session('url'));
			exit;
		}
	}

	$this->assign('freight',$freight);
	$this->assign('remark',$remark);
	$this->assign('addr',$addr);
	$this->assign('yhje',$yhje);
	$this->assign('price',$price-$yhje+$freight);
	$this->assign('list',$list);
	into();
	return $this->fetch();

}

//TODO:省市选择
function village(){
	into();
	return $this->fetch();
}


//TODO:我的红包
function ygq(){
	into();
	return $this->fetch();
}

//TODO:优惠卷
function yhq(){
	if(input('type') == 1) $type=1;
	elseif(input('type') ==2 )$type=2;
	else $type = 0;
	if(request()->isAjax()){
		$postArr=request()->only(['page','size'],'post');
		$validate = Loader::validate('Basic');
		if($validate->check($postArr)){
			$map['userid']=session('user.userid');
			$map['status']=$type;
			$list=new Fullyhq();
			$list=$list->where($map)->order('lqtime desc')->limit($postArr['size'])->page($postArr['page'])->select();
			return json($list);
		}else{
			$result['error']=$validate->getError();
			return $result;
		}
		exit;
	}

	$this->assign('type',$type);
	into();
	return $this->fetch();
}

//TODO:优惠卷详情
function yhqdetail(){
	if(!input('?yid')){
		$this->error('参数错误');
		exit;
	}
	$map['yid']=input('yid');
	$map['ytype']=0;
	$c=new Coupon();
	$c=$c->where($map)->find();
	if($c==null){
		$this->error('优惠券不存在!');
		exit;
	}

	if(request()->isAjax()){
		if(session('user.integral') < $c['yintegralnum']){
			$this->error('积分不足!');
			exit;
		}
		$cl=new Coupon_list();
		$data['yid']=$c['yid'];
		$data['lqlx']=0;
		$data['userid']=session('user.userid');
		$cl->save($data);

		$iu=new Integralupdate();
		unset($data);
		$data['userid']=session('user.userid');
		$data['orderno']=$c['yid'];
		$data['orderintegral']=-$c['yintegralnum'];
		$data['ordertype']=3;
		$iu->save($data);

		$user=new Userinfo();
		$user->save(['integral'=>session('user.integral')-$c['yintegralnum']],['userid'=>session('user.userid')]);
		$this->success('领取成功!','/yhq.html');
		exit;
	}

	$this->assign('c',$c);
	into();
	return $this->fetch();
}





























































































































































}
