<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateRoleUser extends Migrator
{
    public function up()
    {
        // 创建角色与用户关系表
        $table = $this->table('role_user', ['engine'=>'InnoDB','comment'=>'角色信息列表']);
        $table->addColumn('uid', 'integer', ['limit' => 11,  'null'=>false, 'comment'=>'用户ID'])
            ->addColumn('role_id', 'integer', ['limit' => 11, 'null'=>false, 'comment'=>'角色ID'])
            ->addColumn('dept_id', 'integer', ['limit' => 11, 'null'=>false, 'comment'=>'部门ID'])
            ->save();

        //插入基本数据
        $data = [
            ['uid'=>1, 'role_id'=>1],
        ];

        $this->insert('role_user', $data);
    }

    public function down()
    {
        $this->dropTable('role_user');
    }
}
