<?
//$rules 规则列表
$rules=array(
	'gd'=>array(//固定系列
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
	),
	'mj'=>array(//面积
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
	),
	'fj'=>array(//房间
		33=>'两个卫生间用一个',
		34=>'三个卫生间用一个',
		44=>'每个区域一个，面积大于17平两个',
		47=>'卫生间数',
		48=>'每个区域1个',
	),
	'zc'=>array(//主材
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
	),
	'qt'=>array(//其他
		
	),
);
//33止

return $rules;
?>