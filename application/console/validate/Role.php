<?php
/**
 * Created by PhpStorm.
 * User: WL
 * Date: 2017/3/16
 * Time: 0:25
 */

namespace app\console\validate;

use think\Validate;

class Role extends Validate
{
    protected $rule = [
        ['pid', 'require|number|token', '请选择父角色|父角色数据类型错误'],
        ['name', 'require|length:2,20', '请输入角色名称|角色名称长度为2~20位']
    ];
}