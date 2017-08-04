<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SlimitFixture
 *
 */
class SlimitFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 's_limit';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '管理者id', 'precision' => null, 'autoIncrement' => null],
        'pid' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => true, 'default' => null, 'comment' => '父级权限id', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '权限名称', 'precision' => null, 'fixed' => null],
        'node' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '对应权限', 'precision' => null, 'fixed' => null],
        'rank' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => true, 'default' => '0', 'comment' => '排序权重，数值越大权重越高，显示越靠前', 'precision' => null, 'autoIncrement' => null],
        'create_time' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '创建时间', 'precision' => null],
        'update_time' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新时间', 'precision' => null],
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
            'user_id' => 1,
            'pid' => 1,
            'name' => 'Lorem ipsum dolor sit amet',
            'node' => 'Lorem ipsum dolor sit amet',
            'rank' => 1,
            'create_time' => '2017-08-04 18:13:58',
            'update_time' => '2017-08-04 18:13:58'
        ],
    ];
}
