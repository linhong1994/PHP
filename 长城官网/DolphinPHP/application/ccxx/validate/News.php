<?php

namespace app\ccxx\validate;

use think\Validate;


class News extends Validate
{
    // 定义验证规则
    protected $rule = [
        'title'         => 'require|max:30',
        'pic'						=> 'require',
        'intro'         => 'require',
        'content'       => 'require',
    ];

    // 定义验证提示
    protected $message = [
        'title.require' => '标题不能为空',
        'title.max'     => '标题最大30个字',
        'pic'						=> '封面必须上传',
        'intro'       	=> '简介不能为空',
        'content'       => '内容不能为空',
    ];
}
