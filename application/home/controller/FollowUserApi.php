<?php 
namespace app\home\controller;
/**
 * 
 */
class FollowUserApi extends BaseApi
{
	
	//关注/取关一个合伙人
      public function doFollowUser()
      {
         //当前用户uid(关注人)
          $from_uid = session('FINANCE_USER.uid');
         //关注/取关的合伙人对象
          $to_uid = $this->request->param('uid')?$this->request->param('uid'):0;
          model('FollowUser')->followPublisherApi($from_uid,$to_uid);
      } 
}