<?php
namespace app\home\controller;

class Financial extends Base
{
    public function index(){
		$neeq = $this->request->param('neeq/s');//股票代码
		$title2 = $this->request->param('name/s');//项目名称
		$id = $this->request->param('id/s');
		if($id == ""){
			$id = 3;
		}
		$this->assign('title2',$title2);
		$this->assign('title' , "- 报表详情");
        $this->assign('img' , '');
        $this->assign('des' , '这里有最具体的定向增发!');
        $plannoticeddate = $this->request->param('plannoticeddate/s');
		//是否收藏
        $user = $this->jsGlobal;
        if(in_array($neeq.'_'.$plannoticeddate, explode(',', $user['member']['collection_zfmxes']))){
            $this->assign('sign' , 'true');
        } else {
            $this->assign('sign' , 'false');
        }
        $model = model("Zfmxes");
        $info = $model->getZfmxesCompletebao($neeq,$id,1);
		//var_dump($info);
		$this->assign('neeq',$neeq);
		$this->assign('info',$info);
		return view();
	}
	public function index2(){
		//查询数据
		$neeq = $this->request->param('neeq/s');//股票代码
		$title2 = $this->request->param('name/s');//项目名称
		$id = $this->request->param('id/s');
		if($id == ""){
			$id = 3;
		}
		$this->assign('title2',$title2);
		$this->assign('title' , "- 报表详情");
        $this->assign('img' , '');
        $this->assign('des' , '这里有最具体的定向增发!');
        $plannoticeddate = $this->request->param('plannoticeddate/s');
		
        $model = model("Zfmxes");
        $info = $model->getZfmxesCompletebao($neeq,$id,0);
		$this->assign('neeq',$neeq);
		$this->assign('info',$info);
		return view();
	}
	public function index3(){
		//查询数据
		$neeq = $this->request->param('neeq/s');//股票代码
		$title2 = $this->request->param('name/s');//项目名称
		$id = $this->request->param('id/s');
		if($id == ""){
			$id = 3;
		}
		$this->assign('title2',$title2);
		$this->assign('title' , "- 报表详情");
        $this->assign('img' , '');
        $this->assign('des' , '这里有最具体的定向增发!');
        $plannoticeddate = $this->request->param('plannoticeddate/s');
		
        $model = model("Zfmxes");
        $info = $model->getZfmxesCompletebao($neeq,$id,2);
		$this->assign('neeq',$neeq);
		$this->assign('info',$info);
		return view();
	}
	//利润
	public function getCodebase(){
		//查询数据
		$neeq = $this->request->param('neeq/s');
		$id = $this->request->param('id/s');
		if($id == ""){
			$id = 3;
		}
        $model = model("Zfmxes");
        $info = $model->getZfmxesCompletebao($neeq,$id,1);
		return json($info);
	}
	//资产负债表
	public function getCodezhai(){
		//查询数据
		$neeq = $this->request->param('neeq/s');
		$id = $this->request->param('id/s');
		if($id == ""){
			$id = 3;
		}
        $model = model("Zfmxes");
        $info = $model->getZfmxesCompletebao($neeq,$id,0);
		return json($info);
		
	}
	//现金流量表
	public function getCodeliu(){
		//查询数据
		$neeq = $this->request->param('neeq/s');
		$id = $this->request->param('id/s');
		if($id == ""){
			$id = 3;
		}
        $model = model("Zfmxes");
        $info = $model->getZfmxesCompletebao($neeq,$id,2);
		return json($info);
		
	}
	
}
