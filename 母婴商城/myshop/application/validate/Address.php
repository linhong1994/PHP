<?php
namespace app\validate;

use think\Validate;

class Address extends Validate
{
	
protected $rule =   [
    'province'=>'require|chs|max:10|checkprovince',//省份
    'city'=>'require|chs|max:10|checkcity',//城市
    'county'=>'require|chs|max:10|checkcounty',//县区
    'address'=>'require|chsDash|max:50',//详细地址
    'lxr'=>'require|chsAlpha|max:10',//联系人
    'lxfs'=>'require|number|max:13',//联系方式
];

protected $message  =   [
    'province.require' => '省份必须输入',
    'province.chs' => '省份必须为汉字',
    'province.max' => '省份长度必须小于10',
    
    'city.require' => '市级必须输入',
    'city.chs' => '市级必须为汉字',
    'city.max' => '市级长度必须小于10',
    
    'county.require' => '县区必须输入',
    'county.chs' => '县区必须为汉字',
    'county.max' => '县区长度必须小于10',
    
    'address.require' => '详细地址必须输入',
    'address.chsDash' => '详细地址不能包含特殊字符',
    'address.max' => '详细地址长度必须小于50',
    
    'lxr.require' => '联系人必须输入',
    'lxr.chsAlpha' => '联系人必须为汉字和字母',
    'lxr.max' => '联系人长度必须小于10',
    
    'lxfs.require' => '联系方式必须输入',
    'lxfs.number' => '联系方式必须为数字',
    'lxfs.max' => '联系方式长度必须小于13',
    
];

function checkprovince($value){
	return (strstr($value,'请选择')==null) ? true : '请选择省份'; 
}
function checkcity($value){
	return (strstr($value,'请选择')==null) ? true : '请选择市级'; 
}
function checkcounty($value){
	return (strstr($value,'请选择')==null) ? true : '请选择县区'; 
}










}