<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserSkill Entity
 *
 * @property int $id
 * @property int $skill_id
 * @property int $cost_id
 * @property string $desc
 * @property int $is_used
 * @property int $is_checked
 *
 * @property \App\Model\Entity\Skill $skill
 * @property \App\Model\Entity\Cost $cost
 */
class UserSkill extends Entity
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
