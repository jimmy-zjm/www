
//头部导航栏
$(function () {
    var $current_nav = $(".current-menu-item, .single-case .topnav-case, .single-news .topnav-about, .single-ask .topnav-doc, .page-template-page-doc-php .topnav-doc, .page-template-page-about-php .topnav-about").eq(0);
   if ($current_nav.length <= 0) return;
    var current_nav_width = $current_nav.innerWidth();
    var current_nav_left = $current_nav.position().left;
    var $nav_animate_block = $("#nav_animate_block"); //动画滑块
    $nav_animate_block.css({ width: current_nav_width, left: current_nav_left }); //初始状态下，动画滑块位置在current元素
// 鼠标进入a元素时触发动画事件
    $(".nav li ").hover(function () {
        var index = $(this).index();
        var $a_cur = $(".nav ").find("li").eq(index);//鼠标移动到的a元素
//利用触发的a元素索引获取其left位置和它的宽度
        var width = $a_cur.innerWidth();
        var left = $a_cur.position().left;
//设置动画滑块位置
        $nav_animate_block.stop().animate({
            width: width,
            left: left
        }, 300)
    }, function () {
//鼠标离开a元素时，动画滑块返回current元素位置
        $nav_animate_block.stop().animate({
            width: current_nav_width,
            left: current_nav_left
        })
    });
});
//案例鼠标经过移动
$(function(){
    $(".case div").mouseenter(function(){
        $(this).find("img").css("margin-top","12px");
    }).mouseleave(function(){
        $(this).find("img").css("margin-top","19px");
    });
});
//侧边栏
$(function() {
    var wheight = $(window).height();
    var $ptsideContent = $(".ptside-content");
    var $ptside = $(".ptside-side");
    var $cat = $(".catalog");
    //侧边栏随页面滚动
    var timer = setTimeout(function() {
        clearTimeout(timer);

        $(window).on("scroll", function () {  //只要窗口滚动,就触发下面代码
            var scrollTop = document.documentElement.scrollTop + document.body.scrollTop; //获取滚动后的高度
            $ptside.removeClass('fixed-bottom');

            $cat.removeClass('fixed-bottom');
            if (scrollTop > 120) {  //判断滚动后高度超过200px,就显示
                $ptside.addClass('fixed');
                $cat.addClass('fixed');
            } else {
                $ptside.removeClass('fixed');//淡出 static//如果返回或者没有超过,就淡入.必须加上stop()停止之前动画,否则会出现闪动
                $cat.removeClass('fixed');
            }
            if (($(document).height() - scrollTop-wheight < 150) && (wheight -  $ptside.height()) < 150) {
                $ptside.addClass('fixed-bottom');
            }
            if (($(document).height() - scrollTop-wheight < 150) && (wheight -  $ptside.height()) < 150) {
                $cat.addClass('fixed-bottom');
            }
        }).scroll();
    }, 100);
    if ($cat.length > 0) {
        var timer1 = setTimeout(function() {
            clearTimeout(timer1);
            $(window).on("resize", function() {
                $ptsideContent.removeClass('fixed-bottom');
                $cat[0].style.left = $ptsideContent.offset().left + $ptsideContent.outerWidth() + 17 + "px";

            }).resize();
        }, 2000);
    }


})


//回到顶部
$(function() {
    var timer;
    $(window).on("scroll", function () {  //只要窗口滚动,就触发下面代码
        clearTimeout(timer);
        timer = setTimeout(function() {
            var scrollt = document.documentElement.scrollTop + document.body.scrollTop; //获取滚动后的高度
            if (scrollt > 200) {  //判断滚动后高度超过200px,就显示
                $(".gotop").fadeIn(400); //淡出
            } else {
                $(".gotop").stop().fadeOut(400); //如果返回或者没有超过,就淡入.必须加上stop()停止之前动画,否则会出现闪动
            }
        }, 2000);
    });
    $(".gotop").click(function () { //当点击标签的时候,使用animate在200毫秒的时间内,滚到顶部
        $("html,body").animate({scrollTop: "0"}, 'slow');
    });
});


//图片轮播
$(function() {
    var now = 0;
    var $slider = $(".slider");
    var $sliderContent = $slider.find(".slider-content");
    var $items = $sliderContent.find(".slider-item");
    var $leftControl = $slider.find(".slider-left-control");
    var $rightControl = $slider.find(".slider-right-control");
    var $indicators = $(".slider-indicator li");
    var timer;
    var interval = 5000;
    var len = $items.length;
    var slideWidth = $items.eq(0).width();

    for (var l = $indicators.length, i = 0; i < l; i++) {
        $indicators[i].index = i;
    }
    $indicators.eq(now).addClass("active");
    $items.eq(now).addClass("active");
    slide();
    $(".slider-indicator").on("click", "li", function(ev) {
        if (now % len == ev.target.index) return;
        goto(ev.target.index);
    });
    $slider.on("mouseover", function () {
        clearInterval(timer);
        timer = null;
    });
    $slider.on("mouseout",  function () {
            slide();
    });
    $leftControl.on("click", function () {
            prev();
    });

    $rightControl.on("click", function () {
        next();
    });

    function goto (num) {
        var x = now % len, y= num % len;
        $indicators.eq(x).removeClass("active");
        $indicators.eq(y).addClass("active");
        if (now > num) {
            $items.eq(x).stop().css("left", 0).animate({left: slideWidth}, function() {$items.eq(x).removeClass("active");});
            $items.eq(y).stop().css("left", -slideWidth).addClass("active").animate({left: 0});
        } else {
            $items.eq(x).stop().css("left", 0).animate({left: -slideWidth}, function() {$items.eq(x).removeClass("active");});
            $items.eq(y).stop().css("left", slideWidth).addClass("active").animate({left: 0});

        }
        now = num;
    }
     function prev() {
         goto(now-1);
     }
     function next() {
         goto(now+1);
     }
    function slide() {
        clearInterval(timer);
        timer = setInterval(function () {
            next();
        }, interval);
    }

    var timer1 = setTimeout(function() {
        $(window).on("resize", function() {
            clearTimeout(timer1);
            slideWidth = $items.eq(0).width();
        })
    }, 2000);

});


$(function() {
    $("#weixin_icon").on("click", function() {
        var $popbox = $(".popup_box");
        $(".popup").show().fadeIn("fast",function(){
            $popbox.addClass("popup_box_in");
        });

        $(".popup,.icon_close").click(function() {
            //$(".popup_box").addClass("popup_box_out");
            $(".popup_box").addClass("popup_box_out");
            $(".popup").fadeOut("fast",function(){
                $popbox.removeClass("popup_box_in popup_box_out");
            });
        });
        $(".popup_box_inner").click(function() {return false;})
    });
});

$(function() {
    var $form = $("#sendMailForm");
    var $emailTip = $form.find(".tip");
    var $email = $("#email");
    var $username = $("#username");
    var $mobile = $("#mobile");
    $(document).on("click", "#activity500w .btn", function() {
        $emailTip.removeClass("danger");
        $email.val("");
        $username.val("");
        $mobile.val("");
        $(".bg").show();
        $form.fadeIn();
    }).on("click", "#sendMailForm .close", function() {
        $(".bg").hide();
        $form.fadeOut();
        $("#success").hide();
        $("#apply").show();
    }).on("click", "#submit", function() {
        var email = $email.val();
        if (email == "" || $username.val() == "" || $mobile.val() == "") {
            alert("请填写所有项目");
            return;
        }
        if (/.*@\w+(.\w+)+/.test(email)) {
            $emailTip.removeClass("danger");
            $.ajax({
                url: "../wp-content/themes/myLocalPage/sendMail.php",
                type: "POST",
                data: $("#form").serialize(),
                success: function (data) {
                    $("#apply").hide();
                    $("#success").show();
/*                    var mailUrl = "http://mail." + email.substr(email.indexOf("@") + 1);
                    $("#gotoEmail")[0].href = mailUrl;*/
                }
            });
        } else {
            $emailTip.addClass("danger");
            return false;
        }

    })
});







