$(function() {
    function ratingEnable() {
		$('#example-a').barrating('show', {
            wrapperClass: 'br-wrapper-b',
            //readonly: true
        });

        $('#example-a').barrating('set', 'Mediocre');
		
		$('#example-b').barrating('show', {
            wrapperClass: 'br-wrapper-b',
            //readonly: true
        });

        $('#example-b').barrating('set', 'Mediocre');
		
		$('#example-c').barrating('show', {
            wrapperClass: 'br-wrapper-b',
            //readonly: true
        });

        $('#example-c').barrating('set', 'Mediocre');

        $('#example-d').barrating('show', {
            wrapperClass: 'br-wrapper-b',
            //readonly: true
        });

        $('#example-d').barrating('set', 'Mediocre');


    }

    function ratingDisable() {
        $('select').barrating('destroy');
    }

    $('.rating-enable').click(function(event) {
        event.preventDefault();

        ratingEnable();

        $(this).addClass('deactivated');
        $('.rating-disable').removeClass('deactivated');
    });

    $('.rating-disable').click(function(event) {
        event.preventDefault();

        ratingDisable();

        $(this).addClass('deactivated');
        $('.rating-enable').removeClass('deactivated');
    });

    ratingEnable();
});
