<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Activity Entity
 *
 * @property int $id
 * @property string $big_img
 * @property string $title
 * @property float $male_price
 * @property float $female_price
 * @property string $description
 * @property \Cake\I18n\Time $start_time
 * @property \Cake\I18n\Time $end_time
 * @property string $site
 * @property float $site_lat
 * @property float $site_lng
 * @property int $male_lim
 * @property int $type
 * @property int $female_lim
 * @property int $male_rest
 * @property int $female_rest
 * @property string $detail
 * @property string $notice
 * @property int $status
 * @property string $remark
 * @property int $cancelday
 */
class Activity extends Entity
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
