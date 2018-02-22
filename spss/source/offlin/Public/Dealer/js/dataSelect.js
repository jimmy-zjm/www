  $("#birthday").focus(function(){
	  	 document.activeElement.blur();
	  });


var today=getToday();
function getToday(){
	var d=new Date();
	return d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
}
		var calendar = new datePicker();
		calendar.init({
			'trigger': '#birthday', /*按钮选择器，用于触发弹出插件*/
			'type': 'date',/*模式：date日期；datetime日期时间；time时间；ym年月；*/
			'minDate':'1900-1-1',/*最小日期*/
			'maxDate':today,/*最大日期*/
			'onSubmit':function(){/*确认时触发事件*/
				var theSelectData=calendar.value;
			},
			'onClose':function(){/*取消时触发事件*/
			}
	});