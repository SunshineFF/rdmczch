<?php

use Think\Model\RelationModel;

class xianxia extends RelationModel{

    protected $data;

    const CODE = 'xianxia';

    protected $return = [
        'code' => 200,
        'msg' => '提交成功'
    ];

    public function __construct()
    {
        $this->data = M('plugin')->where(['code'=>self::CODE])->find();
    }

    public function get_code($order,$config = null){
        return '<span id="payment_url"></span><input class="button button-block bg-dot button-big" type="submit"  class="payment" value="提交订单" />';

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
//        update_pay_status($order['order_sn'],);

        return $this->return;
    }


}
