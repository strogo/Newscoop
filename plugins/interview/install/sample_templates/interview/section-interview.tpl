<!-- {{ $smarty.template }} -->

{{ include file="html_header.tpl" }}

<table class="main" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    <div id="breadcrubm">
    {{ breadcrumb }}
    </div>
    {{** main content area **}}
    <table class="content" cellspacing="0" cellpadding="0">
    <tr>
      <td>  
        {{ if $smarty.request.interview_action || $sf->interview_action->defined }}
        
            {{ include file='interview-action.tpl }} 

        {{ elseif $smarty.request.interviewitem_action || $sf->interviewitem_action->defined }}
            
                {{ include file='interview/interviewitem-action.tpl }} 
                
        {{ elseif $sf->interview->defined }}
        
            {{ include file='interview/interview-details.tpl' }}
            <br>
            
            {{ if $sf->interview->in_questions_timeframe }}
                <a href="{{ uripath }}?f_interview_id={{ $sf->interview->identifier }}&amp;interviewitem_action=form">Add your question</a>
                <br>
                
                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $sf->interview->identifier }}&amp;interviewitem_action=list">List existing questions</a>
                    <br>
                {{ /list_interviewitems }}
                 
             {{ elseif $sf->interview->in_interview_timeframe }}

                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $sf->interview->identifier }}&amp;interviewitem_action=list">List existing questions</a>
                    <br>
                {{ /list_interviewitems }}

             {{ elseif $sf->interview->status == 'published' }}

                {{ list_interviewitems length=1 constraints='status not rejected' }}
                    <a href="{{ uripath }}?f_interview_id={{ $sf->interview->identifier }}&amp;interviewitem_action=list">Show interview</a>
                    <br>
                {{ /list_interviewitems }}

            {{ /if }}

             
        {{ else }}

            {{ if $sf->user->defined }}
                {{ if $smarty.request.f_interviewnotify == 'on' }}   
                    You will recive interview notifications<br>
                {{ elseif $smarty.request.f_interviewnotify == 'off' }}        
                    You will not recive interview notifications<br>
                {{ /if }}
                    
                {{ if $sf->user->has_permission('plugin_interview_notify') }}
                    <a href="{{ uripath }}?{{ urlparameters }}&amp;f_interviewnotify=off">Do not notify me about new interviews</a>
                {{ elseif  !$sf->user->has_permission('plugin_interview_notify') }}
                    <a href="{{ uripath }}?{{ urlparameters }}&amp;f_interviewnotify=on">Notify me about new interviews</a>
                {{ /if }}
                
            {{ else }}
                <a href="javascript: if (confirm('You need to subscribe to use this funtion. Do you want to go to subscription page?')) location.href='{{ uri options="template user_form.tpl" }}'">Notify me about new interviews</a>
            {{ /if }}
                
            <h4>Interviews awaiting questions:</h4>
            {{ include file='interview/interviews-list.tpl' _constraints="status is pending questions_begin smaller_equal now() questions_end greater_equal now()"}} 
        
            <h4>Interviews awaiting answers:</h4>
            {{ include file='interview/interviews-list.tpl' _constraints="status is pending interview_begin smaller_equal now() interview_end greater_equal now()"}}
            
            <h4>Interviews already answered:</h4>
            {{ include file='interview/interviews-list.tpl' _constraints="status is published"}}
        {{ /if }}  
        
      </td>
    </tr>
    </table>
    {{** end main content area **}}
  </td>
  <td valign="top">
    {{ include file="html_rightbar.tpl" }}
  </td>
</tr>
</table>
{{ include file="html_footer.tpl" }}

<!-- /{{ $smarty.template }} -->
