<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2015-12-26 22:45:09 by allen <blog.rc5j.cn> , caowenpeng1990@126.com
 */
use Cake\I18n\Date;

/**
 * 生成指定长度的随机字符串
 * @param type $length
 * @param int $type 默认1数字字母混合，2纯数字，3纯字母
 * @return string
 */
function createRandomCode($length, $type = 1) {
    $randomCode = "";
    switch ($type) {
        case 1:
            $randomChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 2:
            $randomChars = '0123456789';
            break;
        case 3:
            $randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        default:
            break;
    }
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $randomChars { mt_rand(0, strlen($randomChars) - 1) };
    }
    return $randomCode;
}

/**
 * save 的验证错误信息
 * @param type $entity
 * @param type $msg
 * @return type
 */
function errorMsg($entity, $msg) {
    $errors = $entity->errors();
    $message = null;
    if (is_array($errors)) {
        foreach ($errors as $value) {
            foreach ($value as $val) {
                $message = $val;
                break;
            }
        }
    }
    if ($message) {
        Cake\Log\Log::error($errors, 'devlog');
    }
    return empty($message) ? $msg : $message;
}

/**
 *  获得原图
 * @param type $thumb
 * @return type
 */
function getOriginAvatar($thumb) {
    return preg_replace('/thumb_/', '', $thumb);
}

/**
 * 获40% 缩略图
 * @param type $thumb
 * @return type
 */
function getSmallAvatar($thumb) {
    $small = preg_replace('/thumb_/', 'small_', $thumb);
    if (!file_exists(WWW_ROOT . $small)) {
        return getOriginAvatar($thumb);
    } else {
        return $small;
    }
}

/**
 * 头像不存在或找不到时候设置为默认图
 * @param type $avatar
 * @return string
 */
function getAvatar($avatar) {
    if (preg_match('/(http|https).*/', $avatar)) {
        return $avatar;
    }
    if (empty($avatar) || !file_exists(WWW_ROOT . $avatar)) {
        return '/mobile/images/touxiang.jpg';
    } else {
        return $avatar;
    }
}

/**
 * @deprecated 已废弃
 * @param type $url
 */
function createImg($url) {
    return preg_replace('/upload/', 'imgs', $url);
}


/**
 * 
 * @param type $params
 */
function buildLinkString($params) {
    $string = '';
    foreach ($params as $key => $value) {
        $string .= $key . '="' . $value . '"&';
    }
    //去掉最后一个&字符
    $string = substr($string, 0, count($string) - 2);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }
    return $string;
}

/**
 * 计算2点间距离
 * @param type $coordinate1
 * @param type $coordinate2
 * @return 米  两点间距离
 * @throws Exception
 */
function getDistance($coordinate1, $lng, $lat) {
    $coordinate1_arr = explode(',', $coordinate1);
    if (!is_array($coordinate1_arr) || empty($coordinate1_arr)) {
        throw new Exception;
    } else {
        $lng1 = $coordinate1_arr[0];
        $lat1 = $coordinate1_arr[1];
    }
    $lng2 = $lng;
    $lat2 = $lat;
//    $earthRadius = 6367000; //approximate radius of earth in meters
    $earthRadius = 6371000; //百度地图用的参数
    /*
      Convert these degrees to radians
      to work with the formula
     */
    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
      Using the
      Haversine formula

      http://en.wikipedia.org/wiki/Haversine_formula

      calculate the distance
     */
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    $dis = round($calculatedDistance);
    if ($dis < 1000) {
        return $dis . 'm';
    } else {
        return round($dis / 1000, 1) . 'km';
    }
//     return round($calculatedDistance);
}

//仅适用于本项目对应数据库约会表start_time，end_time字段
//用户将开始时间和结束时间合成页面需要的格式
function getFormateDT($startTime, $endTime, $separator = '.') {
    $timestr = substr($startTime->year, 2)
            . $separator
            . $startTime->month
            . $separator
            . $startTime->day
            . " "
            . $startTime->hour
            . "点~"
            . substr($endTime->year, 2)
            . $separator
            . $endTime->month
            . $separator
            . $endTime->day
            . " "
            . $endTime->hour . '点';
    return $timestr;
}

//获取年月日
function getYMD($time) {

    $timestr = $time->year . '年' . $time->month . "月" . $time->day . '日';
    return $timestr;
}

//获取年月日
function getMD($time) {
    $timestr = $time->month . "月" . $time->day . "日";
    return $timestr;
}

//仅适用于本项目对应数据库约会表start_time，end_time字段
//用户将开始时间和结束时间合成页面需要的格式
function getHIS($startTime, $endTime) {
    $timestr = $startTime->hour . ":00~" . $endTime->hour . ":00";
    return $timestr;
}

//根据出生日期计算年龄
function getAge($birthday) {
    $currentday = new Date();
    if (!($birthday instanceof Date)) {
        return '--';
    }
    return ($currentday->year - $birthday->year);
}

/**
 * //根据开始时间，结束时间，单价计算总价和付费百分比计算价格
 * @param \Cake\I18n\Time $start_time
 * @param \Cake\I18n\Time $end_time
 * @param double $price
 * @param float $percent
 * @return float;
 */
function getCost(\Cake\I18n\Time $start_time, \Cake\I18n\Time $end_time, $price, $percent = 1.0) {
    return getLast($start_time, $end_time) * $price * $percent;
}

/**
 * //根据开始时间，结束时间，单价计算总价和付费百分比计算价格
 * @param \Cake\I18n\Time $start_time
 * @param \Cake\I18n\Time $end_time
 * @param double $price
 * @param float $percent
 * @return float;
 */
function getLast(\Cake\I18n\Time $start_time, \Cake\I18n\Time $end_time) {
    return ($end_time->timestamp - $start_time->timestamp) / 3600;
}

/**
 * 生成浮点随机数
 * @param int $min
 * @param int $max
 * @return float
 */
function randomFloat($min = 0, $max = 1) {
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}

/**
 * 对重要信息的数据库日志记录，例如订单漏单
 * @param string $flag 
 * @param string $msg
 * @param string $data
 */
function dblog($flag, $msg, $data = null) {
    $LogTable = \Cake\ORM\TableRegistry::get('Log');

    $log = $LogTable->newEntity();
    if ($data) {
        $log->data = var_export($data, true);
    }
    $log = $LogTable->patchEntity($log, [
        'flag' => $flag,
        'msg' => $msg
    ]);
    try {
        $LogTable->save($log);
    } catch (\Exception $exc) {
        Cake\Log\Log::error('devlog', $log->errors());
        Cake\Log\Log::error('devlog', $exc->getTraceAsString());
    }
}


/**
 * 对数据库查找出来的图片相对路径进行补充和处理
 * 作用：方便图片的路径管理以及相关操作
 * 图片绝对路径配置在/config/dataconf.php中的img
 * @param string $url
 * @param string $domain
 * @param bool $glide  //是否进行glide处理
 * @return string $url 处理后的可访问图片链接
 */
function generateImgUrl($url, $glide = true, $conf = [], $domain=null, $scheme = '')
{
    return transformUrl($url, $glide, $conf, true, $scheme, $domain);
}


/**
 * 转换URL
 * @param string $url 要转换的url
 * @param bool $glide 是否使用glide功能
 * @param array $glideConf glide功能配置
 * @param bool $isAbsolute 是否转成绝对路径
 * @param null $domain 是否使用此域名转换url
 * @param string $scheme 使用的协议，默认http
 */
function transformUrl($url, $glide = false, $glideConf = [], $isAbsolute = true, $scheme = '', $domain = null)
{
    if(!$url) {
        return '';
    }
    //转化成glide路径
    if($glide) {
        $url = preg_replace('/upload/', 'imgs', $url);
    }
    //添加glide参数
    if($glide && $glideConf) {
        $url .= (strpos($url, '?') ? '&' : '?');
        foreach ($glideConf as $key => $value) {
            $url .= $key . '=' . $value . '&';
        }
    }
    if(!$domain){
        $domain = env('HTTP_HOST');
    }
    //如果是全路径或不想转成全路径则不去做任何处理
    if(!$isAbsolute || checkUrlIsAbsoulte($url)) {
        return $url;
    } else {
        return ($scheme ? $scheme : getScheme()) . '://' . $domain . $url;
    }
}


function checkUrlIsAbsoulte($url)
{
    return preg_match('/(http|https|ftp):\/\//', $url);
}

/**
 *
 * 获取host地址
 * @param string $proto 使用的协议http/https等
 * @return string
 */
function getHost($proto = null) {
    //这个是为了测试环境而写，app比较多地方绑定了正式环境域名
    //如果返回的不是正式环境域名则有些功能会有异常
    /*if($this->request->env('SERVER_NAME') == 'm-my.smartlemon.cn') {
        return $this->request->scheme().'://m.beauty-engine.com';
    }*/
    //return ( $proto ? $proto : env('REQUEST_SCHEME')) . '://' . env('SERVER_NAME');
    if(!$proto) {
        $proto = getScheme();
    }
    return $proto . '://' . env('SERVER_NAME');
}


/**
 * 获取协议头
 * @return string
 */
function getScheme()
{
    $pro = env('SERVER_PROTOCOL');
    $proto = strtolower(substr_replace($pro, '', strpos($pro, '/')));
    return $proto;
}


/**
 * 将相对路径的uri补充成全路径url
 * @deprecated
 * (该方法已经被抛弃,请使用全局方法transformUrl:kebin@date:2017.05.19)
 */
function tranUrl4R2A($uri, $proto = null) {
    return getHost($proto) . $uri;
}


/**
 * 获取当前页面url
 * @return string
 */
function getCurUrl() {
    return getHost() . env('REQUEST_URI');
}


function getCity($str) {
    return preg_replace('/北京,|天津,|重庆,|上海,/', '', $str);
}


/**
 * appstore审核状态
 * @return int
 */
function getAppStoreCheckStatus()
{
    return 1;
}


/**
 * 用户没有开启GPS时默认使用的坐标值
 */
function getDefalutPosition()
{
    //lng经度,lat纬度
    $default = [0, 0];
    return $default;
}


/**
 * 判断是否是默认坐标值
 * @param $position
 * @return bool
 */
function isDefaultPosition($position)
{
    if(is_array($position) && $position[0] == 114.04376228 && ($position[1] == 22.64637076)) {
        return true;
    }
    return (getDefalutPosition() == $position);
}


/**
 * 开发时进行日志记录
 * 日志文件记录在/logs/tmpdev.log文件中
 */
function tmpLog($message)
{
    \Cake\Log\Log::debug($message, ['tmplog']);
}


/**
 * 运营过程中需要记录的日志
 * 记录在/logs/runlog.log中
 * @param string $operation 操作说明
 * @param string $descript 问题说明
 * @param string $relation 关联说明
 * @param $level 0/1/2/3 紧急等级
 * 等级0：安全问题记录日志
 * 等级1：问题日志出现
 * 等级2：代码出现问题导致程序出错
 */
function runLog($operation, $descript = '', $relation = '', $level = 1)
{
    $message = $operation . ($descript ? '|' . $descript : '') . ($relation ? '|' . $relation : '');
    \Cake\Log\Log::debug('|' . $level . '|' . $message, ['runlog']);
}


/**
 * 获取唯一的订单编号
 * 当前算法：
 * 1.type = date: 时间戳第二位开始+2位随机数
 * 2.type = pay: 时间戳+额外字符串+4位随机数
 */
function generateOid($type = 'date', $extstr = null)
{
    $oid = '';
    if($type == 'date') {
        $timestampstr = '' . time();
        $randstr = '' . rand(0, 99);
        $oid = substr($timestampstr, 1) . str_pad($randstr, 2, '0', STR_PAD_LEFT);
    } else if($type == 'pay') {
        $oid = time() . $extstr . createRandomCode(4, 1);
    }
    return $oid;
}


/**
 * @param string $configName
 * @param array $configs
 * @return string
 */
function getConfig($configName = '', $configs = [])
{
    return \Cake\Core\Configure::read($configName);
}


