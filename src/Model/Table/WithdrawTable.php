<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Withdraw Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Admins
 *
 * @method \App\Model\Entity\Withdraw get($primaryKey, $options = [])
 * @method \App\Model\Entity\Withdraw newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Withdraw[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Withdraw|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Withdraw patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Withdraw[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Withdraw findOrCreate($search, callable $callback = null)
 */
class WithdrawTable extends Table
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

        $this->table('lm_withdraw');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('User', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'User'
        ]);
        /*$this->belongsTo('Admins', [
            'foreignKey' => 'admin_id',
            'className' => 'Admin'
        ]);*/
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
            ->numeric('amount')
            ->requirePresence('amount', 'create')
            ->notEmpty('amount');

        $validator
            ->requirePresence('cardno', 'create')
            ->notEmpty('cardno');

        $validator
            ->requirePresence('truename', 'create')
            ->notEmpty('truename');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'User'));
        //$rules->add($rules->existsIn(['admin_id'], 'Admins'));
        return $rules;
    }
}
