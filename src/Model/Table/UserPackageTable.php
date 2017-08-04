<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use MongoDB\BSON\Timestamp;

/**
 * UserPackage Model
 *
 * @method \App\Model\Entity\Date get($primaryKey, $options = [])
 * @method \App\Model\Entity\Date newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Date[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Date|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Date patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Date[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Date findOrCreate($search, callable $callback = null)
 */
class UserPackageTable extends Table
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
        $this->table('lm_user_package');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->hasMany('UsedPackages', [
            'className' => 'UsedPackage',
            'foreignKey' => 'package_id'
        ]);

        $this->belongsTo('User');
        
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'create_time' => 'new',
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
            ->numeric('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmpty('user_id');

        $validator
            ->numeric('package_id')
            ->requirePresence('package_id', 'create')
            ->notEmpty('package_id');

        $validator
            ->numeric('type')
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->numeric('chat_num')
            ->requirePresence('chat_num', 'create')
            ->notEmpty('chat_num');

        $validator
            ->numeric('rest_chat')
            ->requirePresence('rest_chat', 'create')
            ->notEmpty('rest_chat');

        $validator
            ->numeric('browse_num')
            ->requirePresence('browse_num', 'create')
            ->notEmpty('browse_num');

        $validator
            ->numeric('rest_browse')
            ->requirePresence('rest_browse', 'create')
            ->notEmpty('rest_browse');

        $validator
            ->numeric('vir_money')
            ->requirePresence('vir_money', 'create')
            ->notEmpty('vir_money');

        $validator
            ->numeric('cost')
            ->requirePresence('cost', 'create')
            ->notEmpty('cost');

        $validator
            ->numeric('vir_money')
            ->requirePresence('vir_money', 'create')
            ->notEmpty('vir_money');

        $validator
            ->dateTime('deadline')
            ->requirePresence('deadline', 'create')
            ->notEmpty('deadline');

        return $validator;
    }

}
