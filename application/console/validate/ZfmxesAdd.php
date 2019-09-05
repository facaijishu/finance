<?php

namespace app\console\validate;

use think\Validate;

class ZfmxesAdd extends Validate
{
    protected $rule = [
        'raise_money'    => 'require',
        'additional_num'    => 'require',
        'announcement_date'    => 'require',
        'increase_price'    => 'require',
        'profit'    => 'require',
        'cutoff_date'    => 'require',
        'fundraising_use'    => 'require',
        'Issue_object'    => 'require',
        'involved'    => 'require',
        'sale_condition'    => 'require',
    ];

    protected $message = [
        'raise_money.require'    => '请输入计划募资',
        'additional_num.require'    => '请输入增发数量',
        'announcement_date.require'    => '请输入增发公告日',
        'increase_price.require'    => '请输入定增价格',
        'profit.require'    => '请输入增发市盈率',
        'cutoff_date.require'    => '请输入截止日',
        'fundraising_use.require'    => '请输入募资用途',
        'Issue_object.require'    => '请输入发行对象',
        'involved.require'    => '请输入大股东是否参与',
        'sale_condition.require'    => '请输入限售条件',
    ];

    protected $scene = [
        'add'   => ['raise_money','additional_num','announcement_date','increase_price','profit','cutoff_date','fundraising_use','Issue_object','involved','sale_condition'],
        'edit'   => ['raise_money','additional_num','announcement_date','increase_price','profit','cutoff_date','fundraising_use','Issue_object','involved','sale_condition'],
    ];
}