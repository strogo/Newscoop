<h6>{{ $smarty.template }}</h6>

{{ if $sf->interview->status == 'draft' }}
    <a href="{{ uripath }}?f_interviewstatus=pending&amp;f_interview_id={{ $sf->interview->identifier }}">Invite now</a>
    
{{ elseif $sf->interview->status == 'pending' }}
    <a href="{{ uripath }}?f_interviewstatus=published&amp;f_interview_id={{ $sf->interview->identifier }}">Set published</a>
    
{{ elseif $sf->interview->status == 'published' }}
    <a href="{{ uripath }}?f_interviewstatus=rejected&amp;f_interview_id={{ $sf->interview->identifier }}">Set rejected</a>
    
{{ elseif $sf->interview->status == 'rejected' }}
    <a href="{{ uripath }}?f_interviewstatus=draft&amp;f_interview_id={{ $sf->interview->identifier }}">Set draft</a>
    
{{ /if }}
                
<br>
<a href="{{ uripath }}?interview_action=form&amp;f_interview_id={{ $sf->interview->identifier }}">Edit</a>

<br>
<a href="{{ uripath }}?interviewitem_action=list&amp;f_interview_id={{ $sf->interview->identifier }}">List items</a>

<br>
<a href="{{ uripath }}?f_interviewstatus=delete&amp;f_interview_id={{ $sf->interview->identifier }}">Delete</a>