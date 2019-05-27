<?php
namespace Mobile\Logic;

use Think\Image;

class ImageHelper{
    protected $imageClass;

    protected $imageDir = 'Public/images/cache/';

    public function __construct()
    {
        $this->imageClass = new Image();
    }


    public function mergeImageFromQRcode($image1,$backImage){
        var_dump(getcwd());
        exit;
        $image1 = $this->smallImage($image1);
        var_dump($image1);
        exit;
    }

    public function smallImage($image,$width = 206,$height = 206){
//        $avatarUrl = '\qrcode.png';
        //1、将微信二维码缩小至206*206，默认是430
        $thumb = imagecreatetruecolor(206,206);     //创建一个新的画布（缩放后的），从左上角开始填充透明背景
        $img_content = imagecreatefromjpeg(getcwd().$image);//获取图片资源
        $widthFrom = imagesx($image);
        $heightFrom = imagesy($image);
        imagecopyresampled($thumb, $img_content, 0, 0, 0, 0, $width, $height, $widthFrom, $heightFrom);//核心函数，改变图片大小
        $file_name = "\qrcode_1.png";
        imagepng($thumb,getcwd().$file_name);//将图片资源保存到qrcode_1.png中
    }

}