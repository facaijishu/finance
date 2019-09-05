<?php 
namespace app\home\controller;
use app\home\validate\ProjectRequire;
class ProjectRequireApi extends BaseApi
{    
     //用户登录/身份前置判断
    public function __construct()
    {  
       parent::__construct();
       if (session('FINANCE_USER')) {
               //发布前先判断用户类型
               $userType = session('FINANCE_USER.userType');
               //游客和未完善个人信息的绑定用户不能发布一条项目
               /*if ($userType == 0) {
                    
                    return $this->result('',280,'你当前还不是FA財平台合伙人,认证FA財平台合伙人,你的地盘你做主!','json');  
               }
               if ($userType == 1) {
                    $this->result('',281,'你的个人信息未完善','json');
               }*/
        } else {
               return $this->result('',201,'请先登录','json');
        }
     
    }
    
    //发布一条项目
    public function addProjectRequire()
	{
        $data                       = []; 
		$data['uid']                = session('FINANCE_USER.uid');  
        $dict                       = model('Dict');
        
		//发布项目的时间
		$data['create_time']        = time();
		
		//业务一级标签(Y)
        $data['top_dict_id']        = $this->request->param('top_dict_id')?$this->request->param('top_dict_id'):0;
        //业务二级标签(Y)
        $data['dict_id']            = $this->request->param('dict_id')?$this->request->param('dict_id'):0;
        
        //根据一级业务类型的名称&&二级业务名称确定不同发布项目表单的input框
        $top_dict_value             = $dict->field('value')->where(['id'=>$data['top_dict_id']])->find();
        $dict_value                 = $dict->field('value')->where(['id'=>$data['dict_id']])->find();
        if(empty($top_dict_value) || empty($dict_value)) {
            return $this->result('',216,'标签不存在','json');
        }
        //一级业务类型名称
        $top_dict_value             = $top_dict_value['value'];  
        //二级业务名称
        $dict_value                 = $dict_value['value'];
        
        //项目名称
        $data['name']               = $this->request->param('name');
        //业务简介
        $data['business_des']       = $this->request->param('business_des');
        //省份(N)
        $data['province_id']        = $this->request->param('province_id');
        
        //业务有效期（日期）
        $data['validity_period']    = $this->request->param('validity_period');
        $period                     = validity_deadline($this->request->param('validity_period'));
        $deadline                   = $data['create_time']+$period*24*60*60;
        //业务有效期(天数)
        $data['deadline']           = $deadline;
        
        //联系人身份
        $data['contact_status']     = $this->request->param('contact_status');
         
		switch ($top_dict_value) {
			case '新三板':
			    //股票代码
			    $data['stock_code']                 = $this->request->param('stock_code');
			    //股票代码是否公开
			    $data['is_public']                  = $this->request->param('is_public');
			    //所属层级
			    $data['level']                      = $this->request->param('level');
			    //交易方式
			    $data['trans_mode']                 = $this->request->param('trans_mode');
			    //所属行业
			    $industry_id                        = $this->request->param('industry');
			    $dict                               = model('Dict')->getIndustryRelation($industry_id);
			    $data['top_industry_id']            = $dict['top_industry_id'];
			    $data['industry_id']                = $dict['industry_id'];
			    //去年净利润
			    $data['ly_net_profit']              = $this->request->param('ly_net_profit');
			    //今年净利润(可选
			    $data['ty_net_profit']              = $this->request->param('ty_net_profit')?$this->request->param('ty_net_profit'):0.00;
			    //项目说明
			    $data['pro_highlight']              = $this->request->param('pro_highlight');
			    
				if ($dict_value == '定向增发') {
                    //是否Pre-IPO
                    $data['is_pre_ipo']             = $this->request->param('is_pre_ipo');
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //定增价格(可选)
                    $data['price']                  = $this->request->param('price')?$this->request->param('price'):0.00;
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio')?$this->request->param('pe_ratio'):0.00;
                    //融资用途(可选)
                    $data['financing_purpose']      = $this->request->param('financing_purpose')?$this->request->param('financing_purpose'):'';
				} elseif ($dict_value == '大宗交易') {
					//是否Pre-IPO
                    $data['is_pre_ipo']             = $this->request->param('is_pre_ipo');
                    //总股本
                    $data['general_capital']        = $this->request->param('general_capital');
                    //可转让股数
                    $trans_stock_num                = $this->request->param('trans_stock_num')?$this->request->param('trans_stock_num'):0;
                    $data['trans_stock_num']        =  $trans_stock_num;
                    //转让价格
                    $price                          = $this->request->param('price')?$this->request->param('price'):0.00;
                    //转让价格
                    $data['price']                  = $price;
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio')?$this->request->param('pe_ratio'):0.00;
                    //是否可以议价
                    $data['is_bargain']             = $this->request->param('is_bargain');

				} 
				/*elseif ($dict_value == 'Pre-IPO') {
				    //是否报辅导
					$data['is_coach']              = $this->request->param('is_coach');
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
				} */
				
				elseif ($dict_value == '股权质押') {
					//是否Pre-IPO
                    $data['is_pre_ipo']             = $this->request->param('is_pre_ipo');
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //是否为大股东
                    $data['is_stockholder']         = $this->request->param('is_stockholder');
                    //质押率
                    $data['ltv_ratio']              = $this->request->param('ltv_ratio');
				} 
				/*elseif ($dict_value == '资产抵押') {
					//是否Pre-IPO
                    $data['is_pre_ipo']             = $this->request->param('is_pre_ipo');
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //抵押率
                    $data['mortgage_rate']          = $this->request->param('mortgage_rate');
				} */
				elseif ($dict_value == '壳交易') {
					//是否Pre-IPO
                    $data['is_pre_ipo']             = $this->request->param('is_pre_ipo');
                    //总股本
                    $data['general_capital']        = $this->request->param('general_capital');
                    //可转让/出售股数
                    $trans_stock_num                = $this->request->param('trans_stock_num');
                    //出售股数
                    $data['trans_stock_num']        = $trans_stock_num;
                    $price                          = $this->request->param('price');
                    //出售价格
                    $data['price']                  = $price;
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
				} 
				/*elseif ($dict_value == '并购') {
					//是否Pre-IPO
                    $data['is_pre_ipo']             = $this->request->param('is_pre_ipo');
                    //投资者偏好
                    $data['investor_preference']    = $this->request->param('investor_preference');
                    //拟出让股份最小值
                    $data['sell_stock_min']         = $this->request->param('sell_stock_min');
                    //拟出让股份最大值
                    $data['sell_stock_max']         = $this->request->param('sell_stock_max');
                    
				}*/ 
				elseif ($dict_value == '其他') {
					//是否Pre-IPO
                    $data['is_pre_ipo']             = $this->request->param('is_pre_ipo');
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                   
				}
				break;
            case '股权':
                //所属行业
                $industry_id                        = $this->request->param('industry');
                $dict                               = model('Dict')->getIndustryRelation($industry_id);
                $data['top_industry_id']            = $dict['top_industry_id'];
                $data['industry_id']                = $dict['industry_id'];
                
                //去年净利润
                $data['ly_net_profit']              = $this->request->param('ly_net_profit');
                //今年净利润(可选)
                $data['ty_net_profit']              = $this->request->param('ty_net_profit');
                //项目说明（投资亮点）
                $data['pro_highlight']              = $this->request->param('pro_highlight');
                //资金方偏好
                $data['investor_preference']        = $this->request->param('investor_preference');
                
                /*
                 if ($dict_value == '科创板') {
                    //拟出让股份最小值
                    $data['sell_stock_min']         = $this->request->param('sell_stock_min');
                    //拟出让股份最大值
                    $data['sell_stock_max']         = $this->request->param('sell_stock_max');
                     
                }
                 */
                if ($dict_value == '老股转让') {
                    //转让价格
                    $price                          = $this->request->param('price');
                    $data['price']                  = $price;
                    //可转让股数
                    $trans_stock_num = $this->request->param('trans_stock_num');
                    $data['trans_stock_num']        = $trans_stock_num;
                    //本轮估值
                    $valuation = $this->request->param('valuation');
                    $data['valuation']              = $valuation;
                     
                } elseif ($dict_value == 'VC') {
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
	                    
			    } elseif ($dict_value == 'PE') {
			        //是否Pre-IPO
			        $data['is_pre_ipo']             = $this->request->param('is_pre_ipo');
			        //融资规模(Y)
			        $data['financing_scale']        = $this->request->param('financing_scale');
			        //融资用途
			        $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                   
			    } elseif ($dict_value == 'Pre-IPO') {
			    	//是否报辅导
			    	$data['is_coach']               = $this->request->param('is_coach');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
			    } elseif ($dict_value == '并购') {
                    //拟出让股份最小值
                    $data['sell_stock_min']         = $this->request->param('sell_stock_min');
                    //拟出让股份最大值
                    $data['sell_stock_max']         = $this->request->param('sell_stock_max');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
			    } elseif ($dict_value == '其他') {
			        //拟出让股份最小值
			        $data['sell_stock_min']         = $this->request->param('sell_stock_min');
			        //拟出让股份最大值
			        $data['sell_stock_max']         = $this->request->param('sell_stock_max');
			        //融资规模(Y)
			        $data['financing_scale']        = $this->request->param('financing_scale');
			    }
				    
				break;
            case '债权':
                //所属行业
                $industry_id                        = $this->request->param('industry');
                $dict                               = model('Dict')->getIndustryRelation($industry_id);
                $data['top_industry_id']            = $dict['top_industry_id'];
                $data['industry_id']                = $dict['industry_id'];
			    if ($dict_value == '信贷') {
			   	    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //融资利率上限年化
                    $data['rate_cap']               = $this->request->param('rate_cap');
                    //增信方式
                    $data['inc_credit_way']         = $this->request->param('inc_credit_way');
                    //还款来源
                    $data['pay_source']             = $this->request->param('pay_source');
                 
                }
                /*elseif ($dict_value == '信托') {
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //业务有效期
                    $data['validity_period']        = $this->request->param('validity_period');
                    //联系人身份
                    $data['contact_status']         = $this->request->param('contact_status');
                    //融资利率上限
                    $data['rate_cap']               = $this->request->param('rate_cap');
                    //增信方式
                    $data['inc_credit_way']         = $this->request->param('inc_credit_way');
                    //还款来源
                    $data['pay_source']             = $this->request->param('pay_source');
			   } elseif ($dict_value == '资管') {
			        //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //融资利率上限
                    $data['rate_cap']               = $this->request->param('rate_cap');
                    //增信方式
                    $data['inc_credit_way']         = $this->request->param('inc_credit_way');
                    //还款来源
                    $data['pay_source'] = $this->request->param('pay_source');
			   } elseif ($dict_value == '可转债/可交债') {
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //融资利率上限
                    $data['rate_cap']               = $this->request->param('rate_cap');
                    //增信方式
                    $data['inc_credit_way']         = $this->request->param('inc_credit_way');
                    //还款来源
                    $data['pay_source']             = $this->request->param('pay_source');
			   } 
			   */
			   elseif ($dict_value == '融资租赁') {
    			    //融资规模(Y)
    			    $data['financing_scale']        = $this->request->param('financing_scale');
    			    //融资期限
    			    $data['period']                 = $this->request->param('period');
    			    //融资用途
    			    $data['financing_purpose']      = $this->request->param('financing_purpose');
    		        //融资利率上限
    			    $data['rate_cap']               = $this->request->param('rate_cap');
    			    //增信方式
    			    $data['inc_credit_way']         = $this->request->param('inc_credit_way');
    			    //还款来源
    			    $data['pay_source']             = $this->request->param('pay_source');
                    //营业收入
                    $data['business_income']        = $this->request->param('business_income');
                    //负债率
                    $data['debt_ratio']             = $this->request->param('debt_ratio');
			   } 
			   /*
			    elseif ($dict_value == '股权质押') {
                    //融资规模(Y)
			        $data['financing_scale']        = $this->request->param('financing_scale');
			        //融资期限
			        $data['period']                 = $this->request->param('period');
			        //是否为大股东
			        $data['is_stockholder']         = $this->request->param('is_stockholder');
			        //质押率
			        $data['ltv_ratio']              = $this->request->param('ltv_ratio');
			        //市盈率(可选)
			        $data['pe_ratio']               = $this->request->param('pe_ratio');
			        //去年净利润
			        $data['ly_net_profit']          = $this->request->param('ly_net_profit');
			        //今年净利润(可选)
			        $data['ty_net_profit']          = $this->request->param('ty_net_profit');
			        //融资用途
			        $data['financing_purpose']      = $this->request->param('financing_purpose');
			        //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //市盈率(可选)
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //还款来源
                    $data['pay_source']             = $this->request->param('pay_source');
			   }
			   */
			   elseif ($dict_value == '资产证券化') {
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //业务有效期
                    $data['validity_period']        = $this->request->param('validity_period');
                    //联系人身份
                    $data['contact_status']         = $this->request->param('contact_status');
                    //融资利率上限
                    $data['rate_cap']               = $this->request->param('rate_cap');
                    //增信方式
                    $data['inc_credit_way']         = $this->request->param('inc_credit_way');
                    //还款来源
                    $data['pay_source']             = $this->request->param('pay_source');
			   } elseif ($dict_value == '票据业务') {
                    //承兑人
                    $data['acceptor']               = $this->request->param('acceptor');
                    //票据类型
                    $data['bill_type']              = $this->request->param('bill_type');
                    //票面金融
                    $data['coupon_finance']         = $this->request->param('coupon_finance');
                    //剩余天数
                    $data['days_remain']            = $this->request->param('days_remain');
                    //最低售票价格
                    $data['price']                  = $this->request->param('price');
			 } elseif ($dict_value == '商业保理') {
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose'); 
                    //融资利率上限
                    $data['rate_cap']               = $this->request->param('rate_cap');
                    //增信方式
                    $data['inc_credit_way']         = $this->request->param('inc_credit_way');
                    //还款来源
                    $data['pay_source']             = $this->request->param('pay_source');
                    //营业收入
                    $data['business_income']        = $this->request->param('business_income');
                    //负债率
                    $data['debt_ratio']             = $this->request->param('debt_ratio');
			 } elseif ($dict_value == '其他') {
			   	    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //融资利率上限
                    $data['rate_cap']               = $this->request->param('rate_cap');
                    //增信方式
                    $data['inc_credit_way']         = $this->request->param('inc_credit_way');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    //还款来源
                    $data['pay_source']             = $this->request->param('pay_source');
			   }
				break;
			case '上市公司':
			    //股票代码
			    $data['stock_code']                 = $this->request->param('stock_code');
			    //股票代码是否公开
			    $data['is_public']                  = $this->request->param('is_public');
			    //项目说明
			    $data['pro_highlight']              = $this->request->param('pro_highlight');
			    //去年净利润
			    $data['ly_net_profit']              = $this->request->param('ly_net_profit');
			    //今年净利润
			    $data['ty_net_profit']              = $this->request->param('ty_net_profit');
			    //上市公司类型
			    $data['lc_type']                    = $this->request->param('lc_type');
			    //所属行业
			    $industry_id                        = $this->request->param('industry');
			    $dict                               = model('Dict')->getIndustryRelation($industry_id);
			    $data['top_industry_id']            = $dict['top_industry_id'];
			    $data['industry_id']                = $dict['industry_id'];
				if ($dict_value == '定向增发') {
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
                    //定增价格
                    $data['price']                  = $this->request->param('price');
                    //市盈率
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                 	
				} elseif ($dict_value == '股权质押') {
                    //融资规模(Y)
                    $data['financing_scale']        = $this->request->param('financing_scale');
                    //融资期限
                    $data['period']                 = $this->request->param('period');
					//质押率
                    $data['ltv_ratio']              = $this->request->param('ltv_ratio');
                    //市盈率
                    $data['pe_ratio']               = $this->request->param('pe_ratio');
                    //融资利率上限
                    $data['rate_cap']               = $this->request->param('rate_cap');
                    //融资用途
                    $data['financing_purpose']      = $this->request->param('financing_purpose');
                    
				} elseif ($dict_value == '大宗交易') {
                     //交易规模
                     $data['financing_scale']       = $this->request->param('financing_scale');
                     //成本价格
                     $data['price']                 = $this->request->param('price');
				     //折扣率
                     $data['discount_rate']         = $this->request->param('discount_rate');
                     //市盈率
                     $data['pe_ratio']              = $this->request->param('pe_ratio');
				} 
				/*elseif ($dict_value == '壳交易') {
                     //交易规模
                     $data['financing_scale']       = $this->request->param('financing_scale');
                     //市盈率
                     $data['pe_ratio']              = $this->request->param('pe_ratio');
 				     //拟出让股份最小值
                     $data['sell_stock_min']        = $this->request->param('sell_stock_min');
                     //拟出让股份最大值
                     $data['sell_stock_max']        = $this->request->param('sell_stock_max');
                         
				} */
				elseif ($dict_value == '市值管理') {
                     //交易规模
                     $data['financing_scale']       = $this->request->param('financing_scale');
                     //管理期限
                     $data['period']                = $this->request->param('period');
                     //市盈率
                     $data['pe_ratio']              = $this->request->param('pe_ratio');
				} elseif ($dict_value == '并购重组') {
                     //投资者偏好
                     $data['investor_preference']   = $this->request->param('investor_preference');
                  	 //拟出让股份最小值
                     $data['sell_stock_min']        = $this->request->param('sell_stock_min');
                     //拟出让股份最大值
                     $data['sell_stock_max']        = $this->request->param('sell_stock_max');
                    
				} elseif ($dict_value == '其他') {
                     //融资规模(Y)
                     $data['financing_scale']       = $this->request->param('financing_scale');
                     //融资期限
                     $data['period']                = $this->request->param('period');
                     //市盈率
                     $data['pe_ratio']              = $this->request->param('pe_ratio');
				}

				break;
			case '创业孵化':
			    //所需资金
			    $data['financing_scale']            = $this->request->param('financing_scale');
			    //项目说明
			    $data['pro_highlight']              = $this->request->param('pro_highlight');
			    //预期收益
			    $data['expected_return']            = $this->request->param('expected_return');
			    //剩余期限
			    $data['remain_period']              = $this->request->param('remain_period');
			    //所属行业
			    $industry_id                        = $this->request->param('industry');
			    $dict                               = model('Dict')->getIndustryRelation($industry_id);
			    $data['top_industry_id']            = $dict['top_industry_id'];
			    $data['industry_id']                = $dict['industry_id'];
			break;
			/*case '配资业务':
 			    //投入本金
 			    $data['financing_scale']            = $this->request->param('financing_scale');
 			    //配资比例
 			    $data['allocation_ratio']           = $this->request->param('allocation_ratio');
 			    //操盘周期
 			    $data['trade_period']               = $this->request->param('trade_period');
 			    //总操盘金额
 			    $data['total_trade_num']            = $this->request->param('total_trade_num');
 			    //月利率上限
 			    $data['mirc']                       = $this->request->param('mirc');
			break;
			*/
			case '金融产品':
			    if ($dict_value == 'S基金') {
			        //转让价格
			        $price                          = $this->request->param('price');
			        $data['price']                  = $price;
			        //本轮估值
			        $valuation = $this->request->param('valuation');
			        $data['valuation']              = $valuation;
			    }
                //发行机构
                $data['product_issuer']             = $this->request->param('product_issuer');
                //是否备案
                $data['is_record']                  = $this->request->param('is_record');
                //认购金额
                $data['sub_amount']                 = $this->request->param('sub_amount');
                //产品规模
                $data['financing_scale']            = $this->request->param('financing_scale');
                //产品期限
                $data['period']                     = $this->request->param('period');
                //产品状态
                $data['product_status']             = $this->request->param('product_status');
                //项目说明
                $data['pro_highlight']              = $this->request->param('pro_highlight');
                //所属行业
                $industry_id                        = $this->request->param('industry');
                $dict                               = model('Dict')->getIndustryRelation($industry_id);
                $data['top_industry_id']            = $dict['top_industry_id'];
                $data['industry_id']                = $dict['industry_id'];
			break;	
			default:
				$this->code = 215;
				$this->msg  = '业务标签不存在,请先往后台添加';
				return $this->result('',$this->code,$this->msg,'json');
		}
        //上传图片
        $data['img_id']                             = $this->request->param('img_id')?$this->request->param('img_id'):'';
        
        
        $validate = new ProjectRequire($top_dict_value,$dict_value);
        if(!$validate->scene('save')->check($data)){
           $this->code  = 217;
           $this->msg   = $validate->getError();
           return $this->result('',$this->code,$this->msg,'json');
        }
        $pr = model('ProjectRequire');
        $pr->addProjectRequireApi($data);
	}
	
     //根据对应一二级业务标签返回对应发布项目的表单输入框的命名
     public function  showProjectRequireForm()
     {    
          //业务一级标签(Y)
          $top_dict_id      = $this->request->param('top_dict_id')?$this->request->param('top_dict_id'):0;
          //业务二级标签(Y)
          $dict_id          = $this->request->param('dict_id')?$this->request->param('dict_id'):0;
          $dict             = model('Dict');
          
          /**
           * 根据一级业务类型的名称&&二级业务名称确定不同发布项目表单的input框
           */
          $top_dict_value   = $dict->field('value')->where(['id'=>$top_dict_id])->find();

          $dict_value       = $dict->field('value')->where(['id'=>$dict_id])->find();
          
          if (empty($top_dict_value) || empty($dict_value)) {
              return $this->result('',216,'标签不存在','json');
          }

          //一级业务类型名称
          $top_dict_value   = $top_dict_value['value'];          

          //二级业务名称
          $dict_value       = $dict_value['value'];

          $data = [];
          switch ($top_dict_value) {
               case '新三板':
                        $data['name']              = '项目名称';
                        $data['business_des']      = '业务简介';
                        $data['stock_code']        = '股票代码';
                        $data['is_public']         = '不公开';
                    if ($dict_value == '定向增发') {
                        $data['is_pre_ipo']        = '是否Pre-IPO';
                        $data['level']             = '所属层级';
                        $data['trans_mode']        = '交易方式';
                        $data['province_id']       = '所在省份';
                        $data['industry']          = '所属行业';
                        $data['financing_scale']   = '交易规模（约）';
                        $data['period']            = '融资期限';
                        $data['price']             = '定增价格';
                        $data['pe_ratio']          = '市盈率';
                        $data['ly_net_profit']     = '去年净利润';
                        $data['ty_net_profit']     = '今年净利润';
                        $data['financing_purpose'] = '融资用途';
                        $data['pro_highlight']     = '项目说明';
                       
                    } elseif ($dict_value == '大宗交易') {
                        $data['is_pre_ipo']        = '是否Pre-IPO';
                        $data['level']             = '所属层级';
                        $data['trans_mode']        = '交易方式';
                        $data['province_id']       = '所在省份';
                        $data['industry']          = '所属行业';
                        $data['general_capital']   = '总股本';
                        $data['trans_stock_num']   = '可转让股数';
                        $data['price']             = '转让价格';
                        $data['is_bargain']        = '是否可议价';
                        $data['pe_ratio']          = '市盈率';
                        $data['ly_net_profit']     = '去年净利润';
                        $data['ty_net_profit']     = '今年净利润';
                        $data['pro_highlight']     = '项目说明';

                    } elseif ($dict_value == '股权质押') {
                        $data['is_pre_ipo']        = '是否Pre-IPO';
                        $data['level']             = '所属层级';
                        $data['trans_mode']        = '交易方式';
                        $data['province_id']       = '所在省份';
                        $data['industry']          = '所属行业';
                        $data['financing_scale']   = '交易规模（约）';
                        $data['period']            = '融资期限';
                        $data['is_stockholder']    = '是否为大股东';
                        $data['ltv_ratio']         = '质押率';
                        $data['pe_ratio']          = '市盈率';
                        $data['ly_net_profit']     = '去年净利润';
                        $data['ty_net_profit']     = '今年净利润';
                        $data['pro_highlight']     = '项目说明';

                    } 
                    /*
                    elseif ($dict_value == '资产抵押') {
                        $data['is_pre_ipo']        = '是否Pre-IPO';
                        $data['level']             = '所属层级';
                        $data['trans_mode']        = '交易方式';
                        $data['province_id']       = '所在省份';
                        $data['industry']          = '所属行业';
                        $data['financing_scale']   = '交易规模（约）';
                        $data['period']            = '融资期限';
                        $data['mortgage_rate']     = '抵押率';
                        $data['pe_ratio']          = '市盈率';
                        $data['ly_net_profit']     = '去年净利润';
                        $data['ty_net_profit']     = '今年净利润';
                        $data['pro_highlight']     = '资产说明';

                    } elseif ($dict_value == 'Pre-IPO') {
                        $data['level']             = '所属层级';
                        $data['is_coach']          = '是否报辅导';
                        $data['trans_mode']        = '交易方式';
                        $data['province_id']       = '所在省份';
                        $data['industry']          = '所属行业';
                        $data['financing_scale']   = '交易规模（约）';
                        $data['period']            = '融资期限';
                        $data['pe_ratio']          = '市盈率';
                        $data['ly_net_profit']     = '去年净利润';
                        $data['ty_net_profit']     = '今年净利润';
                        $data['pro_highlight']     = '项目说明';
                        $data['financing_purpose'] = '融资说明';

                    } */
                    elseif ($dict_value == '壳交易') {
                        $data['is_pre_ipo']        = '是否Pre-IPO';
                        $data['level']             = '所属层级';
                        $data['trans_mode']        = '交易方式';
                        $data['province_id']       = '所在省份';
                        $data['industry']          = '所属行业';
                        $data['general_capital']   = '总股本';
                        $data['trans_stock_num']   = '可出售股数';
                        $data['price']             = '出售价格';
                        $data['pe_ratio']          = '市盈率';
                        $data['ly_net_profit']     = '去年净利润';
                        $data['ty_net_profit']     = '今年净利润';
                        $data['pro_highlight']     = '项目说明';

                    } 
                    /*
                    elseif ($dict_value == '并购') {
                        $data['is_pre_ipo']          = '是否Pre-IPO';
                        $data['level']               = '所属层级';
                        $data['trans_mode']          = '交易方式';
                        $data['province_id']         = '所在省份';
                        $data['industry']            = '所属行业';
                        $data['sell_stock_min']      = '拟出让股份最小值';
                        $data['sell_stock_max']      = '拟出让股份最大值';
                        $data['ly_net_profit']       = '去年净利润';
                        $data['ty_net_profit']       = '今年净利润';
                        $data['investor_preference'] = '资金方偏好';
                        $data['pro_highlight']       = '项目说明';
                    } */
                    
                    elseif ($dict_value == '其他') {
                        $data['is_pre_ipo']        = '是否Pre-IPO';
                        $data['level']             = '所属层级';
                        $data['trans_mode']        = '交易方式';
                        $data['province_id']       = '所在省份';
                        $data['industry']          = '所属行业';
                        $data['financing_scale']   = '交易规模（约）';
                        $data['period']            = '融资期限';
                        $data['pe_ratio']          = '市盈率';
                        $data['ly_net_profit']     = '去年净利润';
                        $data['ty_net_profit']     = '今年净利润';
                        $data['pro_highlight']     = '项目说明';
                        $data['financing_purpose'] = '融资用途';
                    }
                        $data['img_id']            = '添加图片';
                        $data['validity_period']   = '业务有效期';
                        $data['contact_status']    = '联系人身份';
                    return $this->result($data,200,'','json');
                    break;
               case '股权':
                        $data['name']                = '项目名称';
                        $data['business_des']        = '业务简介';
                        
                    /*if ($dict_value == '科创板') {
                        $data['province_id']         = '所在省份';
                        $data['industry']            = '所属行业';
                        $data['financing_scale']     = '交易规模（约）';
                        $data['sell_stock_min']      = '拟出让股份最小值';
                        $data['sell_stock_max']      = '拟出让股份最大值';
                        $data['ly_net_profit']       = '去年净利润';
                        $data['ty_net_profit']       = '今年净利润';
                        $data['investor_preference'] = '资金方偏好';
                        $data['pro_highlight']       = '项目说明';
                    } */
                    if ($dict_value == '老股转让') {
                        $data['province_id']         = '所在省份';
                        $data['industry']            = '所属行业';
                        $data['ly_net_profit']       = '去年净利润';
                        $data['ty_net_profit']       = '今年净利润';
                        $data['investor_preference'] = '资金方偏好';
                        $data['pro_highlight']       = '项目说明';
                        $data['price']               = '转让价格';
                        $data['trans_stock_num']     = '可转让股数';
                        $data['valuation']           = '本轮估值';
                        
                        
                    } elseif ($dict_value == 'VC') {
                        $data['province_id']         = '所在地区';
                        $data['industry']            = '所属行业/概念';
                        $data['financing_scale']     = '交易规模（约）';
                        $data['period']              = '融资期限';
                        $data['pe_ratio']            = '市盈率';
                        $data['ly_net_profit']       = '去年净利润';
                        $data['ty_net_profit']       = '今年净利润';
                        $data['investor_preference'] = '资金方偏好';
                        $data['financing_purpose']   = '融资用途';
                        $data['pro_highlight']       = '投资亮点';
                    } elseif ($dict_value == 'PE') {
                        $data['is_pre_ipo']          = '是否Pre-IPO';
                        $data['province_id']         = '所在地区';
                        $data['industry']            = '所属行业/概念';
                        $data['financing_scale']     = '融资规模（约）';
                        $data['period']              = '融资期限';
                        $data['pe_ratio']            = '市盈率';
                        $data['ly_net_profit']       = '去年净利润';
                        $data['ty_net_profit']       = '今年净利润';
                        $data['investor_preference'] = '投资者偏好';
                        $data['financing_purpose']   = '融资用途';
                        $data['pro_highlight']       = '投资亮点';
                    } elseif ($dict_value == 'Pre-IPO') {
                         $data['is_coach']            = '是否报辅导';
                         $data['province_id']         = '所在地区';
                         $data['industry']            = '所属行业/概念';
                         $data['financing_scale']     = '融资规模（约）';
                         $data['period']              = '融资期限';
                         $data['pe_ratio']            = '市盈率';
                         $data['ly_net_profit']       = '去年净利润';
                         $data['ty_net_profit']       = '今年净利润';
                         $data['investor_preference'] = '资金方偏好';
                         $data['financing_purpose']   = '融资用途';
                         $data['pro_highlight']       = '投资亮点';

                    } elseif ($dict_value == '并购') {
                         $data['province_id']         = '所在省份';
                         $data['industry']            = '所属行业';
                         $data['financing_scale']     = '交易规模（约）';
                         $data['sell_stock_min']      = '拟出让股份最小值';
                         $data['sell_stock_max']      = '拟出让股份最大值';
                         $data['ly_net_profit']       = '去年净利润';
                         $data['ty_net_profit']       = '今年净利润';
                         $data['investor_preference'] = '资金方偏好';
                         $data['financing_purpose']   = '融资用途';
                         $data['pro_highlight']       = '投资亮点';

                    } elseif ($dict_value == '其他') {
                         $data['province_id']         = '所在省份';
                         $data['industry']            = '所属行业';
                         $data['financing_scale']     = '交易规模（约）';
                         $data['sell_stock_min']      = '拟出让股份最小值';
                         $data['sell_stock_max']      = '拟出让股份最大值';
                         $data['ly_net_profit']       = '去年净利润';
                         $data['ty_net_profit']       = '今年净利润';
                         $data['investor_preference'] = '资金方偏好';
                         $data['pro_highlight']       = '项目说明';
                    }
                          
                          $data['img_id']             = '添加图片';
                          $data['validity_period']    = '业务有效期';
                          $data['contact_status']     = '联系人身份';
                    return $this->result($data,200,'','json');
                    break;
               case '债权':
                         $data['name']              = '项目名称';
                         $data['business_des']      = '业务简介';
                         $data['province_id']       = '所在省份';
                         $data['industry']          = '所属行业';
                    if ($dict_value == '信贷') {
                         $data['financing_scale']   = '交易规模（约）';
                         $data['period']            = '融资期限';
                         $data['rate_cap']          = '融资利率上限(年化)';
                         $data['inc_credit_way']    = '增信方式';
                         $data['financing_purpose'] = '融资说明';
                         $data['pay_source']        = '还款来源';

                    } 
                    
                    /*elseif ($dict_value == '信托') {
                          $data['financing_scale']   = '交易规模（约）';
                          $data['period']            = '融资期限';
                          $data['rate_cap']          = '融资利率上限(年化)';
                          $data['inc_credit_way']    = '增信方式';
                          $data['financing_purpose'] = '融资说明';
                          $data['pay_source']        = '还款来源';

                    } elseif ($dict_value == '资管') {
                          $data['financing_scale']   = '交易规模（约）';
                          $data['period']            = '融资期限';
                          $data['rate_cap']          = '融资利率上限(年化)';
                          $data['inc_credit_way']    = '增信方式';
                          $data['financing_purpose'] = '融资说明';
                          $data['pay_source']        = '还款来源';

                    } elseif ($dict_value == '可转债/可交债') {
                          $data['financing_scale']   = '交易规模（约）';
                          $data['period']            = '融资期限';
                          $data['rate_cap']          = '融资利率上限(年化)';
                          $data['inc_credit_way']    = '增信方式';
                          $data['financing_purpose'] = '融资说明';
                          $data['pay_source']        = '还款来源';

                    } */
                    elseif ($dict_value == '融资租赁') {
                          $data['financing_scale']   = '交易规模（约）';
                          $data['period']            = '融资期限';
                          $data['rate_cap']          = '融资利率上限(年化)';
                          $data['inc_credit_way']    = '增信方式';
                          $data['business_income']   = '营业收入(去年)';
                          $data['debt_ratio']        = '负债率';
                          $data['financing_purpose'] = '融资说明';
                          $data['pay_source']        = '还款来源';

                    } 
                    /*
                     elseif ($dict_value == '股权质押') {
                          $data['financing_scale']   = '交易规模（约）';
                          $data['period']            = '融资期限';
                          $data['is_stockholder']    = '是否为大股东';
                          $data['ltv_ratio']         = '质押率';
                          $data['pe_ratio']          = '市盈率';
                          $data['ly_net_profit']     = '去年净利润';
                          $data['ty_net_profit']     = '今年净利润';
                          $data['financing_purpose'] = '融资说明';
                          $data['pay_source']        = '还款来源';

                    } */
                    elseif ($dict_value == '资产证券化') {
                           $data['financing_scale']    = '交易规模（约）';
                           $data['period']             = '融资期限';
                           $data['rate_cap']           = '融资利率上限(年化)';
                           $data['inc_credit_way']     = '增信方式';
                           $data['financing_purpose']  = '融资说明';
                           $data['pay_source']         = '还款来源';

                    } elseif ($dict_value == '商业保理') {
                           $data['financing_scale']   = '交易规模（约）';
                           $data['period']            = '融资期限';
                           $data['rate_cap']          = '融资利率上限(年化)';
                           $data['inc_credit_way']    = '增信方式';
                           $data['business_income']   = '营业收入(去年)';
                           $data['debt_ratio']        = '负债率';
                           $data['financing_purpose'] = '融资说明';
                           $data['pay_source']        = '还款来源';

                    } elseif ($dict_value == '票据业务') {
                           $data['acceptor']          = '承兑人';
                           $data['bill_type']         = '票据类型';
                           $data['coupon_finance']    = '票面金额';
                           $data['days_remain']       = '剩余天数';
                           $data['price']             = '最低售票价格';

                    } elseif ($dict_value == '其他') {
                           $data['financing_scale']   = '交易规模（约）';
                           $data['period']            = '融资期限';
                           $data['rate_cap']          = '融资利率上限(年化)';
                           $data['inc_credit_way']    = '增信方式';
                           $data['financing_purpose'] = '融资说明';
                           $data['pay_source']        = '还款来源';

                    }
                           $data['img_id']            = '添加图片';
                           $data['validity_period']   = '业务有效期';
                           $data['contact_status']    = '联系人身份';
                    return $this->result($data,200,'','json');
                    break;
               case '创业孵化':
                    //if ($dict_value == '影视文娱' || $dict_value == '资产交易' || $dict_value == '公益爱心' || $dict_value == '其他' || $dict_value == '项目落户') {
                       $data['name']              = '项目名称';
                       $data['business_des']      = '业务简介';
                       $data['province_id']       = '所在省份';
                       $data['industry']          = '所属行业';
                       $data['financing_scale']   = '所需资金';
                       $data['remain_period']     = '剩余期限';
                       $data['expected_return']   = '预计收益';
                       $data['pro_highlight']     = '项目说明';
                       $data['img_id']            = '添加图片';
                       $data['validity_period']   = '业务有效期';
                       $data['contact_status']    = '联系人身份';
                       return $this->result($data,200,'','json');
                    //}
                    break;
               case '上市公司':
                           $data['name']              = '项目名称';
                           $data['business_des']      = '业务简介';
                           $data['stock_code']        = '股票代码';
                           $data['is_public']         = '不公开';
                           $data['lc_type']           = '上市公司类型';
                           $data['province_id']       = '所在省份';
                           $data['industry']          = '所属行业';

                    if ($dict_value == '定向增发') {
                           $data['financing_scale']   = '交易规模（约）';
                           $data['period']            = '融资期限';
                           $data['price']             = '定增价格';
                           $data['pe_ratio']          = '市盈率';
                           $data['ly_net_profit']     = '去年净利润';
                           $data['ty_net_profit']     = '今年净利润';
                           $data['financing_purpose'] = '融资用途';

                    } elseif ($dict_value == '大宗交易') {
                           $data['financing_scale']   = '交易规模（约）';
                           $data['price']             = '成本价格';
                           $data['discount_rate']     = '折扣率';
                           $data['pe_ratio']          = '市盈率';
                           $data['ly_net_profit']     = '去年净利润';
                           $data['ty_net_profit']     = '今年净利润';

                    } elseif ($dict_value == '股权质押') {
                           $data['financing_scale']   = '交易规模（约）';
                           $data['period']            = '融资期限';
                           $data['ltv_ratio']         = '质押率';
                           $data['rate_cap']          = '融资利率上限(年化)';
                           $data['pe_ratio']          = '市盈率';
                           $data['ly_net_profit']     = '去年净利润';
                           $data['ty_net_profit']     = '今年净利润';
                           $data['financing_purpose'] = '融资用途';

                    } elseif ($dict_value == '壳交易') {
                           $data['financing_scale']   = '交易规模（约）';
                           $data['sell_stock_min']    = '拟出让股份最小值';
                           $data['sell_stock_max']    = '拟出让股份最大值';
                           $data['pe_ratio']          = '市盈率';
                           $data['ly_net_profit']     = '去年净利润';
                           $data['ty_net_profit']     = '今年净利润';

                    } elseif ($dict_value == '市值管理') {
                           $data['financing_scale']   = '交易规模（约）';
                           $data['period']            = '管理期限';
                           $data['pe_ratio']          = '市盈率';
                           $data['ly_net_profit']     = '去年净利润';
                           $data['ty_net_profit']     = '今年净利润';

                    } elseif ($dict_value == '并购重组') {
                           $data['sell_stock_min']      = '拟出让股份最小值';
                           $data['sell_stock_max']      = '拟出让股份最大值';
                           $data['ly_net_profit']       = '去年净利润';
                           $data['ty_net_profit']       = '今年净利润';
                           $data['investor_preference'] = '资金方偏好';

                    } elseif ($dict_value == '其他') {
                           $data['financing_scale']  = '交易规模（约）';
                           $data['period']           = '融资期限';
                           $data['pe_ratio']         = '市盈率';
                           $data['ly_net_profit']    = '去年净利润';
                           $data['ty_net_profit']    = '今年净利润';

                    }
                           $data['pro_highlight']    = '项目说明';
                           $data['validity_period']  = '业务有效期';
                           $data['contact_status']   = '联系人身份';
                    return $this->result($data,200,'','json');
                    break;
               case '金融产品':
                           $data['name']            = '产品名称';
                           $data['business_des']    = '产品简介';
                           $data['province_id']     = '所在省份';
                           $data['product_issuer']  = '发行机构';
                           $data['is_record']       = '是否备案';
                           $data['sub_amount']      = '认购金额';
                           $data['period']          = '产品期限';
                           $data['financing_scale'] = '产品规模';
                           $data['product_status']  = '产品状态';
                           $data['pro_highlight']   = '产品说明';
                           $data['validity_period'] = '业务有效期';
                           $data['contact_status']  = '联系人身份';
                           $data['industry']        = '所属行业';
                   
                    if($dict_value == 'S基金'){
                            $data['price']          = '转让价格';
                            $data['valuation']      = '本轮估值';
                    }
                    return $this->result($data,200,'','json');
                    break;
               /*case '配资业务':
                    if ($dict_value == '股票配资' || $dict_value == '期货配资' || $dict_value == '其他') {
                            $data['name']             = '项目名称';
                            $data['business_des']     = '业务简介';
                            $data['province_id']      = '所在省份';
                            $data['financing_scale']  = '投入本金';
                            $data['allocation_ratio'] = '配资比例';
                            $data['total_trade_num']  = '总操盘金额';
                            $data['trade_period']     = '操盘周期';
                            $data['mirc']             = '月利率上限';
                            $data['validity_period']  = '业务有效期';
                            $data['contact_status']   = '联系人身份';
                        return $this->result($data,200,'','json');
                    }
                    break;*/
               default:
                    return $this->result('',215,'业务标签不存在,请先往后台添加','json');
                    break;
          }
          
     }
     
}
