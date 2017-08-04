<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Support Entity
 *
 * @property int $id
 * @property int $supporter_id
 * @property int $supported_id
 * @property \Cake\I18n\Time $create_time
 */
class Support extends Entity
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
