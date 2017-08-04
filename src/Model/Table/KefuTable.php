<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Kefu Model
 *
 * @method \App\Model\Entity\Kefu get($primaryKey, $options = [])
 * @method \App\Model\Entity\Kefu newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Kefu[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Kefu|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Kefu patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Kefu[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Kefu findOrCreate($search, callable $callback = null)
 */
class KefuTable extends Table
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
        $this->table('lm_kefu');
        $this->displayField('nick');
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
        return $validator;
    }
}
