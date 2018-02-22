<?php
namespace Admin\Controller\Overseas;
use \Admin\Controller\Index\AdminController;

/**
 * 后台分类控制器
 */
class CategoryController extends AdminController{

    //分类显示
    public function index(){
    	$cateList = D('Xgj_ov_category');
    	$map['class_id'] = 2;
    	$data = $cateList->where($map)->select();

    	$modelCat = new \Admin\Model\Overseas\CategoryModel('Category','xgj_ov_');
    	$cateData = $modelCat->getCatTree($data);


    	$this->assign('cateData',$cateData);

    	$this->display();
    }

	//添加分类
	public function add(){
		$cateList = D('Xgj_ov_category');
    	$map['class_id'] = 2;
		$data = $cateList->where($map)->select();

		$modelCat = new \Admin\Model\Overseas\CategoryModel('Category','xgj_ov_');
		$cateData = $modelCat->getCatTree($data);

		//获取商品的分类,品牌,类型数据
		$modelGoods= new \Admin\Model\Overseas\GoodsModel;
        $data_ = $modelGoods->getData(2);

        $this->assign('type_list', $data_['type_list']);
		$this->assign('cateData',$cateData);
		$this->display();
	}
	//处理添加分类页面提交过来的数据
	public function insert(){
		$doAdd = D('Xgj_ov_category');
		$data = $doAdd->create();
		if ($data){
			$id=$doAdd->add();
			if ($id){
				// //处理属性
		  //       if(I('post.type_id/d')){//当选择了商品类型的时候才插入属性
		  //           //接受数据
		  //           $info['cate_id'] = $id;
		  //           $attr_id          = I('post.attr_id_list');
		  //           $attr_name          = I('post.attr_name_list');
		  //           $attr_value       = I('post.attr_value_list');
		  //           $attr_price       = I('post.attr_price_list');
		  //           $model = M('xgj_ov_cate_attr');
		  //           for($i=0,$len=count($attr_id); $i<$len; ++$i){
		  //               if(!empty($attr_value[$i])){
		  //                   //当选择了属性值的时候才插入属性
		  //                   $info['attr_id']    = intval($attr_id[$i]);
		  //                   $info['attr_name'] = $attr_name[$i];
		  //                   $info['attr_value'] = $attr_value[$i];
		  //                   $info['attr_price'] = floatval($attr_price[$i]);
		  //                   if($model->data($info)->add() == false){
		  //                       $this->error = '插入商品属性失败';
		  //                       return false;
		  //                   }
		  //               }
		  //           }
		  //       }
				$this->success('分类添加成功','index');
				die();
			}
		}
		$this->error($doAdd->getError());
	}

	//修改分类
	public function edit(){
		//获取待修改的分类信息
		$id = $_GET['pid'];
		$data=D('Xgj_ov_category');
		$dataOne = $data->find($id);//子类的父类信息
		$dataOne['father'] = $dataOne['name'];

		$data2 = D('Xgj_ov_category');
		$id2 = $_GET['id'];
		$dataTwo = $data2->find($id2);

        $type_id=$_GET['tid'];

		$dataOne['name'] = $dataTwo['name'];
		$dataOne['order'] = $dataTwo['order'];
		$dataOne['pid'] = $_GET['pid'];
		$dataOne['id'] = $_GET['id'];
        $dataOne['type_id'] = $type_id;

		$this->assign('dataOne',$dataOne);

		//为上级分类下拉选项获取分类信息
		$cateList = D('Xgj_ov_category');
    	$map['class_id'] = 2;
		$data = $cateList->where($map)->select();

		$modelCat = new \Admin\Model\Overseas\CategoryModel('Category','xgj_ov_');
		$cateData = $modelCat->getCatTree($data);

		//获取商品的分类,品牌,类型数据
		$modelGoods= new \Admin\Model\Overseas\GoodsModel;

        $data_ = $modelGoods->getData(2);

        //$cateAttr=$this->getCateAttr($_GET['id']);

        $this->assign('cate_attr_str', '');

        $this->assign('type_list', $data_['type_list']);

		$this->assign('cateData',$cateData);

		$this->display();
	}
	//执行修改操作
	public function update(){

		$doMod = D('Xgj_ov_category');
		$data = $doMod->create();
		if (!empty($data)){
			if ($doMod->save()!==false){
				// //处理属性
		  //       if(I('post.type_id/d')){//当选择了商品类型的时候才插入属性
		  //           //接受数据
		  //           $info['cate_id'] = I('post.id/d');
		  //           $attr_id          = I('post.attr_id_list');
		  //           $attr_name          = I('post.attr_name_list');
		  //           $attr_value       = I('post.attr_value_list');
		  //           $attr_price       = I('post.attr_price_list');
		  //           $model = M('xgj_ov_cate_attr');
		  //           for($i=0,$len=count($attr_id); $i<$len; ++$i){
		  //               if(!empty($attr_value[$i])){
		  //                   //当选择了属性值的时候才插入属性
		  //                   $info['attr_id']    = intval($attr_id[$i]);
		  //                   $info['attr_name'] = $attr_name[$i];
		  //                   $info['attr_value'] = $attr_value[$i];
		  //                   $info['attr_price'] = floatval($attr_price[$i]);
		  //                   if($model->data($info)->add() == false){
		  //                       $this->error = '插入商品属性失败';
		  //                       return false;
		  //                   }
		  //               }
		  //           }
		  //       }
				$this->success('分类修改成功','index');
				die();
			}
		}
		$this->error($doMod->getError());
	}

	//删除分类
	public function delete(){

		if(empty($_GET)){
			$this->redirect('index');
		}

		$del = D('Xgj_ov_category');

		$where = "pid=$_GET[id]";
		$arr = $del->where($where)->find();

		if(!empty($arr)){
			echo "<script type='text/javascript'>alert('该分类下有子分类不能删除！');history.back();</script>";

			return false;
		}

		$where = "id=$_GET[id]";
		if ($del->where($where)->delete()){
			$this->success('删除成功');
		}
		exit();
	}


	 /*
    获取商品数据, 用于回填
     */
    public function getCateAttr($id){
    	$data['cate']=M('xgj_ov_category')->find($id);

        //取出"其他的"属性, 在属性表中有, 在商品属性表中没有的数据.
        //通过商品id查询出商品属性信息
        $data['attr'] = M('xgj_ov_cate_attr')->field('ga.*,a.name,a.type_id,a.mode,a.input_type,a.value_list')->alias('ga')->join('LEFT JOIN xgj_ov_attribute AS a ON ga.attr_id=a.id')->where(array('cate_id'=>$id,'is_screen'=>2))->order('ga.id')->select();
        //var_dump($data['attr']);die;
        $other_attr = array();
        foreach ($data['attr'] as $a) {
            if(!in_array($a['attr_id'], $other_attr)){
                $other_attr[] = $a['attr_id'];
            }
        }

        //计算出所有的商品属性信息
        if(!empty($other_attr)){
            $map['id'] = array('not in',$other_attr);
        }
        $map['type_id'] = $data['cate']['type_id'];
        $map['is_screen'] = 2;
        $other_attr     = M('xgj_ov_attribute')->field('id AS attr_id,name,type_id,mode,input_type,value_list')->where($map)->select();
        $data['attr']   = array_merge($data['attr'], $other_attr);
        
        //属性信息
        $html = '';
        $temp_arr = array();
        foreach ($data['attr'] as $value) {
            // 输入类型为唯一值
            if($value['mode'] == 0){
                if($value['input_type'] == 0){
                    //单行文本框
                    $html.='<li>
                                <input type="hidden" name="ga_id_list[]" value="'.$value['id'].'" />
                                <input type="hidden" name="attr_name_list[]" value="'.$value['name'].'" />
                                <input type="hidden" name="attr_id_list[]" value="'.$value['attr_id'].'" />
                                <label>'.$value['name'].'</label>
                                <input type="text" class="dfinput" name="attr_value_list[]" value="'.$value['attr_value'].'" />
                                <input type="hidden" name="attr_price_list[]" value="">
                            </li>';
                }elseif($value['input_type'] == 1){
                    //下拉框
                    $html.='<li>
                                <label>'.$value['name'].'</label>
                                <input type="hidden" name="ga_id_list[]" value="'.$value['id'].'" />
                                <input type="hidden" name="attr_name_list[]" value="'.$value['name'].'" />
                                <input type="hidden" name="attr_id_list[]" value="'.$value['attr_id'].'" />
                                <select name="attr_value_list[]" style="opacity:1;border:solid 1px #c3ab7d;height:35px;">
                                    <option value="">请选择...</opton>';
                    $value_list = explode(',', $value['value_list']);
                    $value_list = array_filter($value_list);
                    foreach ($value_list as $v) {
                        $selected = $value['attr_value'] == $v ? 'selected' : '';
                        $html .= '<option value="'.$v.'" '.$selected.'>'.$v.'</option>';
                    }

                    $html.=     '</select>
                                <input type="hidden" name="attr_price_list[]" />
                            </li>';
                }else{
                    //多行文本框
                    $html.='<li>
                                <label>'.$value['name'].'</label>
                                <input type="hidden" name="ga_id_list[]" value="'.$value['id'].'" />
                                <input type="hidden" name="attr_name_list[]" value="'.$value['name'].'" />
                                <input type="hidden" name="attr_id_list[]" value="'.$value['attr_id'].'" />
                                <textarea name="attr_value_list[]" class="textinput" id="" cols="20" rows="5" style="width:328px;height:70px;">'.$value['attr_value'].'</textarea>
                                <input type="hidden" name="attr_price_list[]" value="">
                            </li>';
                }

            }else{
                //单选, 下拉框

                //判断出是否为同类属性中的第一个
                if(in_array($value['attr_id'], $temp_arr)){
                    $sign =  'onclick="removeSpec(this)">[-]';
                }else{
                    $sign =  'onclick="addSpec(this)">[+]';
                    $temp_arr[] = $value['attr_id'];
                }

                $html.='<li>
                            <label><a href="javascript:;" '.$sign.'</a>'.$value['name'].'</label>
                            <input type="hidden" name="attr_name_list[]" value="'.$value['name'].'" />
                            <input type="hidden" name="ga_id_list[]" value="'.$value['id'].'" />
                            <input type="hidden" name="attr_id_list[]" value="'.$value['attr_id'].'" />
                            <select name="attr_value_list[]" style="opacity:1;border:solid 1px #c3ab7d;height:35px;">
                                <option value="">请选择...</opton>';
                $value_list = explode(',', $value['value_list']);
                $value_list = array_filter($value_list);
                foreach ($value_list as $v) {
                    $selected = $value['attr_value'] == $v ? 'selected' : '';
                    $html .= '<option value="'.$v.'" '.$selected.'>'.$v.'</option>';
                }
                $html.=     '</select></li>';
                // $html.=     '</select>
                //             &nbsp;&nbsp;属性价格：
                //             ￥ <input type="text" name="attr_price_list[]" class="dfinput" value="'.$value['attr_price'].'" style="width:100px;" /> 元
                //         </li>';
            }
        }
        $html .= '<li></li>';
        $data['attr_str'] = $html;
        $temp_arr = null;

        //返回数据
        return $data;
    }


    /*通过类型id获取属性, 生成表单返回*/
    public function getAttrListByTypeId($id,$is_screen=2){
        $id = (int)$id;
        $is_screen = (int)$is_screen;
        $data = M('xgj_ov_attribute')->where(array('type_id'=>$id,'is_screen'=>$is_screen))->select();
        $html = '';
        foreach ($data as $value) {

            //属性id

            // 输入类型为唯一值
            if($value['mode'] == 0){
                if($value['input_type'] == 0){
                    //单行文本框
                    $html.='<li>
                                <input type="hidden" name="attr_id_list[]" value="'.$value['id'].'" />
                                <input type="hidden" name="attr_name_list[]" value="'.$value['name'].'" />
                                <label>'.$value['name'].'</label>
                                <input type="text" class="dfinput" name="attr_value_list[]" value="'.$value['value_list'].'" />
                                <input type="hidden" name="attr_price_list[]" value="">
                            </li>';
                }elseif($value['input_type'] == 1){
                    //下拉框
                    $html.='<li>
                                <label>'.$value['name'].'</label>
                                <input type="hidden" name="attr_id_list[]" value="'.$value['id'].'" />
                                <input type="hidden" name="attr_name_list[]" value="'.$value['name'].'" />
                                <select name="attr_value_list[]" style="opacity:1;border:solid 1px #c3ab7d;height:35px;">
                                    <option value="">请选择...</opton>';
                    $value_list = explode(',', $value['value_list']);
                    $value_list = array_filter($value_list);
                    foreach ($value_list as $v) {
                        $html .= '<option value="'.$v.'">'.$v.'</option>';
                    }

                    $html.=     '</select>
                                <input type="hidden" name="attr_price_list[]" />
                            </li>';
                }else{
                    //多行文本框
                    $html.='<li>
                                <label>'.$value['name'].'</label>
                                <input type="hidden" name="attr_id_list[]" value="'.$value['id'].'" />
                                <input type="hidden" name="attr_name_list[]" value="'.$value['name'].'" />
                                <textarea name="attr_value_list[]" class="textinput" id="" cols="20" rows="5" style="width:328px;height:70px;">'.$value['value_list'].'</textarea>
                                <input type="hidden" name="attr_price_list[]" value="">
                            </li>';
                }

            }else{
                //单选, 下拉框
                $html.='<li>
                            <label><a href="javascript:;" onclick="addSpec(this)">[+]</a>'.$value['name'].'</label>
                            <input type="hidden" name="attr_id_list[]" value="'.$value['id'].'" />
                            <input type="hidden" name="attr_name_list[]" value="'.$value['name'].'" />
                            <select name="attr_value_list[]" style="opacity:1;border:solid 1px #c3ab7d;height:35px;">
                                <option value="">请选择...</opton>';
                $value_list = explode(',', $value['value_list']);
                $value_list = array_filter($value_list);
                foreach ($value_list as $v) {
                    $html .= '<option value="'.$v.'">'.$v.'</option>';
                }

                $html.=     '</select></li>';
                // $html.=     '</select>
                //             &nbsp;&nbsp;属性价格：
                //             ￥ <input type="text" name="attr_price_list[]" class="dfinput" id="" style="width:100px;" /> 元
                //         </li>';
            }
        }
        $html .= '<li></li>';
        echo $html;
        die;
    }
}