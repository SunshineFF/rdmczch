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
        if ($currentData[self::COUNT] == 10){
            return true;
        }
        $data[self::UPDATED_AT] = time();
        $data[self::COUNT] = $currentData[self::COUNT] + 1;
    }

    protected function getTodayTotalByUserOnce($user){
        if (!$user['tou_zi']){
            return 0;
        }
        $rule = $this->getRuleByUser($user);
        $money = round($user/$rule/10,2);
        return $money;
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

    /** 获取返还金额天数
     * @param $user
     * @return bool|int
     */
    public function getRuleByUser($user){
        $touZi = $user['touzi'];
        if (!$touZi){
            return false;
        }
        $rule = [];
        if($touZi >= 500 && $touZi < 5000){
            $rule['max'] = 2;
            $rule['max'] = 2;
            return 250;
        }elseif($touZi >= 5000 && $touZi < 10000){
            return 220;
        }elseif($touZi >= 10000 && $touZi < 50000){
            return 200;
        }elseif($touZi >= 50000 && $touZi < 100000){
            return 180;
        }elseif($touZi >= 100000 && $touZi < 500000){
            return 150;
        }elseif($touZi >= 100000 && $touZi < 500000){
            return 120;
        }elseif($touZi >= 1000000){
            return 100;
        }else{
            return false;
        }
    }
}