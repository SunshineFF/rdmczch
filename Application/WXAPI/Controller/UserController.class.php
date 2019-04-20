<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */ 
namespace WXAPI\Controller;
use Think\Controller;
use WXAPI\Logic\UsersLogic;
class UserController extends BaseController {
    public $userLogic;
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();    
    
    } 
    
    public function _initialize(){
        parent::_initialize();
        $this->userLogic = new UsersLogic();
    }
    public function test1(){
    	
    	$accessKeyId = 'LTAIWPvsJMVfnxyj'; //阿里云申请的 Access Key ID
    	$accessKeySecret = 'i2rq0vo3cjGz07FeVTZi2FzFNruiNn'; //阿里云申请的 Access Key Secret
    	$alisms = new \Org\Util\Alisms($accessKeyId, $accessKeySecret);
    	$mobile = '18051228122'; //目标手机号，多个手机号可以逗号分隔
    	$code = 'SMS_73720019'; //短信模板的模板CODE
    	$paramString = '{"number":"344556"}'; //短信模板中的变量；,参数格式{"no": "123456"}
    	
    	$re = $alisms->smsend($mobile,$code,$paramString);
    	echo $re;
    	print_r($re);
    }
    /**
     * 获取全部地址信息
     */
    public function getArea(){
        $data =  M('region')->where(array("parent_id"=>$_GET['parent_id'],"level"=>array("neq",4)))->select();
        $json_arr = array('status'=>1,'msg'=>'成功!','result'=>$data);
        $json_str = json_encode($json_arr);
        exit($json_str);
    }
    /**
     *  登录
     */
    public function login(){
        $username = I('username','');
        $password = I('password','');
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        $data = $this->userLogic->app_login($username,$password);
        
        if($data['status'] != 1)
            exit(json_encode($data));        
        
        $cartLogic = new \Home\Logic\CartLogic();        
        $cartLogic->login_cart_handle($unique_id,$data['result']['user_id']); // 用户登录后 需要对购物车 一些操作               
        exit(json_encode($data));
    }
    /*
     * 第三方登录
     */
    public function thirdLogin(){
        $map['openid'] = I('openid','');
        $map['oauth'] = I('from','');
        $map['nickname'] = I('nickname','');
        $map['head_pic'] = I('head_pic','');        
        $data = $this->userLogic->thirdLogin($map);
        exit(json_encode($data));
    }

    /**
     * 用户注册
     */
    public function reg(){
        $username = I('post.username','');
        $password = I('post.password','');
        $password2 = I('post.password2','');
        $unique_id = I('unique_id');
        //是否开启注册验证码机制
        if(check_mobile($username) && TpCache('sms.regis_sms_enable')){
            $code = I('post.code');
            if(empty($code))
                exit(json_encode(array('status'=>-1,'msg'=>'请输入验证码','result'=>'')));                
            $check_code = $this->userLogic->sms_code_verify($username,$code,$unique_id);
            if($check_code['status'] != 1)
                exit(json_encode(array('status'=>-1,'msg'=>$check_code['msg'],'result'=>'')));
        }        
        $data = $this->userLogic->reg($username,$password,$password2);
        exit(json_encode($data));
    }

    /*
     * 获取用户信息
     */
    public function userInfo(){
        //$user_id = I('user_id');
        $data = $this->userLogic->get_info($this->user_id);
        exit(json_encode($data));
    }

    /*
     *更新用户信息
     */
    public function updateUserInfo(){
        if(IS_POST){
            //$user_id = I('user_id');
            if(!$this->user_id)
                exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));

            I('post.nickname') ? $post['nickname'] = I('post.nickname') : false; //昵称
            I('post.qq') ? $post['qq'] = I('post.qq') : false;  //QQ号码
            I('post.head_pic') ? $post['head_pic'] = I('post.head_pic') : false; //头像地址
            I('post.sex') ? $post['sex'] = I('post.sex') : false;  // 性别
            I('post.birthday') ? $post['birthday'] = strtotime(I('post.birthday')) : false;  // 生日
            I('post.province') ? $post['province'] = I('post.province') : false;  //省份
            I('post.city') ? $post['city'] = I('post.city') : false;  // 城市
            I('post.district') ? $post['district'] = I('post.district') : false;  //地区

            if(!$this->userLogic->update_info($this->user_id,$post))
                exit(json_encode(array('status'=>-1,'msg'=>'更新失败','result'=>'')));
            exit(json_encode(array('status'=>1,'msg'=>'更新成功','result'=>'')));

        }
    }

    /*
     * 修改用户密码
     */
    public function password(){
        if(IS_POST){
            //$user_id = I('user_id');
            if(!$this->user_id)
                exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
            $data = $this->userLogic->password($this->user_id,I('post.old_password'),I('post.new_password'),I('post.confirm_password')); // 获取用户信息
            exit(json_encode($data));
        }
    }

    /**
     * 获取收货地址
     */
    public function getAddressList(){
    	/*"province": "338",
            "city": "569",
            "district": "586",*/
        $this->user_id = I('user_id');
        if(!$this->user_id)
            exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
       $address = M('user_address')->where(array('user_id'=>$this->user_id))->select();
       foreach ($address as $key=>$value)
       {
       	$address[$key]['address'] = M('region')->where(array("id"=>$value['province']))->getField('name').M('region')->where(array("id"=>$value['city']))->getField('name').M('region')->where(array("id"=>$value['district']))->getField('name').$address[$key]['address'];
       }
            
        if(!$address)
            exit(json_encode(array('status'=>1,'msg'=>'没有数据','result'=>'')));
        exit(json_encode(array('status'=>1,'msg'=>'获取成功','result'=>$address)));
    }

    /*
     * 添加地址
     */
    public function addAddress(){

    	$object = file_get_contents('php://input');
    	$_POST = (json_decode($object, true));
    	
        $this->user_id = $_POST['user_id'];
        //echo $this->user_id.'1';
        if(!$this->user_id) exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
        $address_id = I('address_id',0);
        $data = $this->userLogic->add_address($this->user_id,$address_id,I('post.')); // 获取用户信息
        exit(json_encode($data));
    }
    
    /*
     * 编辑地址
     */
    public function editAddress(){
    
    	
    	//echo $address_id;
    	$object = file_get_contents('php://input');
    	$_POST = (json_decode($object, true));
    	
    	$address_id = $_POST['address_id'];
        $this->user_id = $_POST['user_id'];
        //echo $this->user_id.'1';
        if(!$this->user_id) exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
        
        //echo $address_id;
        
        //echo $_POST['address'];
        M('user_address')->where(array("address_id"=>$address_id))->save(array("address"=>$_POST['address'],"mobile"=>$_POST['mobile'],'zipcode'=>$_POST['zipcode'],'consignee'=>$_POST['consignee'],'province'=>$_POST['province'],'city'=>$_POST['city'],'district'=>$_POST['district']));
        exit(json_encode(array('status'=>1,'msg'=>'成功','result'=>'')));
    }
    
    
    /*
     * 地址删除
     */
    public function del_address(){
        $id = I('id');
        $this->user_id = I('user_id');
        if(!$this->user_id) exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
        $address = M('user_address')->where("address_id = $id")->find();
        $row = M('user_address')->where(array('user_id'=>$this->user_id,'address_id'=>$id))->delete();                
        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if($address['is_default'] == 1)
        {
            $address = M('user_address')->where("user_id = {$this->user_id}")->find();            
            M('user_address')->where("address_id = {$address['address_id']}")->save(array('is_default'=>1));
        }        
        if($row)
           exit(json_encode(array('status'=>1,'msg'=>'删除成功','result'=>''))); 
        else
           exit(json_encode(array('status'=>1,'msg'=>'删除失败','result'=>''))); 
    } 
    
    /*
     * 地址删除
     */
    public function get_address(){
    	$id = I('id');
    	$this->user_id = I('user_id');
    	if(!$this->user_id) exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
    	$address = M('user_address')->where("address_id = $id")->find();
    	$address['cityvalue'] = $address['city'];
    	$address['city'] = M('region')->where(array("id"=>$address['province']))->getField('name').M('region')->where(array("id"=>$address['city']))->getField('name').M('region')->where(array("id"=>$address['district']))->getField('name');
  
    	exit(json_encode(array('status'=>1,'msg'=>'删除失败','result'=>$address)));
    }
    
    
    /*
     * 设置默认收货地址
     */
    public function setDefaultAddress(){
//        $user_id = I('user_id',0);
    	$this->user_id = I('user_id',0);
        if(!$this->user_id) exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
        $address_id = I('address_id',0);
        $data = $this->userLogic->set_default($this->user_id,$address_id); // 获取用户信息
        if(!$data)
            exit(json_encode(array('status'=>-1,'msg'=>'操作失败','result'=>'')));
        exit(json_encode(array('status'=>1,'msg'=>'操作成功','result'=>'')));
    }

    /*
     * 获取优惠券列表
     */
    public function getCouponList(){
        $this->user_id = I('user_id',0);
        $p = I('page',0);
        if(!$this->user_id)
            exit(json_encode(array('status'=>-1,'msg'=>'参数有误','result'=>'')));
        $data = $this->userLogic->get_coupon($this->user_id,$_REQUEST['type'],$p);
        
        foreach ($data['result'] as $k=>$v){
        	$data['result'][$k]['use_end_time'] = date("y-m-h h:m:s",$v['use_end_time']);
        }
        
        unset($data['show']);
        exit(json_encode($data));
    }
    /*
     * 获取商品收藏列表
     */
    public function getGoodsCollect(){
        $this->user_id = I('user_id',0);
        $page = I("page",0);
        //if(!$this->user_id) exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
        $data = $this->userLogic->get_goods_collect($this->user_id,$page);
        foreach($data['result'] as $key=>$value){
        	
        		$data['result'][$key]['image'] = SITE_URL.$value['original_img'];
        	
        }
        unset($data['show']);
        exit(json_encode($data));
    }

    /*
     * 用户订单列表
     */
    public function getOrderList(){
        $this->user_id = I('user_id',0);
        $type = I('type','');
        
        if($type == "NO")
        	$type = "";
        
        $page = I('page','');
        if(!$this->user_id) exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
        //条件搜索
        //I('field') && $map[I('field')] = I('value');
        //I('type') && $map['type'] = I('type');
        //$map['user_id'] = $user_id;
        $map = " user_id = {$this->user_id} ";        
        $map = $type ? $map.C($type) : $map;   
        
        //echo 1;
        //print_r($map);
    
        if(I('type') )
        $count = M('order')->where($map)->count();
        $Page       = new \Think\Page($count,10);

        $show = $Page->show();
        $order_str = "order_id DESC";
        $Page->firstRow = $Page->listRows * $page;
        //echo $page;
        //echo $Page->firstRow;
        $order_list = M('order')->order($order_str)->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();

        //获取订单商品
        foreach($order_list as $k=>$v){     
            $order_list[$k] = set_btn_order_status($v);  // 添加属性  包括按钮显示属性 和 订单状态显示属性
            //订单总额
            //$order_list[$k]['total_fee'] = $v['goods_amount'] + $v['shipping_fee'] - $v['integral_money'] -$v['bonus'] - $v['discount'];
            $data = $this->userLogic->get_order_goods($v['order_id']);
            
            foreach ($data['result'] as $key => $value)
            {
            	$data['result'][$key]['image'] = SITE_URL.$value['original_img'];
            }
            
            $order_list[$k]['goods_list'] = $data['result'];            
        }
        exit(json_encode(array('status'=>1,'msg'=>'获取成功','result'=>$order_list)));
    }
    /*
     * 获取订单详情
     */
    public function getOrderDetail(){
        $this->user_id = I('user_id',0);
        if(!$this->user_id) exit(json_encode(array('status'=>-1,'msg'=>'缺少参数','result'=>'')));
        $id = I('id');
        if(I('id')){
            $map['order_id'] = $id;
        }else{
            $map['order_sn'] = I('sn');
        }
        $map['user_id'] = $this->user_id;
        $order_info = M('order')->where($map)->find();
        $order_info = set_btn_order_status($order_info);  // 添加属性  包括按钮显示属性 和 订单状态显示属性
        
        if(!$this->user_id > 0)
            exit(json_encode(array('status'=>-1,'msg'=>'参数有误','result'=>'')));
        if(!$order_info){
            exit(json_encode(array('status'=>-1,'msg'=>'订单不存在','result'=>'')));
        }
        
        $invoice_no = M('DeliveryDoc')->where("order_id = $id")->getField('invoice_no',true);
        $order_info['invoice_no'] = implode(' , ', $invoice_no);
        // 获取 最新的 一次发货时间
        $order_info['shipping_time'] = M('DeliveryDoc')->where("order_id = $id")->order('id desc')->getField('create_time');        
        
        
        //获取订单商品
        $data = $this->userLogic->get_order_goods($order_info['order_id']);
        foreach ($data['result'] as $key=>$value)
        {
        	$data['result'][$key]['image'] = SITE_URL.$value['original_img'];
        }
        $order_info['goods_list'] = $data['result'];
        //$order_info['total_fee'] = $order_info['goods_price'] + $order_info['shipping_price'] - $order_info['integral_money'] -$order_info['coupon_price'] - $order_info['discount'];
        exit(json_encode(array('status'=>1,'msg'=>'获取成功','result'=>$order_info)));
    }

    /**
     * 取消订单
     */
    public function cancelOrder(){
        $id = I('order_id');
        $this->user_id = I('user_id',0);
        if(!$this->user_id > 0 || !$id > 0)
            exit(json_encode(array('status'=>-1,'msg'=>'参数有误','result'=>'')));
        $data = $this->userLogic->cancel_order($this->user_id,$id);
        exit(json_encode($data));
    }
    
    /**
     * 发送手机注册验证码
     * http://www.tp-shop.cn/index.php?m=Api&c=User&a=send_sms_reg_code&mobile=13800138006&unique_id=123456
     */
    public function send_sms_reg_code(){
        $mobile = I('mobile');     
        $user_id = I('user_id');
        if(!check_mobile($mobile))
            exit(json_encode(array('status'=>-1,'msg'=>'手机号码格式有误')));
        $code =  rand(1000,9999);
        $send = $this->userLogic->sms_log($mobile,$code,$user_id);
        if($send['status'] != 1)
            exit(json_encode(array('status'=>-1,'msg'=>$send['msg'])));
        exit(json_encode(array('status'=>1,'msg'=>'验证码已发送，请注意查收')));
    }    

    
    /**
     *  收货确认
     */
    public function orderConfirm(){
        $id = I('order_id',0);
        $this->user_id = I('user_id',0);
        if(!$this->user_id || !$id)
            exit(json_encode(array('status'=>-1,'msg'=>'参数有误','result'=>'')));
        $data = confirm_order($id,$this->user_id);            
        exit(json_encode($data));
    }
    public function comments(){
    	
    	
    	/*
    	 * 
    	 * 
    	 *$user_id = $this->user_id;
        $status = I('get.status');
        $logic = new UsersLogic();
        $result = $logic->get_comment($user_id, $status); //获取评论列表
        $this->assign('comment_list', $result['result']);
        if ($_GET['is_ajax']) {
            $this->display('ajax_comment_list');
            exit;
        }
        $this->display();
    	 */
    	$this->user_id = I('user_id',0);
    	$p = I('page',0);
    	
    	$status = I('get.status');
    	$logic = new UsersLogic();
    	$result = $logic->get_comment($this->user_id , $status,$p); //获取评论列表
    	
    	//$count = M('comment')->where(array("user_id"=>$this->user_id))->count();
    	//$page = new \Think\Page($count,10);
    	//$page->firstRow = $page->listRows * $p;
    	$datas = $result['result'];//M('comment')->where(array("user_id"=>$this->user_id))->limit("{$page->firstRow},{$page->listRows}")->select();
    	
    	foreach ($datas as $key=>$value)
    	{
    		$datas[$key] = array_merge(M('goods')->where(array("goods_id"=>$value['goods_id']))->find(),$value);
    		$datas[$key]['image'] = SITE_URL.$datas[$key]['original_img'];
    		$datas[$key]['add_time'] = date("Y-m-d H:i:s",$datas[$key]['add_time']);
    		$comment = M('comment')->where(array("goods_id"=>$datas[$key]['goods_id'],"order_id"=>$datas[$key]['order_id']))->find();
    		if($comment)
    		{
    			$datas[$key]['service_rank'] = $comment['service_rank'];
    		}
    	}
    	
    	
    	if(!$datas)
    		exit(json_encode(array('status'=>-1,'msg'=>'操作失败','result'=>'')));
    	exit(json_encode(array('status'=>1,'msg'=>'操作成功','result'=>$datas)));
    }
    
    /*
     *添加评论
     */
    public function add_comment(){                
      
            // 晒图片        
            if($_FILES[img_file][tmp_name][0])
            {
                    $upload = new \Think\Upload();// 实例化上传类
                    $upload->maxSize   =    $map['author'] = (1024*1024*3);// 设置附件上传大小 管理员10M  否则 3M
                    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                    $upload->rootPath  =     './Public/upload/comment/'; // 设置附件上传根目录
                    $upload->replace  =     true; // 存在同名文件是否是覆盖，默认为false
                    //$upload->saveName  =   'file_'.$id; // 存在同名文件是否是覆盖，默认为false
                    // 上传文件 
                    $info   =   $upload->upload();                 
                    if(!$info) {// 上传错误提示错误信息                                                                                                
                        exit(json_encode(array('status'=>-1,'msg'=>$upload->getError()))); //$this->error($upload->getError());
                    }else{
                        foreach($info as $key => $val)
                        {
                            $comment_img[] = '/Public/upload/comment/'.$val['savepath'].$val['savename'];                            
                        }   
                        $comment_img = serialize($comment_img); // 上传的图片文件
                    }                     
            }         
         
         
            
            //$unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
            $this->user_id = I('user_id'); // 用户id
            $user_info = M('users')->where("user_id = {$this->user_id}")->find();            

            $add['goods_id'] = I('goods_id');
            $add['email'] = $user_info['email'];
            //$add['nick'] = $user_info['nickname'];
            $add['username'] = $user_info['nickname'];
            $add['order_id'] = I('order_id');
            $add['service_rank'] = I('service_rank');
            $add['deliver_rank'] = I('deliver_rank');
            $add['goods_rank'] = I('goods_rank');
           // $add['content'] = htmlspecialchars(I('post.content'));
            $add['content'] = I('content');
            $add['img'] = $comment_img;
            $add['add_time'] = time();
            $add['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $add['user_id'] = $this->user_id;                    
            
            //添加评论
            $row = $this->userLogic->add_comment($add);
            exit(json_encode($row));
    }  
    
    /*
     * 账户资金
     */
    public function account(){
        
        $this->user_id = I("user_id"); // 唯一id  类似于 pc 端的session id
       // $user_id = I('user_id'); // 用户id
        //获取账户资金记录
        $page = I("page",0);
        $data = $this->userLogic->get_account_log($this->user_id,I('get.type'),$page);
        $account_log = $data['result'];
        
        foreach ($account_log as $key=>$value)
        {
        	$account_log[$key]['change_time'] = date("Y-m-d h:i:s",$value['change_time']);
        }
        
        exit(json_encode(array('status'=>1,'msg'=>'获取成功','result'=>$account_log)));
    }    
    
    /**
     * 退换货列表
     */
    public function return_goods_list()
    {        
        
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
       // $user_id = I('user_id'); // 用户id       
        $count = M('return_goods')->where("user_id = {$this->user_id}")->count();        
        $page = new \Think\Page($count,4);
        $list = M('return_goods')->where("user_id = {$this->user_id}")->order("id desc")->limit("{$page->firstRow},{$page->listRows}")->select();
        $goods_id_arr = get_arr_column($list, 'goods_id');
        if(!empty($goods_id_arr))
            $goodsList = M('goods')->where("goods_id in (".  implode(',',$goods_id_arr).")")->getField('goods_id,goods_name');        
        foreach ($list as $key => $val)
        {
            $val['goods_name'] = $goodsList[$val[goods_id]];
            $list[$key] = $val;
        }
        //$this->assign('page', $page->show());// 赋值分页输出                    	    	
        exit(json_encode(array('status'=>1,'msg'=>'获取成功','result'=>$list)));
    }    
    
    
    /**
     *  售后 详情
     */
    public function return_goods_info()
    {
        $id = I('id',0);
        $return_goods = M('return_goods')->where("id = $id")->find();
        if($return_goods['imgs'])
            $return_goods['imgs'] = explode(',', $return_goods['imgs']);        
        $goods = M('goods')->where("goods_id = {$return_goods['goods_id']} ")->find();                
        $return_goods['goods_name'] = $goods['goods_name'];
        exit(json_encode(array('status'=>1,'msg'=>'获取成功','result'=>$return_goods)));
    }    
    
    
    /**
     * 申请退货状态
     */
    public function return_goods_status()
    {
        $order_id = I('order_id',0);        
        $goods_id = I('goods_id',0);
        $spec_key = I('spec_key','');
        
        $return_goods = M('return_goods')->where("order_id = $order_id and goods_id = $goods_id and spec_key = '$spec_key' and status in(0,1)")->find();            
        if(!empty($return_goods))        
            exit(json_encode(array('status'=>1,'msg'=>'已经在申请退货中..','result'=>$return_goods['id']))); 
         else
             exit(json_encode(array('status'=>1,'msg'=>'可以去申请退货','result'=>-1)));
    }
    /**
     * 申请退货
     */
    public function return_goods()
    {
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        //$user_id = I('user_id'); // 用户id              
        $order_id = I('order_id',0);
        $order_sn = I('order_sn',0);
        $goods_id = I('goods_id',0);
        $type = I('type',0); // 0 退货  1为换货
        $reason = I('reason',''); // 问题描述
        $spec_key = I('spec_key');
		                
        if(empty($order_id) || empty($order_sn) || empty($goods_id)|| empty($this->user_id)|| empty($type)|| empty($reason))
            exit(json_encode(array('status'=>-1,'msg'=>'参数不齐!')));
        
        $c = M('order')->where("order_id = $order_id and user_id = {$this->user_id}")->count();
        if($c == 0)
        {
             exit(json_encode(array('status'=>-3,'msg'=>'非法操作!')));           
        }         
        
        $return_goods = M('return_goods')->where("order_id = $order_id and goods_id = $goods_id and spec_key = '$spec_key' and status in(0,1)")->find();            
        if(!empty($return_goods))
        {
            exit(json_encode(array('status'=>-2,'msg'=>'已经提交过退货申请!')));
        }       
        if(IS_POST)
        {
            
    		// 晒图片
    		if($_FILES[img_file][tmp_name][0])
    		{
    			$upload = new \Think\Upload();// 实例化上传类
    			$upload->maxSize   =    $map['author'] = (1024*1024*3);// 设置附件上传大小 管理员10M  否则 3M
    			$upload->exts      =    array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    			$upload->rootPath  =    './Public/upload/return_goods/'; // 设置附件上传根目录
    			$upload->replace   =    true; // 存在同名文件是否是覆盖，默认为false
    			//$upload->saveName  =  'file_'.$id; // 存在同名文件是否是覆盖，默认为false
    			// 上传文件
    			$upinfo  =  $upload->upload();
    			if(!$upinfo) {// 上传错误提示错误信息
    				$this->error($upload->getError());
    			}else{
    				foreach($upinfo as $key => $val)
    				{
    					$return_imgs[] = '/Public/upload/return_goods/'.$val['savepath'].$val['savename'];
    				}
    				$data['imgs'] = implode(',', $return_imgs);// 上传的图片文件
    			}
    		}            
            $data['order_id'] = $order_id; 
            $data['order_sn'] = $order_sn; 
            $data['goods_id'] = $goods_id; 
            $data['addtime'] = time(); 
            $data['user_id'] = $this->user_id;            
            $data['type'] = $type; // 服务类型  退货 或者 换货
            $data['reason'] = $reason; // 问题描述            
            $data['spec_key'] = $spec_key; // 商品规格						
            M('return_goods')->add($data);      
            exit(json_encode(array('status'=>1,'msg'=>'申请成功,客服第一时间会帮你处理!')));                        
        }     
    } 

    
    public function validateOpenid()
    {
    
        $open_id = $_GET['openid'];
        $res = M('users')->where(array("open_id"=>$open_id))->find();
        
        if($res)
        {
        	$res['head_pic'] = SITE_URL.$res['head_pic'];
            $tp_config = M('config')->where(array("name"=>'hot_keywords'))->find();
            
            $res['apply'] = D('seller_apply')->where(array("user_id"=>$res['user_id']))->find();
            
            if($tp_config['name'] == 'hot_keywords')
                    $res['hot_keywords'] = explode('|', $tp_config['value']);
                
            echo  json_encode(array("code"=>'200','msg'=>'验证成功',"data"=>$res));
        }
        else
            echo json_encode(array("code"=>'400','msg'=>'验证失败'));
    }
    
    public function bindPhone(){
    	$user_id = $_GET['user_id'];
    	$phoneNum = $_GET['phone'];
    	$res = M('users')->where(array("user_id"=>$user_id))->save(array("mobile"=>$phoneNum));
    	echo json_encode(array("code"=>'1','msg'=>'验证成功'));
    	 
    }
    
    public function register()
    {
    	$data['city'] = $_GET['city'];
    	$data['country'] = $_GET['country'];
    	$data['gender'] = $_GET['gender'];
    	$data['open_id'] = $_GET['open_id'];
    	$data['nick_name'] = $_GET['nick_name'];
    	$data['province'] = $_GET['province'];
    	$data['head_pic'] = $_GET['head_pic'];
    	$data['reg_time'] = time();
    	$id = M('users')->add($data);
    	$res = M('users')->where(array("user_id"=>$id))->find();
    
    	$this->test($data['head_pic'],$id);
    	$res = M('users')->where(array("user_id"=>$id))->find();
    	$res['head_pic'] = SITE_URL.$res['head_pic'];
    	$res = M('users')->where(array("user_id"=>$id))->find();
    	if($res)
    	{
    		$tp_config = M('config')->where(array("name"=>'hot_keywords'))->find();
    
    		if($tp_config['name'] == 'hot_keywords')
    			$res['hot_keywords'] = explode('|', $tp_config['value']);
    
    			$res['apply'] = D('seller_apply')->where(array("user_id"=>$res['user_id']))->find();
    			
    
    			echo json_encode(array("code"=>'200','msg'=>'注册成功','res'=>$res));
    	}
    	else
    		echo json_encode(array("code"=>'400','msg'=>'失败'));
    
    }
    
    
    function test($url,$id) {
    
    
    
    	$header = array("Connection: Keep-Alive", "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Pragma: no-cache", "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3", "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:29.0) Gecko/20100101 Firefox/29.0");
    
    	$ch = curl_init();
    
    	curl_setopt($ch, CURLOPT_URL, $url);
    
    	curl_setopt($ch, CURLOPT_HEADER, $v);
    
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    
    
    	$content = curl_exec($ch);
    
    	$curlinfo = curl_getinfo($ch);
    
    	//echo "string";
    
    	//print_r($curlinfo);
    
    	//关闭连接
    
    	curl_close($ch);
    
    
    	if ($curlinfo['http_code'] == 200) {
    
    		if ($curlinfo['content_type'] == 'image/jpeg') {
    
    			$exf = '.jpg';
    
    		} else if ($curlinfo['content_type'] == 'image/png') {
    
    			$exf = '.png';
    
    		} else if ($curlinfo['content_type'] == 'image/gif') {
    
    			$exf = '.gif';
    
    		}
    
    		//存放图片的路径及图片名称  *****这里注意 你的文件夹是否有创建文件的权限 chomd -R 777 mywenjian
    
    		$filename = "Public/head/".$id . $exf;//这里默认是当前文件夹，可以加路径的 可以改为$filepath = '../'.$filename
    
    		$res = file_put_contents($filename, $content);//同样这里就可以改为$res = file_put_contents($filepath, $content);
    
    		$filename = "/".$filename;
    		
    		M('users')->where(array("user_id"=>$id))->save(array("head_pic"=>$filename));
    	}
    
    }
    
    
    public function getHotKeywords()
    {
    
    	
    		$tp_config = M('config')->where(array("name"=>'hot_keywords'))->find();
    
    		if($tp_config['name'] == 'hot_keywords')
    			$res['hot_keywords'] = explode('|', $tp_config['value']);
    
    
    		echo  json_encode(array("code"=>'200','msg'=>'验证成功',"data"=>$res));
    	
    }
    public function logoutWX(){
    	
    	$open_id = $_GET['openid'];
    	
    	if($open_id == null)
    	{
    		echo json_encode(array("code"=>'400','msg'=>'非法参数'));
    		exit();
    	}
    	
    	
    	$res = 1;///D('phone_captcha')->where(array("phone"=>$phone,'captcha'=>$num))->find();
    	if($res)
    	{
    		$res = M('users')->where(array("open_id"=>$open_id))->save(array("open_id"=>'',"openid"=>'','oauth'=>''));
    	
    	
    		if($res)
    		{
    			echo json_encode(array("code"=>'200','msg'=>'注销成功','res'=>$res));
    		}
    		else
    			echo json_encode(array("code"=>'400','msg'=>'注销有误,请稍后重试'));
    	}
    	else
    		echo json_encode(array("code"=>'400','msg'=>'验证码或者手机号码有误'));
    }
    
    public function validate()
    {
        $phone = $_GET['phone'];
        $num = $_GET['num'];
        $open_id = $_GET['openid'];
    
        if($open_id == null)
        {
            echo json_encode(array("code"=>'400','msg'=>'非法参数'));
            exit();
        }
        if($phone == null || strlen($phone) != 11)
        {
            echo json_encode(array("code"=>'400','msg'=>'手机号码输入有误'));
            exit();
        }
        
        
        $res = 1;///D('phone_captcha')->where(array("phone"=>$phone,'captcha'=>$num))->find();
        if($res)
        {
            $res = M('users')->where(array("mobile"=>$phone))->save(array("open_id"=>$open_id,"openid"=>$open_id,'oauth'=>'weixin'));
            
            
            if($res)
            {
            	$res = M('users')->where(array("open_id"=>$open_id))->find();
                echo json_encode(array("code"=>'200','msg'=>'登录成功','res'=>$res));
            }
            else
                echo json_encode(array("code"=>'400','msg'=>'手机号码有误'));
        }
        else
            echo json_encode(array("code"=>'400','msg'=>'验证码或者手机号码有误'));
    }
    
    
    public function register1()
    {
        $phone = $_GET['phone'];
        $num = $_GET['num'];
        $user_id = $_GET['user_id'];
        $pass = $_GET['pass'];
        $remindpass= $_GET['remindpass'];
        
        
        
        
        if($phone == null || strlen($phone) != 11)
        {
            echo json_encode(array("code"=>'400','msg'=>'手机号码输入有误'));
            exit();
        }
        $res = M("sms_log")->where(array("mobile"=>$phone,"code"=>$num))->find();
        if(!$res){
        	echo json_encode(array("code"=>'400','msg'=>'验证码输入有误'));
        	exit();
        }
        
        $data['mobile'] = $phone;
        $data['nickname'] = $pass;
        $data['email'] = $remindpass;
         
         $res = M('users')->where(array("user_id"=>$user_id))->save($data);
         $res = M('users')->where(array("user_id"=>$user_id))->find();
         if($res)
         {
              echo json_encode(array("code"=>'200','msg'=>'完善成功','res'=>$res));
         }
         else
              echo json_encode(array("code"=>'400','msg'=>'失败'));
        
    }
    
    
    public function points()
    {
    	$this->user_id = I('user_id');
    	$p = I('page',0);
    	$type = I('type','all');
    	$this->assign('type',$type);
    	if($type == 'recharge'){
    		$count = M('recharge')->where("user_id=" . $this->user_id)->count();
    		$Page = new Page($count, 16);
    		$Page->firstRow = $p * $Page->listRows;
    		$account_log = M('recharge')->where("user_id=" . $this->user_id)->order('order_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    	}else if($type == 'points'){
    		$count = M('account_log')->where("user_id=" . $this->user_id ." and pay_points!=0 ")->count();
    		$Page       = new \Think\Page($count,10);
    		$Page->firstRow = $p * $Page->listRows;
    		$account_log = M('account_log')->where("user_id=" . $this->user_id." and pay_points!=0 ")->order('log_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    	}else{
    		$count = M('account_log')->where("user_id=" . $this->user_id)->count();
    		$Page = new Page($count, 16);
    		$Page->firstRow = $p * $Page->listRows;
    		$account_log = M('account_log')->where("user_id=" . $this->user_id)->order('log_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    	}
    	foreach ($account_log as $key=>$value)
    	{
    		$account_log[$key]['change_time'] = date("Y-m-d h-i-s",$value['change_time']);
    	}
    	echo json_encode(array("code"=>'200','msg'=>'成功','res'=>$account_log));
    }
    
    public function apply(){
    	$_GET['add_time'] = time();
    	$_GET['status'] = 0;
    	if($_GET['aid'] == 0)
    	M("seller_apply")->add($_GET);
    	else{
    		M("seller_apply")->where(array("id"=>$_GET['aid']))->save($_GET);
    	}
    	//print_r($_GET);
    	echo json_encode(array("code"=>'200','msg'=>'成功'));
    }
    public function getOpenid(){
    	$result = $this->get_url_content($_GET['url']);
    	echo $result;
    }
    public function get_url_content($url)
    {
        $user_agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)";
        //$data_string = json_encode($data);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            //'Content-Length: ' . strlen($data_string)
        ));
        
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}