(function($) {
    $(window).bind('keydown', function(event) {
        if (event.ctrlKey || event.metaKey) {
            switch (String.fromCharCode(event.which).toLowerCase()) {
                case 's':
                    event.preventDefault();
                    $('.button-primary').click();
                    //alert('ctrl-s');
                    break;
                case 'f':
                    event.preventDefault();
                    //alert('ctrl-f');
                    break;
                case 'g':
                    //event.preventDefault();
                    alert('ctrl-g');
                    break;
            }
        }
    });

    jQuery("#calc_form").submit(function(event) {
        /* stop form from submitting normally */
        event.preventDefault();

        /* get the action attribute from the form element */
        var url = jQuery(this).attr('action');

        /* Send the data using post */
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: {
                action: jQuery('#calc_action').val(),
                home: jQuery('#home').val(),
                siteurl: jQuery('#siteurl').val(),
                api_url: jQuery('#api_url').val(),
                nonce: jQuery('#calc_nonce_field').val()
            },
            success: function(data, textStatus, XMLHttpRequest) {
                //alert(data);

                var timeleft = 3;
                var downloadTimer = setInterval(function() {
                    timeleft--;
                    jQuery("#message").text(timeleft);
                    if (timeleft <= 0) {
                        jQuery("#message").text(data).show();
                        clearInterval(downloadTimer);
                    }
                }, 1000);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert(errorThrown);
                jQuery("#message").text(data).show();

            }
        });
    });

    jQuery("#calc_user_url").submit(function(event) {
        /* stop form from submitting normally */
        event.preventDefault();

        /* get the action attribute from the form element */
        var url = jQuery(this).attr('action');

        /* Send the data using post */
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: {
                action: jQuery('#calc_user_url_action').val(),
                login: jQuery('#login').val(),
                signup: jQuery('#signup').val(),
                nonce: jQuery('#calc_nonce_field').val()
            },
            success: function(data, textStatus, XMLHttpRequest) {
                //alert(data);

                var timeleft = 3;
                var downloadTimer = setInterval(function() {
                    timeleft--;
                    jQuery("#msg_u_u").text(timeleft);
                    if (timeleft <= 0) {
                        jQuery("#msg_u_u").text(data).show();
                        clearInterval(downloadTimer);
                    }
                }, 1000);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                //alert(errorThrown);
                jQuery("#msg_u_u").text(data).show();

            }
        });
    });

})(jQuery);