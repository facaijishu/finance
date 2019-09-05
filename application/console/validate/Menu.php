<?php
/**
 * Created by PhpStorm.
 * User: WL
 * Date: 2017/3/16
 * Time: 0:25
 */

namespace app\console\validate;

use think\Validate;

class Menu extends Validate
{
    protected $rule = [
        ['pid', 'require|number|token', '请选择上级菜单|菜单数据类型错误'],
        ['name', 'require|length:2,50', '请输入菜单名称|菜单名称长度为2~50位'],
        ['module', 'require', '请输入模块名'],
        ['controller', 'require', '请输入控制器名'],
        ['action', 'require', '请输入方法名'],
        ['type', 'require', '请选择菜单类型']
    ];
}