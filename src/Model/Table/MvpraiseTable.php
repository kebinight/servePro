<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Mvpraise Model
 *
 * @method \App\Model\Entity\Mvpraise get($primaryKey, $options = [])
 * @method \App\Model\Entity\Mvpraise newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Mvpraise[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Mvpraise|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Mvpraise patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Mvpraise[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Mvpraise findOrCreate($search, callable $callback = null)
 */
class MvpraiseTable extends Table
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

        $this->table('lm_mvpraise');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_time' => 'new',
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
            ->integer('movement_id')
            ->allowEmpty('movement_id', 'create')
            ->notEmpty('movement_id');

        $validator
            ->integer('user_id')
            ->allowEmpty('user_id', 'create')
            ->notEmpty('user_id');

        return $validator;
    }
}
