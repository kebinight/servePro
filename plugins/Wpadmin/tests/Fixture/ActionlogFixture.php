<?php
namespace Wpadmin\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ActionlogFixture
 *
 */
class ActionlogFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'lm_actionlog';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '主键，自增', 'autoIncrement' => true, 'precision' => null],
        'url' => ['type' => 'string', 'length' => 1000, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '请求地址', 'precision' => null, 'fixed' => null],
        'type' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '请求类型', 'precision' => null, 'fixed' => null],
        'useragent' => ['type' => 'string', 'length' => 1000, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '浏览器信息', 'precision' => null, 'fixed' => null],
        'ip' => ['type' => 'string', 'length' => 80, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '客户端IP', 'precision' => null, 'fixed' => null],
        'filename' => ['type' => 'string', 'length' => 250, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '脚本名称', 'precision' => null, 'fixed' => null],
        'msg' => ['type' => 'string', 'length' => 150, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '日志内容', 'precision' => null, 'fixed' => null],
        'controller' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '控制器', 'precision' => null, 'fixed' => null],
        'action' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '动作', 'precision' => null, 'fixed' => null],
        'param' => ['type' => 'string', 'length' => 1000, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '请求参数', 'precision' => null, 'fixed' => null],
        'user' => ['type' => 'string', 'length' => 80, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '操作者', 'precision' => null, 'fixed' => null],
        'create_time' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '创建时间', 'precision' => null],
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
            'url' => 'Lorem ipsum dolor sit amet',
            'type' => 'Lorem ipsum dolor sit amet',
            'useragent' => 'Lorem ipsum dolor sit amet',
            'ip' => 'Lorem ipsum dolor sit amet',
            'filename' => 'Lorem ipsum dolor sit amet',
            'msg' => 'Lorem ipsum dolor sit amet',
            'controller' => 'Lorem ipsum dolor sit amet',
            'action' => 'Lorem ipsum dolor sit amet',
            'param' => 'Lorem ipsum dolor sit amet',
            'user' => 'Lorem ipsum dolor sit amet',
            'create_time' => '2016-10-17 11:11:20'
        ],
    ];
}
