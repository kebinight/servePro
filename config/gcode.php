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
    //支付模块
    //支付方式
    const PAY_TYPE_WX = 1;  //微信支付
    const PAY_TYPE_ALI = 2;  //支付宝支付
    const PAY_TYPE_MB = 3;   //美币支付
    //支付单
    const PAYORDER_TYPE_CZ = 1; //充值美币
    const PAYORDER_TYPE_TC = 2;    //购买套餐
    const PAYORDER_TYPE_CZTC = 3;    //购买充值套餐
    const PAYORDER_TYPE_WX = 10;  //查看美女微信
    const PAYORDER_TYPE_BVD = 11;  //查看形象视频
    const PAYORDER_TYPE_GIFT = 12;  //赠送礼物

    //设置模块
    const SETTING_DATE_SHARE_DESC = 1;  //约会页分享描述设置
    const SETTING_CHAT_SETTING = 2;  //每天可以与单个用户免费聊XX句话
    const SETTING_PAY_SETTING = 3;   //支付配置

    //会员中心模块
    const VIP_VIP = 1;
    const VIP_RECHARGE = 2;
    const VIP_LINSHI = 10;  //临时套餐

    //后台消息通知模块
    const ADMIN_NOTICE_BASIC_PIC = 1;     //用户更新形象照片
    const ADMIN_NOTICE_AUTH_VID = 2;  //用户更新真人视频
    const ADMIN_NOTICE_AVATAR = 3;   //用户更新头像
    const ADMIN_NOTICE_ACTIVITY = 4;        //用户报名参加活动
    const ADMIN_NOTICE_USER_REGIST = 5;     //用户注册
    const ADMIN_NOTICE_WITHDRAW = 6;        //用户提现
    const ADMIN_NOTICE_ADVICE = 7;          //用户反馈意见

    //打招呼用语模块
    const SAY_HI_MAP = 1;           //地图及机器人打招呼
    const SAY_HI_COMMON = 2;        //便捷用语

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

    /**
     * 获取支付方式
     */
    public static function getPayType($type = null) {
        $strs = [
            GlobalCode::PAY_TYPE_WX => '微信支付',
            GlobalCode::PAY_TYPE_ALI => '支付宝支付',
        ];
        if($type !== null) {
            return isset($strs[$type]) ? $strs[$type] : '未知支付方式';
        } else {
            return $strs;
        }
    }


    /*
     * 获取前端需要展示的支付方式相关信息
     */
    public static function getPayui8Id($type = null) {
        $configs = getConfig('pay');
        $arr = [
            GlobalCode::PAY_TYPE_WX => $configs['wx'],
            GlobalCode::PAY_TYPE_ALI => $configs['ali'],
        ];

        if($type !== null) {
            return isset($arr[$type]) ? $arr[$type]: '';
        } else {
            return $arr;
        }
    }


    /**
     * 获取设置类型
     */
    public static function getSettingType($type = null) {
        $strs = [
            GlobalCode::SETTING_DATE_SHARE_DESC => '约会页分享描述设置',
            GlobalCode::SETTING_CHAT_SETTING => '每天可以与单个用户免费聊XX句话',
            GlobalCode::SETTING_PAY_SETTING => '支付配置',
        ];
        if($type !== null) {
            return isset($strs[$type]) ? $strs[$type] : '未知类型';
        } else {
            return $strs;
        }
    }

    /**
     * 获取套餐类型
     */
    public static function getPackageType($type = null) {
        $strs = [
            GlobalCode::VIP_VIP => 'VIP套餐',
            GlobalCode::VIP_RECHARGE => '充值套餐',
        ];
        if($type !== null) {
            return isset($strs[$type]) ? $strs[$type] : '未知类型';
        } else {
            return $strs;
        }
    }

    /**
     * 获取支付订单类型
     * @param null $st
     * @return array|mixed|string
     */
    public static function getPayOrderType($st = null) {
        $status = Array(
            GlobalCode::PAYORDER_TYPE_CZ => '充值',
            GlobalCode::PAYORDER_TYPE_TC => '购买套餐',
            GlobalCode::PAYORDER_TYPE_CZTC => '购买充值套餐',
            GlobalCode::PAYORDER_TYPE_WX => '支付TA的微信',
            GlobalCode::PAYORDER_TYPE_BVD => '支付形象视频',
            GlobalCode::PAYORDER_TYPE_GIFT => '赠送礼物',
        );
        if($st !== null) {
            return isset($status[$st]) ? $status[$st] : '未知';
        }
        return $status;
    }

    /**
     * 获取后台消息类型
     */
    public static function getAdminNotice($type = null)
    {
        $status = Array(
            GlobalCode::ADMIN_NOTICE_BASIC_PIC => '更新形象照片',
            GlobalCode::ADMIN_NOTICE_AUTH_VID => '更新真人视频',
            GlobalCode::ADMIN_NOTICE_AVATAR => '更新头像',
            GlobalCode::ADMIN_NOTICE_ACTIVITY => '报名活动',
            GlobalCode::ADMIN_NOTICE_USER_REGIST => '成功注册',
            GlobalCode::ADMIN_NOTICE_WITHDRAW => '申请提现',
            GlobalCode::ADMIN_NOTICE_ADVICE => '反馈意见',
        );
        if($type !== null) {
            return isset($status[$type]) ? $status[$type] : '未知';
        }
        return $status;
    }


    /**
     * 获取打招呼用语类型
     */
    public static function getSayHiType($type = null)
    {
        $status = Array(
            GlobalCode::SAY_HI_MAP => '地图打招呼用语',
            GlobalCode::SAY_HI_COMMON => '便捷打招呼用语',
        );
        if($type !== null) {
            return isset($status[$type]) ? $status[$type] : '未知';
        }
        return $status;
    }
}