<?php
namespace app\home\controller;

class Message extends Base
{
    public function index(){
        $id = $this->request->param('id/d');
        $RequestAddress = [
            'controller' => $this->Request['controller'] , 
            'action' => $this->Request['action'] , 
            'id' => $id
                ];
        session('RequestAddress' , $RequestAddress);
        $info = $this->jsGlobal;
        if(empty($info['member']['userPhone']) || $info['member']['userType'] == 0){
            $url = url('Binding/index');
            header("Location: $url");
            die;
        } else {
            $model = model("project");
            $info = $model->getProjectSimpleInfoById($id);
            $this->assign('info' , $info);
            $this->assign('title' , $info['pro_name'].' -');
            $img = '';
            if(isset($info['top_img'])){
                $img = $info['top_img'];
            }
            $this->assign('img' , $img);
            $introdect = '';
            if(isset($info['introduction'])){
                $introdect = $info['introduction'];
            }
            $this->assign('des' , $introdect);
            return view();
        }
    }
    public function add(){
        $data = [];
        $content = $this->request->param('content/s');
        $data['content'] = $content;
        $model = model("message");
        if($model->createMessage($data)){
            $data['url'] = url('Message/addok' , ['id' => session('RequestAddress.id')]);
            session('RequestAddress' , null);
            $this->result($data, 1, '', 'json');
        } else {
            $this->result('', 0, '留言失败', 'json');
        }
    }
    public function addok(){
        $id = $this->request->param('id/d');
        $model = model("project");
        $info = $model->getProjectSimpleInfoById($id);
        $url = url('ProjectInfo/index' , ['id' => $id]);
        $this->assign('url' , $url);
        $this->assign('title' , $info['pro_name'].' -');
        $img = '';
        if(isset($info['top_img'])){
            $img = $info['top_img'];
        }
        $this->assign('img' , $img);
        $introdect = '';
        if(isset($info['introduction'])){
            $introdect = $info['introduction'];
        }
        $this->assign('des' , $introdect);
        return view();
    }
}