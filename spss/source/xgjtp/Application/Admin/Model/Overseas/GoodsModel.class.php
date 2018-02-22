<?php
namespace Admin\Model\Overseas;
use \Think\Model;
/**
 * 商品模型
 */

class GoodsModel extends Model{
    protected $trueTableName = 'xgj_ov_goods';
    protected $fields= array('id','d_id','goods_title','goods_title_style','pro_info','goods_sn','cate_id','brand_id','type_id','market_price','shop_price','groupbuy_price','face_image','is_putaway','is_new','is_hot','is_rec','seo_keywords','seo_description','description','weight','weight_unit','warn_number','goods_detail','seller_note','is_delete','click_count','addtime','class_id','package_list','after_sales_support','duties','luggage','purchase','vat','service_charge','discount_amount','country_id','goods_mnemonic' );

    protected $_validate = array(
            array('goods_title','require','商品名称不能为空!',1),
            array('goods_sn','','不能有相同的商品编码!',1,'unique',1),
            array('goods_sn','/^\w+$/','商品编码格式不正确，由字母、数字、下划线组成',2,'',3),
            array('goods_sn','1,30','商品编号的长度为1-30位',2,'length',3),
            array('cate_id','0','商品分类没有选择',0,'notequal'),
            array('market_price','require','市场价格为必填',1),
            array('market_price','currency','市场价格不合理',1),
            array('purchase','require','采购价格为必填',1),
            array('purchase','currency','采购价格不合理',1),
            array('purchase','currency','采购价格不合理',1),
            array('discount_amount','currency','优惠金额不合理',0),
            array('weight','currency','请填写正确的商品重量',2),
            array('is_putaway','1','是否上架信息错误',0,'equal'),
            array('seo_keyworkds','0,255','商品的关键字内容太长',0,'length'),
            array('seo_description','0,255','商品的简单描述内容过长',0,'length'),
        );


    /*******插入商品之前**************************************************************/
    protected function _before_insert(&$data, $options){
        /*******处理基本信息*******/
        $data['addtime']             = time();//添加时间
        $data['is_delete']           = 0;
        $data['is_putaway']          = I('post.is_putaway')?1:0;
        $data['is_new']              = I('post.is_new')?1:0;
        $data['is_hot']              = I('post.is_hot')?1:0;
        $data['is_rec']              = I('post.is_rec')?1:0;
        $data['goods_sn']            = empty($_POST['goods_sn'])?uniqid():I('post.goods_sn');
        $data['luggage']             =I('post.luggage');//运费
       
        //商品商品的封面图

         if(isset($_FILES['face_image'])&&$_FILES['face_image']['error']==0){
             $image = uploadOne('face_image','Goods',C('IMG_THUMB_FACE'));
             if($image['code']!=1){
                 //商品封面图片上传失败
                 $this->error = $image['error'];
                 return false;
             }
             $data['face_image'] = $image['images'];
         }
    }

    /*******插入商品之后**************************************************************/
    protected function _after_insert($data, $options){

        //商品id
        $goods_id = $data['id'];

        //修改商品的编号
        if(empty($_POST['goods_sn'])){
            $goods_sn = C('GOODS_SN_PREFIX').str_pad($goods_id,8,0,STR_PAD_LEFT);
        }else{
            $goods_sn = I('post.goods_sn');
        }
        $sql = "UPDATE xgj_ov_goods SET goods_sn='{$goods_sn}' WHERE id='{$goods_id}'";
        $this->execute($sql);

        // 下行代码会引起商品属性表中的数据插入两次, 所以注释
        // $this->where(array('id'=>$goods_id))->setField('goods_sn', $goods_sn);


        //处理属性
        if(I('post.type_id/d')){//当选择了商品类型的时候才插入属性
            //接受数据
            $info['goods_id'] = $goods_id;
            $attr_id          = I('post.attr_id_list');
            $attr_value       = I('post.attr_value_list');
            $attr_price       = I('post.attr_price_list');
            $model = M('xgj_ov_goods_attr');
            for($i=0,$len=count($attr_id); $i<$len; ++$i){
                if(!empty($attr_value[$i])){
                    //当选择了属性值的时候才插入属性
                    $info['attr_id']    = intval($attr_id[$i]);
                    $info['attr_value'] = $attr_value[$i];
                    $info['attr_price'] = floatval($attr_price[$i]);
                    if($model->data($info)->add() == false){
                        $this->error = '插入商品属性失败';
                        return false;
                    }
                }
            }
        }

        /***上传商品相册图片***/
        if(!empty($_FILES['img_url'])){
            $pics = array();
            $img_model = M('xgj_ov_image');

            for ($i=0; $i < count($_FILES['img_url']['name']); ++$i) {
                $pics[] = array(
                    'name'     => $_FILES['img_url']['name'][$i],
                    'type'     => $_FILES['img_url']['type'][$i],
                    'tmp_name' => $_FILES['img_url']['tmp_name'][$i],
                    'error'    => $_FILES['img_url']['error'][$i],
                    'size'     => $_FILES['img_url']['size'][$i],
                );
            }
            
            foreach ($pics as $key=>$value) {
                $_FILES[$key] = $value;
                if($_FILES[$key]['error']==0){
                    //将上传成功的商品相册图片地址保存起来
                    $image_info = uploadOne($key,'Goods',C('IMG_THUMB'));
                    if($image_info['code']==1){
                        $data = array(
                                'goods_id' => $goods_id,
                                'url'      => $image_info['images'],
                                'is_show'  => 1,
                                'class_id' => 2,
                            );
                        if(!$img_model->add($data)){
                            $this->error = '商品图片数据插入失败';
                            return false;
                        }
                    }else{
                        $this->error = '商品相册图片上传失败:'.$image_info['error'];
                        return false;
                    }
                }
            }
        }
		 /***上传商品视频***/
        if(!empty($_FILES['video_url'])){
            $pics = array();
            $video_model = M('xgj_ov_video');

            for ($i=0; $i < count($_FILES['video_url']['name']); ++$i) {
                $pics[] = array(
                    'name'     => $_FILES['video_url']['name'][$i],
                    'type'     => $_FILES['video_url']['type'][$i],
                    'tmp_name' => $_FILES['video_url']['tmp_name'][$i],
                    'error'    => $_FILES['video_url']['error'][$i],
                    'size'     => $_FILES['video_url']['size'][$i],
                );
            }
            
            foreach ($pics as $key=>$value) {
                $_FILES[$key] = $value;
                if($_FILES[$key]['error']==0){
                    //将上传成功的商品视频地址保存起来
                    $image_info = uploadOne($key,'Goods',array(),'Video_exts');
                    if($image_info['code']==1){
                        $data1 = array(
                                'goods_id' => $goods_id,
								'title'    =>I('post.video_title')[$key],
                                'url'      => $image_info['images'],
                                'is_show'  => 1,
                                'class_id' => 2,
                            );
                        if(!$video_model->add($data1)){
                            $this->error = '商品视频数据插入失败';
                            return false;
                        }
                        
                    }else{
                        $this->error = '商品视频上传失败:'.$image_info['error'];
                        return false;
                    }
                }
            }
			
			
        }
    }

    /*
    修改商品之前
     */
    protected function _before_update(&$data, $options){
        /*******处理基本信息*******/
        $data['updatetime']          = time();//添加时间
        $data['is_putaway']          = I('post.is_putaway')?1:0;
        $data['is_new']              = I('post.is_new')?1:0;
        $data['is_hot']              = I('post.is_hot')?1:0;
        $data['is_rec']              = I('post.is_rec')?1:0;
        $data['luggage']             =I('post.luggage');//运费

        //商品的封面图

        if(isset($_FILES['face_image'])&&$_FILES['face_image']['error']==0){//有新的图片上传
             $image = uploadOne('face_image','Goods',C('IMG_THUMB_FACE'));
             if($image['code']!=1){
                 //商品封面图片上传失败
                 $this->error = $image['error'];
                 return false;
             }
             $data['face_image'] = $image['images'];

             //删除y原来的封面图片
             $img_url = M('xgj_ov_goods')->where($options['where'])->getField('face_image');
             deleteImage($img_url,C('IMG_THUMB_FACE'));
        }
        return true;
    }

    /*
    修改商品之后
     */
    protected function _after_update($data, $options){
        /*处理属性*/
        $goods_id = $options['where']['id'];

        //检查是否修改了商品类型,如果修改了,将以前的类型的所有属性删除
        $old_type_id = I('post.old_type_id/d');
        $type_id = I('post.type_id/d');
        if($old_type_id!=$type_id){
           M('xgj_ov_goods_attr')->where(array('goods_id'=>$options['where']['id']))->delete();
        }

        //接收数据
        $info['goods_id'] = $goods_id;
        $ga_id            = I('post.ga_id_list');
        $attr_id          = I('post.attr_id_list');
        $attr_value       = I('post.attr_value_list');
        $attr_price       = I('post.attr_price_list');
        //循环插入商品属性
        $model = M('xgj_ov_goods_attr');
        for($i=0,$len=count($attr_id); $i<$len; ++$i){
            if(empty($attr_value[$i])) continue;//跳过空的属性值

            //收集数据
            $info['attr_id'] = intval($attr_id[$i]);
            $info['attr_value'] = $attr_value[$i];
            $info['attr_price'] = floatval($attr_price[$i]);
            if(!empty($ga_id[$i])){
                //执行修改
        // var_dump($info);
                $model->data($info)->where(array('id'=>$ga_id[$i]))->save();
            }else{
                //执行添加
        // var_dump($info);
                $model->data($info)->add();
            }
        }
// die;
        /***上传商品相册图片***/
		
        if(!empty($_FILES['img_url'])){
            $pics = array();
            $img_model = M('xgj_ov_image');

            for ($i=0; $i < count($_FILES['img_url']['name']); ++$i) {
                $pics[] = array(
                    'name' => $_FILES['img_url']['name'][$i],
                    'type' => $_FILES['img_url']['type'][$i],
                    'tmp_name' => $_FILES['img_url']['tmp_name'][$i],
                    'error' => $_FILES['img_url']['error'][$i],
                    'size' => $_FILES['img_url']['size'][$i],
                );
            }
            //var_dump($pics);exit;
            foreach ($pics as $key=>$value) {
                $_FILES[$key] = $value;
                if($_FILES[$key]['error']==0){
                    //将上传成功的商品相册图片地址保存起来
                    $image_info = uploadOne($key,'Goods',C('IMG_THUMB'));
                    if($image_info['code']==1){
                        $data = array(
                                'goods_id' => $goods_id,
                                'url'      => $image_info['images'],
                                'is_show'  => 1,
                                'class_id'=>2
                            );
                        if(!$img_model->add($data)){
                            $this->error = '商品图片数据插入失败';
                            return false;
                        }
                    }else{
                        $this->error = '商品相册图片上传失败:'.$image_info['error'];
                        return false;
                    }
                }
            }
        }

		//商品视频
		
		if(!empty($_FILES['video_url'])){
            $pics = array();
            $video_model = M('xgj_ov_video');
			
            for ($i=0; $i < count($_FILES['video_url']['name']); ++$i) {
                $pics[] = array(
                    'name' => $_FILES['video_url']['name'][$i],
                    'type' => $_FILES['video_url']['type'][$i],
                    'tmp_name' => $_FILES['video_url']['tmp_name'][$i],
                    'error' => $_FILES['video_url']['error'][$i],
                    'size' => $_FILES['video_url']['size'][$i],
                );
            }
            foreach ($pics as $key=>$value) {
                $_FILES[$key] = $value;
		
                if($_FILES[$key]['error']==0){
                    //将上传成功的商品视频地址保存起来
                    $video_info = uploadOne($key,'Goods',array(),'Video_exts');
                    if($video_info['code']==1){
                        $data1[] = array(
                                'goods_id' => $goods_id,
                                'url'      => $video_info['images'],
                                'is_show'  => 1,
                                'class_id'=>2,
								'title'    =>I('post.video_title')[$key]);
                        if(!$video_model->add($data1)){
                            $this->error = '商品视频数据插入失败';
                            return false;
                        }
                       
                    }else{
                        $this->error = '商品视频上传失败:'.$video_info['error'];
                        return false;
                    }
                }
            }
			
			
        }

    }


    /**
     * 获取商品的分类,品牌,类型数据
     * @return [type] [description]
     */
    public function getData(){
        // 获取分类数据
        $cate = new \Admin\Model\Overseas\CategoryModel('category','xgj_ov_');
        $cate_list = $cate->where(array('is_show'=>1,'class_id'=>2))->order('`order`')->select();
        $cate_tree = $cate->getCatTree($cate_list);
        $data['cate_tree'] = $cate_tree;

        //获取品牌数据
        $brand = new \Admin\Model\Overseas\BrandModel;
        $brand_list = $brand->where(array('class_id'=>2))->order('`order` asc')->select();
        $data['brand_list'] = $brand_list;

        //获取经销商数据
        $dealer_list = M('xgj_ov_dealer')->where(array('is_show'=>'1'))->order('`sort` asc')->select();
        $data['dealer_list'] = $dealer_list;

        //获取商品类型
        $type = new \Admin\Model\Overseas\TypeModel;
        $type_list = $type->where(array('is_use'=>1))->select();
        $data['type_list'] = $type_list;
        
        //获取国家信息
        $data['list'] = M('xgj_eu_country')->select();
        return $data;
    }

    /**
     * 获取所有的商品信息
     * @return [type] [description]
     */
    public function getAll($is_delete=0){
        //拼凑条件
        $map = array();
        if(isset($_GET['send'])){
            $goods_title = I('goods_title');
            $goods_sn    = I('goods_sn');
            $cate_id     = I('cate_id/d');
            $brand_id    = I('brand_id/d');
            $is_putaway  = I('is_putaway/d');

            if(!empty($goods_title)){
                $map['goods_title'] = array('like',"%{$goods_title}%");
            }
            if(!empty($goods_sn)){
                $map['goods_sn'] = array('eq',$goods_sn);
            }
            if(!empty($brand_id)){
                $map['brand_id'] = array('eq',$brand_id);
            }
            if(!empty($cate_id)){
                $map['cate_id'] = array('eq',$cate_id);
            }
            if($is_putaway == 1){
                $map['is_putaway'] = array('eq',1);
            }elseif($is_putaway == 2){
                $map['is_putaway'] = array('eq',0);
            }
        }

        $map['is_delete']  = array('eq', $is_delete);
        $map['class_id'] = 2;

        //分页
        $total        = $this->where($map)->count();
        $page         = getPage($total, C('GOODS_PAGE_SIZE'));
        $data['page'] = $page['page'];

        // 商品数据
        unset($map['class_id']);
        unset($map['cate_id']);
        $map['g.class_id'] = 2;
        if(!empty($cate_id)){
            $map['g.cate_id'] = array('eq',$cate_id);
        }
        $data['goods_list'] = $this->field('g.*,c.name AS cate_name,b.name AS brand_name')->alias('g')->join('LEFT JOIN xgj_ov_category AS c ON g.cate_id=c.id')->join('LEFT JOIN xgj_ov_brand AS b ON g.brand_id=b.id')->where($map)->order('addtime DESC')->limit($page['limit'])->select();

        //处理数据
        foreach ($data['goods_list'] as &$v) {
            $v['face_image'] = getImage($v['face_image'],54,54);
            $v['addtime']    = date('Y-m-d H:i', $v['addtime']);
            $total           = M()->query('SELECT SUM(`number`) AS total FROM xgj_ov_stock WHERE goods_id='.$v['id']);
            $v['stock']      = max(0,$total[0]['total']);
            $v['duties']     = $v['duties']*100;
        }

        return $data;
    }

    /*
    获取商品数据, 用于回填
     */
    public function getGoods($id){
        //商品数据
        $data['goods'] = $this->find($id);
        $data['goods']['face_image'] = getImage($data['goods']['face_image'],350,350);
        $data['goods']['video_image'] = getImage($data['goods']['video_image']);
        $data['goods']['goods_detail'] = htmlspecialchars_decode($data['goods']['goods_detail']);


        //取出"其他的"属性, 在属性表中有, 在商品属性表中没有的数据.
        //通过商品id查询出商品属性信息
        $data['attr'] = M('xgj_ov_goods_attr')->field('ga.*,a.name,a.type_id,a.mode,a.input_type,a.value_list')->alias('ga')->join('LEFT JOIN xgj_ov_attribute AS a ON ga.attr_id=a.id')->where(array('goods_id'=>$id))->order('ga.id')->select();
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
        $map['type_id'] = $data['goods']['type_id'];
        $other_attr     = M('xgj_ov_attribute')->field('id AS attr_id,name,type_id,mode,input_type,value_list')->where($map)->select();
        $data['attr']   = array_merge($data['attr'], $other_attr);

        //通过商品id查询出商品相册信息
        $data['image'] = M('xgj_ov_image')->where(array('goods_id'=>$id))->select();
        array_walk($data['image'], function(&$v,$k){
            $v['url'] = getImage($v['url'],350,350);
        });

		//通过商品id查询出商品视频
        $data['video'] = M('xgj_ov_video')->where(array('goods_id'=>$id))->select();
        array_walk($data['video'], function(&$v,$k){
            $v['url'] = getImage($v['url']);
        });
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

                $html.=     '</select>
                            &nbsp;&nbsp;属性价格：
                            ￥ <input type="text" name="attr_price_list[]" class="dfinput" value="'.$value['attr_price'].'" style="width:100px;" /> 元
                        </li>';
            }
        }
        $html .= '<li></li>';
        $data['attr_str'] = $html;
        $temp_arr = null;

        //返回数据
        return $data;
    }

    /*
    真正删除商品
     */
    public function deleteGoods($id){
        $goods = $this->where(array('id'=>$id))->find();
        if(!$goods) return false;
        if($this->where(array(
                'id'=>array('eq',$id),
                'is_delete'=>array('eq',1),
            ))->delete()){

            // 取出商品的图片地址,删除封面图和封面缩略图片
            $image_path = $goods['face_image'];
            deleteImage($image_path, C('IMG_THUMB_FACE'));

            //删除商品的相册图片 和 相册缩略图片 和 相册记录
            $image_list = M('xgj_ov_image')->where(array('goods_id'=>$id))->select();
            foreach ($image_list as $vv) {
                deleteImage($vv['url'], C('IMG_THUMB'));
            }
            M('xgj_ov_image')->where(array('goods_id'=>$id))->delete();

            //删除商品关联的 所有属性
            M('xgj_ov_goods_attr')->where(array('goods_id'=>$id))->delete();

            //删除商品的所有的库存
            M('xgj_ov_stock')->where(array('goods_id'=>$id))->delete();
            return true;
        }else{
            return false;
        }
    }
}