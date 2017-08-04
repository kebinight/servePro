<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Dates Model
 *
 * @property \Cake\ORM\Association\BelongsTo $UserSkills
 *
 * @method \App\Model\Entity\Date get($primaryKey, $options = [])
 * @method \App\Model\Entity\Date newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Date[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Date|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Date patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Date[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Date findOrCreate($search, callable $callback = null)
 */
class DateTable extends Table
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

        $this->table('lm_date');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->belongsTo('UserSkill', [
            'foreignKey' => 'user_skill_id'
        ]);

        $this->belongsTo('Skill', [
            'foreignKey' => 'skill_id'
        ]);

        $this->belongsTo('User', [
            'foreignKey' => 'user_id'
        ]);

        $this->belongsToMany('Tags', [
            'joinTable' => 'lm_date_tag',
            'dependent' => false,
            'foreignKey' => 'date_id',
            'className' => "Tag",
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_time' => 'new',
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
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->requirePresence('site', 'create')
            ->notEmpty('site');

        $validator
            ->numeric('price')
            ->requirePresence('price', 'create')
            ->notEmpty('price');

        $validator
            ->allowEmpty('description');

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
        $rules->add($rules->existsIn(['user_skill_id'], 'UserSkill'));

        return $rules;
    }

}
