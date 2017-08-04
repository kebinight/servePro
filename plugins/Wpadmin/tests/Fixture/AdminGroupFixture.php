<?php
namespace Wpadmin\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AdminGroupFixture
 *
 */
class AdminGroupFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'lm_admin_group';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'admin_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '管理员', 'precision' => null, 'autoIncrement' => null],
        'group_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '所属组', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'admin_id' => ['type' => 'unique', 'columns' => ['admin_id', 'group_id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'admin_id' => 1,
            'group_id' => 1
        ],
    ];
}
