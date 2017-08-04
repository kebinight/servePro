<?php

namespace App\Controller\Mobile;

use App\Controller\Admin\SettingController;
use App\Controller\Component\BusinessComponent;
use App\Controller\Mobile\AppController;
use App\Model\Entity\Flow;
use App\Pack\Aitask;
use App\Pack\Netim;
use Cake\Database\Schema\Table;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use CarouselPosition;
use CheckStatus;
use GlobalCode;
use Overtrue\Pinyin\Pinyin;
use PackType;
use PayOrderType;
use ServiceType;
use UserStatus;

/**
 * Index Controller
 *
 * @property \App\Model\Table\IndexTable $Index
 * @property \App\Controller\Component\SmsComponent $Sms
 * @property \App\Controller\Component\WxComponent $Wx
 * @property \App\Controller\Component\EncryptComponent $Encrypt
 * @property \App\Controller\Component\PushComponent $Push
 * @property \App\Controller\Component\BdmapComponent $Bdmap
 * @property \App\Controller\Component\BusinessComponent $Business
 */
class IndexController extends AppController {

    public function index() {
        $this->Common->dealReturn(true, 'hello, here is mobile');
    }
}
