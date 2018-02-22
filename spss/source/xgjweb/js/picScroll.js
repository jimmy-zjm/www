// JavaScript Document
function picScroll(id1,id2,id3,num)
{
	var speed=num; //数字越大速度越慢
	var tab=document.getElementById(id1);
	var tab1=document.getElementById(id2);
	var tab2=document.getElementById(id3);
	tab2.innerHTML=tab1.innerHTML;
	function Marquee()
	{
		if(tab2.offsetWidth-tab.scrollLeft<=0)
		{
		tab.scrollLeft-=tab1.offsetWidth;
		}
		else
		{
		tab.scrollLeft++;
		}
	}
	var MyMar=setInterval(Marquee,speed);
	tab.onmouseover=function() {clearInterval(MyMar)};
	tab.onmouseout=function() {MyMar=setInterval(Marquee,speed)};
}