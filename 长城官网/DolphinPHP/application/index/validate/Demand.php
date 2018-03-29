<?php

namespace app\index\validate;

use think\Validate;


class Demand extends Validate
{
    // 定义验证规则
    protected $rule = [
        'name'      => 'require',
        'tel'       => 'require|number',
        'qq'				=> 'require',
        'demand'    => 'require',
    ];

    // 定义验证提示
    protected $message = [
        'name'         => '姓名不能为空',
        'tel.require'  => '电话不能为空',
        'tel.number'   => '电话只能为数字',
        'qq'           => 'QQ/微信不能为空',
        'demand'       => '需求描述不能为空',
     ];
}
