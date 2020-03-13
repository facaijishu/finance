<?php
namespace app\home\controller;

class News extends Base
{
    public function index(){
        $id = $this->request->param('id/d');
        $model = model("News");
        $info = $model->getNewsInfoById($id);
        $this->assign('title' , $info['title'].' -');
        $img = '';
        if(isset($info['top_img'])){
            $img = $info['top_img'];
        }
        $model   = model('MaterialLibrary');
        $img_url = $model->getMaterialInfoById($img);
        $this->assign('img' , $img_url['url']);
        $introdect = '';
        if(isset($info['introduce'])){
            $introdect = $info['introduce'];
        } else {
            $introdect = substr(strip_tags($info['content_h5']), 0, 20);
        }
        $this->assign('des' , $introdect);
        $this->assign('info' , $info);
        return view();
    }
    
    public function newslist(){
        $this->assign('title' , '热门资讯');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最全面的金融资讯!');
        $model          = model("News");
        $consultation   = $model->getListApi(config("ins_num"));
        $num            = count($consultation);
        falog("DDD");
        $this->assign('num',$num);
        $this->assign('consultation' , $consultation);
        return view();
    }
    
	//加载更多
    public function morelist(){
		$num = $this->request->post("id");
		if(!empty($num)){
			$model = model("News");
			$result = $model->alias('n')
						//->join("dict d" , 'n.type = d.id')
						->where(['n.status' => 1])
						->order("is_hot desc,id desc")
						->limit($num,10)
						->select();
			
			$model = model("MaterialLibrary");
			foreach ($result as $key => $value) {
				$top_img = $model->getMaterialInfoById($value['top_img']);
				$result[$key]['top_img']          = $top_img['url'];
				$result[$key]['top_img_compress'] = $top_img['url_compress'];
				$result[$key]['release_date']     = substr($value['create_time'],0,10);
				$result[$key]['source']           = "FA財";
			}
			$this->result($result,1,"success",'json');
		}
    }
    
    
    
    
    
    
}
