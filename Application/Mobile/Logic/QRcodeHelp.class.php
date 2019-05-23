<?php
namespace Mobile\Logic;

use think\image\Exception;
use ThinkPHP\Library\Vendor\phpqrcode;
include_once "ThinkPHP/Library/Vendor/phpqrcode/phpqrcode.php";

class QRcodeHelp{
    /**
     * 默认容错级别
     */
    const DEFULT_CorrectionLevel = 'L';
    /**
     * 默认图片大小
     */
    const DEFAULT_MatrixPointSize = 6;

    public function getPng($content,$errorCorrectionLevel = self::DEFULT_CorrectionLevel,$matrixPointSize = self::DEFAULT_MatrixPointSize){
        //生成二维码图片
        $imgName = $this->getTodayDir().'/';
        $imgName .= time().'.png';
        phpqrcode\QRcode::png($content, $imgName, $errorCorrectionLevel, $matrixPointSize, 2);
        return $imgName;
    }

    public function getTodayDir(){
        $time = time();
        $dir = UPLOAD_PATH;
        $dir .= date('Y_m',$time);
        $dir .= '/'.date('d',$time);
        if (!$this->mkdirs($dir)){
            throw new Exception("创建文件 $dir 失败");
        };
        return $dir;
    }

    function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode,true)) return TRUE;
        return false;
    }
}