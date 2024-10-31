jQuery(document).ready(function ($) {

    /* ******* Select2 ******** */
    function nt_init_select2() {

        if ($('.ops-select2').length) {
            $('.ops-select2').each(function () {
                var placeholder = $(this).data('placeholder');
                var search = $(this).data('search');
                var searchValue = -1;

                var allowClear = $(this).data('allow-clear');
                var allowMultiple = $(this).data('multiple');
                var allowClearValue = false;
                var multipleValue = false;

                if (typeof search !== 'undefined') {
                    searchValue = 0;
                }

                if (typeof allowClear !== 'undefined') {
                    allowClearValue = true;
                }

                if (typeof allowMultiple !== 'undefined') {
                    multipleValue = true;
                }

                $(this).select2({
                    multiple: multipleValue,
                    placeholder: placeholder,
                    allowClear: allowClearValue,
                    minimumResultsForSearch: searchValue
                });
            });
        }
    }

    nt_init_select2();

    /* ******* HIDE ******** */
    $('.ops-popup .ops-overlay, .ops-popup .ops-close').on('click', function (e) {
        e.preventDefault();
        $('.ops-popup').fadeOut();
    });


    $('.ops-open-form').on('click', function (e) {
        e.preventDefault();
        $('.ops-popup').fadeIn();
    });


    /* ****** ADD KEYWORD ****** */
    $('body').on('change', '.ops-popup input[name=wp_id_type]', function () {

        $('.ops-search input[name=search]').val('');
        $('.ops-search input[name=wp_id]').val('');
        $('.ops-search .ops-search-content-ok').hide();

        var selectedValue = $(this).val();
        if (selectedValue === '0') {
            $('body').find('.ops-popup .ops-search').slideUp();
        } else {
            var selectedPlaceholder = $(this).data('input-placeholder');
            $('body').find('.ops-popup input[name=wp_id]').attr('placeholder', selectedPlaceholder);
            $('body').find('.ops-popup .ops-search').slideDown();
        }


    });


    var filtr_timeout;
    $('body').on('keyup', '.ops-form-add-keyword input[name=search], .ops-form-edit-keyword input[name=search]', function () {

        _this = $(this), length = _this.val().length;
        var search = $(this).val();
        var type = $('body').find('.ops-popup input[name=wp_id_type]:checked').val();
        console.log(type);
        clearTimeout(filtr_timeout);

        filtr_timeout = setTimeout(function () {
            if (length > 2 || length == 0) {
                console.log('go');
                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {
                        action: 'ops_search_content',
                        search: search,
                        type: type
                    },
                    success: function (data) {
                        console.log('xx');
                        $('body').find('.ops-search-content-output').html(data);
                    },
                    error: function () {
                        // error
                    }
                });
            }
        }, 300);
    });


    $('body').on('click', '.ops-add-keyword', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_add_keyword',
            },
            beforeSend: function () {
                $('.ops-popup').removeClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {
                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('click', '.ops-add-keyword-group', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_add_keyword_group',
            },
            beforeSend: function () {
                $('.ops-popup').removeClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {
                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
            },
            error: function () {
                // error
            }
        });
    });


    $('body').on('click', '.ops-edit-keyword', function (e) {
        e.preventDefault();
        var pid = $(this).data('pid');

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_edit_keyword',
                pid: pid
            },
            beforeSend: function () {
                $('.ops-popup').removeClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {
                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('submit', 'form.ops-form-add-keyword', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function () {
                $('.ops-preloader').show();
            },
            success: function (data) {
                console.log(data);
                if (data.success === false) {
                    $('.ops-popup-error').show().html(data.data.message);
                    $('.ops-preloader').hide();
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });


    $('body').on('submit', 'form.ops-form-add-keyword-group', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function () {
                $('.ops-preloader').show();
            },
            success: function (data) {
                if (data.success === false) {
                    $('.ops-popup-error').show().html(data.data.message);
                    $('.ops-preloader').hide();
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('click', '.ops-remove-selected-keywords-from-group', function (e) {
        e.preventDefault();

        var button = $(this);
        var group = $(this).data('group');

        var keyword_ids = [];

        $('body').find('.ops-keyword-checkbox').each(function () {
            if ($(this).is(':checked') === true) {
                keyword_ids.push($(this).val());
            }
        });

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_remove_selected_keywords_from_group',
                group: group,
                keyword_ids: keyword_ids
            },
            beforeSend: function () {
                $(button).addClass('ops-disabled');
                $('.ops-preloader').show();
            },
            success: function (data) {
                if (data.success === false) {
                    $(button).removeClass('ops-disabled');
                    $('.ops-mass-action-error').show().html(data.data.message);
                    $('.ops-preloader').hide();
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });


    $('body').on('click', '.ops-remove-keyword-group', function (e) {
        e.preventDefault();

        var confirm_text = 'Really remove the group?';
        var r = confirm(confirm_text);

        if (r === true) {
            var button = $(this);
            var groupId = $(this).data('group-id');


            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'ops_remove_keyword_group',
                    group_id: groupId
                },
                beforeSend: function () {
                    $(button).addClass('ops-disabled');
                    $('.ops-preloader').show();
                },
                success: function (data) {
                    console.log(data);
                    if (data.success === false) {
                        $(button).removeClass('ops-disabled');
                        $('.ops-mass-action-error').show().html(data.data.message);
                        $('.ops-preloader').hide();
                    } else {
                        window.location.replace(data.data.url);
                    }
                },
                error: function () {
                    // error
                }
            });
        }
    });


    $('body').on('submit', 'form.ops-add-selected-keywords-to-group', function (e) {
        e.preventDefault();

        var form = $(this);
        var group = $(this).find('select[name=group] option:selected').val();

        var keyword_ids = [];

        $('body').find('.ops-keyword-checkbox').each(function () {
            if ($(this).is(':checked') === true) {
                keyword_ids.push($(this).val());
            }
        });

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_add_selected_keywords_to_group',
                group: group,
                keyword_ids: keyword_ids
            },
            beforeSend: function () {
                $(form).addClass('ops-disabled');
                $('.ops-preloader').show();
            },
            success: function (data) {
                if (data.success === false) {
                    $(form).removeClass('ops-disabled');
                    $('.ops-mass-action-error').show().html(data.data.message);
                    $('.ops-preloader').hide();
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });

    });


    // edit keyword
    $('body').on('submit', 'form.ops-form-edit-keyword', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            success: function (data) {
                if (data.success === false) {
                    $('.ops-popup-error').show().html(data.data.message);
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });

    // delete keyword
    $('body').on('submit', 'form.ops-form-delete-keyword', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            success: function (data) {
                if (data.success === false) {
                    $('.ops-popup-error').show().html(data.data.message);
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('click', '.ops-search-content', function () {
        var pid = $(this).data('pid');
        var title = $(this).html();
        $('.ops-search input[name=search]').val(title);
        $('.ops-search input[name=wp_id]').val(pid);
        $('body').find('.ops-search .ops-search-content').remove();
        $('body').find('.ops-search-content-ok').show();
    });

    // load keyword graph
    $('body').on('click', '.ops-load-keyword-graph', function (e) {
        e.preventDefault();
        var pid = $(this).data('pid');


        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_keyword_graph',
                pid: pid
            },
            beforeSend: function () {
                $('.ops-popup').addClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {
                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
            },
            error: function () {
                // error
            }
        });

    });


    $('body').on('click', '.ops-select-all-keywords', function () {
        var input = $(this).find('input');

        if ($(input).is(':checked')) {
            $(input).prop('checked', false);
            $('.ops-keyword-checkbox').prop('checked', false);

        } else {

            $(input).prop('checked', true);
            $('body').find('.ops-keyword-checkbox').prop('checked', true);
        }
    });

    $('body').on('click', '.ops-select-all-keywords input', function () {
        var input = $(this);

        if ($(input).is(':checked')) {
            $(input).prop('checked', false);
            $('.ops-keyword-checkbox').prop('checked', false);

        } else {

            $(input).prop('checked', true);
            $('body').find('.ops-keyword-checkbox').prop('checked', true);
        }
    });

    // load keyword graph
    $('body').on('click', '.ops-delete-selected-keywords', function (e) {
        e.preventDefault();

        var confirm_text = 'Really delete the keywords?';
        var r = confirm(confirm_text);

        if (r === true) {

            var button = $(this);

            var keyword_ids = [];

            $('body').find('.ops-keyword-checkbox').each(function () {
                if ($(this).is(':checked') === true) {
                    keyword_ids.push($(this).val());
                }
            });

            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'ops_delete_selected_keywords',
                    keyword_ids: keyword_ids
                },
                beforeSend: function () {
                    $(button).addClass('ops-disabled');
                    $('.ops-preloader').show();
                },
                success: function (data) {
                    window.location.replace(data.data.url);
                },
                error: function () {
                    // error
                }
            });
        }


    });


    /* ****** BACKLINKS ****** */

    $('body').on('click', '.ops-add-backlink', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_add_backlink',
            },
            beforeSend: function () {
                $('.ops-popup').removeClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {

                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
                nt_init_select2();
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('click', '.ops-edit-backlink', function (e) {
        e.preventDefault();
        var pid = $(this).data('pid');

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_edit_backlink',
                pid: pid
            },
            beforeSend: function () {
                $('.ops-popup').removeClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {
                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
                nt_init_select2();
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('submit', 'form.ops-form-add-backlink', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function () {
                $('.ops-preloader').show();
            },
            success: function (data) {
                if (data.success === false) {
                    $('.ops-popup-error').show().html(data.data.message);
                    $('.ops-preloader').hide();
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('submit', 'form.ops-form-edit-backlink', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            success: function (data) {
                if (data.success === false) {
                    $('.ops-popup-error').show().html(data.data.message);
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });


    $('body').on('submit', 'form.ops-form-delete-backlink', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            success: function (data) {
                if (data.success === false) {
                    $('.ops-popup-error').show().html(data.data.message);
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });



    /* ******* PREMIUM ******** */
    $('.ops-premium-form-sign-up input[name=invoice_details]').on('change', function () {

        if ($(this).is(':checked') === true) {
            $('.ops-invoice-details').show();
        } else {
            $('.ops-invoice-details').hide();
        }

    });

    $('body').on('submit', 'form.ops-premium-form-sign-up', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function () {
                $('.ops-preloader').show();
            },
            success: function (data) {
                console.log(data);
                $('.ops-preloader').hide();
                if (data.success === false) {
                    $('.ops-error').show().html(data.data.message);
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('click', '.ops-forget-premium', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_forget_premium',
            },
            beforeSend: function () {
                $('.ops-popup').removeClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {
                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('submit', 'form.ops-form-forget-premium', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function () {
                $('.ops-preloader').show();
            },
            success: function (data) {
                $('.ops-preloader').hide();

                if (data.success === false) {
                    $('.ops-error').show().html(data.data.message);
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });


    $('body').on('click', '.ops-add-existing-api-key', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_add_existing_api_key',
            },
            beforeSend: function () {
                $('.ops-popup').removeClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {
                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
            },
            error: function () {
                // error
            }
        });
    });

    $('body').on('submit', 'form.ops-form-add-existing-api-key', function (e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function () {
                $('.ops-preloader').show();
            },
            success: function (data) {
                $('.ops-preloader').hide();

                if (data.success === false) {
                    $(form).find('.ops-error').show().html(data.data.message);
                } else {
                    window.location.replace(data.data.url);
                }
            },
            error: function () {
                // error
            }
        });
    });


    $('body').on('click', 'a.ops-revoke-access-google-api', function (e) {
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_revoke_google_search_console_access',
            },
            beforeSend: function () {
                $('.ops-preloader').show();
            },
            success: function (data) {
                window.location.replace(data.data.url);
            },
            error: function () {
                // error
            }
        });

    });

    /* KEYWORDS AGAIN */

    $('body').on('click', '.ops-graph-wrapper .ops-graph .values .backlinks .backlink', function () {

        var date = $(this).data('date');
        var keyword_id = $(this).data('keyword-id');

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: 'ops_load_backlinks_for_date',
                date: date,
                keyword_id: keyword_id
            },
            beforeSend: function () {
                $('.ops-popup').removeClass('ops-big');
                $('.ops-popup').fadeIn();
                $('.ops-popup-content').empty();
                $('.ops-popup-preloader').show();
            },
            success: function (data) {
                $('.ops-popup-preloader').hide();
                $('.ops-popup-content').html(data);
            },
            error: function () {
                // error
            }
        });
    });


    /* FILTER KEYWRODS */
    $('.ops-filter-graph-keyword').on('click', function () {

        if ($(this).hasClass('active') === false) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }


        // show active
        if ($('body').find('.ops-filter-graph-keyword.active').length) {

            $('.ops-graph .value').hide();
            $('.ops-graph .lane').hide();

            $('body').find('.ops-filter-graph-keyword.active').each(function () {
                var showId = $(this).data('id');

                $('.ops-graph .value-' + showId).show();
                $('.ops-graph .lane-' + showId).show();
            });
        } else {
            // show all
            $('.ops-graph .value').show();
            $('.ops-graph .lane').show();
        }

    });


    /* FILTER BACKLINKS */
    $('.ops-show-secondary-filter').on('click', function (e) {
        e.preventDefault();
        $('.ops-secondary-filter').slideDown();
    });


    /* ******* CALCULATOR ******** */
    $('body').on('change', '.ops-calculator select[name=period]', function (e) {
        e.preventDefault();
        recalculate_price();
    });


    $('body').on('keyup', '.ops-calculator input[name=keywords]', function (e) {
        e.preventDefault();
        recalculate_price();
    });

    function recalculate_price() {
        var perRequest = parseFloat($('.ops-calculator').find('input[name=keywords]').data('per-request'));
        var keywords = parseInt($('.ops-calculator').find('input[name=keywords]').val());
        var period = parseInt($('body').find('.ops-calculator select[name=period] option:selected').val());

        if (isNaN(keywords) === false && isNaN(period) === false) {
            var howMuch = Math.round(30 / period) * keywords * perRequest;


            $('body').find('input[name=credit]').val(Math.ceil(howMuch));
            $('body').find('.ops-estimated-costs').html('$' + Math.ceil(howMuch));
        }
    }


});
















