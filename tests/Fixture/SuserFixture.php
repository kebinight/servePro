<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SuserFixture
 *
 */
class SuserFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 's_user';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'avatar' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '用户头像', 'precision' => null, 'fixed' => null],
        'account' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '用户账号', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '用户密码', 'precision' => null, 'fixed' => null],
        'nick' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '用户昵称', 'precision' => null, 'fixed' => null],
        'truename' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '真实姓名', 'precision' => null, 'fixed' => null],
        'gender' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => true, 'default' => '1', 'comment' => '性别：1#男性 2#女性', 'precision' => null, 'autoIncrement' => null],
        'status' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => true, 'default' => '1', 'comment' => '状态：0#禁用 1#启用', 'precision' => null, 'autoIncrement' => null],
        'is_del' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => true, 'default' => '0', 'comment' => '逻辑删除：0#否 1#是', 'precision' => null, 'autoIncrement' => null],
        'create_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '创建时间', 'precision' => null],
        'update_time' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '更改时间', 'precision' => null],
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
            'avatar' => 'Lorem ipsum dolor sit amet',
            'account' => 'Lorem ipsum dolor sit amet',
            'password' => 'Lorem ipsum dolor sit amet',
            'nick' => 'Lorem ipsum dolor sit amet',
            'truename' => 'Lorem ipsum dolor sit amet',
            'gender' => 1,
            'status' => 1,
            'is_del' => 1,
            'create_time' => '2017-08-04 18:14:39',
            'update_time' => '2017-08-04 18:14:39'
        ],
    ];
}
