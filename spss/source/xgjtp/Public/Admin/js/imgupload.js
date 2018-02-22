function imgChange(obj1, obj2) {
            //获取点击的文本框
            var file = document.getElementById("file");
            //存放图片的父级元素
            var imgContainer = document.getElementsByClassName(obj1)[0];
            //获取的图片文件
            var fileList = file.files;
           
            var  imgurl=window.URL.createObjectURL(file.files[0]);
            var $z_addImg=$(".z_addImg");
            if($z_addImg.length>0){
                $z_addImg.remove();
            }
                var img = document.createElement("img");
                img.setAttribute("src",imgurl);
                var imgAdd = document.createElement("div");
                imgAdd.setAttribute("class", "z_addImg");
                imgAdd.appendChild(img);
                imgContainer.appendChild(imgAdd);
             
            imgRemove();
        };

        function imgRemove() {
        	var imgList = document.getElementsByClassName("z_addImg");
        	var mask = document.getElementsByClassName("z_mask")[0];
        	var cancel = document.getElementsByClassName("z_cancel")[0];
        	var sure = document.getElementsByClassName("z_sure")[0];
        	for (var j = 0; j < imgList.length; j++) {
        		imgList[j].index = j;
        		imgList[j].onclick = function() {
        			var t = this;
        			mask.style.display = "block";
        			cancel.onclick = function() {
        				mask.style.display = "none";
        			};
        			sure.onclick = function() {
        				mask.style.display = "none";
        				t.style.display = "none";
        			};

        		}
        	};
        };