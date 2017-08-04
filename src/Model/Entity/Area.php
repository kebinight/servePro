<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Area Entity
 *
 * @property int $id
 * @property int $pid
 * @property string $name
 * @property bool $type
 * @property string $letter
 * @property bool $status
 * @property string $code
 * @property \Cake\I18n\Time $ctime
 * @property \Cake\I18n\Time $utime
 */
class Area extends Entity
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
