
jQuery(document).ready(function() {
var topmenu = jQuery("#mynav");
var topmenu_top = 0;
reset_topmenu_top(topmenu, topmenu_top);
jQuery(window).scroll(function() {
reset_topmenu_top(topmenu, topmenu_top);
});
});
function reset_topmenu_top(topmenu, topmenu_top) {
var a=140;
var document_scroll_top = jQuery(document).scrollTop();
var b=document_scroll_top -a;
var topmenubk =jQuery("#mynavbk");
var mynavspan =jQuery("#mynavspan");
var mynavimg =jQuery("#mynavimg");
if (b > topmenu_top) {
topmenu.css('top', b);
topmenu.css('background', '#f1f1f1');
topmenubk.css('background', '#f1f1f1');
mynavspan.css('display', 'none');
mynavimg.css('display', 'block');
}
if (b <= topmenu_top) {
topmenu.css('top', topmenu_top);
topmenu.css('background', '#fff');
topmenubk.css('background', '#fff');
mynavspan.css('display', 'block');
mynavimg.css('display', 'none');
}
}
