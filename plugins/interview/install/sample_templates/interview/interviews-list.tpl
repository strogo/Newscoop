<!-- {{ $smarty.template }} -->

{{ list_interviews length=10 constraints=$_constraints }}
    {{ if $sf->current_interviews_list->at_beginning }}
    <table border="1" width="100%">
        <tr>
            <th>Title</th>
            <th>Questios Timeframe</th>
            <th>Interview Timeframe</th>
            <th>Status</th>
            <th>Moderator</th>
            <th>Guest</th>
        </tr>
    {{ /if }}
    
    <tr>
        <td><a href="{{ uripath }}?f_interview_id={{ $sf->interview->identifier }}">{{ $sf->interview->title }}</a></td>
        <td>{{ $sf->interview->questions_begin|camp_date_format:'%Y-%m-%d %H:%i' }} - {{ $sf->interview->questions_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td>
        <td>{{ $sf->interview->interview_begin|camp_date_format:'%Y-%m-%d %H:%i' }} - {{ $sf->interview->interview_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td>
        <td>{{ $sf->interview->status }}</td>
        <td>{{ $sf->interview->moderator->name }}</td>
        <td>{{ $sf->interview->guest->name }}</td>
    </tr>
    
    {{ if $sf->current_interviews_list->at_end }}
        </table>
    {{ /if }}
{{ /list_interviews }}

{{ if $sf->prev_list_empty }}
    No interview found
{{ /if }}

<!-- /{{ $smarty.template }} -->