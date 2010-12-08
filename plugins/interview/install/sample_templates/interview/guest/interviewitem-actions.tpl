{{ if $sf->interviewitem->status == 'pending' }}
    <a href="{{ uripath }}?interviewitem_action=form&amp;f_interviewitem_id={{ $sf->interviewitem->identifier }}">Answer</a>
{{ /if }}
    