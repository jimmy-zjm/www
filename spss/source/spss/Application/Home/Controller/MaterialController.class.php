<?php
namespace Home\Controller;

class MaterialController extends BaseController {

    private $pageNum = 15;//分页 每页显示数
    private $infoPageNum = 5;//详情分页 每页显示数


    public function detail(){

        // $this->assign('cateList',$cateList); 
        $this->display();
    }

    //建材列表页
    public function floorList(){
    	/********************/
    	//查询顶级分类
  		$map['pid']      = array('EQ',0);
  		$map['class_id'] = array('EQ',1);
  		$map['is_show']  = array('EQ',1);
    	$top = M('xgj_eu_category')->where($map)->order('`order`')->select();
    	/********************/


    	/********************/
    	//子级分类
    	foreach ($top as $k => $v) {
    		$map['pid'] = array('EQ',$v['id']);
    		$list[$v['name']] = M('xgj_eu_category')->field('id,name')->where($map)->order('`order`')->select();
    	}
    	/********************/


    	/********************/
    	//查询所选择建材商品
      $pId = I('get.pId');
      if (!empty($pId)) $goodsMap['c.pid'] = array('EQ',$pId);
      // $goodsMap['g.class_id']           = array('EQ',1);
      $goodsMap['g.is_putaway']            = array('EQ',1);
      $goodsMap['g.is_delete']             = array('EQ',0);
    	/********************/

    	
    	/********************/
    	//存在搜索条件就加入条件查询
    	$keyword = I('get.keyword');
    	if (!empty($keyword)) $goodsMap['g.goods_title'] = array('like',"%$keyword%");

    	$cate_id = I('get.id');
    	if (!empty($cate_id)) $goodsMap['g.cate_id']     = array('EQ',$cate_id);
    	/********************/


    	/********************/
    	//分页  
      $count  = M('xgj_eu_category c')->join('xgj_eu_goods_new g on c.id = g.cate_id')->where($goodsMap)->count();

      $page   = getPage($count,$this->pageNum);
      $data   = M('xgj_eu_category c')->join('xgj_eu_goods_new g on c.id = g.cate_id')->field('g.id,goods_title,market_price,shop_price,face_image,c.order')->where($goodsMap)->limit($page['limit'])->order('c.order')->select();
      /********************/	

      /**********************/
      //查询头部广告
      $map['is_on']     = 1;
      $map['ad_pos_id'] = 14;
      $image = M('xgj_ad')->where($map)->getField('image');
      $this->assign('image',getimage($image)); 
      /**********************/
        

    	$this->assign('list',$list);
    	$this->assign('data',$data);
    	$this->assign("page",$page['page']);

    	$this->display();
    }

    //详情页
    public function info(){
    	$id = I('id');

    	/***************************/
    	//查询商品信息
    	$dataMap['is_putaway'] = '1';
    	$dataMap['id'] 		     = $id;
    	$data = M('xgj_eu_goods_new')->where($dataMap)->find();

    	//商品不存在返回
    	if (empty($data)) {
    		layout(false);
    		$this->error('抱歉！没有您要的商品');
    	}

    	//查询商品图片册
    	$imgMap['goods_id'] = $id;
    	$imgMap['is_show']  = '1';
    	$img  = M('xgj_eu_image')->field('url')->where($imgMap)->select();

    	//查询商品属性
      $list = M('xgj_eu_attribute')->where("type_id = '{$data['type_id']}'")->select();
  		$attr = M('xgj_eu_goods_attr')->where("goods_id = '{$id}'")->select();
   
  		//处理商品属性
  		foreach ($list as $k => $v) {
      	$list[$k]['val'] = explode(',', $v['value_list']);
        $listAttr[$v['id']] = $v;
      }
      foreach ($attr as $k => $v) {
        if ($v['attr_price']>0) $price[$v['id']] = $v["attr_price"];
        if (!empty($v['attr_value'])) $listAttr[$v['attr_id']]['attr'][] = $v;
      }
  		/***************************/

  		/**********************/
  		//查询登录状态下是否收藏并查询收藏总量
  		if (!empty($_SESSION['user']['userId'])) {
    			$userId  = $_SESSION['user']['userId'];
    			$collect = M('xgj_concern')->where(array('goods_id'=>$id,'user_id'=>$userId,'class_id'=>2))->count();
    			$this->assign('collect',$collect);
  		}
  		//收藏总数
  		$count   = M('xgj_concern')->where(array('goods_id'=>$id,'class_id'=>2))->count();
  		/************************/


  		/************************/
  		//评论
  		$commentMap['c.goods_id'] = $id;
  		$commentMap['c.display']  = '1';
    	//分页  
      $commentCount = M('xgj_eu_comment c')->field('star')->where($commentMap)->select();
      $page   	    = getAjaxPage(count($commentCount),$this->infoPageNum);
		  $comment 	    = M('xgj_eu_comment c')->join("xgj_users u on c.user_id=u.user_id")->where($commentMap)->limit($page['limit'])->order('c.add_time desc')->select();
      //处理评论晒图
      foreach ($comment as $k => $v) {
        if ($v['images']) $comment[$k]['imgArray'] = explode('|', $v['images']);
      }
 		  //计算好评度
  		$star = 0;
  		foreach ($commentCount as $k => $v) {
  			$star += $v['star'];
  		}
  		$star = ceil($star/(count($commentCount)*5)*100);
  		/************************/


  		/************************/
    	//促销专区
    	$goodsMap['is_putaway'] = '1';
  		$goodsMap['cate_id']    = $data['cate_id'];
  		$goodsMap['id']    		  = array('NEQ',$id);
  		$goods = M('xgj_eu_goods_new')->field('id,goods_title,market_price,shop_price,face_image')->where($goodsMap)->limit(20)->select();
  		//打乱数组
  		shuffle($goods);
  		//取前5条数组
  		$goods = array_slice($goods,0,5);
  		/************************/

      $this->assign('price',$price);
    	$this->assign('star',$star);
    	$this->assign('page',$page['page']);
      $this->assign('comment',$comment);
    	$this->assign('commentCount',count($commentCount));
    	$this->assign('goods',$goods);
    	$this->assign('count',$count);
    	$this->assign('list',$listAttr);
    	$this->assign('data',$data);
    	$this->assign('img',$img);
    	$this->display();
    }

    public function page(){
    	layout(false);
    	$id = I('get.id');
    	$map['c.goods_id'] = $id;
  		$map['c.display']  = '1';
    	$count   = M('xgj_eu_comment c')->field('star')->where($map)->count();
      $page  	 = getAjaxPage($count,$this->infoPageNum);

  		$comment = M('xgj_eu_comment c')->join("xgj_users u on c.user_id=u.user_id")->field('c.*,u.face')->where($map)->limit($page['limit'])->order('c.add_time desc')->select();
      //处理评论晒图
      foreach ($comment as $k => $v) {
        if ($v['images']) $comment[$k]['imgArray'] = explode('|', $v['images']);
      }
  		$data = '';

  		foreach ($comment as $k => $v) {
  			$data .= "<div class='tabs2-con2'><p class='tabs2-con2-1'><span class='tabs2-con2-1-txt1'>".$v['content']."</span>";
  			$data .= "<span class='tabs2-con2-1-img1'>";
        foreach ($v['imgArray'] as $key => $val) {
          $data .= "<img class='activeimg' onclick='activeimg(this)' src=__PUBLIC__/Uploads/".$val .">";
        }
        $data .= "</span>";
  			$data .= "<span class='tabs2-con2-1-img2'><img class='bigimg' src=''/></span></p><p class='tabs2-con2-2'><span>";
  			for ($i=0; $i < $v['star']; $i++) {
  				$data .= "★";
  			}
  			$data .= "</span>";
  			$data .= "<span>".date('Y-m-d H:i:s',$v['add_time'])."</span>";
  			$data .= "</p>";
  			$data .= " <p class='tabs2-con2-3'>";
  			$data .= "<span><img src='__PUBLIC__/Uploads/'".$v['face']."' width='60' height='60'></span>";
  			$data .= "<span>".$v['user_name']."</span>";
  			$data .= "</p>";
  			$data .= "</div>";
  		}
      $data .= $page['page'];
      $data .= "
      <script>
        $('.page a').click(function(){
          var p = $(this).attr('data-page');
          $.get('".U('page')."',{'id':".$id.",'p':p},function(data){
            $('#page').html(data);
          })
        })
      </script>";
  		echo $data;
    }


    //收藏
    public function collect(){
    	if (empty($_SESSION['user']['userId'])) {
    		echo '请先登录再收藏';
    	}else{
    		$id = I('post.id');
        $attr = I('post.attr');
	    	$userId = $_SESSION['user']['userId'];

        $dataMap['is_putaway'] = '1';
        $dataMap['id']         = $id;
        $data = M('xgj_eu_goods_new')->field('type_id,shop_price,goods_title,face_image')->where($dataMap)->find();

        //商品不存在返回
        if (empty($data)) die('抱歉！没有您要的商品');
        /***************************/

        /***************************/
        //查询商品属性是否存在
        foreach ($attr as $k => $v) {
          $rowMap['a.id']       = $v['1'];
          $row = M('xgj_eu_goods_attr a')->join('xgj_eu_attribute b on a.attr_id = b.id')->field('a.*,b.name')->where($rowMap)->find();
          
          if (empty($row) || $row['goods_id']!=$id || $row['attr_id']!=$v['0']) die('您选择的'.$row['name'].'不存在');
        }
        /***************************/


	    	if (M('xgj_concern')->where(array('goods_id'=>$id,'user_id'=>$userId,'class_id'=>2))->count()>0) {
	    		echo '您已收藏此商品！';
	    	}else{
	    		$re = M('xgj_eu_goods_new')->field('face_image,goods_title,shop_price')->where("id=$id")->find();
		    	$data = array(
					'class_id'     => 2,
					'user_id'      => $userId,
					'goods_id'     => $id,
					'c_images'     => $re['face_image'],
					'c_goodsname'  => $re['goods_title'],
					'c_goodsprice' => $re['shop_price'],
          'attr'         => json_encode($attr),
		    		);

  		    //添加关注的情况
          if(!M('xgj_concern')->data($data)->add()) echo '收藏失败';
          else echo '1';
	    	}
    	}
    }


    //加入购物车
    public function goCart(){
      //查询是否登录
      $tz   = I('post.tz');
      if(empty($_SESSION['user']['userId']) && $tz==1) die('login');

      if(empty($_SESSION['user']['userId'])) die('请先登录再加入购物车');

      /***************************/
      //查询商品是否存在
      $id   = I('post.id');
      $num  = I('post.num');
      $attr = I('post.attr');

      if ($num < 1) die('数量不能小于1');

      $dataMap['is_putaway'] = '1';
      $dataMap['id']         = $id;
      $data = M('xgj_eu_goods_new')->field('type_id,shop_price,goods_title,face_image')->where($dataMap)->find();

      //商品不存在返回
      if (empty($data)) die('抱歉！没有您要的商品');
      /***************************/


      /***************************/
      //查询商品属性是否存在
      foreach ($attr as $k => $v) {
        $rowMap['a.id']       = $v['1'];
        $row = M('xgj_eu_goods_attr a')->join('xgj_eu_attribute b on a.attr_id = b.id')->field('a.*,b.name')->where($rowMap)->find();
        
        if (!empty($row) && $row['goods_id']==$id && $row['attr_id']==$v['0']) {
          //如存在处理商品属性 
          $addAttr[$row['name']] = $row['attr_value'];
          $attr_price[]          = $row['attr_price'];
        }else{
          die('您选择的'.$row['name'].'不存在');
        }
      }

      $price = array_sum($attr_price);
      /***************************/


      /***************************/
      //加入购物车表
      $addData = array(
        'user_id'   =>$_SESSION['user']['userId'],
        'cat_id'    =>$id,
        'price'     =>$data['shop_price']+$price,
        'class'     =>2,
        'attr'      =>json_encode($addAttr),
        'img'       =>$data['face_image'],
        );

        
      //查询购物车内是否有此商品 有就更新数量，没有就添加
      $is = M('xgj_furnish_cart')->where($addData)->find();
      if (empty($is)) {
        $addData['num']       = $num;
        $addData['shop_name'] = $data['goods_title'];
        $return = M('xgj_furnish_cart')->add($addData);
      }else{
        $saveMap['user_id'] = $_SESSION['user']['userId'];
        $saveMap['cart_id'] = $is['cart_id'];
        $save['num'] = $is['num']+$num;
        $return = M('xgj_furnish_cart')->where($saveMap)->save($save);
      }

      //返回结果
      if ($return>0) echo  empty($is)?$return:$is['cart_id'];
      else echo '添加失败';
      /***************************/
    }

    //关注的欧洲建材商品加入购物车
    public function AddCart(){
      //查询是否登录
      if(empty($_SESSION['user']['userId'])) die('请先登录再加入购物车');

      /***************************/
      //查询商品是否存在
      $id   = I('post.goods_id');
      $num  = I('post.num');
      $c_id = I('post.id');

      if ($num < 1) die('数量不能小于1');

      $dataMap['is_putaway'] = '1';
      $dataMap['id']         = $id;
      $data = M('xgj_eu_goods_new')->field('type_id,shop_price,goods_title,face_image')->where($dataMap)->find();

      //商品不存在返回
      if (empty($data)) die('抱歉！没有您要的商品');
      /***************************/

      $attr = M('xgj_concern')->where(array("c_id"=>$c_id))->getField('attr');

      /***************************/
      //查询选择商品属性后是否改变单价
      $attrMap['goods_id']   = $id;
      $attrMap['attr_price'] = array('NEQ',0);
      foreach ($attr as $k => $v) {
        $attrMap['attr_value'] = $v;
        $attr_price[] = M('xgj_eu_goods_attr')->where($attrMap)->getField('attr_price');
      }
      $price = array_sum($attr_price);
      /***************************/

      /***************************/
      //加入购物车表
      $addData = array(
        'user_id'   =>$_SESSION['user']['userId'],
        'cat_id'    =>$id,
        'price'     =>$data['shop_price']+$price,
        'class'     =>2,
        'attr'      =>$attr,
        'img'       =>$data['face_image'],
        );

      //查询购物车内是否有此商品 有就更新数量，没有就添加
      $is = M('xgj_furnish_cart')->where($addData)->find();
      if (empty($is)) {
        $addData['num']       = $num;
        $addData['shop_name'] = $data['goods_title'];
        $return = M('xgj_furnish_cart')->add($addData);
      }else{
        $saveMap['user_id'] = $_SESSION['user']['userId'];
        $saveMap['cart_id'] = $is['cart_id'];
        $save['num'] = $is['num']+$num;
        $return = M('xgj_furnish_cart')->where($saveMap)->save($save);
      }

      //返回结果
      if ($return>0) echo  empty($is)?$return:$is['cart_id'];
      else echo 2;
      /***************************/
    }
}