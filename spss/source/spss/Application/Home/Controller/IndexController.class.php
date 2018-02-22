<?php
namespace Home\Controller;

class IndexController extends BaseController {

	private $pageNum = 20; //首页搜索每页显示数
    
    public function index(){
    	$list = D('Index')->getBanner(3);
    	$this->assign("list",$list);
        $this->display();
    }

	public function aboutus(){
		$histroy=D('Index')->article(23);
		$cbrandList=D('Index')->getCbrandAll(1);
	//	var_dump($cbrandList);die();
		$this->assign("aboutus_list",$histroy);
		$this->assign('cbrandList',$cbrandList);
        $this->display();
    }
		//合作品牌详情页面
	function cbrand(){
		
		$id=intval(I('id'));
		//分页每页的条数
		

		//var_dump($show);die();
		//合作品牌图册
		$cbrandImage=D('Index')->getCbrandImage($id);
		//合作品牌
		$cbrandInfo=D('Index')->getCbrandOne($id);
		$cbrandInfo['logo']=getImage($cbrandInfo['logo']);
		$cbrandInfo['product']=explode('|',$cbrandInfo['product']);

		

		//产品应用
		$num=1;
		$p = 1;
		$list=D('Index')->getCbrandList($id,$p,$num);//显示列表内容
		$count=D('Index')->getCbrandCount($id);//分页的总条数
		//总页数
		$pcount = ceil($count/$num);
		$this->assign('pcount',$pcount);
//		var_dump($list);die();
		$this->assign("p",$p);
		$this->assign('cbrandImage',$cbrandImage);
		$this->assign('cbrandInfo',$cbrandInfo);
		$this->assign('list',$list);
		$this->display();
	}
	public function ajaxCbrand(){
	
		layout(false);
		$page = $_GET['page'];
		$id = $_GET['id'];
		$num = 1;
		$list=D('Index')->getCbrandList($id,$page,$num);//显示列表内容
		$count=D('Index')->getCbrandCount($id);//分页的总条数
		$pcount = ceil($count/$num);
		
        if (!empty($list)){
        	foreach($list as $k => $v){
    		echo '
				<div class="cbrandProApp_left"><img src="'.getImage($v['image']).'" width="360" height="240"></div>
				<div class="cbrandProApp_right">'.     
					htmlspecialchars_decode($v['content']).
				'</div>';
        	}
		}
        echo '                
            <!--分页开始-->
            <div class="page clear">';
              if (!empty($page)){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.',1)">首页</a>';  
	            if ($page==1) {
              		echo '<a href="javascript:;">[上一页]</a>';
              	}else{
              		echo '<a href="javascript:;" onclick="page('.$id.','.($page-1).')">[上一页]</a>';
              	}
	          }
              if ($page > 2){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.','.($page-2).')">'.($page-2).'</a>';
              }
              if ($page > 1){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.','.($page-1).')">'.($page-1).'</a>' ;
              }
              echo '<span>'.$page.'</span>';
              if ($pcount > $page){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.','.($page+1).')">'.($page+1).'</a>';
              }
              if ($pcount > ($page+1)){
	            echo '
	              <a href="javascript:;" onclick="page('.$id.','.($page+2).')">'.($page+2).'</a>';
              }
              if (!empty($page)){
              	if ($page<$pcount) {
              		echo '<a href="javascript:;" onclick="page('.$id.','.($page+1).')">[下一页]</a>';
              	}else{
              		echo '<a href="javascript:;">[下一页]</a>';
              	}
				echo '
	              <a href="javascript:;" onclick="page('.$id.','.$pcount.')">[尾页]</a>';
              }
        echo '        
            </div>
			<div class="clear2"></div>
            <!--分页结束-->       
		';
	}
	/**
	 * 开发商合作案例
	 **/
	public function developer(){		
		
		$data = D('Index')->article_cat(33);
		foreach($data as $key =>$val){
			$data[$key]['list']=D('Index')->article($val['cat_id']);
		}
		//var_dump($data);die();
		$this->assign('data',$data);
		$this->display();
	}
    	//全国展示中心
	function show(){
		
		
		$city = urldecode(I('get.city'));
		if(!empty($city))
			$where['d_city|d_service_city_all'] =array('like',"%$city%");
		
		
		$count = M('xgj_furnish_dealer')->where($where)->count();
	
		$show       = getPage($count,12);
	
		$list = M('xgj_furnish_dealer')->where($where)->limit($show['limit'])->select();

		//全国经销商分布省市
		$mapcity = M('xgj_furnish_dealer')->select();
		if(!empty($mapcity)){
				foreach ($mapcity as &$v2) {
					//去除重复市
					if(@!in_array($v2['d_city'], $district[$v2['d_province']])){
						$district[$v2['d_province']][] = $v2['d_city'];
					}
				}
		}
		

		foreach($list as $k =>&$val){

				$result = M('xgj_furnish_dealer_img')->where(array('d_id'=>$val['d_id'],'is_show'=>1))->select();
				
				foreach($result as $key =>$v){
						$v['url'] = getImage($v['url']);
						$val['images'][] = $v;				
				}
				
				
		}
		
		$this->assign('district', $district);
		$this->assign('dealer', $list);
		$this->assign('page', $show['page']);
		$this->display();
	}
	
	//首页搜索
	public function search(){
		$search = I('get.search');

		//查询家居
		$fumap['quote_name'] = array('like',"%$search%");
		$fumap['is_putaway'] = 1;
		$list['fu'] = M('xgj_furnish_quote')->field('quote_id,img,quote_name')->where($fumap)->select();

		/********************************************************/
		//查询建材
		//根据建材商品名称查询
		$eumap['goods']['goods_title'] = array('like',"%$search%");
		$eumap['goods']['is_putaway']  = 1;
		$list['eu']['goods'] = M('xgj_eu_goods_new')->field('id,face_image,goods_title')->where($eumap['goods'])->select();

		//根据建材属性名称查询
		$eumap['attr']['t.name'] = array('like',"%$search%");
		$eumap['attr']['g.is_putaway'] = 1;
		$list['eu']['attr'] = M('xgj_eu_type t')->join('xgj_eu_goods_new g on t.id=g.type_id')->field('g.id,g.face_image,g.goods_title')->where($eumap['attr'])->select();

		//将上述2个查询到的结果放在一起
		$list['eu'] = array_merge($list['eu']['goods'],$list['eu']['attr']);
		/********************************************************/

		//查询机电耗材
		$scmap['c_name|product_name'] = array('like',"%$search%");
		$scmap['is_put']              = 1;
		$list['sc'] = M('xgj_s_consumable')->field('id,c_img,c_name')->where($scmap)->select();

		//将上面数组放在一起
		$lists = array_merge($list['fu'],$list['eu'],$list['sc']);

		//分页  
		$count = count($lists);          //总数
		$page  = getPage($count,$this->pageNum);	 

		//获取当前分页数
		$p = I('get.p');	
		$pmax = ceil($count/$this->pageNum);
		if ($p<1 || empty($p)) $p = 1;
		else if($p>$pmax) 	   $p = $pmax;

		//截取当前分页对应的分页数据
		$start = ($p-1)*$this->pageNum;
		$data = array_slice($lists, $start,$this->pageNum);
	   
		$this->assign('page',$page['page']);
		$this->assign('search',$search);
		$this->assign('list',$data);
		$this->display();
	}

	//查看文章
	public function checkArticle(){
		$cat_id=I('cat_id')?I('cat_id'):'';
		$Data=D('Index')->getArticle($cat_id);
		$a_id=I('a_id')?I('a_id'):$Data['info'][0]['article_id'];
		$Info=D('Index')->getArticleOne($a_id);
		$this->assign('data',$Data);
		$this->assign('Info',$Info);
		$this->display();
	}
}