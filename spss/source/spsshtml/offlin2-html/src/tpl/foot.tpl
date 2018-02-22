<footer>
	<ul>
		<li><a class="f-btn f-btn01" href="personnalCenter.html">
			个人中心
		</a></li>
		<li><a class="f-btn f-btn02"  href="selectSys.html">
			报价系统
		</a></li>
		<li ><a class="f-btn f-btn03"  href="workorder.html">
			施工订单
		</a></li>
		<li ><a class="f-btn f-btn04"  href="aftersale-worker.html">
			售后订单
		</a></li>
		<li ><a class="f-btn f-btn05"  href="set.html">
			设置
		</a></li>
	</ul>
	<script type="text/javascript">
		
		if(localStorage.active){
			$(".f-btn").eq(localStorage.active).parent("li").addClass("active")
		}else{
			$(".f-btn").eq(1).parent("li").addClass("active")
		}

		

		$(".f-btn").click(function(ev){
			
			ev.preventDefault();
			nojump=false;
			localStorage.active=$(".f-btn").index(this);
			
			window.location.href =$(this).attr("href");
			
			
		})

	</script>
</footer>