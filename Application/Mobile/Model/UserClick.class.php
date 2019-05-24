<?php

namespace Mobile\Model;

use Home\Logic\UsersLogic;

class UserClick extends \Think\Model
{
    const USER_ID = 'user_id';
    const COUNT = 'count';  //点击量
    const TOTAL = 'total';  //返还额度
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $tableName = 'user_click';

    protected $userLoginc;

    public function __construct(
        $name = '', $tablePrefix = '', $connection = '')
    {
        parent::__construct($name, $tablePrefix, $connection);
    }

    public function initFunction(){
        if (!$this->userLoginc){
            $this->userLoginc = new UsersLogic();
        }
        return $this;
    }

    public function updateDataByUser($user){
        $this->initFunction();
        $currentData = $this->getCurrentData($user['user_id']);
        $data[self::UPDATED_AT] = time();
        $data[self::COUNT] = $currentData[self::COUNT] + 1;
    }

    protected function getCurrentData($userId){
        $time = strtotime(date('Ymd',time()));
        $data = $this->where([self::USER_ID => $userId,self::CREATED_AT =>$time])->find();
        if (!$data){
            return [
                self::USER_ID => $userId,
                self::CREATED_AT => $time,
                self::COUNT => 0,
                self::TOTAL => 0
            ];
        }else{
            return $data;
        }
    }
}