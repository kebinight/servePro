<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Smsmsg Model
 *
 * @method \App\Model\Entity\Smsmsg get($primaryKey, $options = [])
 * @method \App\Model\Entity\Smsmsg newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Smsmsg[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Smsmsg|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Smsmsg patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Smsmsg[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Smsmsg findOrCreate($search, callable $callback = null)
 */
class SmsmsgTable extends Table
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

        $this->table('lm_smsmsg');
        $this->displayField('id');
        $this->primaryKey('id');
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
            ->requirePresence('phone', 'create')
            ->notEmpty('phone');

        $validator
            ->allowEmpty('code');

        $validator
            ->allowEmpty('content');

        $validator
            ->dateTime('create_time')
            ->allowEmpty('create_time');

        return $validator;
    }
}
