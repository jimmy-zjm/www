       function show(box) {
        var text = box.innerHTML;
        var newBox = document.createElement("div");
        var btn = document.createElement("a");
        newBox.innerHTML = text.substring(0, 350);
        btn.innerHTML = text.length > 350 ? "展开" : "&nbsp;";
		if (btn.innerHTML == "展开") {
				btn.style.background="url(./images/zhankai.jpg)  no-repeat right top";
            }
		 else {
			btn.innerHTML = "&nbsp;";
			btn.style.background="none";
		}
		btn.className="zhankai";
        btn.href = "###";
        btn.onclick = function () {
            if (btn.innerHTML == "展开") {
                btn.innerHTML = "收起";
				btn.style.background="url(./images/shouqi.jpg)  no-repeat right top";
                newBox.innerHTML = text;
            } else {
                btn.innerHTML = "展开";
				btn.style.background="url(./images/zhankai.jpg)  no-repeat right top";
                newBox.innerHTML = text.substring(0, 350);
            }
            return false;
        }
        box.innerHTML = "";
        box.appendChild(newBox);
        box.appendChild(btn);
    }
    var divs = document.getElementsByTagName('div')
    for (var i = 0; i < divs.length; i++) {
        if (divs[i].className == 'box') show(divs[i]);
    } 
 
 
 
      function show2(box2) {
        var text = box2.innerHTML;
        var newbox2 = document.createElement("div");
        var btn = document.createElement("a");
        newbox2.innerHTML = text.substring(0, 150);
        btn.innerHTML = text.length > 250 ? "展开" : "&nbsp;";
		if (btn.innerHTML == "展开") {
				btn.style.background="url(./images/zhankai.jpg)  no-repeat right top";
            }
		 else {
			btn.innerHTML = "&nbsp;";
			btn.style.background="none";
		}
		btn.className="zhankai";
        btn.href = "###";
        btn.onclick = function () {
            if (btn.innerHTML == "展开") {
                btn.innerHTML = "收起";
				btn.style.background="url(./images/shouqi.jpg)  no-repeat right top";
                newbox2.innerHTML = text;
            } else {
                btn.innerHTML = "展开";
				btn.style.background="url(./images/zhankai.jpg)  no-repeat right top";
                newbox2.innerHTML = text.substring(0, 150);
            }
            return false;
        }
        box2.innerHTML = "";
        box2.appendChild(newbox2);
        box2.appendChild(btn);
    }
    var divs = document.getElementsByTagName('div')
    for (var i = 0; i < divs.length; i++) {
        if (divs[i].className == 'box2') show2(divs[i]);
    }  
  
  	
  
  
  
  
  
 
	