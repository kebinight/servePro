<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UsedPackage Model
 *
 * @method \App\Model\Entity\Date get($primaryKey, $options = [])
 * @method \App\Model\Entity\Date newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Date[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Date|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Date patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Date[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Date findOrCreate($search, callable $callback = null)
 */
class UsedPackageTable extends Table
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

        $this->table('lm_used_package');
        $this->displayField('type');
        $this->primaryKey('id');

        $this->belongsTo('Used', [
            'className' => 'User',
            'foreignKey' => 'used_id'
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
            ->numeric('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmpty('user_id');

        $validator
            ->numeric('used_id')
            ->requirePresence('used_id', 'create')
            ->notEmpty('used_id');

        $validator
            ->numeric('package_id')
            ->requirePresence('package_id', 'create')
            ->notEmpty('package_id');

        $validator
            ->numeric('type')
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->dateTime('deadline')
            ->requirePresence('deadline', 'create')
            ->notEmpty('deadline');
        return $validator;
    }



}
