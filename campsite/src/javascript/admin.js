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

var autocomplete = false;
var terms = [];
$(document).ready(function() {
    // topics autocomplete
    $('.autocomplete.topics').autocomplete({
        source: function(request, response) {
            if (terms.length == 0) { // populate terms
                $('ul.tree.sortable strong').each(function() {
                    terms.push($(this).text());
                });
            }
            if (terms.length == 0) { // still needs to populate
                $('ul.tree label').each(function() {
                    terms.push($(this).text());
                });
            }

            var match = [];
            var re = new RegExp(request.term, "i");
            for (i = 0; i < terms.length; i++) {
                if (terms[i].search(re) >= 0) {
                    match.push(terms[i]);
                }
            }
            response(match);
        },
        close: function(event, ui) {
            $('input[name=search]').change();
        },
    });
});
