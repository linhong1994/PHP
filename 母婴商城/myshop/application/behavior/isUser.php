<?php
namespace app\behavior;

use think\Controller;
use think\Session;
use think\Db;
/**
* 
*/
class isUser
{
  use \traits\controller\Jump;//类里面引入jump;类

  //绑定到CheckAuth标签，可以用于检测Session以用来判断用户是否登录
  public function run(&$params){
		if(Session::has('user'))
		{
			$user = Session::get('user');
			$map['id']=$user['id'];
			if(Db::table('user')->where($map)->value('rand') != $user['rand']){
				Session::delete('user');
				return $this->error('请登录！','login');
			}
		}else{
			return $this->error('请登录！','login');
		}
    
  }
}