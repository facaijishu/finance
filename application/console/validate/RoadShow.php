<?php

namespace app\console\validate;

use think\Validate;

class RoadShow extends Validate
{
    protected $rule = [
        'road_name'    => 'require',
        'company_name'    => 'require',
        'road_introduce'    => 'require',
        'top_img'    => 'require',
        'video'    => 'require',
//        'speaker'    => 'require',
    ];

    protected $message = [
        'road_name.require'    => '路演项目名称为空,请输入',
        'company_name.require'    => '企业名称为空,请输入',
        'road_introduce.require'    => '路演项目简介为空,请输入',
        'top_img.require'    => '头图为空,请上传',
        'video.require'    => '视频为空,请上传',
//        'speaker.require'    => '主讲人照片为空,请上传',
    ];

    protected $scene = [
        'add'   => ['road_name' , 'company_name' , 'road_introduce','top_img','video'],
        'edit'   => ['road_name' , 'company_name' , 'road_introduce','top_img','video'],
    ];
}