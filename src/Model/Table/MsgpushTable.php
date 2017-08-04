<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Msgpush Model
 *
 * @method \App\Model\Entity\Msgpush get($primaryKey, $options = [])
 * @method \App\Model\Entity\Msgpush newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Msgpush[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Msgpush|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Msgpush patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Msgpush[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Msgpush findOrCreate($search, callable $callback = null)
 */
class MsgpushTable extends Table
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
        $this->table('lm_msgpush');
        $this->primaryKey('id');
        $this->belongsTo('Ptmsg', [
            'className' => 'Ptmsg',
            'foreignKey' => 'msg_id',
        ]);
        $this->belongsTo('User', [
            'className' => 'User',
            'foreignKey' => 'user_id',
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
            ->integer('msg_id')
            ->requirePresence('msg_id');
        $validator
            ->integer('user_id')
            ->requirePresence('user_id');
        return $validator;
    }
}
