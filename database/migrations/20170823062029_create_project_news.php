<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateProjectNews extends Migrator
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
        $table = $this->table('project_news', ['engine'=>'InnoDB','comment'=>'项目新闻报道', 'id'=>'n_id']);
        $table->addColumn('pro_id', 'integer', ['limit' => 11,  'null'=>false, 'default'=>0, 'comment'=>'项目id'])
            ->addColumn('title', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'新闻标题'])
            ->addColumn('source', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'新闻来源'])
            ->addColumn('source_url', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'新闻来源url'])
            ->addColumn('content', 'text', ['null'=>false, 'default'=>'', 'comment'=>'内容'])
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('create_uid', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建人'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:1正常;-1不可用'])
            ->save();
    }
}
