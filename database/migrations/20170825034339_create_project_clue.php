<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateProjectClue extends Migrator
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
        $table = $this->table('project_clue', ['engine'=>'InnoDB','comment'=>'项目线索', 'id'=>'clue_id']);
        $table->addColumn('pro_name', 'string', ['limit' => 128,  'null'=>false, 'default'=>'', 'comment'=>'项目名称'])
            ->addColumn('capital_plan', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'null'=>false, 'default'=>0, 'comment'=>'资本计划'])
            ->addColumn('financing_amount', 'decimal', ['precision' => 10, 'scale'=>2, 'null'=>false, 'default'=>0.00, 'comment'=>'融资金额'])
            ->addColumn('stock_code', 'string', ['limit' => 6, 'null'=>false, 'default'=>'', 'comment'=>'股票代码'])
            ->addColumn('company_name', 'string', ['limit' => 128,'null'=>false, 'default'=>'', 'comment'=>'企业名称'])
            ->addColumn('contacts', 'string', ['limit' => 45,'null'=>false, 'default'=>'', 'comment'=>'联系人'])
            ->addColumn('contacts_tel', 'string', ['limit' => 20,'null'=>false, 'default'=>'', 'comment'=>'联系人电话'])
            ->addColumn('contacts_email', 'string', ['limit' => 45,'null'=>false, 'default'=>'', 'comment'=>'联系人邮箱'])
            ->addColumn('introduction', 'text', ['null'=>false, 'default'=>'', 'comment'=>'项目简介'])
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('create_uid', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建人'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:0未处理;1已处理'])
            ->save();
    }
}
