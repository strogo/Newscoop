Dear {{ $sf->interview->questioneer->name }},<br>

we like to invite to interview {{ $sf->interview->title }}:
{{ include file='interview/interview-details.tpl' }}

<p>
You can add your question(s) 
<a href="http://{{ uripath }}?f_interview_id={{ $sf->interview->identifier }}&amp;interview_action=">here</a>. 

<p>
Best regards,<br>
your {{ $sf->publication->site }} team.


{{ assign var='subject' value='Hallo' }}
{{ assign var='sender' value='Interview notifier <ich@interview>' }}