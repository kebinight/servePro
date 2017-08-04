<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserPackage Entity
 *
 * @property int $id
 * @property string $title
 * @property int $user_id
 * @property int $package_id
 * @property int $chat_num
 * @property int $rest_chat
 * @property int $browse_num
 * @property int $rest_browse
 * @property int $type
 * @property float $cost
 * @property float $vir_money
 * @property \Cake\I18n\Time $deadline
 * @property \Cake\I18n\Time $create_time
 */
class UserPackage extends Entity
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
