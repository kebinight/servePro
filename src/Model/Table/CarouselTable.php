<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Carousel Model
 *
 * @method \App\Model\Entity\Carousel get($primaryKey, $options = [])
 * @method \App\Model\Entity\Carousel newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Carousel[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Carousel|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Carousel patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Carousel[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Carousel findOrCreate($search, callable $callback = null)
 */
class CarouselTable extends Table
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

        $this->table('lm_carousel');
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
            ->integer('position')
            ->requirePresence('position', 'create')
            ->notEmpty('position');
        return $validator;
    }
}
