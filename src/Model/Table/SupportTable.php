<?php
namespace App\Model\Table;

use Cake\Datasource\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Costs Model
 *
 * @method \App\Model\Entity\Support get($primaryKey, $options = [])
 * @method \App\Model\Entity\Support newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Support[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Support|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Support patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Support[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Support findOrCreate($search, callable $callback = null)
 */
class SupportTable extends Table
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
        $this->table('lm_support');
        $this->primaryKey('id');

        $this->belongsTo('Supporter', [
            'className' => 'User',
            'foreignKey' => 'supporter_id'
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
            ->integer('supporter_id')
            ->notEmpty('supporter_id');

        $validator
            ->integer('supported_id')
            ->notEmpty('supported_id');

        return $validator;
    }

}
