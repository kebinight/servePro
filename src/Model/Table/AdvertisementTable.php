<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Advertisement Model
 *
 * @method \App\Model\Entity\Advertisement get($primaryKey, $options = [])
 * @method \App\Model\Entity\Advertisement newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Advertisement[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Advertisement|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Advertisement patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Advertisement[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Advertisement findOrCreate($search, callable $callback = null)
 */
class AdvertisementTable extends Table
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

        $this->table('lm_advertisement');
        $this->displayField('id');
        $this->primaryKey('id');

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
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');
        $validator
            ->allowEmpty('title');
        $validator
            ->requirePresence('pic_url', 'create')
            ->notEmpty('pic_url');
        $validator
            ->requirePresence('to_url', 'create')
            ->allowEmpty('to_url');
        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');
        return $validator;
    }
}
