{{ if $sf->blog->user_id == $sf->user->identifier || $sf->user->has_permission('plugin_blog_admin') }}
    <small>
    <a href="{{ url }}&amp;f_blog_action=edit">edit</a>
    <a href="{{ url }}&amp;f_blogaction=blog_delete">delete</a>
    </small>
{{ /if }}