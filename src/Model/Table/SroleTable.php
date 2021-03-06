<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Srole Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Srole get($primaryKey, $options = [])
 * @method \App\Model\Entity\Srole newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Srole[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Srole|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Srole patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Srole[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Srole findOrCreate($search, callable $callback = null)
 */
class SroleTable extends Table
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

        $this->table('s_role');
        $this->primaryKey('id');
        $this->displayField('name');

        //管理员
        $this->hasOne('Admin', [
            'className' => 'Suser',
            'foreignKey' => 'admin_id'
        ]);

        //角色权限
        $this->belongsToMany('Slimit', [
            'through' => 'SRoleLimit',
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'limit_id'
            //'joinTable' => 'SRoleLimit',
            //'className' => 'Slimit'
        ]);

        //角色对应菜单
        $this->belongsToMany('Smenu', [
            'through' => 'SMenuRole',
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'menu_id'
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
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->integer('rank')
            ->requirePresence('rank', 'create')
            ->notEmpty('rank');

        $validator
            ->integer('status');

        $validator
            ->allowEmpty('remark');

        $validator
            ->dateTime('create_time')
            ->allowEmpty('create_time');

        $validator
            ->dateTime('update_time')
            ->allowEmpty('update_time');

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
        return $rules;
    }
}
