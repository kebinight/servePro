<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Payorder Entity
 *
 * @property int $id
 * @property int $type
 * @property int $relate_id
 * @property int $user_id
 * @property int $seller_id
 * @property string $title
 * @property string $order_no
 * @property string $out_trade_no
 * @property int $paytype
 * @property float $price
 * @property float $fee
 * @property string $remark
 * @property int $status
 * @property \Cake\I18n\Time $create_time
 * @property \Cake\I18n\Time $update_time
 *
 * @property \App\Model\Entity\Relate $relate
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Seller $seller
 */
class Payorder extends Entity
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
