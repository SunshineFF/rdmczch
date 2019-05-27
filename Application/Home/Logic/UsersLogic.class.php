<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace Home\Logic;

use Think\Exception;
use Think\Model\RelationModel;
use Think\Page;

/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class UsersLogic extends RelationModel
{
    /*
     * 登陆
     */
    public function login($username,$password){
    	$result = array();
        if(!$username || !$password)
           $result= array('status'=>0,'msg'=>'请填写账号或密码');
        $user = M('users')->where("mobile='{$username}' OR email='{$username}'")->find();
        if(!$user){
           $result = array('status'=>-1,'msg'=>'账号不存在!');
        }elseif(encrypt($password) != $user['password']){
           $result = array('status'=>-2,'msg'=>'密码错误!');
        }elseif($user['is_lock'] == 1){
           $result = array('status'=>-3,'msg'=>'账号异常已被锁定！！！');
        }else{
           $result = array('status'=>1,'msg'=>'登陆成功','result'=>$user);
        }
        return $result;
    }
    
    /*
     * app端登陆
     */
    public function app_login($username,$password){

    	$result = array();
        if(!$username || !$password)
           $result= array('status'=>0,'msg'=>'请填写账号或密码');
        $user = M('users')->where("mobile='{$username}' OR email='{$username}'")->find();
        if(!$user){
           $result = array('status'=>-1,'msg'=>'账号不存在!');
        }elseif($password != $user['password']){
           $result = array('status'=>-2,'msg'=>'密码错误!');
        }elseif($user['is_lock'] == 1){
           $result = array('status'=>-3,'msg'=>'账号异常已被锁定！！！');
        }else{
            //查询用户信息之后, 查询用户的登记昵称
            $levelId = $user['level'];
            $levelName = M("user_level")->where("level_id = {$levelId}")->getField("level_name"); 
            $user['level_name'] = $levelName;            
            $user['token'] = md5(time().mt_rand(1,999999999));
            M('users')->where("user_id = {$user['user_id']}")->save(array('token'=>$user['token'],'last_login'=>time()));
            $result = array('status'=>1,'msg'=>'登陆成功','result'=>$user);
        }
        return $result;
    }       
    
    //绑定账号
    public function oauth_bind($data = array()){
    	$user = session('user');
    	if(empty($user['openid'])){
    		if(M('users')->where(array('openid'=>$data['openid']))->count()>0){
    			return array('status'=>-1,'msg'=>'您的'.$data['oauth'].'账号已经绑定过账号');
    		}else{
    			M('users')->where(array('user_id'=>$user['user_id']))->save($data);
    			return array('status'=>1,'msg'=>'绑定成功','result'=>$data);
    		}
    	}else{
    		return array('status'=>-1,'msg'=>'您的账号已绑定过，请不要重复绑定');
    	}
    }
    
    /*
     * 第三方登录
     */
    public function thirdLogin($data=array()){
        $openid = $data['openid']; //第三方返回唯一标识
        $oauth = $data['oauth']; //来源
        if(!$openid || !$oauth)
            return array('status'=>-1,'msg'=>'参数有误','result'=>'');
        //获取用户信息
        if(isset($data['unionid'])){
        	$map['unionid'] = $data['unionid'];
        	$user = get_user_info($data['unionid'],4,$oauth);
        }else{
        	$user = get_user_info($openid,3,$oauth);
        }
        if(!$user){
            //账户不存在 注册一个
            $map['password'] = '';
            $map['openid'] = $openid;
            $map['nickname'] = $data['nickname'];
            $map['reg_time'] = time();
            $map['oauth'] = $oauth;
            $map['head_pic'] = $data['head_pic'];
            $map['sex'] = $data['sex'] === null ? 0 :  $data['sex'];
            $map['token'] = md5(time().mt_rand(1,99999));
            $map['first_leader'] = cookie('first_leader'); // 推荐人id
            if($_GET['first_leader'])
                $map['first_leader'] = $_GET['first_leader']; // 微信授权登录返回时 get 带着参数的
            
            // 如果找到他老爸还要找他爷爷他祖父等
            if($map['first_leader'])
            {
                $first_leader = M('users')->where("user_id = {$map['first_leader']}")->find();
                $map['second_leader'] = $first_leader['first_leader']; //  第一级推荐人
                $map['third_leader'] = $first_leader['second_leader']; // 第二级推荐人
            }else
			{
				$map['first_leader'] = 0;
			}
            // 成为分销商条件  
            //$distribut_condition = tpCache('distribut.condition'); 
            //if($distribut_condition == 0)  // 直接成为分销商, 每个人都可以做分销        
            $map['is_distribut']  = 1;        
            
            $row_id = M('users')->add($map);
			// 会员注册送优惠券
			$coupon = M('coupon')->where("send_end_time > ".time()." and ((createnum - send_num) > 0 or createnum = 0) and type = 2")->select();
			foreach ($coupon as $key => $val)
			{
				// 送券            
				M('coupon_list')->add(array('cid'=>$val['id'],'type'=>$val['type'],'uid'=>$row_id,'send_time'=>time()));
				M('Coupon')->where("id = {$val['id']}")->setInc('send_num'); // 优惠券领取数量加一            
			}
						
        }else
        {
            $user['token'] = md5(time().mt_rand(1,999999999));
            M('users')->where("user_id = '{$user['user_id']}'")->save(array('token'=>$user['token'],'last_login'=>time()));
        }
		 
        return array('status'=>1,'msg'=>'登陆成功','result'=>$user);
    }

    /**
     * 注册
     * @param $username  邮箱或手机
     * @param $password  密码
     * @param $password2 确认密码
     * @return array
     */
    public function reg($username,$password,$password2){
    	$is_validated = 0 ;
        if(check_email($username)){
            $is_validated = 1;
            $map['email_validated'] = 1;
            $map['nickname'] = $map['email'] = $username; //邮箱注册
        }

        if(check_mobile($username)){
            $is_validated = 1;
            $map['mobile_validated'] = 1;
            $map['nickname'] = $map['mobile'] = $username; //手机注册
        }

        if($is_validated != 1)
            return array('status'=>-1,'msg'=>'请用手机号或邮箱注册','result'=>'');

        if(!$username || !$password)
            return array('status'=>-1,'msg'=>'请输入用户名或密码','result'=>'');

        //验证两次密码是否匹配
        if($password2 != $password)
            return array('status'=>-1,'msg'=>'两次输入密码不一致','result'=>'');
        //验证是否存在用户名
        if(get_user_info($username,1)||get_user_info($username,2))
            return array('status'=>-1,'msg'=>'账号已存在','result'=>'');

        $map['password'] = encrypt($password);
        $map['reg_time'] = time();
        $map['first_leader'] = cookie('first_leader'); // 推荐人id
        // 如果找到他老爸还要找他爷爷他祖父等
        if($map['first_leader'])
        {
            $first_leader = M('users')->where("user_id = {$map['first_leader']}")->find();
            $map['second_leader'] = $first_leader['first_leader'];
            $map['third_leader'] = $first_leader['second_leader'];
        }else
		{
			$map['first_leader'] = 0;
		}              
        
        // 成为分销商条件  
        //$distribut_condition = tpCache('distribut.condition'); 
        //if($distribut_condition == 0)  // 直接成为分销商, 每个人都可以做分销        
        $map['is_distribut']  = 1; // 默认每个人都可以成为分销商

        try{
            $this->startTrans();
            $map['password_2'] = md5(I('password_2'));
            $map = $this->_initUserData($map);
            $user_id = M('users')->add($map);
            $this->commit();
            if(!$user_id)
                return array('status'=>-1,'msg'=>'注册失败','result'=>'');
        }catch (\Exception $exception){
            $this->rollback();
            return $this->error($exception->getMessage());
        }

        $pay_points = tpCache('basic.reg_integral'); // 会员注册赠送积分
        if($pay_points > 0)
            accountLog($user_id, 0,$pay_points, '会员注册赠送积分'); // 记录日志流水                  
        $user = M('users')->where("user_id = {$user_id}")->find();
        $this->_initParentPath($user);
        // 会员注册送优惠券
        $coupon = M('coupon')->where("send_end_time > ".time()." and ((createnum - send_num) > 0 or createnum = 0) and type = 2")->select();
        if(!empty($coupon)){
        	foreach ($coupon as $key => $val)
        	{
        		M('coupon_list')->add(array('cid'=>$val['id'],'type'=>$val['type'],'uid'=>$user_id,'send_time'=>time()));
        		M('Coupon')->where("id = {$val['id']}")->setInc('send_num'); // 优惠券领取数量加一
        	}
        }       
        return array('status'=>1,'msg'=>'注册成功','result'=>$user);
    }

    /** 初始化用户路径
     * @param $user
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _initParentPath(&$user){
        if (!$user['parent_path']){
            return;
        }
        $parentArray = explode(',',$user['parent_path']);
        $firstParent = current($parentArray);
        $firstParent = $this->where(['user_id' => $firstParent])->find();
        $userAllArray = unserialize($firstParent['all_child']);
        if (count($userAllArray) == 0){
            $userAllArray = [];
            $userAllArray[$user['parent_id']][$user['user_type']] = $user['user_id'];
        }else{
            $userAllArray[$user['parent_id']][$user['user_type']] = $user['user_id'];
        }
        $this->where(['user_id' => $firstParent['user_id']])->save(['all_child'=>serialize($userAllArray)]);
    }

    /**
     * @param $user
     * @throws \Exception
     */
    protected function _initUserData($user){
        if ($_POST['invite_code'] == '18613045257'){
            return $user;
        }
        $this->_initUserParent($user);
        $parent = isset($user['parent']) ? $user['parent'] : false;
        if (!$parent){
            throw new \Exception('参数不正确');
        }
        $this->_initUserParenPath($user);
        $this->canUseParent($user);
        unset($user['parent']);
        return $user;
    }

    /** 更新直推的积分
     * @param $zhituiId
     */
    protected function _updateZhiTuiJifen($zhituiId){
        $this->where(['user_id' => $zhituiId])->setInc('pay_points',100);
    }

    /**初始化用户的父级和直推用户
     * 直推默认为父级
     * @param $user
     * @throws \Exception
     */
    protected function _initUserParent(&$user){
        $zhitui = $this->where(['invite_code' => $_POST['invite_code']])->find();
        if (!$zhitui){
            throw new \Exception('推荐人手机号填写不正确');
        }
        $user['zhitui_id'] = $zhitui['user_id'];
      //  $parent = $this->getUserParent($zhitui);  //双线，默认分配小区
        $parent = $this->getUserParentNew($zhitui);  //单线只开放大区
        $user['parent'] = $parent;
        $parentId = $parent['user_id'];
        $user['parent_id'] = $parentId;
        $this->_updateZhiTuiJifen($user['zhitui_id']);
    }

    /** 根据直推账号获取推荐人
     * @param $user
     * @return array|false|mixed|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getUserParentNew($user){
        if ($user['parent_path']){
            $parentArray = explode(',',$user['parent_path']);
            //拿到关系表
            $parent = $this->where(['user_id' => current($parentArray)])->find();
            $allChild = unserialize($parent['all_child']);
            $lastParent = end($allChild);
            $parentId = current($lastParent);
            $parent = $this->where(['user_id' => $parentId])->find();
        }else{
            $parent = $user;
        }
        return $parent;
    }

    /** 初始化用户的 父级路径，方便以后寻址
     * @param $user
     */
    protected function _initUserParenPath(&$user){
        $parent = $user['parent'];
        if ($parent['parent_path']){
            $parentPath = '';
            $parentPath .= $parent['parent_path'];
            $parentPath .= ',';
            $parentPath .= $parent['user_id'];
        }else{
            $parentPath = $parent['user_id'];
        }
        $user['parent_path'] = $parentPath;
    }

    /** 查看父级有没有位置
     * @param $user
     * @throws \Exception
     */
    protected function canUseParent(&$user){
        $count = M('users')->where(['parent_id' => $user['parent_id']])->count();
//        if ($count == 0){
//            $user['user_type'] = 1;
//            return true;
//        }elseif($count == 1){
       if($count == 0){
            $user['user_type'] = 1;
            return true;
        }
        throw new \Exception('您填写的父级已经没有位置了');
    }

    /*
      * 获取当前登录用户信息
      */
     public function get_info($user_id){
         if(!$user_id > 0)
             return array('status'=>-1,'msg'=>'缺少参数','result'=>'');
        $user_info = M('users')->where("user_id = {$user_id}")->find();
        if(!$user_info)
            return false;
         
         $user_info['coupon_count'] = M('coupon_list')->where("uid = $user_id and use_time = 0")->count(); //获取优惠券列表         
         $user_info['collect_count'] = M('goods_collect')->where(array('user_id'=>$user_id))->count(); //获取收藏数量         
                                    
         $user_info['waitPay']     = M('order')->where("user_id = $user_id ".C('WAITPAY'))->count(); //待付款数量
         $user_info['waitSend']    = M('order')->where("user_id = $user_id ".C('WAITSEND'))->count(); //待发货数量         
         $user_info['waitReceive'] = M('order')->where("user_id = $user_id ".C('WAITRECEIVE'))->count(); //待收货数量                  
         $user_info['order_count'] = $user_info['waitPay'] + $user_info['waitSend'] + $user_info['waitReceive'];
         return array('status'=>1,'msg'=>'获取成功','result'=>$user_info);
     }
     
    /*
     * 获取最近一笔订单
     */
    public function get_last_order($user_id){
        $last_order = M('order')->where("user_id = {$user_id}")->order('order_id DESC')->find();
        return $last_order;
    }


    /*
     * 获取订单商品
     */
    public function get_order_goods($order_id){
        $sql = "SELECT og.*,g.original_img FROM __PREFIX__order_goods og LEFT JOIN __PREFIX__goods g ON g.goods_id = og.goods_id WHERE order_id = ".$order_id;
        $goods_list = $this->query($sql);

        $return['status'] = 1;
        $return['msg'] = '';
        $return['result'] = $goods_list;
        return $return;
    }

    /*
     * 获取账户资金记录
     */
    public function get_account_log($user_id,$type=0){
        //查询条件
//        $type = I('get.type',0);
        if($type == 1){
            //收入
            $where = 'user_money > 0 OR pay_points > 0 AND user_id='.$user_id;
        }
        if($type == 2){
            //支出
            $where = 'user_money < 0 OR pay_points < 0 AND user_id='.$user_id;
        }

        $count = M('account_log')->where($where ? $where : 'user_id = '.$user_id)->count();
        $Page = new Page($count,16);
        $logs = M('account_log')->where($where ? $where : 'user_id = '.$user_id)->order('change_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $return['status'] = 1;
        $return['msg'] = '';
        $return['result'] = $logs;
        $return['show'] = $Page->show();

        return $return;
    }
    /*
     * 获取优惠券
     */
    public function get_coupon($user_id,$type =0 ){
       
        //查询条件
        //    $type = I('get.type',0);           
     

        $where = ' AND l.order_id = 0 AND c.use_end_time > '.time(); // 未使用
        if($type == 1){
            //已使用
            $where = ' AND l.order_id > 0 AND l.use_time > 0 ';
        }
        if($type == 2){
            //已过期
            $where = ' AND '.time().' > c.use_end_time ';
        }        
        //获取数量
        $sql = "SELECT count(l.id) as total_num FROM __PREFIX__coupon_list".
            " l LEFT JOIN __PREFIX__coupon".
            " c ON l.cid =  c.id WHERE l.uid = {$user_id} {$where}";
        $count = $this->query($sql);
        $count = $count[0]['total_num'];

        $Page = new Page($count,10);

        $sql = "SELECT l.*,c.name,c.money,c.use_end_time,c.condition FROM __PREFIX__coupon_list".
            " l LEFT JOIN __PREFIX__coupon".
            " c ON l.cid =  c.id WHERE l.uid = {$user_id} {$where}  ORDER BY l.send_time DESC,l.use_time LIMIT {$Page->firstRow},{$Page->listRows}";

        $logs = $this->query($sql);

        $return['status'] = 1;
        $return['msg'] = '获取成功';
        $return['result'] = $logs;
        $return['show'] = $Page->show();
        return $return;
    }

    /**
     * 获取商品收藏列表
     * @param $user_id  用户id
     */
    public function get_goods_collect($user_id){
        $count = M('goods_collect')->where(array('user_id'=>$user_id))->count();
        $page = new Page($count,10);
        $show = $page->show();
        //获取我的收藏列表
        $sql = "SELECT c.collect_id,c.add_time,g.goods_id,g.goods_name,g.shop_price,g.original_img FROM __PREFIX__goods_collect c ".
            "inner JOIN __PREFIX__goods g ON g.goods_id = c.goods_id ".
            "WHERE c.user_id = ".$user_id.
            " ORDER BY c.add_time DESC LIMIT {$page->firstRow},{$page->listRows}";
        $result = M()->query($sql);
        $return['status'] = 3;
        $return['msg'] = '获取成功';
        $return['result'] = $result;
        $return['show'] = $show;        
        return $return;
    }

    /**
     * 获取评论列表
     * @param $user_id 用户id
     * @param $status  状态 0 未评论 1 已评论
     * @return mixed
     */
    public function get_comment($user_id,$status=2){
        if($status == 1){
            //已评论
            $count2 = M('')->query("select count(*) as count from `__PREFIX__comment`  as c inner join __PREFIX__order_goods as g on c.goods_id = g.goods_id and c.order_id = g.order_id where c.user_id = $user_id");
            $count2 = $count2[0]['count'];
            
            $page = new Page($count2,10);
            $sql = "select c.*,g.*,(select order_sn from  __PREFIX__order where order_id = c.order_id ) as order_sn  from  __PREFIX__comment as c inner join __PREFIX__order_goods as g on c.goods_id = g.goods_id and c.order_id = g.order_id where c.user_id = $user_id order by c.add_time desc LIMIT {$page->firstRow},{$page->listRows}";
        }else{
        	$countsql = " select count(1) as comment_count from __PREFIX__order_goods as og
        	left join __PREFIX__order as o on o.order_id = og.order_id where o.user_id = $user_id  and og.is_send = 1 ";
        	$where = '';
            $countsql .= $where = " and og.is_comment = 0 ";
//        	if($status == 0){
//        		$countsql .= $where = " and og.is_comment = 0 ";
//        	}
        	$comment = M()->query($countsql);
        	$count1 = $comment[0][comment_count]; // 待评价
            $page = new Page($count1,3);
            $sql =" select o.add_time,o.order_sn,og.order_id,og.goods_id,og.goods_name,o.store_id from __PREFIX__order_goods as og
            left join __PREFIX__order as o on o.order_id = og.order_id  where o.user_id = $user_id and og.is_send = 1
            $where order by o.order_id desc  LIMIT {$page->firstRow},{$page->listRows}";
        }
        $show = $page->show();
        $comment_list = M()->query($sql);
        if($comment_list){
        	$return['result'] = $comment_list;
        	$return['show'] = $show; //分页
        	return $return;
        }else{
        	return array();
        }
    }
    
    /**
     * 添加评论
     * @param $order_id  订单id
     * @param $goods_id  商品id
     * @param $user_email用户邮箱地址
     * @param $username  用户名
     * @return bool
     */
    public function add_comment($add){
        if(!$add[order_id] || !$add[goods_id])
            return array('status'=>-1,'msg'=>'非法操作','result'=>'');
        
        //检查订单是否已完成
        $order = M('order')->where("order_id = {$add[order_id]} AND user_id = {$add[user_id]}")->find();
        if($order['order_status'] != 2)
            return array('status'=>-1,'msg'=>'该笔订单还未完成','result'=>'');

        //检查是否已评论过
        $goods = M('comment')->where("order_id = {$add[order_id]} AND goods_id = {$add[goods_id]}")->find();
        if($goods)            
            return array('status'=>-1,'msg'=>'您已经评论过该商品','result'=>'');        
        
        $row = M('comment')->add($add);
        if($row)
        {
            //更新订单商品表状态
            M('order_goods')->where(array('goods_id'=>$add[goods_id],'order_id'=>$add[order_id]))->save(array('is_comment'=>1));
            M('goods')->where(array('goods_id'=>$add[goods_id]))->setInc('comment_count',1); // 评论数加一  
            // 查看这个订单是否全部已经评论,如果全部评论了 修改整个订单评论状态            
            $comment_count   = M('order_goods')->where("order_id ='{$add[order_id]}' and is_comment = 0")->count();
            if($comment_count == 0) // 如果所有的商品都已经评价了 订单状态改成已评价
            {
                M('order')->where("order_id ='{$add[order_id]}'")->save(array('order_status'=>4));
            }
            return array('status'=>1,'msg'=>'评论成功','result'=>'');
        }
        return array('status'=>-1,'msg'=>'评论失败','result'=>'');
    }

    /**
     * 邮箱或手机绑定
     * @param $email_mobile  邮箱或者手机
     * @param int $type  1 为更新邮箱模式  2 手机
     * @param int $user_id  用户id
     * @return bool
     */
    public function update_email_mobile($email_mobile,$user_id,$type=1){
        //检查是否存在邮件
        if($type == 1)
            $field = 'email';
        if($type == 2)
            $field = 'mobile';
        $condition['user_id'] = array('neq',$user_id);
        $condition[$field] = $email_mobile;

        $is_exist = M('users')->where($condition)->find();
        if($is_exist)
            return false;
        unset($condition[$field]);
        $condition['user_id'] = $user_id;
        $validate = $field.'_validated';
        M('users')->where($condition)->save(array($field=>$email_mobile,$validate=>1));
        return true;
    }

    /**
     * 更新用户信息
     * @param $user_id
     * @param $post  要更新的信息
     * @return bool
     */
    public function update_info($user_id,$post=array()){
        $model = M('users')->where("user_id = ".$user_id);
        $row = $model->save($post);
        if($row === false)
           return false;
        return true;
    }

    /**
     * 地址添加/编辑
     * @param $user_id 用户id
     * @param $user_id 地址id(编辑时需传入)
     * @return array
     */
    public function add_address($user_id,$address_id=0,$data){
        $post = $data;
        if($address_id == 0)
        {
            $c = M('UserAddress')->where("user_id = $user_id")->count();
            if($c >= 20)
                return array('status'=>-1,'msg'=>'最多只能添加20个收货地址','result'=>'');
        }        
        
        //检查手机格式
        if($post['consignee'] == '')
            return array('status'=>-1,'msg'=>'收货人不能为空','result'=>'');
        if(!$post['province'] || !$post['city'] || !$post['district'])
            return array('status'=>-1,'msg'=>'所在地区不能为空','result'=>'');
        if(!$post['address'])
            return array('status'=>-1,'msg'=>'地址不能为空','result'=>'');
        if(!check_mobile($post['mobile']))
            return array('status'=>-1,'msg'=>'手机号码格式有误','result'=>'');

        //编辑模式
        if($address_id > 0){

            $address = M('user_address')->where(array('address_id'=>$address_id,'user_id'=> $user_id))->find();
            if($post['is_default'] == 1 && $address['is_default'] != 1)
                M('user_address')->where(array('user_id'=>$user_id))->save(array('is_default'=>0));
            $row = M('user_address')->where(array('address_id'=>$address_id,'user_id'=> $user_id))->save($post);
            if(!$row)
                return array('status'=>-1,'msg'=>'操作完成','result'=>'');
            return array('status'=>1,'msg'=>'编辑成功','result'=>'');
        }
        //添加模式
        $post['user_id'] = $user_id;
        
        // 如果目前只有一个收货地址则改为默认收货地址
        $c = M('user_address')->where("user_id = {$post['user_id']}")->count();        
        if($c == 0)  $post['is_default'] = 1;
        
        $address_id = M('user_address')->add($post);
        //如果设为默认地址
        $insert_id = M()->getLastInsID();
        $map['user_id'] = $user_id;
        $map['address_id'] = array('neq',$insert_id);
               
        if($post['is_default'] == 1)
            M('user_address')->where($map)->save(array('is_default'=>0));
        if(!$address_id)
            return array('status'=>-1,'msg'=>'添加失败','result'=>'');
        
        
        return array('status'=>1,'msg'=>'添加成功','result'=>$address_id);
    }

    /**
     * 设置默认收货地址
     * @param $user_id
     * @param $address_id
     */
    public function set_default($user_id,$address_id){
        M('user_address')->where(array('user_id'=>$user_id))->save(array('is_default'=>0)); //改变以前的默认地址地址状态
        $row = M('user_address')->where(array('user_id'=>$user_id,'address_id'=>$address_id))->save(array('is_default'=>1));
        if(!$row)
            return false;
        return true;
    }

    /**
     * 修改密码
     * @param $user_id  用户id
     * @param $old_password  旧密码
     * @param $new_password  新密码
     * @param $confirm_password 确认新 密码
     */
    public function password($user_id,$old_password,$new_password,$confirm_password,$is_update=true){
        $data = $this->get_info($user_id);
        $user = $data['result'];
        if(strlen($new_password) < 6)
            return array('status'=>-1,'msg'=>'密码不能低于6位字符','result'=>'');
        if($new_password != $confirm_password)
            return array('status'=>-1,'msg'=>'两次密码输入不一致','result'=>'');
        //验证原密码
        if($is_update && ($user['password'] != '' && encrypt($old_password) != $user['password']))
            return array('status'=>-1,'msg'=>'密码验证失败','result'=>'');
        $row = M('users')->where("user_id='{$user_id}'")->save(array('password'=>encrypt($new_password)));
        if(!$row)
            return array('status'=>-1,'msg'=>'修改失败','result'=>'');
        return array('status'=>1,'msg'=>'修改成功','result'=>'');
    }


    /**
     * 修改密码
     * @param $user_id  用户id
     * @param $old_password  旧密码
     * @param $new_password  新密码
     * @param $confirm_password 确认新 密码
     */
    public function password2($user_id,$old_password,$new_password,$confirm_password,$is_update=true){
        $data = $this->get_info($user_id);
        $user = $data['result'];
        if($new_password != $confirm_password)
            return array('status'=>-1,'msg'=>'两次二级密码输入不一致','result'=>'');
        //验证原密码
        if($is_update && ($user['password'] != '' && md5($old_password) != $user['password']))
            return array('status'=>-1,'msg'=>'密码验证失败','result'=>'');
        $row = M('users')->where("user_id='{$user_id}'")->save(array('password'=>md5($new_password)));
        if(!$row)
            return array('status'=>-1,'msg'=>'修改失败','result'=>'');
        return array('status'=>1,'msg'=>'修改成功','result'=>'');
    }


    /**
     * 取消订单
     */
    public function cancel_order($user_id,$order_id){
        $order = M('order')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->find();
        //检查是否未支付订单 已支付联系客服处理退款
        if(empty($order))
            return array('status'=>-1,'msg'=>'订单不存在','result'=>'');
        //检查是否未支付的订单
        if($order['pay_status'] > 0 || $order['order_status'] > 0)
            return array('status'=>-1,'msg'=>'支付状态或订单状态不允许','result'=>'');
        //获取记录表信息
        //$log = M('account_log')->where(array('order_id'=>$order_id))->find();
        //有余额支付的情况
        if($order['user_money'] > 0 || $order['integral'] > 0){
            accountLog($user_id,$order['user_money'],$order['integral'],"订单取消，退回{$order['user_money']}元,{$order['integral']}积分");
        }

        $row = M('order')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->save(array('order_status'=>3));
				
        $data['order_id'] = $order_id;
        $data['action_user'] = $user_id;
        $data['action_note'] = '您取消了订单';
        $data['order_status'] = 3;
        $data['pay_status'] = $order['pay_status'];
        $data['shipping_status'] = $order['shipping_status'];
        $data['log_time'] = time();
        $data['status_desc'] = '用户取消订单';        
        M('order_action')->add($data);//订单操作记录		
		
        if(!$row)
            return array('status'=>-1,'msg'=>'操作失败','result'=>'');
        return array('status'=>1,'msg'=>'操作成功','result'=>'');

    }

    /**
     * app发送短信验证码记录
     * @param $mobile
     * @param $code
     * @param $session_id
     */
    public function sms_log($mobile,$code,$session_id){
        //判断是否存在验证码
        $data = M('sms_log')->where(array('mobile'=>$mobile,'session_id'=>$session_id))->order('id DESC')->find();
        //获取时间配置
        $sms_time_out = tpCache('sms.sms_time_out');
        $sms_time_out = $sms_time_out ? $sms_time_out : 120;
        //120秒以内不可重复发送
        if($data && (time() - $data['add_time']) < $sms_time_out)
            return array('status'=>-1,'msg'=>$sms_time_out.'秒内不允许重复发送');
        $row = M('sms_log')->add(array('mobile'=>$mobile,'code'=>$code,'add_time'=>time(),'session_id'=>$session_id));
        if(!$row)
            return array('status'=>-1,'msg'=>'发送失败');
        //$send = sendSMS($mobile,'您好，你的验证码是：'.$code);
        $send = sendSMS($mobile,$code);
        return array('status'=>1,'msg'=>'发送成功');
        if(!$send)
            return array('status'=>-1,'msg'=>'发送失败');
        return array('status'=>1,'msg'=>'发送成功');
    }

    /**
     * 短信验证码验证
     * @param $mobile   手机
     * @param $code  验证码
     * @param $session_id   唯一标示
     * @return bool
     */
    public function sms_code_verify($mobile,$code,$session_id){
        //判断是否存在验证码
        $data = M('sms_log')->where(array('mobile'=>$mobile,'session_id'=>$session_id,'code'=>$code))->order('id DESC')->find();
        if(empty($data))
            return array('status'=>-1,'msg'=>'手机验证码不匹配');

        //获取时间配置
        $sms_time_out = tpCache('sms.sms_time_out');
        $sms_time_out = $sms_time_out ? $sms_time_out : 120;
        //验证是否过时
        if((time() - $data['add_time']) > $sms_time_out)
            return array('status'=>-1,'msg'=>'手机验证码超时'); //超时处理
        M('sms_log')->where(array('mobile'=>$mobile,'session_id'=>$session_id,'code'=>$code))->delete();
        return array('status'=>1,'msg'=>'验证成功');
    }
    
    /**
     * 发送验证码
     * @param $sender 接收人
     * @param $type 发送类型
     * @return json
     */
    public function send_validate_code($sender,$type){
    	$sms_time_out = tpCache('sms.sms_time_out');
    	$sms_time_out = $sms_time_out ? $sms_time_out : 180;
    	//获取上一次的发送时间
    	$send = session('validate_code');
    	if(!empty($send) && $send['time'] > time() && $send['sender'] == $sender){
    		//在有效期范围内 相同号码不再发送
    		$res = array('status'=>-1,'msg'=>'规定时间内,不要重复发送验证码');
    	}
    	$code =  mt_rand(1000,9999);
    	if($type == 'email'){
    		//检查是否邮箱格式
    		if(!check_email($sender))
    			$res = array('status'=>-1,'msg'=>'邮箱码格式有误');
    		$send = send_email($sender,'验证码','您好，你的验证码是：'.$code);
    	}else{
    		if(!check_mobile($sender))
    			$res = array('status'=>-1,'msg'=>'手机号码格式有误');
    		//$send = sendSMS($sender,'您好，你的验证码是：'.$code);
                $send = sendSMS($sender,$code);
    	}
    	if($send){
    		$info['code'] = $code;
    		$info['sender'] = $sender;
    		$check['is_check'] = 0;
    		$info['time'] = time() + $sms_time_out; //有效验证时间
    		session('validate_code',$info);
    		$res = array('status'=>1,'msg'=>'验证码已发送，请注意查收');
    	}else{
    		$res = array('status'=>-1,'msg'=>'验证码发送失败,请联系管理员');
    	}
    	return $res;
    }
    
    public function check_validate_code($code,$sender){   	
    	$check = session('validate_code');
    	if(empty($check))
    	{
    		$res = array('status'=>0,'msg'=>'请先获取验证码');
    	}elseif($check['time']<time())
    	{
    		$res = array('status'=>0,'msg'=>'验证码已超时失效');
    	}elseif($code!=$check['code'] || $check['sender']!=$sender)
    	{
    		$res = array('status'=>0,'msg'=>'验证失败,验证码有误');
    	}else
    	{
    		$check['is_check'] = 1; //标示验证通过
    		session('validate_code',$check);
    		$res = array('status'=>1,'msg'=>'验证成功');
    	}
        return $res;
    }
    /**
     * @time 2016/09/01
     * @author dyr
     * 设置用户系统消息已读
     */
    public function setSysMessageForRead()
    {
        $user_info = session('user');
        if (!empty($user_info['user_id'])) {
            $data['status'] = 1;
            M('user_message')->where(array('user_id' => $user_info['user_id'], 'category' => 0))->save($data);
        }
    }

    protected function error($msg = '出了一些无法预料问题。请联系相关人员')
    {
        return array('status'=>-1,'msg'=>$msg,'result'=>'');
    }

    protected function success($msg = '出了一些无法预料问题。请联系相关人员')
    {
        return array('status'=>-1,'msg'=>$msg,'result'=>'');
    }


    /** 用户投资逻辑
     * @param $user
     * @return array
     */
    public function touZi($user){
        $money = $_POST['money'];
        if ((int)$money < 500){
            return array('status'=>-1,'msg'=>'投资金额必须大于500');
        }
        if ($money > $user['user_money']){
            return array('status'=>-1,'msg'=>'请填写正确的额度');
        }
        $user['tou_zi'] = $user['tou_zi'] + $money;
        $user['user_money'] = $user['user_money'] - $money;
//        $this->where(['user_id'=>$user['user_id']])->setInc('tou_zi',$money);
//        $this->where(['user_id'=>$user['user_id']])->setDec('user_money',$money);
        $this->where(['user_id' => $user['user_id']])->save($user);
        $this->updateZhiTui($user,$money);
        accountLogOnly($user['user_id'],$money,'用户投资');
        return array('status'=>1,'msg'=>'投资成功');
    }

    /** 更新直推用户奖励
     * @param $user
     * @param $money
     * @return bool
     */
    public function updateZhiTui($user,$money){
        if ($user['zhitui_id']){
            $type = $this->getTypeByMoney($user);
            if ($type === false){
                return false;
            }
            $zhituiM = 0;
            switch ($user['user_type']){
                case 1:
                    if ($type == 1){
                        $zhituiM = $money*0.2;
                    }elseif($type == 2){
                        $rule = $this->getRule($type,$user['zhitui_id']);
                        if ($rule){
                            $zhituiM = $money * $rule['zhitui'];
                        }
                    }
                    break;
                case 2:
                    if ($type == 1){
                        $rule = $this->getRule($type,$user['zhitui_id']);
                        if ($rule){
                            $zhituiM = $money * $rule['zhitui'];
                        }
                    }elseif($type == 2){
                        $zhituiM = $money*0.2;
                    }
                    break;
                default:
                    break;
            }
            $this->where(['user_id' => $user['zhitui_id']])->setInc('user_money',$zhituiM);
            accountLogOnly($user['zhitui_id'],$zhituiM,'直推奖励');
        }
        $this->updateAllTouZi($user);
    }

    /** 更新这个分支的投资信息
     * @param $user
     * @param $money
     */
    protected function updateAllTouZi($user){
        $firstParentId = current(explode(',',$user['parent_path']));
        if ($firstParentId){
            $firstParent = $this->where(['user_id' => $firstParentId])->find();
            $touziString = $firstParent['all_touzi'];
            if ($touziString){
                $touziArray = unserialize($touziString);
            }else{
                $touziArray = [];
            }
            $user = $this->where(['user_id' => $user['user_id']])->find();
            $one = [$user['user_id'] => $user['tou_zi']];
            $touziArray[$user['parent_id']] = [
                $user['user_type'] => $one
            ];
            $touziString = serialize($touziArray);
            $this->where(['user_id' => $firstParentId])->save(['all_touzi'=> $touziString]);
        }
    }

    /** 根据用户投资金额，获取奖励规则
     * @param $type
     * @param $userId
     * @return array|bool
     */
    public function getRule($type,$userId){
        $touzi = $this->where(['user_id' => $userId])->field('tou_zi')->find();
        $touzi = (int)$touzi['tou_zi'];
        $rule = [
            'zhitui' => 0.06,
            'max_get' => 2,
            'all_day' => 2000,
            'max_day_get' => 50
        ];
        if ($type == 1){
            $rule['zhitui'] = 0.2;
            $rule['max_get'] = 0;
            $rule['all_day'] = 0;
            $rule['max_day_get'] = 0;
        }else{
          if($touzi >= 500 && $touzi < 5000){
              return $rule;
            }elseif($touzi >= 5000 && $touzi < 10000){
              $rule['zhitui'] = 0.1;
              $rule['max_get'] = 3;
              $rule['all_day'] = 1800;
              $rule['max_day_get'] = 500;
            }elseif($touzi >= 10000 && $touzi < 50000){
              $rule['zhitui'] = 0.15;
              $rule['max_get'] = 5;
              $rule['all_day'] = 1500;
              $rule['max_day_get'] = 1000;
            }elseif($touzi >= 50000 && $touzi < 100000){
              $rule['zhitui'] = 0.2;
              $rule['max_get'] = 8;
              $rule['all_day'] = 1200;
              $rule['max_day_get'] = 5000;
            }elseif($touzi >= 100000 && $touzi < 500000){
                $rule['zhitui'] = 0.2;
                $rule['max_get'] = 10;
                $rule['all_day'] = 1000;
                $rule['max_day_get'] = 10000;
            }elseif($touzi >= 100000 && $touzi < 500000){
                $rule['zhitui'] = 0.3;
                $rule['max_get'] = 15;
                $rule['all_day'] = 800;
                $rule['max_day_get'] = 50000;
            }elseif($touzi >= 1000000){
              $rule['zhitui'] = 0.3;
              $rule['max_get'] = 20;
              $rule['all_day'] = 500;
              $rule['max_day_get'] = 100000;
          }else{
              return false;
          }
        }
        return $rule;
    }

    /** 根据关系树，得到A分支或者B分支为大区或小区
     * @param $user
     * @return bool|int
     */
    protected function getTypeByUser($user){
        if (!$user['parent_id']){
            return false;
        }
        $parentId = $user['parent_id'];
        $parentArray = explode(',',$user['parent_path']);
        //拿到关系表
        $parent = $this->where(['user_id' => current($parentArray)])->find();
        $allChild = unserialize($parent['all_child']);
        $A = $B = 0;
        $current = '';
        $AChild = $BChild = [];
        for ($i = 0; $i < count($allChild);$i++){
            if (isset($allChild[$parentId])){
                $children = $allChild[$parentId];
                if ($i > 0){
                    if ($current == 1){
                        if (isset($children[1])){
                            $AChild[] = $children[1];
                            $A++;
                        }
                        if (isset($children[2])){
                            $AChild[] = $children[2];
                            $A++;
                        }
                    }else{
                        if (isset($children[1])){
                            $BChild[] = $children[1];
                            $B++;
                        }
                        if (isset($children[2])){
                            $BChild[] = $children[2];
                            $B++;
                        }
                    }
                }else{
                    if (isset($children[1])){
                        $AChild[] = $children[1];
                        $A++;
                    }
                    if (isset($children[2])){
                        $BChild[] = $children[2];
                        $B++;
                    }
                }
            }
            if (count($AChild) == 0 && count($BChild) == 0) break;
            if (count($AChild) > 0){
                $key = array_keys($AChild)[0];
                $parentId = $AChild[$key];
                $current = 1;
                unset($AChild[$key]);
            }else{
                $key = array_keys($BChild)[0];
                $parentId = $BChild[$key];
                $current = 2;
                unset($BChild[$key]);
            }
        }
        if ($A > $B){
            return 1;  //A大区
        }elseif($A < $B){
            return 2; //B小区
        }

        return 0;
    }

    /**
     * @param $user
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function isXiaoQu($user){
        $type = $this->getTypeByMoney($user);
        $xiao = false;
        switch ($user['user_type']){
            case 1:
              if($type == 2){
                  $xiao = true;
                }
                break;
            case 2:
                if ($type == 1){
                    $xiao = true;
                }
                break;
            default:
                break;
        }
        return $xiao;
    }

    /** 分配一天的奖励
     * @param $user
     * @return array
     */
    public function getOneDayMoney($user){
        $today = date('Y-m-d',time());
        if ($user['one_day_time_dong']  && $user['one_day_time_jifen']
            && $today == date('Y-m-d',$user['one_day_time_jifen'])
            && $today == date('Y-m-d',$user['one_day_time_dong'])
        ){
            return ['status' => -1,'msg'=>'您已经领取过奖励了，请明天再领'];
        }
        $rule = $this->getRule(2,$user['user_id']);
        if (!$rule){
            return ['status' => -1,'msg'=>'您没有投资暂时不能领取奖励'];
        }
        try{
            $this->updateJifenOneDay($user,$rule);
            if (!$this->isXiaoQu($user)){
                return ['status' => -1,'msg'=>'您当前属于大区，不能领取奖励'];
            }
            $total = $this->getAllMoneyFromUser($user);
            $money = round($total*0.06/$rule['all_day'],2);
            $data = [];
            $user = $this->where(['user_id' => $user['user_id']])->find();
            $data['user_money'] = $user['user_money'] + $money;
            $data['one_day_time_dong'] = time();
            $this->where(['user_id' => $user['user_id']])->save($data);
        }catch (\Exception $e){
            return ['status' => -1,'msg'=>$e->getMessage()];
        }

        accountLogOnly($user['user_id'],$money,'业绩的每天分红');
        return ['status' => 1,'msg'=>'奖励发放完毕'];
    }

    /** 计算当前用户下的所有业绩
     * @param $user
     * @return float|int|mixed
     */
    protected function getAllMoneyFromUser($user){
        if (!$this->isXiaoQu($user)){
            throw new \Exception('您当前所处为大区，不能参与分配奖励');
        }
        $parentArray = explode(',',$user['parent_path']);
        //拿到关系表
        $parent = $this->where(['user_id' => current($parentArray)])->find();
        $allChild = unserialize($parent['all_touzi']);
        $totalA = $totalB = 0;
        $current = '';
        $AChild = $BChild = [];
        $currentId = $user['user_id'];
        for ($i = 0; $i < count($allChild);$i++){
            if (isset($allChild[$currentId])){
                $children = $allChild[$currentId];
                if ($i > 0){
                    if ($current == 1){
                        if (isset($children[1])){
                            $one = $children[1];
                            $AChild[] = key($one);
                            $totalA += current($one);
                        }
                        if (isset($children[2])){
                            $one = $children[2];
                            $AChild[] = key($one);
                            $totalA += current($one);
                        }
                    }else{
                        if (isset($children[1])){
                            $one = $children[1];
                            $BChild[] = key($one);
                            $totalB += current($one);
                        }
                        if (isset($children[2])){
                            $one = $children[2];
                            $BChild[] = key($one);
                            $totalB += current($one);
                        }
                    }
                }else{
                    if (isset($children[1])){
                        $one = $children[1];
                        $AChild[] = key($one);
                        $totalA += current($one);
                    }
                    if (isset($children[2])){
                        $one = $children[2];
                        $BChild[] = key($one);
                        $totalB += current($one);
                    }
                }
            }
            if (count($AChild) == 0 && count($BChild) == 0) break;
            if (count($AChild) > 0){
                $key = array_keys($AChild)[0];
                $currentId = $AChild[$key];
                $current = 1;
                unset($AChild[$key]);
            }else{
                $key = array_keys($BChild)[0];
                $currentId = $BChild[$key];
                $current = 2;
                unset($BChild[$key]);
            }
        }
        if($user['type'] == 1){
            $total = $totalA;
        }else{
            $total = $totalB;
        }
        $total = $total/100;
        $total += $user['tou_zi'];
        return $total;
    }

    /**根据用户业绩区分大小区
     * @param $user
     * @return int | bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getTypeByMoney($user){
        $parentArray = explode(',',$user['parent_path']);
        //拿到关系表
        $parent = $this->where(['user_id' => current($parentArray)])->find();
        $allChild = unserialize($parent['all_touzi']);
        $totalA = $totalB = 0;
        $current = '';
        $AChild = $BChild = [];
        $currentId = $user['user_id'];
        for ($i = 0; $i < count($allChild);$i++){
            if (isset($allChild[$currentId])){
                $children = $allChild[$currentId];
                if ($i > 0){
                    if ($current == 1){
                        if (isset($children[1])){
                            $one = $children[1];
                            $AChild[] = key($one);
                            $totalA += current($one);
                        }
                        if (isset($children[2])){
                            $one = $children[2];
                            $AChild[] = key($one);
                            $totalA += current($one);
                        }
                    }else{
                        if (isset($children[1])){
                            $one = $children[1];
                            $BChild[] = key($one);
                            $totalB += current($one);
                        }
                        if (isset($children[2])){
                            $one = $children[2];
                            $BChild[] = key($one);
                            $totalB += current($one);
                        }
                    }
                }else{
                    if (isset($children[1])){
                        $one = $children[1];
                        $AChild[] = key($one);
                        $totalA += current($one);
                    }
                    if (isset($children[2])){
                        $one = $children[2];
                        $BChild[] = key($one);
                        $totalB += current($one);
                    }
                }
            }
            if (count($AChild) == 0 && count($BChild) == 0) break;
            if (count($AChild) > 0){
                $key = array_keys($AChild)[0];
                $currentId = $AChild[$key];
                $current = 1;
                unset($AChild[$key]);
            }else{
                $key = array_keys($BChild)[0];
                $currentId = $BChild[$key];
                $current = 2;
                unset($BChild[$key]);
            }
        }
        if($totalA > $totalB){
            return 1;
        }elseif($totalB > $totalA){
            return 2;
        }
        return 0;
    }

    /** 进行积分至余额的转化
     * @param $user
     * @param $rule
     */
    protected function updateJifenOneDay($user,$rule){
        if (!$rule){
            return;
        }
        $money = round($user['pay_points'] / $rule['all_day'],2);
        $user['pay_points'] = $user['pay_points'] - $money;
        $user['user_money'] = $user['user_money'] + $money;
        $user['one_day_time_jifen'] = time();
        $this->where(['user_id' => $user['user_id']])->save($user);
        accountLogOnly($user['user_id'],$money,'积分转余额');
    }
    /** 根据用户的直推获取最少的上级
     * @param $user
     */
    protected function getUserParent($user){
        if ($user['parent_path']){
            $parentArray = explode(',',$user['parent_path']);
            //拿到关系表
            $parent = $this->where(['user_id' => current($parentArray)])->find();
        }else{
            $parent = $user;
        }
        $allChild = unserialize($parent['all_child']);
        $current = $user['user_id'];
        $currentLevel = $nextLevel = [];
        for ($i = 0;$i < count($allChild);$i++){
            if (!isset($allChild[$current])){
                break;
            }
            $one = $allChild[$current];
            if (!isset($one[2])){
                break;
            }else{
                $nextLevel[] = $one[1];
                $nextLevel[] = $one[2];
            }
            if (count($nextLevel) > 0){
                $currentLevel = $nextLevel;
                $nextLevel = [];
            }
            if (count($currentLevel) > 0){
                $key = array_keys($currentLevel)[0];
                $current = $currentLevel[$key];
                unset($currentLevel[$key]);
            }
        }
        $parent = $this->where(['user_id' => $current])->find();
        return $parent;
    }


    /** 用户转账
     * @param $user
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function zhuanZhang($user){
        $toMoney = I('money');
        $toUserName = I('username');
        if ($toMoney > $user['user_money']){
            return ['status' => -1,'msg'=>'您的余额只有'.$user['user_money'].'请充值或者减少转账额度'];
        }
        $toUser = $this->where(['mobile' => $toUserName])->find();
        if ($toUserName == $user['mobile']){
            return ['status' => -1,'msg'=>'您不能转账给自己'];
        }
        if ($toUser){
            return ['status' => -1,'msg'=>'目标账户不存在'];
        }
        try{
            $this->startTrans();
            $user['user_money'] = $user['user_money'] - $toMoney;
            $toUser['user_money'] = $toUser['user_money'] + $toMoney;
            $this->where(['user_id' => $user['user_id']])->save($user);
            $this->where(['user_id' => $toUser['user_id']])->save($toUser);
            accountLogOnly($user['user_id'],$toMoney,'转账给'.$toUserName.':'.$toMoney);
            accountLogOnly($toUser['user_id'],$toMoney,'收到'.$user['mobile'].'的转账：'.$toMoney);
            $this->commit();
            return ['status' => 1,'msg'=>'转账成功'];
        }catch (\Exception $exception){
            $this->rollback();
            \Think\Log::write($exception->getMessage().$exception->getTraceAsString(),'WARN');
            return ['status' => -1,'msg'=>'转账失败，请联系技术人员'];
        }
    }
}