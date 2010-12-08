<!-- {{ $smarty.template }} -->

<table border="1" width=100%>
    <tr><td>Item Id:</td><td>{{ $sf->interviewitem->identifier }}</td></tr>
    <tr><td width=150>Interview Id:</td><td>{{ $sf->interviewitem->interview_id }}</td></tr>
    <tr><td width=150>Questioneer:</td><td>{{ $sf->interviewitem->questioneer->Name }}</td></tr>
    <tr><td>Question:</td><td>{{ $sf->interviewitem->question }}</td></tr>
    <tr><td>Status:</td><td>{{ $sf->interviewitem->status }}</td></tr>
    <tr><td>Answer:</td><td>{{ $sf->interviewitem->answer }}</td></tr>
    <tr><td>Order</td><td>{{ $sf->interviewitem->order }}</td></tr>
</table>

<!-- /{{ $smarty.template }} -->