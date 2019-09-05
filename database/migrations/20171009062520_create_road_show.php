<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateRoadShow extends Migrator
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
        $table = $this->table('road_show', ['engine'=>'InnoDB','comment'=>'路演表', 'id'=>'rs_id']);
        $table->addColumn('road_name', 'string', ['limit' => 128,  'null'=>false, 'default'=>'', 'comment'=>'路演项目名称'])
            ->addColumn('company_name', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'企业名称'])
            ->addColumn('type', 'integer', ['limit' => 11,  'null'=>false, 'default'=>0,'comment'=>'形式'])
            ->addColumn('road_introduce', 'text', ['null'=>false, 'default'=>'', 'comment'=>'路演项目简介'])
            ->addColumn('speaker_introduce', 'text', ['null'=>false, 'default'=>'', 'comment'=>'主讲人项目简介'])
            ->addColumn('review', 'text', ['null'=>false, 'default'=>'', 'comment'=>'机构点评'])
            ->addColumn('top_img', 'string', ['limit' => 128,'null'=>false, 'default'=>'', 'comment'=>'头图'])
            ->addColumn('video', 'string', ['limit' => 128,'null'=>false, 'default'=>'', 'comment'=>'路演视频'])
            ->addColumn('duration', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'时长'])
            ->addColumn('video_url', 'string', ['limit' => 255,'null'=>false, 'default'=>'', 'comment'=>'路演地址'])
            ->addColumn('content', 'text', ['null'=>false, 'default'=>'', 'comment'=>'内容'])
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('create_uid', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建人'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:1启用;-1停用'])
            ->save();
    }
}
