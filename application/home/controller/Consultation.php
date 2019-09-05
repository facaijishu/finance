<?php
namespace app\home\controller;

class Consultation extends Base
{
    public function index(){
        $id = $this->request->param('id/d');
        $model = model("Consultation");
        $info = $model->getConsultationInfoById($id);
        $this->assign('title' , $info['title'].' -');
        $img = '';
        if(isset($info['top_img'])){
            $img = $info['top_img'];
        }
        $model = model('MaterialLibrary');
        $img_url = $model->getMaterialInfoById($img);
        $this->assign('img' , $img_url['url']);
        $introdect = '';
        if(isset($info['introduce'])){
            $introdect = $info['introduce'];
        } else {
            $introdect = substr(strip_tags($info['content']), 0, 20);
        }
        $this->assign('des' , $introdect);
        $this->assign('info' , $info);
        return view();
    }
    public function conlist(){
        $this->assign('title' , '资讯列表 -');
        $this->assign('img' , '');
        $this->assign('des' , '这里有最全面的金融资讯!');
        $model = model("consultation");
        $consultation = $model->getConsultationList(config("ins_num"));
		$num = count($consultation);
		$this->assign('num',$num);
        $this->assign('consultation' , $consultation);
        return view();
    }
	//加载更多
	public function conlistdata(){
		$num = $this->request->post("id");
		if(!empty($num)){
			$model = model("consultation");
			$result = $model->alias('con')
						->join("dict d" , 'con.type = d.id')
						->where(['con.status' => 1 , 'd.des' => ''])
						->order("is_hot desc,con_id desc")
						->limit($num,10)
						->select();
			
			$model = model("MaterialLibrary");
			foreach ($result as $key => $value) {
				$top_img = $model->getMaterialInfoById($value['top_img']);
				$result[$key]['top_img'] = $top_img['url'];
				$result[$key]['top_img_compress'] = $top_img['url_compress'];
			}
			$this->result($result,1,"success",'json');
		}
    }
}
