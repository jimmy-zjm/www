/**
 * TIsNull		表示判断字段是否为空，如果为空，返回true
 * @param 	string	nameId	元素名
 * @param	string errorProId	错误提示的id
 * @param 	string	info		错误提示
 * @returns {Boolean}			true/false
 */
function T_IsNull(nameId, errorProId, info) {
	var str = document.getElementById(nameId);

	if (str.value == "") {
		$("#" + errorProId).html(info + "不能为空!").show();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	return false;
}


/**
 * T_InBetweenLenEn		表示判断字段是否在设定的长度之间，是否可以为空（字符提示）
 * @param 	string	nameId	元素名
 * @param	string	errorProId	错误提示控件Id
 * @param 	string	info		错误提示
 * @param 	int		max· 设置的最大长度
 * @param 	int		min· 设置的最大长度
 * @param 	Boolean	isnull	是否为空
 * @returns {Boolean}			true/false
 */
function T_InBetweenLenEn(name, errorProId, info, min, max, isnull) {
	var str = document.getElementById(name);

	if (!isnull) {
		if (str.value == "") {
			$("#" + errorProId).html(info + "不能为空!").show();
			str.focus();
			return true;
		}else{
			$("#" + errorProId).html("").hide();
		}
	}

	if(str.value == ""){
		$("#" + errorProId).html("").hide();
		return false;
	}

	if (T_CountCharacters(str.value) < min) {
		$("#" + errorProId).html(info + "不得少于" + min + "个字符!").show();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	if (T_CountCharacters(str.value) > max) {
		$("#" + errorProId).html(info + "不得超过" + max + "个字符!").show();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
		return false;
	}

	return false;
}


/**
 * T_InBetweenLenCh		表示判断字段是否在设定的长度之间，是否可以为空（中文）
 * @param 	string	nameId	元素名
 * @param	string	errorProId	错误提示控件Id
 * @param 	string	info		错误提示
 * @param 	int		max· 设置的最大长度
 * @param 	int		min· 设置的最大长度
 * @param 	Boolean	isnull	是否为空
 * @returns {Boolean}			true/false
 */
function T_InBetweenLenCh(nameId, errorProId, info, min, max, isnull) {
	var str = document.getElementById(nameId);

	if (!isnull) {
		if (str.value == "") {
			$("#" + errorProId).html(info + "不能为空!").show();
			str.focus();
			return true;
		}else{
			$("#" + errorProId).html("").hide();
		}
	}

	if(str.value == ""){
		$("#" + errorProId).html("").hide();
		return false;
	}

	if (T_CountCharacters(str.value) < min) {
		$("#" + errorProId).html(info + "不得少于" + (min / 2) + "个汉字!").show();
		str.focus();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	if (T_CountCharacters(str.value) > max) {
		$("#" + errorProId).html(info + "不得超过" + (max / 2) + "个汉字!").show();
		str.focus();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	return false;
}


/**
 * T_CountCharacters		表示计算字符个数，包括中文计算2个字符
 * @param		string	 str		需计算的字符串
 * @returns 	int		totalCount	计算结果
 */
function T_CountCharacters(str) {
	var totalCount = 0;

	for (var i = 0; i < str.length; i++) {
		var c = str.charCodeAt(i);
		if ((c >= 0x0001 && c <= 0x007e) || (0xff60 <= c && c <= 0xff9f)) {
			totalCount++;
		} else {
			totalCount += 2;
		}
	}

	return totalCount;
}


/**
 * T_IsEnOrNum		表示判断字段是否只有英文或数字
 * @param 	string	nameId	元素名
 * @param	string errorProId	错误提示的id
 * @param 	string	info		错误提示
 * @returns {Boolean}			true/false
 */
function T_IsEnOrNum(nameId, errorProId, info) {
	var str = document.getElementById(nameId);
	var regEnAndNum = /^(?=.*[a-zA-Z]+)(?=.*[0-9]+)[a-zA-Z0-9]+$/;

	if(!regEnAndNum.test(str.value)){
		$("#" + errorProId).html(info + "太简单!").show();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	return false;
}


/**
 * T_IsContainCh		表示判断字段是否包含中文
 * @param 	string	nameId	元素名
 * @param	string errorProId	错误提示的id
 * @param 	string	info		错误提示
 * @returns {Boolean}			true/false
 */
function T_IsContainCh(nameId, errorProId, info) {
	var str = document.getElementById(nameId);
	var regCh = /[\u4E00-\u9FA5]/;
	if (regCh.test(str.value)) {
		$("#" + errorProId).html(info + "不得包含中文!").show();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	return false;
}


/**
 *T_IsEqual					表示比较是不是相等
 * @param 	string	nameId		第一个元素名
 * @param 	string	nameConfirmId	第二个元素名
 * @param 	string	errorProId	错误提示的id
 * @param 	string	info	错误提示
 * @returns {boolean}	true/false
 */
function T_IsEqual(nameId, nameConfirmId) {

	var str = document.getElementById(nameId).value;
	var strConfirm = document.getElementById(nameConfirmId).value;

	if(str != strConfirm){
		$("#passWordConfirmErrorDivId").show();
		return true;
	}else{
		$("#passWordConfirmErrorDivId").html("").hide();
	}
	return false;

}

/**
 * T_IsSpecialChar	表示判断字符串当中是否包含特殊字符
 *@param 	string	nameId	元素名
 * @param	string errorProId	错误提示的id
 * @param 	string	info		错误提示
 * @returns {Boolean}			true/false
 */
function T_IsSpecialChar(nameId, errorProId, info){
	var str = document.getElementById(nameId);
	var regCh = /[\ \<\>\、\!\@\#\$\%\^\&\*\(\)\`)]/;
	if (regCh.test(str.value)) {
		$("#" + errorProId).html(info + "不得包含特殊字符!").show();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	return false;
}


/**
 * T_IsMobile	表示判断字段是否为有效的手机号码，是否可以为空(true表示可以为空)
 * @param string nameId		元素名
 * @param string errorProId		错误提示的id
 * @param Boolean isnull	是否为空
 * @returns {boolean}	true/false
 */
function T_IsMobile(nameId, errorProId, isnull) {
	var mobile = document.getElementById(nameId);

	if (!isnull) {
		if (mobile.value == "") {
			$("#" + errorProId).html("请填写手机号码!").show();
			mobile.focus();

			return true;
		}else{
			$("#" + errorProId).html("").hide();
		}
	}

	if(mobile.value==""){
		$("#" + errorProId).html("").hide();
		return false;
	}

	if (isNaN(mobile.value) || (mobile.value.length != 11)) {
		$("#" + errorProId).html("手机号码为11位数字!").show();
		mobile.focus();

		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	var reg = /^[1][3-8]+\d{9}/;
	if (!reg.test(mobile.value)) {
		$("#" + errorProId).html("请输入正确的手机号码!").show();
		mobile.focus();

		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	return false;
}


/**
 * T_IsTel		表示判断字段是否为有效的电话号码，是否可以为空(true表示可以为空)
 * @param string nameId	元素名
 * @param string errorProId	错误提示的id
 * @param Boolean isnull	是否为空
 * @returns {boolean}	true/false
 */
function T_IsTel(nameId, errorProId, isnull){
	var tel = document.getElementById(nameId);

	if (!isnull) {
		if (tel.value == "") {
			$("#" + errorProId).html("请填写电话号码!").show();
			tel.focus();
			return true;
		}else{
			$("#" + errorProId).html("").hide();
		}
	}

	if(tel.value==""){
		$("#" + errorProId).html("").hide();
		return false;
	}

	var reg = /^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/;
	if (!reg.test(tel.value)) {
		$("#" + errorProId).html("电话格式为 '区号-直播号-分机号'").show();
		tel.focus();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	return false;
}


/**
 * T_IsEmail		表示判断字段是否为有效的邮箱，是否可以为空(true表示可以为空)
 * @param 	string	name	元素名
 * @param	string	errorProId	错误提示的id
 * @param 	Boolean	isnull	是否为空
 * @returns {Boolean}			true/false
 */
function T_IsEmail(nameId, errorProId, isnull) {
	var email = document.getElementById(nameId);

	//表示不能为空
	if (!isnull) {
		if (email.value == "") {
			$("#" + errorProId).html("请填写Email邮箱!").show();
			email.focus();
			return true;
		}else{
			$("#" + errorProId).html("").hide();
		}
	}

	//表示可以为空
	if(email.value == ""){
		$("#" + errorProId).html("").hide();
		return false;
	}

	if (!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email.value)) {
		$("#" + errorProId).html("请填写正确的Email邮箱!").show();
		email.focus();
		return true;
	}else{
		$("#" + errorProId).html("").hide();
	}

	return false;
}