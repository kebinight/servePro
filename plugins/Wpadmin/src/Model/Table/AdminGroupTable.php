<?php
namespace Wpadmin\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AdminGroup Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Admins
 * @property \Cake\ORM\Association\BelongsTo $Groups
 *
 * @method \Wpadmin\Model\Entity\AdminGroup get($primaryKey, $options = [])
 * @method \Wpadmin\Model\Entity\AdminGroup newEntity($data = null, array $options = [])
 * @method \Wpadmin\Model\Entity\AdminGroup[] newEntities(array $data, array $options = [])
 * @method \Wpadmin\Model\Entity\AdminGroup|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Wpadmin\Model\Entity\AdminGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Wpadmin\Model\Entity\AdminGroup[] patchEntities($entities, array $data, array $options = [])
 * @method \Wpadmin\Model\Entity\AdminGroup findOrCreate($search, callable $callback = null)
 */
class AdminGroupTable extends Table
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

        $this->table('lm_admin_group');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Admins', [
            'foreignKey' => 'admin_id',
            'joinType' => 'INNER',
            'className' => 'Wpadmin.Admins'
        ]);
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER',
            'className' => 'Wpadmin.Groups'
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
        $rules->add($rules->existsIn(['admin_id'], 'Admins'));
        $rules->add($rules->existsIn(['group_id'], 'Groups'));

        return $rules;
    }
}
