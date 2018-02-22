$(".padmenu").click(function() {
    if ($(this).hasClass("cura")) {
        $(this).children(".new-sub").hide(); //当前菜单下的二级菜单隐藏
        $(".padmenu").removeClass("cura"); //同一级的菜单项
    } else {
        $(".padmenu").removeClass("cura"); //移除所有的样式
        $(this).addClass("cura"); //给当前菜单添加特定样式
        $(".padmenu").children(".new-sub").slideUp("fast"); //隐藏所有的二级菜单
        $(this).children(".new-sub").slideDown("fast"); //展示当前的二级菜单
    }
});