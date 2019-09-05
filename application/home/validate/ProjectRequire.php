<?php

namespace app\home\validate;

use think\Validate;

class ProjectRequire extends Validate
{   
	//根据发布项目不同的一二级业务组合,有不同的字段验证
    public function __construct($top_dict_value = '',$dict_value = '')
    {  
           switch ($top_dict_value) {
           	case '新三板':

           		if ($dict_value == '定向增发') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['price','check_price:1'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['financing_purpose','check_financing_purpose:1'],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];

                    $data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','price','pe_ratio','ly_net_profit','ty_net_profit','financing_purpose','pro_highlight'];
           		} elseif ($dict_value == '大宗交易') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ["general_capital","require|check_general_capital:1","请输入总股本"],
                        ["trans_stock_num","require|check_trans_stock_num:1","请输入可转让股数"],
                        ["price","check_price2:1"],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];
           			$data = ['name','business_des','stock_code','province_id','top_industry_id','general_capital','trans_stock_num','price','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight'];
           		} elseif ($dict_value == '股权质押') {
                   $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['ltv_ratio','check_ltv_ratio:1'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                        
                      ]; 
           		   $data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','ltv_ratio','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight'];
           		} 
           		/*elseif ($dict_value == '资产抵押') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['mortgage_rate','check_mortgage_rate:1'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],                       
                        ['pro_highlight','require|check_pro_highlight2:1','请输入资产说明'],
                        
                      ];
           		   $data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','mortgage_rate','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight'];
           		} elseif ($dict_value == 'Pre-IPO') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['mortgage_rate','check_mortgage_rate:1'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],                       
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                        ['financing_purpose','check_financing_purpose2:1'], 
                        
                      ];
           		   $data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight','financing_purpose'];
           		} */
           		elseif ($dict_value == '壳交易') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['general_capital','require|check_general_capital:1','请输入总股本'],
                        ["trans_stock_num","require|check_trans_stock_num2:1","请输入可出售股数"],
                        ['price','check_price4:1'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],                       
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                           
                      ];
           		   $data = ['name','business_des','stock_code','province_id','top_industry_id','general_capital','trans_stock_num','price','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight'];
           		} 
           		/*
           		 elseif ($dict_value == '并购') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['sell_stock_min','require|<=:sell_stock_max|check_sell_stock:1','请输入拟出让股份最小值|拟出让股份最小值必须小于等于最大值'],
                        ['sell_stock_max','require|check_sell_stock:1','请输入拟出让股份最大值'],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                                                   
                      ];
           		   $data = ['name','business_des','stock_code','province_id','top_industry_id','sell_stock_min','sell_stock_max','ly_net_profit','ty_net_profit','pro_highlight'];
           		} */
           		else{
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                        ['financing_purpose','check_financing_purpose:1'],
                        
                      ];
                   $data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight','financing_purpose'];           			
           		} 
           		
           		break;
           	case '股权':
           	    /*if ($dict_value == '科创板') {
           	        $sp_rule = [
           	            ["name","require|max:20","请输入名称|名称不能超过20个字符"],
           	            ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
           	            ["province_id","require","请选择所在省份"],
           	            ["top_industry_id","require","请选择行业"],
           	            ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
           	            ['sell_stock_min','require|<=:sell_stock_max|check_sell_stock:1','请输入拟出让股份最小值|拟出让股份最小值必须小于等于最大值'],
           	            ['sell_stock_max','require|check_sell_stock:1','请输入拟出让股份最大值'],
           	            ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
           	            ["ty_net_profit","check_ty:1"],
           	            ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
           	            
           	        ];
           	        $data = ['name','business_des','province_id','top_industry_id','financing_scale','ly_net_profit','ty_net_profit','pro_highlight'];
           	    }*/
           		if ($dict_value == '老股转让') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                        ["top_industry_id","require","请选择行业"],
                        ['price','check_price4:1'],
                        ['trans_stock_num',"require|check_trans_stock_num:1","请输入可转让股数"],
                        ['valuation',"require|check_valuation:1","请输入本轮估值"],
                                                   
                      ];
                    $data = ['name','business_des','province_id','top_industry_id','trans_stock_num','valuation','price','ly_net_profit','ty_net_profit','pro_highlight'];
           		} elseif ($dict_value == 'VC') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['financing_purpose','check_financing_purpose:1'],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale','pe_ratio','ly_net_profit','ty_net_profit','financing_purpose','pro_highlight'];
           		} elseif ($dict_value == 'PE') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['financing_purpose','check_financing_purpose:1'],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale','pe_ratio','ly_net_profit','ty_net_profit','financing_purpose','pro_highlight'];
           		} elseif ($dict_value == 'Pre-IPO') {
                     $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['financing_purpose','check_financing_purpose:1'],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale','pe_ratio','ly_net_profit','ty_net_profit','financing_purpose','pro_highlight'];
           		} elseif ($dict_value == '并购') {
                     $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['sell_stock_min','require|<=:sell_stock_max|check_sell_stock:1','请输入拟出让股份最小值|拟出让股份最小值必须小于等于最大值'],
                        ['sell_stock_max','require|check_sell_stock:1','请输入拟出让股份最大值'],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                         ['financing_purpose','check_financing_purpose:1'],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale','sell_stock_min','sell_stock_max','ly_net_profit','ty_net_profit','pro_highlight'];
           		} else{
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['sell_stock_min','require|<=:sell_stock_max|check_sell_stock:1','请输入拟出让股份最小值|拟出让股份最小值必须小于等于最大值'],
                        ['sell_stock_max','require|check_sell_stock:1','请输入拟出让股份最大值'],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale','sell_stock_min','sell_stock_max','ly_net_profit','ty_net_profit','pro_highlight'];
           		}
           		
           		break;
           	case '债权':
           		if ($dict_value == '信贷') {
                     $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['rate_cap','check_rate_cap:1'],
                        ['financing_purpose','check_financing_purpose2:1'],
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale','rate_cap','financing_purpose','pay_source'];
           		} 
           		/*elseif ($dict_value == '信托') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['rate_cap','check_rate_cap:1'],
                        ['financing_purpose','check_financing_purpose2:1'],
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale',
           			'rate_cap','financing_purpose','pay_source'];
           		} elseif ($dict_value == '资管') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['rate_cap','check_rate_cap:1'],
                        ['financing_purpose','check_financing_purpose2:1'],
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale',
           			'rate_cap','financing_purpose','pay_source'];
           		} elseif ($dict_value == '可转债/可交债') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['rate_cap','check_rate_cap:1'],
                        ['financing_purpose','check_financing_purpose2:1'],
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale',
           			'rate_cap','financing_purpose','pay_source'];
           		} */
           		elseif ($dict_value == '融资租赁') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['rate_cap','check_rate_cap:1'],
                        ['business_income','require|check_business_income:1','请输入营业收入(去年)'],
                        ['debt_ratio','check_debt_ratio:1'],
                        ['financing_purpose','check_financing_purpose2:1'],
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale',
           			'rate_cap','business_income','debt_ratio','financing_purpose','pay_source'];
           		} 
           		/*
           		elseif ($dict_value == '股权质押') {
                     $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['ltv_ratio','check_ltv_ratio:1'],                        
                        ['pe_ratio','check_pe_ratio:1'],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['financing_purpose','check_financing_purpose2:1'],
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           	        $data = ['name','business_des','province_id','top_industry_id','financing_scale',
                    'ltv_ratio','pe_ratio','ly_net_profit','ty_net_profit','financing_purpose','pay_source'];
           		} */
           		elseif ($dict_value == '资产证券化') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['rate_cap','check_rate_cap:1'],                        
                        ['financing_purpose','check_financing_purpose2:1'],
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale',
                    'rate_cap','financing_purpose','pay_source'];
           		} elseif ($dict_value == '商业保理') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['rate_cap','check_rate_cap:1'],
                        ['business_income','require|check_business_income:1','请输入营业收入(去年)'],   
                        ['debt_ratio','check_debt_ratio:1'],                     
                        ['financing_purpose','check_financing_purpose2:1'],
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale',
                    'rate_cap','business_income','debt_ratio','financing_purpose','pay_source'];
           		} elseif ($dict_value == '票据业务') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['coupon_finance','require|check_coupon_finance:1','请输入票面金额'],
                        ['days_remain','require|check_days_remain:1','请输入剩余天数'],
                        ['price','require|check_price5:1','请输入最低售票价格'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','coupon_finance','days_remain','price'];
           		} else{
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['rate_cap','check_rate_cap:1'],
                        ['financing_purpose','check_financing_purpose2:1'],                        
                        ['pay_source','require|check_pay_source:1','请输入还款来源'],
                                                   
                      ];
           			$data = ['name','business_des','province_id','top_industry_id','financing_scale','rate_cap','financing_purpose','pay_source'];
           		}
           		break;
           	case '创业孵化':
           		 $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale4:1','请输入所需资金'],
                        ['remain_period','check_days_remain2:1'],
                        ['expected_return','check_expected_return:1'],                        
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],                                                   
                      ];
                $data = ['name','business_des','province_id','top_industry_id','financing_scale','remain_period','expected_return','pro_highlight'];   
               
           		break;
           	case '上市公司':
           		if ($dict_value == '定向增发') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['price','check_price:1'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['financing_purpose','check_financing_purpose:1'],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];
           			$data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','price','pe_ratio','ly_net_profit','ty_net_profit','financing_purpose','pro_highlight'];
           		} elseif ($dict_value == '大宗交易') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ["price","check_price3:1"],
                        ['discount_rate','require|check_discount_rate:1','请输入折扣率'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];
           			$data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','price','discount_rate','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight'];
           		} elseif ($dict_value == '股权质押') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['ltv_ratio','check_ltv_ratio:1'],
                        ['rate_cap','check_rate_cap:1'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['financing_purpose','check_financing_purpose:1'],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];
           			$data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','ltv_ratio','rate_cap','pe_ratio','ly_net_profit','ty_net_profit','financing_purpose','pro_highlight'];
           		} 
           		/*elseif ($dict_value == '壳交易') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ['sell_stock_min','require|<=:sell_stock_max|check_sell_stock:1','请输入拟出让股份最小值|拟出让股份最小值必须小于等于最大值'],
                        ['sell_stock_max','require|check_sell_stock:1','请输入拟出让股份最大值'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['financing_purpose','check_financing_purpose:1'],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];
           			$data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','sell_stock_min','sell_stock_max','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight'];
           		} */
           		elseif ($dict_value == '市值管理') {
                     $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];
           			$data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight'];
           		} elseif ($dict_value == '并购重组') {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['sell_stock_min','require|<=:sell_stock_max|check_sell_stock:1','请输入拟出让股份最小值|拟出让股份最小值必须小于等于最大值'],
                        ['sell_stock_max','require|check_sell_stock:1','请输入拟出让股份最大值'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];
           			$data = ['name','business_des','stock_code','province_id','top_industry_id','sell_stock_min','sell_stock_max','ly_net_profit','ty_net_profit','pro_highlight'];
           		} else {
                    $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["stock_code","require|number|length:6","请输入股票代码|股票代码必须是数字|股票代码长度为6位"],
                        ["province_id","require","请选择所在省份"],
                        ["top_industry_id","require","请选择行业"],
                        ['financing_scale','require|check_financing_scale:1','请输入交易规模'],
                        ["pe_ratio","check_pe_ratio:1"],
                        ["ly_net_profit","require|check_ly:1",'请输入去年净利润'],
                        ["ty_net_profit","check_ty:1"],
                        ['pro_highlight','require|check_pro_highlight:1','请输入项目说明'],
                      ];
           			$data = ['name','business_des','stock_code','province_id','top_industry_id','financing_scale','pe_ratio','ly_net_profit','ty_net_profit','pro_highlight'];
           		}
           		break;
           	case '金融产品':
           	    if ($dict_value == 'S基金') {
           	        $sp_rule = [
           	            ["name","require|max:20","请输入名称|名称不能超过20个字符"],
           	            ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
           	            ["province_id","require","请选择所在省份"],
           	            ["top_industry_id","require","请选择行业"],
           	            ["product_issuer","require|max:20","请输入发行机构|发行机构不能超过20个字符"],
           	            ['sub_amount','require|check_sub_amount:1','请输入认购金额'],
           	            ['financing_scale','require|check_financing_scale3:1','请输入产品规模'],
           	            ['pro_highlight','require|check_pro_highlight3:1','请输入产品说明'],
           	            ['price','check_price4:1'],
           	            ['valuation',"require|check_valuation:1","请输入本轮估值"],
           	        ];
           	        $data = ['name','business_des','province_id','top_industry_id','product_issuer','sub_amount','financing_scale','pro_highlight','price','valuation'];
           	    }else{
           	        $sp_rule = [
           	            ["name","require|max:20","请输入名称|名称不能超过20个字符"],
           	            ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
           	            ["province_id","require","请选择所在省份"],
           	            ["top_industry_id","require","请选择行业"],
           	            ["product_issuer","require|max:20","请输入发行机构|发行机构不能超过20个字符"],
           	            ['sub_amount','require|check_sub_amount:1','请输入认购金额'],
           	            ['financing_scale','require|check_financing_scale3:1','请输入产品规模'],
           	            ['pro_highlight','require|check_pro_highlight3:1','请输入产品说明'],
           	        ];
           	        $data = ['name','business_des','province_id','top_industry_id','product_issuer','sub_amount','financing_scale','pro_highlight'];
           	    }
                
           		break;
       		/*case '配资业务':
                $sp_rule = [
                        ["name","require|max:20","请输入名称|名称不能超过20个字符"],
                        ["business_des","require|max:50","请输入简介|简介不能超过50个字符"],
                        ["province_id","require","请选择所在省份"],
                        ['financing_scale','require|check_financing_scale5:1','请输入投入本金'],
                        ['allocation_ratio','require|check_allocation_ratio:1','请输入配资比例'],
                        ['total_trade_num','check_ttn:1'],
                        ['trade_period','require|check_trade_period:1','请输入操盘周期'],
                        ['mirc','check_mirc:1'],
                      ];
       		    $data = ['name','business_des','province_id','financing_scale','allocation_ratio','total_trade_num','trade_period','mirc'];
                break; */           	
           	default:
           		 $data = [];
           		break;
           }

          $this->rule = $sp_rule;
          $this->scene = ['save'=>$data];
           
    }
  
    //自定义验证交易规模(万元)
    protected function check_financing_scale($value)
    {
       if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
       	   return '交易规模最多为10位数字,最多保留两位小数';
       }
       return true;
    }
   
    //自定义验证产品规模(万元)
    protected function check_financing_scale3($value)
    {
       if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
           return '产品规模最多为10位数字,最多保留两位小数';
       }
       return true;
    }
    //自定义验证所需资金(万元)
    protected function check_financing_scale4($value)
    {
       if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
           return '所需资金最多为10位数字,最多保留两位小数';
       }
       return true;
    }
    //自定义验证投入本金(万元)
    protected function check_financing_scale5($value)
    {
       if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
           return '投入本金最多为10位数字,最多保留两位小数';
       }
       return true;
    }
    //自定义验证融资用途
   protected function check_financing_purpose($value)
   {     
         $value = trim($value);
         if (empty($value)) {
             return true;
         } 
         if (mb_strlen($value,'utf8')<10 || mb_strlen($value,'utf8')>1000) {
             return '融资用途字数必须在10~1000个之间';
         } else {
             return true;
         }
         
         
   } 
   //自定义验证融资说明 
   protected function check_financing_purpose2($value)
   {     
         $value = trim($value);
         if (empty($value)) {
             return true;
         } 
         if (mb_strlen($value,'utf8')<10 || mb_strlen($value,'utf8')>1000) {
             return '融资说明字数必须在10~1000个之间';
         } else {
             return true;
         }
         
         
   } 
   
   //自定义验证项目说明
   protected function check_pro_highlight($value)
   {     
         $value = trim($value);
         if (empty($value)) {
             return '请输入项目说明';
         } 
         if (mb_strlen($value,'utf8')<10 || mb_strlen($value,'utf8')>1000) {
             return '项目说明字数必须在10~1000个之间';
         } else {
             return true;
         }
         
         
   } 
   //自定义验证资产说明
   protected function check_pro_highlight2($value)
   {     
         $value = trim($value);
         if (empty($value)) {
             return '请输入资产说明';
         } 
         if (mb_strlen($value,'utf8')<10 || mb_strlen($value,'utf8')>1000) {
             return '资产说明字数必须在10~1000个之间';
         } else {
             return true;
         }
         
         
   } 
    //自定义验证产品说明
   protected function check_pro_highlight3($value)
   {     
         $value = trim($value);
         if (empty($value)) {
             return '请输入产品说明';
         } 
         if (mb_strlen($value,'utf8')<10 || mb_strlen($value,'utf8')>1000) {
             return '产品说明字数必须在10~1000个之间';
         } else {
             return true;
         }
         
         
   } 

    //自定义验证还款来源
   protected function check_pay_source($value)
   {     
         $value = trim($value);

         if (mb_strlen($value,'utf8')<10 || mb_strlen($value,'utf8')>1000) {
             return '还款来源字数必须在10~1000个之间';
         } else {
             return true;
         }
         
         
   } 
    //自定义验证定增价格(元)
    protected function check_price($value)
    {  
    	if (empty($value)) {
    		return true;
    	} else {
    		if (!preg_match('/^[1-9][0-9]{0,3}(\.[0-9]{1,2})?$/', $value)) {
       	        return '定增价格最多为4位数字,最多保留两位小数';
           }
            return true;
    	}
    	
    }
    //自定义验证转让价格(元)
     protected function check_price2($value)
    {  
        if (empty($value)) {
            return true;
        } else {
            if (!preg_match('/^[1-9][0-9]{0,3}(\.[0-9]{1,2})?$/', $value)) {
                return '转让价格最多为4位数字,最多保留两位小数';
           }
            return true;
        }
        
    }
    //自定义验证成本价格(元)
     protected function check_price3($value)
    {  
        if (empty($value)) {
            return true;
        } else {
            if (!preg_match('/^[1-9][0-9]{0,3}(\.[0-9]{1,2})?$/', $value)) {
                return '成本价格最多为4位数字,最多保留两位小数';
           }
            return true;
        }
        
    }
    //自定义验证出售价格(元)
     protected function check_price4($value)
    {  
        if (empty($value)) {
            return true;
        } else {
            if (!preg_match('/^[1-9][0-9]{0,3}(\.[0-9]{1,2})?$/', $value)) {
                return '出售价格最多为4位数字,最多保留两位小数';
           }
            return true;
        }
        
    }
    //自定义验证最低售票价格(元)
     protected function check_price5($value)
    {  
        
        if (!preg_match('/^[1-9][0-9]{0,3}(\.[0-9]{1,2})?$/', $value)) {
                return '最低售票价格最多为4位数字,最多保留两位小数';
        }
            return true;
        
        
    }
    //自定义验证市盈率(%)
    protected function check_pe_ratio($value)
    {  
       if (empty($value)) {
       	  return true;
       } else {
          if (!preg_match('/^-?(100|[1-9][0-9]?(\.[0-9]{1,2})?)$/', $value)) {
       	   return '市盈率范围:-100~100,可为负数,最多保留两位小数';
          }
          return true;	
       }
       
    }
    //自定义去年净利润
    protected function check_ly($value)
    {
    	if (!preg_match('/^-?[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
       	   return '去年净利润最多为10位数字,最多保留两位小数,可为负数';
        }
       return true;
    }
    //自定义今年净利润
    protected function check_ty($value)
    {   
    	if (empty($value)) {
    		return true;
    	} else {
    		if (!preg_match('/^-?[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
       	       return '今年净利润最多为10位数字,最多保留两位小数,可为负数';
            }
            return true;
    	}
    	
    }
    //自定义质押率
    protected function check_ltv_ratio($value)
    {  
       if (empty($value)) {
       	   return true;
        } else {
           if (!preg_match('/^(100|[1-9][0-9]{0,1}(\.[0-9]{1,2})?)$/', $value)) {
       	      return '质押率范围:[0-100]%,不包含0,最多保留两位小数';
           }
           return true;	   
       }
       
    }
    //自定义抵押率
    protected function check_mortgage_rate($value)
    {  
       if (empty($value)) {
       	 return true;
       } else {
         if (!preg_match('/^(100|[1-9][0-9]{0,1}(\.[0-9]{1,2})?)$/', $value)) {
       	   return '抵押率范围:[0-100]%,不包含0,最多保留两位小数';
         }
         return true;	  
       }
       
    }
    //自定义总股本
    protected function check_general_capital($value)
    {
       if (!preg_match('/^[1-9][0-9]{0,9}$/', $value)) {
       	   return '总股本最多为10位数字';
       }
       return true;
    }
    //自定义可转让股数
    protected function check_trans_stock_num($value)
    {
       if (!preg_match('/^[1-9][0-9]{0,9}$/', $value)) {
       	   return '可转让股数最多为10位数字';
       }
       return true;
    }
    //自定义可出售股数
    protected function check_trans_stock_num2($value)
    {
       if (!preg_match('/^[1-9][0-9]{0,9}$/', $value)) {
           return '可出售股数最多为10位数字';
       }
       return true;
    }
    //自定义融资利率上限rate_cap
    protected function check_rate_cap($value)
    {  
    	if (empty($value)) {
    		return true;
    	} else {
    	    if (!preg_match('/^(100|[1-9][0-9]{0,1}(\.[0-9]{1,2})?)$/', $value)) {
       	      return '融资利率上限范围:[0-100]%,最多保留两位小数';
            }
            return true;	
    	}
    	
    }
    //自定义营业收入
    protected function check_business_income($value)
    {
       if (!preg_match('/^-?[1-9][0-9]{0,9}(\.[0-9]{1,2})?$/', $value)) {
       	   return '营业收入最多为10数字,可为负数,最多保留两位小数';
       }
       return true;
    }
    //自定义负债率
    protected function check_debt_ratio($value)
    {   if (empty($value)) {
           return true;
        }
    	if (!preg_match('/^(200|[1]?[0-9]{1,2}(\.[0-9]{1,2})?)$/', $value)) {
       	   return '负债率范围:[0-200]%,最多保留两位小数';
       }
       return true;
    }
    //自定义票面金额
    protected function check_coupon_finance($value)
    {
       if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
       	   return '票面金额最多为10数字,最多保留两位小数';
       }
       return true;
    }
    //自定义预期收益
    protected function check_expected_return($value)
    {  
       if (empty($value)) {
            return true;
       } else {
           if (!preg_match('/^[1-9][0-9]{0,9}(\.[0-9]{1,2})?$/', $value)) {
            return '预期收益最多为10数字,最多保留两位小数';
           }
            return true;
       }
       
       
    }
    //自定义剩余天数
    protected function check_days_remain($value)
    {  
        if (empty($value)) {
            return true;
        } else {
            if (!preg_match('/^[0-9]{1,4}$/', $value)) {
            return '剩余天数最多为4位数字';
            }
            return true;
        }
        
       
    }
    //自定义剩余期限
    protected function check_days_remain2($value)
    {  
        if (empty($value)) {
            return true;
        } else {
            if (!preg_match('/^[1-9][0-9]{0,3}$/', $value)) {
            return '剩余期限最多为4位数字';
            }
            return true;
        }
        
       
    }
    //自定义配资比例
    protected function check_allocation_ratio($value)
    {
    	if (!preg_match('/^[1-9][0-9]{0,3}(\.[0-9]{1,2})?$/', $value)) {
       	   return '配资比例最多为4位数字,最多保留两位小数';
       }
       return true;
    }
    //自定义总操盘金额
    protected function check_ttn($value)
    {   
        if (empty($value)) {
            return true;
        } else {
            if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
              return '总操盘金额最多为10位数字,最多保留两位小数';
           }
            return true;
        }
        
    }
    //自定义月利率上限
    protected function check_mirc($value)
    {   
        if (empty($value)) {
            return true;
        } else {
            if (!preg_match('/^100|([1-9][0-9]?(\.[0-9]{1,2})?)$/', $value)) {
              return '月利率上限范围:0-100,不包含0,最多保留两位小数';
           }
           return true;
        }
        
    }
    //自定义认购金额
    protected function check_sub_amount($value)
    {
    	if (!preg_match('/^[1-9][0-9]{0,9}(\.[0-9]{1,2})?$/', $value)) {
       	   return '认购金额最多为10位数字,最多保留两位小数';
        }
        return true;
    }
    //自定义操盘周期
    protected function check_trade_period($value)
    {
        if (!preg_match('/^[1-9][0-9]{0,3}$/', $value)) {
       	   return '操盘周期最多为4位数字';
        }
        return true;	
    }
    //自定义折扣率
    protected function check_discount_rate($value)
    {
        if (!preg_match('/^(100|[1-9][0-9]?(\.[0-9]{1,2})?)$/', $value)) {
       	   return '折扣率范围:[0-100]%,不包含0,最多保留两位小数';
        }
        return true;	
    } 
    //自定义拟出让股份 
    protected function check_sell_stock($value)
    {  
       
       if (!preg_match('/^(100|[0-9]{1,2}(\.[0-9]{1,4})?)$/', $value)) {
          return '拟出让股份范围:[0-100]%,不包含0,最多保留四位小数';
       }
       return true;    
       
       
    }   
    
    //自定义拟出让股份
    protected function check_valuation($value)
    {
        
        if (!preg_match('/^[1-9][0-9]{0,9}$/', $value)) {
            return '最大估值最多为10位数字';
        }
        return true;
 
    } 
}