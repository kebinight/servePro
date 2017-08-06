<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SRoleLimit Entity
 *
 * @property int $id
 * @property \Cake\I18n\Time $create_time
 *
 * @property \App\Model\Entity\Suser $admin
 * @property \App\Model\Entity\Srole $role
 * @property \App\Model\Entity\Slimit $slimit
 */
class SRoleLimit extends Entity
{

}
