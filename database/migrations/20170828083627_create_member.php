<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateMember extends Migrator
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
        $table = $this->table('member', ['engine'=>'InnoDB','comment'=>'微信用户表', 'id'=>'uid']);
        $table->addColumn('userName', 'string', ['limit' => 128,  'null'=>false, 'default'=>'', 'comment'=>'昵称'])
            ->addColumn('openId', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'openid'])
            ->addColumn('userSex', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'null'=>false, 'default'=>0, 'comment'=>'性别'])
            ->addColumn('userPhone', 'string', ['limit' => 20,'null'=>false, 'default'=> '', 'comment'=>'电话'])
            ->addColumn('userPhoto', 'string', ['limit' => 255,'null'=>false, 'default'=> '', 'comment'=>'头像'])
            ->addColumn('userType', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=> 0, 'comment'=>'用户类型:0普通;1绑定用户;2认证用户;'])
            ->addColumn('collection', 'text', ['null'=>false, 'default'=> '', 'comment'=>'用户收藏项目id'])
            ->addColumn('customerService', 'integer', ['limit' => 11,'null'=>false, 'default'=> 0, 'comment'=>'微信用户绑定客服'])
            ->addColumn('createTime', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('lastTime', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'最近更新时间'])
            ->addColumn('lastIP', 'string', ['limit' => 54,'null'=>false, 'default'=>'', 'comment'=>'最近ip'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:1正常;-1删除'])
            ->save();
    }
}
