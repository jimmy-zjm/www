<?php
namespace Admin\Model\Index;
use \Think\Model;
/*
视频位置模型
 */
class VideoposModel extends Model{
    protected $trueTableName = 'xgj_video_pos';

    protected $_validate=array(
        array('name','require','视频不能为空',1,''),
        array('width','1,1024','视频位的宽度应该为1-1024之间',1,'between'),
        //array('width','number','视频位的宽度必须为数字',1),
        array('height','1,1024','视频位的高度应该为1-1024之间',1,'between'),
        //array('height','number','视频位的高度必须为数字',1),
        array('pos_desc','0,255','视频位描述应该在0-255之内',2,'length'),
    );

    function video_cat_list($pid = 0, $num = 0){
		//$sql="select * from xgj_article_cat where parent_id=$pid";
		$result=M('xgj_video_pos')->where("pid=$pid")->select();
		$str = str_repeat ( '&nbsp;&nbsp;', $num );
		$num += 2;
		$aa='';
		foreach ($result as $v){
			$aa.="<tr>
			<td>{$v['pid']}</td>
			<td>$str<a href=".U('edit',array('id'=>$v['id'])).">{$v['name']}</a></td>
			<td><a href=".U('Admin/Index/Video/index',array('id'=>$v['id'])).">视频列表</a>|<a href=".U('edit',array('id'=>$v['id'])).">修改</a>|<a href=".U('delete',array('id'=>$v['id']))." onclick='return delete()'>删除</a></td>
			</tr><tr>
			{$this->video_cat_list($v['id'],$num)}
			</tr>";
		}
		return $aa;
	}


	function video_cat_option($pid = 0, $num = 0, $curid = 0){
		$result=M('xgj_video_pos')->where("pid=$pid")->select();
		$str = str_repeat ( '+', $num );
		$num += 2;
		$string='';
		foreach ($result as $v){
			if ($v['id'] == $curid) {
				$string.="<option selected value='{$v['id']}'>|+{$str}{$v['name']}</option>{$this->video_cat_option($v ['id'], $num ,$curid)}";
			} else {
				$string.="<option value='{$v['id']}'>|+{$str}{$v['name']}</option>{$this->video_cat_option($v ['id'], $num ,$curid )}";
			}
		}

		return $string;
	}

	

}