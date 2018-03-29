<?php
namespace app\model;

use think\Model;
use think\Session;
class Useraddr extends Model{
public $user;

//TODO:自定义初始化
protected function initialize()
{
    //需要调用`Model`的`initialize`方法
    parent::initialize();
    //自定义的初始化
    $this->user=Session::get('user');
}


//TODO:去除默认地址
function undefault(){
	$map['userid']=$this->user['userid'];
	$map['ifmr']=1;
	$useraddr = $this->where($map)->find();
	if($useraddr!=null){
		$map['rid']=$useraddr['rid'];
		$data['ifmr']=0;
		$this->where($map)->update($data);
	}

}

//TODO:设置默认地址
function setdefault($rid){
	$map['userid']=$this->user['userid'];
	$map['rid']=$rid;
	$useraddr = $this->where($map)->find();
	if($useraddr==null){
		return '该地址记录不存在!';
	}else{
		$this->undefault();
		if($this->where($map)->update(['ifmr'=>1])){
			return null;
		}else{
			return '更新数据失败!';
		}
	}
	
}




//TODO:删除地址
function del($rid){
	$map['userid']=$this->user['userid'];
	$useraddr=$this->where(['rid'=>$rid])->find();
	if($useraddr==null){
		return '该地址记录不存在!';
	}else{
		if($this->where(['rid'=>$rid])->delete()){
			$rid = $this->where($map)->find()['rid'];
			$this->setdefault($rid);
		}else{
			return '删除数据失败!';
		}
	}

}








}