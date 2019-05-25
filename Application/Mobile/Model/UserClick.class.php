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

    /** 初始化类
     * @return $this
     */
    public function initFunction(){
        if (!$this->userLoginc){
            $this->userLoginc = new UsersLogic();
        }
        return $this;
    }

    /** 根据点击量返还金额
     * @param $user
     * @return bool
     */
    public function updateDataByUser($user){
        $this->initFunction();
        $data = $this->getCurrentData($user['user_id']);
        if ($user['tou_zi'] == $user['tou_zi_return']){
            return true;
        }
        if ($data[self::COUNT] == 10){
            return true;
        }
        $todayMoney = $this->getTodayTotalByUserOnce($user);
        if (!$todayMoney){
            return true;
        }
        if (($user['tou_zi_return'] + $todayMoney) > $user['tou_zi']){
            $todayMoney = $todayMoney - ($user['tou_zi_return'] + $todayMoney - $user['tou_zi']);
        }
        $data[self::UPDATED_AT] = time();
        $data[self::COUNT] += 1;
        $data[self::TOTAL] += $todayMoney;
        try{
            $this->startTrans();
            if ($data[self::COUNT] == 1){
                $this->add($data);
            }else{
                $this->save($data);
            }
            $data['tou_zi_return'] = ($user['tou_zi_return'] + $todayMoney);
            $data['user_money'] = ($user['user_money'] + $todayMoney);
            M('users')->where(['user_id' => $user['user_id']])->save($data);
            accountLogOnly($user['user_id'],$todayMoney,'点击返还金额');
            $this->commit();
            return true;
        }catch (\Exception $exception){
            $this->rollback();
            \Think\Log::write($exception->getMessage().$exception->getTraceAsString(),'WARN');
            return false;
        }
        return false;
    }

    /** 根据用户投资额 获取当天返还的金额
     * @param $user
     * @return float|int
     */
    protected function getTodayTotalByUserOnce($user){
        if (!$user['tou_zi']){
            return 0;
        }
        $rule = $this->getRuleByUser($user);
        if (!$rule){
            return 0;
        }
        $money = round($user['tou_zi']/$rule/10,2);
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
        $touZi = $user['tou_zi'];
        if (!$touZi){
            return false;
        }
        $rule = [];
        if($touZi >= 500 && $touZi < 5000){
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