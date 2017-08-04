<?php

/**
 * @date : 2016-5-3
 * @author : Wash Cai <1020183302@qq.com>
 */

namespace App\Utils\umeng;

require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCustomizedcast.php');

/**
 * 友盟推送
 * @author Wash Cai <1020183302@qq.com>
 *
 */
class Umeng {

    protected $appkey = NULL;
    protected $appMasterSecret = NULL;
    protected $timestamp = NULL;
    protected $validation_token = NULL;

    function __construct($android_key, $android_secret, $ios_key, $ios_secret) {
        $this->android_appkey = $android_key;
        $this->android_appMasterSecret = $android_secret;
        $this->ios_appkey = $ios_key;
        $this->ios_appMasterSecret = $ios_secret;
        $this->timestamp = strval(time());
    }

    /**
     * 广播(低于200个app集成为未上线无限制，上线之后每天不超过3次)
     * @param string $title 广播标题
     * @param string $content 广播内容
     * @param string $ticker 通知栏提示文字
     * @param boolean $production_mode 是否生产环境，true为生产环境，false为测试环境
     * @param string $extra 用户自定义key-value。只对"通知(display_type=notification)"生效。可以配合通知到达后, 打开App, 打开URL, 打开Activity使用。
     * @param string $expire_time 消息过期时间,其值不可小于发送时间,默认为3天后过期。格式: "YYYY-MM-DD hh:mm:ss"。
     * @param int $badge ios消息数量提示
     * @param string $after_open 之后打开，值为"go_app", "go_url", "go_activity", "go_custom",
     *                                           "go_app": 打开应用
     *                                           "go_url": 跳转到URL
     *                                           "go_activity": 打开特定的activity
     *                                           "go_custom": 用户自定义内容。
     * @@param string $sound ios声音
     * @return boolean true:发送成功;false:发送失败
     */
    function sendAll($title, $content, $ticker, $production_mode = 'true', $extra = '', $expire_time = '', $badge = '', $after_open = '', $sound = '') {
        // 安卓推送
        $android_brocast = new \AndroidNotification();
        $android_brocast->setAppMasterSecret($this->android_appMasterSecret);
        $android_brocast->setPredefinedKeyValue("appkey", $this->android_appkey);
        $android_brocast->setPredefinedKeyValue("timestamp", $this->timestamp); // 时间戳
        $android_brocast->setPredefinedKeyValue('expire_time', $expire_time); // 过期时间

        $android_brocast->setPredefinedKeyValue("ticker", $ticker); // 安卓提示
        $android_brocast->setPredefinedKeyValue("title", $title); // 安卓标题
        $android_brocast->setPredefinedKeyValue("text", $content); // 安卓内容
        $android_brocast->setPredefinedKeyValue("after_open", $after_open); // 之后打开
        $android_brocast->setPredefinedKeyValue("activity", 'com.chinamatop.club.app.activity.MainActivity'); // 对应的activity
        $android_brocast->setPredefinedKeyValue("production_mode", $production_mode); // 生产环境
        $android_brocast->setPredefinedKeyValue('type', 'broadcast'); // 类型为广播
        $android_brocast->setExtraField("extra", $extra); // 安卓额外内容
        $android_res = json_decode($android_brocast->send());

        // ios推送
        $ios_brocast = new \IOSNotification();
        $ios_brocast->setAppMasterSecret($this->ios_appMasterSecret);
        $ios_brocast->setPredefinedKeyValue("appkey", $this->ios_appkey);
        $ios_brocast->setPredefinedKeyValue("timestamp", $this->timestamp);
        $ios_brocast->setPredefinedKeyValue('type', 'broadcast'); // 类型为广播
        $ios_brocast->setPredefinedKeyValue("production_mode", $production_mode); // 生产环境
        $ios_brocast->setPredefinedKeyValue("alert", $title); // ios标题
        $ios_brocast->setPredefinedKeyValue("badge", $badge); // ios信息提示
        $ios_brocast->setPredefinedKeyValue("sound", $sound); // ios声音
        $ios_brocast->setCustomizedField("extra", $extra); // ios额外内容
        $ios_res = json_decode($ios_brocast->send());
        if ($ios_res->ret == 'FAIL' && $android_res->ret == 'FAIL') {
            if ($production_mode) {
                return false;
            } else {
                return '安卓:' . $this->showError($android_res->data->error_code) . '|苹果:' . $this->showError($ios_res->data->error_code);
            }
        }
        if ($ios_res->ret == 'SUCCESS' || $android_res->ret == 'SUCCESS') {
            if ($production_mode) {
                return true;
            } else {
                return 'ios任务id:' . $ios_res->data->msg_id . '|android任务id:' . $android_res->data->msg_id;
            }
        }
    }

    /**
     * 单播（alias以','分开可以列播，不可超过50个，超过建议使用文件播）
     * @param string or array $alias 用户自定义标识
     * @param string $title 标题
     * @param string $content 内容
     * @param string $ticker 通知栏提示文字
     * @param string $alias_type 用户类型， 一定要与客户端的一样
     * @param boolean $production_mode 是否生产环境，true为生产环境，false为测试环境
     * @param array $extra 用户自定义key-value。只对"通知(display_type=notification)"生效。可以配合通知到达后, 打开App, 打开URL, 打开Activity使用。
     * @param string $expire_time 消息过期时间,其值不可小于发送时间,默认为3天后过期。格式: "YYYY-MM-DD hh:mm:ss"。
     * @param int $badge ios消息数量提示
     * @param string $after_open 之后打开，值为"go_app", "go_url", "go_activity", "go_custom",
     *                                  "go_app": 打开应用
     *                                  "go_url": 跳转到URL
     *                                  "go_activity": 打开特定的activity
     *                                  "go_custom": 用户自定义内容。
     * @param string $sound ios声音
     * @return boolean true:发送成功;false:发送失败;
     */
    function sendAlias($alias, $title, $content, $ticker, $alias_type, $production_mode = 'true', $extra = '', $expire_time = '', $badge = '', $after_open = '', $sound = '') {
        // 安卓推送
        $android_brocast = new \AndroidNotification();
        $android_brocast->setAppMasterSecret($this->android_appMasterSecret);
        $android_brocast->setPredefinedKeyValue("appkey", $this->android_appkey);
        $android_brocast->setPredefinedKeyValue("alias", $alias); // 用户自定义标识
        $android_brocast->setPredefinedKeyValue("alias_type", $alias_type); // 用户类型
        $android_brocast->setPredefinedKeyValue("timestamp", $this->timestamp); // 时间戳
        $android_brocast->setPredefinedKeyValue('expire_time', $expire_time); // 过期时间
        $android_brocast->setPredefinedKeyValue("ticker", $ticker); // 安卓提示
        $android_brocast->setPredefinedKeyValue("title", $title); // 安卓标题
        $android_brocast->setPredefinedKeyValue("text", $content); // 安卓内容
        $android_brocast->setPredefinedKeyValue("after_open", $after_open); // 之后打开
        $android_brocast->setPredefinedKeyValue("activity", 'com.chinamatop.club.app.activity.MainActivity'); // 对应的activity
        $android_brocast->setPredefinedKeyValue("production_mode", $production_mode); // 生产环境
        $android_brocast->setPredefinedKeyValue('type', 'customizedcast'); // 类型为单播
        $android_brocast->setExtraField("extra", $extra); // 安卓额外内容
        $android_res = json_decode($android_brocast->send());

        // ios推送
        $ios_brocast = new \IOSNotification();
        $ios_brocast->setAppMasterSecret($this->ios_appMasterSecret);
        $ios_brocast->setPredefinedKeyValue("appkey", $this->ios_appkey);
        $ios_brocast->setPredefinedKeyValue("alias", $alias); // 用户自定义标识
        $ios_brocast->setPredefinedKeyValue("alias_type", $alias_type); // 用户类型
        $ios_brocast->setPredefinedKeyValue("timestamp", $this->timestamp); // 时间戳
        $ios_brocast->setPredefinedKeyValue("production_mode", $production_mode); // 生产环境
        $ios_brocast->setPredefinedKeyValue('type', 'customizedcast'); // 类型为单播
        $ios_brocast->setPredefinedKeyValue("alert", $title); // ios标题
        $ios_brocast->setPredefinedKeyValue("badge", $badge); // ios信息提示
        $ios_brocast->setPredefinedKeyValue("sound", $sound); // ios声音
        $ios_brocast->setCustomizedField("extra", $extra); // ios额外内容
        $ios_res = json_decode($ios_brocast->send());
        if ($ios_res->ret == 'FAIL' && $android_res->ret == 'FAIL') {
            if ($production_mode) {
                return false;
            } else {
                return '安卓:' . $this->showError($android_res->data->error_code) . '|苹果:' . $this->showError($ios_res->data->error_code);
            }
        }
        if ($ios_res->ret == 'SUCCESS' || $android_res->ret == 'SUCCESS') {
            if ($production_mode) {
                return true;
            } else {
                return 'ios任务id:' . (isset($ios_res->data->msg_id)?$ios_res->data->msg_id:'') . '|android任务id:' . (isset($android_res->data->msg_id)?$android_res->data->msg_id:'');
            }
        }
    }

    /**
     * 文件播（用来群播）
     * @param string $title 标题
     * @param string $content 内容
     * @param string $ticker 通知栏提示文字
     * @param string $file 用户的alias标识以换行符：\n拼接起来，每行一个，文件最大不可超过10M
     * @param string $alias_type 用户类型， 一定要与客户端的一样
     * @param boolean $production_mode 是否生产环境，true为生产环境，false为测试环境
     * @param int $badge ios消息数量提示
     * @param string $after_open 之后打开，值为"go_app", "go_url", "go_activity", "go_custom",
     *                                  "go_app": 打开应用
     *                                  "go_url": 跳转到URL
     *                                  "go_activity": 打开特定的activity
     *                                  "go_custom": 用户自定义内容。
     * @param string $sound ios声音
     * @return boolean true:发送成功;false:发送失败;
     */
    function sendFile($title, $content, $ticker, $file, $alias_type, $production_mode = 'true', $extra = '', $badge = '', $after_open = '', $sound = '') {
        // 安卓推送
        $android_data = '';
        $android_res = '';
        $android_brocast = new \AndroidNotification();
        $android_brocast->setAppMasterSecret($this->android_appMasterSecret);
        $android_brocast->setPredefinedKeyValue("appkey", $this->android_appkey);
        $android_brocast->setPredefinedKeyValue("timestamp", $this->timestamp); // 时间戳
        $android_brocast->setPredefinedKeyValue('type', 'customizedcast'); // 类型为群播
        $android_brocast->setPredefinedKeyValue('alias_type', $alias_type); // 用户类型
        $android_brocast->setPredefinedKeyValue("ticker", $ticker); // 提示信息
        $android_brocast->setPredefinedKeyValue("title", $title); // 标题
        $android_brocast->setPredefinedKeyValue("text", $content); // 内容
        $android_brocast->setPredefinedKeyValue("after_open", $after_open);  // 安卓之后动作
        $android_brocast->setPredefinedKeyValue("activity", 'com.chinamatop.club.app.activity.MainActivity'); // 对应的activity
        $android_brocast->setPredefinedKeyValue("production_mode", $production_mode); // 生产环境
        $android_brocast->setExtraField("extra", $extra); // 安卓额外内容
        $android_upload = $android_brocast->uploadContents($file);
        $android_data = json_decode($android_upload);
        if ($android_data->ret == 'SUCCESS') {
            $android_brocast->setPredefinedKeyValue("file_id", $android_data->data->file_id); // 设置上传的file_id]
            $android_res = json_decode($android_brocast->send());
        }
        // ios推送
        $ios_data = '';
        $ios_res = '';
        $ios_brocast = new \IOSNotification();
        $ios_brocast->setAppMasterSecret($this->ios_appMasterSecret);
        $ios_brocast->setPredefinedKeyValue("appkey", $this->ios_appkey);
        $ios_brocast->setPredefinedKeyValue("timestamp", $this->timestamp); // 时间戳
        $ios_brocast->setPredefinedKeyValue('alias_type', $alias_type); // 用户类型
        $ios_brocast->setPredefinedKeyValue("production_mode", $production_mode); // 生产环境
        $ios_brocast->setPredefinedKeyValue('type', 'customizedcast'); // 类型为群播
        $ios_brocast->setPredefinedKeyValue("alert", $title); // ios提示信息
        $ios_brocast->setPredefinedKeyValue("badge", $badge); // ios信息数量提示
        $ios_brocast->setPredefinedKeyValue("sound", $sound); // ios声音提示
        $ios_brocast->setCustomizedField("extra", $extra); // ios额外内容
        $ios_upload = $ios_brocast->uploadContents($file);
        $ios_data = json_decode($ios_upload);
        if ($ios_data->ret == 'SUCCESS') {
            $ios_brocast->setPredefinedKeyValue("file_id", $ios_data->data->file_id); // 设置上传的file_id
            $ios_res = json_decode($ios_brocast->send());
        }
        if ($ios_data->ret == 'FAIL' && $android_data->ret == 'FAIL') {
            if ($production_mode) {
                return false;
            } else {
                return '安卓:' . $this->showError($android_res->data->error_code) . '|苹果:' . $this->showError($ios_res->data->error_code);
            }
        }
        if ($ios_res->ret == 'FAIL' && $android_res->ret == 'FAIL') {
            if ($production_mode) {
                return false;
            } else {
                return '安卓:' . $this->showError($android_res->data->error_code) . '|苹果:' . $this->showError($ios_res->data->error_code);
            }
        }
        if ($ios_res->ret == 'SUCCESS' || $android_res->ret == 'SUCCESS') {
            if ($production_mode) {
                return true;
            } else {
                return 'ios任务id:' . $ios_res->data->task_id . '|android任务id:' . $android_res->data->task_id;
            }
        }
    }

    /**
     * 任务状态查询
     * @param string $task_id 发送信息成功后返回的task_id
     * @return string 返回信息里面有成功与否和信息
     */
    function android_check($task_id) {
        $url = 'http://msg.umeng.com/api/status';
        $this->data->task_id = $task_id;
        $this->data->appkey = $this->android_appkey;
        $this->data->timestamp = $this->timestamp;
        $this->data->appMasterSecret = $this->android_appMasterSecret;
        $postBody = json_encode($this->data);
        $sign = md5("POST" . $url . $postBody . $this->android_appMasterSecret);
        $url = $url . "?sign=" . $sign;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 任务状态查询
     * @param string $task_id 发送信息成功后返回的task_id
     * @return string 返回信息里面有成功与否和信息
     */
    function ios_check($task_id) {
        $url = 'http://msg.umeng.com/api/status';
        $this->data->task_id = $task_id;
        $this->data->appkey = $this->ios_appkey;
        $this->data->timestamp = $this->timestamp;
        $this->data->appMasterSecret = $this->ios_appMasterSecret;
        $postBody = json_encode($this->data);
        $sign = md5("POST" . $url . $postBody . $this->ios_appMasterSecret);
        $url = $url . "?sign=" . $sign;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 测试环境下的错误码提示
     * @param int $code 错误码
     * @return string 返回错误提示
     */
    function showError($code) {
        switch ($code) {
            case '1000':
                return '请求参数没有appkey';
            case '1001':
                return '请求参数没有payload';
            case '1002':
                return '请求参数payload中没有body';
            case '1003':
                return 'display_type为message时，请求参数没有custom';
            case '1004':
                return '请求参数没有display_type';
            case '1005':
                return 'img url格式不对，请以https或者http开始';
            case '1006':
                return 'sound url格式不对，请以https或者http开始';
            case '1007':
                return 'url格式不对，请以https或者http开始';
            case '1008':
                return 'display_type为notification时，body中ticker不能为空';
            case '1009':
                return 'display_type为notification时，body中title不能为空';
            case '1010':
                return 'display_type为notification时，body中text不能为空';
            case '1011':
                return 'play_vibrate的值只能为true或者false';
            case '1012':
                return 'play_lights的值只能为true或者false';
            case '1013':
                return 'play_sound的值只能为true或者false';
            case '1014':
                return 'task-id没有找到';
            case '1015':
                return '请求参数中没有device_tokens';
            case '1016':
                return '请求参数没有type';
            case '1017':
                return 'production_mode只能为true或者false';
            case '1018':
                return 'appkey错误：指定的appkey尚未开通推送服务';
            case '1019':
                return 'display_type填写错误';
            case '1020':
                return '应用组中尚未添加应用';
            case '2000':
                return '该应用已被禁用';
            case '2001':
                return '过期时间必须大于当前时间';
            case '2002':
                return '定时发送时间必须大于当前时间';
            case '2003':
                return '过期时间必须大于定时发送时间';
            case '2004':
                return 'IP白名单尚未添加, 请到网站后台添加您的服务器IP白名单。';
            case '2005':
                return '该消息不存在';
            case '2006':
                return 'validation token错误';
            case '2007':
                return '未对请求进行签名';
            case '2008':
                return 'json解析错误';
            case '2009':
                return '请填写alias或者file_id';
            case '2010':
                return '与alias对应的device_tokens为空';
            case '2011':
                return 'alias个数已超过50';
            case '2012':
                return '此appkey今天的广播数已超过3次';
            case '2013':
                return '消息还在排队，请稍候再查询';
            case '2014':
                return '消息取消失败，请稍候再试';
            case '2015':
                return 'device_tokens个数已超过50';
            case '2016':
                return '请填写filter';
            case '2017':
                return '添加tag失败';
            case '2018':
                return '请填写file_id';
            case '2019':
                return '与此file_id对应的文件不存在';
            case '2020':
                return '服务正在升级中，请稍候再试';
            case '2021':
                return 'appkey不存在';
            case '2022':
                return 'payload长度过长';
            case '2023':
                return '文件上传失败，请重试';
            case '2024':
                return '限速值必须为正整数';
            case '2025':
                return 'aps字段不能为空';
            case '2026':
                return '1分钟内发送任务次数超出3次';
            case '2027':
                return '签名不正确';
            case '2028':
                return '时间戳已过期';
            case '2029':
                return 'content内容不能为空';
            case '2030':
                return 'launch_from/not_launch_from条件中的日期须小于发送日期';
            case '2031':
                return 'filter格式不正确';
            case '2032':
                return '未上传生产证书，请到Web后台上传';
            case '2033':
                return '未上传开发证书，请到Web后台上传';
            case '2034':
                return '证书已过期';
            case '2035':
                return '定时任务证书过期';
            case '2036':
                return '时间戳格式错误';
            case '2038':
                return '文件上传失败';
            case '3000':
                return '友盟数据库错误';
            case '3001':
                return '友盟数据库错误';
            case '3002':
                return '友盟数据库错误';
            case '3003':
                return '友盟数据库错误';
            case '3004':
                return '友盟数据库错误';
            case '4000':
                return '友盟系统错误';
            case '4001':
                return '友盟系统忙';
            case '4002':
                return '友盟操作失败';
            case '4003':
                return 'appkey格式错误';
            case '4004':
                return '消息类型格式错误';
            case '4005':
                return 'msg格式错误';
            case '4006':
                return 'body格式错误';
            case '4007':
                return 'deliverPolicy格式错误';
            case '4008':
                return '失效时间格式错误';
            case '4009':
                return '单个服务器队列已满';
            case '4010':
                return '设备号格式错误';
            case '4011':
                return '消息扩展字段无效';
            case '4012':
                return '没有权限访问';
            case '4013':
                return '异步发送消息失败';
            case '4014':
                return 'appkey和device_tokens不对应';
            case '4015':
                return '没有找到应用信息';
            case '4016':
                return '文件编码有误';
            case '4017':
                return '文件类型有误';
            case '4018':
                return '文件远程地址有误';
            case '4019':
                return '文件描述信息有误';
            case '4020':
                return 'device_token有误(注意，友盟的device_token是严格的44位字符串)';
            case '4021':
                return 'HSF异步服务超时';
            case '4022':
                return 'appkey已经注册';
            case '4023':
                return '服务器网络异常';
            case '4024':
                return '非法访问';
            case '4025':
                return 'device-token全部失败';
            case '4026':
                return 'device-token部分失败';
            case '4027':
                return '拉取文件失败';
            case '5000':
                return 'device_token错误';
            case '5001':
                return '证书不存在';
            case '5002':
                return 'p,d是umeng保留字段';
            case '5003':
                return 'alert字段不能为空';
            case '5004':
                return 'alert只能是String类型';
            case '5005':
                return 'device_token格式错误';
            case '5006':
                return '创建socket错误';
            case '5007':
                return 'certificate_revoked错误';
            case '5008':
                return 'certificate_unkown错误';
            case '5009':
                return 'handshake_failure错误';
        }
    }

}
