<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateThrough extends Migrator
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
        $table = $this->table('through', ['engine'=>'InnoDB','comment'=>'微信用户认证表', 'id'=>'t_id']);
        $table->addColumn('uid', 'integer', ['limit' => 11,  'null'=>false, 'default'=>0, 'comment'=>'用户id'])
            ->addColumn('realName', 'string', ['limit' => 45, 'null'=>false, 'default'=>'', 'comment'=>'姓名'])
            ->addColumn('phone', 'string', ['limit' => 45, 'null'=>false, 'default'=>'', 'comment'=>'电话'])
            ->addColumn('email', 'string', ['limit' => 128,'null'=>false, 'default'=> '', 'comment'=>'邮箱'])
            ->addColumn('position', 'string', ['limit' => 128,'null'=>false, 'default'=> '', 'comment'=>'职位'])
            ->addColumn('company', 'string', ['limit' => 128,'null'=>false, 'default'=> '', 'comment'=>'公司'])
            ->addColumn('card', 'string', ['limit' => 128,'null'=>false, 'default'=> '', 'comment'=>'名片'])
            ->addColumn('createTime', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('updateTime', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'更新时间'])
            ->addColumn('throughTime', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'通过审核时间'])
            ->addColumn('lastTime', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'上次登录时间'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:0未审核;1审核通过;-1审核未通过'])
            ->save();
    }
}
