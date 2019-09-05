<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddDictData extends Migrator
{
    public function up()
    {
        //添加数据菜单
        $data = [
            ['dt_id' => 1, 'value' => '行业1', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 2, 'value' => '地域1', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 3, 'value' => '定增融资', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 3, 'value' => '股权质押', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 3, 'value' => '找做市商', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 3, 'value' => '接收并购', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 4, 'value' => '协议', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 4, 'value' => '做市', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 5, 'value' => '创新层', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 5, 'value' => '基础层', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 5, 'value' => '拟上市', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 6, 'value' => '项目问题', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 6, 'value' => '流程问题', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 7, 'value' => '精选', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 7, 'value' => '优质', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
            ['dt_id' => 7, 'value' => '创新', 'des' => '','list_order' => 0, 'create_time' => 1503285068 , 'create_uid' => 1 , 'status' => 1],
        ];
        $this->insert('dict', $data);
        
    }

    public function down()
    {
        $table_prefix = $this->getTablePrefix();
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=1 and name="行业1"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=2 and name="地域1"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=3 and name="定增融资"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=3 and name="股权质押"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=3 and name="找做市商"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=3 and name="接收并购"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=4 and name="协议"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=4 and name="做市"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=5 and name="创新层"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=5 and name="基础层"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=5 and name="拟上市"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=6 and name="项目问题"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=6 and name="流程问题"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=7 and name="精选"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=7 and name="优质"', $table_prefix.'dict'));
        $this->execute(sprintf('DELETE FROM %s WHERE dt_id=7 and name="创新"', $table_prefix.'dict'));
    }

    public function getTablePrefix()
    {
        return $this->getAdapter()->getOptions()['table_prefix'];
    }
}
