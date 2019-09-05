<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateDictType extends Migrator
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
    public function change()
    {
        $table = $this->table('dict_type', ['engine'=>'InnoDB','comment'=>'数据类型', 'id'=>'dt_id']);
        $table->addColumn('type', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,  'null'=>false, 'default'=>0, 'comment'=>'数据类型选择:1.project'])
            ->addColumn('sign', 'string', ['limit' => 20, 'null'=>false, 'default'=>'', 'comment'=>'数据类型标签'])
            ->addColumn('name', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'数据类型名称'])
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('create_uid', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建人'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:1正常;-1不可用'])
            ->save();
    }
}
