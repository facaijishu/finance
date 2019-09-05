<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateProject extends Migrator
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
        $table = $this->table('project', ['engine'=>'InnoDB','comment'=>'项目表', 'id'=>'pro_id']);
        $table->addColumn('pro_name', 'string', ['limit' => 128,  'null'=>false, 'default'=>'', 'comment'=>'项目名称'])
            ->addColumn('company_name', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'企业名称'])
            ->addColumn('stock_code', 'string', ['limit' => 6,  'null'=>false, 'default'=>'','comment'=>'股票编码'])
            ->addColumn('stock_name', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'股票名称'])
            ->addColumn('inc_industry', 'string', ['limit' => 128, 'null'=>false, 'default'=>'', 'comment'=>'所属行业'])
            ->addColumn('inc_area', 'string', ['limit' => 255, 'null'=>false, 'default'=>'', 'comment'=>'所属地域'])
            ->addColumn('inc_sign', 'string', ['limit' => 255, 'null'=>false, 'default'=>'', 'comment'=>'所属标签'])
            ->addColumn('introduction', 'string', ['limit' => 255,'null'=>false, 'default'=>'', 'comment'=>'简介'])
            ->addColumn('organization', 'string', ['limit' => 255,'null'=>false, 'default'=>'', 'comment'=>'辅导机构'])
            ->addColumn('capital_plan', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'资本计划:定增融资、股权质押、找做市商、接收并购'])
            ->addColumn('financing_amount', 'decimal', ['precision' => 10, 'scale'=>2,'null'=>false, 'default'=>0.00, 'comment'=>'融资金额'])
            ->addColumn('corporate_valuation', 'decimal', ['precision' => 10, 'scale'=>2,'null'=>false, 'default'=>0.00, 'comment'=>'公司估值'])
            ->addColumn('net_profit', 'decimal', ['precision' => 10, 'scale'=>2,'null'=>false, 'default'=>0.00, 'comment'=>'净利润'])
            ->addColumn('annual_revenue', 'decimal', ['precision' => 10, 'scale'=>2,'null'=>false, 'default'=>0.00, 'comment'=>'年营业收入'])
            ->addColumn('total_capital', 'decimal', ['precision' => 10, 'scale'=>2,'null'=>false, 'default'=>0.00, 'comment'=>'总股本'])
            ->addColumn('build_time', 'integer', [ 'limit'=>11,'null'=>false, 'default'=>0, 'comment'=>'成立时间'])
            ->addColumn('transfer_type', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'转让类型:协议、做市'])
            ->addColumn('hierarchy', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'层级:创新层、基础层、拟上市'])
            ->addColumn('rebate', 'decimal', ['precision' => 4, 'scale'=>2,'null'=>false, 'default'=>0.00, 'comment'=>'返点'])
            ->addColumn('contacts', 'string', ['limit' => 45,'null'=>false, 'default'=>'', 'comment'=>'联系人'])
            ->addColumn('contacts_tel', 'string', ['limit' => 20,'null'=>false, 'default'=>'', 'comment'=>'联系人电话'])
            ->addColumn('contacts_email', 'string', ['limit' => 45,'null'=>false, 'default'=>'', 'comment'=>'联系人邮箱'])
            ->addColumn('top_img', 'string', ['limit' => 128,'null'=>false, 'default'=>'', 'comment'=>'头图'])
            ->addColumn('roadshow_url', 'string', ['limit' => 128,'null'=>false, 'default'=>'', 'comment'=>'路演视频地址'])
            ->addColumn('investment_lights', 'text', ['null'=>false, 'default'=>'', 'comment'=>'投资亮点'])
            ->addColumn('business_plan', 'string', ['limit'=>128,'null'=>false, 'default'=>'', 'comment'=>'商业计划书'])
            ->addColumn('factor_table', 'string', ['limit'=>128,'null'=>false, 'default'=>'', 'comment'=>'要素表'])
            ->addColumn('company_video', 'string', ['limit'=>128,'null'=>false, 'default'=>'', 'comment'=>'企业视频'])
                
            ->addColumn('enterprise_url', 'string', ['limit'=>128,'null'=>false, 'default'=>'', 'comment'=>'企业亮点']) 
            ->addColumn('company_url', 'string', ['limit'=>128,'null'=>false, 'default'=>'', 'comment'=>'公司简介']) 
            ->addColumn('product_url', 'string', ['limit'=>128,'null'=>false, 'default'=>'', 'comment'=>'核心产品']) 
            ->addColumn('analysis_url', 'string', ['limit'=>128,'null'=>false, 'default'=>'', 'comment'=>'行业分析']) 
            ->addColumn('enterprise_des', 'text', ['null'=>false, 'default'=>'', 'comment'=>'企业亮点描述']) 
            ->addColumn('company_des', 'text', ['null'=>false, 'default'=>'', 'comment'=>'公司简介描述']) 
            ->addColumn('product_des', 'text', ['null'=>false, 'default'=>'', 'comment'=>'核心产品描述']) 
            ->addColumn('analysis_des', 'text', ['null'=>false, 'default'=>'', 'comment'=>'行业分析描述']) 
                
            ->addColumn('create_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建时间'])
            ->addColumn('last_time', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'上次更新时间'])
            ->addColumn('create_uid', 'integer', ['limit' => 11,'null'=>false, 'default'=>0, 'comment'=>'创建人'])
            ->addColumn('status', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'null'=>false, 'default'=>0, 'comment'=>'状态:1草稿;2融资中;3结束'])
            ->addIndex(['stock_code'], ['type' => 'normal'])
            ->save();
    }
}
