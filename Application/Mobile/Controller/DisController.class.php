<?php

namespace Mobile\Controller;

class DisController extends MobileBaseController
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
}