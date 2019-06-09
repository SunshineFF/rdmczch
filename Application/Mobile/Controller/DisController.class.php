<?php

namespace Mobile\Controller;

use Home\Logic\UsersLogic;
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
        $data = $this->initDataFromUser($this->user);
        $this->assign('data',json_encode($data));
        return $this->display();
    }

    /** 初始化用户数据
     * @param $user
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    protected function initDataFromUser($user){
        if (!$user['parent_path'] && !$user['all_child']){
            return [];
        }
        if($user['parent_path']){
            $firstParentId = current(explode(',',$user['parent_path']));
            $userModel = M('users');
            $firstParent = $userModel->where(['user_id' => $firstParentId])->find();
        }else{
            $firstParent = $user;
        }
        $dataNew = [];
        $allChild = unserialize($firstParent['all_child']);
        $current = $user['user_id'];
        $currentLevel = [];
        for ($i = 0;$i < count($allChild) ; $i++){
            if (!isset($allChild[$current])){
                if (count($currentLevel) == 0)break;
                $key = array_keys($currentLevel)[0];
                $current = $currentLevel[$key];
                unset($currentLevel[$key]);
                continue;
            }
            $one = $allChild[$current];
            if (isset($one[2])){
                $dataNew[] = $one[2];
                $dataNew[] = $one[1];
                $currentLevel[] = $one[1];
                $currentLevel[] = $one[2];
            }else{
                $dataNew[] = $one[1];
                $currentLevel[] = $one[1];
            }
            if (count($currentLevel) > 0){
                $key = array_keys($currentLevel)[0];
                $current = $currentLevel[$key];
                unset($currentLevel[$key]);
            }
        }
        if (count($dataNew) == 0){
            return [];
        }
        $data = M('users')->query("select user_id id,parent_id pId,mobile name from ty_users where user_id in (".array_to_string($dataNew).") order by user_id desc");
        $one = ['id'=>$user['user_id'],'pid'=>0,'name' => $user['mobile'],'isParent'=>true];
        $data[] = $one;
        arsort($data);
        $dataNew = [];
        foreach ($data as $one){
            $one['pId'] = $one['pid'];
            unset($one['pid']);
            $dataNew[] = $one;
        }
        return $dataNew;
    }

}

function array_to_string($array){
    $string = '';
    $string .= "'";
    $string .= implode("','",$array);
    $string .= "'";
    return $string;
}