<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateRoleAccess extends Migrator
{
    public function up()
    {
        $table = $this->table('role_access', ['engine'=>'InnoDB','comment'=>'角色权限表']);
        $table->addColumn('role_id', 'integer', ['limit' => 11,  'null'=>false, 'default'=>0, 'comment'=>'角色id'])
            ->addColumn('menu_id', 'integer', ['limit' => 11, 'null'=>false, 'default'=>0, 'comment'=>'权限或菜单ID'])
            ->addColumn('type', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null'=>false, 'default'=>0, 'comment'=>'0:菜单项；1:权限项'])
            ->save();
    }

    public function down()
    {
        $this->dropTable('role_access');
    }
}
