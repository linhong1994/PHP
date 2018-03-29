<?php

namespace app\ccxx\admin;

use app\admin\controller\Admin;
use think\Db;
use app\ccxx\model\News;
use app\ccxx\model\Demand;


class Index extends Admin
{

  function index()
  {

		$news=new News();
		$newscount=$news->where('status',1)->count();
		$this->assign('newscount',$newscount);
		$demand=new Demand();
		$demandcount=$demand->where('status',1)->count();
		$this->assign('demandcount',$demandcount);
    $this->assign('page_title', '首页');
    return $this->fetch(); // 渲染模板
  }
  

	
	
	
	
	
	
	
	
	
}