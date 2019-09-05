<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateCustomerService extends Migrator
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
        $table = $this->table('customer_service', ['engine'=>'InnoDB','comment'=>'微信客服表', 'id'=>'cs_id']);
        $table->addColumn('kf_account', 'string', ['limit' => 45,  'null'=>false, 'default'=>'', 'comment'=>'客服账号'])
            ->addColumn('kf_headimgurl', 'string', ['limit' => 255, 'null'=>false, 'default'=>'', 'comment'=>'客服头像'])
            ->addColumn('kf_id', 'integer', ['limit' => 11, 'null'=>false, 'default'=>0, 'comment'=>'客服编号'])
            ->addColumn('kf_nick', 'string', ['limit' => 255,'null'=>false, 'default'=> '', 'comment'=>'客服昵称'])
            ->addColumn('kf_wx', 'string', ['limit' => 128,'null'=>false, 'default'=> '', 'comment'=>'客服微信号'])
            ->addColumn('kf_qr_code', 'string', ['limit' => 255,'null'=>false, 'default'=> '', 'comment'=>'客服二维码'])
            ->addColumn('count_member', 'integer', ['limit' => 11,'null'=>false, 'default'=> 0, 'comment'=>'绑定微信用户数量'])
            ->addColumn('createTime', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:1正常;-1禁止'])
            ->save();
    }
}
