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

$(document).ready(function() {
    var terms = [];
    $('ul.tree.sortable strong').each(function() {
        terms.push($(this).text());
    });

    $('.autocomplete.topics').autocomplete({
        source: function(request, response) {
            var match = [];
            var re = new RegExp(request.term, "i");
            for (i = 0; i < terms.length; i++) {
                if (terms[i].search(re) >= 0) {
                    match.push(terms[i]);
                }
            }
            response(match);
        }
    });
});
