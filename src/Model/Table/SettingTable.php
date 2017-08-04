<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Setting Model
 * @method \App\Model\Entity\Setting get($primaryKey, $options = [])
 * @method \App\Model\Entity\Setting newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Setting[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Setting|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setting patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Setting[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Setting findOrCreate($search, callable $callback = null)
 */
class SettingTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('lm_setting');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'create_time' => 'new',
                    'update_time' => 'always',
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
    public function validationDefault(Validator $validator) {
        return $validator;
    }

}
