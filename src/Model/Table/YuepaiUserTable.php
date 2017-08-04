<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * YuepaiUser Model
 *
 * @method \App\Model\Entity\Cost get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cost newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cost[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cost|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cost patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cost[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cost findOrCreate($search, callable $callback = null)
 */
class YuepaiUserTable extends Table
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

        $this->table('lm_yuepai_user');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->belongsTo('Yuepai', [
            'foreignKey' => 'yuepai_id'
        ]);

        $this->belongsTo('User', [
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
            ->integer('yuepai_id')
            ->allowEmpty('yuepai_id', 'create');

        $validator
            ->integer('user_id')
            ->allowEmpty('user_id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('phone', 'create')
            ->notEmpty('phone');

        $validator
            ->requirePresence('area', 'create')
            ->notEmpty('area');

        $validator
            ->numeric('check')
            ->requirePresence('checked', 'create')
            ->notEmpty('check');
        return $validator;
    }
}
