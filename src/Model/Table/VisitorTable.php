<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Visitor Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Visitor
 * @property \Cake\ORM\Association\BelongsTo $Visited
 *
 * @method \App\Model\Entity\Visitor get($primaryKey, $options = [])
 * @method \App\Model\Entity\Visitor newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Visitor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Visitor|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Visitor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Visitor[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Visitor findOrCreate($search, callable $callback = null)
 */
class VisitorTable extends Table
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

        $this->table('lm_visitor');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Visiter', [
            'foreignKey' => 'visitor_id',
            'joinType' => 'INNER',
            'className' => 'User'
        ]);
        $this->belongsTo('Visited', [
            'foreignKey' => 'visited_id',
            'joinType' => 'INNER',
            'className' => 'User'
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
            ->integer('visitor_id')
            ->allowEmpty('visitor_id', 'create');

        $validator
            ->integer('visited_id')
            ->allowEmpty('visited_id', 'create');
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
