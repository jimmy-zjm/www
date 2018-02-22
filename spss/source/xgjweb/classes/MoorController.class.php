<?php
/**
 * 购物车控制器
 * 适用于欧洲团购&德国母婴
 * @date 2016-3-10
 * @author grass <14712905@qq.com>
 */
class MoorController extends Controller{

    /**
     * 添加到购物车
     *
     */
	public function index(){
		header('Location: http://www.myspss.com/');
    }

    public function originCallNo(){
		$mobile=I('originCallNo');
		if(!empty($mobile)){
			$data=D('Moor')->userinfo($mobile);
			echo"<table>
						<tr><td>昵称</td><td>".$data['user_name']."</td></tr>
						<tr><td>真实姓名</td><td>".$data['real_name']."</td></tr>
						<tr><td>电子邮箱</td><td>".$data['email']."</td></tr>
						<tr><td>身份证号码</td><td>".$data['identity_card']."</td></tr>
						<tr><td>性别</td><td>".$data['sex']."</td></tr>
						<tr><td>月收入</td><td>".$data['monthly_profit']."</td></tr>
						<tr><td>教育程度</td><td>".$data['education_status']."</td></tr>
						<tr><td>省</td><td>".$data['pro']."</td></tr>
						<tr><td>市</td><td>".$data['city']."</td></tr>
						<tr><td>县/区</td><td>".$data['area']."</td></tr>
						<tr><td>详细地址 </td><td>".$data['addr']."</td></tr>
					</table>";
			}
    }

    public function name(){
		$mobile=I('phone');
		if(!empty($mobile)){
			$data=D('Moor')->userinfo($mobile);
			echo"<table>
						<tr><td>类型</td><td>7陌系统已存储</td></tr>
						<tr><td>昵称</td><td>".$data['user_name']."</td></tr>
						<tr><td>真实姓名</td><td>".$data['real_name']."</td></tr>
						<tr><td>电子邮箱</td><td>".$data['email']."</td></tr>
						<tr><td>身份证号码</td><td>".$data['identity_card']."</td></tr>
						<tr><td>性别</td><td>".$data['sex']."</td></tr>
						<tr><td>月收入</td><td>".$data['monthly_profit']."</td></tr>
						<tr><td>教育程度</td><td>".$data['education_status']."</td></tr>
						<tr><td>省</td><td>".$data['pro']."</td></tr>
						<tr><td>市</td><td>".$data['city']."</td></tr>
						<tr><td>县/区</td><td>".$data['area']."</td></tr>
						<tr><td>详细地址 </td><td>".$data['addr']."</td></tr>
			</table>";
		}
    }

    public function qimoClientId(){
		//var_dump($_GET);
		$userid=I('qimoClientId');
		if(!empty($userid)){
			$data=D('Moor')->userinfo1($userid);
			echo"<table>
						<tr><td>手机号</td><td>".$data['mobile_phone']."</td></tr>
						<tr><td>昵称</td><td>".$data['user_name']."</td></tr>
						<tr><td>真实姓名</td><td>".$data['real_name']."</td></tr>
						<tr><td>电子邮箱</td><td>".$data['email']."</td></tr>
						<tr><td>身份证号码</td><td>".$data['identity_card']."</td></tr>
						<tr><td>性别</td><td>".$data['sex']."</td></tr>
						<tr><td>月收入</td><td>".$data['monthly_profit']."</td></tr>
						<tr><td>教育程度</td><td>".$data['education_status']."</td></tr>
						<tr><td>省</td><td>".$data['pro']."</td></tr>
						<tr><td>市</td><td>".$data['city']."</td></tr>
						<tr><td>县/区</td><td>".$data['area']."</td></tr>
						<tr><td>详细地址 </td><td>".$data['addr']."</td></tr>
			</table>";
		}
    }



}