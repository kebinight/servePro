<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Srole Entity
 *
 * @property int $id
 * @property string $name
 * @property int $rank
 * @property int $status
 * @property string $remark
 * @property \Cake\I18n\Time $create_time
 * @property \Cake\I18n\Time $update_time
 *
 * @property \App\Model\Entity\Suser $admin
 */
class Srole extends Entity
{

}
