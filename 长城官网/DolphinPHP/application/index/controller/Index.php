<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\index\controller;
use app\index\model\Demand;
use app\index\model\News;
/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Home
{
  public function index()
  {
    // 默认跳转模块
    if (config('home_default_module') != 'index') {
        $this->redirect(config('home_default_module'). '/index/index');
    }
		$news=new News();
		$new = $news->where('status',1)->order('time desc')->limit(3)->select();
		$this->assign('new',$new);
		

    return $this->fetch(); // 渲染模板
  }
	
	public function blog_single(){
		if(input('?id')){
			$id=input('id');
		}else{
			$this->error('参数错误啦!!',url('blog'));
		}
		$news=new News();
		$vo=$news->where('id',$id)->find();
		$this->assign('vo',$vo);
		
		$new = $news->where('status',1)->order('time desc')->limit(10)->select();
		$this->assign('new',$new);
		$news->where('id',$id)->setInc('view');
		
		return $this->fetch(); // 渲染模板
	}
	
	public function blog(){
/*		if(input('?page')){
			$page=inpu('page');
		}else{
			$page=1;
		}*/
		$news=new News();
		$list = $news->where('status',1)->order('time desc')->paginate(config('web_site_page'));
		$this->assign('list',$list);
		$hot = $news->where('status',1)->order('view desc')->limit(10)->select();
		$this->assign('hot',$hot);
		
		return $this->fetch(); // 渲染模板
	}
	
	public function project_image(){
		return $this->fetch(); // 渲染模板
	}
	
	public function project(){
		return $this->fetch(); // 渲染模板
	}
	
	public function publish(){
		if(request()->isPost()){
			$data['name']=input('name');
			$data['tel']=input('tel');
			$data['qq']=input('qq');
			$data['demand']=input('demand');
			$demand=new Demand();
			if($demand->save($data)){
				$this->success('提交成功,请等待客服联系!',url('index'));
			}else{
				$this->success('提交失败,请重新填写!');
			}
		}
		return $this->fetch(); // 渲染模板
	}
	public function us_image(){
		return $this->fetch(); // 渲染模板
	}
}
