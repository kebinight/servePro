<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Date Entity
 *
 * @property int $id
 * @property int $skill_id
 * @property string $title
 * @property \Cake\I18n\Time $start_time
 * @property \Cake\I18n\Time $end_time
 * @property string $site
 * @property float $price
 * @property string $description
 * @property int $status
 * @property int $user_id
 * @property \Cake\I18n\Time $created_time
 *
 * @property \App\Model\Entity\UserSkill $userSkill
 * @property \App\Model\Entity\User $userw
 */
class Date extends Entity
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
