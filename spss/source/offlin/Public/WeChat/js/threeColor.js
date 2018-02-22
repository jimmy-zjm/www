function bgColor(){
       	$(".order-list .item-top").each(function(i,domEle){
        	var obj=$(domEle);
				if(i%3==0){
					obj.addClass("bgcolor1");
				}
				if(i%3==1){
					obj.addClass("bgcolor2");

				}
				if(i%3==2){
					obj.addClass("bgcolor3");

				}
 })} 


  function borderColor(){

			$(".color-list li").each(function(i,domEle){
				var obj=$(domEle);
				if(i%3==0){
					obj.addClass("bStyle1");
				}
				if(i%3==1){
					obj.addClass("bStyle2");

				}
				if(i%3==2){
					obj.addClass("bStyle3");

				}
			})
		
  }