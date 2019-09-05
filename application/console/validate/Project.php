<?php

namespace app\console\validate;

use think\Validate;

class Project extends Validate
{
    protected $rule = [
        ['pro_name', 'require', '请输入项目名称'],
        ['company_name', 'require', '请输入企业名称'],
        ['stock_name', 'require', '请输入股票名称'],
        ['stock_code', 'require|length:6,6', '请输入股票代码|请输入正确的股票代码'],
        ['organization', 'require', '请输入辅导机构'],
        ['inc_industry', 'require', '请选择所属行业'],
        ['inc_area', 'require', '请选择所属地域'],
        ['inc_sign', 'require', '请选择所属业务'],
//        ['financing_amount', 'require|number', '请输入融资金额|请输入正确的融资金额'],
        ['corporate_valuation', 'require|number', '请输入公司估值|请输入正确的公司估值'],
        ['net_profit', 'require|number', '请输入净利润|请输入正确的净利润'],
        ['annual_revenue', 'require|number', '请输入年营业收入|请输入正确的年营业收入'],
        ['total_capital', 'require|number', '请输入总股本|请输入正确的总股本'],
        ['build_time', 'require|length:10,10', '请输入成立时间|请输入正确的成立时间'],
//        ['rebate', 'require|number', '请输入项目返点|请输入正确的项目返点'],
        ['contacts', 'require', '请输入联系人'],
        ['contacts_tel', 'require', '请输入联系人电话'],
        ['contacts_email', 'require', '请输入联系人邮箱'],
        ['introduction', 'require', '请输入简介'],
        ['investment_lights', 'require', '请输入投资亮点'],
        ['enterprise_url', 'require', '请添加企业亮点图片'],
        ['enterprise_des', 'require', '请输入企业亮点描述'],
        ['company_url', 'require', '请添加公司简介图片'],
        ['company_des', 'require', '请输入公司简介描述'],
        ['product_url', 'require', '请添加核心产品图片'],
        ['product_des', 'require', '请输入核心产品描述'],
        ['analysis_url', 'require', '请添加行业分析图片'],
        ['analysis_des', 'require', '请输入行业分析描述'],
    ];

    protected $scene = [
        'add'   => ['pro_name' , 'company_name' , 'stock_name' , 'stock_code' , 'organization' , 'inc_industry' , 'inc_area' , 'inc_sign' , 'corporate_valuation' , 'net_profit' , 'annual_revenue' , 'total_capital' , 'build_time'  , 'contacts' , 'contacts_tel' , 'contacts_email' , 'introduction' , 'investment_lights'],
        'edit'  => ['pro_name' , 'company_name' , 'stock_name' , 'stock_code' , 'organization' , 'inc_industry' , 'inc_area' , 'inc_sign' , 'corporate_valuation' , 'net_profit' , 'annual_revenue' , 'total_capital' , 'build_time'  , 'contacts' , 'contacts_tel' , 'contacts_email' , 'introduction' , 'investment_lights'],
    ];
}