<?php
namespace app\validate;

use think\Validate;

class Detail extends Validate
{
	
protected $rule =   [
    'type'=>'require|alpha|length:2',
    'pid'=>'alphaNum|length:5',
    'psid'=>'alphaNum|length:7',
    'num'=>'number|gt:0',
];

protected $message  =   [
    'type.require' => '状态信息出错',
    'type.alpha' => '状态信息出错',
    'type.length' => '状态信息出错',
    
    'pid.alphaNum' => '商品信息出错',
    'pid.length' => '商品信息出错',
    
    'psid.alphaNum' => '规格信息出错',
    'psid.length' => '规格信息出错',
    
		'num.number'=>'数量信息出错',
		'num.gt'=>'数量信息出错',
];










}