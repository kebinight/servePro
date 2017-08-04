<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserSkills Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Skills
 * @property \Cake\ORM\Association\BelongsTo $Costs
 *
 * @method \App\Model\Entity\UserSkill get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserSkill newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UserSkill[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserSkill|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserSkill patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserSkill[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserSkill findOrCreate($search, callable $callback = null)
 */
class UserSkillTable extends Table
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

        $this->table('lm_user_skill');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Skill', [
            'foreignKey' => 'skill_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Cost', [
            'foreignKey' => 'cost_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('User',[
            'className'=>'User'
        ]);

        $this->belongsToMany('Tags', [
            'joinTable' => 'lm_user_skill_tag',
            'foreignKey' => 'user_skill_id',
            'targetForeignKey' => 'tag_id',
            'className' => 'Tag'
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
            ->integer('skill_id')
            ->requirePresence('skill_id', 'create')
            ->notEmpty('skill_id', '请选择技能名称');

        $validator
            ->integer('cost_id')
            ->requirePresence('cost_id', 'create')
            ->notEmpty('cost_id', '请选择技能价格');

        $validator
            ->integer('is_used')
            ->requirePresence('is_used', 'create')
            ->notEmpty('is_used', '非法请求');

        $validator
            ->integer('is_checked')
            ->requirePresence('is_checked', 'create')
            ->notEmpty('is_checked', '非法请求');

        $validator
            ->lengthBetween('description', [1, 100]);
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
        $rules->add($rules->existsIn(['skill_id'], 'Skill'));
        $rules->add($rules->existsIn(['cost_id'], 'Cost'));

        return $rules;
    }

}
