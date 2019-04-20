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
use Think\Page;
use Think\Page2;

class PayController extends BaseController
{

    public function index()
    {}

    public function zfCallback()
    {
        
        // "out_trade_no
        $out_trade_no = $_POST['out_trade_no'];
        $trade_no = $_POST['trade_no'];
        $trade_status = $_POST['trade_status'];
        
        if ($_POST['trade_status'] == 'TRADE_FINISHED') {
            return;
            $osn = $out_trade_no;
            
            $res = D("order")->where(array(
                "order_sn" => $osn
            ))->find();
            
            if ($res && $res['pay_status'] == 0) {
                
                if ($res['otype'] == 1) {
                    D("order")->where(array(
                        "order_sn" => $osn
                    ))->delete();
                }
            }
        } else 
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $res = D("order")->where(array(
                    "order_sn" => $out_trade_no
                ))->find();
                if ($res && $res['pay_status'] == 0) {
                    
                    if ($res['otype'] == 1) {
                        
                        $data['pay_status'] = 1;
                        $data['order_status'] = 2;
                        $data['shipping_status'] = 1;
                        $data['app_status'] = 2;
                        $data['pay_time'] = time();
                        $data['total_fee'] = $_POST['total_fee'];
                        
                        
                        //积分处理
                        $good = D('goods')->where(array(
                            "goods_id" => $res['good_id']
                        ))->find();
                        $is_integral = $res['is_integral'];
                        $user_id = $res['user_id'];
                        
                        
                        
                        
                        if ($good['goods_id'] == 144 || $good['goods_id'] == 191)
                            $pay = $good['market_price'];
                        else
                            $pay = $res['total_amount'];
                        
                        $points_money = $pay - $_POST['total_fee'];
                        $point_rate = D("config")->where(array(
                            "name" => "point_rate"
                        ))->getField("value");
                        
                        
                        
                        
                        
                        if ($is_integral == 1) {
                            // file_put_contents("./zZF.txt", '222'.'222', FILE_APPEND);
                            $userpoints = D('users')->where(array(
                                'user_id' => $user_id
                            ))->getField("points");
                        
                            //file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                            $user['points'] = $userpoints - $points_money * $point_rate;
                            D('users')->where(array(
                            'user_id' => $user_id
                            ))->save($user);
                            // file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                            $data['use_points'] = $points_money * $point_rate;
                            $data['points_money'] = $points_money;
                        
                            $log = array();
                            $log['user_id'] = $user_id;
                            $log['order_sn'] = $res['order_sn'];
                            $log['pay_points'] = $data['use_points'];
                            $log['points'] = $user['points'];
                            $log['user_money'] = 0;
                            $log['frozen_money'] = 0;
                            $log['change_time'] = time();
                            $log['desc'] = '购买商品积分消费';
                            D('account_log')->add($log);
                        }
                        
                        
                        
                    } else {
                        //file_put_contents("./zZF.txt", json_encode($_POST), FILE_APPEND);
                        $data['pay_status'] = 1;
                        $data['order_status'] = 1;
                        $data['pay_time'] = time();
                        $data['total_fee'] = $_POST['total_fee'];
                        
                        
                        //积分处理
                        $good = D('goods')->where(array(
                            "goods_id" => $res['good_id']
                        ))->find();
                        $is_integral = $res['is_integral'];
                        $user_id = $res['user_id'];
                        
                        
                        
                        
                        if ($good['goods_id'] == 144 || $good['goods_id'] == 191)
                            $pay = $good['market_price'];
                        else
                            $pay = $res['total_amount'];
                        
                        $points_money = $pay - $_POST['total_fee'];
                        $point_rate = D("config")->where(array(
                            "name" => "point_rate"
                        ))->getField("value");
                        
                        
                        
                        
                        
                        if ($is_integral == 1) {
                           // file_put_contents("./zZF.txt", '222'.'222', FILE_APPEND);
                            $userpoints = D('users')->where(array(
                                'user_id' => $user_id
                            ))->getField("points");
                            
                            //file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                            $user['points'] = $userpoints - $points_money * $point_rate;
                            D('users')->where(array(
                                'user_id' => $user_id
                            ))->save($user);
                           // file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                            $data['use_points'] = $points_money * $point_rate;
                            $data['points_money'] = $points_money;
                            
                            $log = array();
                            $log['user_id'] = $user_id;
                            $log['order_sn'] = $res['order_sn'];
                            $log['pay_points'] = $data['use_points'];
                            $log['points'] = $user['points'];
                            $log['user_money'] = 0;
                            $log['frozen_money'] = 0;
                            $log['change_time'] = time();
                            $log['desc'] = '购买商品积分消费';
                            D('account_log')->add($log);
                        }
                        
                        // 添加消息记录
                        $good = D('goods')->where(array(
                            "goods_id" => $res['goods_id']
                        ))->find();
                        if ($res['market_price'] == 0)
                            $msg['description'] = sprintf("您购买的商品:%s已付款成功", $good['goods_name']);
                        else
                            $msg['description'] = sprintf("您购买的商品:%s预付款已支付", $good['goods_name']);
                        $msg['type'] = 0;
                        $msg['user_id'] = $res['user_id'];
                        $msg['user_type'] = 0;
                        $msg['time'] = time();
                        D('message')->add($msg);
                    }
                    D("order")->where(array(
                        "order_sn" => $out_trade_no
                    ))->save($data);
                } else {
                    $res = D("repairs")->where(array(
                        "order_no" => $out_trade_no
                    ))->find();
                    
                    if ($res) {
                        
                        if ($res['orderstate'] == - 1) {
                            $data['orderState'] = 0;
                            $data['pay_time'] = time();
                            $data['total_fee'] = $_POST['total_fee'];
                            
                            
                            $user_id = $res['user_id'];
                            
                            $user = D('users')->where(array(
                                "user_id" => $user_id
                            ))->find();
                            
                            
                            //积分处理
                            
                            $is_integral = $res['is_integral'];
                            $user_id = $res['user_id'];
                            
                            $pay = $res['ordermoney'];
                            
                            $points_money = $pay - $_POST['total_fee'];
                            $point_rate = D("config")->where(array(
                                "name" => "point_rate"
                            ))->getField("value");
                            
                            
                            
                            
                            
                            if ($is_integral == 1) {
                                // file_put_contents("./zZF.txt", '222'.'222', FILE_APPEND);
                                $userpoints = D('users')->where(array(
                                    'user_id' => $user_id
                                ))->getField("points");
                            
                                //file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                                $user['points'] = $userpoints - $points_money * $point_rate;
                                D('users')->where(array(
                                'user_id' => $user_id
                                ))->save($user);
                                // file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                                $data['use_points'] = $points_money * $point_rate;
                                $data['points_money'] = $points_money;
                            
                                $log = array();
                                $log['user_id'] = $user_id;
                                $log['order_sn'] = $res['order_no'];
                                $log['pay_points'] = $data['use_points'];
                                $log['points'] = $user['points'];
                                $log['user_money'] = 0;
                                $log['frozen_money'] = 0;
                                $log['change_time'] = time();
                                $log['desc'] = '维修消费';
                                D('account_log')->add($log);
                            }
                            
                            
                            
                            if ($user['service_users']) {
                                $msg['repair_id'] = $res['orderid'];
                                $msg['log_note'] = '支付维修订单';
                                $msg['time'] = time();
                                D("repairs_log")->add($msg);
                                
                                /*
                                 * $msg['repair_id'] = $res['orderid'];
                                 * $msg['log_note'] = '指派维修订单:' . '系统自动指派';
                                 * $msg['user_id'] = $user['service_users'];
                                 * $msg['time'] = time();
                                 *
                                 * D("repairs_log")->add($msg);
                                 *
                                 * $data['repair_id'] = $user['service_users'];
                                 * $data['repairState'] = 1;
                                 * $data['repair_time'] = time();
                                 *
                                 *
                                 * //添加消息记录（服务商）
                                 * if($res['type'] == 0)
                                 * $good = D('goods')->where(array("goods_id"=>144))->find();
                                 * else
                                 * $good = D('goods')->where(array("goods_id"=>146))->find();
                                 *
                                 * $msg['description'] = sprintf("您接收到新的维修订单:%s",$good['goods_name']);
                                 * $msg['type'] = 0;
                                 * $msg['user_id'] = $user['service_users'];
                                 * $msg['user_type'] = 1;
                                 * $msg['time'] = time();
                                 * D('message')->add($msg);
                                 */
                            } else {
                                $msg['repair_id'] = $res['orderid'];
                                $msg['log_note'] = '支付维修订单';
                                $msg['time'] = time();
                                D("repairs_log")->add($msg);
                            }
                            D("repairs")->where(array(
                                "order_no" => $out_trade_no
                            ))->save($data);
                            
                            // 添加消息记录
                            if ($res['type'] == 0)
                                $good = D('goods')->where(array(
                                    "goods_id" => 144
                                ))->find();
                            else
                                $good = D('goods')->where(array(
                                    "goods_id" => 146
                                ))->find();
                            
                            $msg['description'] = sprintf("您维修的:%s已付款成功", $good['goods_name']);
                            $msg['type'] = 1;
                            $msg['user_id'] = $res['user_id'];
                            $msg['user_type'] = 0;
                            $msg['time'] = time();
                            D('message')->add($msg);
                            
                            try {
                                require_once ("./src/JPush/JPush.php");
                                require_once ("./src/JPush/core/JPushException.php");
                                $client = new \JPush('9ffed5d6454fb6bd37777907', '5a68e64311c15a5b3eb3529f');
                                
                                $result = $client->push()
                                    ->setPlatform('ios', 'android')
                                    ->addAlias('13861292076')
                                    ->setNotificationAlert('新的维修订单')
                                    ->addAndroidNotification('Hi, 新的维修订单。', '新的维修订单', 1, array(
                                    "type" => "6"
                                ))
                                    ->addIosNotification('Hi, 新的维修订单。', 'tmp.mp3', '+1', true, 'iOS category', array(
                                    "type" => "6"
                                ))
                                    ->setMessage("msg content", 'msg title', 'type', array(
                                    "type" => "6"
                                ))
                                    ->setOptions(100000, 3600, null, true)
                                    ->send();
                            } catch (\APIRequestException $e) {} catch (\APIConnectionException $e) {}
                            
                            if ($user['service_users']) {
                                /*
                                 * $user = D("service_users")->where(array(
                                 * "id" => $user['service_users']
                                 * ))->find();
                                 * $mobile = $user['phone'];
                                 *
                                 * require_once ("./src/JPush/JPush.php");
                                 *
                                 * $client = new \JPush('9ffed5d6454fb6bd37777907', '5a68e64311c15a5b3eb3529f');
                                 * $result = $client->push()
                                 * ->setPlatform('ios', 'android')
                                 * ->addAlias($mobile)
                                 * ->setNotificationAlert('新的维修订单')
                                 * ->addAndroidNotification('Hi, 新的维修订单。', '新的维修订单', 1, array(
                                 * "type" => "2"
                                 * ))
                                 * ->addIosNotification('Hi, 新的维修订单。', '新的维修订单', '+1', true, 'iOS category', array(
                                 * "type" => "2"
                                 * ))
                                 * ->setMessage("msg content", 'msg title', 'type', array(
                                 * "type" => "2"
                                 * ))
                                 * ->setOptions(100000, 3600, null, true)
                                 * ->send();
                                 */
                            }
                        }
                    }
                }
            }
    }

    public function wxCallback()
    {
        
        // file_put_contents("./wx.txt", '1', FILE_APPEND);
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA']; // 这里拿到微信返回的数据结果
        file_put_contents("./wx.txt", $postStr, FILE_APPEND);
        
        $getData = $this->xmlstr_to_array($postStr); // 为了方便我就直接把结果转成数组，看个人爱好了
                                                     // file_put_contents("./wx.txt", json_encode($getData), FILE_APPEND);
        
        if (($getData['total_fee']) && ($getData['result_code'] == 'SUCCESS')) {
            $osn = trim($getData['out_trade_no']);
            
            $res = M("order")->where(array(
                "master_order_sn" => $osn
            ))->select();
            
            foreach ($res as $order)
            if ($order && $order['pay_status'] == 0)
            {
                M("order")->where(array(
                "order_id" => $order['order_id']
                ))->save(array("pay_status"=>1));
            }
            echo 'success';
            return ;
            if ($res && $res['pay_status'] == 0) {
                
                if ($res['otype'] == 1) {
                    $data['pay_status'] = 1;
                    $data['order_status'] = 2;
                    $data['shipping_status'] = 1;
                    $data['app_status'] = 2;
                    $data['pay_time'] = time();
                    $data['total_fee'] = $getData['total_fee'] / 100;
                    
                    
                    //积分处理
                    $good = D('goods')->where(array(
                        "goods_id" => $res['good_id']
                    ))->find();
                    $is_integral = $res['is_integral'];
                    $user_id = $res['user_id'];
                    
                    
                    
                    
                    $pay = $res['total_amount'];
                    
                    $points_money = $pay - (int)$getData['total_fee'] / 100;
                    $point_rate = D("config")->where(array(
                        "name" => "point_rate"
                    ))->getField("value");
                    
 
                    if ($is_integral == 1) {
                        // file_put_contents("./zZF.txt", '222'.'222', FILE_APPEND);
                        $userpoints = D('users')->where(array(
                            'user_id' => $user_id
                        ))->getField("points");
                    
                        //file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                        $user['points'] = $userpoints - $points_money * $point_rate;
                        D('users')->where(array(
                        'user_id' => $user_id
                        ))->save($user);
                        // file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                        $data['use_points'] = $points_money * $point_rate;
                        $data['points_money'] = $points_money;
                    
                        $log = array();
                        $log['user_id'] = $user_id;
                        $log['order_sn'] = $res['order_sn'];
                        $log['pay_points'] = $data['use_points'];
                        $log['points'] = $user['points'];
                        $log['user_money'] = 0;
                        $log['frozen_money'] = 0;
                        $log['change_time'] = time();
                        $log['desc'] = '购买商品积分消费';
                        D('account_log')->add($log);
                    }
                    
                    
                    
                    
                } else {
                    $data['pay_status'] = 1;
                    $data['order_status'] = 1;
                    $data['pay_time'] = time();
                    $data['total_fee'] = $getData['total_fee'] / 100;
                    
                    
                    
                    //积分处理
                    $good = D('goods')->where(array(
                        "goods_id" => $res['good_id']
                    ))->find();
                    $is_integral = $res['is_integral'];
                    $user_id = $res['user_id'];

                    $pay = $res['total_amount'];
                    
                    $points_money = $pay - (int)$getData['total_fee'] / 100;
                    $point_rate = D("config")->where(array(
                        "name" => "point_rate"
                    ))->getField("value");
                    
                    
                    if ($is_integral == 1) {
                        // file_put_contents("./zZF.txt", '222'.'222', FILE_APPEND);
                        $userpoints = D('users')->where(array(
                            'user_id' => $user_id
                        ))->getField("points");
                    
                        //file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                        $user['points'] = $userpoints - $points_money * $point_rate;
                        D('users')->where(array(
                        'user_id' => $user_id
                        ))->save($user);
                        // file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                        $data['use_points'] = $points_money * $point_rate;
                        $data['points_money'] = $points_money;
                    
                        $log = array();
                        $log['user_id'] = $user_id;
                        $log['order_sn'] = $res['order_sn'];
                        $log['pay_points'] = $data['use_points'];
                        $log['points'] = $user['points'];
                        $log['user_money'] = 0;
                        $log['frozen_money'] = 0;
                        $log['change_time'] = time();
                        $log['desc'] = '购买商品积分消费';
                        D('account_log')->add($log);
                    }
                    
                    
                    // 添加消息记录
                    $good = D('goods')->where(array(
                        "goods_id" => $res['goods_id']
                    ))->find();
                    if ($res['market_price'] == 0)
                        $msg['description'] = sprintf("您购买的商品:%s已付款成功", $good['goods_name']);
                    else
                        $msg['description'] = sprintf("您购买的商品:%s预付款已支付", $good['goods_name']);
                    $msg['type'] = 0;
                    $msg['user_id'] = $res['user_id'];
                    $msg['user_type'] = 0;
                    $msg['time'] = time();
                    D('message')->add($msg);
                }
                
                D("order")->where(array(
                    "order_sn" => $osn
                ))->save($data);
            } else {
                $res = D("repairs")->where(array(
                    "order_no" => $osn
                ))->find();
                
                try {
                    require_once ("./src/JPush/JPush.php");
                    require_once ("./src/JPush/core/JPushException.php");
                    $client = new \JPush('9ffed5d6454fb6bd37777907', '5a68e64311c15a5b3eb3529f');
                    
                    $result = $client->push()
                        ->setPlatform('ios', 'android')
                        ->addAlias('13861292076')
                        ->setNotificationAlert('新的维修订单')
                        ->addAndroidNotification('Hi, 新的维修订单。', '新的维修订单', 1, array(
                        "type" => "6"
                    ))
                        ->addIosNotification('Hi, 新的维修订单。', 'tmp.mp3', '+1', true, 'iOS category', array(
                        "type" => "6"
                    ))
                        ->setMessage("msg content", 'msg title', 'type', array(
                        "type" => "6"
                    ))
                        ->setOptions(100000, 3600, null, true)
                        ->send();
                } catch (\APIRequestException $e) {} catch (\APIConnectionException $e) {}
                
                if ($res) {
                    if ($res['orderstate'] == - 1) {
                        $data['orderState'] = 0;
                        $data['pay_time'] = time();
                        $data['total_fee'] = $getData['total_fee'] / 100;
                        
                        $user_id = $res['user_id'];
                        
                        $user = D('users')->where(array(
                            "user_id" => $user_id
                        ))->find();
                        
                        
                        //积分处理
                        
                        $is_integral = $res['is_integral'];
                        $user_id = $res['user_id'];
                        
                        $pay = $res['ordermoney'];
                        
                        $points_money = $pay - $getData['total_fee'] / 100;
                        $point_rate = D("config")->where(array(
                            "name" => "point_rate"
                        ))->getField("value");
                        
                        
                        
                        
                        
                        if ($is_integral == 1) {
                            // file_put_contents("./zZF.txt", '222'.'222', FILE_APPEND);
                            $userpoints = D('users')->where(array(
                                'user_id' => $user_id
                            ))->getField("points");
                        
                            //file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                            $user['points'] = $userpoints - $points_money * $point_rate;
                            D('users')->where(array(
                            'user_id' => $user_id
                            ))->save($user);
                            // file_put_contents("./zZF.txt", '222'.$userpoints.'222', FILE_APPEND);
                            $data['use_points'] = $points_money * $point_rate;
                            $data['points_money'] = $points_money;
                        
                            $log = array();
                            $log['user_id'] = $user_id;
                            $log['order_sn'] = $res['order_no'];
                            $log['pay_points'] = $data['use_points'];
                            $log['points'] = $user['points'];
                            $log['user_money'] = 0;
                            $log['frozen_money'] = 0;
                            $log['change_time'] = time();
                            $log['desc'] = '维修积分消费';
                            D('account_log')->add($log);
                        }
                        
                        
                        
                        
                        
                        
                        if ($user['service_users']) {
                            /*
                             * $msg['repair_id'] = $res['orderid'];
                             * $msg['log_note'] = '支付维修订单';
                             * $msg['time'] = time();
                             * D("repairs_log")->add($msg);
                             *
                             * $msg['repair_id'] = $res['orderid'];
                             * $msg['log_note'] = '指派维修订单:' . '系统自动指派';
                             * $msg['user_id'] = $user['service_users'];
                             * $msg['time'] = time();
                             *
                             * D("repairs_log")->add($msg);
                             *
                             * $data['repair_id'] = $user['service_users'];
                             * $data['repairState'] = 1;
                             * $data['repair_time'] = time();
                             *
                             * //添加消息记录（服务商）
                             * if($res['type'] == 0)
                             * $good = D('goods')->where(array("goods_id"=>144))->find();
                             * else
                             * $good = D('goods')->where(array("goods_id"=>146))->find();
                             *
                             * $msg['description'] = sprintf("您接收到新的维修订单:%s",$good['goods_name']);
                             * $msg['type'] = 0;
                             * $msg['user_id'] = $user['service_users'];
                             * $msg['user_type'] = 1;
                             * $msg['time'] = time();
                             * D('message')->add($msg);
                             */
                        } else {
                            $msg['repair_id'] = $res['orderid'];
                            $msg['log_note'] = '支付维修订单';
                            $msg['time'] = time();
                            D("repairs_log")->add($msg);
                        }
                        
                        D("repairs")->where(array(
                            "order_no" => $osn
                        ))->save($data);
                        
                        // 添加消息记录
                        if ($res['type'] == 0)
                            $good = D('goods')->where(array(
                                "goods_id" => 144
                            ))->find();
                        else
                            $good = D('goods')->where(array(
                                "goods_id" => 146
                            ))->find();
                        
                        $msg['description'] = sprintf("您维修的:%s已付款成功", $good['goods_name']);
                        $msg['type'] = 1;
                        $msg['user_id'] = $res['user_id'];
                        $msg['user_type'] = 0;
                        $msg['time'] = time();
                        D('message')->add($msg);
                        
                        if ($user['service_users']) {
                            /*
                             * $user = D("service_users")->where(array(
                             * "id" => $user['service_users']
                             * ))->find();
                             * $mobile = $user['phone'];
                             *
                             * require_once ("./src/JPush/JPush.php");
                             *
                             * $client = new \JPush('9ffed5d6454fb6bd37777907', '5a68e64311c15a5b3eb3529f');
                             * $result = $client->push()
                             * ->setPlatform('ios', 'android')
                             * ->addAlias($mobile)
                             * ->setNotificationAlert('新的维修订单')
                             * ->addAndroidNotification('Hi, 新的维修订单。', '新的维修订单', 1, array(
                             * "type" => "2"
                             * ))
                             * ->addIosNotification('Hi, 新的维修订单。', '新的维修订单', '+1', true, 'iOS category', array(
                             * "type" => "2"
                             * ))
                             * ->setMessage("msg content", 'msg title', 'type', array(
                             * "type" => "2"
                             * ))
                             * ->setOptions(100000, 3600, null, true)
                             * ->send();
                             */
                        }
                    }
                }
            }
            echo 'success';
        } 

        else {
            
            return;
            $osn = trim($getData['out_trade_no']);
            
            file_put_contents("./wzfcancel.txt", json_encode($osn), FILE_APPEND);
            $res = D("order")->where(array(
                "order_sn" => $osn
            ))->find();
            
            if ($res && $res['pay_status'] == 0) {
                
                if ($res['otype'] == 1) {
                    D("order")->where(array(
                        "order_sn" => $osn
                    ))->delete();
                }
            }
        }
    }

    function commerce_quick_wechat_notify_submit1($order, $notify)
    {
        $flag = '';
        
        if ($order->status == checkout_complete || $order->status == completed) {
            echo 'The bill is finished.';
        }
        // Handle trade types of cases.
        switch ($notify[return_code]) {
            // Transaction successful.
            case SUCCESS:
                $flag = commerce_order_status_update($order, checkout_complete);
                break;
        }
        
        if ($flag) {
            commerce_checkout_complete($order);
            // header("Content-type: text/xml; charset=utf-8");
            require_once WechatAppPay . php;
            $WechatAppPay = new WxPayHelper();
            
            $return = array(
                return_code => SUCCESS,
                return_ok => OK
            );
            $xml = $WechatAppPay->arrayToXml($return);
            
            return $xml;
        } else {
            echo fail;
            die();
        }
    }

    function xmlstr_to_array($xmlstr)
    {
        libxml_disable_entity_loader(true);
        
        $xmlstring = simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $val = json_decode(json_encode($xmlstring),true);
        
        return $val;
    }

    function domnode_to_array($node)
    {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i ++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (! isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } elseif ($v) {
                        $output = (string) $v;
                    }
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }
}