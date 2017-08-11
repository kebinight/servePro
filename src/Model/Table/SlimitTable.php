<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Slimit Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Admins
 *
 * @method \App\Model\Entity\Slimit get($primaryKey, $options = [])
 * @method \App\Model\Entity\Slimit newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Slimit[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Slimit|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Slimit patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Slimit[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Slimit findOrCreate($search, callable $callback = null)
 */
class SlimitTable extends Table
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

        $this->table('s_limit');
        $this->displayField('name');
        $this->primaryKey('id');

        //管理者
        $this->hasOne('Admin', [
            'className' => 'Suser',
            'foreignKey' => 'admin_id'
        ]);

        //自关联
        $this->belongsTo('Parent', [
            'className' => 'Slimit',
            'foreignKey' => 'parent_id'
        ]);

        $this->hasMany('Child', [
            'className' => 'Slimit',
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
            ->integer('pid')
            ->allowEmpty('pid');

        $validator
            ->integer('status')
            ->allowEmpty('status');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('node', 'create')
            ->notEmpty('node');

        $validator
            ->allowEmpty('remark');

        $validator
            ->integer('rank')
            ->allowEmpty('rank');
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
        //$rules->add($rules->existsIn(['admin_id'], 'Admin'));
        return $rules;
    }
}
