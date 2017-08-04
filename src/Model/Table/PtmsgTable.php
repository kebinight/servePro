<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Ptmsg Model
 *
 * @method \App\Model\Entity\Ptmsg get($primaryKey, $options = [])
 * @method \App\Model\Entity\Ptmsg newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Ptmsg[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Ptmsg|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Ptmsg patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Ptmsg[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Ptmsg findOrCreate($search, callable $callback = null)
 */
class PtmsgTable extends Table
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
        $this->table('lm_ptmsg');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->hasMany('Msgpushs', [
            'className' => 'Msgpush',
            'foreignKey' => 'msg_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
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
