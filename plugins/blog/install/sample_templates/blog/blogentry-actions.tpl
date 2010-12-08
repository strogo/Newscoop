{{ if $sf->blog->user_id == $sf->user->identifier || $sf->user->has_permission('plugin_blog_admin') }}
    <small>
    <a href="{{ url }}&amp;f_blogaction=entry_delete">delete</a>
    </small>
{{ /if }}