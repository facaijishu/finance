<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateUser extends Migrator
{

    public function up()
    {
        // 创建用户表
        $table = $this->table('user', ['engine'=>'InnoDB','comment'=>'用户表', 'id'=>'uid']);
        $table->addColumn('login_name', 'string', ['limit' => 128,  'null'=>false, 'default'=>'', 'comment'=>'用户名称'])
            ->addColumn('login_pwd', 'string', ['limit' => 255, 'null'=>false, 'default'=>'', 'comment'=>'用户登录密码'])
            ->addColumn('salt', 'string', ['limit' => 32,  'null'=>false, 'default'=>0, 'comment'=>'密码加盐值'])
            ->addColumn('email', 'string', ['limit' => 255, 'null'=>false, 'default'=>'', 'comment'=>'用户邮箱'])
            ->addColumn('mobile', 'string', ['limit' => 11, 'null'=>false, 'default'=>'', 'comment'=>'用户手机号'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'null'=>false, 'default'=>'', 'comment'=>'头像'])
            ->addColumn('nickname', 'string', ['limit' => 128,'null'=>false, 'default'=>'', 'comment'=>'用户昵称'])
            ->addColumn('real_name', 'string', ['limit' => 128,'null'=>false, 'default'=>0, 'comment'=>'真实姓名'])
            ->addColumn('remark', 'string', ['limit' => 255,'null'=>false, 'default'=>0, 'comment'=>'备注'])
            ->addColumn('user_type', 'string', ['limit' => 32,'null'=>false, 'default'=>'', 'comment'=>'用户类型'])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'用户状态。0：禁止；1：正常'])
            ->addColumn('create_ip', 'string', ['limit' => 16,'null'=>false, 'default'=>'', 'comment'=>'用户创建IP'])
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'用户创建时间'])
            ->addColumn('update_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'用户更新时间'])
            ->addColumn('last_login_ip', 'string', ['limit' => 16,'null'=>false, 'default'=>'', 'comment'=>'上次登录IP'])
            ->addColumn('last_login_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'上次登录时间'])
            ->addColumn('login_count', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'登录次数'])
            ->addColumn('default_role_id', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'默认角色ID'])
            ->addIndex(['login_name'], ['name' => 'index_login_name', 'type' => 'normal'])
            ->save();

        //插入基本数据
        $data = [
            ['uid'=>1, 'login_name'=>'admin', 'login_pwd'=>'16032bea0af6cd4a8ea500d55408602a', 'salt'=>'aabb', 'email'=>'', 'mobile'=>'', 'avatar'=>'', 'nickname'=>'', 'real_name'=>'', 'remark'=>'', 'user_type'=>'', 'status'=>1, 'create_ip'=>'127.0.0.1', 'create_time'=>0, 'update_time'=>0, 'last_login_ip'=>'127.0.0.1', 'last_login_time'=>'1497067254', 'login_count'=>0, 'default_role_id'=>0],
        ];

        $this->insert('user', $data);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
