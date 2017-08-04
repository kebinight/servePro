<?php

namespace Wpadmin\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;

/**
 * CwpAdmin Entity.
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property bool $enabled
 * @property \Cake\I18n\Time $ctime
 * @property \Cake\I18n\Time $utime
 * @property \Cake\I18n\Time $login_time
 * @property string $login_ip
 */
class Admin extends Entity {

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
        'id' => false,
    ];
    
    protected $_hidden = ['password'];

    protected function _setPassword($password) {
        return (new DefaultPasswordHasher)->hash($password);
    }

    protected function _geGroups() {
    }

    
}
