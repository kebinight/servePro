<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Wxorder Model
 *
 * @method \App\Model\Entity\Wxorder get($primaryKey, $options = [])
 * @method \App\Model\Entity\Wxorder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Wxorder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Wxorder|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Wxorder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Wxorder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Wxorder findOrCreate($search, callable $callback = null)
 */
class WxorderTable extends Table
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

        $this->table('lm_wxorder');
        $this->primaryKey('id');

        $this->belongsTo('Wxer', [
            'className'=>'User',
            'foreignKey' => 'wxer_id',
            'joinType' => 'INNER'
        ]);

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
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmpty('user_id');

        $validator
            ->integer('wxer_id')
            ->requirePresence('wxer_id', 'create')
            ->notEmpty('wxer_id');

        $validator
            ->requirePresence('anhao', 'create')
            ->notEmpty('anhao');

        return $validator;
    }
}
