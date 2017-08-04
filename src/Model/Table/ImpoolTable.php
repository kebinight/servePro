<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Impool Model
 *
 * @method \App\Model\Entity\Impool get($primaryKey, $options = [])
 * @method \App\Model\Entity\Impool newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Impool[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Impool|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Impool patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Impool[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Impool findOrCreate($search, callable $callback = null)
 */
class ImpoolTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_impool');
        $this->displayField('id');
        $this->primaryKey('id');
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
    public function validationDefault(Validator $validator) {
        $validator
                ->integer('id')
                ->allowEmpty('id', 'create');

        $validator
                ->requirePresence('accid', 'create')
                ->notEmpty('accid');

        $validator
                ->requirePresence('token', 'create')
                ->notEmpty('token');


        return $validator;
    }

}
