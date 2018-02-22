/**
 * TIsNull		表示判断字段是否为空，如果为空，返回true
 * @param 	string	nameId	元素名
 * @param	string errorProId	错误提示的id
 * @param 	string	info		错误提示
 * @returns {Boolean}			true/false
 */
function TIsNull(nameId, errorProId, info) {
	var str = document.getElementById(nameId);

	if (str.value == "") {
		$("#" + errorProId).html(info + "不能为空!!!").show();
		return true;
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
			$("#" + errorProId).html(info + "不能为空!!!").show();
			return true;
		}
	}

	if(str.value == ""){
		return false;
	}

	if (T_CountCharacters(str.value) < min) {
		$("#" + errorProId).html(info + "不得少于" + min + "个字符!!!").show();
		return true;
	}
	if (T_CountCharacters(str.value) > max) {
		$("#" + errorProId).html(info + "不得超过" + max + "个字符!!!").show();
		return true;
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
		$("#" + errorProId).html(info + "太简单!!!").show();
		return true;
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
		$("#" + errorProId).html(info + "不得包含中文!!!").show();
		return true;
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
		$("#" + errorProId).html(info + "不得包含特殊字符!!!").show();
		return true;
	}

	return false;
}