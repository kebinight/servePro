<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use MongoDB\BSON\Timestamp;

/**
 * Package Model
 *
 * @method \App\Model\Entity\Date get($primaryKey, $options = [])
 * @method \App\Model\Entity\Date newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Date[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Date|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Date patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Date[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Date findOrCreate($search, callable $callback = null)
 */
class PackageTable extends Table
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

        $this->table('lm_package');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->hasMany('UserPackage', [
            'className' => 'UserPackage',
            'foreignKey' => 'package_id'
        ]);
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'create_time' => 'new',
                    'update_time' => 'always'
                ]
            ]
        ]);
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
            ->requirePresence('title', 'create')
            ->notEmpty('title');
        $validator
            ->requirePresence('type', 'create')
            ->notEmpty('type');
        $validator
            ->numeric('chat_num')
            ->requirePresence('chat_num', 'create')
            ->notEmpty('chat_num');
        $validator
            ->numeric('browse_num')
            ->requirePresence('browse_num', 'create')
            ->notEmpty('browse_num');
        $validator
            ->numeric('vir_money')
            ->requirePresence('vir_money', 'create')
            ->notEmpty('vir_money');
        $validator
            ->numeric('price')
            ->requirePresence('price', 'create')
            ->notEmpty('price');
        $validator
            ->numeric('vali_time')
            ->requirePresence('vali_time', 'create')
            ->notEmpty('vali_time');
        $validator
            ->numeric('stock');
        $validator
            ->numeric('act_dct');
        $validator
            ->numeric('act_send_num');
        return $validator;
    }

}
