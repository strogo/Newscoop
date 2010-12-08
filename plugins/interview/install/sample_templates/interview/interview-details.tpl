<!-- {{ $smarty.template }} -->

<table border=1 width=100%>
    <tr><td width=150>InterviewId</td><td>{{ $sf->interview->identifier }}</td></tr>
    <tr><td>Title</td><td>{{ $sf->interview->title }}</td></tr>
    <tr><td>Language</td><td>{{ $sf->interview->language->Name }}</td></tr>
    <tr><td>Moderator</td><td>{{ $sf->interview->moderator->Name }}</td></tr>
    <tr><td>Guest</td><td>{{ $sf->interview->guest->Name }}</td></tr>
    <tr><td>Thumbnail</td><td>{{ if $sf->interview->image->thumbnailurl }}<img src="{{ $sf->interview->image->thumbnailurl }}">{{ /if }}</td></tr>
    <tr><td>Description (short)</td><td>{{ $sf->interview->description_short }}</td></tr>
    <tr><td>Description</td><td>{{ $sf->interview->description }}</td></tr>
    <tr><td>Interview Begin</td><td>{{ $sf->interview->interview_begin|camp_date_format:'%Y-%m-%d %H:%i' }}</td></tr>
    <tr><td>Interview End</td><td>{{ $sf->interview->interview_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td></tr>
    <tr><td>Questions Begin</td><td>{{ $sf->interview->questions_begin|camp_date_format:'%Y-%m-%d %H:%i' }}</td></tr>
    <tr><td>Questions End</td><td>{{ $sf->interview->questions_end|camp_date_format:'%Y-%m-%d %H:%i' }}</td></tr>
    <tr><td>Questions Limit</td><td>{{ $sf->interview->questions_limit }}</td></tr>
    <tr><td>Status</td><td>{{ $sf->interview->status }}</td></tr>
    <tr><td>Order</td><td>{{ $sf->interview->order }}</td></tr>
</table>


<!-- /{{ $smarty.template }} -->