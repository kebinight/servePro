<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserFans Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Followings
 *
 * @method \App\Model\Entity\UserFan get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserFan newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserFan[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserFan|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserFan patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserFan[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserFan findOrCreate($search, callable $callback = null)
 */
class UserFansTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_user_fans');
        $this->displayField('id');
        $this->primaryKey('id');
        //粉丝
        $this->belongsTo('User', [
            'className' => 'User',
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        //关注
        $this->belongsTo('Follower', [
            'className' => 'User',
            'foreignKey' => 'following_id',
            'joinType' => 'INNER'
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
    public function validationDefault(Validator $validator) {
        $validator
                ->integer('id')
                ->allowEmpty('id', 'create');

        $validator
                ->integer('type')
                ->requirePresence('type', 'create')
                ->notEmpty('type');

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

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->existsIn(['user_id'], 'User'));
        $rules->add($rules->existsIn(['following_id'], 'User'));

        return $rules;
    }

}
