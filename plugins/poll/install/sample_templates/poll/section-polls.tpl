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

    {{ if $sf->poll->defined }}
    
        <h3>Poll Details</h3>
        
        {{ include file='poll/poll-form-ajax.tpl' included=true}}
        
        <br>
        <a href="{{ uri options="template section-polls.tpl" }}">All Polls</a>
        
    {{ else }}
       
        <p>
        
        <tr>
            <th align="left">Name</th>
            <th>Voting Begin</th>
            <th>Voting End</th>
            <th>Current</th>
            <th>Alloved/Taken Votes</th>
            <th>Votes</th>
        </tr>  
        <tr><td colspan="6"><hr></td></tr>
        {{ local }}
  
        {{ list_polls name="polls_list" length="10" order='bylastmodified ASC' }}
           <tr align="center">
            <td align="left">
                <a href="{{ uri options="template section-polls.tpl" }}">
                    {{ $sf->poll->name }}
                </a>
            </td>
            <td>{{ $sf->poll->date_begin|date_format }}</td>
            <td>{{ $sf->poll->date_end|date_format }}</td>
            <td>{{ if $sf->poll->is_current }} Y {{ else }} N {{ /if }}</td>
            <td>{{ $sf->poll->votes_per_user }}/{{ $sf->poll->user_vote_count }}</td>
            <td>{{ $sf->poll->votes }}
          </tr>
           
        {{ if  $sf->current_list->at_end }}
        <tr><td colspan="6"><hr></td></tr>
        <tr>
            <td>{{ $sf->current_list->count }} Items</td>
            <td colspan="5">
                {{ if $sf->current_list->has_previous }}
                    <a href="{{ uripath }}?p_polls_list_start={{ $sf->current_list->previous }}">previous</a>
                {{ else }}
                    previous    
                {{ /if }}
                |
                {{ if $sf->current_list->has_next }}
                    <a href="{{ uripath }}?p_polls_list_start={{ $sf->current_list->next }}">next</a>
                {{ else }}
                    next
                {{ /if }}
            </td>
        </tr>
        {{ /if }}
           
        {{ /list_polls }}
        {{ /local }}
    
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