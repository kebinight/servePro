<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use App\Utils\umeng\Umeng;

/**
 * Push component  推送组件
 */
class PushComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    protected $key;
    protected $secret;
    protected $Umeng; 

    public function initialize(array $config) {
        parent::initialize($config);
        $android_conf = \Cake\Core\Configure::read('umeng_android');
        $ios_conf = \Cake\Core\Configure::read('umeng_ios');
        $this->android_key = $android_conf['AppKey'];
        $this->android_secret = $android_conf['AppMasterSecret'];
        $this->ios_key = $ios_conf['AppKey'];
        $this->ios_secret = $ios_conf['AppMasterSecret'];
    }

    /**
     * 广播(低于200个app集成为未上线无限制，上线之后每天不超过3次)
     * @param string $title 广播标题
     * @param string $content 广播内容
     * @param string $ticker 通知栏提示文字
     * @param boolean $production_mode 是否生产环境，true为生产环境，false为测试环境
     * @param string $extra 用户自定义key-value。只对"通知(display_type=notification)"生效。可以配合通知到达后, 打开App, 打开URL, 打开Activity使用。
     * @param string $after_open 之后打开，值为"go_app", "go_url", "go_activity", "go_custom",
     *                                           "go_app": 打开应用
     *                                           "go_url": 跳转到URL
     *                                           "go_activity": 打开特定的activity
     *                                           "go_custom": 用户自定义内容。
     * @param string $expire_time 消息过期时间,其值不可小于发送时间,默认为3天后过期。格式: "YYYY-MM-DD hh:mm:ss"。
     * @param int $badge ios消息数量提示
     * @@param string $sound ios声音
     * @return boolean true:发送成功;false:发送失败
     */
    public function sendAll($title, $content, $ticker, $production_mode = 'true', $extra = '', $after_open = '', $expire_time = '', $badge = '', $sound = ''){
        $umngObj = new Umeng($this->android_key, $this->android_secret, $this->ios_key, $this->ios_secret);
        $res = $umngObj->sendAll($title, $content, $ticker, $production_mode, $extra, $expire_time, $badge, $after_open, $sound);
        return $res;
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
    public function sendAlias($alias, $title, $content, $ticker, $alias_type = 'MY', $production_mode = 'true', $extra = '', $after_open = '', $expire_time = '', $badge = '', $sound = ''){
        $umngObj = new Umeng($this->android_key, $this->android_secret, $this->ios_key, $this->ios_secret);
        $res = $umngObj->sendAlias($alias, $title, $content, $ticker, $alias_type, $production_mode, $extra, $expire_time, $badge, $after_open, $sound);
        return $res;
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
    public function sendFile($title, $content, $ticker, $file, $alias_type = 'MY', $production_mode = 'true', $extra = '', $badge = '', $after_open = '', $sound = ''){
        $umngObj = new Umeng($this->android_key, $this->android_secret, $this->ios_key, $this->ios_secret);
        $res = $umngObj->sendFile($title, $content, $ticker, $file, $alias_type, $production_mode, $extra, $badge, $after_open, $sound);
        return $res;
    }
    
    /**
     * 安卓查询状态
     * @param int $task_id
     */
    public function android_check($task_id){
        $umngObj = new Umeng($this->android_key, $this->android_secret, $this->ios_key, $this->ios_secret);
        $res = $umngObj->android_check($task_id);
        return $res;
    }
    
    /**
     * ios查询状态
     * @param int $task_id
     */
    public function ios_check($task_id){
        $umngObj = new Umeng($this->android_key, $this->android_secret, $this->ios_key, $this->ios_secret);
        $res = $umngObj->ios_check($task_id);
        return $res;
    }
    
}
