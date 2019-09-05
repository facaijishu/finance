<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateRole extends Migrator
{
    public function up()
    {
        // 创建角色表
        $table = $this->table('role', ['engine'=>'InnoDB','comment'=>'角色信息列表']);
        $table->addColumn('name', 'string', ['limit' => 128,  'null'=>false, 'default'=>'', 'comment'=>'角色名称'])
            ->addColumn('pid', 'integer', ['limit' => 11, 'null'=>false, 'default'=>0, 'comment'=>'父角色ID'])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY,  'null'=>false, 'default'=>0, 'comment'=>'状态'])
            ->addColumn('remark', 'string', ['limit' => 255, 'null'=>false, 'default'=>'', 'comment'=>'备注'])
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('update_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'更新时间'])
            ->addColumn('list_order', 'integer', ['limit' => 11, 'null'=>false, 'default'=>100, 'comment'=>'排序字段'])
            ->addIndex(['name'], ['name' => 'index_name', 'type' => 'normal'])
            ->save();

        //插入基本数据
        $data = [
            ['id'=>1, 'name'=>'超级管理员', 'pid'=>0, 'status'=>1, 'remark'=>'拥有系统最高管理员权限！', 'create_time'=>1329633709, 'update_time'=>1329633709, 'list_order'=>100],
        ];

        $this->insert('role', $data);
    }

    public function down()
    {
        $this->dropTable('role');
    }
}
