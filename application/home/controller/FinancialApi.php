<?php 
namespace app\home\controller;
/**
 * 
 */
class FinancialApi extends BaseApi
{
	//利润表(年报+中报,年报,中报),资产负债表(年报+中报,年报,中报),现金流量表(年报+中报,年报,中报)
	//id=1(年报),id=2(中报),id=3(最新)
	//type=1(利润表),type=0(资产负债表),type=2(现金流量表)
	public function getCompletebao()
	{
		//股票代码
		$neeq = $this->request->param('neeq/s');
		//年报/中报/最新
		$id = $this->request->param('id')?$this->request->param('id'):3;
		//利润表/资产负债表/现金流量表
		$type = $this->request->param('type')?$this->request->param('type'):1;

        $model = model("Project");
        $info = $model->getCompletebaoApi($neeq,$id,$type);
		return json($info);
	}
}