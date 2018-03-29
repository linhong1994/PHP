<?php
namespace app\model;

use think\Model;
use think\Collection;
use think\Session;
class Integralupdate extends Model{

public $user;

//自定义初始化
protected function initialize()
{
    //需要调用`Model`的`initialize`方法
    parent::initialize();
    //TODO:自定义的初始化
    $this->user=Session::get('user');
}

//增加一条记录 增加积分
function add($series){
	$data['userid']=$this->user['userid'];
	$data['orderintegral']=5;//增加积分数
	$data['ordertype']=2;//类型
	$data['series']=$series;//连续天数
	if($this->data($data)->save()){
		return $data;
	}else{
		unset($data);
		$data['error']='增加记录失败！';
		return $data;
	}
}

//每日签到
function sign(){
	$where['userid']=$this->user['userid'];
	$where['ordertype']=2;
	if($where['userid']==null){
		$where['error']='获取用户信息失败!';
		return $where;
	}
	$result=$this->where($where)->order('insertdate desc')->find();
	if($result==null){
		return $this->add(1);
	}else{
		$time=$result['insertdate'];
		$time=date('Y-m-d', strtotime($time));//获取到天单位
		$time=strtotime(date('Y-m-d'))-strtotime($time);
		if($time==0){
			$where['error']='今日已签到!';
			return $where;
		}elseif($time==86400){//连续签到
			return $this->add($result['series']+1);
		}else{//多天未签到
			return $this->add(1);
		}
		
	}
	
}




























}