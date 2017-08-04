<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Kefu Entity
 *
 * @property int $id
 * @property int $job_num
 * @property int $relate_id
 * @property int $is_enable
 * @property int $is_del
 * @property String $nick
 * @property String $avatar
 * @property String $grants
 * @property String $im_accid
 * @property String $im_token
 * @property \DateTime $create_time
 * @property \DateTime $update_time
 */
class Kefu extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
