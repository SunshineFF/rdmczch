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

    /**
     * 支付逻辑
     */
    public function post_product(){
        $orderModel = M('product_order');
        try{
            $orderModel->startTrans();
            $orderData = $this->initOrderData();
            $return = $this->payOrder($orderData);
            $orderModel->add($orderData);
            $orderModel->commit();
            $this->success($return['msg'],U('User/index'));
        }catch (\Exception $exception){
            $orderModel->rollback();
            $this->error($exception->getMessage(),U('Product/index'));
        }
    }

    /** 获取订单方式并进行支付
     * @param $orderData
     * @return mixed
     */
    protected function payOrder($orderData){
        $payment = $this->initPayment();
        return $payment->payForProduct($orderData['total'],$this->user);
    }

    private function initPayment(){
        $method = I('method');
        include_once "plugins/payment/{$method}/{$method}.class.php"; // D:\wamp\www\svn_tpshop\www\plugins\payment\alipay\alipayPayment.class.php
        $code = '\\'.$method; // \alipay
        return new $code();
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

    /** 初始化订单数据
     * @return array
     * @throws \Exception
     */
    protected function initOrderData(){
        $productId = I('product_id');
        $time = time();
        $product = $this->getProductData($productId);
        $method = I('method');
        $data = [];
        $data['product_id'] = $productId;
        $data['total'] = $product['price'];
        $data['created_at'] = $time;
        $data['updated_at'] = $time;
        $data['user_id'] = $this->user_id;
        $data['status'] = $this->getStatus($method);
        $data['pay_method'] = $method;
        $data['order_id'] = date('YmdHis').rand(1000,9999);
        return $data;
    }

    /** 获取藏品信息
     * @param $productId
     * @return mixed
     * @throws \Exception
     */
    protected function getProductData($productId){
       $product = M('product')->where(['id'=>$productId])->find();
       if (!$product){
           throw new \Exception('藏品不存在');
       }
       return $product;
    }

    /** 根据订单支付方式 确定订单当前状态
     * @param $method
     * @return int
     */
    protected function getStatus($method){
        switch ($method){
            case 'yue':
                return 1;
            case 'xianxia':
                return 0;
            default:
                return 0;
        }
    }

    public function user_list(){
        $sql = "select * from __PREFIX__product_order o left join __PREFIX__product p on p.id = o.product_id
 where o.user_id=".$this->user_id;
        $orderList = M('product_order')->query($sql);
        $this->assign('order_list',$orderList);
        return $this->display();
    }


}