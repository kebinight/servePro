<?php
namespace Wpadmin\Model\Entity;

use Cake\ORM\Entity;

/**
 * GroupMenu Entity.
 *
 * @property int $id
 * @property int $group_id
 * @property \Admin\Model\Entity\Group $group
 * @property int $menu_id
 * @property \Admin\Model\Entity\Menu $menu
 */
class GroupMenu extends Entity
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
