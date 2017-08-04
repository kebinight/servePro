<?php
namespace Wpadmin\Model\Entity;

use Cake\ORM\Entity;

/**
 * CwpGroup Entity.
 *
 * @property int $Id
 * @property string $name
 * @property string $remark
 * @property \Cake\I18n\Time $ctime
 * @property \Cake\I18n\Time $utime
 */
class Group extends Entity
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
        'id' => false,
    ];
}
