<?php
namespace Admin\Model;
/**
 * 分页类
 */
final class Page{
    //当前url
    private $url;
    //总记录数
    private $total;
    //每页数量
    private $size = 5;
    //当前页码
    private $page = 1;
    //总页码数
    private $amount;
    //首页
    private $first;
    //上一页
    private $prev;
    //下一页
    private $next;
    //最后一页
    private $end;
    //limit
    public $limit;

    //自定义配置
    private $config;

    //url get参数
    private $query_string;


    public function __construct($total, $size, $config=array('info'=>1,'first'=>1,'prev'=>1,'num'=>1,'next'=>1,'end'=>1,'jump'=>1)){
        $this->total = $total<0?0:$total;
        $this->size  = $size<0?5:$size;
        $this->config = $config;
        $this->compute();
    }
    public function compute(){
        $this->url    = $_SERVER['PHP_SELF'];//获取当前url的地址

        //获取地址栏中的url参数,并将地址栏中的page参数去除
        $this->query_string    = preg_replace('/page=\d&?/', '', $_SERVER['QUERY_STRING']);

        //总页数
        $this->amount = ceil($this->total/$this->size);

        //当前页数
        $this->page   = (int)isset($_GET['page'])?$_GET['page']:1;
        $this->page   = $this->page<=0?1:($this->page>=$this->amount?$this->amount:$this->page);

        $this->first  = 1;//首页
        $this->prev   = $this->page-1;
        $this->prev   = $this->prev<=0?1:$this->prev;//下一页
        $this->next   = $this->page+1;//下一页
        $this->next   = $this->next>$this->amount?$this->amount:$this->next;
        $this->end    = $this->amount;//最后一页

        //用于sql查询的limit语句
        $first_row = ($this->page-1)*$this->size;
        $first_row = $first_row<0?0:$first_row;
        $this->limit  = $first_row .','. $this->size;
    }
    public function show(){
        $str = "<div id='turn-page'>";
        if(isset($this->config['info']))
            $str.= $this->info();
        if(isset($this->config['first']))
            $str.= $this->first();
        if(isset($this->config['prev']))
            $str.= $this->prev();
        if(isset($this->config['num']))
            $str.= $this->num();
        if(isset($this->config['next']))
            $str.= $this->next();
        if(isset($this->config['end']))
            $str.= $this->end();
        if(isset($this->config['jump']))
            $str.= $this->jump();
        $str.= '</div>';
        return $str;
    }
    //页码信息
    private function info(){
        return "总记录数:{$this->total} | 当前位置:{$this->page}/{$this->amount} | ";
    }
    //首页
    private function first(){
        if($this->page==1) return "[首页]";
        return "<a href='{$this->url}?page={$this->first}&{$this->query_string}'>[首页]</a>";
    }
    //上一页
    private function prev(){
        if($this->page==1) return "[上一页]";
        return "<a href='{$this->url}?page={$this->prev}&{$this->query_string}'>[上一页]</a>";
    }
    //数字页码
    private function num(){
        $str = '';
        for ($i=1; $i <= $this->amount; $i++) {
            $on  = $this->page == $i?"class='on'":'';
            if($i<=6){
                $str.= "<a $on href='$this->url?page=$i&{$this->query_string}'>$i</a>";
            }
        }
        $on  = $this->page == 7?"class='on'":'';
        if($this->amount==7){
            $str.= "<a $on href='$this->url?page=7&{$this->query_string}'>7</a>";
        }
        if($this->amount>=8){
            $str.= '...';
            $on  = $this->page == $this->amount?"class='on'":'';
            $str.= "<a $on href='$this->url?page={$this->amount}&{$this->query_string}'>$this->amount</a>";
        }
        return $str;
    }
    //下一页
    private function next(){
        if($this->page==$this->amount) return "[下一页]";
        return "<a href='{$this->url}?page={$this->next}&{$this->query_string}'>[下一页]</a>";
    }
    //末页
    private function end(){
        if($this->page==$this->amount) return "[末页]";
        return "<a href='{$this->url}?page={$this->end}&{$this->query_string}'>[末页]</a>";
    }
    //跳转按钮
    private function jump(){
        $str = "<form style='display:inline' action='$this->url' method='get'>";
        $str.= "<input type='text' size='1' name='page' value='$this->page' />";
        $str.= "<input type='submit' value='跳转' />";
        $str.="</form>";
        return $str;
    }
}


