{{ if $sf->preview_blogcomment_action->defined }}
    <p>
    {{ if $sf->preview_blogcomment_action->ok }} 
        <b>{{ if $sf->language->name == "English" }}Blogcomment preview:{{ else }}Vista previa de blog comentario:{{ /if }}</b>
        <p>{{ if $sf->language->name == "English" }}Your Name{{ else }}Su nombre{{ /if }}: {{ $sf->preview{{ if $sf->language->name == "English" }}Your Name{{ else }}Su nombre{{ /if }}nt_action->user_name }}<br>
        {{ if $sf->language->name == "English" }}Your e-mail{{ else }}Su e-mail{{ /if }}: {{ $sf->preview_blogcomment_action->user_email }}<br>
        {{ if $sf->language->name == "English" }}Title{{ else }}Título{{ /if }}: {{ $sf->preview_blogcomment_action->title }}<br>
        {{ if $sf->language->name == "English" }}Comment{{ else }}Comentar{{ /if }}: {{ $sf->preview_blogcomment_action->content }}<br>
        {{ if $sf->language->name == "English" }}Mood{{ else }}Estado de ánimo{{ /if }}: {{ $sf->preview_blogcomment_action->mood->name }}
    {{ else }}
        <b><i>{{ $sf->preview_blogcomment_action->error_message }}</i></b> 
    {{ /if }}
           
    {{ include file="classic/tpl/blog/comment-form.tpl" }} 
        
{{ elseif $sf->submit_blogcomment_action->defined }}

    {{ if $sf->submit_blogcomment_action->ok }}
        <p>
        <i><b>{{ if $sf->language->name == "English" }}Comment added{{ else }}Comentario añadido{{ /if }}</b></i>
    {{ else }}
        <p>
        <i><b>{{ $sf->submit_blogcomment_action->error_message }}</b></i>
        {{ include file="classic/tpl/blog/comment-form.tpl" }} 
    {{ /if }}
    
{{ else }}

    {{ if $sf->blog->comment_mode == 'login' && !$sf->user->exists }}  
        {{ if $sf->language->name == "English" }}You need to register/login to post blog comments{{ else }}Es necesario registrarse / iniciar sesión para enviar comentarios del blog{{ /if }}.     
    {{ else }}
        {{ include file="classic/tpl/blog/comment-form.tpl" }}  
    {{ /if }}
    
{{ /if }}
