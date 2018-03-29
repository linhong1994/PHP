<?php
namespace app\validate;

use think\Validate;

class Basic extends Validate
{
    protected $rule =   [
        'page' => 'require|number',//页数
        'size' => 'require|number',//每页大小
    ];

    protected $message  =   [
        'page.require' => '页数必须输入',
        'page.number' => '页数必须为数字',
        'size.require' => '页数大小必须输入',
        'size.require' => '页数大小必须为数字',
        
    ];

}