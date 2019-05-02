<?php
namespace Mobile\Controller;

class MemberController extends MobileBaseController
{
    public $user_id = 0;
    public $user = array();

    /*
    * 初始化操作
    */
    public function _initialize()
    {
        parent::_initialize();
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

        $level_name = M('user_level')->where("level_id = '{$this->user['level']}'")->getField('level_name');

        $this->assign('level_name', $level_name);
    }

    public function index()
    {
        $level_list = M('user_level')->select();
        $this->assign('level_list', $level_list);
        $this->display();
    }

    public function level_upgrade(){
        echo 111;
        exit;
    }
}
