<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateMenu extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        // create the table
        $table = $this->table('menu', ['engine'=>'InnoDB','comment'=>'后台菜单表']);
        $table->addColumn('name', 'string', ['limit' => 64,  'null'=>false, 'default'=>'', 'comment'=>'菜单名称'])
            ->addColumn('icon', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'菜单图标'])
            ->addColumn('pid', 'integer', ['limit' => 11,  'null'=>false, 'default'=>0, 'comment'=>'上级菜单id'])
            ->addColumn('module', 'string', ['limit' => 32, 'null'=>false, 'default'=>'', 'comment'=>'模块名称'])
            ->addColumn('controller', 'string', ['limit' => 32, 'null'=>false, 'default'=>'', 'comment'=>'控制器名称'])
            ->addColumn('action', 'string', ['limit' => 32, 'null'=>false, 'default'=>'', 'comment'=>'方法名称'])
            ->addColumn('param', 'string', ['limit' => 255,'null'=>false, 'default'=>'', 'comment'=>'附加参数'])
            ->addColumn('type', 'integer', ['limit' => MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'类型,0:只作菜单；1:权限点+菜单'])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'是否禁用'])
            ->addColumn('remark', 'string', ['limit' => 255,'null'=>false, 'default'=>'', 'comment'=>'备注'])
            ->addColumn('list_order', 'integer', ['limit' => 11,'null'=>false, 'default'=>100, 'comment'=>'排序'])
            ->addIndex(['pid'], ['name' => 'index_pid', 'type' => 'normal'])
            ->save();

        //插入基本数据
        $data = [
            ['id' => 1, 'name' => '缓存更新', 'icon' => 'fa-trash-o', 'pid' => 0, 'module' => 'console', 'controller' => 'index', 'action' => 'cache', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 1],
            ['id' => 2, 'name' => '系统公告', 'icon' => 'fa-bullhorn', 'pid' => 0, 'module' => 'console', 'controller' => 'index', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 2],
            ['id' => 3, 'name' => '系统管理', 'icon' => 'fa-cogs', 'pid' => 0, 'module' => 'console', 'controller' => 'role', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 4, 'name' => '系统信息', 'icon' => 'fa-flag-o', 'pid' => 2, 'module' => 'console', 'controller' => 'index', 'action' => 'index', 'param' => '', 'type' => 0, 'status' => 1, 'remark' => '', 'list_order' => 1],
            ['id' => 5, 'name' => '菜单管理', 'icon' => 'fa-book', 'pid' => 3, 'module' => 'console', 'controller' => 'menu', 'action' => 'index', 'param' => '', 'type' => 0, 'status' => 1, 'remark' => '', 'list_order' => 1],
            ['id' => 6, 'name' => '角色管理', 'icon' => 'fa-group', 'pid' => 3, 'module' => 'console', 'controller' => 'role', 'action' => 'index', 'param' => '', 'type' => 0, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 7, 'name' => '用户管理', 'icon' => 'fa-user', 'pid' => 3, 'module' => 'console', 'controller' => 'user', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 4],
            ['id' => 8, 'name' => '添加菜单', 'icon' => 'fa-book', 'pid' => 5, 'module' => 'console', 'controller' => 'menu', 'action' => 'add', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 1],
            ['id' => 9, 'name' => '编辑菜单', 'icon' => 'fa-book', 'pid' => 5, 'module' => 'console', 'controller' => 'menu', 'action' => 'edit', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 2],
            ['id' => 10, 'name' => '删除菜单', 'icon' => 'fa-book', 'pid' => 5, 'module' => 'console', 'controller' => 'menu', 'action' => 'delete', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 3],
            ['id' => 11, 'name' => '添加角色', 'icon' => 'fa-book', 'pid' => 6, 'module' => 'console', 'controller' => 'role', 'action' => 'add', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 1],
            ['id' => 12, 'name' => '编辑角色', 'icon' => 'fa-book', 'pid' => 6, 'module' => 'console', 'controller' => 'role', 'action' => 'edit', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 2],
            ['id' => 13, 'name' => '删除角色', 'icon' => 'fa-book', 'pid' => 6, 'module' => 'console', 'controller' => 'role', 'action' => 'delete', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 3],
            ['id' => 14, 'name' => '权限设置', 'icon' => 'fa-book', 'pid' => 6, 'module' => 'console', 'controller' => 'role', 'action' => 'authorize', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 4],
            ['id' => 15, 'name' => '添加用户', 'icon' => 'fa-book', 'pid' => 7, 'module' => 'console', 'controller' => 'user', 'action' => 'add', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 1],
            ['id' => 16, 'name' => '编辑用户', 'icon' => 'fa-book', 'pid' => 7, 'module' => 'console', 'controller' => 'user', 'action' => 'edit', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 2],
            ['id' => 17, 'name' => '删除用户', 'icon' => 'fa-book', 'pid' => 7, 'module' => 'console', 'controller' => 'user', 'action' => 'delete', 'param' => '', 'type' => 1, 'status' => 0, 'remark' => '', 'list_order' => 3],
            ['id' => 18, 'name' => '数据字典', 'icon' => 'fa-database', 'pid' => 0, 'module' => 'console', 'controller' => 'dict', 'action' => 'index', 'param' => '', 'type' => 0, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 19, 'name' => '数据类型', 'icon' => 'fa-cubes', 'pid' => 18, 'module' => 'console', 'controller' => 'dict_type', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 20, 'name' => '数据管理', 'icon' => 'fa-list', 'pid' => 18, 'module' => 'console', 'controller' => 'dict', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 21, 'name' => '项目管理', 'icon' => 'fa-clipboard', 'pid' => 0, 'module' => 'console', 'controller' => 'project', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 22, 'name' => '项目线索管理', 'icon' => 'fa-exclamation-circle', 'pid' => 0, 'module' => 'console', 'controller' => 'project_clue', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 23, 'name' => '项目留言管理', 'icon' => 'fa-comments', 'pid' => 0, 'module' => 'console', 'controller' => 'message', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 24, 'name' => '客服管理', 'icon' => 'fa-comments', 'pid' => 0, 'module' => 'console', 'controller' => 'customer_service', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 25, 'name' => '客服用户', 'icon' => 'fa-user', 'pid' => 24, 'module' => 'console', 'controller' => 'customer_service', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
            ['id' => 26, 'name' => '会话管理', 'icon' => 'fa-comments', 'pid' => 24, 'module' => 'console', 'controller' => 'conversation', 'action' => 'index', 'param' => '', 'type' => 1, 'status' => 1, 'remark' => '', 'list_order' => 3],
        ];

        $this->insert('menu', $data);
    }

    public function down()
    {
        $this->dropTable('menu');
    }
}
