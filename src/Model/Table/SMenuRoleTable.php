<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SMenuRole Model
 *
 * @method \App\Model\Entity\SMenuRole get($primaryKey, $options = [])
 * @method \App\Model\Entity\SMenuRole newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SMenuRole[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SMenuRole|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SMenuRole patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SMenuRole[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SMenuRole findOrCreate($search, callable $callback = null)
 */
class SMenuRoleTable extends Table
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

        $this->table('s_menu_role');
        $this->primaryKey('id');

        //管理者
        $this->hasOne('Admin', [
            'className' => 'Suser',
            'foreignKey' => 'admin_id'
        ]);

        //多对多中间表
        $this->belongsTo('Menu', [
            'className' => 'Smenu',
            'foreignKey' => 'menu_id'
        ]);

        $this->belongsTo('Role', [
            'className' => 'Srole',
            'foreignKey' => 'role_id'
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

        return $validator;
    }
}
