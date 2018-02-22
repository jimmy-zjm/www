<?php
namespace Admin\Model\Index;
use \Think\Model;
/*
视频位置模型
 */
class VideoModel extends Model{
    protected $trueTableName = 'xgj_video';
    protected $fileds = array('id','video_pos_id','title','text','image','video','is_on','start_time','end_time');

    protected $_validate=array(
        array('video_pos_id','require','视频位置不能为空',1,''),
        array('start_time','require','必须选择视频开始时间',1,''),
        array('end_time','require','必须选择视频结束时间',1,''),
    );


}