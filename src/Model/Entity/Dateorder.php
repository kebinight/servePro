<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Dateorder Entity
 *
 * @property int $id
 * @property int $consumer_id
 * @property string $dater_name
 * @property int $date_id
 * @property int $dater_id
 * @property int $user_skill_id
 * @property int $status
 * @property \Cake\I18n\Time $update_time
 * @property int $operate_status
 * @property string $site
 * @property float $site_lat
 * @property float $site_lng
 * @property float $price
 * @property float $count
 * @property int $is_complain
 * @property float $pre_pay
 * @property float $pre_precent
 * @property \Cake\I18n\Time $start_time
 * @property \Cake\I18n\Time $end_time
 * @property int $date_time
 * @property \Cake\I18n\Time $create_time
 *
 * @property \App\Model\Entity\Consumer $consumer
 * @property \App\Model\Entity\Date $date
 * @property \App\Model\Entity\Dater $dater
 * @property \App\Model\Entity\UserSkill $user_skill
 */
class Dateorder extends Entity
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
