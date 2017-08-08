<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Slimit Entity
 *
 * @property int $id
 * @property int $parent_id
 * @property int $status
 * @property string $name
 * @property string $node
 * @property int $rank
 * @property string $remark
 * @property \Cake\I18n\Time $create_time
 * @property \Cake\I18n\Time $update_time
 *
 * @property \App\Model\Entity\Suser $admin
 */
class Slimit extends Entity
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
