<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Yuepai Model
 *
 * @method \App\Model\Entity\Cost get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cost newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cost[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cost|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cost patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cost[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cost findOrCreate($search, callable $callback = null)
 */
class YuepaiTable extends Table
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

        $this->table('lm_yuepai');
        $this->displayField('act_time');
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
            ->numeric('rest_num')
            ->requirePresence('rest_num', 'create')
            ->notEmpty('rest_num');

        $validator
            ->numeric('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->dateTime('act_time')
            ->requirePresence('act_time', 'create')
            ->notEmpty('act_time');
        return $validator;
    }
}
