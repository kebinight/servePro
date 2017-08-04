<?php
use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public function up()
    {

        $this->table('lm_actionlog')
            ->addColumn('url', 'string', [
                'comment' => '请求地址',
                'default' => null,
                'limit' => 1000,
                'null' => false,
            ])
            ->addColumn('type', 'string', [
                'comment' => '请求类型',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('useragent', 'string', [
                'comment' => '浏览器信息',
                'default' => null,
                'limit' => 1000,
                'null' => false,
            ])
            ->addColumn('ip', 'string', [
                'comment' => '客户端IP',
                'default' => null,
                'limit' => 80,
                'null' => false,
            ])
            ->addColumn('filename', 'string', [
                'comment' => '脚本名称',
                'default' => null,
                'limit' => 250,
                'null' => false,
            ])
            ->addColumn('msg', 'string', [
                'comment' => '日志内容',
                'default' => null,
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('controller', 'string', [
                'comment' => '控制器',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('action', 'string', [
                'comment' => '动作',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('param', 'string', [
                'comment' => '请求参数',
                'default' => null,
                'limit' => 1000,
                'null' => false,
            ])
            ->addColumn('user', 'string', [
                'comment' => '操作者',
                'default' => null,
                'limit' => 80,
                'null' => false,
            ])
            ->addColumn('create_time', 'datetime', [
                'comment' => '创建时间',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('lm_admin')
            ->addColumn('username', 'string', [
                'comment' => '用户名',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'comment' => '密码',
                'default' => null,
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('enabled', 'boolean', [
                'comment' => '1启用0禁用',
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('ctime', 'datetime', [
                'comment' => '创建时间',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('utime', 'datetime', [
                'comment' => '修改时间',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('login_time', 'datetime', [
                'comment' => '登录时间',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('login_ip', 'string', [
                'comment' => '登录ip',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addIndex(
                [
                    'username',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('lm_admin_group')
            ->addColumn('admin_id', 'integer', [
                'comment' => '管理员',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('group_id', 'integer', [
                'comment' => '所属组',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'admin_id',
                    'group_id',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('lm_group')
            ->addColumn('name', 'string', [
                'comment' => '群组名称',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('remark', 'string', [
                'comment' => '备注',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('ctime', 'datetime', [
                'comment' => '创建时间',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('utime', 'datetime', [
                'comment' => '修改时间',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'name',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('lm_group_menu')
            ->addColumn('group_id', 'integer', [
                'comment' => '群组',
                'default' => 0,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('menu_id', 'integer', [
                'comment' => '权限',
                'default' => 0,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'group_id',
                    'menu_id',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('lm_menu')
            ->addColumn('name', 'string', [
                'comment' => '节点名称',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('node', 'string', [
                'comment' => '路径',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('pid', 'integer', [
                'comment' => '父ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('class', 'string', [
                'comment' => '样式',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('rank', 'integer', [
                'comment' => '排序',
                'default' => null,
                'limit' => 6,
                'null' => true,
            ])
            ->addColumn('is_menu', 'boolean', [
                'comment' => '是否在菜单显示',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('status', 'boolean', [
                'comment' => '状态',
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('remark', 'string', [
                'comment' => '备注',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->create();
    }

    public function down()
    {
        $this->dropTable('lm_actionlog');
        $this->dropTable('lm_admin');
        $this->dropTable('lm_admin_group');
        $this->dropTable('lm_group');
        $this->dropTable('lm_group_menu');
        $this->dropTable('lm_menu');
    }
}
