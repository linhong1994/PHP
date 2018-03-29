<?php

namespace app\ccxx\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\ccxx\model\Demand as demandmodel;


class Demand extends Admin
{

  function index()
  {
    // 查询
    $map = $this->getMap();
    // 排序
    $order = $this->getOrder('time desc,status desc');
    // 数据列表
    $data_list = demandmodel::where($map)->order($order)->paginate();
    // 新增或编辑页面的字段
    $fields = [
        ['hidden', 'id'],
        ['text', 'name', '姓名'],
        ['text', 'tel', '电话'],
        ['text', 'qq', 'QQ/微信'],
        ['ckeditor', 'demand', '需求描述'],
        ['radio', 'status', '立即启用', '', ['否', '是'], 1],
    ];
    // 使用ZBuilder快速创建数据表格
    return ZBuilder::make('table')
      ->setSearch(['name' => '姓名', 'tel' => '电话', 'qq' => 'QQ/微信', 'demand' => '需求描述']) // 设置搜索框
      ->addColumns([ // 批量添加数据列
        ['id', 'ID'],
        ['name', '姓名'],
        ['tel', '电话'],
        ['qq', 'QQ/微信'],
        ['time', '提交时间'],
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