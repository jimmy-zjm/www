<?php
require( 'conf/config.inc.php' );
//显示会员中心页面
if (isset($_GET['center']) || empty($_GET))
	$type='center';

//显示注册页面
elseif (isset($_GET['register']))
    $type='register';

//显示登录页面
elseif (isset($_GET['login']))
    $type='login';

//手机注册页面
elseif (isset($_GET['wapreg']))
    $type='wapreg';

elseif (isset($_GET['dowapreg']))
    $type='dowapreg';

//处理注册
elseif (isset($_GET['doregister']))
    $type='doregister';

//注册验证用户名是否存在
elseif (isset($_GET['checkRegister']))
    $type='checkRegister';

//修改密码AJAX 验证密码
elseif (isset($_GET['ajaxeditpass']))
    $type='ajaxeditpass';

//处理登录
elseif (isset($_GET['doLogin']))
    $type='doLogin';

//退出
elseif (isset($_GET['userQuit']))
    $type='userQuit';

//显示订单页面
elseif (isset($_GET['order']))
    $type='order';

elseif (isset($_GET['house']))
    $type='user_house';

elseif (isset($_GET['house_add']))
    $type='house_add';

//显示关注的商品
elseif (isset($_GET['concernGoods']))
    $type='concernGoods';
//取消关注的商品
elseif (isset($_GET['cancergoods']))
    $type='cancergoods';
//取消关注的商品1
elseif (isset($_GET['cancergoods1']))
    $type='cancergoods1';
//加入购物车
elseif (isset($_GET['eucart']))
    $type='eucart';
//显示个人信息
elseif (isset($_GET['userInfo']))
    $type='userInfo';
//修改个人信息
elseif (isset($_GET['userInfoEdit']))
    $type='userInfoEdit';
//查询收货地址
elseif (isset($_GET['addr']))
    $type='addr';
//设为默认收货地址
elseif (isset($_GET['addrDefaultSet']))
    $type='addrDefaultSet';
//添加收货地址
elseif (isset($_GET['addrInfoAddShow']))
    $type='addrInfoAddShow';
//添加收货地址
elseif (isset($_GET['doAddrInfoAdd']))
    $type='doAddrInfoAdd';
//显示修改收货地址界面
elseif (isset($_GET['addrInfoEditShow']))
    $type='addrInfoEditShow';
//实行修改收货地址
elseif (isset($_GET['doAddrInfoEdit']))
    $type='doAddrInfoEdit';
//实行删除收货地址
elseif (isset($_GET['addrInfoDel']))
    $type='addrInfoDel';
//显示找回密码页面
elseif (isset($_GET['findpassword']))
    $type='findpassword';
  //执行找回密码操作
elseif (isset($_GET['dofindpassword']))
    $type='dofindpassword';
//显示修改密码页面
elseif (isset($_GET['passWordModifyShow']))
    $type='passWordModifyShow';
//使用ajax检测原始密码是否正确
elseif (isset($_GET['ajaxCheckPassWord']))
    $type='ajaxCheckPassWord';
//执行修改密码
elseif (isset($_GET['doPassWordModify']))
    $type='doPassWordModify';
//优惠券
elseif (isset($_GET['couponShow']))
    $type='couponShow';
//激活优惠券
elseif (isset($_GET['activateCoupon']))
    $type='activateCoupon';
//激活优惠券
elseif (isset($_GET['actCoulist']))
    $type='actCoulist';
//显示欧团评价列表页面(未评价)
elseif (isset($_GET['euEvaluateShowNone']))
    $type='euEvaluateShowNone';
//显示欧团评价列表页面(已评价)
elseif (isset($_GET['euEvaluateShowAlready']))
    $type='euEvaluateShowAlready';
//海外超市订单列表页
elseif (isset($_GET['supermarketOrder']))
    $type='supermarketOrder';
//海外超市订单列表页分页
elseif (isset($_GET['supermarketOrderPage']))
    $type='supermarketOrderPage';
//海外超市(未评价)
elseif (isset($_GET['supermarketOrderEvaluateNone']))
    $type='supermarketOrderEvaluateNone';
//海外超市(已评价)
elseif (isset($_GET['supermarketOrderEvaluateAlready']))
    $type='supermarketOrderEvaluateAlready';
//海外超市评价页面
elseif (isset($_GET['ovEvaluate']))
    $type='ovEvaluate';
//海外超市查看评价页面
elseif (isset($_GET['doOvEvaluate']))
    $type='doOvEvaluate';
//海外超市执行添加评价
elseif (isset($_GET['addOvEvaluate']))
    $type='addOvEvaluate';
//欧洲建材评价提交页面
elseif (isset($_GET['euEvaluate']))
    $type='euEvaluate';
//欧洲建材评价详情页面
elseif (isset($_GET['euEvaluation']))
    $type='euEvaluation';
//显示家居评价列表页面未评价
elseif (isset($_GET['evaluateShowNone']))
    $type='evaluateShowNone';
//显示家居评价列表页面已评价
elseif (isset($_GET['evaluateShowAlready']))
    $type='evaluateShowAlready';
//家居评价提交页面
elseif (isset($_GET['fuEvaluate']))
    $type='fuEvaluate';
//家居评价详情页面
elseif (isset($_GET['fuEvaluation']))
    $type='fuEvaluation';
//执行家居评价操作
elseif (isset($_GET['doaddfuEvaluation']))
    $type='doaddfuEvaluation';
//执行评价操作
elseif (isset($_GET['doEvaluate']))
    $type='doEvaluate';
//执行评价操作
elseif (isset($_GET['addEuEvaluate']))
    $type='addEuEvaluate';
//显示取消订单界面
elseif (isset($_GET['cancelOrderShow']))
    $type='cancelOrderShow';
//显示返修退换货列表页界面
elseif (isset($_GET['returnShow']))
    $type='returnShow';
//显示返修退换货操作(详情页)界面
elseif (isset($_GET['returnApplicationShow']))
    $type='returnApplicationShow';
//执行返修退换货操作
elseif (isset($_GET['doApply']))
    $type='doApply';
//显示个人资产(余额)界面
elseif (isset($_GET['assetShow']))
    $type='assetShow';
//显示家居订单界面
elseif (isset($_GET['homeOrderShow']))
    $type='homeOrderShow';
//显示我的中心页面
elseif (isset($_GET['mycenter']))
    $type='mycenter';
//显示我的建议页面
elseif (isset($_GET['mysuggest']))
    $type='mysuggest';
//显示材料清单页面
elseif (isset($_GET['mainmaterials']))
     $type='mainmaterials';
//显示账户余额页面
elseif (isset($_GET['balance']))
    $type='balance';
//显示售后订单页面
elseif (isset($_GET['aftersale']))
    $type='aftersale';
//显示密码管理页面
elseif (isset($_GET['passwordmanger']))
    $type='passwordmanger';
//显示修改房屋信息页面
elseif (isset($_GET['houseinformation']))
    $type='houseinformation';
//增加房屋信息
elseif (isset($_GET['dohousedata']))
    $type="dohousedata";
//显示健康家居房屋信息
elseif (isset($_GET['homeoffer']))
    $type="homeoffer";
//执行下载
 elseif (isset($_GET['downLoad']))
    $type="downLoad";
//获取短信验证码
 elseif (isset($_GET['message']))
    $type="message";
//找回密码验证手机号码
 elseif (isset($_GET['checkRegister1']))
    $type="checkRegister1";
//修改订单状态
 elseif (isset($_GET['order_status']))
    $type="order_status";

//修改订单状态
 elseif (isset($_GET['ov_order_status']))
    $type="ov_order_status";

//我的订单分页--全部订单
 elseif (isset($_GET['order_tabs1']))
    $type="order_tabs1";
//我的订单分页--待付款
 elseif (isset($_GET['order_tabs2']))
    $type="order_tabs2";
//我的订单分页--待收货
 elseif (isset($_GET['order_tabs3']))
    $type="order_tabs3";
//我的订单分页--待评价
 elseif (isset($_GET['order_tabs4']))
    $type="order_tabs4";
//找回密码手机验证
 elseif (isset($_GET['dotel']))
    $type="dotel";
//找回密码验证码
 elseif (isset($_GET['domsg']))
    $type="domsg";
//找回密码密码验证 
 elseif (isset($_GET['dopass']))
    $type="dopass";
 elseif (isset($_GET['afterService']))
    $type="afterService";
 elseif (isset($_GET['afterTab']))
    $type="afterTab";
//删除订单
 elseif (isset($_GET['delOrder']))
    $type="delOrder";
//删除订单
 elseif (isset($_GET['activationCoupon']))
    $type="activationCoupon";
//欧洲建材订单详情
 elseif (isset($_GET['euOrderDetails']))
    $type="euOrderDetails";
//海外超市订单详情
 elseif (isset($_GET['ovOrderDetails']))
    $type="ovOrderDetails";
//删除订单
 elseif (isset($_GET['delEuOrder']))
    $type="delEuOrder";
//积分
elseif(isset($_GET['integral'])){
    $type="integral";
}
//积分收入
elseif(isset($_GET['integralIncome'])){
    $type="integralIncome";
}
//积分支出
elseif(isset($_GET['integralExpenditure'])){
    $type="integralExpenditure";
}
//确认收货
elseif(isset($_GET['theGoods'])){
    $type="theGoods";
}
//取消订单
elseif(isset($_GET['cancel'])){
    $type="cancel";
}
//确认收货
elseif(isset($_GET['ovtheGoods'])){
    $type="ovtheGoods";
}
//海外超市删除订单
elseif(isset($_GET['delovOrder'])){
    $type="delovOrder";
}
//取消订单
elseif(isset($_GET['ovcancel'])){
    $type="ovcancel";
}

elseif (isset($_GET['euEvaluateShowAll']))
    $type="euEvaluateShowAll";

elseif (isset($_GET['evaluateShowAll']))
    $type="evaluateShowAll";
//删除优惠券兑换明细
 elseif (isset($_GET['delcoupon']))
    $type="delcoupon";

else
	$type='error';




define("ACTION", "userController.".$type);

$act = explode("." , ACTION);

if (!is_file(CLASS_DIR . "/" . $act[0] . '.php')) die('module "'.$act[0].'" is not implemented.');
require( CLASS_DIR . "/".$act[0].".php" );
$app = new $act[0]();

if (!method_exists($app, $act[1])) die('method "'.$act[1].'" in "'.$act[0].'" is not implemented.');
$app->$act[1]();

?>