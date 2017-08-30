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
    //通用业务类状态
    const API_SUCCESS           = 200;  //处理成功
    const API_REG_NO_FIN        = 201;  //未完成注册
    const API_FORBIDDEN_ACCOUNT = 202;  //账号被限制登录
    const API_NO_ACCOUNT        = 203; //未注册的账号
    const API_NO_LOGIN          = 403; //用户尚未登录

    //接口调用类状态
    const API_ERROR             = 10100;      //失败的调用
    const API_OPTIONS           = 10101;  //参数不正确

    const API_CHECK_NOPASS      = 10200;  //接口安全认证不通过
    const API_TIMEOUT           = 10201;  //接口时间戳过期

    const API_NOT_SAFE          = 10300;  //请求受限制，无法访问接口
    const API_NO_LIMIT          = 10301;  //请求受限制，缺少访问权限

    //其他业务类状态(> 20000)
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
            GlobalCode::API_SUCCESS => '处理成功',
            GlobalCode::API_REG_NO_FIN => '未完成注册',
            GlobalCode::API_FORBIDDEN_ACCOUNT => '账号被限制登录',
            GlobalCode::API_NO_ACCOUNT => '未注册的账号',
            GlobalCode::API_NO_LOGIN => '尚未登录',

            GlobalCode::API_ERROR => '失败的调用',
            GlobalCode::API_OPTIONS => '参数不正确',

            GlobalCode::API_CHECK_NOPASS => '接口安全认证不通过',
            GlobalCode::API_TIMEOUT => '时间戳过期',

            GlobalCode::API_NOT_SAFE => '请求接口受限制',
            GlobalCode::API_NO_LIMIT => '缺少访问权限',
        ];

        if($code !== null) {
            return isset($strs[$code])?$strs[$code] : '非法状态，请联系管理员';
        } else {
            return '';
        }
    }

}