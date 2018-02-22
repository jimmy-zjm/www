function FreshTime()

{

var endtime=new Date("2015/11/20,12:20:12");//结束时间

var nowtime = new Date();//当前时间

var lefttime=parseInt((endtime.getTime()-nowtime.getTime())/1000);

d=parseInt(lefttime/3600/24);

h=parseInt((lefttime/3600)%24);

m=parseInt((lefttime/60)%60);

s=parseInt(lefttime%60);

document.getElementById("LeftTime").innerHTML="剩余" + d+"天"+"&nbsp;&nbsp;"+h+"："+m+"："+s+"";

if(lefttime<=0){

document.getElementById("LeftTime").innerHTML="团代购活动已结束";

clearInterval(sh);

}

}

FreshTime();

var sh;

sh=setInterval(FreshTime,1000);
