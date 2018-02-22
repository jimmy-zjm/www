// JavaScript Document
        function show(n){
            for(var i=1;i<=6;i++){
                document.getElementById("tab"+i);
                document.getElementById("p"+i).style.display = 'none';
            }
                document.getElementById("tab"+n);
                document.getElementById("p"+n).style.display = "block";
        }

