<?php
namespace Wpadmin\Model\Entity;

use Cake\ORM\Entity;

/**
 * CwpMenu Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $node
 * @property int $pid
 * @property string $class
 * @property int $rank
 * @property bool $is_menu
 * @property bool $status
 * @property string $remark
 */
class Menu extends Entity
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
