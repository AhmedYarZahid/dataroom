$.fn.parallax = function(strength) {
    var scrollTop = $(window).scrollTop();

    strength = Number(strength) || .5;

    this.each(function() {
        var $elem = $(this),
            moveValue = Math.round((scrollTop - $elem.offset().top) * strength),
            bgPos = $elem.css('background-position').split(' '),
            bgPosX = bgPos[0] || '50%',
            initialBgPosY = $elem.data('parallax-bg-pos-y');

        $elem.css({
            'position': 'relative',
            'will-change': 'transform, background-position',
            '-moz-transform': 'translateX(0px)',
            '-webkit-transform': 'translateX(0px)',
            '-o-transform': 'translateX(0px)',
            '-ms-transform': 'translateX(0px)',
            'transform': 'translateX(0px)'
        });

        if (!initialBgPosY) {
            initialBgPosY = bgPos[1] || '0%';
            $elem.data('parallax-bg-pos-y', initialBgPosY)
        }

        if (moveValue < 0) {
            moveValue = 0;
        }

        $elem.css('background-position', bgPosX + ' calc(' + initialBgPosY + ' + ' + moveValue + 'px)');
    });

    return this;
};

// Play video backgrounds
function onYouTubePlayerAPIReady() {
    $(function() {
        var videoBgPlayers = {};

        function rescaleVideoBg() {
            $('.video-bg').each(function(index) {
                var $this = $(this),
                    $wrapper = $this.parent(),
                    player = videoBgPlayers[$this.attr('id')],
                    wrapperWidth = $wrapper.width(),
                    wrapperHeight = $wrapper.height(),
                    playerWidth,
                    playerHeight,
                    playerTop,
                    playerLeft;

                if (wrapperWidth / wrapperHeight > 16 / 9) {
                    playerWidth = wrapperWidth;
                    playerHeight = wrapperWidth / 16 * 9
                    playerTop = -(playerHeight - wrapperHeight) / 2;
                    playerLeft = 0;
                } else {
                    playerWidth = wrapperHeight / 9 * 16;
                    playerHeight = wrapperHeight;
                    playerTop = 0;
                    playerLeft = -(playerWidth - wrapperWidth) / 2;
                }

                player.setSize(playerWidth, playerHeight);
                $this.css({
                    left: playerLeft,
                    top: playerTop
                });
            });
        }

        // Youtube and DOM are ready
        $('.video-bg').each(function(index) {
            var $this = $(this),
                player;

            $this.attr('id', 'tp-video-bg-' + index);

            player = new YT.Player($this.attr('id'), {
                events: {
                    onReady: function(e) {
                        var player = e.target,
                            settings = $this.data('settings') || {};

                        player.loadVideoByUrl(settings.url);
                        player.mute();
                    },
                    'onStateChange': function(e) {
                        var player = e.target,
                            settings = $this.data('settings') || {};

                        if (settings.loop) {
                            if (e.data === YT.PlayerState.ENDED) {
                                player.seekTo(0);
                                player.playVideo();
                            }
                        }

                        rescaleVideoBg();
                    }
                },
                playerVars: $this.data('settings')
            });

            videoBgPlayers[$this.attr('id')] = player;
        });

        rescaleVideoBg();

        $(window).on('resize', rescaleVideoBg);
    });
}

$(function() {
    function appendCalculatedStyles() {
        var viewPortHeight = $(window).height(),
            $styles = $('<style id="tp-calculated-styles"></style>'),
            styles;

        styles = '.tp-min-height-100 { min-height: ' + viewPortHeight + 'px!important; }' +
            '.tp-min-height-75 { min-height: ' + viewPortHeight * .75 + 'px!important; }' +
            '.tp-min-height-50 { min-height: ' + viewPortHeight * .5 + 'px!important; }' +
            '.tp-min-height-25 { min-height: ' + viewPortHeight * .25 + 'px!important; }';

        $('#tp-calculated-styles').remove();

        $styles.html(styles);
        $('head').append($styles);
    }

    function appendYoutubeApiScript() {
        $('script:first').append('<script src="https://www.youtube.com/player_api"></script>>');
    }

    /* On Load */
    appendYoutubeApiScript();

    $(window).on('load resize scroll touchmove', appendCalculatedStyles);

    $(window).on('scroll', function() {
        $('.parallax-bg').parallax(.5);
    });

    $('.trendy-page').on('tp-ajax-block-loaded', function() {

    });

    // Make ULs' dots the same color as ULs' texts
    $('.trendy-page ul').each(function() {
        $(this).children('li').each(function() {
            var $li = $(this),
                $span = $li.children('span');

            if ($span.length) {
                $li.css({
                    color: $span.css('color'),
                    position: 'relative',
                    zIndex: '1'
                });
            }
        });
    });

    $('.tp-ajax-tpl').each(function() {
        var $this = $(this),
            tpl = $('<div>').append($this.clone().removeClass('tp-ajax-tpl')).html(),
            url = $this.data('tp-ajax-url'),
            compiledTpl;

        $.ajax({
            url: url,
            jsonp: "callback",
            dataType: "jsonp",
            success: function(response) {
                var $trendyPage = $this.closest('.trendy-page'),
                    $prev = $this,
                    addedElements = [],
                    $newBlock;

                compiledTpl = Handlebars.compile(tpl);

                if (Array.isArray(response)) {
                    response.forEach(function(data) {
                        $prev.after(compiledTpl(data));
                        $prev = $prev.next();

                        addedElements.add($prev);
                    });

                    $this.remove();
                } else if ($.isPlainObject(response)) {
                    $newBlock = $(compiledTpl(response));
                    $this.replaceWith($newBlock);

                    addedElements.push($newBlock);
                } else {
                    $this.html(response);

                    addedElements.push($this);
                }

                $trendyPage.trigger('tp-ajax-block-loaded', addedElements);
            },
            error: function() {
                console.warn('Could not load data for dynamic TP block');
            }
        });
    });
});

