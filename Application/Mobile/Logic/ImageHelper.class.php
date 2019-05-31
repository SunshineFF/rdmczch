<?php
namespace Mobile\Logic;

use Think\Image;

class ImageHelper{
    protected $imageClass;

    protected $QRcodeHelp;

    protected $imageDir = 'Public/images/cache/';

    public function __construct()
    {
        $this->imageClass = new Image();
        $this->QRcodeHelp = new QRcodeHelp();
    }

    public function mergeImageFromQRcode($image1,$backImage,$width,$x = 0,$y = 0){
        $root = str_replace('\\','/',getcwd().'/');
        $image1 = $root.$image1;
        $imageCache = $this->ImgCompress($image1,$root,$width);
        $backImage = $root.$backImage;
        $image_1 = imagecreatefromjpeg($imageCache);
        $image_2 = imagecreatefrompng($backImage);
        imagecopymerge($image_2, $image_1, $x, $y, 0, 0, imagesx($image_1), imagesy($image_1), 100);
        // 输出合成图片
        //imagepng($image[,$filename]) — 以 PNG 格式将图像输出到浏览器或文件
        $merge = $root.$this->QRcodeHelp->getTodayDir(true).'/'.time().'.png';
        imagepng($image_2,$merge);
        return $merge;
    }



    /**
     * @param $src  string  图片路径
     * @param $root string  项目绝对路径
     * @param int $out_with  输出的宽度
     * @return string
     */
    public function ImgCompress($src, $root,$out_with = 150)
    {
        // 获取图片基本信息
        list($width, $height, $type, $attr) = getimagesize($src);
        // 获取图片后缀名
        $pic_type = image_type_to_extension($type, false);
        // 拼接方法
        $imagecreatefrom = "imagecreatefrom" . $pic_type;
        // 打开传入的图片
        $in_pic = $imagecreatefrom($src);
        // 压缩后的图片长宽
        $new_width = $out_with;
        $new_height = $out_with / $width * $height;
        // 生成中间图片
        $temp = imagecreatetruecolor($new_width, $new_height);
        // 图片按比例合并在一起。
        imagecopyresampled($temp, $in_pic, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        // 销毁输入图片
        $dir = $root.$this->imageDir;
        if (!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $fileName = $root.$this->imageDir.time().".jpg";
        imagejpeg($temp, $fileName);
        imagedestroy($in_pic);
        return $fileName;
    }




}