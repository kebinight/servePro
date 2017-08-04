<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Movement Model
 *
 * @method \App\Model\Entity\Movement get($primaryKey, $options = [])
 * @method \App\Model\Entity\Movement newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Movement[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Movement|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Movement patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Movement[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Movement findOrCreate($search, callable $callback = null)
 */
class MovementTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_movement');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->belongsTo('User',[
           'className'=>'User' 
        ]);

        $this->hasMany('Mvpraises',[
            'className'=>'Mvpraise',
            'foreignKey' => 'movement_id',
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
    public function validationDefault(Validator $validator) {
        $validator
                ->integer('id')
                ->allowEmpty('id', 'create');

        $validator
                ->integer('type')
                ->requirePresence('type', 'create')
                ->notEmpty('type');

        $validator
                ->allowEmpty('images');

        $validator
                ->allowEmpty('video');

        $validator
                ->allowEmpty('video_cover');


        return $validator;
    }

}
