var adaptUILayout = (function () {
	var regulateScreen = (function () {
		var cache = {};
		var defSize = {width: window.screen.width,height: window.screen.height};
		var ver = window.navigator.appVersion;
		var s = window.orientation;
		var _ = null;
		var check = function (key) {return key.constructor == String ? ver.indexOf(key) > -1 : ver.test(key);};
		var add = function (name, key, size) {if (name && key)cache[name] = {key: key,size: size};};
		var del = function (name) {if (cache[name])delete cache[name];};
		var cal = function () {
			if (_ != null)return _;
			for (var name in cache) {if (check(cache[name].key)) { _ = cache[name].size;break;}}
			if (_ == null)_ = defSize;
			return _;
		};
		return {add: add,del: del,cal: cal,s: s};
	})();
	var adaptViewport=function(width){
		var flag=false;
		if(navigator.userAgent.match(/iPad/i)&&document.documentElement.clientWidth<=768)flag=true;
		if(!navigator.userAgent.match(/iPad/i)&&document.documentElement.clientWidth<=width)flag=true;
		return flag;
	};
	var adapt = function (uiWidth) {
		var deviceWidth,devicePixelRatio,targetDensitydpi,initialContent,head,viewport,ua;
		ua = navigator.userAgent.toLowerCase();
		isiOS = ua.indexOf('ipad') > -1 || ua.indexOf('iphone') > -1;
		devicePixelRatio = window.devicePixelRatio;
		devicePixelRatio < 1.5 ? 2 : devicePixelRatio;
		if (window.orientation == 0 || window.orientation == 180) {
			if (regulateScreen.s != 0) {
				if (regulateScreen.cal().width < regulateScreen.cal().height) {
					deviceWidth = regulateScreen.cal().width;
				} else {
					deviceWidth = regulateScreen.cal().height;
				}
			} else {
				deviceWidth = regulateScreen.cal().width;
			}
		} else {
			if (regulateScreen.s != 0) {
				if (regulateScreen.cal().width > regulateScreen.cal().height) {
					deviceWidth = regulateScreen.cal().width;
				} else {
					deviceWidth = regulateScreen.cal().height;
				}
			} else {
				deviceWidth = regulateScreen.cal().height;
			}
		}
		if (devicePixelRatio == 2 && (deviceWidth == 320 || deviceWidth == 360 || deviceWidth == 592 || deviceWidth == 640)) { deviceWidth *= 2;};
		if (devicePixelRatio == 1.5 && (deviceWidth == 320)) {deviceWidth *= 2;devicePixelRatio = 2;};
		if (devicePixelRatio == 1.5 && (deviceWidth == 640)) {devicePixelRatio = 2;};
		targetDensitydpi = uiWidth / deviceWidth * devicePixelRatio * 160;
		initialContent = isiOS ? 'width=' + uiWidth + 'px' + ', user-scalable=no': 'target-densitydpi=' + targetDensitydpi + ', width=' + uiWidth + ', user-scalable=no';
		meta=meta=document.getElementsByName("viewport")[0];
		meta.content = initialContent;
	};
	return {regulateScreen: regulateScreen,adapt: adapt,viewport:adaptViewport};
})(); 
var isViewport=adaptUILayout.viewport(750);
if(isViewport){
    adaptUILayout.adapt(750);
    $(window).bind('orientationchange', function (e) {adaptUILayout.adapt(750);});
}
$(function(){$("body").addClass('show');try{viewport(adaptUILayout.regulateScreen.cal())}catch(e){}});
