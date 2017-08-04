<?php
namespace Wpadmin\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Wpadmin\Model\Entity\Adminmsg;

/**
 * Adminmsg Model
 *
 */
class AdminmsgTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('lm_adminmsg');
        $this->displayField('id');
        $this->primaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('type')
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->requirePresence('msg', 'create')
            ->notEmpty('msg');

        $validator
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->dateTime('create_time')
            ->requirePresence('create_time', 'create')
            ->notEmpty('create_time');

        $validator
            ->dateTime('update_time')
            ->requirePresence('update_time', 'create')
            ->notEmpty('update_time');

        return $validator;
    }
}
