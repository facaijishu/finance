<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddDictTypeData extends Migrator
{
    public function up()
    {
        //添加数据类型菜单
        $data = [
            ['type' => 'project', 'sign' => 'industry', 'name' => '所属行业', 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['type' => 'project', 'sign' => 'section', 'name' => '所属地域', 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['type' => 'project', 'sign' => 'capital_plan', 'name' => '资本计划', 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['type' => 'project', 'sign' => 'transfer_type', 'name' => '转让类型', 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['type' => 'project', 'sign' => 'hierarchy', 'name' => '所属层级', 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['type' => 'project', 'sign' => 'qa_type', 'name' => '项目问答类型', 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['type' => 'project', 'sign' => 'sign', 'name' => '所属标签', 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
        ];
        $this->insert('dict_type', $data);
        
    }

    public function down()
    {
        $table_prefix = $this->getTablePrefix();
        $this->execute(sprintf('DELETE FROM %s WHERE sign="industry" and name="所属行业"', $table_prefix.'dict_type'));
        $this->execute(sprintf('DELETE FROM %s WHERE sign="section" and name="所属地域"', $table_prefix.'dict_type'));
        $this->execute(sprintf('DELETE FROM %s WHERE sign="capital_plan" and name="资本计划"', $table_prefix.'dict_type'));
        $this->execute(sprintf('DELETE FROM %s WHERE sign="transfer_type" and name="转让类型"', $table_prefix.'dict_type'));
        $this->execute(sprintf('DELETE FROM %s WHERE sign="hierarchy" and name="所属层级"', $table_prefix.'dict_type'));
        $this->execute(sprintf('DELETE FROM %s WHERE sign="qa_type" and name="项目问答类型"', $table_prefix.'dict_type'));
        $this->execute(sprintf('DELETE FROM %s WHERE sign="sign" and name="所属标签"', $table_prefix.'dict_type'));
    }

    public function getTablePrefix()
    {
        return $this->getAdapter()->getOptions()['table_prefix'];
    }
}
