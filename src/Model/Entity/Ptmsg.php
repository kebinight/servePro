<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Ptmsg Entity
 * 平台消息
 * @property int $id
 * @property int $msg_type
 * @property {String} towho
 * @property {String} title
 * @property {String} body
 * @property {String} to_url
 * @property {DateTime} create_time
 * @property {DateTime} update_time
 */
class Ptmsg extends Entity
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
