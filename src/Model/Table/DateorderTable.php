<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Dateorder Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Consumers
 * @property \Cake\ORM\Association\BelongsTo $Dates
 * @property \Cake\ORM\Association\BelongsTo $Daters
 * @property \Cake\ORM\Association\BelongsTo $UserSkills
 *
 * @method \App\Model\Entity\Dateorder get($primaryKey, $options = [])
 * @method \App\Model\Entity\Dateorder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Dateorder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Dateorder|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Dateorder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Dateorder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Dateorder findOrCreate($search, callable $callback = null)
 */
class DateorderTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_dateorder');
        $this->displayField('id');
        $this->primaryKey('id');

        //邀约者
        $this->belongsTo('Dater', [
            'className'=>'User',
            'foreignKey' => 'dater_id',
            'joinType' => 'INNER'
        ]);

        //赴约者
        $this->belongsTo('Buyer', [
            'className'=>'User',
            'foreignKey' => 'consumer_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('UserSkill', [
            'foreignKey' => 'user_skill_id',
        ]);

        $this->belongsTo('Date', [
            'foreignKey' => 'date_id',
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
                ->requirePresence('dater_name', 'create')
                ->notEmpty('dater_name');

        $validator
                ->requirePresence('consumer', 'create')
                ->notEmpty('consumer');

        $validator
                ->requirePresence('site', 'create')
                ->notEmpty('site');

        $validator
                ->numeric('site_lat')
                ->requirePresence('site_lat', 'create')
                ->notEmpty('site_lat');

        $validator
                ->numeric('site_lng')
                ->requirePresence('site_lng', 'create')
                ->notEmpty('site_lng');

        $validator
                ->numeric('price')
                ->requirePresence('price', 'create')
                ->notEmpty('price');

        $validator
                ->numeric('amount')
                ->requirePresence('amount', 'create')
                ->notEmpty('amount');


        $validator
                ->numeric('pre_pay')
                ->requirePresence('pre_pay', 'create')
                ->notEmpty('pre_pay');

        $validator
                ->numeric('pre_precent')
                ->requirePresence('pre_precent', 'create')
                ->notEmpty('pre_precent');

        $validator
                ->dateTime('start_time')
                ->requirePresence('start_time', 'create')
                ->notEmpty('start_time');

        $validator
                ->dateTime('end_time')
                ->requirePresence('end_time', 'create')
                ->notEmpty('end_time');

        $validator
                ->integer('date_time')
                ->requirePresence('date_time', 'create')
                ->notEmpty('date_time');

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
        $rules->add($rules->existsIn(['consumer_id'], 'Buyer'));
        //$rules->add($rules->existsIn(['dater_id'], 'Daters'));
//        $rules->add($rules->existsIn(['date_id'], 'User'));
        //$rules->add($rules->existsIn(['user_skill_id'], 'UserSkill'));
        //$rules->add($rules->existsIn(['date_id'], 'Date'));
        return $rules;
    }

}
