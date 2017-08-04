<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Activity Model
 *
 * @method \App\Model\Entity\Activity get($primaryKey, $options = [])
 * @method \App\Model\Entity\Activity newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Activity[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Activity|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Activity patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Activity[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Activity findOrCreate($search, callable $callback = null)
 */
class ActivityTable extends Table
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

        $this->table('lm_activity');
        $this->displayField('title');
        $this->primaryKey('id');

        /*$this->belongsToMany('Actregistrations', [
            'through' => 'Actregistration',
            'className' => 'Actregistration',
            'foreignKey' => 'activity_id',
            'targetForeignKey' => 'user_id',
        ]);*/

        $this->hasMany('Actregistrations', [

            'className' => 'Actregistration',
            'foreignKey' => 'activity_id',

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
            ->requirePresence('big_img', 'create')
            ->notEmpty('big_img');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->numeric('male_price')
            ->requirePresence('male_price', 'create')
            ->notEmpty('male_price');

        $validator
            ->numeric('female_price')
            ->requirePresence('female_price', 'create')
            ->notEmpty('female_price');

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->dateTime('start_time')
            ->requirePresence('start_time', 'create')
            ->notEmpty('start_time');

        $validator
            ->dateTime('end_time')
            ->requirePresence('end_time', 'create')
            ->notEmpty('end_time');

        $validator
            ->requirePresence('site', 'create')
            ->notEmpty('site');

        $validator
            ->numeric('site_lat')
            ->requirePresence('site_lat', 'create')
            ->notEmpty('site_lat');

        $validator
            ->numeric('site_lng')
            ->requirePresence('site_lng', 'create')
            ->notEmpty('site_lng');

        $validator
            ->integer('male_lim')
            ->requirePresence('male_lim', 'create')
            ->notEmpty('male_lim');

        $validator
            ->integer('female_lim')
            ->requirePresence('female_lim', 'create')
            ->notEmpty('female_lim');

        $validator
            ->requirePresence('detail', 'create')
            ->notEmpty('detail');

        $validator
            ->requirePresence('notice', 'create')
            ->notEmpty('notice');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->requirePresence('remark', 'create')
            ->notEmpty('remark');

        return $validator;
    }
}
