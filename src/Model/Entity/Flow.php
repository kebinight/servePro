<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Flow Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $buyer_id
 * @property int $relate_id
 * @property int $type
 * @property string $type_msg
 * @property int $income
 * @property float $amount
 * @property float $price
 * @property float $pre_amount
 * @property float $after_amount
 * @property int $paytype
 * @property int $status
 * @property string $remark
 * @property \Cake\I18n\Time $create_time
 * @property \Cake\I18n\Time $update_time
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Buyer $buyer
 * @property \App\Model\Entity\Relate $relate
 */
class Flow extends Entity
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
