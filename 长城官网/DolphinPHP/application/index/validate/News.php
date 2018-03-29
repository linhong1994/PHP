<?php

namespace app\index\validate;

use think\Validate;


class News extends Validate
{
    // 定义验证规则
    protected $rule = [
        'title'         => 'require',
        'intro'         => 'require',
        'content'       => 'require',
    ];

    // 定义验证提示
    protected $message = [
        'title'         => '标题不能为空',
        'intro'         => '简介不能为空',
        'content'       => '内容不能为空',
    ];
}
