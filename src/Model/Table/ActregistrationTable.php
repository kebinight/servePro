<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Actregistration Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Activities
 *
 * @method \App\Model\Entity\Actregistration get($primaryKey, $options = [])
 * @method \App\Model\Entity\Actregistration newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Actregistration[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Actregistration|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Actregistration patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Actregistration[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Actregistration findOrCreate($search, callable $callback = null)
 */
class ActregistrationTable extends Table
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

        $this->table('lm_actregistration');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('User', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Activity', [
            'foreignKey' => 'activity_id',
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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->numeric('cost')
            ->requirePresence('cost', 'create')
            ->notEmpty('cost');

        $validator
            ->numeric('punish')
            ->requirePresence('punish', 'create')
            ->notEmpty('punish');

        $validator
            ->numeric('punish_percent')
            ->requirePresence('punish_percent', 'create')
            ->notEmpty('punish_percent');
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
        $rules->add($rules->existsIn(['activity_id'], 'Activity'));
        return $rules;
    }
}
