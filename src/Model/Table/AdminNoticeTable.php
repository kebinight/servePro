<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Conversation Model
 * @method \App\Model\Entity\AdminNotice get($primaryKey, $options = [])
 * @method \App\Model\Entity\AdminNotice newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AdminNotice[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AdminNotice|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AdminNotice patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AdminNotice[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AdminNotice findOrCreate($search, callable $callback = null)
 */
class AdminNoticeTable extends Table
{

    /**
     * Initialize method
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('lm_admin_notice');
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
            ->notEmpty('notice');
        return $validator;
    }
}
