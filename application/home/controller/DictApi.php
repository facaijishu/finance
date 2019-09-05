<?php 
namespace app\home\controller;
class DictApi extends BaseApi
{
	//获取业务类型一级和二级偏好标签
	public function getServiceTypeDict()
	{
        $dict = model('Dict');
        $dict->getServiceTypeDictApi();
	}
	
	//获取所属行业一级和二级偏好标签
	public function getIndustryDict()
	{
		$dict = model('Dict');
		$dict->getIndustryDictApi();
	}
	
	//获取投融规模的标签
	public function getSizeDict()
	{
		$dict = model('Dict');
		$dict->getSizeDictApi();
	}
	
	//获取所在省份的标签
	public function getToProvinceDict()
	{
       $dict = model('Dict');
	   $dict->getToProvinceDictApi();
	}
	
}