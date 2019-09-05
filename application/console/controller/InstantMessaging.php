<?php
namespace app\console\controller;
use think\Image;

class InstantMessaging extends ConsoleBase{
    public function index(){
        $info = \app\console\service\User::getInstance()->getInfo();
        $model = model("SelfCustomerService");
        $info = $model->getSelfCustomerServiceInfoByUid($info['uid']);
        if(empty($info) || $info['kf_openId'] == '' || $info['status'] != 1){
            return view("fail");
        } else {
            $model = model("KfMessageLast");
            $list = $model->getMessageListByScsId($info['scs_id']);
            $this->assign('list' , $list);
            return view();
        }
    }
    public function selectMemberList(){
        $info = \app\console\service\User::getInstance()->getInfo();
        $model = model("SelfCustomerService");
        $info = $model->getSelfCustomerServiceInfoByUid($info['uid']);
        $model = model("KfMessageLast");
        $list = $model->getMessageListByScsId($info['scs_id']);
        $this->result($list, 1, '轮循成功', 'json');
    }
    public function getMemberInfo(){
        $uid = $this->request->post('uid');
        $model = model("Member");
        $info = $model->getMemberInfoById($uid);
        $info['userSex'] = $info['userSex'] == 1 ? '男' : '女';
        if($info['userType'] == 0){
            $info['userType'] = '游客';
        }elseif ($info['userType'] == 1) {
            $info['userType'] = '绑定用户';
        }elseif ($info['userType'] == 2) {
            $info['userType'] = '金融合伙人';
        }
        if($info['superior'] != 0){
            $superior = $model->getMemberInfoById($info['superior']);
            $info['superior'] = $superior['userName'];
        } else {
            $info['superior'] = '暂无';
        }
        
        $model = model("KfMessageLast");
        $info1 = $model->getMessageInfoByUid($uid);
        $model = model("KfMessage");
        if($info1['is_read'] != 0){
            $list1 = $model->getMessageList($uid ,0 ,$info1['is_read']);
        } else {
            $list1 = [];
        }
        $page = config("paginate");
        $list2 = $model->getMessageList($uid , $info1['is_read'] , $page['list_rows']);
        $list1 = array_reverse($list1);
        $list2 = array_reverse($list2);
        
        if(!empty($list1)){
            $max = $list1[count($list1)-1]['km_id'];
            if(!empty($list2)){
                $min = $list2[0]['km_id'];
            } else {
                $min = $list1[0]['km_id'];
            }
        } else {
            if(!empty($list2)){
                $max = $list2[count($list2)-1]['km_id'];
                $min = $list2[0]['km_id'];
            }
        }
        $info['min'] = $min;
        $info['max'] = $max;
        $info['new'] = $list1;
        $info['old'] = $list2;
        $this->result($info, 1, '搜索成功', 'json');
    }
    public function selectNewInfo(){
        $data = input();
        $model = model("KfMessageLast");
        $info = $model->getNewInfoByUid($data['uid']);
        $model = model("KfMessage");
        $list = $model->getAllInfoByUid($data['uid'] , 0 , $data['max']);
        if($list){
            $this->result($list, 1, '搜索成功', 'json');
        } else {
            $this->result('', 0, '搜素失败', 'json');
        }
    }
    public function add(){
        $data = input();
        $result1 = model("KfMessageLast")->where(['m_uid' => $data['uid']])->update(['direction' => '<--' , 'last_time' => time() , 'first_time' => 0]);
        $model = model("KfMessage");
        if($result1 && $result2 = $model->addKfMessage($data , 'text')){
            $this->result($result2, 1, '保存成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '保存失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function upload(){
        $data = input();
        $file = request()->file("file");
        if($file){
            $info = $file->getInfo();
            $name = $info['name'];
            $type = strtolower(substr($name,strrpos($name,'.')+1));
            if(in_array($type, ['jpg' , 'jpeg' , 'gif' , 'png'])){
                $path = $data['type'] . DS . 'img';
                $path_cut = $data['type'] . DS . 'img_cut';
            }elseif (in_array($type, ['pdf'])) {
                $path = $data['type'] . DS . 'pdf';
            }elseif (in_array($type, ['mkv','rmvb','mp4'])) {
                $path = $data['type'] . DS . 'video';
            }
            $dir = ROOT_PATH . 'public' . DS . 'uploads' . DS . $path;
            if(!is_dir($dir)){
                mkdir($dir , 0777, true);
            }
            $result = $file->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $path);
            if(isset($path_cut)){
                $dir = ROOT_PATH . 'public' . DS . 'uploads' . DS . $path_cut;
                if(!is_dir($dir.DS.substr($result->getSaveName(), 0 , 8))){
                    mkdir($dir.DS.substr($result->getSaveName(), 0 , 8) , 0777, true);
                }
                $image = Image::open($result);
                $width = $image->width();
                $height = $image->height();
                if($width > $height){
                    $image->crop(floor($height/2)*2, floor($height/2)*2, $width/2-floor($height/2), $height%2)->save($dir . DS . $result->getSaveName());
                } else {
                    $image->crop(floor($width/2)*2, floor($width/2)*2, $width%2, $height/2-floor($width/2))->save($dir . DS . $result->getSaveName());
                }
            }
            if($result){
                $model = model("material_library");
                if(isset($path_cut)){
                    $id = $model->createMaterial($path , $path_cut , $result);
                } else {
                    $id = $model->createMaterial($path , '' , $result);
                }
                if($id){
                    $data['url'] = $path . DS . $result->getSaveName();
                    $result1 = model("KfMessageLast")->where(['m_uid' => $data['uid']])->update(['direction' => '<--' , 'last_time' => time() , 'first_time' => 0]);
                    $model = model("KfMessage");
                    $result2 = $model->addKfMessage($data , 'image');
                    $this->result($result2 ,1 , '上传成功' , 'json');
                } else {
                    $error = $model->getError() ? $model->getError() : '保存上传材料失败';
                    $this->result('', 0, $error, 'json');
                }
            }else{
                $this->result('', 0, '上传失败', 'json');
            }
        } else {
            $this->result('', 0, '上传文件不存在', 'json');
        }
    }
}