<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateProjectQa extends Migrator
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
        $table = $this->table('project_qa', ['engine'=>'InnoDB','comment'=>'项目问答表', 'id'=>'qa_id']);
        $table->addColumn('pro_id', 'integer', ['limit' => 11,  'null'=>false, 'default'=>0, 'comment'=>'项目id'])
            ->addColumn('type', 'integer', ['limit' => 11, 'null'=>false, 'default'=>0, 'comment'=>'问答类型'])
            ->addColumn('question', 'string', ['limit' => 255, 'null'=>false, 'default'=>'', 'comment'=>'问题'])
            ->addColumn('answer', 'text', ['null'=>false, 'default'=>'', 'comment'=>'答案'])
            ->addColumn('hide', 'integer', ['limit' => Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'null'=>false, 'default'=>0, 'comment'=>'是否隐藏:0否;1是'])
            ->addColumn('hot', 'integer', ['limit' => Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'null'=>false, 'default'=>0, 'comment'=>'是否置顶:0否;1是'])
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('create_uid', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建人'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:1正常;-1不可用'])
            ->save();
    }
}
