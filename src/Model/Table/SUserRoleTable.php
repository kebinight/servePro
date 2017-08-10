<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SUserRole Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Admins
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Roles
 *
 * @method \App\Model\Entity\SUserRole get($primaryKey, $options = [])
 * @method \App\Model\Entity\SUserRole newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SUserRole[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SUserRole|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SUserRole patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SUserRole[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SUserRole findOrCreate($search, callable $callback = null)
 */
class SUserRoleTable extends Table
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

        $this->table('s_user_role');
        $this->displayField('id');
        $this->primaryKey('id');

        //多对多中间表
        $this->belongsTo('User', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Role', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER'
        ]);

        //管理者
        $this->hasOne('Admin', [
            'className' => 'Suser',
            'foreignKey' => 'admin_id'
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

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Suser'));
        $rules->add($rules->existsIn(['role_id'], 'Srole'));
        return $rules;
    }
}
