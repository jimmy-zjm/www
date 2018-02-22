<?php
namespace Admin\Controller\Overseas;
use \Admin\Controller\Index\AdminController;

/**
 * 后台商品控制器
 */
class GoodsController extends AdminController{
    private $m;
    public function __construct(){
        parent::__construct();
        $this->m = new \Admin\Model\Overseas\GoodsModel;
    }


    /*
    商品列表
     */
    public function index(){
        $data  = $this->m->getAll();
        $data_ = $this->m->getData();
        $this->assign('page', $data['page']);
        $this->assign('goods_list', $data['goods_list']);
        $this->assign('cate_list', $data_['cate_tree']);
        $this->assign('brand_list', $data_['brand_list']);
        $this->assign('dealer_list', $data_['dealer_list']);
        $this->display();
    }

    /*
    商品回收站
     */
    public function recycle(){
        //获取所有商品数据
        $data  = $this->m->getAll(1);
        //获取商品的分类, 品牌,类型数据
        $data_ = $this->m->getData();

        $this->assign('page', $data['page']);
        $this->assign('goods_list', $data['goods_list']);
        $this->assign('cate_list', $data_['cate_tree']);
        $this->assign('brand_list', $data_['brand_list']);
        $this->display();
    }

    /*
    商品从回收站还原
     */
    public function recover($id){
        $id = intval($id);
        if($this->m->where(array('id'=>$id))->setField('is_delete',0)){
            $this->success('商品还原成功',U('recycle'));
            die;
        }
        $this->error($this->m->getError());
    }

    /*
    真正删除商品数据
     */
    public function trueDelete($id){
        $id = intval($id);
        if($this->m->deleteGoods($id)){
            $this->success('商品删除成功',U('recycle'));
            die;
        }
        $this->error($this->m->getError());
    }


    /*
    展示添加商品的页面
     */
    public function add(){
        //获取商品的分类,品牌,类型数据
        $data = $this->m->getData();

        $this->assign('brand_list', $data['brand_list']);
        $this->assign('type_list', $data['type_list']);
        $this->assign('cate_list', $data['cate_tree']);
        $this->assign('dealer_list', $data['dealer_list']);
		$this->assign('list', $data['list']);
        $this->display();
    }

    /*
    执行添加商品
     */
    public function insert(){
        //vdump($_FILES);
        if(!IS_POST) $this->redirect('index');

        if($this->m->create(I('post.'),1)){
            if($this->m->add() !== false){
                $this->success('添加成功',U('add'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

    /*
    展示编辑商品的页面
     */
    public function edit($id){
       

        //接收商品的id
        $id = intval($id);
        if(empty($id)) $this->redirect('参数非法');

        //获取商品的分类,品牌,类型数据
        $data = $this->m->getData();

        //获取商品的信息,用于回填数据
        $goods = $this->m->getGoods($id);
        $this->assign('goods', $goods['goods']);
        $this->assign('goods_attr_str', $goods['attr_str']);
        $this->assign('goods_image_list', $goods['image']);
		$this->assign('goods_video_list', $goods['video']);
        $this->assign('brand_list', $data['brand_list']);
        $this->assign('dealer_list', $data['dealer_list']);
        $this->assign('type_list', $data['type_list']);
        $this->assign('cate_list', $data['cate_tree']);
		$this->assign('list', $data['list']);
        $this->display();
    }

    /*
    执行修改商品
     */
    public function update(){
        if(!IS_POST) $this->redirect('index');
        if($d = $this->m->create(I('post.'),2)){
            if($this->m->save() !== false){
                $this->success('修改成功',U('index'));
                die;
            }
        }
        $this->error($this->m->getError());
    }

    /*
    将商品放入回收站
     */
    public function delete($id){
        $id = intval($id);
        if($this->m->where(array('id'=>$id))->setField('is_delete',1)){
            $this->success('放入回收站成功',U('index'));
            die;
        }else{
            $this->error($this->m->getError());
        }
    }

    /*
    切换是否团购的状态
     */
    public function toggleGroup($id){
        $id = intval($id);
        if(empty($id)) die;
        if(M()->execute('UPDATE xgj_ov_goods SET is_groupbuy = is_groupbuy^1 WHERE id='. $id)){
            echo 1;
        }else{
            echo -1;
        }
        die;
    }

    /*
    切换是否正品的状态
     */
    public function toggleReal($id){
        $id = intval($id);
        if(empty($id)) die;
        if(M()->execute('UPDATE xgj_ov_goods SET is_real = is_real^1 WHERE id='. $id)){
            echo 1;
        }else{
            echo -1;
        }
        die;
    }

    /*
    切换是商品相册的图片的显示或者隐藏
     */
    public function toggleImage($id,$type=''){
        $id = intval($id);
        if(empty($id)) die;
		$type=!empty($type)?$type:'0';
		if($type=='1')
			$table='xgj_ov_video';
		else 
			$table='xgj_ov_image';
        if(M()->execute('UPDATE '.$table.' SET is_show = is_show^1 WHERE id='. $id)){
            echo 1;
        }else{
            echo -1;
        }
        die;
    }

    /*
    删除商品相册中的图片
     */
    public function deleteImage($id,$type=''){
        $id = intval($id);
        if(empty($id)) die;
				$type=!empty($type)?$type:'0';
		if($type=='1')
			$table=M('xgj_ov_video');
		else 
			$table=M('xgj_ov_image');
        $image = $table->find($id);
        if($table->delete($id)){
            //删除图片文件
            deleteImage($image['url'],C('IMG_THUMB'));
            echo 1;
        }else{
            echo -1;
        }
        die;
    }




    /*通过类型id获取属性, 生成表单返回*/
    public function getAttrListByTypeId($id){
        $id = (int)$id;
        $is_screen = (int)$is_screen;
        $data = M('xgj_ov_attribute')->where(array('type_id'=>$id))->select();
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

                $html.=     '</select>
                            &nbsp;&nbsp;属性价格：
                            ￥ <input type="text" name="attr_price_list[]" class="dfinput" id="" style="width:100px;" /> 元
                        </li>';
            }
        }
        $html .= '<li></li>';
        echo $html;
        die;
    }


}