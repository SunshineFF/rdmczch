<?php
namespace Mobile\Controller;

use Mobile\Controller\MobileBaseController;

class ProductController extends MobileBaseController
{
    protected $user_id;
    protected $user;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        if (session('?user')) {
            $user = session('user');
            $user = M('users')->where("user_id = {$user['user_id']}")->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
        }
        $nologin = array(
            'login', 'pop_login', 'do_login', 'logout', 'verify', 'set_pwd', 'finished',
            'verifyHandle', 'reg', 'send_sms_reg_code', 'find_pwd', 'check_validate_code',
            'forget_pwd', 'check_captcha', 'check_username', 'send_validate_code', 'express',
        );
        if (!$this->user_id && !in_array(ACTION_NAME, $nologin)) {
            header("location:" . U('Mobile/User/login'));
            exit;
        }
    }

    public function index(){
        return $this->display();
    }

    public function post_product(){
        try{

        }catch (\Exception $exception){

        }
    }

    /**
     * 选择支付方式页面
     */
    public function select_payment_method(){
        try{
            $product = $this->getProductData(I('product_id'));
            $this->assign('product',$product);
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
        $this->display();
    }

    protected function initOrderData(){
        $productId = I('product_id');
        $time = time();
        $product = $this->getProductData($productId);
        $data = [];
        $data['product_id'] = $productId;
        $data['total'] = $product['price'];
        $data['created_at'] = $time;
        $data['updated_at'] = $time;
        $data['user_id'] = $this->user_id;
        $data['order_id'] = date('YmdHis').rand(1000,9999);
    }

    protected function getProductData($productId){
       $product = M('product')->where(['id'=>$productId])->find();
       if (!$product){
           throw new \Exception('藏品不存在');
       }
       return $product;
    }
}