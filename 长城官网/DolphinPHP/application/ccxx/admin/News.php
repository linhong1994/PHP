<?php

namespace app\ccxx\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\ccxx\model\News as newsmodel;


class News extends Admin
{

  function index()
  {
    // 查询
    $map = $this->getMap();
    // 排序
    $order = $this->getOrder('time desc,status desc');
    // 数据列表
    $data_list = newsmodel::where($map)->order($order)->paginate();//getList($map, $order);
    // 新增或编辑页面的字段
    $fields = [
        ['hidden', 'id'],
        ['text', 'title', '标题'],
        ['image', 'pic', '封面'],
        ['text', 'author', '作者', '','admin'],
        ['text', 'view', '阅读量', '','0'],
        ['textarea', 'intro', '简介'],
        ['ckeditor', 'content', '内容'],
        ['radio', 'status', '立即启用', '', ['否', '是'], 1]
    ];
    // 使用ZBuilder快速创建数据表格
    return ZBuilder::make('table')
      ->setSearch(['title' => '标题','author' => '作者']) // 设置搜索框
      ->addColumns([ // 批量添加数据列
        ['id', 'ID'],
        ['pic', '封面', 'picture'],
        ['title', '标题', 'text.edit'],
        ['author', '作者', 'text.edit'],
        ['view', '阅读量', 'number'],
        ['time', '更新时间'],
        ['status', '状态', 'switch'],
        ['right_button', '操作', 'btn']
      ])
      ->autoAdd($fields, '', true, true) // 添加自动新增按钮
      ->addTopButtons('enable,disable,delete') // 批量添加顶部按钮
      ->autoEdit($fields, '', true, true) // 添加自动编辑按钮
      ->addRightButtons(['delete']) // 批量添加右侧按钮
      ->addOrder('id,time,status')
      ->setRowList($data_list) // 设置表格数据
      ->fetch(); // 渲染模板
  }
  

	
	
	
	
	
	
	
	
	
}