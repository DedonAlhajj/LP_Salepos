(function($) {
    "use strict";

    $('.alert .close').on('click', function(){
        $(this).parent().removeClass('show');
    })

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //Detect device mobile
    var isMobile = false;
    if (/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || ($(window).width() < 1100)) {
        $('body').addClass('mobile');
        isMobile = true;
    } else {
        isMobile = false;
    }

    if(isMobile) {
        $('.category__list').removeClass('list__open');
        $('.right-pane').addClass('w-100');

        $('.header-middle').removeClass('slim').addClass('slim');

        if($('.header-bottom').length){
            var menu = $('.header-bottom .sidebar').clone();
            $('.category__list').append(menu);
        }
        
        if($('.header-bottom').length){
            var search = $('.header-search').clone();
            $('.header-search').html('');
            $('.header-bottom .container-fluid').html(search);
        }

        $(document).on('click', '.has-dropdown > a', function(e) {
            e.preventDefault();
            var dropdown = $(this).siblings('.dropdown');
            dropdown.toggleClass('show');
        });

        $(document).on('click', '.user-menu', function(e) {
            e.preventDefault();
            var dropdown = $(this).children('.user-dropdown-menu');
            dropdown.toggleClass('show');
        });

        $('#hero-slider .carousel-item').each(function(){
            var bg = $(this).css('background-image');
            var new_bg = bg.replace("desktop", "mobile");
            $(this).css('background-image', new_bg);
        })
            
        // $('body').on('click', function(){
        //     $('.dropdown').removeClass('show');
        //     $('.user-dropdown-menu').removeClass('show');
        // })
    }
 
    /*------------------------------------------
                     Tooltip
    --------------------------------------------*/
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })

    /*------------------------------------------
                      Scroll to top
    --------------------------------------------*/
    $(window).scroll(function() {
        if ($(this).scrollTop() >= 200) { // If page is scrolled more than 50px
            $('#scrolltotop').fadeIn(500); // Fade in the arrow
            $('.cart__menu.fixed').fadeIn(500);
        } else {
            $('#scrolltotop').fadeOut(500); // Else fade out the arrow
            $('.cart__menu.fixed').fadeOut(500);
        }
    });
    $('#scrolltotop').click(function() { // When arrow is clicked
        $('body,html').animate({
            scrollTop: 0 // Scroll to top of body
        }, 700);
    });

    $(document).ready(function() {

        /*------------------------------------    
             left sidebar menu 
        --------------------------------------*/

        $('.category__menu').on('click', function() {
            $('.category__list').toggleClass('list__open');
            if(!isMobile) {
                $('.right-pane').toggleClass('w-100');
            }
        });

        $(".shopping__cart__inner").mCustomScrollbar({
            theme: "light",
            scrollInertia: 200
        });


        /*------------------------------------    
             Shopping Cart 
        --------------------------------------*/

        $('.cart__menu').on('click', function() {
            $('.shopping__cart').addClass('shopping__cart__on');
            $('.body__overlay').addClass('is-visible');
        });
        $('.panel_setting').on('click', function() {
            $('.setting-panel').addClass('setting-panel-on');
            $('.body__overlay').addClass('is-visible');

        });
        $('.offsetmenu__close__btn').on('click', function() {
            $('.shopping__cart').removeClass('shopping__cart__on');
            $('.body__overlay').removeClass('is-visible');
        });
        $('.offsetmenu__close__btn').on('click', function() {
            $('.setting-panel').removeClass('setting-panel-on');
            $('.body__overlay').removeClass('is-visible');
        });


        //Close body Overlay 
        $('.body__overlay').on('click', function() {
            $(this).removeClass('is-visible');
            $('.offsetmenu').removeClass('offsetmenu__on');
            $('.shopping__cart').removeClass('shopping__cart__on');
            $('.filter__wrap').removeClass('filter__menu__on');
            $('.user__meta').removeClass('user__meta__on');
            $("#mobile-nav").removeClass("show");
        });


        /*------------------------------------------
                        Carousel 
        --------------------------------------------*/
        $('#hero-slider').carousel({
            interval: 2000
        })

        /*------------------------------------------
                         Product quantity
        --------------------------------------------*/

        var quantitiy = 0;
        $('.quantity-right-plus').on("click", function(e) {
            e.preventDefault();
            var quantity = parseInt($(this).parent().siblings("input.input-number").val());
            var max = $(this).parent().siblings("input.input-number").attr('max');
            $(this).parent().siblings("input.input-number").val(quantity + 1);
            if ((quantity+1) > max) {
                $(this).parent().siblings("input.input-number").val(max);
            }
        });
        $('.quantity-left-minus').on("click", function(e) {
            e.preventDefault();
            var quantity = parseInt($(this).parent().siblings("input.input-number").val());
            if (quantity > 1) {
                $(this).parent().siblings("input.input-number").val(quantity - 1);
            }
        });


        /*------------------------
           Category menu Activation
        --------------------------*/
        $('.category-sub-menu li.has-sub').on('click', function() {
            var element = $(this);
            if (element.hasClass('open')) {
                element.removeClass('open');
                element.find('li').removeClass('open');
                element.find('ul').slideUp('fast');
            } else {
                element.addClass('open');
                element.children('ul').slideDown('fast');
                element.siblings('li').children('ul').slideUp('fast');
                element.siblings('li').removeClass('open');
                element.siblings('li').find('li').removeClass('open');
                element.siblings('li').find('ul').slideUp('fast');
            }
        });

    });

}(jQuery));