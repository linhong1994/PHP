<?php

namespace Addons\Bll;
use Common\Controller\Addon;

/**
 * 大自然插件
 * @author 安静丶是明白
 */

    class bllAddon extends Addon{

        public $info = array(
            'name'=>'Bll',
            'title'=>'大自然',
            'description'=>'大自然',
            'status'=>1,
            'author'=>'安静丶是明白',
            'version'=>'1.0',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Bll/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Bll/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }