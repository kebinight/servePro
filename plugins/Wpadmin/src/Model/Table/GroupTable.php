<?php

namespace Wpadmin\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CwpGroup Model
 *
 */
class GroupTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_group');
        $this->alias('g');
        $this->primaryKey('id');
        $this->displayField('name');

        $this->hasMany('Admin', [
            'className' => 'Admin.Admin',
            'joinTable' => 'lm_admin_group',
            'foreignKey' => 'group_id',
            'dependent' => true
        ]);

        $this->belongsToMany('menu', [
            'className' => 'Wpadmin.Menu',
            'joinTable' => 'lm_group_menu',
            'foreignKey' => 'group_id',
            'targetForeignKey' => 'menu_id'
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'ctime' => 'new',
                    'utime' => 'always'
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
                ->add('Id', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('Id', 'create');

        $validator
                ->requirePresence('name', 'create')
                ->notEmpty('name')
                ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
                ->requirePresence('remark', 'create')
                ->notEmpty('remark');


        return $validator;
    }

}
