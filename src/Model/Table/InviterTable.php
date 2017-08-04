<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Inviter Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Inviter
 * @property \Cake\ORM\Association\BelongsTo $Invited
 *
 * @method \App\Model\Entity\Inviter get($primaryKey, $options = [])
 * @method \App\Model\Entity\Inviter newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Inviter[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Inviter|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Inviter patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Inviter[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Inviter findOrCreate($search, callable $callback = null)
 */
class InviterTable extends Table
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
        $this->table('lm_invitation');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->belongsTo('Invitor', [
            'foreignKey' => 'inviter_id',
            'joinType' => 'INNER',
            'className' => 'User'
        ]);
        $this->belongsTo('Invited', [
            'foreignKey' => 'invited_id',
            'joinType' => 'INNER',
            'className' => 'User'
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
            ->integer('inviter_id')
            ->requirePresence('inviter_id');

        $validator
            ->integer('invited_id')
            ->requirePresence('invited_id');
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
        return $rules;
    }
}
