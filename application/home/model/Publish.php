<?php 
namespace app\home\model;
/**
 * 
 */
class Publish extends BaseModel
{   
    //发布一条发现
	public function addPublishApi($pb = [],$tag_name = '')
	{
       if (empty($pb)) {
       	   $this->code = 201;
       	   $this->msg = '数据不存在';
       	   return $this->result('',$this->code,$this->msg);
       }
       $this->startTrans();
       try {
       	  $this->allowField(true)->save($pb);
          $pub_id = $this->getLastInsId();
          $dict = model('Dict');

          if ($tag_name) {
             //提交的标签名和数据字典-个性标签表标签名去重后插入到数据字典-个性标签表
             $table_tag = model('DictPerson')->field('name')->select()->toArray();
             $tag_dict_person = [];
             if (!empty($table_tag)) {
                $name_arr = array_column($table_tag, 'name');
             } else {
                $name_arr = [];
             }
             
             
             if (strpos($tag_name,',') !== false) {
             	//如果添加多个
             	$tag_id_column = [];
             	$tag_arr = explode(',', $tag_name);
               
             	foreach ($tag_arr as $v) {
                    
                    if (!in_array($v, $name_arr)) {
                        $tag_dict_person[] = ['name'=>$v,'create_time'=>time()];
                    }    
                    
                            
                    $tag_id_column[] = ['tag_name'=>$v,'create_time'=>time(),'pub_id'=>$pub_id]; 	   	
             	}
                model('DictPerson')->allowField(true)->saveAll($tag_dict_person);
             	model('TagsPublish')->allowField(true)->saveAll($tag_id_column);
             }else{
                
                if (!in_array($tag_name, $name_arr)) {
                    model('DictPerson')->allowField(true)->save(['name'=>$tag_name,'create_time'=>time()]);
                }    
                
                //如果添加一个
                model('TagsPublish')->allowField(true)->save(['tag_name'=>$tag_name,'create_time'=>time(),'pub_id'=>$pub_id]);

             }

          }
          //每发布一条发现,member对应的需求数量+1
          model('Member')->where(['uid'=>$pb['uid']])->setInc('require_num');
          //同时更新到session中
          $info = model('Member')->where(['uid'=>$pb['uid']])->find()->toArray();
          session('FINANCE_USER',$info);
          $this->commit();
          $this->code = 200;
          $this->msg = '恭喜 发布成功';
          return $this->result('',$this->code,$this->msg);

       } catch (\Exception $e) {
       	  $this->rollback();
          $this->code = 218;
          $this->msg = '发布失败 '.$e->getMessage();
          return $this->result('',$this->code,$this->msg);   
       }
       
       
	}
      //发布列表搜索展示
      public function publishListSearch($keyword = '',$page = 1,$uid = 0,$length = 2)
      {
         
            $member = model('Member');
            $user = $member->field('uid')->where(['uid'=>$uid])->find();
            if(empty($user)){
                $this->code = 208;
                $this->msg = '缺少用户id参数';
                return $this->result('',$this->code,$this->msg); 
            }
            $offset = ($page-1)*$length;
            //if($keyword === ''){
                
                $list = $this->alias('pub')
                     ->join('Member m','pub.uid=m.uid')
                     ->field("pub.id,m.uid,m.userPhoto,m.realName,m.company_jc,m.position,pub.content,pub.img_id,pub.create_time,pub.point_num,pub.comment_num")
                     ->where(['m.status'=>1,'pub.is_del'=>2,'pub.status'=>1]);
                //查询当前用户已举报的一键发布
                $remove_pub = model('Report')->field('pub_id')
                                             ->where(['uid'=>$uid])
                                             ->select()
                                             ->toArray();
                if (!empty($remove_pub)) {
                    $remove_id = implode(',',array_column($remove_pub, 'pub_id'));
                    $map['id'] = array('not in',$remove_id);
                    $list->where($map);
                }
         
                /**拼接根据用户选择的精准匹配标签和一键发布的个性标签匹配条件**/
                $list = $list->limit($offset,$length)
                             ->order('pub.rank desc,pub.id desc')
                             ->select()
                             ->toArray();
                
                if(empty($list)){
                    $this->code = 201;
                    $this->msg = '数据不存在';
                    return $this->result('',$this->code,$this->msg);
                }
                //处理是否关注,图片,偏好标签,个性标签展示
                
                //查询当前登录用户已关注的人
                $to_uid_arr = [];
                $to_uid = model('FollowUser')->field('to_uid')
                                             ->where(['from_uid'=>$uid,'is_del'=>2])
                                             ->select()
                                             ->toArray();
                if (!empty($to_uid)) {
                    $to_uid_arr= array_column($to_uid, 'to_uid');
                }
                //图片
                $ml = model('MaterialLibrary');
                // //添加偏好标签的发现
                // $pd = model('PublishDict');
                //添加个性标签的发现
                $my = model('TagsPublish');
                
                foreach ($list as &$v) {
                    $v['img_url'] = [];
                    //是否关注
                    if (in_array($v['uid'], $to_uid_arr)) {
                        $v['is_follow'] = '已关注';
                    }else{
                        $v['is_follow'] = '关注';
                    }
                    //是否添加了图片
                    if ($v['img_id']) {
                       $url = $ml->field('url')->where('ml_id in ('.$v['img_id'].')')->select()->toArray();
                       if (!empty($url)) {
                           
                            $v['img_url'] = $url;
                       }
                    }

                    //个性标签名
                    $my_tag = [];

                    //是否添加个性标签
                    $tag = $my->field('tag_name')
                              ->where(['pub_id'=>$v['id']])
                              ->select()
                              ->toArray();
                    if (!empty($tag)) {
                        foreach ($tag as &$t) {
                            $my_tag[] = $t['tag_name'];
                        }
                    }
                   
                    $v['all_tag']     = $my_tag;
                    $v['create_time'] = friend_date(strtotime($v['create_time']));
                    
                    //当前用户点赞过哪些一键发布
                    $point = model('Point')->field('pub_id')
                                           ->where(['uid'=>$uid,'is_del'=>2])
                                           ->select()
                                           ->toArray();
                                           $v['is_point'] = 0;
                    if (!empty($point)) {
                        foreach ($point as &$p) {
                           if($v['id']==$p['pub_id']){
                               $v['is_point'] = 1;
                           }else{
                               $v['is_point'] = 0;
                           }
                        }
                    }else{
                        $v['is_point'] = 0;
                    }
                }

                return $this->result($list);
            /*}else{

                //符合匹配结果的用户id
                $match_uid = '';
                //符合匹配结果的发布id
                $match_id = '';
                $list = $this->alias('pub')
                     ->join('Member m','pub.uid=m.uid')
                     ->join('Through th','pub.uid=th.uid')
                     ->field("pub.id,m.uid,m.userPhoto,th.realName,th.company_jc,th.position,pub.content,pub.img_id,pub.create_time,pub.point_num,pub.comment_num");
                     
                //根据关键词匹配用户名
                $realName = model('Through')->field('m.uid')
                                            ->where('th.realName like "%'.$keyword.'%"')
                                            ->select()
                                            ->toArray();     
                if (!empty($realName)) {
                    // $this->code = 214;
                    // $this->msg = '未搜索到结果';
                    // return $this->result('',$this->code,$this->msg);
                    $match_uid .= 'pub.uid in ('.implode(',',array_column($realName,'uid')).') or ';
                }else{
                    $match_uid .= 'pub.uid in ("") or ';
                }
                
                //根据关键词匹配个性标签
                $tags = model('TagsPublish')->Distinct(true)
                                            ->field('pub_id')
                                            ->where('tag_name like "%'.$keyword.'%"')
                                            ->select()
                                            ->toArray();
                if (!empty($tags)) {
                    $match_id .= 'pub.id in ('.implode(',',array_column($tags,'pub_id')).') or ';
                }else{
                    $match_id .= 'pub.id in ("") or ';
                }
                
                $result = rtrim($match_uid.$match_id,' or ');
                
                $list->where($result);
                //查询当前用户已举报的一键发布
                $remove_pub = model('Report')->field('pub_id')
                                             ->where(['uid'=>$uid])
                                             ->select()
                                             ->toArray();
                if (!empty($remove_pub)) {
                    $remove_id = implode(',',array_column($remove_pub, 'pub_id'));
                    $map['id'] = array('not in',$remove_id);
                    $list->where($map);
                }
                    $list = $list->limit($offset,$length)
                                ->order('pub.rank desc,pub.id desc')
                                ->select()
                                ->toArray();
                if (empty($list)) {
                    $this->code = 214;
                    $this->msg = '未搜索到结果';
                    return $this->result('',$this->code,$this->msg);
                }

                //处理是否关注,图片,点赞,评论,个性标签展示
                
                //当前用户是否关注查询的发现
                //查询当前用户关注的合伙人(未取关)
                $to_uid_arr = [];
                $to_uid = model('FollowUser')->field('to_uid')->where(['from_uid'=>$uid])->select()->toArray();
                if (!empty($to_uid)) {
                    $to_uid_arr= array_column($to_uid, 'to_uid');
                }
                //图片
                $ml = model('MaterialLibrary');
                //添加个性标签的发现
                $my = model('TagsPublish');
                
                foreach ($list as &$v) {

                    //是否关注
                    if (in_array($v['uid'], $to_uid_arr)) {
                        $v['is_follow'] = '已关注';
                    }else{
                        $v['is_follow'] = '关注';
                    }
                    $v['img_url'] = [];
                    //是否添加了图片
                    if ($v['img_id']) {
                       $url = $ml->field('url')->where('ml_id in ('.$v['img_id'].')')->select()->toArray();
                       if (!empty($url)) {
                           
                            $v['img_url'] = $url;

                       }
                    }

                    //个性标签名
                    $my_tag = [];

 
                    //是否添加个性标签
                    $tag = $my->field('tag_name')
                              ->where(['pub_id'=>$v['id']])
                              ->select()
                              ->toArray();
                    if (!empty($tag)) {
                        $my_tag =  array_column($tag,'tag_name');
                    }
                    
                    $v['all_tag'] = $my_tag;
                    $v['create_time'] = friend_date(strtotime($v['create_time']));
                    //当前用户点赞过哪些一键发布
                    $point = model('Point')->field('pub_id')
                                           ->where(['uid'=>$uid])
                                           ->select()
                                           ->toArray();
                    if (!empty($point)) {
                         $point_arr = array_column($point, 'pub_id');
                        if (in_array($v['id'],$point_arr)) {
                            $v['is_point'] = 1;
                        }else{
                            $v['is_point'] = 0;
                        }
                    }
                }

                return $this->result($list);
            }*/
       }
       
      //发现详情(发现内容+当前发现的评论+转发当前发现的人)
      public function showDetail($uid = 0,$pub_id = 0)
      {     

            if (empty($uid) || !is_numeric($uid) ) {
              $this->code = 208;
              $this->msg = '缺少用户参数或参数非法';
              return $this->result('',$this->code,$this->msg);
            }

            if (empty($pub_id) || !is_numeric($pub_id)) {
              $this->code = 226;
              $this->msg = '缺少发现参数或参数非法';
              return $this->result('',$this->code,$this->msg);
            }
            //发现的内容
            $pub = $this->alias('pub')
                        ->join('Member m','pub.uid=m.uid')
                        ->field("pub.id,m.uid,m.userPhoto,m.realName,m.company_jc,m.position,pub.content,pub.img_id,pub.create_time,pub.point_num,pub.comment_num")
                        ->where(['pub.id'=>$pub_id])
                        ->find();
            if (empty($pub)) {
                $this->code = 201;
                $this->msg = '数据不存在';
                return $this->result('',$this->code,$this->msg);
            }
            $pub = $pub->toArray();
            //是否关注
            $fu = model('FollowUser')->where(['from_uid'=>$uid,'to_uid'=>$pub['uid']])->find();
            if (empty($fu)) {
                //未关注
                $pub['is_follow'] = '关注';
            }else{
                if ($fu['is_del'] == 1) {
                    //未关注
                    $pub['is_follow'] = '关注';
                } 
               
                if ($fu['is_del'] == 2) {
                    //已关注
                    $pub['is_follow'] = '已关注';
                } 
            }
            //当前用户是否点赞过该发现
            $point = model('Point')->field('pub_id')
                                   ->where(['uid'=>$uid,'is_del'=>2])
                                   ->select()
                                   ->toArray();
            if (!empty($point)) {
                 foreach ($point as &$p) {
                     if($pub['id']==$p['pub_id']){
                         $pub['is_point'] = 1;
                     }else{
                         $pub['is_point'] = 0;
                     }
                 }
                 
            }else{
                $pub['is_point'] = 0;
            }
            //添加的图片
            $pub['img_url'] = [];
            if ($pub['img_id']) {
                $ml = model('MaterialLibrary')->field('url')
                                              ->where('ml_id in ('.$pub['img_id'].')')
                                              ->select()
                                              ->toArray();
                if (empty($ml)) {
                    $this->code = 232;
                    $this->msg = '该发现的图片不存在';
                    return $this->result('',$this->code,$this->msg);
                }

                $pub['img_url'] = $ml;
            } 

            //添加的个性标签
            $my_tag = [];
            $tp = model('TagsPublish')->field('tag_name')
                                      ->where(['pub_id'=>$pub['id'],'is_del'=>2])
                                      ->select()
                                      ->toArray();
            if(!empty($tp)){
               //是否添加个性标签
                if (!empty($tp)) {
                    foreach ($tp as &$t) {
                       $my_tag[] = $t['tag_name'];
                   }
               }
            }

            $pub['all_tag'] = implode(',',$my_tag);
            //分享内容
            //$pub['share']['desc']   = $pub['content'];
            $pub['share']['desc']   = "对接请点击，直接对话";
            $pub['share']['title']  = $pub['realName'].'的业务';
            $pub['share']['imgUrl'] = $pub['userPhoto'];

            return $this->result($pub);
      }
     
     //获取个人未被删除的发现
      public function getMyPublishApi($uid = 0,$page = 1,$length = 2)
      {
         
            $member = model('Member');
            $user = $member->field('uid')->where(['uid'=>$uid])->find();
            if(empty($user)){
                $this->code = 208;
                $this->msg = '缺少用户id参数';
                return $this->result('',$this->code,$this->msg); 
            }
            $offset = ($page-1)*$length;
           
                $list = $this->alias('pub')
                     ->join('Member m','pub.uid=m.uid')
                     ->field("pub.id,m.uid,m.userPhoto,m.realName,m.company_jc,m.position,pub.content,pub.img_id,pub.create_time,pub.point_num,pub.comment_num")
                     ->where(['m.status'=>1,'pub.is_del'=>2,'pub.status'=>1,'pub.uid'=>$uid])
                     ->limit($offset,$length)
                     ->order('pub.rank desc,pub.id desc')
                     ->select()
                     ->toArray();
                if(empty($list)){
                    $this->code = 201;
                    $this->msg = '数据不存在';
                    return $this->result('',$this->code,$this->msg);
                }
                //处理图片,偏好标签,个性标签展示
                
                
                //图片
                $ml = model('MaterialLibrary');
                //添加偏好标签的发现
                // $pd = model('PublishDict');
                //添加个性标签的发现
                $my = model('TagsPublish');
                
                foreach ($list as &$v) {
                    $v['img_url'] = [];
                    
                    //是否添加了图片
                    if ($v['img_id']) {
                       $url = $ml->field('url')->where('ml_id in ('.$v['img_id'].')')->select()->toArray();
                       if (!empty($url)) {
                           
                            $v['img_url'] = $url;
                       }
                    }

                    //个性标签名
                    $my_tag = [];
                    //是否添加个性标签
                    $tag = $my->field('tag_name')
                              ->where(['pub_id'=>$v['id']])
                              ->select()
                              ->toArray();
                    if (!empty($tag)) {
                        $my_tag =  array_column($tag,'tag_name');
                    }
                    // $all_tag = array_merge($dict_tag,$my_tag);
                    $v['all_tag'] = implode(',', $my_tag);
                    $v['create_time'] = friend_date(strtotime($v['create_time']));
                }

                return $this->result($list);        
        }

        //删除一条发现
        public function delMyPublishApi($pub_id = 0,$uid = 0)
        {   
            
            if (empty($uid)) {
              $this->code = 208;
              $this->msg = '缺少用户id参数';
              return $this->result('',$this->code,$this->msg);
            }
            
            if (!$pub_id) {
                $this->code = 226;
                $this->msg = '缺少发现id参数';
                return $this->result('',$this->code,$this->msg);
            }

            $this->startTrans();
            try {
                 //删除一条发现
                 $this->where(['id'=>$pub_id])->update(['is_del'=>1]);
                 //更新member表数量
                  //每删除一条发现,member对应的需求数量-1
                  model('Member')->where(['uid'=>$uid])->setDec('require_num');
                  //同时更新session
                  $info = model('Member')->where(['uid'=>$uid])->find()->toArray();
                  session('FINANCE_USER',$info);
                 //查看该发现下是否有未删除的个性标签,如果有,将未删除的删除
                 $tp = model('TagsPublish');
                 $re = $tp->where(['pub_id'=>$pub_id,'is_del'=>2])->select();
                 if (!empty($re)) {
                      $tp->where(['pub_id'=>$pub_id,'is_del'=>2])->update(['is_del'=>1]);
                 }

                $this->commit();
                $this->code = 200;
                $this->msg = '删除成功';
                return $this->result('',$this->code,$this->msg);
            } catch (Exception $e) {

                $this->rollback();
                $this->code = 259;
                $this->msg = '删除失败';
                return $this->result('',$this->code,$this->msg);
            }
        }
    //首页-搜索-发现
    public function getIndexSearchPublish($keyword = '',$page = 1,$uid = 0,$length = 0,$offset = 0)
    {   

        $match_publish = '';//发现匹配的所有条件

        //5.发现
        $pb = $this->alias('pb')
                   ->join('Member m','pb.uid=m.uid','left')
                   ->join('Through t','pb.uid=t.uid','left')
                   ->field([
                    'pb.id',
                    'm.uid',
                    'm.userPhoto',
                    'm.realName',
                    'm.company_jc',
                    'm.position',
                    'pb.content',
                    'pb.img_id',
                    'pb.create_time',
                    'pb.point_num',
                    'pb.comment_num',
                    'pb.type',
                ])
                   ->order('pb.rank desc,pb.id desc')
                   ->where(['pb.is_del'=>2,'pb.status'=>1]);
 
                //组装发现用户名
                $match_publish .= 'm.realName like "%'.$keyword.'%" or ';
                //根据模糊关键词匹配tags_publish表中的个性标签,返回对应的发布id
                $tp = model('TagsPublish');
                $pub_id = $tp->field('pub_id')
                             ->where('tag_name like "%'.$keyword.'%"')
                             ->select()
                             ->toArray();
                if(!empty($pub_id)){
                    //组装根据发现个性标签条件匹配到的发现id
                    $match_publish .= 'pb.id in ('.implode(',',array_column($pub_id,'pub_id')).') or ';

                }else{
                    $match_publish .= 'pb.id in ("") or ';
                }

                //执行查询发现
                $publish_list = $pb->where(rtrim($match_publish,' or '))
                                   ->limit($offset,$length)
                                   ->select()
                                   ->toArray();
                if (empty($publish_list)) {
                    $this->code = 214;
                    $this->msg = '抱歉没有发现的相关内容';
                    return $this->result('',$this->code,$this->msg);
                }
                

                   //处理是否关注,图片,点赞,评论,个性标签展示
                        //当前用户是否关注查询的发现
                        //查询当前用户关注的合伙人(未取关)
                        $to_uid_arr = [];
                        $to_uid = model('FollowUser')->field('to_uid')
                                                 ->where(['from_uid'=>$uid,'is_del'=>2])
                                                 ->select()
                                                 ->toArray();
                      
                        if (!empty($to_uid)) {
                            $to_uid_arr= array_column($to_uid, 'to_uid');
                        }

                        //图片
                        $ml = model('MaterialLibrary');
                        //添加个性标签的发现
                        $my = model('TagsPublish');
                        
                        foreach ($publish_list as &$v) {

                            //是否关注
                            if (in_array($v['uid'], $to_uid_arr)) {
                                $v['is_follow'] = '已关注';
                            }else{
                                $v['is_follow'] = '关注';
                            }
                            $v['img_url'] = [];
                            //是否添加了图片
                            if ($v['img_id']) {
                               $url = $ml->field('url')->where('ml_id in ('.$v['img_id'].')')->select()->toArray();
                               if (!empty($url)) {
                                    $img_url = array_column($url, 'url');
                                    foreach ($img_url as &$val) {
                                        $val = config('view_replace_str')['__DOMAIN__'].$val;
                                    }
                                    $v['img_url'] = $img_url;

                               }
                            }

                            //个性标签名
                            $my_tag = [];

         
                            //是否添加个性标签
                            $tag = $my->field('tag_name')
                                      ->where(['pub_id'=>$v['id']])
                                      ->select()
                                      ->toArray();
                            if (!empty($tag)) {
                                $my_tag =  array_column($tag,'tag_name');
                            }
                            
                            $v['all_tag'] = $my_tag;
                            $v['create_time'] = friend_date(strtotime($v['create_time']));
                            //当前用户点赞过哪些一键发布
                            $point = model('Point')->field('pub_id')
                                                   ->where(['uid'=>$uid])
                                                   ->select()
                                                   ->toArray();
                            if (!empty($point)) {
                                 $point_arr = array_column($point, 'pub_id');
                                if (in_array($v['id'],$point_arr)) {
                                    $v['is_point'] = 1;
                                }else{
                                    $v['is_point'] = 0;
                                }
                            }
                        } 

                return $this->result($publish_list);
    }
    
    //获取用户发布的发现需求
    public function sumPubNum($uid){
        $count = $this->where(['uid'=>$uid,'is_del' => 2,'status'  => 1,])->count('id');
        return $count;
    }
    
}
