<?php
namespace Wpadmin\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Actionlog Model
 *
 * @method \Wpadmin\Model\Entity\Actionlog get($primaryKey, $options = [])
 * @method \Wpadmin\Model\Entity\Actionlog newEntity($data = null, array $options = [])
 * @method \Wpadmin\Model\Entity\Actionlog[] newEntities(array $data, array $options = [])
 * @method \Wpadmin\Model\Entity\Actionlog|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Wpadmin\Model\Entity\Actionlog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Wpadmin\Model\Entity\Actionlog[] patchEntities($entities, array $data, array $options = [])
 * @method \Wpadmin\Model\Entity\Actionlog findOrCreate($search, callable $callback = null)
 */
class ActionlogTable extends Table
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

        $this->table('lm_actionlog');
        $this->displayField('id');
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
            ->requirePresence('url', 'create')
            ->notEmpty('url');

        $validator
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->requirePresence('useragent', 'create')
            ->notEmpty('useragent');

        $validator
            ->requirePresence('ip', 'create')
            ->notEmpty('ip');

        $validator
            ->requirePresence('filename', 'create')
            ->notEmpty('filename');

        $validator
            ->requirePresence('msg', 'create')
            ->notEmpty('msg');

        $validator
            ->requirePresence('controller', 'create')
            ->notEmpty('controller');

        $validator
            ->requirePresence('action', 'create')
            ->notEmpty('action');

        $validator
            ->requirePresence('param', 'create')
            ->notEmpty('param');

        $validator
            ->requirePresence('user', 'create')
            ->notEmpty('user');

        $validator
            ->dateTime('create_time')
            ->requirePresence('create_time', 'create')
            ->notEmpty('create_time');

        return $validator;
    }
}
