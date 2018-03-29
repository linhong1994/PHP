<?php

return [
    // 模块名[必填]
    'name'        => 'ccxx',
    // 模块标题[必填]
    'title'       => '长城信息',
    // 模块唯一标识[必填]，格式：模块名.开发者标识.module
    'identifier'  => 'ccxx.anjing.module',
    // 模块图标[选填]
    'icon'        => 'fa fa-fw fa-newspaper-o',
    // 模块描述[选填]
    'description' => '长城信息模块',
    // 开发者[必填]
    'author'      => '安静丶是明白',
    // 版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
    'version'     => '1.0.0',
    // 数据表[有数据库表时必填]
    'tables' => [
        'ccxx_demand',
        'ccxx_news',
    ],
    // 原始数据库表前缀
    // 用于在导入模块sql时，将原有的表前缀转换成系统的表前缀
    // 一般模块自带sql文件时才需要配置
    'database_prefix' => 'dp_',
];
