<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Actregistration Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $activity_id
 * @property int $status
 * @property float $cost
 * @property float $punish
 * @property float $punish_percent
 * @property \Cake\I18n\Time $create_time
 * @property \Cake\I18n\Time $cancel_time
 * @property \Cake\I18n\Time $update_time
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Activity $activity
 */
class Actregistration extends Entity
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
