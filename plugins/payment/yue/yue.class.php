<?php

use Think\Model\RelationModel;

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

}
