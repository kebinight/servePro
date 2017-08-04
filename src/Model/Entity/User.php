<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
/**
 * User Entity
 *
 * @property int $id
 * @property string $phone
 * @property string $pwd
 * @property string $user_token
 * @property string $union_id
 * @property string $wx_openid
 * @property string $app_wx_openid
 * @property string $truename
 * @property string $level
 * @property string $position
 * @property string $email
 * @property int $gender
 * @property string $city
 * @property string $avatar
 * @property float $money
 * @property float $bonus_point
 * @property int $is_normal
 * @property int $status
 * @property int $vp_status
 * @property float $recharge
 * @property float $consumed
 * @property bool $enabled
 * @property bool $is_del
 * @property string $device
 * @property \Cake\I18n\Time $create_time
 * @property \Cake\I18n\Time $update_time
 * @property string $guid
 *
 * @property \App\Model\Entity\Union $union
 */
class User extends Entity {

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
    protected $_hidden = ['pwd'];

    protected function _setPwd($password) {
        return (new DefaultPasswordHasher)->hash($password);
    }

}
