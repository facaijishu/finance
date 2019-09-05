<?php
//配置文件
return [
    //超级管理员用户ID
    'administrator'         =>[1,2,44,56],
    'agentid'               =>1000003,
    'corpid'                =>'wwbcd6599e0d0c363c',
    'corpsecret'            =>'3qvYOJVvJLgiRPDf3aFJTLlEvsLRg7KosucfCjo4iHg',

    //分页配置
    'paginate'              => [
        'list_rows' => 10,
        'type'      => 'bootstrap'
    ],
    'start_time' => '2017-12',
    //好丽友模板消息列表
//    'company_play_money' => 'Lz68vOrMoQhEbh26J58VsWyKaxKg98aqjWhr2MHDuEA', //奖金发放通知
//    'register_examine' => 'oA5hXYs8U-gHIDtMvfWy4ZF-p0iQI4fi6AKJSefYX9A', //注册审核通知
//    'recommend_success' => '0EZ1nOhoUJInYbp4whrRx7mR9vhpmKCH0-aprCAXAHQ', //推荐成功通知
//    'project_progress' => 'oA6gkYbklxHSCoVf47P1usT_4Od0kansVknriQ9Y1wU', //项目进展通知
//    'study_examine' => 'kPbLCTRDJeUiCyf2t0suzdIsOiSAad-yYjwJEvDSpEo', //老师审核通知
//    'new_project' => 'ToFaplOeB2q4rCXrMFocRYwWT857r1A1bkZ7ZOUqNH8', //新项目通知
    //FA財模板消息列表
    'company_play_money' => 'LbzV_XjWgYBpelokfhmhBK-V3vdGUl_re-_o1QKG2B4', //奖金发放通知
    'register_examine' => '4MaCIlw6GfI49d-dTMiAltPGsOGTX5shqwLCg0x3waE', //注册审核通知
    'recommend_success' => 'PdEjt_fQADOzGYFAxedqHur4hZWIAMGMp3NxZS09bwI', //推荐成功通知
    'project_progress' => '4WfJzopLDe0Rm3Otwjye3SfEwc5Zie-1N4cHLQrqQmY', //项目进展通知
    'study_examine' => 'tZC1dsoSROf0EY3XH4_u8ymXhikfN9dfM0nRXeiUWoc', //老师审核通知
    'new_project' => '8Ci8KF4Sk1-M0TdlWBfH7SHoTpjdgr13p9_UKHaVSPs', //新项目通知
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => APP_PATH . 'console/view/default/common/' . DS . 'dispatch_jump.html',
    'dispatch_error_tmpl'    => APP_PATH . 'console/view/default/common/' . DS . 'dispatch_jump.html',

    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__UPLOAD__'    => __ROOT__.'/uploads',
        '__COMMON__'    => __ROOT__.'/static/common',
        '__STATIC__'    => __ROOT__.'/static/console',
        '__IMG__'       => __ROOT__.'/static/console/images',
        '__CSS__'       => __ROOT__.'/static/console/css',
        '__JS__'        => __ROOT__.'/static/console/js',
        '__DOMAIN__'    =>'http://'.$_SERVER['HTTP_HOST'].'/uploads/',
        '__URL__'       =>'http://'.$_SERVER['HTTP_HOST'],
    ],

    'file_upload'   =>[
        'image' => [
            'extension' => ['jpg', 'gif', 'png'],
            'size' => 1024 //单位：KB
        ],
        'file' => []
    ],
];
