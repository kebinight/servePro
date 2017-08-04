<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Payorder Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Relates
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Sellers
 *
 * @method \App\Model\Entity\Payorder get($primaryKey, $options = [])
 * @method \App\Model\Entity\Payorder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Payorder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Payorder|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Payorder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Payorder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Payorder findOrCreate($search, callable $callback = null)
 */
class PayorderTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_payorder');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->belongsTo('Relates', [
            'foreignKey' => 'relate_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('User', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Seller', [
            'className' => 'User',
            'foreignKey' => 'seller_id',
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
                ->requirePresence('title', 'create')
                ->notEmpty('title');

        $validator
                ->requirePresence('order_no', 'create')
                ->notEmpty('order_no');

        $validator
                ->allowEmpty('out_trade_no');

        $validator
                ->integer('paytype')
                ->allowEmpty('paytype');

        $validator
                ->decimal('price')
                ->requirePresence('price', 'create')
                ->notEmpty('price');

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

        return $rules;
    }

}
