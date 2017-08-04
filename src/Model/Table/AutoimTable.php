<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Autoim Model
 *
 * @method \App\Model\Entity\Autoim get($primaryKey, $options = [])
 * @method \App\Model\Entity\Autoim newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Autoim[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Autoim|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Autoim patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Autoim[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Autoim findOrCreate($search, callable $callback = null)
 */
class AutoimTable extends Table
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
        $this->table('lm_autoim');
        $this->displayField('body');
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
            ->requirePresence('body', 'create')
            ->notEmpty('body');

        return $validator;
    }
}
