<?php
/**
 * Created by PhpStorm.
 * User: WL
 * Date: 2017/3/8
 * Time: 23:35
 */

namespace app\console\controller;

use \sys\RBAC;
use app\common\controller\Base;
use app\console\service\User;
use app\console\model\Menu;
use think\Lang;

class ConsoleBase extends Base
{

    public function __construct()
    {
        //模板主题
        $config['view_theme']='default';
        $config['template']['view_path'] = APP_PATH . request()->module() . DS . 'view' . DS . $config['view_theme'] . DS;
        //添加配置
        config($config);
        parent::__construct();
    }


    public function _initialize() {
        $this->lang = new Lang();
        config([
            "user_auth_on"          => true, //是否开启权限认证
            "user_auth_type"        => 2, //默认认证类型 1 登录认证 2 实时认证
            "require_auth_module"   => "", //需要认证模块
            "not_auth_module"       => "Publics", //无需认证模块
            "user_auth_gateway"     => url('Publics/login') //登录地址
        ]);

        $access_id = $this->getAccessId();
        if (false == RBAC::AccessDecision($access_id)) {
            //检查是否登录
            if (false === RBAC::checkLogin()) {
                //跳转到登录界面
                $this->redirect(config('user_auth_gateway'));
            }
            //没有操作权限
            $this->permissionDisplay();
        }
        parent::_initialize();
        //验证登录
        $this->VerifyLogin();

        //获取菜单表列
        $menu = new Menu;

        //菜单缓存
        $menu->menu_cache();
        //获取短信地址
        $this->getSmsUrl();

        //print_r($menu->getMenuList());die;
        $this->assign('menu_list', $menu->getMenuList());
        $this->assign('user', User::getInstance()->getInfo());

        //图片上传配置
        $image_upload_config = config('file_upload.image');
        $this->assign('image_upload_config', json_encode($image_upload_config));
    }
    public function getSmsUrl(){
        $this->assign('url' , url("Sms/index"));
//        vendor("Passport.SingleLogin");
//        $login = new \SingleLogin();
//        $this->assign('smsUrl' , $login->buildLoginURI('jrfacai'));
    }

    /**
     * 验证登录
     * @return boolean
     */
    private function VerifyLogin()
    {
        //检查是否登录
        $uid = (int)User::getInstance()->isLogin();
        if (empty($uid)) {
            //$this->error('请先登录！', url('publics/login'));
            return false;
        }
        //获取当前登录用户信息
        $userInfo = User::getInstance()->getInfo();
        if (empty($userInfo)) {
            User::getInstance()->logout();
            $this->error(lang('get_no_user_please_relogin'), url('publics/login'));
            return false;
        }
        //是否锁定
        if (!$userInfo->status) {
            User::getInstance()->logout();
            $this->error(lang('account_locked'), url('publics/login'));
            return false;
        }
    }

    /**
     * 取得访问权限点/菜单ID
     * @return array|bool|false|\PDOStatement|string|\think\Model
     */
    private function getAccessId(){
        if(!is_null(request()->param('menu_id/d'))) {
            $map['id']          = request()->param('menu_id/d');
        }
        if(!is_null(request()->param('menu_type/d'))) {
            $map['type']        = request()->param('menu_type/d');
        }

        $map['module']      = strtolower(request()->module());
        $map['controller']  = \think\Loader::parseName(request()->controller());
        $map['action']      = strtolower(request()->action());

        $menu = model('Menu')->field('id')->where($map)->find();
        return $menu ? $menu->id : false;
    }

    /**
     * 无访问权限提示显示
     */
    private function permissionDisplay()
    {
        $accept = $this->request->header('accept');

        if(stripos($accept, 'html') !== false){
            $html = view('common/permission_error', ['msg' => lang('do_not_have_privilege')]);
            throw new \think\exception\HttpResponseException($html);
        }else{
            $this->error(lang('do_not_have_privilege'));
        }
        exit();
    }
    public function getTextAreaValue($data){
        if(!empty($data)){
            $str = preg_replace('/\s+/', '<br>', $data);
            $arr = explode('<br>', $str);
            $data = $arr;
        }
        return $data;
    }
}