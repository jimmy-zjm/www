<?php
namespace Home\Model;
use Think\Model;

/**
 * @author Administrator
 *
 */
class StaffModel  extends Model {
	protected $autoCheckFields =false;
	
	public function staffList(){
        $total=M('pad_user')->where(['pid' => $_SESSION['dealerId']])->count();
        $result['page']               = getAjaxPage($total, 1);
        $result['list']=M('pad_user')->where(['pid' => $_SESSION['dealerId']])->limit($result['page']['limit'])->select();
        return $result;
    }

   /* public function staffInfo($id){
        $re['user'] = M('pad_user')->where(['id' => $id,'pid' => $_SESSION['dealerId']])->find();
        if (empty($re['user'])) {
            echo "<SCRIPT type='text/javascript'>alert('该用户不存在!!!');history.back();</SCRIPT>";exit;
        }

        $re['user']['birthday1'] = substr($re['user']['birthday'], 0,4); 
        $re['user']['birthday2'] = substr($re['user']['birthday'], 4,2); 
        $re['user']['birthday3'] = substr($re['user']['birthday'], 6,2); 
        $total=M('pad_customer')->where(['u_id' => $id])->order("time desc")->count();
        $re['page']               = getAjaxPage($total, 1);
        $re['customer']=M('pad_customer')->where(['u_id' => $id])->order("time desc")->limit($re['page']['limit'])->select();
        return $re;
    }*/


    public function updateUserOpen($id,$font){
        if ($font=='启用') $data['is_open']='1';
        else if($font=='停用') $data['is_open']='2';
        $result = M('pad_user')->where(['id'=>$id])->save($data);
        return $result;
    }

    public function updateUser($data,$id){

        if ($data['birthday2'] < '10') $data['birthday2'] = '0'.$data['birthday2'];
        if ($data['birthday3'] < '10') $data['birthday3'] = '0'.$data['birthday3'];
        
        $data['birthday'] = $data['birthday1'].$data['birthday2'].$data['birthday3'];

        unset($data['id']);
        unset($data['birthday1']);
        unset($data['birthday2']);
        unset($data['birthday3']);
        $result = M('pad_user')->where(['id'=>$id])->save($data);
        return $result;
    }

    public function getPadUser($id){
        
        if (empty($id)) {
            $id = $_SESSION['dealerId'];
        }

        $re = M('pad_user')->where(['id'=>$id])->find();
        return $re;
    }

    public function getPadUsers($map,$page){
		
        $re = M('pad_user a')->field('a.*,b.name company')->join('left join pad_company b on a.c_id=b.id')->where($map)->limit($page)->select();
        return $re;
    }

    public function getStaffInfo($id,$page){

        $user = M('pad_user')->field('id')->where(['pid'=>$id])->select();
        foreach ($user as $k => $v) {
            $ids .= $v['id'].',';
        }

        $ids = trim($ids,',');

        if (!empty($ids)) {
            $map['pid']  = array('in',$ids);
            $users = M('pad_user')->field('id')->where($map)->select();
            foreach ($users as $k => $v) {
                $ids .= ','.$v['id'];
            }
            $ids = trim($ids,',');
        }
        $ids = $id.','.$ids;

        $cMap['u_id']  = array('in',$ids);
 
        $re=M('pad_customer')->where($cMap)->order("time desc")->limit($page['limit'])->select();

        return $re;
    }

    // public function pidVerify($pid){
    //     $user = $this->getPadUser($pid);
        
    // }
}

