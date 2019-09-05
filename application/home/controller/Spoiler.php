<?php
namespace app\home\controller;

class Spoiler extends Base
{
    public function index(){
//        $id = $this->request->param('id/d');
        $model = model("spoiler");
        $list  = $model->getSpoilerList();
        $this->assign('id' , $list[count($list)-1]['sp_id']);
        $this->assign('list' , $list);
        $this->assign('user' , session("FINANCE_USER"));
        $info = model("Spoiler")->getSpoilerList(0,1);
        $this->assign('des' , "前沿剧透，推荐给你");
        if(empty($info[0]['title'])){
            $this->assign('title' , '- 前沿剧透');
        } else {
            $this->assign('title' , $info[0]['title']);
        }
        $this->assign('img' , 'http://fin.jrfacai.com/static/frontend/images/share-img.png');
         //客服logo
        $model = model('Project');
        $project = $model->getProjectLimitOne();
        $this->assign('project',$project);
        return view();
    }
    
    public function conlist(){
        $model = model("spoiler");
        $spoiler = $model->getSpoilerList();
        $this->assign('spoiler' , $spoiler);
        $this->assign('title' , '剧透列表 -');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最全面的金融剧透!');
        return view();
    }
    public function thumbsUp(){
        $id = $this->request->param('id/d');
        $model = model("Spoiler");
        if($model->createThumbsUp($id)){
            $this->result( session('FINANCE_USER.userPhoto'), 1,'点赞成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '点赞失败';
            $this->result('', 0, $error, 'json');
        }
    }
    public function selectMoreInfo(){
        $data = input();
        $model = model("spoiler");
        $list = $model->getSpoilerList($data['id']);
        if($list){
            $this->result($list, 1, '搜索成功', 'json');
        } else {
            $this->result([], 0, '已无数据', 'json');
        }
    }
    public function addContent(){
        $data = input();
        $model = model("SpoilerComments");
        if($result = $model->createSpoilerComments($data)){
            $this->result($result, 1, '评论成功', 'json');
        } else {
            $error = $model->getError() ? $model->getError() : '评论失败';
            $this->result('', 0, $error, 'json');
        }
    }
}
