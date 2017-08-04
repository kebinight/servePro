<?php

namespace Wpadmin\Model\Table;

use Wpadmin\Model\Entity\Menu;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CwpMenu Model
 *
 */
class MenuTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_menu');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->belongsToMany('g', [
            'className' => 'Wpadmin.Group',
            'joinTable' => 'lm_group_menu',
            'foreignKey' => 'menu_id',
            'targetForeignKey' => 'group_id'
        ]);
        
        /*$this->belongsToMany('Admins', [
            'className' => 'Wpadmin.Admin',
            'joinTable' => 'lm_admin_menu',
            'foreignKey' => 'menu_id',
            'targetForeignKey' => 'admin_id'
        ]);*/
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
                ->add('id', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('id', 'create');

        $validator
                ->allowEmpty('name');

        $validator
                ->allowEmpty('node');

        $validator
                ->add('pid', 'valid', ['rule' => 'numeric'])
                ->requirePresence('pid', 'create')
                ->notEmpty('pid');

        $validator
                ->allowEmpty('class');

        $validator
                ->add('rank', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('rank');

        $validator
                ->add('is_menu', 'valid', ['rule' => 'boolean'])
                ->requirePresence('is_menu', 'create')
                ->notEmpty('is_menu');

        $validator
                ->add('status', 'valid', ['rule' => 'boolean'])
                ->requirePresence('status', 'create')
                ->notEmpty('status');

        $validator
                ->allowEmpty('remark');

        return $validator;
    }

}
