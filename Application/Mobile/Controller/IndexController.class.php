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
 * $Author: 当燃 2016-01-09
 */
namespace Mobile\Controller;

use Mobile\Model\StoreModel;

class IndexController extends MobileBaseController {

    /** 首页群主店铺产品ID
     * @var array
     */
    protected $sellerProducts = [250,249,247,287];

    /** 抢购产品ID
     * @var array
     */
    protected $qiangGou = [262,246,264,211,212,286];

    /** 首页工厂直销ID
     * @var array
     */
    protected $zhixiao = [268,265,263,252,251,243,242,222,221,199,168,201,260,288,278,283,281,270,279,295,291,277,284];
    /** 新品预售ID
     * @var array
     */
    protected $xinpin = [179,190,173];

    public function index(){
        /*
            //获取微信配置
            $wechat_list = M('wx_user')->select();
            $wechat_config = $wechat_list[0];
            $this->weixin_config = $wechat_config;
            // 微信Jssdk 操作类 用分享朋友圈 JS
            $jssdk = new \Mobile\Logic\Jssdk($this->weixin_config['appid'], $this->weixin_config['appsecret']);
            $signPackage = $jssdk->GetSignPackage();
            print_r($signPackage);
        */
//        $hot_goods = M('goods')->where("is_hot=1 and is_on_sale=1")->order('goods_id DESC')->limit(20)->cache(true,TPSHOP_CACHE_TIME)->select();//首页热卖商品
        $hot_goods = M('goods')->where(['goods_id' => ['in',$this->zhixiao]])->order('goods_id DESC')->limit(20)->cache(true,TPSHOP_CACHE_TIME)->select();//首页工厂直销
        $group_store = M('goods')->where(['goods_id' => ['in',$this->sellerProducts]])->order('goods_id DESC')->limit(20)->cache(true,TPSHOP_CACHE_TIME)->select();//首页店铺展示产品
//        var_dump($hot_goods);
        $flash_sale= M('goods')->where(['goods_id' => ['in',$this->qiangGou]])->order('goods_id DESC')->limit(20)->cache(true,TPSHOP_CACHE_TIME)->select();//首页限时抢购
        $pre_sale= M('goods')->where(['goods_id' => ['in',$this->xinpin]])->order('goods_id DESC')->limit(20)->cache(true,TPSHOP_CACHE_TIME)->select();//新品预售
        $thems = M('goods_category')->where('level=1')->order('sort_order')->limit(9)->cache(true,TPSHOP_CACHE_TIME)->select();
        $this->assign('thems',$thems);
        $this->assign('group_store',$group_store);
        $this->assign('pre_sale',$pre_sale);
        $this->assign('flash_sale',$flash_sale);
        $this->assign('hot_goods',$hot_goods);
        $this->assign('store_banner',$this->getStoreFromCurrentUser());
//        $favourite_goods = M('goods')->where("is_recommend=1 and is_on_sale=1")->order('goods_id DESC')->limit(20)->cache(true,TPSHOP_CACHE_TIME)->select();//首页推荐商品
//        $this->assign('favourite_goods',$favourite_goods);
        $this->display();
    }

    /** 初始化群主店铺的banner
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getStoreFromCurrentUser(){
        $default = [];
        if (session('?user')) {
            $user = session('user');
            $store = M('store')->where(['user_id' => $user['qun_id']])->find();
        }else{
            $store = M('store')->where(['user_id' => 21])->find();
        }
        if (is_array($store) && $store['mb_slide']){
            $default['image'] = explode(',',$store['mb_slide']);
            $default['url'] = explode(',',$store['mb_slide_url']);
            return $default;
        }else{
            return [
                'image' => [
                    '/Template/mobile/new/Static/img/index/20190529110711.png',
                    '/Template/mobile/new/Static/img/index/20190529110711.png',
                    '/Template/mobile/new/Static/img/index/20190529110711.png'
                ],
                'url' => [
                    '/Store/index/store_id/21.html',
                    '/Store/index/store_id/21.html',
                    '/Store/index/store_id/21.html',
                ]
            ];
        }
    }

    /**
     * 分类列表显示
     */
    public function categoryList(){
        $this->display();
    }

    /**
     * 模板列表
     */
    public function mobanlist(){
        $arr = glob("D:/wamp/www/svn_tpshop/mobile--html/*.html");
        foreach($arr as $key => $val)
        {
            $html = end(explode('/', $val));
            echo "<a href='http://www.php.com/svn_tpshop/mobile--html/{$html}' target='_blank'>{$html}</a> <br/>";
        }
    }

    /**
     * 商品列表页
     */
    public function goodsList(){
        $goodsLogic = new \Home\Logic\GoodsLogic(); // 前台商品操作逻辑类
        $id = I('get.id',0); // 当前分类id
        $lists = getCatGrandson($id);
        $this->assign('lists',$lists);
        $this->display();
    }

    public function ajaxGetMore(){
        $p = I('p',1);
        $favourite_goods = M('goods')->where("is_recommend=1 and is_on_sale=1  and goods_state = 1 ")->order('sort DESC')->page($p,10)->cache(true,TPSHOP_CACHE_TIME)->select();//首页推荐商品
        $this->assign('favourite_goods',$favourite_goods);
        $this->display();
    }

    /**
     * 店铺街
     * @author dyr
     * @time 2016/08/15
     */
    public function street()
    {
        $store_class = M('store_class')->where('')->select();
        $this->assign('store_class', $store_class);//店铺分类
        $this->display();
    }

    /**
     * ajax 获取店铺街
     */
    public function ajaxStreetList()
    {
        $p = I('p',1);
        $sc_id = I('get.sc_id','');
        $store_list = D('store')->getStreetList($sc_id,$p,10);
        foreach($store_list as $key=>$value){
            $store_list[$key]['goods_array'] = D('store')->getStoreGoods($value['store_id'],4);
        }
        $this->assign('store_list',$store_list);
        $this->display();
    }

    /**
     * 品牌街
     * @author dyr
     * @time 2016/08/15
     */
    public function brand()
    {
        $brand_model = M('brand');
        $brand_where['status'] = 0;
        $brand_class = $brand_model->field('cat_name')->group('cat_name')->order(array('sort'=>'desc','id'=>'asc'))->where($brand_where)->select();
        $brand_list = $brand_model->field('id,name,logo,url')->order(array('sort'=>'desc','id'=>'asc'))->where($brand_where)->select();
        $brand_count = count($brand_list);
        for ($i = 0; $i < $brand_count; $i++) {
            if (($i + 1) % 4 == 0) {
                $brand_list[$i]['brandLink'] = 'brandRightLink';
            } else {
                $brand_list[$i]['brandLink'] = 'brandLink';
            }
        }
        $this->assign('brand_list', $brand_list);//品牌列表
        $this->assign('brand_class', $brand_class);//品牌分类
        $this->display();
    }
}