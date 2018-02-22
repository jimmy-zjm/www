<?php
namespace Admin\Controller\Index;
use \Admin\Controller\Index\AdminController;

class ActiveController extends AdminController{

	public function index(){
		$map ='';
		if(!empty($_GET['start']) && !empty($_GET['end'])){
            $map="coupon_number between {$_GET['start']} and {$_GET['end']}";
        }elseif(!empty($_GET['start'])){
            $map="coupon_number={$_GET['start']}";
        }elseif(!empty($_GET['end'])){
            $map="coupon_number={$_GET['end']}";
        }
        if(!empty($map)){
        	$total = M('xgj_users')->where($map)->count();
	        $page  = getPage($total,C('ADPOS_PAGE_SIZE'));
	        $data  = M('xgj_users')->where($map)->order('user_id')->limit($page['limit'])->select();
			
	        $this->assign('page', $page['page']);
	        $this->assign('list', $data);
        }
        $this->display();
	}

	public function exl(){
		$map ='';
		if(!empty($_GET['start']) && !empty($_GET['end'])){
            $map="coupon_number between {$_GET['start']} and {$_GET['end']}";
        }elseif(!empty($_GET['start'])){
            $map="coupon_number={$_GET['start']}";
        }elseif(!empty($_GET['end'])){
            $map="coupon_number={$_GET['end']}";
        }
        if(!empty($map)){
	        $info  = M('xgj_users')->where($map)->order('user_id')->select();
        }
        //导入PHPExcel类库
        //相当于引入了vendor目录下面PHPExcel\PHPExcel.php

        vendor('Excel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '编号')
            ->setCellValue('B1', '会员名称')
            ->setCellValue('C1', '手机号码')
            ->setCellValue('D1', '优惠券号')
            ->setCellValue('E1', '注册时间');
            // ->setCellValue('E2', '型号')
            // ->setCellValue('F2', '品牌/产地')
            // ->setCellValue('G2', '批次')
            // ->setCellValue('H2', '单位')
            // ->setCellValue('I2', '数量')
            // ->setCellValue('J2', '单价')
            // ->setCellValue('K2', '总价');

		foreach($info as $k =>$v){
            $k = $k+2;
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A".$k, $k-1)
            ->setCellValue("B".$k, $v['user_name'])
            ->setCellValue("C".$k, $v['mobile_phone'])
            ->setCellValue("D".$k, $v['coupon_number'])
            ->setCellValue("E".$k, date("Y-m-d H:i:s",$v['reg_time']));
            // ->setCellValue("E".$k, $v['list']['0']['goods_model'])
            // ->setCellValue("F".$k, $v['list']['0']['goods_brand'])
            // ->setCellValue("G".$k, !empty($v['batch']) || $v['batch']=='0'?$v['batch']+1:'抱歉！找不到此材料批次')
			// ->setCellValue("H".$k, $v['list']['0']['goods_unit'])
            // ->setCellValue("I".$k, $v['num']) 
            // ->setCellValue("J".$k, $v['list']['0']['shop_price'])
            // ->setCellValue("K".$k, $v['list']['0']['shop_price']*$v['num']);
        }
        $objPHPExcel->getActiveSheet()->setTitle("优惠券注册会员"); 
        $objPHPExcel->setActiveSheetIndex(0);   
        ob_end_clean() ;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='."优惠券注册会员".'.xls');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
	}
	
	




}
