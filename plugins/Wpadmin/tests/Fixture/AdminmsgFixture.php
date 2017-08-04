<?php
namespace Wpadmin\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AdminmsgFixture
 *
 */
class AdminmsgFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'adminmsg';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '后台消息', 'autoIncrement' => true, 'precision' => null],
        'type' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '类型', 'precision' => null, 'autoIncrement' => null],
        'msg' => ['type' => 'string', 'length' => 250, 'null' => false, 'default' => null, 'comment' => '内容', 'precision' => null, 'fixed' => null],
        'status' => ['type' => 'string', 'length' => 250, 'null' => false, 'default' => null, 'comment' => '状态', 'precision' => null, 'fixed' => null],
        'create_time' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '创建时间', 'precision' => null],
        'update_time' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '修改时间', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
            'type' => 1,
            'msg' => 'Lorem ipsum dolor sit amet',
            'status' => 'Lorem ipsum dolor sit amet',
            'create_time' => '2016-04-25 11:19:27',
            'update_time' => '2016-04-25 11:19:27'
        ],
    ];
}
