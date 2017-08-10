<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Suser Model
 *
 * @method \App\Model\Entity\Suser get($primaryKey, $options = [])
 * @method \App\Model\Entity\Suser newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Suser[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Suser|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Suser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Suser[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Suser findOrCreate($search, callable $callback = null)
 */
class SuserTable extends Table
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

        $this->table('s_user');
        $this->displayField('id');
        $this->primaryKey('id');

        //用户角色
        $this->belongsToMany('Srole', [
            'through' => 'SUserRole',
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'role_id'
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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('avatar');

        $validator
            ->requirePresence('account', 'create')
            ->notEmpty('account');

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        $validator
            ->requirePresence('nick', 'create')
            ->notEmpty('nick');

        $validator
            ->allowEmpty('truename');

        $validator
            ->integer('gender')
            ->allowEmpty('gender');

        $validator
            ->integer('status')
            ->allowEmpty('status');

        $validator
            ->integer('is_del')
            ->allowEmpty('is_del');

        $validator
            ->dateTime('create_time')
            ->allowEmpty('create_time');

        $validator
            ->dateTime('update_time')
            ->allowEmpty('update_time');

        return $validator;
    }
}
