<?php
namespace app\home\controller;

class ProjectClue extends Base
{   

    public function index($pro_origin = 0){
        $this->assign('pro_origin' , $pro_origin);
        $this->assign('title' , ' - 项目投递');
        $this->assign('img' , '');
        $this->assign('des' , '这里可以投递您的项目!');
          //客服logo
        $model = model('Project');
        $project = $model->getProjectLimitOne();
        $this->assign('project',$project);
        return view();
    }
    public function add(){
        if(request()->isPost()){
            $data    = input();
            $uid     = session('FINANCE_USER.uid');
            $through = model('Through')->getThroughInfoById($uid);
            $data['contacts']           = $through['realName'];
            $data['contacts_tel']       = $through['phone'];
            $data['contacts_email']     = $through['email'];
            $data['contacts_position']  = $through['position'];
            $data['company_name']       = $through['company'];
            
            $projectclue = model('ProjectClue');
            if($projectclue->createProjectClue($data)){
                $url = url('ProjectClue/succeed');
                header("Location: $url");
                die;
            }else{
                $error = $projectclue->getError() ? $projectclue->getError() : '添加项目失败';
                $url = url('ProjectClue/lose' , ['error' => $error]);
                header("Location: $url");
                die;
            }
        } else {
            $pro_origin = input('pro_origin');
            $this->assign('pro_origin',$pro_origin);
            
            $model = model("dict");
            $list = $model->getDictByType("capital_plan");
            $this->assign('list' , $list);
            $this->assign('title' , '- 项目投递');
            $this->assign('img' , '');
            $this->assign('des' , '这里可以提交您的项目!');
            return view();
        }
    }
    public function upload(){
        $file = $_FILES;
        if($file){
            $name = explode('.', $file['file']['name']);
            $type = $name[count($name)-1];
            $filename = date('ymd').rand(1000, 9999).'.'.$type;
            if(in_array($type, ['jpg' , 'jpeg' , 'gif' , 'png'])){
                $path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'home' . DS . 'project_clue' . DS;
                if(!is_dir($path)){
                    mkdir($path , 0777 , true);
                }
                $result = move_uploaded_file($file['file']['tmp_name'], $path.$filename);
                if($result){
                    $data['name'] = $filename;
                    $this->result($data ,1 , '上传成功' , 'json');
                }else{
                    $this->result('', 0, '上传失败', 'json');
                }
            } else {
                $this->result('' ,0 , '上传文件格式不正确' , 'json');
            }
        } else {
            $this->result('' ,0 , '图片不存在' , 'json');
        }
    }
    public function succeed(){
        $this->assign('title' , '- 项目投递');
        $this->assign('img' , '');
        $this->assign('des' , '这里可以提交您的项目!');
        return view();
    }
    public function lose(){
        $error = $this->request->param('error/s');
        $this->assign('error' , $error);
        $this->assign('title' , '- 项目投递');
        $this->assign('img' , '');
        $this->assign('des' , '这里可以提交您的项目!');
        return view();
    }
}
