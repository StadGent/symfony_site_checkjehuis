$(function () {

    /**
     * SIDEBAR
     */
    var $sidebar = $('#sidebar-overlay');
    function sidebarOverlay() {
        $('.heading-sidebar-button a').on('click', function (e) {
            $sidebar.show();
            e.stopPropagation();
        });
        $('html').on('click', function (e) {
            var inSidebar = $(e.target).parents('#sidebar-overlay').length > 0 || $(e.target).is('#sidebar-overlay');
            if (!inSidebar && $sidebar.is(':visible')) $sidebar.hide();
        });
        $sidebar.find('.sidebar-close').on('click', function () {
            $sidebar.hide();
        });
    }

    /**
     * LOCKED HEADING
     */
    var $lockedHead = null;
    function setLockedHeading()
    {
        var $head = $('.heading-lock-top').first();

        if (!$head.length) return;

        var head = $head[0];

        var bounds = head.getBoundingClientRect();

        if (bounds.top < -10) {
            if ($lockedHead) return;

            $lockedHead = $head.clone();
            $lockedHead.css({
                position: 'fixed',
                top: 0,
                zIndex: 1039, // don't go over the bootstrap modals
                width: '100%'
            });
            $lockedHead.removeClass('heading-lock-top');
            $('body').append($lockedHead);
        } else {
            if (!$lockedHead) return;
            $lockedHead.remove();
            $lockedHead = null;
        }
    }

    /**
     * EVEN SPACED LIST
     */
    function evenSpacing() {
        $('.even-spacing').each(function () {
            var $container  = $(this);
            var $children   = $container.children();
            var childCount  = $children.length || 1;

            var width = Math.floor(($container.width()) / childCount).toString() + 'px';

            $children.css('width', width);
        });
    }

    /**
     * POPOVER
     */
    function popover() {
        var $popovers = $('[data-toggle="popover"]');

        $popovers.on('click', function (e) {
            var $link = $(this);
            if ($link.hasClass('popover-once')) {
                if ($link.hasClass('popover-once-clicked')) {
                    return true;
                }
                $link.addClass('popover-once-clicked');
                e.preventDefault();
                return false;
            }
        });

        $popovers.popover();
        $popovers.on('focusout', function () {
            $(this).popover('hide');
        });
    }

    /**
     * KEEP NEXT BUTTONS AT SAME HEIGHT
     */
    $('.btn-next-checked-height').each(function(i, el) {
        var viewport = $(window).height();
        var offset = $(el).offset().top;
        var minOffset = 0;

        if (viewport > 800) {
            minOffset = 600;
        } else if (viewport > 700) {
            minOffset = 500;
        }

        if (minOffset && offset < minOffset) {
            var diff = minOffset - offset;
            $(el).css('margin-top', diff + "px");
        }
    });

    /**
     * initialize
     */
    (function init(register) {

        evenSpacing();
        setLockedHeading();

        if (register) {
            // register one time configurations
            sidebarOverlay();
            popover();

            // register adjustments on resize
            $(window).resize(function () { init() });
            $(window).scroll(function () { init() });
        }
    })(true);
});