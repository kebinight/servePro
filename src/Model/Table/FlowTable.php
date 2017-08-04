<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Flow Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Buyers
 * @property \Cake\ORM\Association\BelongsTo $Relates
 *
 * @method \App\Model\Entity\Flow get($primaryKey, $options = [])
 * @method \App\Model\Entity\Flow newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Flow[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Flow|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Flow patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Flow[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Flow findOrCreate($search, callable $callback = null)
 */
class FlowTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_flow');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('User', [
            'className' => 'User',
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Buyer', [
            'className' => 'User',
            'foreignKey' => 'buyer_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Relates', [
            'foreignKey' => 'relate_id',
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
                ->requirePresence('type_msg', 'create')
                ->notEmpty('type_msg');

        $validator
                ->integer('income')
                ->requirePresence('income', 'create')
                ->notEmpty('income');

        $validator
                ->decimal('amount')
                ->requirePresence('amount', 'create')
                ->notEmpty('amount');

        $validator
                ->decimal('price')
                ->requirePresence('price', 'create')
                ->notEmpty('price');

        $validator
                ->decimal('pre_amount')
                ->requirePresence('pre_amount', 'create')
                ->notEmpty('pre_amount');

        $validator
                ->decimal('after_amount')
                ->requirePresence('after_amount', 'create')
                ->notEmpty('after_amount');

        $validator
                ->integer('paytype')
                ->requirePresence('paytype', 'create')
                ->notEmpty('paytype');


        $validator
                ->requirePresence('remark', 'create')
                ->notEmpty('remark');


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
          // $rules->add($rules->existsIn(['buyer_id'], 'User'));
//        $rules->add($rules->existsIn(['relate_id'], 'Relates'));

        return $rules;
    }

}
