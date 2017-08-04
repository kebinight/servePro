<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Suggest Model
 *
 * @method \App\Model\Entity\Suggest get($primaryKey, $options = [])
 * @method \App\Model\Entity\Suggest newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Suggest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Suggest|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Suggest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Suggest[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Suggest findOrCreate($search, callable $callback = null)
 */
class SuggestTable extends Table
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

        $this->table('lm_suggest');
        $this->displayField('body');
        $this->primaryKey('id');

        $this->belongsTo('User', [
            'className' => 'User',
            'foreignKey' => 'user_id'
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
            ->requirePresence('body', 'create');

        return $validator;
    }
}
