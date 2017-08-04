<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MenuFixture
 *
 */
class MenuFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 's_menu';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '菜单项名称', 'precision' => null, 'fixed' => null],
        'node' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '节点对应的页面url', 'precision' => null, 'fixed' => null],
        'pid' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => '0', 'comment' => '父级节点id', 'precision' => null, 'autoIncrement' => null],
        'class' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '节点样式', 'precision' => null, 'fixed' => null],
        'rank' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => '0', 'comment' => '排序权重，数值越大权重越高，也就越靠前', 'precision' => null, 'autoIncrement' => null],
        'status' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => '1', 'comment' => '启动状态：0#禁用 1#启动', 'precision' => null, 'autoIncrement' => null],
        'remark' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '备注说明', 'precision' => null, 'fixed' => null],
        'create_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '创建日期时间', 'precision' => null],
        'update_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '更改日期时间', 'precision' => null],
        '_indexes' => [
            'pid' => ['type' => 'index', 'columns' => ['pid'], 'length' => []],
        ],
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
            'name' => 'Lorem ipsum dolor sit amet',
            'node' => 'Lorem ipsum dolor sit amet',
            'pid' => 1,
            'class' => 'Lorem ipsum dolor sit amet',
            'rank' => 1,
            'status' => 1,
            'remark' => 'Lorem ipsum dolor sit amet',
            'create_time' => '2017-08-04 18:15:18',
            'update_time' => '2017-08-04 18:15:18'
        ],
    ];
}
