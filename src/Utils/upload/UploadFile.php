<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace App\Utils\upload;

/**
 * 文件上传类
 * @author    liu21st <liu21st@gmail.com>
 * @edit      kebin <475583819@qq.com>
 */
class UploadFile {

    //非定制的配置
    private $pConfig = array(
    );

    //可定制的配置
    private $config = array(
        'maxSize' => -1,                             // 上传文件的最大值, 单位Byte
        'supportMulti' => true,                      // 是否支持多文件上传
        'allowExts' => array(),                      // 允许上传的文件后缀 留空不作后缀检查
        'allowTypes' => array(),                     // 允许上传的文件类型 留空不做检查
        'thumbEnable' => false,                      // 使用对上传图片进行缩略图处理
        'imageClassPath' => 'ORG.Util.Image',        // 图库类包路径
        'thumbWidth' => '',                       // 缩略图宽度，生成不同规格图片可以使用","分隔
        'thumbHeight' => '',                      // 缩略图高度，
        'thumbPrefix' => 'thumb_',                   // 缩略图前缀
        'thumbSuffix' => '',                         // 缩略图后缀
        'thumbPath' => '',                           // 缩略图保存路径
        'thumbFile' => '',                           // 缩略图文件名，
        'thumbExt' => '',                            // 缩略图扩展名
        'thumbRemoveOrigin' => false,                // 是否移除原图
        'smallPrefix' => 'small_',                   // 中图前缀
        'zipImages' => false,                        // 压缩图片文件上传
        'autoSub' => false,                          // 启用子目录保存文件
        'subType' => 'hash',                         // 子目录创建方式 可以使用hash date custom
        'subDir' => '',                              // 子目录名称 subType为custom方式后有效
        'dateFormat' => 'Ymd',
        'hashLevel' => 1,                            // hash的目录层次
        'savePath' => '',                            // 上传文件保存路径
        'autoCheck' => true,                         // 是否自动检查附件
        'uploadReplace' => false,                    // 存在同名是否覆盖
        'saveRule' => 'uniqid',                      // 上传文件命名规则,可以是函数名，也可以是
        'hashType' => 'md5_file',                    // 上传文件Hash规则函数名
    );

    // 错误信息
    private $error = '';
    // 额外错误信息，一般是空的，拥有更多详细说明的错误提示
    private $ohterError = [];

    // 上传成功的文件信息
    private $uploadFileInfo;


    public function __get($name) {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }


    public function __set($name, $value) {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }


    public function __isset($name)
    {
        return isset($this->config[$name]);
    }


    /**
     * 架构函数
     * @access public
     * @param array $config  上传参数
     */
    public function __construct($config = array())
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }


    /**
     * 将一个文件保存到另外一个目录
     * 单一功能，将上传的文件保存到指定路径
     * @param array $file
     * [
     *      'resourcePath' => '',        //存储路径，目录不存在则创建目录（允许多级目录的创建）
     *      'resourceName' => '',        //存储名称
     *      'extension'=> '',            //扩展名
     *      'tmp_name' => '',            //文件上传时原名
     * ]
     * @param string $value  数据表名
     * @return string
     */
    private function save($file) {
        if(empty($file['savePath'])) {
            $this->error('缺少文件保存路径');
        }
        if(empty($file['saveName'])) {
            $this->error('缺少文件保存名称');
        }
        $savePath = $file['savePath'];
        $filename = $savePath . $file['saveName'];

        if(!$this->checkDirectory($file)) {
            return false;
        }

        if (!$this->uploadReplace && is_file($filename)) {
            // 不覆盖同名文件
            $this->error = '文件已经存在！文件名为：[' . $filename . ']';
            return false;
        }

        //将上传的文件移到保存路径
        if (!move_uploaded_file($file['tmp_name'], $this->autoCharset($filename))) {
            $this->error = '文件上传保存失败！';
            return false;
        }
        return true;
    }


    /**
     * 生成多种规格缩略图
     * @param array $file
     * [
     *      'savePath'    => (必填)源文件存储路径（不包含文件名，以/结尾）
     *      'saveName'    => (必填)源文件存储文件名
     *      'extension'   => (必填)源文件扩展名
     *      'thumbWidth'  => (必填)缩略图宽度，多种规格使用逗号隔开
     *      'thumbHeight' => (选填)缩略图高度，多种规格使用逗号隔开，默认与thumbWidth等比收缩
     *      'thumbPrefix' => (选填)缩略图名称前缀
     *      'thumbSuffix' => (选填)缩略图名称后缀
     *      'thumbFile'   => (选填)缩略图名称，默认源文件名称
     *      'thumbPath'   => (选填)缩略图保存路径，以"/"结尾，默认与源文件相同
     *      'thumbExt'    => (选填)缩略图名称扩展名，默认与源文件相同
     * ]
     * @return
     */
    public function save2Thumbs($file)
    {
        //处理结果集
        $res = [];
        if(empty($file['savePath']) || empty($file['saveName'])) {
            $res[] = [
                'error' => '错误的参数[savePath=' . isset($file['savePath']) ? $file['savePath'] : 'null' .
                    ';' . 'saveName=' . isset($file['saveName']) ? $file['saveName'] : 'null]'
            ];
        }
        //支持gif/jpg/bmp/png格式的图片生成缩略图
        $filename = $file['savePath'] . $file['saveName'];
        $image = getimagesize($filename);  //获取图像信息
        $file['extension'] = isset($file['extension']) ? $file['extension'] : $this->getImgExt($image[2]);
        if (in_array(strtolower($file['extension']), array('gif', 'jpg', 'jpeg', 'bmp', 'png'))) {
            if (false !== $image) {
                //生成缩略图
                $thumbWidth = explode(',', isset($file['thumbWidth']) ? $file['thumbWidth'] : $this->thumbWidth);
                $thumbHeight = explode(',', isset($file['thumbHeight']) ? $file['thumbHeight'] : $this->thumbHeight);
                $thumbPrefix = explode(',', isset($file['thumbPrefix']) ? $file['thumbPrefix'] : $this->thumbPrefix);
                $thumbSuffix = explode(',', isset($file['thumbSuffix']) ? $file['thumbSuffix'] : $this->thumbSuffix);
                $thumbFile = explode(',', isset($file['thumbFile']) ? $file['thumbFile'] : $this->thumbFile);
                $thumbPath = isset($file['thumbPath']) ? $file['thumbPath'] : ($this->thumbPath ? $this->thumbPath : dirname($filename) . '/');
                $thumbExt = isset($file['thumbExt']) ? $file['thumbExt'] : ($this->thumbExt ? $this->thumbExt : $this->getImgExt($image[2]));  //自定义缩略图扩展名
                //支持生成不同规格的缩略图
                if($thumbWidth && count($thumbWidth)) {
                    for ($i = 0, $len = count($thumbWidth); $i < $len; $i++) {
                        if (!empty($thumbFile[$i])) {
                            $thumbname = $thumbFile[$i];
                        } else {
                            //没有指定图片名称则使用相应规则生成默认名称
                            $prefix = isset($thumbPrefix[$i]) ? $thumbPrefix[$i] : $thumbPrefix[0];
                            $suffix = isset($thumbSuffix[$i]) ? $thumbSuffix[$i] : $thumbSuffix[0];
                            //获取不带有扩展名的文件名
                            $basename =  basename($filename, '.' . $file['extension']);
                            $thumbname = $prefix . $basename . $suffix;
                        }
                        $thumbname .= ($i > 0) ? '_' . $i : '';
                        $thumbWidthTmp = $thumbWidth[$i];
                        //缩略图宽度最少1像素
                        if($thumbWidthTmp < 1) {
                            $res[] = [
                                'error' => '错误的参数[thumbWidth=' . $thumbWidthTmp . ']'
                            ];
                            continue;
                        }
                        $thumbHeightTmp = !empty($thumbHeight[$i]) ? $thumbHeight[$i] : intval($thumbWidthTmp * $image[0] / $image[1]);
                        $savePathTmp = $thumbPath . $thumbname . '.' . $thumbExt;

                        if(!$this->checkDirectory(['savePath' => $thumbPath])) {
                            return false;
                        }
                        /**
                         *****************************************************************
                         * 这里的图片处理需要使用到第三方插件，这里使用的是php开源的图片处理库intervention/image
                         *****************************************************************
                         */
                        try {
                            \Intervention\Image\ImageManagerStatic::make($filename)
                                ->resize(intval($thumbWidthTmp), intval($thumbHeightTmp))
                                ->save($savePathTmp);
                            $res[] = [
                                'width' => $thumbWidthTmp,
                                'height' => $thumbHeightTmp,
                                'savePath' => $thumbPath,
                                'fileName' => $thumbname . '.' . $thumbExt
                            ];
                        } catch (\Intervention\Image\Exception\NotReadableException $e) {
                            $res[] = [
                                'error' => '源文件无法读取[' . $filename . ']'
                            ];
                        } catch (\Intervention\Image\Exception\NotWritableException $e) {
                            $res = [
                                'error' => '保存失败（可能原因：保存路径有误）[' . $savePathTmp. ']'
                            ];
                        }
                    }

                    if ($this->thumbRemoveOrigin) {
                        // 生成缩略图之后删除原图
                        unlink($filename);
                    }
                } else {
                    $res[] = [
                        'error' => '缺少参数[thumbWidth]'
                    ];
                }
            } else {
                $res[] = [
                    'error' => '非图片格式无法生成缩略图'
                ];
            }
        } else {
            $res[] = [
                'error' => "目前仅支持'gif', 'jpg', 'jpeg', 'bmp', 'png'格式的图片生成缩略图"
            ];
        }
        return $res;
    }


    /**
     * 根据图片类型获取扩展名
     * getimagesize函数获取到的图片类型
     * @param $type
     * @return mixed|string
     */
    private function getImgExt($type)
    {
        $types = [1 => 'gif', 2 => 'jpg', 3 => 'png', 4 => 'swf', 5 => 'psd', 6 => 'bmp'];
        return isset($types[$type]) ? $types[$type] : '';
    }


    /**
     * 上传所有文件
     * @access public
     * @param string $savePath  上传文件保存路径
     * @param array $config 额外配置文件,可进行额外扩展
     * [
     *      'thumbEnable' => true,            //保存并生成缩略图
     *      'thumbWidth' => '30, 40, 50',     //缩略图宽度,多个规格可以使用逗号隔开
     *      'thumbHeight' => '30, 40, 50',    //缩略图高度，根据thumbWidth对应生成缩略图，若宽度有多个而高度没有对应尺寸则会进行等比计算生成相应高度
     *      'thumbPrefix' => （选填）缩略图名称前缀
     *      'thumbSuffix' => （选填）缩略图名称后缀
     *      'thumbFile' => （选填）缩略图名称，默认源文件名称
     *      'thumbPath' => （选填）缩略图保存路径，以"/"结尾，默认与源文件相同
     *      'thumbExt' => （选填）缩略图名称扩展名，默认与源文件相同
     * ]
     * @return boolean
     */
    public function upload($savePath = '', $config = []) {
        //如果不指定保存文件名，则由系统默认
        if (empty($savePath))
            $savePath = $this->savePath;
        $fileInfo = array();
        $isUpload = false;

        // 获取上传的文件信息
        // 对$_FILES数组信息处理
        $files = $this->dealFiles($_FILES, (isset($config['key']) ? $config['key'] : null));
        foreach ($files as $key => $file) {
            //过滤无效的上传
            if (!empty($file['name'])) {
                //登记上传文件的扩展信息
                if (!isset($file['key']))
                    $file['key'] = $key;
                $file['extension'] = $this->getExt($file['name']);
                $file['savePath'] = $savePath;
                $file['saveName'] = $this->getSaveName($file);

                // 自动检查附件
                if ($this->autoCheck) {
                    if (!$this->check($file))
                        return false;
                }

                //保存上传文件
                if (!$this->save($file))
                    return false;
                if (function_exists($this->hashType)) {
                    $fun = $this->hashType;
                    $file['hash'] = $fun($this->autoCharset($file['savePath'] . $file['saveName']));
                }

                //生成缩略图
                if(!empty($config) && $config['thumbEnable'] || $this->thumbEnable) {
                    $file = array_merge($file, $config);
                    $res = $this->save2Thumbs($file);
                    if(!empty($res) && isset($res[0]['error']) && $res && isset($config['thumbNeeded']) && $config['thumbNeeded']) {
                        $this->error = '缩略图生成失败';
                        $this->ohterError = $res;
                        unlink($file['savePath'] . $file['saveName']);
                        return false;
                    };
                    $file['thumbSaveResult'] = $res;
                }
                //上传成功后保存文件信息，供其他地方调用
                unset($file['tmp_name'], $file['error']);
                $fileInfo[] = $file;
                $isUpload = true;
            }
        }
        if ($isUpload) {
            $this->uploadFileInfo = $fileInfo;
            return true;
        } else {
            $this->error = '没有选择上传文件';
            return false;
        }
    }


    /**
     * 转换上传文件数组变量为正确的方式
     * @access private
     * @param array $files  上传的文件变量
     * @param string $getKey 要选择上传的key关键字，对应前端的name属性
     * @return array
     */
    public function dealFiles($files, $getKey = null) {
        $fileArray = array();
        $n = 0;
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    if((!$getKey || $getKey == $key)) {
                        $fileArray[$n]['key'] = $key;
                        foreach ($keys as $_key) {
                            $fileArray[$n][$_key] = $file[$_key][$i];
                        }
                        $n++;
                    }
                }
            } else {
                $fileArray[$key] = $file;
            }
        }
        return $fileArray;
    }


    /**
     * 获取错误代码信息
     * @access public
     * @param string $errorNo  错误号码
     * @return void
     */
    protected function error($errorNo) {
        switch ($errorNo) {
            case 1:
                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
                break;
            case 2:
                $this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
                break;
            case 3:
                $this->error = '文件只有部分被上传';
                break;
            case 4:
                $this->error = '没有文件被上传';
                break;
            case 6:
                $this->error = '找不到临时文件夹';
                break;
            case 7:
                $this->error = '文件写入失败';
                break;
            default:
                $this->error = '未知上传错误！';
        }
        return;
    }


    /**
     * 根据上传文件命名规则取得保存文件名
     * @access private
     * @param string $filename 数据
     * @return string
     */
    private function getSaveName($fileinfo) {
        $rule = $this->saveRule;
        if (empty($rule)) {//没有定义命名规则，则保持文件名不变
            $saveName = time() . '_' . $fileinfo['name'];
        } else {
            if (function_exists($rule)) {
                //使用函数生成一个唯一文件标识号
                if($rule == 'uniqid') {
                    $saveName = $rule(isset($fileinfo['prefix']) ? $fileinfo['prefix'] : '', true) . "." . $fileinfo['extension'];
                } else {
                    $saveName = $rule() . "." . $fileinfo['extension'];
                }
            } else {
                //使用给定的文件名作为标识号
                $saveName = $rule . '_' . microtime() . "." . $fileinfo['extension'];
            }
        }
        if ($this->autoSub) {
            // 使用子目录保存文件
            $filename['savename'] = $saveName;
            $saveName = $this->getSubName($filename) . $saveName;
        }
        return $saveName;
    }


    /**
     * 获取子目录的名称
     * @access private
     * @param array $file  上传的文件信息
     * @return string
     */
    private function getSubName($file) {
        switch ($this->subType) {
            case 'custom':
                $dir = $this->subDir;
                break;
            case 'date':
                $dir = date($this->dateFormat, time()) . '/';
                break;
            case 'hash':
            default:
                $name = md5($file['savename']);
                $dir = '';
                for ($i = 0; $i < $this->hashLevel; $i++) {
                    $dir .= $name{$i} . '/';
                }
                break;
        }
        if (!is_dir($file['savepath'] . $dir)) {
            mkdir($file['savepath'] . $dir, 0777, true);
        }
        return $dir;
    }


    /**
     * 检查上传的文件
     * @access private
     * @param array $file 文件信息
     * @return boolean
     */
    private function check($file) {
        if ($file['error'] !== 0) {
            //文件上传失败
            //捕获错误代码
            $this->error($file['error']);
            return false;
        }
        //文件上传成功，进行自定义规则检查
        //检查文件大小
        if (!$this->checkSize($file['size'])) {
            $this->error = '上传文件大小不符！';
            return false;
        }

        //检查文件Mime类型
        if (!$this->checkType($file['type'])) {
            $this->error = '上传文件MIME类型不允许！';
            return false;
        }
        //检查文件类型
        if (!$this->checkExt($file['extension'])) {
            $this->error = '上传文件类型不允许';
            return false;
        }

        //检查是否合法上传
        if (!$this->checkUpload($file['tmp_name'])) {
            $this->error = '非法上传文件！';
            return false;
        }
        return true;
    }


    // 自动转换字符集 支持数组转换
    private function  autoCharset($fContents, $from = 'gbk', $to = 'utf-8') {
        $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
        $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
        if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
            //如果编码相同或者非字符串标量则不转换
            return $fContents;
        }
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    }


    /**
     * 检查上传的文件类型是否合法
     * @access private
     * @param string $type 数据
     * @return boolean
     */
    private function checkType($type) {
        if (!empty($this->allowTypes))
            return in_array(strtolower($type), $this->allowTypes);
        return true;
    }

    /**
     * 检查上传的文件后缀是否合法
     * @access private
     * @param string $ext 后缀名
     * @return boolean
     */
    private function checkExt($ext) {
        if (!empty($this->allowExts))
            return in_array(strtolower($ext), $this->allowExts, true);
        return true;
    }

    /**
     * 检查文件大小是否合法
     * @access private
     * @param integer $size 数据
     * @return boolean
     */
    private function checkSize($size) {
        return !($size > $this->maxSize) || (-1 == $this->maxSize);
    }

    /**
     * 检查文件是否非法提交
     * @access private
     * @param string $filename 文件名
     * @return boolean
     */
    private function checkUpload($filename) {
        return is_uploaded_file($filename);
    }

    /**
     * 取得上传文件的后缀
     * @access private
     * @param string $filename 文件名
     * @return boolean
     */
    private function getExt($filename) {
        $pathinfo = pathinfo($filename);
        return $pathinfo['extension'];
    }


    /**
     * 取得上传文件的信息
     * @access public
     * @return array
     */
    public function getUploadFileInfo() {
        return $this->uploadFileInfo;
    }


    /**
     * 取得主操作错误信息
     * 主操作定义：
     * 举个栗子：上传文件操作，将源文件保存起来是主操作，如果生成缩略图是非必要的操作，则为额外操作。
     * @access public
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->error;
    }


    /**
     * 取得额外操作错误信息
     * 主操作定义：
     * 举个栗子：上传文件操作，将源文件保存起来是主操作，如果生成缩略图是非必要的操作，则为额外操作。
     * @access public
     * @return string
     */
    public function getOtherErrMsg()
    {
        return $this->ohterError;
    }


    /**
     * 检查目录是否存在
     * 不存在的默认自动创建目录
     * @param $file
     * @return bool
     */
    public function checkDirectory($file)
    {
        $savePath = isset($file['savePath']) ? $file['savePath'] : '';
        $autoBuild = isset($file['autoBuild']) ? $file['autoBuild'] : true;
        // 检查上传目录
        if (!is_dir($savePath)) {
            // 检查目录是否编码后的
            if (is_dir(base64_decode($savePath))) {
                $savePath = base64_decode($savePath);
            } else {
                // 尝试创建目录,允许创建多级目录
                if (!mkdir($savePath, 0777, $autoBuild)) {
                    $this->error = '上传目录' . $savePath . '不存在';
                    return false;
                }
            }
        } else {
            if (!is_writeable($savePath)) {
                $this->error = '上传目录' . $savePath . '不可写';
                return false;
            }
        }
        return true;
    }
}
