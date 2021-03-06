<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Smenu Model
 *
 * @method \App\Model\Entity\Smenu get($primaryKey, $options = [])
 * @method \App\Model\Entity\Smenu newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Smenu[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Smenu|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Smenu patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Smenu[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Smenu findOrCreate($search, callable $callback = null)
 */
class SmenuTable extends Table
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

        $this->table('s_menu');
        $this->displayField('name');
        $this->primaryKey('id');

        //管理员
        $this->hasOne('Admin', [
            'className' => 'Suser',
            'foreignKey' => 'admin_id'
        ]);

        //菜单对应角色权限
        $this->belongsToMany('Srole', [
            'through' => 'SMenuRole',
            'foreignKey' => 'menu_id',
            'targetForeignKey' => 'role_id'
        ]);

        //自关联
        $this->belongsTo('Parent', [
            'className' => 'Smenu',
            'foreignKey' => 'parent_id'
        ]);

        $this->hasMany('Children', [
            'className' => 'Smenu',
            'foreignKey' => 'parent_id',
            'dependent' => true,
            'cascadeCallbacks' => true
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
            ->allowEmpty('node');

        $validator
            ->integer('parent_id')
            ->requirePresence('parent_id', 'create')
            ->notEmpty('parent_id');

        $validator
            ->allowEmpty('class');

        $validator
            ->integer('rank')
            ->requirePresence('rank', 'create')
            ->notEmpty('rank');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

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
}
