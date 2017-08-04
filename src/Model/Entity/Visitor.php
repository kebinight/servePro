<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Visitor Entity
 *
 * @property int $id
 * @property int $visitor_id
 * @property int $visited_id
 * @property \Cake\I18n\Time $create_time
 * @property \Cake\I18n\Time $update_time
 *
 * @property \App\Model\Entity\User $visitor
 * @property \App\Model\Entity\User $visited
 */
class Visitor extends Entity
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
