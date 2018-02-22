<?php
define('XS_K',1);
class quote {
	var $house=array(		//房屋信息
		'type'=>'',				//房型：gy公寓 dj叠加 lp联排 dd独栋
		'area'=>0,				//int 全使用面积
		'num_ws'=>0,			//int 室数
		'num_zw'=>0,
		'num_cw'=>0,
		'num_kt'=>0,
		'num_cf'=>0,
		'num_wsj'=>0,
		'num_yt'=>0,
		'area_ws'=>0,
		'area_zw'=>0,			//int 主卧面积 
		'area_cw'=>0,			//次卧面积 
		'area_kt'=>0,
		'area_cf'=>0,
		'area_wsj'=>0,
		'area_yt'=>0,
	);						
	var $nums=array();		//储存各物件的使用量
	var $chf_num;			//主材（父材）用量
	var $list;				//清单商品列表
	var $item;				//清单商品条目
	
	var $rules= array(
		19=>'固定一个',
		21=>'固定两个',
		30=>'固定8个',
		32=>'主材固定10',
		43=>'固定30个',
		54=>'固定3个',
		55=>'固定4个',
		56=>'固定5个',
		57=>'固定6个',
		58=>'固定7个',
		59=>'固定9个',
		60=>'固定10个',
		
		1=>'500m2一台',
		2=>'面积/50(往大取整)',
		10=>'面积/100(往小取整)',
		11=>'面积/50(往小取整)',
		13=>'面积*K(系数）',
		16=>'小于240m2一台',
		17=>'240~280m2一台',
		18=>'280m2以上一台',
		26=>'风机盘管200',
		27=>'风机盘管400',
		28=>'风机盘管500',
		35=>'面积*0.06',
		36=>'面积*0.14',
		37=>'面积*0.02',
		38=>'面积*0.014',
		39=>'面积*0.4',
		40=>'面积*0.3',
		51=>'面积*5',
		52=>'等于面积',
		53=>'面积/15',
		62=>'VIVA\'O150T',
		63=>'VIVA\'O250T',
		64=>'VIVA\'O350T',
		65=>'面积/20(往大取整)',
		66=>'200m2一件',
		67=>'面积/10(往大取整)',
		68=>'面积*20',
		
		33=>'两个卫生间用一个',
		34=>'三个卫生间用一个',
		44=>'每个区域一个，面积大于17平两个',
		47=>'卫生间数',
		48=>'每个区域1个',
		
		3=>'主材*10',
		4=>'主材/4（往小取整）',
		5=>'主材-1',
		6=>'（主材+1）*4',
		7=>'主材/3（往小取整）',
		8=>'（主材+1）*3',
		9=>'主材*0.2（往大取3的整数倍）',
		12=>'主材*3',
		14=>'主材数',
		15=>'主材/1.5（往小取整）',
		20=>'主材*4',
		22=>'主材*2',
		23=>'主材*8',
		24=>'主材/2',
		25=>'（主材/2-1）*2',
		29=>'主材*6（别墅再加15）',
		31=>'主材*2+1',
		41=>'主材/3（取大）',
		42=>'主材*2+2',
		45=>'电热执行器',
		46=>'管接头',
		49=>'主材*12',
		50=>'（主材+1）*2',
		61=>'主材*0.3',
		69=>'主材*100',
		70=>'主材*20',
		71=>'主材*5',
		72=>'主材*6',

		201=>'新风D80室内送风口',
		203=>'新风D80室内排风口',
		204=>'新风D100室内送风口',
		205=>'新风D160室内送风口',
		//暂时不用
		/* 206=>'新风D75PVC-U排水管',
		207=>'新风D110PVC-U排水管',
		208=>'新风D160PVC-U排水管',
		209=>'新风D110-75-110PVC-U异三通',
		210=>'新风D160-110-160PVC-U异三通',
		212=>'新风D110PVC-U正三通',
		213=>'新风D160PVC-U正三通',
		214=>'新风D110-75PVC-U变径',
		215=>'新风D160-110PVC-U变径',
		216=>'新风D75PVC-U 45度弯头',
		217=>'新风D110PVC-U 45度弯头',
		218=>'新风D160PVC-U 45度弯头',
		232=>'新风D75PVC-U吊卡',
		233=>'新风D110PVC-U吊卡',
		234=>'新风D160PVC-U吊卡',
		240=>'新风D110*75PVC-U补芯', */

	);
	
	function __construct($house=array()){
		if(!isset($house['num_zw'])){
			if($house['type']=='gy' or $house['type']=='lp') $house['num_zw']=1;
			else $house['num_zw']=2;
		}
		
		if(!isset($house['num_cw'])){
			if($house['type']=='gy' or $house['type']=='lp') $house['num_zw']=2;
			else $house['num_zw']=4;
		}
		
		if(!isset($house['area_ws'])){
			$house['area_ws']=$house['area_zw']+$house['area_cw'];
		}
		
		$this->house=$house;
	}
	
	/*
	param
		list 清单商品列表
	*/
	function get_nums($list){
		$this->nums=array();//初始化，适用于不同系统
		$this->list=$list;
		foreach($list as $k=>$item){
			if(isset($this->nums[$item['goods_id']])){
				$this->nums[$item['goods_id']]+=$this->get_num($item);
			}else{
				$this->nums[$item['goods_id']]=$this->get_num($item);
			}
		}
		
		return $this->nums;
	}
	
	protected function get_num($item){
		$this->item=$item;
		$this->chf_num=0;	
		if($item['chf_id']>0){
			$this->chf_num=$this->nums[$item['chf_id']];
		}
		
		$func='_f'.intval($item['rule_id']);

		$num=$this->$func();
		if(!is_int($num)){
			$num=intval($num);
			logx(2,'list num not int',$this->chf_num,$this->item,$this->house);
		}
		if($num<0){
			$num=0;
			logx(3,'list num < 0',$this->chf_num,$this->item,$this->house);
		}
		return $num;
	}
	
	//------除尘
	
	//default
	protected function _f0(){
		logx(3,'no quote rule id',$this->chf_num,$this->item,$this->house);
		return 0;
	}
	
	//500m2一台
	protected function _f1(){
		$num=intval($this->house['area']/500);
		if($num<1) $num=1;
		return $num;
	}
	
	//面积/50(往大取整)
	protected function _f2(){
		return ceil($this->house['area']/50);
	}
	
	//主材*10
	protected function _f3(){
		return ceil($this->chf_num*10);
	}
	
	//主材/4（往小取整）
	protected function _f4(){
		return intval($this->chf_num/4);
	}
	
	//主材-1
	protected function _f5(){
		return $this->chf_num-1;
	}
	
	//（主材+1）*4
	protected function _f6(){
		return ($this->chf_num+1)*4;
	}
	
	//主材/3（往小取整）
	protected function _f7(){
		return intval($this->chf_num/3);
	}
	
	//（主材+1）*3
	protected function _f8(){
		return ($this->chf_num+1)*3;
	}
	
	//主材*0.2（往大取3的整数倍）
	protected function _f9(){	
		$num=$this->chf_num*0.2;
		return ceil($num/3)*3;
	}
	
	//面积/100(往小取整)
	protected function _f10(){
		return intval($this->house['area']/100);
	}
	
	//面积/50(往小取整)
	protected function _f11(){
		return intval($this->house['area']/50);
	}
	
	//主材*3
	protected function _f12(){
		return $this->chf_num*3;
	}
	
	//面积*K(系数）
	protected function _f13(){
		return $this->house['area']*XS_K;
	}
	
	//主材数
	protected function _f14(){
		return $this->chf_num;
	}
	
	//主材/1.5（往小取整）
	protected function _f15(){
		return intval($this->chf_num/1.5);
	}
	
	//--------地源空调（水系统）
	
	//小于240m2一台
	protected function _f16(){
		if($this->house['area']<=240) return 1;
		else return 0;
	}
	
	//240~280m2一台
	protected function _f17(){
		if(240<$this->house['area'] && $this->house['area']<=280) return 1;
		else return 0;
	}
	
	//280m2以上一台
	protected function _f18(){
		if(280<$this->house['area']) return 1;
		else return 0;
	}
	
	//固定一个
	protected function _f19(){
		return 1;
	}
	
	//主材*4
	protected function _f20(){
		return $this->chf_num*4;
	}
	
	//固定两个
	protected function _f21(){
		return 2;
	}
	
	//主材*2
	protected function _f22(){
		return $this->chf_num*2;
	}
	
	//主材*8
	protected function _f23(){
		return $this->chf_num*8;
	}
	
	//主材/2
	protected function _f24(){
		return $this->chf_num/2;
	}
	
	//（主材/2-1）*2
	protected function _f25(){
		return ($this->chf_num/2-1)*2;
	}
	
	//风机盘管200
	protected function _f26(){
		if($this->house['num_ws']==0) return 0;
		//卧平均面积
		$area=$this->house['area_ws']/$this->house['num_ws'];
		if($area<=10) return $this->house['num_ws'];
		else return 0;	
	}
	
	//风机盘管400
	protected function _f27(){
		if($this->house['num_ws']==0) return 0;
		//卧平均面积
		$area=$this->house['area_ws']/$this->house['num_ws'];
		if($area>10 && $area<=20) return $this->house['num_ws'];
		else return 0;	
	}
	
	//风机盘管500
	protected function _f28(){
		if($this->house['num_ws']==0) return 0;
		//卧平均面积
		$area=$this->house['area_ws']/$this->house['num_ws'];
		if($area>20) return $this->house['num_ws'];
		else return 0;	
	}
	
	//主材*6（别墅再加15）
	protected function _f29(){
		$bs=0;//别墅另加数
		if($this->house['type']=='dd') $bs=15;
		return $this->chf_num*6+$bs;
	}
	
	//固定8个
	protected function _f30(){
		return 8;
	}
	
	//主材*2+1
	protected function _f31(){
		return $this->chf_num*2+1;
	}
	
	//主材固定系列
	protected function _zcgd($num){
		if($this->chf_num==0) return 0;
		return $num;
	}
	
	//主材固定10
	protected function _f32(){
		return $this->_zcgd(10);
	}
	
	//--------地暖 湿法 壁挂炉
	
	//两个卫生间用一个
	protected function _f33(){
		if($this->house['num_wsj']<=2){
			return 1;
		}else{
			return 0;
		}
	}
	
	//三个卫生间用一个
	protected function _f34(){
		if($this->house['num_wsj']>=3){
			return 1;
		}else{
			return 0;
		}
	}
	
	//面积*0.06
	protected function _f35(){
		return $this->house['area']*0.06;
	}
	
	//面积*0.14
	protected function _f36(){
		return $this->house['area']*0.14;
	}
	
	//面积*0.02
	protected function _f37(){
		return $this->house['area']*0.02;
	}
	
	//面积*0.014
	protected function _f38(){
		return $this->house['area']*0.014;
	}
	
	//面积*0.4
	protected function _f39(){
		return $this->house['area']*0.4;
	}
	
	//面积*0.3
	protected function _f40(){
		return $this->house['area']*0.3;
	}
	
	//主材/3（取大）
	protected function _f41(){
		return ceil($this->chf_num/3);
	}
	
	//主材*2+2
	protected function _f42(){
		return $this->chf_num*2+2;
	}
	
	//固定30个
	protected function _f43(){
		return 30;
	}
	
	//每个区域一个，面积大于17平两个
	protected function _f44(){
		$num=$this->house['num_ws']+$this->house['num_cf']+$this->house['num_kt']+$this->house['num_wsj']+$this->house['num_yt'];
		if(($area_zw/$num_zw)>17) $num+=1;
		if(($area_cw/$num_cw)>17) $num+=1;
		if(($area_cf/$num_cf)>17) $num+=1;
		if(($area_kt/$num_kt)>17) $num+=1;
		if(($area_wsj/$num_wsj)>17) $num+=1;
		if(($area_yt/$num_yt)>17) $num+=1;
		
		return $num;
	}
	
	//电热执行器
	protected function _f45(){
		return $this->chf_num-$this->house['num_wsj'];
	}
	
	//管接头
	protected function _f46(){
		return ($this->chf_num+$this->house['num_wsj'])*2;
	}
	
	//卫生间数
	protected function _f47(){
		return $this->house['num_wsj'];
	}
	
	//每个区域1个
	protected function _f48(){
		return $this->house['num_ws']+$this->house['num_cf']+$this->house['num_kt']+$this->house['num_wsj']+$this->house['num_yt'];
	}
	
	//主材*12
	protected function _f49(){
		return $this->chf_num*12;
	}
	
	//（主材+1）*2
	protected function _f50(){
		return ($this->chf_num+1)*2;
	}
	
	//面积*5
	protected function _f51(){
		return $this->house['area']*5;
	}
	
	//等于面积
	protected function _f52(){
		return $this->house['area'];
	}
	
	//面积/15
	protected function _f53(){
		return $this->house['area']/15;
	}
	
	//固定3个
	protected function _f54(){
		return 3;
	}
	
	//固定4个
	protected function _f55(){
		return 4;
	}
	
	//固定5个
	protected function _f56(){
		return 5;
	}
	
	//固定6个
	protected function _f57(){
		return 6;
	}
	
	//固定7个
	protected function _f58(){
		return 7;
	}
	
	//固定9个
	protected function _f59(){
		return 9;
	}
	
	//固定10个
	protected function _f60(){
		return 10;
	}

	//新风报价
	protected function _xf($v1=0,$v2=0,$v3=0,$v4=0,$v5=0,$v6=0,$v7=0,$v8=0,$v9=0){
		if($this->house['num_ws']==1 && $this->num_kt==1) return $v1;
		if($this->house['area']>=50 && $this->house['area']<=70 && $this->house['num_ws']==2) return $v2;
		if($this->house['area']>70 && $this->house['area']<=90 && $this->house['num_ws']==2) return $v3;
		if($this->house['area']>70 && $this->house['area']<=90 && $this->house['num_ws']==3) return $v4;
		if($this->house['area']>90 && $this->house['area']<=110 && $this->house['num_ws']==3) return $v5;
		if($this->house['area']>110 && $this->house['area']<=130 && $this->house['num_ws']==3) return $v6;
		if($this->house['area']>120 && $this->house['area']<=140 && $this->house['num_ws']==4) return $v7;
		if($this->house['area']>140 && $this->house['area']<=160 && $this->house['num_ws']==4) return $v8;
		if($this->house['area']>160 && $this->house['area']<=180 && $this->house['num_ws']==4) return $v9;
		
		logx(3,'_xf 新风报价未匹配到条件',$this->house);
		return 1;//默认值
	}
	
	//-------热回收新风(膜)
	
	//主材*0.3
	protected function _f61(){
		return $this->chf_num*0.3;
	}
	
	//VIVA'O150T
	protected function _f62(){
		if($this->house['area']<=100) return 1;
		else return 0;
	}
	
	//VIVA'O250T
	protected function _f63(){
		if($this->house['area']>100 && $this->house['area']<=160) return 1;
		else return 0;
	}
	
	//VIVA'O350T
	protected function _f64(){
		if($this->house['area']>160) return 1;
		else return 0;
	}
	
	//面积/20(往大取整)
	protected function _f65(){
		return ceil($this->house['area']/20);
	}
	
	//200m2一件
	protected function _f66(){
		return ceil($this->house['area']/200);
	}
	
	//面积/10(往大取整)
	protected function _f67(){
		return ceil($this->house['area']/10);
	}
	
	//面积*20
	protected function _f68(){
		return $this->house['area']*20;
	}
	
	//主材*100
	protected function _f69(){
		return $this->chf_num*100;
	}
	
	//主材*20
	protected function _f70(){
		return $this->chf_num*20;
	}
	
	//主材*5
	protected function _f71(){
		return $this->chf_num*5;
	}
	
	//主材*6
	protected function _f72(){
		return $this->chf_num*6;
	}

	
	//新风D80室内送风口(原版)
// 	protected function _f201(){
// 		return $this->_xf(3,5,	5,	5,	5,	5,	5,	6,	6);
// 	}
	
	//新风D80室内送风口
	protected function _f201(){
		return $this->_xf(2,3,	3,	4,	4,	4,	5,	5,	5);
	}

	//新风D80室内排风口
	protected function _f203(){
		return $this->_xf(1,1,	1,	1,	1,	2,	3,	3,	2);
	}
	//新风D100室内送风口
	protected function _f204(){
		return $this->_xf(1	,2,	2,	2,	2,	3,	2,	2,	3);
	}
	//新风D160室内送风口
	protected function _f205(){
		return $this->_xf(2,2,	2,	2,	2,	2,	2,	2,	2);
	}
	//新风D75PVC-U排水管
	protected function _f206(){
		return $this->_xf(10,13,9,	15,	13,	22,	22,	26,	18);
	}
	//新风D110PVC-U排水管
	protected function _f207(){
		return $this->_xf(9	,9,	14,	6,	8,	12,	23,	15,	16);
	}
	//新风D160PVC-U排水管
	protected function _f208(){
		return $this->_xf(2	,10,14,	6,	7,4,5,	9,	17);
	}
	//新风D110-75-110PVC-U异三通
	protected function _f209(){
		return $this->_xf(0	,0,	1,	0,	2,	0,	2,	0,	0);
	}
	//新风D160-110-160PVC-U异三通
	protected function _f210(){
		return $this->_xf(0	,2,	1,	1,	2,	1,	0,	3,	4);
	}	

	//新风D110PVC-U正三通
	protected function _f212(){
		return $this->_xf(3	,2,	3,	4,	1,	3,	4,	4,	4);
	}	
	//新风D160PVC-U正三通
	protected function _f213(){
		return $this->_xf(0	,2,	0,	0,	1,	2,	2,	1,	1);
	}	
	//新风D110-75PVC-U变径
	protected function _f214(){
		return $this->_xf(4	,4,	4,	5,	3,	7,	8,	5,	5);
	}	
	//新风D160-110PVC-U变径
	protected function _f215(){
		return $this->_xf(0	,3,	2,	2,	2,	4,	4,	4,	2);
	}		
	//新风D75PVC-U 45度弯头
	protected function _f216(){
		return $this->_xf(4	,8,	10,	8,	12,	12,	6,	20,	14);
	}		
	//新风D110PVC-U 45度弯头
	protected function _f217(){
		return $this->_xf(5	,2,	4,	2,	6,	4,	14,	4,	0);
	}
	//新风D160PVC-U 45度弯头
	protected function _f218(){
		return $this->_xf(2	,10,8,	4,	6,	4,	4,	8,	6);
	}
	
	//新风D75PVC-U吊卡
	protected function _f232(){
		return $this->_xf(10,13,9,15,13,22,	22,	26,	18);
	}
	//新风D110PVC-U吊卡
	protected function _f233(){
		return $this->_xf(9	,9,	14,	6,	8,	12,	23,	15,	16);
	}
	//新风D160PVC-U吊卡
	protected function _f234(){
		return $this->_xf(2	,10,14,	6,7,4,	5,	9,	17);
	}	

	//新风D110*75PVC-U补芯
	protected function _f240(){
		return $this->_xf(0,	2,	0,	1,	1,	1,	0,	2,	5);
	}
	
	
}
?>