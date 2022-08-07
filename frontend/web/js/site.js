/* Common scripts */

$(function() {
    $('body').on('mouseover', '.main-menu-closed', function() {
        var $this = $(this),
            $mainNav = $('#main-nav'),
            $navLinks = $mainNav.find('.nav > li > a');

        $this.removeClass('main-menu-closed');

        $this.toggleClass('open', !$mainNav.hasClass('open'));

        if ($mainNav.hasClass('open')) {
            TweenMax.staggerTo($navLinks, .4, { x: '-=200', opacity: 0, clearProps: 'x, opacity'}, 0.05);
        } else {
            TweenMax.staggerFrom($navLinks, .4, {x: '+=200', opacity: 0, clearProps: 'x, opacity'}, 0.05);
        }

        $mainNav.toggleClass('open');
    });

    $('body').on('click', '.main-menu-btn.open', function() {
        var $this = $(this),
            $mainNav = $('#main-nav'),
            $navLinks = $mainNav.find('.nav > li > a');

        $this.toggleClass('open', !$mainNav.hasClass('open'));

        if ($mainNav.hasClass('open')) {
            TweenMax.staggerTo($navLinks, .4, { x: '-=200', opacity: 0, clearProps: 'x, opacity'}, 0.05);
        } else {
            TweenMax.staggerFrom($navLinks, .4, {x: '+=200', opacity: 0, clearProps: 'x, opacity'}, 0.05);
        }

        $mainNav.toggleClass('open');

        setTimeout(function() { $this.addClass('main-menu-closed'); }, 800);
    });

    $('.login-btn').on('click', function() {
        $('.login-form-popup').toggleClass('open');
        return false;
    });

    /* Add the dropdown-menu class to submenus */
    $('.dropdown-submenu > ul').each(function() {
        $(this).addClass('dropdown-menu');
    });

    /* Open submenu on click */
    $('.navbar-nav > li.dropdown > a').click(function() {
        $(this).parent().toggleClass('open');

        return false;
    });

    $('.dropdown-submenu > a').click(function() {
        $(this).parent().toggleClass('open');

        return false;
    });

    /* Handle Show Details buttons on Offices page */
    $('.offices-index').on('click', '.member-show-info-btn, .member-hide-info-btn', function() {
        var $this = $(this);

        $this.siblings('.member-info').toggleClass('hidden');
        $this.parent().children('.member-show-info-btn').toggleClass('hidden');
        $this.parent().children('.member-hide-info-btn').toggleClass('hidden');
    });

    /* Collapse children submenu when parent is collapsed */
    $('.navbar').on('hide.bs.dropdown', function() {
        $(this).find('.open').removeClass('open');
    });

    // Show items on click
    $('.dropdown').hover(function(){
        $('.dropdown-toggle', this).trigger('click');
    });

    $('.content-header .cities').marquee({
        speed: 50,
        gap: 0,
        delayBeforeStart: 0,
        duplicated: true,
        startVisible: true
    });

    if ($(window).width() < 1024) {
        $('.content-header .partners').marquee({
            speed: 25,
            gap: 0,
            delayBeforeStart: 0,
            duplicated: true,
            startVisible: true
        });
    }

    // Update html tag's font-size to keep `rem` actual
    (function() {
        updateRootEm();

        $(window).on('resize', updateRootEm);

        function updateRootEm() {
            var windowWidth = $(window).width(),
                divider = windowWidth > 1024 ? 128 : 32;

            //$('html').css('fontSize', windowWidth / divider);
        }
    })();

    // Custom heading bg and content for trendy pages
    (function() {
        var $header = $('.content-header-banner'),
            $headerData = $('.tp-content-block.header');

        if (!$headerData.length) {
            return;
        }

        $header.animate({opacity: .25}, 400, function () {
            $header.css({backgroundImage: 'url(' + $headerData.find('.bg-image').prop('src') + ')'});
            $header.find('.page-title').html($headerData.find('.heading-text').html());
        }).animate({opacity: 1}, 400);
    })();

    var i = 1;
    $('a').each(function(){
        url = $(this).attr('href');
        if (url.toLowerCase().indexOf("https://dev-www.ajassocies.fr") >= 0){
            $(this).attr('href' , url.replace('https://dev-www.ajassocies.fr' , window.location.origin));
        }
        if (url.toLowerCase().indexOf("https://dev-www.ajadataroom.fr") >= 0){
            $(this).attr('href' , url.replace('https://dev-www.ajadataroom.fr' , window.location.origin));
        }
        i++;
    });

    var i = 1;
    $('img').each(function(){
        url = $(this).attr('src');
        if (url.toLowerCase().indexOf("http://frontend.work") >= 0){
            $(this).attr('src' , url.replace( 'http://frontend.work' , 'https://www.ajassocies.fr'));
        }

        if (url.toLowerCase().indexOf("/uploads") == 0 && window.location.origin != 'https://www.ajassocies.fr'){
            console.log('dev env');
            $(this).attr('src' , 'https://www.ajassocies.fr' + url );
        }
        i++;
    });
});