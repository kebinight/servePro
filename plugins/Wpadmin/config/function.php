<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2015-12-26 22:45:09 by allen <blog.rc5j.cn> , caowenpeng1990@126.com
 */


/**
 * 获取数据库写入的错误信息
 * @param type $errors
 * @return type
 */
function getMessage($errors) {
    if(\Cake\Core\Configure::read('debug')) {
        Cake\Log\Log::error($errors,'devlog');
    }
    foreach ($errors as $value) {
        foreach ($value as $val) {
            $error = $val;
            break;
        }
    }
    return $error;
}

function getPluginConfig($key){
    return \Cake\Core\Configure::read($key);
}

/**
 * 模板的默认输出方法 
 * @param type $val 变量值
 * @param type $default 默认值
 * @return type
 */
function templateDefault($val,$default){
    return empty($val)?$default:$val;
}

function randColor(){
    $colors = ['muted','active','success','warning','danger'];
    $rand = rand(0, 4);
    return $colors[$rand];
}