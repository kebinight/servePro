<?php
namespace Wpadmin\Model\Entity;

use Cake\ORM\Entity;

/**
 * Actionlog Entity
 *
 * @property int $id
 * @property string $url
 * @property string $type
 * @property string $useragent
 * @property string $ip
 * @property string $filename
 * @property string $msg
 * @property string $controller
 * @property string $action
 * @property string $param
 * @property string $user
 * @property \Cake\I18n\Time $create_time
 */
class Actionlog extends Entity
{

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
}
