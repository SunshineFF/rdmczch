<?php

use Think\Model\RelationModel;
use Home\Logic\UsersLogic;

class yue extends RelationModel{

    protected $data;

    const CODE = 'yue';

    protected $return = [
        'code' => 200,
        'msg' => '支付成功'
    ];

    public function __construct()
    {
        $this->data = M('plugin')->where(['code'=>self::CODE])->find();
    }

    public function get_code($order,$config = null){
        return '<span id="payment_url" uu="'.U('mobile/member/yuepay').'" ></span><input class="button button-block bg-dot button-big" type="submit"  class="payment" value=" 立刻支付 " />';

    }

    public function getData($key){
        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        return null;
    }

    /** 正常订单支付接口
     * @return array
     */
    public function respond2(){
        $order_id = I('order_id');
        if (!$order_id){
            $this->return['code'] = 400;
            $this->return['msg'] = '订单不存在';
            return $this->return;
        }
        $order = M('order')->where(['order_id' => $order_id])->find();
        $this->return['order_sn'] = $order['order_sn'];
        $user = M('users')->where(['user_id' => $order['user_id']])->find();
        if ($order['total_amount'] > $user['user_money']){
            $this->return['code'] = 400;
            $this->return['msg'] = '您的余额不足，请充值';
            return $this->return;
        }
        if ( true || $order['jifen'] > $user['pay_points']){
            $this->return['code'] = 400;
            $this->return['msg'] = '您的积分不足，请获取积分';
            return $this->return;
        }
        $userModel = M('users');
        try{
            $userModel->startTrans();
            $this->changeUserMoney($user,$order,$userModel);
            update_pay_status($order['order_sn']);
            $userModel->commit();
        }catch (\Exception $e){
            $userModel->rollback();
            $this->return['code'] = 400;
            $this->return['msg'] = '网络错误，请稍后再试。';
            return $this->return;
        }
        return $this->return;
    }

    protected function changeUserMoney($user,$order,$userModel){
        $newMoney = $user['user_money'] - $order['total_amount'];
        $user['user_money'] = $newMoney;
        $userModel->save($user);
    }

    /**
     * @param $money
     * @param $user
     * @return array
     * @throws Exception
     */
    public function payForProduct($money,$user){
        if ($money > $user['user_money']){
            throw new \Exception('您的余额不足，请充值');
        }
        $userLogic = new UsersLogic();
        try{
            $userLogic->startTrans();
            $newMoney = $user['user_money'] - $money;
            $user['user_money'] = $newMoney;
            $user['tou_zi'] = $user['tou_zi'] + $money;
            $userLogic->save($user);
            $userLogic->updateZhiTui($user,$money);
            accountLogOnly($user['user_id'],$money,'用户购买藏品');
            $userLogic->commit();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $this->return;
    }

}
