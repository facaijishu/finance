<?php

namespace app\console\validate;

use think\Validate;

class StYanbaos extends Validate
{
    protected $rule = [
        'title'    => 'require',
        'org'    => 'require',
        'authors'    => 'require',
        'report_date'    => 'require',
        'text'    => 'require',
    ];

    protected $message = [
        'title.require'    => '请输入标题',
        'org.require'    => '请输入机构',
        'authors.require'    => '请输入作者',
        'text.require'    => '请输入研报详情',
    ];

    protected $scene = [
        'add'   => ['title','org','authors','report_date','text'],
        'edit'   => ['title','org','authors','report_date','text'],
    ];
}