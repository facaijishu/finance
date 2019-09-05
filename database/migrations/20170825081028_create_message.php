<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateMessage extends Migrator
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
        $table = $this->table('message', ['engine'=>'InnoDB','comment'=>'项目留言', 'id'=>'m_id']);
        $table->addColumn('pro_id', 'integer', ['limit' => 11,  'null'=>false, 'default'=>0, 'comment'=>'项目id'])
            ->addColumn('pid', 'integer', ['limit' => 11, 'null'=>false, 'default'=>0, 'comment'=>'父级留言'])
            ->addColumn('content', 'text', ['null'=>false, 'default'=>'', 'comment'=>'留言内容'])
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('last_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'最近更新时间'])
            ->addColumn('create_uid', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建人'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:1正常;-1删除'])
            ->save();
    }
}
