<?php
//配置文件
return [
    'integral' => 10,
    'AllowTime' => [
        'start' => 9,
        'end' => 20
    ],
    'ins_num' => 10,
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__COMMON__'    => __ROOT__.'/static/common',
        '__UPLOAD__'    => __ROOT__.'/uploads',
        '__PROJECT__'   => __ROOT__.'/uploads/project',
        '__ORGANIZE__'  => __ROOT__.'/uploads/organize',
        '__HOME__'      => __ROOT__.'/static/home',
        '__IMG__'       => __ROOT__.'/static/home/images',
        '__CSS__'       => __ROOT__.'/static/home/css',
		'__FA__'        => __ROOT__.'/static/home/faicon',
        '__JS__'        => __ROOT__.'/static/home/js',
        '__DOMAIN__'    =>'http://'.$_SERVER['HTTP_HOST'].'/uploads/',
        '__NEWCJ__'     => __ROOT__.'/static/newcj',
        '__URL__'       =>'http://'.$_SERVER['HTTP_HOST'],
    ],
    //好丽友模板消息列表
//    'company_play_money' => 'Lz68vOrMoQhEbh26J58VsWyKaxKg98aqjWhr2MHDuEA', //奖金发放通知
//    'register_examine' => 'oA5hXYs8U-gHIDtMvfWy4ZF-p0iQI4fi6AKJSefYX9A', //注册审核通知
//    'recommend_success' => '0EZ1nOhoUJInYbp4whrRx7mR9vhpmKCH0-aprCAXAHQ', //推荐成功通知
//    'project_progress' => 'oA6gkYbklxHSCoVf47P1usT_4Od0kansVknriQ9Y1wU', //项目进展通知
//    'study_examine' => 'kPbLCTRDJeUiCyf2t0suzdIsOiSAad-yYjwJEvDSpEo', //老师审核通知
//    'new_project' => 'ToFaplOeB2q4rCXrMFocRYwWT857r1A1bkZ7ZOUqNH8', //新项目通知
//      'kehu_progress' => 'Ne3RBmtHemjqXm8SvtGKK1KnzjzI-ElIP2KO5aFt0UQ',//客服进展客户信息
    //FA財模板消息列表
    'company_play_money'    => 'LbzV_XjWgYBpelokfhmhBK-V3vdGUl_re-_o1QKG2B4', //奖金发放通知
    'register_examine'      => '4MaCIlw6GfI49d-dTMiAltPGsOGTX5shqwLCg0x3waE', //注册审核通知
    'recommend_success'     => 'PdEjt_fQADOzGYFAxedqHur4hZWIAMGMp3NxZS09bwI', //推荐成功通知
    'project_progress'      => '4WfJzopLDe0Rm3Otwjye3SfEwc5Zie-1N4cHLQrqQmY', //项目进展通知
    'study_examine'         => 'tZC1dsoSROf0EY3XH4_u8ymXhikfN9dfM0nRXeiUWoc', //老师审核通知
    'new_project'           => '8Ci8KF4Sk1-M0TdlWBfH7SHoTpjdgr13p9_UKHaVSPs', //新项目通知
    'new_project_info'      => 'ToFaplOeB2q4rCXrMFocRYwWT857r1A1bkZ7ZOUqNH8', //新项目提醒
    'kehu_progress'         => 'A_u2XjAg0PgR1gwCH5kHgLh0dgW5KB97WPzXjO2DsQQ',//客服进展客户信息
    'chat_content'          => 'rQ53Qg49ZU-9oOCINXeqTMydpBsu-VaIgGwJuF9Oz-U',//用户咨询提醒
];
