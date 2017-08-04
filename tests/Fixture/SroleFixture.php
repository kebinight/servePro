<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SroleFixture
 *
 */
class SroleFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 's_role';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '创建者id', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '角色名称', 'precision' => null, 'fixed' => null],
        'rank' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '排序权重，数值越大权重越高（显示越靠前）', 'precision' => null, 'autoIncrement' => null],
        'remark' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '备注说明', 'precision' => null, 'fixed' => null],
        'create_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '创建时间', 'precision' => null],
        'update_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '更改时间', 'precision' => null],
        '_indexes' => [
            'id' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
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
            'name' => 'Lorem ipsum dolor sit amet',
            'rank' => 1,
            'remark' => 'Lorem ipsum dolor sit amet',
            'create_time' => '2017-08-04 18:16:54',
            'update_time' => '2017-08-04 18:16:54'
        ],
    ];
}
