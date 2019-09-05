<?php

namespace app\console\validate;

use think\Validate;

class CarouselFigure extends Validate
{
    protected $rule = [
        'title'    => 'require',
        'img'    => 'require',
    ];

    protected $message = [
        'title.require'    => '标题为空,请输入',
        'img.require'    => '图片为空,请上传',
    ];

    protected $scene = [
        'add'   => ['title' , 'img'],
        'edit'   => ['title' , 'img'],
    ];
}