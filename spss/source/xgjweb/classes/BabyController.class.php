
<?php
/**
 * 海外超市控制器
 * 适用于欧洲团购&海外超市
 * @date 2016-3-21
 * @author grass <14712905@qq.com>
 */
class BabyController extends GoodsController{
    private $class_id = 2;

    /*
    海外超市首页
     */
    public function index(){
        $data = D('Baby')->getIndexAll();
        if(isset($_GET['test']) && C('APP_DEBUG')){
            echo '<pre>';
                print_r($data);
            echo '</pre>';
        }
        $this->assign('cate_list', $data['cate_list']);//分类
        $this->assign('concern_list', $data['concern_list']);//关注的所有商品
        $this->assign('bann_list', $data['bann_list']);//banner广告图
        $this->assign('country_list', $data['country_list']);//10个国家专区
        $this->display('baby/index.html');
    }



    /*
    海外超市列表页面
     */
    public function lst(){
        $price=empty($_GET['price'])?'':$_GET['price'];
        $val=empty($_GET['val'])?'':$_GET['val'];
        $sk=empty($_GET['sk'])?'':$_GET['sk'];
        $b_id=empty($_GET['brand_id'])?'':$_GET['brand_id'];
        $data = D('Baby')->getListAll();
        $arr=explode(',', $_GET['id']);
        $pid=$arr[0];
        $id=$arr[1];
        if(isset($_GET['test']) && C('APP_DEBUG')){
            echo '<pre>';
                print_r($data['order_list']);
            echo '</pre>';
            die;
        }
        $this->assign('brand_list', $data['brand_list']);//品牌列表
        $this->assign('concern_list', $data['concern_list']);//关注的所有商品
        $this->assign('cate_list',  $data['cate_list']);//分类
        $this->assign('list_cate',  $data['list_cate']);//分类搜索
        $this->assign('goods_list', $data['goods_list2']);//商品列表
        $this->assign('price_list', $data['price_list']);//价格阶段
        $this->assign('order_list', $data['order_list']);//更多查询条件
        $this->assign('page', $data['page']);
        $this->assign('pid', $pid);
        $this->assign('id', $id);
        $this->assign('b_id', $b_id);
        $this->assign('price',$price);
        $this->assign('val', $val);
        $this->assign('sk', $sk);
        $this->display('baby/list.html');
    }
	    /*
    海外超市列表页面
     */
    public function detail(){
        //接受数据
        $goods_id = I('id',true);
        if(empty($goods_id)) $this->error('参数错误','index.php');
        $data = D('Baby')->getDetailAll($goods_id);
        $this->assign('cate_list', $data['cate_list']);//分类
        $this->assign('concern_list', $data['concern_list']);//关注的所有商品
        $this->assign('goods_info', $data['goods_info']);//商品信息
        $this->assign('goods_image', $data['goods_image']);//商品相册
        $this->assign('goods_comment', $data['comment']);//平均客户评论
        $this->display('baby/detail.html');
    }

     /*获取品牌列表*/
    public function getbrand(){
        $pid=empty($_GET['pid'])?'':$_GET['pid'];
        $price=empty($_GET['price'])?'':$_GET['price'];
        $val=empty($_GET['val'])?'':$_GET['val'];
        $sk=empty($_GET['sk'])?'':$_GET['sk'];
        $id=empty($_GET['id'])?'':$_GET['id'];
        unset($map);
        $map['is_show']    = 1;
        $map['class_id']   = 2;
        $map['cate_id']    = $pid;
        $data = M('xgj_ov_brand')->where($map)->order('`order` ASC')->select();
        foreach ($data as $key => &$bann) {
            $bann['logo'] = getImage($bann['logo']);
        }
        if(count($data)<32){
            $b = array_rand($data,count($data));
        }else{
            $b = array_rand($data,32);
        }
        $str='';
        foreach ($data as $k => $v) {
            if(in_array($k,$b)){
                $str.="<div class='supermarket_list-banner-list-demo'>
                    <a href=baby.php?list&id=$pid,$id&price=$price&brand_id=$v[id]&val=$val&sk=$sk>
                        <img src='".$v['logo']."'/>
                    </a>
                </div>";
            }
        }
        echo $str;
    }
    public function search(){
        if(!empty($_POST['key']) && !empty($_POST['pid']) && !empty($_POST['id'])){
            $key=$_POST['key'];
            $pid=$_POST['pid'];
            $id=$_POST['id'];
            $data = M('xgj_ov_goods')->where("cate_id=$id and goods_title like '%{$key}%' ")->order('`id` ASC')->limit('0,9')->select();
            /*查询用户关注的所有商品*/
            if(isset($_SESSION['userId'])){
                $info=M('xgj_concern')->field('goods_id')->where(array('class_id'=>"2",'user_id'=>$_SESSION['userId']))->select();
                foreach ($info as $k => $v) {
                    $concern[]=$v['goods_id'];
                }
                $concern_list=$concern;
            }else{
                $concern_list=array();
            }
            $str="<div class='supermarket_index-center-floor-list'>";
            foreach ($data as $k => $v) {
                $str.="<div class='supermarket_index-center-floor-list-demo'>
                                <div class='supermarket_index-center-floor-list-demo-img'>
                                    <a href='baby.php?detail&id=$pid,$v[id]' target='_black'>
                                        <img src='".getImage($v['face_image'])."'>
                                    </a>
                                </div>
                                
                                <div class='supermarket_index-center-floor-list-demo-name'>
                                    <a href='baby.php?detail&id=$pid,$v[id]' target='_black'>
                                        $v[goods_title]
                                    </a>
                                </div>
                                
                                <div class='supermarket_index-center-floor-list-demo-info'>
                                    <div class='supermarket_index-center-floor-list-demo-info-original_cost'>
                                        ¥$v[market_price]
                                    </div>
                                    
                                    <div class='supermarket_index-center-floor-list-demo-info-current_price'>
                                        ¥$v[purchase]
                                    </div>                    
                                    <div class='supermarket_index-center-floor-list-demo-info-collection'>";
                                        if (in_array($v['id'],$concern_list)){
                                            $str.="<a href='javascript:;''  >
                                                    <img  src='images/xin04.png'>
                                                </a>";
                                        }else{
                                            $str.="<a href='javascript:;'  onclick='chageImg($v[id])'>
                                            <img id='img$v[id]' src='images/xin05.png'>
                                        </a>";
                                    }
                                $str.="
                                </div>
                                </div>
                                <div class='clear'></div>
                                </div>
                                <div class='clear'></div>
                                </div>";
            }
            echo $str;
        }
    }

    //  public function search(){
    //     if(!empty($_GET['pid']) && !empty($_GET['opt']) && !empty($_GET['id'])){
    //         $pid=$_GET['pid'];
    //         $id=$_GET['id'];
    //         $opt=$_GET['opt'];
    //         $str="<div class='supermarket_index-center-floor-list'>";
    //         switch ($opt) {
    //             case '1':
    //                 $data=$this->page($pid,$id,'id');
    //                 foreach ($data['goods_list2'] as $k => $v) {
    //                     $str.="
    //                         <div class='supermarket_index-center-floor-list-demo'>
    //                             <div class='supermarket_index-center-floor-list-demo-img'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     <img src='".getImage($v['face_image'])."'>
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-name'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     $v[goods_title]
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-info'>
    //                                 <div class='supermarket_index-center-floor-list-demo-info-original_cost'>
    //                                     ¥$v[market_price]
    //                                 </div>
                                    
    //                                 <div class='supermarket_index-center-floor-list-demo-info-current_price'>
    //                                     ¥$v[purchase]
    //                                 </div>                    
    //                             </div>
    //                         </div>";
    //                 }
    //                 $str.="<div class='clear'></div>
    //                     </div>
    //                     <div class='clear32'></div>
                        
    //                     <div  class='page'>".$data['page']."</div>
                        
    //                     <div class='clear32'></div>";
    //                 echo $str;
    //                 break;
    //             case '2':
    //                 $data=$this->page($pid,$id,'purchase');
    //                 foreach ($data['goods_list2'] as $k => $v) {
    //                     $str.="
    //                         <div class='supermarket_index-center-floor-list-demo'>
    //                             <div class='supermarket_index-center-floor-list-demo-img'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     <img src='".getImage($v['face_image'])."'>
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-name'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     $v[goods_title]
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-info'>
    //                                 <div class='supermarket_index-center-floor-list-demo-info-original_cost'>
    //                                     ¥$v[market_price]
    //                                 </div>
                                    
    //                                 <div class='supermarket_index-center-floor-list-demo-info-current_price'>
    //                                     ¥$v[purchase]
    //                                 </div>                    
    //                             </div>
    //                         </div>";
    //                 }
    //                 $str.="<div class='clear'></div>
    //                     </div>
    //                     <div class='clear32'></div>
                        
    //                     <div  class='page'>".$data['page']."</div>
                        
    //                     <div class='clear32'></div>";
    //                 echo $str;
    //                 break;
    //             case '3':
    //                 $data=$this->page($pid,$id,'id');
    //                 foreach ($data['goods_list2'] as $k => $v) {
    //                     $str.="
    //                     <div class='supermarket_index-center-floor-list-demo'>
    //                         <div class='supermarket_index-center-floor-list-demo-img'>
    //                             <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     <img src='".getImage($v['face_image'])."'>
    //                                 </a>
    //                         </div>
                            
    //                         <div class='supermarket_index-center-floor-list-demo-name'>
    //                             <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                 $v[goods_title]
    //                             </a>
    //                         </div>
                            
    //                         <div class='supermarket_index-center-floor-list-demo-info'>
    //                             <div class='supermarket_index-center-floor-list-demo-info-original_cost'>
    //                                 ¥$v[market_price]
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-info-current_price'>
    //                                 ¥$v[purchase]
    //                             </div>                    
    //                         </div>
    //                     </div>";
    //                 }
    //                 $str.="<div class='clear'></div>
    //                     </div>
    //                     <div class='clear32'></div>
                        
    //                     <div  class='page'>".$data['page']."</div>
                        
    //                     <div class='clear32'></div>";
    //                 echo $str;
    //                 break;
    //             case '4':
    //                 $data=$this->page($pid,$id,'id');
    //                 foreach ($data['goods_list2'] as $k => $v) {
    //                     $str.="
    //                         <div class='supermarket_index-center-floor-list-demo'>
    //                             <div class='supermarket_index-center-floor-list-demo-img'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     <img src='".getImage($v['face_image'])."'>
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-name'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     $v[goods_title]
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-info'>
    //                                 <div class='supermarket_index-center-floor-list-demo-info-original_cost'>
    //                                     ¥$v[market_price]
    //                                 </div>
                                    
    //                                 <div class='supermarket_index-center-floor-list-demo-info-current_price'>
    //                                     ¥$v[purchase]
    //                                 </div>                    
    //                             </div>
    //                         </div>";
    //                 }
    //                 $str.="<div class='clear'></div>
    //                     </div>
    //                     <div class='clear32'></div>
                        
    //                     <div  class='page'>".$data['page']."</div>
                        
    //                     <div class='clear32'></div>";
    //                 echo $str;
    //                 break;
    //             case '5':
    //                 $data=$this->page($pid,$id,'addtime');
    //                 foreach ($data['goods_list2'] as $k => $v) {
    //                     $str.="
    //                         <div class='supermarket_index-center-floor-list-demo'>
    //                             <div class='supermarket_index-center-floor-list-demo-img'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     <img src='".getImage($v['face_image'])."'>
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-name'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     $v[goods_title]
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-info'>
    //                                 <div class='supermarket_index-center-floor-list-demo-info-original_cost'>
    //                                     ¥$v[market_price]
    //                                 </div>
                                    
    //                                 <div class='supermarket_index-center-floor-list-demo-info-current_price'>
    //                                     ¥$v[purchase]
    //                                 </div>                    
    //                             </div>
    //                         </div>";
    //                 }
    //                 $str.="<div class='clear'></div>
    //                     </div>
    //                     <div class='clear32'></div>
                        
    //                     <div  class='page'>".$data['page']."</div>
                        
    //                     <div class='clear32'></div>";
    //                 echo $str;
    //                 break;
    //             default:
    //                 $data=$this->page($pid,$id,'id');
    //                 foreach ($data['goods_list2'] as $k => $v) {
    //                     $str.="
    //                         <div class='supermarket_index-center-floor-list-demo'>
    //                             <div class='supermarket_index-center-floor-list-demo-img'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     <img src='".getImage($v['face_image'])."'>
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-name'>
    //                                 <a href='baby.php?detail&id=$v[id]' target='_black'>
    //                                     $v[goods_title]
    //                                 </a>
    //                             </div>
                                
    //                             <div class='supermarket_index-center-floor-list-demo-info'>
    //                                 <div class='supermarket_index-center-floor-list-demo-info-original_cost'>
    //                                     ¥$v[market_price]
    //                                 </div>
                                    
    //                                 <div class='supermarket_index-center-floor-list-demo-info-current_price'>
    //                                     ¥$v[purchase]
    //                                 </div>                    
    //                             </div>
    //                         </div>";
    //                 }
    //                 $str.="<div class='clear'></div>
    //                     </div>
    //                     <div class='clear32'></div>
                        
    //                     <div  class='page'>".$data['page']."</div>
                        
    //                     <div class='clear32'></div>";
    //                 echo $str;
    //                 break;
                
    //         }
    //     }
    // }

    public function page($pid,$id,$order){

        /******************分页*************/
        require_once(WWW_DIR."/libs/page.php");
        //分页每页的条数
        $num=2;

        //全部订单
        if(empty($_GET['p']) || $_GET['p']<=1){
            $page = 1;
            $p1 = 1;
        }else{
            $page = $_GET['p'];
            $p1 = 0;
        }
        $data['goods_list']=M()->fetchAll("SELECT * FROM xgj_ov_goods where cate_id=$id and is_delete=0 AND is_putaway=1 and class_id=2 order by $order asc");
        //显示列表内容
        $page1 = ($page-1)*$num;
        $sql2 = "SELECT * FROM xgj_ov_goods where cate_id=$id and is_delete=0 AND is_putaway=1 and class_id=2 order by $order asc limit $page1,$num";
        $data['goods_list2'] = M()->fetchAll($sql2);
        //分页的总条数
        $orderAll = count($data['goods_list']);

        //实例化分页类
        $t = new Page($num, $orderAll, $page, 5, "baby.php?list&id=$pid,$id&p=");
        //分页样式
        $page=$t->subPageCss2();//分页样式
        //模板传值
        if (empty($data['goods_list']) && $p1 == 1) {
            $data["page"]='';
        }else{
            $data["page"]=$page;
        }
        return $data;
    }
}

