/**
 * Displays flash message.
 *
 * @param string message
 * @param string type
 * @return void
 */
function flashMessage(message, type)
{
    if (type) {
        messageClass = type;
    } else { // default is info
        messageClass = 'highlight';
    }

    $('<div class="flash ui-state-' + messageClass + '"><p>' + message + '</p></div>')
        .appendTo('body')
        .click(function() {
            $(this).hide();
        })
        .delay(3000)
        .fadeOut('slow');
}
