<div class="logintop">

{{ if $sf->url->get_parameter('logout') }}
    <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
    <META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
    {{ $sf->url->set_parameter('f_blog_id', $sf->blog->identifier) }}
    {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
    {{ $sf->url->set_parameter('tpl', $sf->default_template->identifier) }}
    {{ $sf->url->set_parameter('tpid', $sf->default_topic->identifier) }}
    {{ $sf->url->reset_parameter('logout') }}
    <META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
{{ /if }}

{{ if !$sf->user->logged_in }}
    {{ local }}
        {{ unset_section }}
        <div id="register">
        <a href="{{ uri options="template classic/register.tpl" }}">{{ if $sf->language->name == "English" }}Register{{ else }}Registrarse{{ /if }}</a> |
        </div>
    {{ /local }}
{{ /if }}

         
{{ if $sf->user->logged_in }} 
 
    {{ unset_section }}
    <div id="user"><a href="{{ uri options="template classic/register.tpl" }}">{{ $sf->user->name }}</a></div>
    {{ set_default_section }}
    {{ set_default_article }}

    {{ $sf->url->set_parameter('f_blog_id', $sf->blog->identifier) }}
    {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
    {{ $sf->url->set_parameter('tpl', $sf->default_template->identifier) }}
    {{ $sf->url->set_parameter('tpid', $sf->default_topic->identifier) }}
    {{ $sf->url->set_parameter('logout', 1) }}
    
    <div id="logout"><a href="{{ uri }}">{{ if $sf->language->name == "English" }}Logout{{ else }}Desconectarse{{ /if }}</a></div>
    
    {{ $sf->url->reset_parameter('f_blog_id') }}
    {{ $sf->url->reset_parameter('f_blogentry_id') }}
    {{ $sf->url->reset_parameter('tpl') }}
    {{ $sf->url->reset_parameter('tpid') }}
    {{ $sf->url->reset_parameter('logout') }}

{{ else }}
 
    {{ $sf->url->set_parameter('f_blog_id', $sf->blog->identifier) }}
    {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
    {{ $sf->url->set_parameter('tpl', $sf->default_template->identifier) }}
    {{ $sf->url->set_parameter('tpid', $sf->default_topic->identifier) }}
    
    <div id="singin"> {{ if $sf->language->name == "English" }}Sign in{{ else }}Entra{{ /if }}: </div>
    {{ login_form submit_button="Send" }}
          {{ camp_edit object="login" attribute="uname" }}
          {{ camp_edit object="login" attribute="password" }}
    {{ /login_form }}

    {{ if $sf->login_action->is_error }}
        <div class="loginerror"><div class="loginerrorinner">
        {{ $sf->login_action->error_message }}
        </div></div>
    {{ /if }}
    
    {{ $sf->url->reset_parameter('f_blog_id') }}
    {{ $sf->url->reset_parameter('f_blogentry_id') }}
    {{ $sf->url->reset_parameter('tpl') }}
    {{ $sf->url->reset_parameter('tpid') }}
    
{{ /if }}

<div class="clear"></div>
</div><!-- .logintop -->


