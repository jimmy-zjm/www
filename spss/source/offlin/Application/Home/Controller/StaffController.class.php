<?php
namespace Home\Controller;
use Think\Controller;
class StaffController extends BaseController {


	public function _initialize() {  
		parent::_initialize();
		$this->assign('now','1');
	}

	//员工管理
	public function index(){
		$user = $this->thirdly();
		
		$pid=getCatGrandson($_SESSION['dealerId'],'pad_user');
		$pid[]=$_SESSION['dealerId'];
		$map['a.pid']  = array('in',$pid);
		
		if(!empty(I('get.search')))
				$map['a.name|a.real_name|a.tel|b.name']=array('like',"%".urldecode(I('get.search'))."%");
		$total = count(D('Staff')->getPadUsers($map));
		$page  = getPage($total,C('Staff_PAGE_SIZE'));

		$list  = D('Staff')->getPadUsers($map,$page['limit']);

		$this->assign('list',$list);
		$this->assign('page',$page);
		
        
		$this->display();
	}


	/*public function second(){

		$pid  = I('get.pid');

		if (empty($pid)) {
			$re = $this->thirdly();
			$pid = $re['id'];
		}else{
			$re = D('Staff')->getPadUser($pid);
			if ($re['id'] != $_SESSION['dealerId'] && $re['pid'] != $_SESSION['dealerId']) {
				$re = D('Staff')->getPadUser($re['pid']);
				if ($re['pid'] != $_SESSION['dealerId']) {
					echo "<SCRIPT type='text/javascript'>alert('您没有此权限!!!');history.back();</SCRIPT>";exit;
				}
			}
		}

        $total = count(D('Staff')->getPadUsers($pid));

        $page  = getPage($total,C('Staff_PAGE_SIZE'));

        $list  = D('Staff')->getPadUsers($pid,$page);

		$this->assign('list',$list);
		$this->assign('page',$page['page']);
		
        
		$this->display();
	}*/

	public function thirdly(){
		$re = D('Staff')->getPadUser();
		if ($re['level']==3) {
			echo "<SCRIPT type='text/javascript'>alert('您没有此权限!!!');history.back();</SCRIPT>";exit;
		}else{
			return $re;
		}
	}



	//设置用户子账号是否开启
	public function userOpen(){
		layout(false);
		$font = $_GET['font'];
		$id   = $_GET['id'];
		if (!empty($font) && $font=='启用' || !empty($font) && $font=='停用') {
			
			$save = D('Staff')->updateUserOpen($id,$font);
			echo $save;
		}
	}
		//员工信息
	public function staffInfo(){
		$re = $this->thirdly();

		$id = I('get.id');

		$user = D('Staff')->getPadUser($id);

		$user['birthday1'] = substr($user['birthday'], 0,4); 
        $user['birthday2'] = substr($user['birthday'], 4,2); 
        $user['birthday3'] = substr($user['birthday'], 6,2); 

		$total = count(D('Staff')->getStaffInfo($id));

        $page  = getPage($total,C('Staff_PAGE_SIZE'));

        $list  = D('Staff')->getStaffInfo($id,$page);
         // dump($page);exit;

		$this->assign('user',$user);
		$this->assign('list',$list);
		$this->assign('page',$page['page']);
		
        
		$this->display();
	}




	//修改子账号的详细信息
	public function updateUser(){

		layout(false);
		
		foreach ($_POST as $key => $value) {
			if ($value == '') {
				echo "<SCRIPT type='text/javascript'>alert('请填写完整再提交!!!');history.back();</SCRIPT>";exit;
			}
		}
		if(!preg_match("/^[0-9]+$/", $_POST['id'])){
		   echo "<SCRIPT type='text/javascript'>alert('该用户不存在!!!');history.back();</SCRIPT>";exit;
		}
		$id = $_POST['id'];
		$data = $_POST;
		
		$data = D('Staff')->updateUser($data,$id);
		if ($data == 1) echo "<SCRIPT type='text/javascript'>alert('修改成功!!!');history.back();</SCRIPT>";
		else echo "<SCRIPT type='text/javascript'>alert('修改失败!!!');history.back();</SCRIPT>";
	}


	//获取某一年某一月的日有几天
	public function calDays(){
		layout(false);
		$num = cal_days_in_month(CAL_GREGORIAN, $_GET['y'], $_GET['n']); // 31
		echo $num;
	}

	public function staffinfos(){
		layout(false);
		$id=$_GET['id'];
		$data['list']= M('pad_remark r')->join('pad_user u on r.u_id=u.id')->where(array('r.c_id'=>$id))->order('r.addtime desc')->select();//$db->getAll("select r.*,u.name ,u.real_name from pad_remark r join xgj_furnish_dealer u on r.u_id=u.id where r.c_id=$id order by r.addtime desc");
		$html="<div class='pad-checkbox-clickAddId-center-bk'> 
        <div class='pad-checkbox-clickAddId-center'>
            <div class='pad-checkbox-clickAddId-center-list-bk'>
                <div class='pad-checkbox-clickAddId-center-title'>
					<div class='pad-checkbox-clickAddId-center-title-left'>
						<span class='span01'>
							修改日志
						</span>
					</div>
					
					<div class='pad-checkbox-clickAddId-center-title-right' id='CkOutId01' onclick='clo()'>
						<a href='javascript:;'>
							<img src='Public/img/cha01.png'/>
						</a>
					</div>
				</div>
                
				<div class='clear2'></div>
				
                <div class='pad-checkbox-clickAddId-center-list'>";
		foreach ($data['list'] as $k => $v) {
			$html.="<div class='pad-checkbox-clickAddId-center-demo'>
                        <div class='pad-checkbox-clickAddId-center-demo-01'>
                            <div class='pad-checkbox-clickAddId-center-demo-01-name'>
                                {$v['u_name']}更新了备注
                            </div>
                            
                            <div class='pad-checkbox-clickAddId-center-demo-01-time'>"
                                .date('m-d H:i',$v['addtime']).
                          " </div>
                            
                            <div class='clear'></div>
                        </div>
                        
                        <div class='clear'></div>
                        
                        <div class='pad-checkbox-clickAddId-center-demo-02'>
                            <div class='pad-checkbox-clickAddId-center-demo-02-word'>
                                {$v['content']}
                            </div>
                        </div>
                    </div>
                    
                    <div class='clear27'></div>";
		}    
               $html.="</div>
			   
			   	<div class='clear2'></div>
            </div>
        </div>
        
        <div class='clear'></div>
    </div>" ;
    echo $html;die;

	}


	
}
