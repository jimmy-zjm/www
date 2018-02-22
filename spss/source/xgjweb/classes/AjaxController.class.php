<?php
/**
 * ajax专用控制器
 * @date 2016-3-16
 * @author grass <14712905@qq.com>
 */
class AjaxController extends Controller{
    private $validate = array(
            array('a_name','require','收货人不能为空'),
            array('a_mobile_phone','phone','手机号码格式不正确'),
            array('a_pro','require','省份没有选择'),
            array('a_city','require','城市没有选择'),
            array('a_area','require','区/县没有选择'),
            array('a_addr','require','详细地址不能为空'),
        );

    /*
    ajax设置默认地址
     */
    public function setDefaultAddr(){
        $id = I('get.id',true);
        if(empty($id)) die;
        $user_id = session('userId');
        $map['user_id'] = $user_id;
        $map['default'] = 1;
        M('xgj_address')->where($map)->setField('default',0);
        if(M('xgj_address')->where(array('a_id'=>$id))->setField('default',1)){
            die('1');
        }
        die('-1');
    }

    /*
    ajax删除 地址
     */
    public function delAddr(){
        $id = I('get.id',true);
        if(empty($id)) die;
        $user_id = session('userId');
        $is_default = M('xgj_address')->where(array('a_id'=>$id))->getField('default');
        if($is_default==1){
            api(-1,'不能删除默认地址','得得得');
        }
        if(M('xgj_address')->where(array('a_id'=>$id))->delete()){
            api(1,'success');
        }else{
            api(-1,'系统错误');
        }
    }

    /*
    根据id获取地址信息
     */
    public function getAddr(){
        $id = I('get.id',true);
        if(empty($id)) die;
        $user_id = session('userId');
        $map['user_id'] = $user_id;
        $map['a_id'] = $id;
        $addr = M('xgj_address')->where($map)->select();
        //var_dump($map,$addr);die;
        if(empty($addr)){
            api(-1,'参数错误');
        }else{
            api(1,'success',$addr[0]);
        }
    }

    /*
    修改地址信息
     */
    public function saveAddress(){
        $id = I('post.a_id',true);
        if(empty($id)) die;
        $user_id = session('userId');
        $map['user_id'] = $user_id;
        $map['a_id'] = $id;
        if($data = M('xgj_address')->validate($this->validate)->create()){
                $data['a_pro']=$_POST['cho_Province'];
                $data['a_city']=$_POST['cho_City'];
                $data['a_area']=$_POST['cho_Area'];
            if(M('xgj_address')->data($data)->where($map)->save()){
                $address1 = D('Order')->getAddress(1);
                $address2 = D('Order')->getAddress(0);
                $data = array(
                    'addr1' => $address1,
                    'addr2' => $address2,
                );
                return api(1,'success',$data);
            }else{
                api(-1,'参数错误');
            }

        }else{
            api(-1,M('xgj_address')->getError());
        }

    }

    /*
    ajax获取城市
     */
    public function ajaxGetArea(){
        $id = I('get.id',true);//获取所有的省份,
        $id = $id?$id:1;//默认获取省列表,  省份的pid都为1
        $data = M('xgj_area')->where(array('pid'=>$id))->select();
        if($data){
            return api(1,'success',$data);
        }else{
            return api(-1,'error','非法参数');
        }
    }


   
    /*
    ajax添加地址
     */
    public function addAddress(){
        $_POST['default'] = 0;
        $_POST['user_id'] = session('userId');
        if($data = M('xgj_address')->validate($this->validate)->create()){
            $data['a_pro']=$_POST['cho_Province'];
            $data['a_city']=$_POST['cho_City'];
            $data['a_area']=$_POST['cho_Area'];
            if($id = M('xgj_address')->data($data)->add()){
                $address1 = D('Order')->getAddress(1);
                $address2 = D('Order')->getAddress(0);
                $data = array(
                    'addr1' => $address1,
                    'addr2' => $address2,
                );
                return api(1,'success',$data);
            }else{
                return api(-1,'系统错误');
            }
        }
        return api(-1,M('xgj_address')->getError());
    } 

    /*
    获取地址信息
     */
    public function addrList(){
        $address1 = D('Order')->getAddress(1);
        $address2 = D('Order')->getAddress(0);
        //var_dump($address1,$address2);die;
        $data = array(
            'addr1' => $address1,
            'addr2' => $address2,
        );
        //var_dump($data);die;
        if(!empty($address1) || !empty($address2)){
            api(1,'success',$data);
        }else{
            api(0,'empty');
        }
    }

    /*
    获取库存信息, 需要传入商品id
     */
    public function getStock(){
        $goods_id = I('goods_id');//商品id
        if(empty($goods_id)) api(-1,'参数错误');

        $ga_id = I('ga_id');//选择的商品属性id
        if(!empty($ga_id)) $ga_id = array_filter($ga_id);//去除空的商品属性值
        $stock = M('xgj_eu_stock')->where(array('goods_id'=>$goods_id,'number'=>array('gt',0)))->select();//该商品的所有库存

        if(empty($stock)) api(-2,'库存不足');
        if(count($stock)==1 && $stock[0]['number']<=0) api(-2,'库存不足');
        if(empty($stock[0]['goods_attr_id'])) api(2,'success', $this->computeStock($goods_id));

        if(isset($_GET['test'])){
            echo 'ga_id[]:';
            var_dump($ga_id);
            echo '<hr/>';
        }

        $attr_num = count(explode(',',$stock[0]['goods_attr_id']));
        $old_ga_id = $ga_id;

        //根据商品属性 拼凑正则
        //将没有选择的商品属性填充为0
        for ($i=0; $i < $attr_num; $i++) if(!isset($ga_id[$i])) $ga_id[$i] = 0;
        ksort($ga_id);

        $search_preg = '';
        foreach ($ga_id as $k => $v) {
            if($v===0){
                $search_preg .= '\d*?,';
            }else{
                $search_preg .= $v.',';
            }
        }
        $search_preg = '/'.rtrim($search_preg,',').'/';
        // var_dump($search_preg);die;

        //将所有的库存值放到一个一维数组中
        $stock_all = $stock_all_true = array();
        foreach ($stock as $k2 => $v2) {
            $a = explode(',',$v2['goods_attr_id']);
            for ($ii=0; $ii < count($a); $ii++){
                $stock_all_true[] = $a[$ii];
            }

            if(preg_match($search_preg, $v2['goods_attr_id'])){
                $a = explode(',',$v2['goods_attr_id']);
                for ($i=0; $i < count($a); $i++){
                    if($v2['number']>0)
                    $stock_all[] = $a[$i];
                }
            }
        }
        $stock_all = array_unique($stock_all);
        // var_dump($stock_all);die;
        $stock_all_true = array_unique($stock_all_true);
        sort($stock_all);
        sort($stock_all_true);

        /*属性没有选择齐全的情况 */
        if(count($old_ga_id)<$attr_num && is_array($old_ga_id) && !empty($old_ga_id)){
            foreach ($old_ga_id as $key => $value) {
                $gaid = $value;
                $sql = "SELECT id FROM xgj_eu_goods_attr WHERE attr_id=(SELECT attr_id FROM xgj_eu_goods_attr WHERE id={$gaid}) AND goods_id={$goods_id}";
                $attr_list = M()->fetchAll($sql);
                $attr_list = array_map(function($v){return $v['id'];}, $attr_list);// 同属性的属性id
            // var_dump($stock_all_true);die;
                foreach ($attr_list as $key => $value) {
                    if(in_array($value,$stock_all_true)){
                        $stock_all[] = $value;
                    }
                }
            }
            $stock_all = array_unique($stock_all);
            sort($stock_all);
            // var_dump($stock_all);die;

            //计算库存数量
            $stock_total = $this->computeStock($goods_id);
        }elseif(empty($old_ga_id)){
            //计算库存数量
            $stock_total = $this->computeStock($goods_id);
        }else{
            /*选择了全部属性的情况*/
            //笛卡尔积查询字符串,一维数组
            $dkej_str = array();
            $arr1 = $ga_id;
            if(count($arr1)==1){//只有一组属性的情况
                $gaid = $ga_id[0];
                $sql = "SELECT id FROM xgj_eu_goods_attr WHERE attr_id=(SELECT attr_id FROM xgj_eu_goods_attr WHERE id={$gaid}) AND goods_id={$goods_id}";
                $attr_list = M()->fetchAll($sql);
                $attr_list = array_map(function($v){return $v['id'];}, $attr_list);// 同属性的属性id
                foreach ($attr_list as $key => $value) {
                    if(in_array($value,$stock_all_true)){
                        $stock_all[] = $value;
                    }
                }
                $stock_all = array_unique($stock_all);
                sort($stock_all);
            // var_dump($stock_all);die;

            }elseif(count($arr1)==2){//有两组属性的情况
                foreach ($arr1 as $key => $value) {
                    $gaid = $value;
                    $sql = "SELECT id FROM xgj_eu_goods_attr WHERE attr_id=(SELECT attr_id FROM xgj_eu_goods_attr WHERE id={$gaid}) AND goods_id={$goods_id}";
                    $attr_list = M()->fetchAll($sql);
                    $attr_list = array_map(function($v){return $v['id'];}, $attr_list);// 同属性的属性id
                    // var_dump($value);die;
                    foreach ($attr_list as $key => $value) {
                        if(in_array($value,$stock_all_true)){
                            $stock_all[] = $value;
                        }
                    }
                }
                $stock_all = array_unique($stock_all);
                sort($stock_all);
                // var_dump($stock_all);die;

            }elseif(count($arr1)==3){//有三组属性的情况
                for ($i=1,$len = count($arr1); $i < $len; ++$i) {
                    for ($j=0; $j <= $len - 1-$i; ++$j) {
                        if($i==1 && $j==0){
                            $dkej_str[] = $arr1[$j].','.$arr1[$j+1].',\d*?';
                        }elseif($i==1 && $j==1){
                            $dkej_str[] = $arr1[$j].',\d*?,'.$arr1[$j+1];
                        }elseif($i==2 && $j==0){
                            $dkej_str[] = '\d*?,'.$arr1[$j].','.$arr1[$j+1];
                        }
                        $temp = $arr1[$j];
                        $arr1[$j] = $arr1[$j+1];
                        $arr1[$j+1] = $temp;
                    }
                }
            }
            // var_dump($dkej_str);die;

            //通过笛卡尔积查询字符串 查询出相应的库存信息
            $arr2 = array();
            foreach ($dkej_str as $kk => $vv) {
                foreach ($stock as $kkk => $vvv) {
                    if(preg_match('/'.$vv.'/', $vvv['goods_attr_id'])){
                        $arr2[$vvv['id']] = $vvv;
                    }
                }
            }
            sort($arr2);

            //合并数据
            $arr3 = array();
            foreach ($arr2 as $k7 => $v7) {
                $arr3 = array_merge($arr3,explode(',',$v7['goods_attr_id']));
            }
            $stock_all = array_unique(array_merge($stock_all,$arr3));
            // var_dump($arr3);die;

            //计算库存数量
            $stock_total = $this->computeStock($goods_id,join(',',$old_ga_id));
        }



        //查询出所有的 商品属性信息
        $sql = "SELECT ga.*,a.name AS attr_name,a.mode FROM xgj_eu_goods_attr AS ga LEFT JOIN xgj_eu_attribute AS a ON ga.attr_id=a.id WHERE goods_id={$goods_id} AND a.mode=1 ORDER BY ga.id ASC";
        $ga_list = M()->fetchAll($sql);
        // var_dump($ga_list);die;

        //标识库存状态
        $ga_list_new = array();
        foreach ($ga_list as $k3 => &$v3) {
            if(in_array($v3['id'],$stock_all)){
                $v3['have_stock'] = 1;
            }else{
                $v3['have_stock'] = 0;
            }
            $ga_list_new[$v3['attr_id']][] = $v3;
        }

        //将key转换为连续的下标
        $i=0;
        $ga_list_new2 = array();
        foreach ($ga_list_new as $key => $value) {
            $ga_list_new2[$i++] = $value;
        }

        //调试

        //输出结果
        api(1,'success',array('list'=>$ga_list_new2,'stock_total'=>$stock_total));
    }

    public function getStock2(){
        /********************接受数据****************/
        //商品id
        $goods_id = I('goods_id',true);
        //商品属性id
        $ga_id = I('ga_id');
        if(empty($ga_id)) $ga_id = array();//防止$ga_id=''
        $ga_id = array_filter($ga_id);//防止空的参数
        sort($ga_id);//让下标连续
        // var_dump($ga_id);
        if(empty($goods_id)) api(-1,'参数错误');
        
        $stock = M('xgj_eu_stock')->where(array('goods_id'=>$goods_id,'number'=>array('gt',0)))->select();//该商品的所有库存
        if(empty($stock)) api(-2,'库存不足');
        if(count($stock)==1 && $stock[0]['number']<=0) api(-2,'库存不足');

        /******************基础数据*******************/
        //所有的商品属性数据
        $map['goods_id'] = $goods_id;
        $sql = "SELECT ga.*,g.name AS attr_name,g.mode FROM xgj_eu_goods_attr AS ga LEFT JOIN xgj_eu_attribute AS g ON ga.attr_id=g.id WHERE ga.goods_id='{$goods_id}' AND g.mode=1 ORDER BY ga.id ASC";
        $ga_list__ = M()->fetchAll($sql);
        $ga_list   = $ga_list_  = array();
        $i         = 0;
        foreach ($ga_list__ as $v)  $ga_list_[$v['attr_id']][] = $v;
        foreach ($ga_list_  as $v2) $ga_list[$i++] = $v2;
        unset($ga_list__,$ga_list_);

        //所有的库存数据$stock_list, $stock_id_list
        $stock_list = M('xgj_eu_stock')->where($map)->select();
        $stock_id_list = array();
        foreach ($stock_list as $s) {
            $s_ = explode(',', $s['goods_attr_id']);
            foreach ($s_ as $ss) {
                $stock_id_list[] = $ss;
            }
        }
        $stock_id_list = array_unique($stock_id_list);//库存id集合

        //商品属性的个数
        $sql = "SELECT ga.* FROM xgj_eu_goods_attr ga LEFT JOIN xgj_eu_attribute g ON ga.attr_id=g.id WHERE goods_id='{$goods_id}' AND g.mode=1 GROUP BY attr_id";
        $ga_num = count(M()->fetchAll($sql));

        //当前选择的第几个
        $which_one = -1;
        foreach ($ga_list as $v3) {
           $ga_id_list[] = array_map(function($v){return $v['id'];}, $v3);
        }
        if(count($ga_id)==1){
            foreach ($ga_id_list as $k=>$v4) {
                if(in_array($ga_id[0], $v4)){
                    $which_one = $k;
                    break;
                }
            }
        }elseif($ga_num==3 && count($ga_id)==2){
            if(in_array($ga_id[0], $ga_id_list[0]) && in_array($ga_id[1], $ga_id_list[1])){
                $which_one = 1;//12
            }elseif(in_array($ga_id[0], $ga_id_list[0]) && in_array($ga_id[1], $ga_id_list[2])){
                $which_one = 2;//13
            }elseif(in_array($ga_id[0], $ga_id_list[1]) && in_array($ga_id[1], $ga_id_list[2])){
                $which_one = 3;//23
            }
        }

        /****************************库存数据***************************/
        if($ga_num==1){//一个属性,
            //一个都没有选择和选择其中一个 算法一样
            //库存中的商品属性id集合
            $stock_id_list = array_map(function($v){return $v['goods_attr_id'];}, $stock_list);
            $this->processStock($ga_list[0],$stock_id_list);
        }elseif($ga_num==2){//两个属性
            if(empty($ga_id)){//一个都没有选择
                foreach ($ga_list as &$ga) {
                    $this->processStock($ga,$stock_id_list);
                }
            }else{//有选择
                if(count($ga_id)==1){//只选择了一个
                    /********根据选择的商品属性id查询出相应的库存数据$stock_line********/
                    if($which_one==0){//选择了第一个
                        $pattern = "/{$ga_id[0]},\d+/";
                    }elseif($which_one==1){//选择了第二个
                        $pattern = "/\d+,{$ga_id[0]}/";
                    }else{
                        api(-1,'非法参数');
                    }
                    $stock_id_list  = $this->computeStockId($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id[0]);
                    foreach ($ga_list as &$ga) {
                        $this->processStock($ga,$stock_id_list);
                    }
                }else{//全部都选择了
                    $pattern = "/{$ga_id[0]},\d+/";
                    $stock_id_list  = $this->computeStockId($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id[0]);
                    $pattern = "/\d+,{$ga_id[1]}/";
                    $stock_id_list2 = $this->computeStockId($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id[1]);
                    foreach ($ga_list as &$ga) {
                        $this->processStock($ga,$stock_id_list);
                    }
                }
                
            }
        }elseif($ga_num==3){
            if(empty($ga_id)){//一个都没有选择
                foreach ($ga_list as &$ga) {
                    $this->processStock($ga,$stock_id_list);
                }
            }else{
                if(count($ga_id)==1){//只选择了一个
                    if($which_one==0){//选择了第一个
                        $pattern = "/{$ga_id[0]},\d+,\d+/";
                    }elseif($which_one==1){//选择了第二个
                        $pattern = "/\d+,{$ga_id[0]},\d+/";
                    }elseif($which_one==2){//选择了第三个
                        $pattern = "/\d+,\d+,{$ga_id[0]}/";
                    }else{
                        api(-1,'非法参数');
                    }
                    $stock_id_list  = $this->computeStockId($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id[0]);
                    foreach ($ga_list as &$ga) {
                        $this->processStock($ga,$stock_id_list);
                    }
                }elseif(count($ga_id)==2){
                    // $pattern = "/{$ga_id[0]},{$ga_id[1]},\d+/";
                    if($which_one==1){
                        $pattern = "/{$ga_id[0]},{$ga_id[1]},\d+/";
                    }elseif($which_one==2){
                        $pattern = "/{$ga_id[0]},\d+,{$ga_id[1]}/";
                    }elseif($which_one==3){
                        $pattern = "/\d+,{$ga_id[0]},{$ga_id[1]}/";
                    }
                    $stock_id_list  = $this->computeStockId2($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id);
                    foreach ($ga_list as &$ga) {
                        $this->processStock($ga,$stock_id_list);
                    }
                }else{//全部都选择了
                    $pattern = "/{$ga_id[0]},\d+,\d+/";
                    $stock_id_list  = $this->computeStockId($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id[0]);
                    $pattern = "/\d+,{$ga_id[1]},\d+/";
                    $stock_id_list2 = $this->computeStockId($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id[1]);
                    $pattern = "/\d+,\d+,{$ga_id[2]}/";
                    $stock_id_list3 = $this->computeStockId($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id[2]);
                    $stock_id_list  = array_intersect($stock_id_list, $stock_id_list2, $stock_id_list3);
                    foreach ($ga_list as &$ga) {
                        $this->processStock($ga,$stock_id_list);
                    }
                }
            }

        }

        /**计算库存**/

        if($ga_num == count($ga_id)){
            $goods_attr_str = join(',', $ga_id);
            $stock_total = (int) M('xgj_eu_stock')->where(array('goods_id'=>$goods_id,'goods_attr_id'=>$goods_attr_str))->getField('number');
        }else{
            $stock_total = 1;
        }
        api(1,'success',array('list'=>$ga_list,'stock_total'=>$stock_total));
    }


    private function computeStockId2($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id){
        $stock_line = array();
        foreach ($stock_list as $vv) {
            if(preg_match($pattern, $vv['goods_attr_id'])){
                $temp = explode(',',$vv['goods_attr_id']);
                foreach ($temp as $key => $v) {
                    $stock_line[] = $v;
                }
            }
        }
        $stock_line = array_unique($stock_line);
        $sql = "SELECT * FROM xgj_eu_goods_attr WHERE goods_id='{$goods_id}' AND attr_id = (SELECT attr_id FROM xgj_eu_goods_attr WHERE id='{$ga_id[0]}') ORDER BY id ASC";
        $ga_sib_list1 = M()->fetchAll($sql);
        $sql = "SELECT * FROM xgj_eu_goods_attr WHERE goods_id='{$goods_id}' AND attr_id = (SELECT attr_id FROM xgj_eu_goods_attr WHERE id='{$ga_id[1]}') ORDER BY id ASC";
        $ga_sib_list2 = M()->fetchAll($sql);
        $ga_sib_list_1 = array_map(function($v){return $v['id'];},$ga_sib_list1);
        $ga_sib_list_2 = array_map(function($v){return $v['id'];},$ga_sib_list2);

        $stock_none     = array_diff($ga_sib_list_1, $stock_id_list);
        $stock_id_list_ = array_merge($stock_line,$ga_sib_list_1);
        $stock_id_list__  = array();
        foreach ($stock_id_list_ as &$vvv) {
            if(!in_array($vvv,$stock_none)){
                $stock_id_list__[] = $vvv;
            }
        }
        $stock_none     = array_diff($ga_sib_list_2, $stock_id_list);
        $stock_id_list_ = array_merge($stock_line,$ga_sib_list_2);
        // $stock_id_list  = array();
        foreach ($stock_id_list_ as &$vvv) {
            if(!in_array($vvv,$stock_none)){
                $stock_id_list__[] = $vvv;
            }
        }
        $stock_id_list = $stock_id_list__;
        return $stock_id_list;
    }
    //传入一个商品属性id,计算库存id集合
    private function computeStockId($stock_list,$stock_id_list,$pattern,$goods_id,$ga_id){
        $stock_line = array();
        foreach ($stock_list as $vv) {
            if(preg_match($pattern, $vv['goods_attr_id'])){
                $temp = explode(',',$vv['goods_attr_id']);
                foreach ($temp as $key => $v) {
                    $stock_line[] = $v;
                }
            }
        }

        /********查询出相同属性的同辈属性$ga_sib_list********/
        $sql = "SELECT * FROM xgj_eu_goods_attr WHERE goods_id='{$goods_id}' AND attr_id = (SELECT attr_id FROM xgj_eu_goods_attr WHERE id='{$ga_id}') ORDER BY id ASC";
        $ga_sib_list = M()->fetchAll($sql);
        $ga_sib_list = array_map(function($v){return $v['id'];},$ga_sib_list);
        // var_dump($ga_sib_list);die;

        /********排除掉全部商品属性数据中没有的属性$ga_sib_list********/
        $stock_none     = array_diff($ga_sib_list, $stock_id_list);
        $stock_id_list_ = array_merge($stock_line,$ga_sib_list);
        $stock_id_list  = array();
        foreach ($stock_id_list_ as &$vvv) {
            if(!in_array($vvv,$stock_none)){
                $stock_id_list[] = $vvv;
            }
        }
        return array_unique($stock_id_list);
    }

    private function processStock(&$ga_list, $stock_id_list){
        foreach ($ga_list as &$ga) {
            if(in_array($ga['id'], $stock_id_list)){
                $ga['have_stock'] = 1;
            }else{
                $ga['have_stock'] = 0;
            }
        }
        return $ga_list;
    }

    /*
    计算库存数量
     */
    public function computeStock($goods_id, $goods_attr_id=''){
        $sql = "SELECT SUM(`number`) AS total FROM xgj_eu_stock WHERE goods_id='{$goods_id}'";
        if(!empty($goods_attr_id)){
            $sql .= " AND goods_attr_id='{$goods_attr_id}'";
        }
        $total = M()->fetchColumn($sql,'total');
        return $total?$total:0;
    }

    /*
    ajax关注商品
     */
    public function concern(){

        //验证登陆
        $user_id = session('userId');
        if(empty($user_id)){
            return api(-1,'必须登录之后才能关注');
        }

        //没有传入商品的id
        $id = I('get.id',true);
        $class_id = I('get.class_id',true);
        if(empty($id)){
            return api(-1,'参数错误');
        }
        if(!empty($class_id) && $class_id==3){
            //获取商品数据
            $goods                = M('xgj_ov_goods')->find($id);
            
            //拼凑关注表中的数据
            $data['goods_id']     = $id;
            $data['class_id']     = 3;
            $data['user_id']      = $user_id;
            $data['c_images']     = $goods['face_image'];
            $data['c_goodsname']  = $goods['goods_title'];
            $data['c_goodsprice'] = $goods['purchase'];
            //取消关注的情况
            if(M('xgj_concern')->where(array('goods_id'=>$id,'user_id'=>$user_id,'class_id'=>3))->count()>0){
                if(M('xgj_concern')->where(array('goods_id'=>$id,'user_id'=>$user_id,'class_id'=>3))->delete()){
                    return api(2,'success');
                }else{
                    return api(-1,'系统错误1');
                }
            }
        }else{
            //获取商品数据
            $goods                = M('xgj_eu_goods_new')->find($id);

            //拼凑关注表中的数据
            $data['goods_id']     = $id;
            $data['class_id']     = 2;
            $data['user_id']      = $user_id;
            $data['c_images']     = $goods['face_image'];
            $data['c_goodsname']  = $goods['goods_title'];
            $data['c_goodsprice'] = $goods['purchase'];
            //取消关注的情况
            if(M('xgj_concern')->where(array('goods_id'=>$id,'user_id'=>$user_id,'class_id'=>2))->count()>0){
                if(M('xgj_concern')->where(array('goods_id'=>$id,'user_id'=>$user_id,'class_id'=>2))->delete()){
                    return api(2,'success');
                }else{
                    return api(-1,'系统错误1');
                }
            }
        }        

        //添加关注的情况
        if(M('xgj_concern')->data($data)->add()){
            return api(1,'success');
        }else{
            return api(-1,'系统错误2');
        }
    }

    /*
    ajax获取评论
     */
    public function getCommentList(){
        $goods_id = I('id',true);
        if(empty($goods_id)) api(-1,'参数错误');

        $map['goods_id']    = $goods_id;
        $total              = M('xgj_eu_comment')->where($map)->count();
        $page               = getAjaxPage($total, C('DETAIL_COMMENT_PAGE_SIZE'));
        $data['comm_list']  = D('Goods')->getCommentList($goods_id, $page['limit']);
        $data['total']      = $total;
        $data['total_page'] = $page['total_page'];
        api(1, 'success', $data);
    }

    /*
    ajax获取评论
     */
    public function getCommentList2(){
        $goods_id = I('id',true);
        if(empty($goods_id)) api(-1,'参数错误');

        $map['goods_id']    = $goods_id;
        $total              = M('xgj_ov_comment')->where($map)->count();
        $page               = getAjaxPage($total, C('DETAIL_COMMENT_PAGE_SIZE'));
        $data['comm_list']  = D('Baby')->getCommentList($goods_id, $page['limit']);
        $data['total']      = $total;
        $data['total_page'] = $page['total_page'];
        //var_dump($data);die;
        api(1, 'success', $data);
    }

    /*
    商品详情页面, ajax总入口
     */
    public function detailInit(){
        $goods_id = I('id',true);
        if(empty($goods_id)) api(-1,'参数错误');

        //商品的关注信息
        $data['is_concern'] = $this->isConcern($goods_id);


        api(1,'success',$data);
    }

    /*
    是否已经关注, 返回 1或者0
     */
    public function isConcern($goods_id){
        if($user_id = session('userId')){
            $map['user_id']   = $user_id;
            $map['goods_id']  = $goods_id;
            $concern_count    = M('xgj_concern')->where($map)->count();
            $concern = $concern_count>0?'1':'0';
        }else{
            $concern = '0';
        }
        return $concern;
    }

    /*
    添加购物车
     */
    public function addCart(){
        if(!(isset($_GET['id']) && isset($_GET['amount']))){
            api(-1,'参数错误');
        }
        if(D('Cart')->addCart()){
            api(1,'success');
        }else{
            api(-2,'未知错误');
        }
    }
    /*
    添加海外购物车
     */
    public function addOvCart(){
        if(!(isset($_GET['id']) && isset($_GET['amount']))){
            api(-1,'参数错误');
        }
        if(D('Cart')->addOvCart()){
            api(1,'success');
        }else{
            api(-2,'未知错误');
        }
    }

    /*
    获取头部所有的数据
     */
    public function getHeader(){
        /*获取购物车数量*/
        $data['cart_total'] = D('Cart')->getTotal();

        api(1,'success',$data,'');
    }
	    /*
    确认订单时验证是否填写身份信息
     */
    public function realname(){
        /*获取购物车数量*/
         $userInfo = M()->fetch('SELECT real_name,identity_card,coupon,integral FROM xgj_users WHERE user_id='.$_SESSION['userId']);//会员信息
		 if(empty($userInfo['real_name']) || empty($userInfo['identity_card'])){
               api(-1,'请先完善身份信息');
        }else
				api(1,'success');
    }
}