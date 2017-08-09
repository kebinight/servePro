<?php

/**
 * 全局状态码及常规说明
 * 全局常量及其说明
 * Created by PhpStorm.
 * User: kebin
 * Date: 2017/4/24
 * Time: 11:01
 */

class GlobalCode {

    /**
     * ********************************************************
     * 全局状态码
     * ********************************************************
     */
    const FAIL = -1;  //操作失败
    const SUCCESS = 200;  //处理成功
    const REG_NOT_FIN = 201;  //未完成注册
    const FORBIDDEN_ACCOUNT = 202;  //账号被限制登录
    const ACCOUNT_NOT_FIND = 203; //未注册的账号

    const API_CHECK_NOPASS = 401;  //接口安全认证不通过
    const NO_LOGIN = 403;  //尚未登录
    const API_NOT_SAFE = 405;  //请求受限制，无法访问接口
    const TIMESTAMP_OUTTIME = 408;  //接口时间戳过期
    const OPTIONS_NOTRIGHT = 412;  //参数不正确
    const ERROR = 500;      //失败的调用

    /**
     * ********************************************************
     * 全局常量
     * ********************************************************
     */
    const COMMON_STATUS_OFF = 0;  //通用状态，禁用
    const COMMON_STATUS_ON = 1;   //通用状态,启用


    /**
     * 获取全局状态码
     * @param null $code
     * @return mixed|string
     */
    public static function toString($code = null) {
        $strs = [
            GlobalCode::FAIL => '操作失败',
            GlobalCode::SUCCESS => '处理成功',
            GlobalCode::REG_NOT_FIN => '未完成注册',
            GlobalCode::FORBIDDEN_ACCOUNT => '账号被限制登录',
            GlobalCode::ACCOUNT_NOT_FIND => '未注册的账号',
            GlobalCode::API_CHECK_NOPASS => '接口安全认证不通过',
            GlobalCode::NO_LOGIN => '尚未登录',
            GlobalCode::API_NOT_SAFE => '请求受限制，无法访问接口',
            GlobalCode::TIMESTAMP_OUTTIME => '接口时间戳过期',
            GlobalCode::OPTIONS_NOTRIGHT => '参数不正确',
            GlobalCode::ERROR => '失败的调用',
        ];

        if($code !== null) {
            return isset($strs[$code])?$strs[$code] : '未知状态';
        } else {
            return '';
        }
    }

}