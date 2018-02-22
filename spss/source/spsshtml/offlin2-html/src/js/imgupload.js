
function statWordNum(textid,divid,stat)
{ 
  var all=$("#"+textid).val();
  var reallen=all.replace(/[^\x00-\xff]/g, '_').length;
  if(reallen>stat){
    var textidvalue=$("#"+textid).val();
    $("#"+textid).val(textidvalue.substring(0,stat));
    reallen=stat;
  }
  
   $("#"+divid).html("(已输入:<font color='red'>"+reallen+"</font>个字,不能超过"+stat+"个字)");
  
  if (reallen==0) {
     $("#"+divid).html("");
  }
}
  



 function imgChange(obj1, obj2) {
            //获取点击的文本框
            var file = document.getElementById("file");
            //存放图片的父级元素
            var imgContainer = document.getElementsByClassName(obj1)[0];
            //获取的图片文件
            var fileList = file.files;
            //文本框的父级元素
            var input = document.getElementsByClassName(obj2)[0];
            var imgArr = [];
            //遍历获取到得图片文件
            for (var i = 0; i < fileList.length; i++) {
                var imgUrl = window.URL.createObjectURL(file.files[i]);
                imgArr.push(imgUrl);
                var img = document.createElement("img");
                img.setAttribute("src", imgArr[i]);
                var imgAdd = document.createElement("div");
                imgAdd.setAttribute("class", "z_addImg col-xs-6 col-md-4 col-md-3");
                imgAdd.appendChild(img);
                imgContainer.appendChild(imgAdd);
            };
            imgRemove();
        };


        function imgRemove() {
            var file = document.getElementById("file");

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
                    // t.style.display = "none";
                    $(imgList).remove();
                    if(file.outerHTML){
                       file.outerHTML=file.outerHTML;
                   }else{      
                      file.value="";
                  }
              };

          }
      };
  };