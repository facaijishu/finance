<?php

namespace app\console\validate;

use think\Validate;

class ZryxesEffect extends Validate
{
    protected $rule = [
        'sec_uri_tyshortname'    => 'require',
        'trademethod'    => 'require',
        'sublevel'    => 'require',
        'new'    => 'require',
        'direction'    => 'require',
        'price'    => 'require',
        'num'    => 'require',
        'enddate'    => 'require',
        'isdiscount'    => 'require',
        'name'    => 'require',
        'contact'    => 'require',
        'accountment'    => 'require',
    ];

    protected $message = [
        'sec_uri_tyshortname.require'    => '请输入股票名称',
        'trademethod.require'    => '请输入转让类型',
        'sublevel.require'    => '请输入所属层级',
        'new.require'    => '请输入最新收盘价',
        'direction.require'    => '请输入买卖方向',
        'price.require'    => '请输入意向价格',
        'num.require'    => '请输入意向数量',
        'enddate.require'    => '请输入截止时间',
        'isdiscount.require'    => '请输入是否可议价',
        'name.require'    => '请输入联系人',
        'contact.require'    => '请输入联系方式',
        'accountment.require'    => '请输入联系号码',
    ];

    protected $scene = [
        'add'   => ['new','sublevel','trademethod','sec_uri_tyshortname','direction','price','num','enddate','isdiscount','name','contact','accountment'],
        'edit'   => ['new','sublevel','trademethod','sec_uri_tyshortname','direction','price','num','enddate','isdiscount','name','contact','accountment'],
    ];
}