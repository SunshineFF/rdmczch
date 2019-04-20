var server = require('../../utils/server');
var QQMapWX = require('../../utils/qqmap-wx-jssdk.js');
var server = require('../../utils/server');
var seat;
var isLoc = false;
Page({
	data: {
		"address":"定位中",
		banner: [],
		goods: [],
		bannerHeight: Math.ceil(290.0 / 750.0 * getApp().screenWidth)
	},
	showCoupon: function (e) {
		wx.navigateTo({
		  url: '../member/coupon/index',
		  success: function(res){
			// success
		  },
		  fail: function() {
			// fail
		  },
		  complete: function() {
			// complete
		  }
		})
	},
	showOrder: function (e) {
		wx.navigateTo({
		  url: '../order/list/list',
		  success: function(res){
			// success
		  },
		  fail: function() {
			// fail
		  },
		  complete: function() {
			// complete
		  }
		})
	},
	showCollect: function (e) {
		wx.navigateTo({
		  url: '../member/collect/collect',
		  success: function(res){
			// success
		  },
		  fail: function() {
			// fail
		  },
		  complete: function() {
			// complete
		  }
		})
	},
	showMine: function (e) {
		wx.switchTab({
			url: "../member/index/index"
		});
	},
	showSeller: function (e) {
		wx.navigateTo({
		  url: '../seller/index',
		  success: function(res){
			// success
		  },
		  fail: function() {
			// fail
		  },
		  complete: function() {
			// complete
		  }
		})
	},
	search: function (e) {
		wx.navigateTo({
			url: "../search/index"
		});
	},
	showCarts: function (e) {
		wx.switchTab({
			url: "../cart/cart"
		});
	},
	onLoad: function (options) {
		//seat = options.seat;
		//wx.showToast({title:seat+"seat"});
		//
		//this.loadMainGoods();
		this.getInviteCode(options);

		var app = getApp();
		app.getOpenId(function () {

			var openId = getApp().globalData.openid;

            server.getJSON("/User/validateOpenid",{openid:openId},function(res){

				if (res.data.code == 200) {
						getApp().globalData.userInfo = res.data.data;
						getApp().globalData.login = true;
						//wx.switchTab({
						//url: '/pages/index/index'
						//});
					}
					else{
						if (res.data.code == '400') {
						console.log("need register");

						app.register(function () {

                           getApp().globalData.login = true;
						});
					}
					}

			});

		});	
	},
	getInviteCode: function (options) {
		if (options.uid != undefined) {
			wx.showToast({
				title: '来自用户:' + options.uid + '的分享',
				icon: 'success',
				duration: 2000
			})
		}
	},
	
	loadBanner: function () {
		var that = this;
   var city = that.data.address;
	 city = encodeURI(city);
        server.getJSON("/Index/home",{city:that.data.address},function(res){
var banner = res.data.result.ad;
				var goods = res.data.result.goods;
				var ad = res.data.ad;
				that.setData({
					banner: banner,
					goods: goods,
					ad: ad
				});
		});

		
		
	},
	loadMainGoods: function () {
		var that = this;
		var query = new AV.Query('Goods');
		query.equalTo('isHot', true);
		query.find().then(function (goodsObjects) {
			that.setData({
				goods: goodsObjects
			});
		});
	},
	onShow:function(){
var app = getApp();
		var self = this;

		if (isLoc) {
var address = getApp().globalData.city;
this.setData({address:address});
self.loadBanner();
return;
		}
		wx.getLocation({
			type: 'gcj02',
			success: function (res) {
				var latitude = res.latitude;
				var longitude = res.longitude;

				app.globalData.lat = latitude;
				app.globalData.lng = longitude;

				// 实例划API核心类
				var map = new QQMapWX({
					key: 'LAWBZ-2CHCD-MCK4X-PSTUA-NJZJJ-IHFQ2' // 必填
				});
				////address: res.result.address_component.city
				// 调用接口
				map.reverseGeocoder({
					location: {
						latitude: latitude,
						longitude: longitude
					},
					success: function (res) {
						console.log(res);

						if (res.result.ad_info.city != undefined) {
							self.setData({

								address: res.result.ad_info.city
							});
getApp().globalData.city = res.result.ad_info.city;
							isLoc = true;
							self.loadBanner();
						}
					},
					fail: function (res) {
						console.log(res);
					},
					complete: function (res) {
						console.log(res);
					}
				});
			}
		})



	},
	clickBanner: function (e) {

		var goodsId = e.currentTarget.dataset.goodsId;
		wx.navigateTo({
			url: "../goods/detail/detail?objectId=" + goodsId
		});
	},
	clickBanner: function (e) {

		var goodsId = e.currentTarget.dataset.goodsId;
		wx.navigateTo({
			url: "../goods/detail/detail?objectId=" + goodsId
		});
	},
	showDetail: function (e) {
		var goodsId = e.currentTarget.dataset.goodsId;
		wx.navigateTo({
			url: "../goods/detail/detail?objectId=" + goodsId
		});
	},
	showCategories: function () {
		// wx.navigateTo({
		// 	url: "../category/category"
		// });
		wx.switchTab({
			url: "../category/category"
		});
	},
	showGroupList: function () {
		wx.navigateTo({
			url: "../goods/grouplist/list"
		});
	},
	onShareAppMessage: function () {
		return {
			title: '吕氏电商系统',
			desc: '一个基于tpshop开发的开源电商系统',
			path: '/pages/index/index?uid=4719784'
		}
	},
	select:function(){
		wx.navigateTo({
			url: '../switchcity/switchcity',
			success: function(res){
				// success
			},
			fail: function(res) {
				// fail
			},
			complete: function(res) {
				// complete
			}
		})
	}
})