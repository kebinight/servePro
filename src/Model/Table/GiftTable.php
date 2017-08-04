<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Gift Model
 *
 * @method \App\Model\Entity\Gift get($primaryKey, $options = [])
 * @method \App\Model\Entity\Gift newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Gift[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Gift|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Gift patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Gift[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Gift findOrCreate($search, callable $callback = null)
 */
class GiftTable extends Table
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

        $this->table('lm_gift');
        $this->displayField('pic');
        $this->primaryKey('id');
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
            ->numeric('price')
            ->requirePresence('price', 'create')
            ->notEmpty('price');

        $validator
            ->allowEmpty('name');

        $validator
            ->requirePresence('pic', 'create')
            ->notEmpty('pic');

        $validator
            ->allowEmpty('remark');

        return $validator;
    }
}
