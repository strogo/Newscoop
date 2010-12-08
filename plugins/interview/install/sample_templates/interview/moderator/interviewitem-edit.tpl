<h6>{{ $smarty.template }}</h6>

<p>Interview: <a href="{{ uripath }}?f_interview_id={{ $sf->interview->identifier }}">{{ $sf->interview->title }}</a></p>


{{ if $sf->interviewitem_action->defined }}

OK: {{ if $sf->interviewitem_action->ok }} true {{ else }} false {{ /if }}<br>


    {{ if $sf->interviewitem_action->error }}
        <h6>Form Errors:</h6>
        
        <font color="red">{{ $sf->interviewitem_action->error->message }}</font>
        <p>
        
        {{ include file='interview/moderator/interviewitem-form.tpl' }}
        
    {{ else }}
         <h6>Interviewitem saved</h6>
         {{ include file='interview/interviewitem-details.tpl' }}
         {{ include file='interview/moderator/interviewitem-actions.tpl' }}
    {{ /if }}
    
{{ else }}

    {{ include file='interview/moderator/interviewitem-form.tpl' }}
    
{{ /if }}