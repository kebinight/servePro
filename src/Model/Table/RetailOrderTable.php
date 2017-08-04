<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RetailOrder Model
 *
 * @method \App\Model\Entity\RetailOrder get($primaryKey, $options = [])
 * @method \App\Model\Entity\RetailOrder newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RetailOrder[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RetailOrder|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RetailOrder patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RetailOrder[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RetailOrder findOrCreate($search, callable $callback = null)
 */
class RetailOrderTable extends Table
{

    /**
     * Initialize method
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('lm_retail_order');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'create_time' => 'new',
                    'update_time' => 'always',
                ]
            ]
        ]);
    }


    /**
     * Default validation rules.
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
            ->integer('buyer_id')
            ->requirePresence('buyer_id', 'create')
            ->notEmpty('buyer_id');

        $validator
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        return $validator;
    }
}
