/**
 * Displays flash message.
 *
 * @param string message
 * @param string type
 * @return object
 */
function flashMessage(message, type, fixed)
{
    if (type) {
        messageClass = type;
    } else { // default is info
        messageClass = 'highlight';
    }

    var flash = $('<div class="flash ui-state-' + messageClass + '"><p>' + message + '</p></div>')
        .appendTo('body')
        .click(function() {
            $(this).hide();
        });

    if (!fixed) {
        flash.delay(3000).fadeOut('slow');
    }

    return flash;
}

/**
 * Call server function
 * @param {array} p_callback
 * @param {object} p_args
 * @param {callback} p_handle
 * @return bool
 */
function callServer(p_callback, p_args, p_handle)
{
    var flash = flashMessage('Processing...', null, true);
    $.ajax({
        'url': g_admin_endpoint,
        'type': 'POST',
        'data': {
            'security_token': g_security_token,
            'callback': p_callback,
            'args': p_args,
        },
        'dataType': 'json',
        'success': function(json) {
            flash.fadeOut();
            if (json.error) {
                flashMessage(json.message, 'error');
                return;
            }

            if (p_handle) {
                p_handle(json);
            }
        },
        'error': function(xhr, textStatus, errorThrown) {
            flashMessage(g_msg_session_expired, 'error', true);
        },
    });
}
