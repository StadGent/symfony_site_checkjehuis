$(function () {

    /**
     * DISABLE CACHING OR IE9 WILL EXPLODE
     */
    $.ajaxSetup({ cache: false });

    var $body = $('body');

    /**
     * CUSTOM FORM RADIOS
     */

    $('.option-container').on('click', function () {

        var $form = $(this).parents('form').first();
        var $container = $(this);

        if (!$container.hasClass('active')) {
            var inputSelector = $container.attr('data-option-field');
            var value = $container.attr('data-option-value');
            var $input = $('#' + inputSelector);
            var $options = $container.parent().find('.option-container');

            $options.removeClass('active');
            $container.addClass('active');
            $input.val(value);
            ajaxForm($form);
        }

    });

    /**
     * CUSTOM FORM SWITCH
     */

    $('.option-switch').on('click', function () {

        var $form = $(this).parents('form').first();
        var $switch = $(this);
        var valLeft = $switch.attr('data-value-left');
        var valRight = $switch.attr('data-value-right');
        var fieldSelector = $switch.attr('data-switch-field');
        var $input = $('#' + fieldSelector);

        if ($switch.hasClass('switch-left')) {
            $switch.removeClass('switch-left');
            $switch.addClass('switch-right');
            $input.val(valRight);
        } else {
            $switch.removeClass('switch-right');
            $switch.addClass('switch-left');
            $input.val(valLeft);
        }
        ajaxForm($form);
    });

    /**
     * TIMELINE
     */

    $('.timeline-segment').on('click', function () {

        var $segment = $(this);

        if (!$segment.hasClass('selected')) {
            var $form = $(this).parents('form').first();
            var inputSelector = $segment.attr('data-option-field');
            var value = parseInt($segment.attr('data-option-value'), 10);
            var $input = $('#' + inputSelector);
            var $segments = $segment.parent().children();

            $segments.removeClass('active selected');
            $input.val(value);

            $segments.each(function () {
                var val = parseInt($(this).attr('data-option-value'), 10);

                if (value > val || value == 3000) {
                    $(this).addClass('active');
                }
                if (value === val) {
                    $(this).addClass('selected');
                }
            });
            ajaxForm($form);
        }

    });

    /**
     * CUSTOM DROPDOWNS
     */
    $('.house-config-dropdown .btn').on('click', function (e) {

        var $button = $(this);
        var $container = $button.parent();
        var $options = $container.find('.dropdown-options');

        $options.css('width', $button.outerWidth() + 'px');

        if (!$options.is(':visible')) {
            e.stopPropagation();
            // Hide all other options.
            $('.dropdown-options').hide();

            // Show clicked options.
            $options.show();
        }
    });
    $('.house-config-dropdown .dropdown-options .list-group-item').on('click', function (e) {
        var $form = $(this).parents('form').first();
        var $item = $(this);
        var $dropdown = $item.parents('.house-config-dropdown').first();
        var $dropdownLabel = $dropdown.find('.dropdown-value-label');
        var fieldSelector = $dropdown.attr('data-dropdown-field');
        var $input = $('#' + fieldSelector);
        var value = $item.attr('data-dropdown-value');
        var label = $item.html();

        $dropdown.attr('data-dropdown-value', value);
        $input.val(value);
        $dropdownLabel.html(label);
        $('.dropdown-options').hide();

        if ($form.length) {
            ajaxForm($form);
        }
    });
    $('.dropdown-selector').on('click', function (e) {

        var $radios = $(this).parent().children();
        var isActive = $(this).hasClass('active');

        if (!isActive) {
            $radios.removeClass('active');
            $(this).addClass('active');
        }
    });
    $body.on('click', function () {
        $('.dropdown-options').hide();
    });

    /**
     * VALIDATION
     */
    function invalidate($input) {
        $input.parents('.form-group').addClass('has-error');
        $input.focus();
        return false;
    }

    function validate($input, e) {
        $input.parents('.form-group').removeClass('has-error');
        return true;
    }

    function validateInput($input) {

        var val = $input.val();

        var $optionContainer = $input.parents('.option-container');
        var required = false;
        if ($optionContainer.length) {
            if (!$optionContainer.hasClass('active')) {
                return true;
            }
            required = true;
        } else {
            required = $input.attr('required') === 'required';
        }

        if (required && !val) {
            return invalidate($input);
        }

        if ($input.hasClass('validate-number')) {
            val = val.replace(',', '.');
            return $.isNumeric(val) ? validate($input) : invalidate($input);
        }

        if ($input.hasClass('validate-integer')) {
            return Math.floor(val) == val && $.isNumeric(val) ? validate($input) : invalidate($input);
        }
    }

    function validateForm($form) {
        var isValid = true;

        $form.find('input.validate').each(function () {
            var $input = $(this);
            if (!validateInput($input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    $body.on('submit', 'form.validate', function () { validateForm($(this)); });
    $body.on('blur keyup change', 'input.validate', function () { return validateInput($(this)); });

    /**
     * HOUSE SCORE SLIDER
     */

    window.updateScoreSlider = function updateScoreSlider(config) {

        var animateText = false;
        if (config.align == 'left' && $('.house-grade-scale-text-left').is(':hidden') ||
            config.align == 'right' && $('.house-grade-scale-text-right').is(':hidden')) {
            animateText = true;
        }

        if (animateText) $('.house-grade-scale-text').hide('fade');

        var $slider = $('.house-avg-score');
        $slider.animate({
            left: config.position.toString() + '%'
        }, 500);

        if (config.centered) {
            $slider.addClass('neutral');
        } else {
            $slider.removeClass('neutral');
        }

        if (animateText) {
            if (config.align == 'right') {
                $('.house-grade-scale-text-right').show('fade');
            } else {
                $('.house-grade-scale-text-left').show('fade');
            }
        }

        $slider.find('.house-grade-scale-label').html(config.label);
    };

    /**
     * ADMIN TABLE FILTERS
     */
    $body.on('change', '.form-control.table-filter', function () {
        var name = $(this).attr('name');
        var value = $(this).val();

        window.location.search = '?table-filter[' + name + ']=' + value;
    });

    /**
     * ajax forms
     */

    window.ajaxForm = function ajaxForm($form) {

        if (!$form.hasClass('form-ajax')) {
            return;
        }

        if ($form.hasClass('validate') && !validateForm($form)) {
            return;
        }

        var url = $form.attr('action');
        var method = $form.attr('method');
        var $indicator = $form.find('.form-ajax-refresh-indicator');

        if ($indicator.attr('data-original-value') == undefined) {
            $indicator.attr('data-original-value', $indicator.html());
        }

        var $spinner = $('<span><i class="fa fa-fw fa-refresh fa-spin"></i> ' + $indicator.attr('data-original-value') + '</span>');

        $indicator.html($spinner);

        $.ajax({
            url: url,
            type: method,
            data: $form.serialize(),
            success: function () {},
            complete: function () {
                $indicator.html($indicator.attr('data-original-value'));
            }
        });

    };

    $body.find('input.form-ajax-trigger').on('keyup change blur', function () {
        var $form = $(this).parents('form').first();
        ajaxForm($form);
    });

    $body.find('input.form-ajax-trigger-delay').on('keyup', function () {
        var $el = $(this);
        var value = $(this).val();
        setTimeout(function () {
            if (value == $el.val()) {
                var $form = $el.parents('form').first();
                ajaxForm($form);
            }
        }, 500);
    });

    /**
     * SOLAR AND HEAT MAP POPUPS
     */

    $('.map-popup').on('click', function (e) {

        var url = $(this).attr('data-map-url');
        window.open(url, 'map', 'menubar=no,location=yes,resizable=yes,scrollbars=yes,status=no,left=50,top=50,width=750,height=550');

    });

    /**
     * SOLAR PANELS UPDATE POLLING
     *
     * since the value for solar panels can be changed from other websites,
     * we poll for changes and update the input field
     */

    var $inputSolarPanels = $('#house_solar_wp');
    pollSolarPanels();
    function pollSolarPanels() {
        if ($inputSolarPanels.length) {
            var url = $inputSolarPanels.attr('data-poll-url');
            $.ajax({
                url: url,
                success: function (response) {
                    var val = response.solar_panels;
                    if (response && response.success && val) {
                        if ($inputSolarPanels.attr('data-current-val') != val) {
                            $inputSolarPanels.val(val);
                            $inputSolarPanels.attr('data-current-val', val);
                        }
                    }
                }
            }).always(function () {
                setTimeout(pollSolarPanels, 2000);
            });
        } else {
            setTimeout(pollSolarPanels, 2000);
        }
    }
});
