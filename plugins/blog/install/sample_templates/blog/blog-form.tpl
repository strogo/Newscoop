<div style="width</td><td>250px; border</td><td>1px solid #000; padding</td><td>6px">

{{ if $sf->user->identifier && ($smarty.request.f_blog_action || $sf->blog_action->defined) }}

    {{ if $sf->blog_action->defined }}
    
        {{ if $sf->blog_action->ok }}
        
            <p>
            <i><b>Blog saved</b></i>
                    
            <table border=1>
            <tr><td>identifier</td><td>{{ $sf->blog->identifier }}</td></tr>
            <tr><td>language_id</td><td>{{ $sf->blog->language_id }}</td></tr>
            <tr><td>user_id</td><td>{{ $sf->blog->user_id }}</td></tr>
            <tr><td>title</td><td>{{ $sf->blog->title }}</td></tr>
            <tr><td>info</td><td>{{ $sf->blog->info }}</td></tr>
            <tr><td>date</td><td>{{ $sf->blog->published|camp_date_format }}</td></tr>
            <tr><td>admin_remark</td><td>{{ $sf->blog->admin_remark }}</td></tr>
            <tr><td>request_text</td><td>{{ $sf->blog->request_text }}</td></tr>
            <tr><td>status</td><td>{{ $sf->blog->status }}</td></tr>
            <tr><td>admin_status</td><td>{{ $sf->blog->admin_status }}</td></tr>
            <tr><td>entries_online</td><td>{{ $sf->blog->entries_online }}</td></tr>
            <tr><td>entries_offline</td><td>{{ $sf->blog->entries_offline }}</td></tr>
            </table>
            
        {{ else }}
        
            <p>
            <i><b>{{ $sf->blog_action->error_message }}</b></i>
            
            {{ blog_form}}
                <table>
                <tr><th colspan="2" align="left">Edit your Blog:</th></tr>
                <tr><td>Title</td><td>{{ blog_edit attribute=title html_code="size=39" }}</td></tr>
                <tr><td>Info</td><td> {{ blog_edit attribute=info wysiwyg=1 html_code="rows=6 cols=30" }}</td></tr>
                <tr><td>Request text</td><td> {{ blog_edit attribute=request_text  html_code="rows=6 cols=30" }}</td></tr>
                <tr><td></td><td><input type="submit"></td></tr>
                </table>
            {{ /blog_form }} 
                  
        {{ /if }} 
        
    {{ elseif $smarty.request.f_blog_action == 'edit' }}
    
            {{ blog_form}}
                <table>
                <tr><th colspan="2" align="left">Edit your Blog:</th></tr>
                <tr><td>Title</td><td>{{ blog_edit attribute=title html_code="size=39" }}</td></tr>
                <tr><td>Info</td><td> {{ blog_edit attribute=info wysiwyg=1 html_code="rows=6 cols=30" }}</td></tr>
                <tr><td>Request text</td><td> {{ blog_edit attribute=request_text  html_code="rows=6 cols=30" }}</td></tr>
                <tr><td>Status</td><td> {{ blog_edit attribute=status }}</td></tr>
                <tr><td></td><td><input type="submit"></td></tr>
                </table>
            {{ /blog_form }} 
        
    {{ elseif $smarty.request.f_blog_action == 'create' }}

        {{ list_blogs constraints="user_id is `$sf->user->identifier`" length=1 }}
            You already have an blog. Edit <a href="{{ uri }}&amp;f_blog_action=edit">here</a>.
        {{ /list_blogs }}
        
        {{ if $sf->prev_list_empty }}
        
            {{ blog_form}}
                <table>
                <tr><th colspan="2" align="left">Create your Blog:</th></tr>
                <tr><td>Title</td><td>{{ blog_edit attribute=title html_code="size=39" }}</td></tr>
                <tr><td>Info</td><td> {{ blog_edit attribute=info wysiwyg=1  html_code="rows=6 cols=30"}}</td></tr>
                <tr><td>Request text</td><td> {{ blog_edit attribute=request_text  html_code="rows=6 cols=30" }}</td></tr>
                <tr><td>Status</td><td> {{ blog_edit attribute=status }}</td></tr>
                <tr><td></td><td><input type="submit"></td></tr>
            </table>
            {{ /blog_form }} 
            
        {{ /if }}
    {{ /if }}
    
{{ elseif $sf->blogcomment->defined || $sf->blogentry->defined || $sf->blog->defined }}

    {{ if $sf->blogcomment->defined }}
    
        <h3>Blogcomment Details</h3>
        <table border=1>
        <tr><td>identifier</td><td>{{ $sf->blogcomment->identifier }}</td></tr>
        <tr><td>entry_id</td><td>{{ $sf->blogcomment->entry_id }}</td></tr>
        <tr><td>blog_id</td><td>{{ $sf->blogcomment->blog_id }}</td></tr>
        <tr><td>user_id</td><td>{{ $sf->blogcomment->user_id }}</td></tr>
        <tr><td>date</td><td>{{ $sf->blogcomment->published }}</td></tr>
        <tr><td>title</td><td>{{ $sf->blogcomment->title }}</td></tr>
        <tr><td>content</td><td>{{ $sf->blogcomment->content }}</td></tr>
        <tr><td>mood</td><td>{{ $sf->blogcomment->mood }}</td></tr>
        <tr><td>status</td><td>{{ $sf->blogcomment->status }}</td></tr>
        <tr><td>admin_status</td><td>{{ $sf->blogcomment->admin_status }}</td></tr>
        </table>
        <p>
        
    {{ elseif $sf->blogentry->defined }}
    
        <h3>Blogentry Details</h3>
        <table border=1>
        <tr><td>identifier</td><td>{{ $sf->blogentry->identifier }}</td></tr>
        <tr><td>user_id</td><td>{{ $sf->blogentry->user_id }}</td></tr>
        <tr><td>date</td><td>{{ $sf->blogentry->published }}</td></tr>
        <tr><td>released</td><td>{{ $sf->blogentry->released }}</td></tr>
        <tr><td>status</td><td>{{ $sf->blogentry->status }}</td></tr>
        <tr><td>title</td><td>{{ $sf->blogentry->title }}</td></tr>
        <tr><td>content</td><td>{{ $sf->blogentry->content }}</td></tr>
        <tr><td>tags</td><td>{{ $sf->blogentry->tags }}</td></tr>
        <tr><td>mood</td><td>{{ $sf->blogentry->mood }}</td></tr>
        <tr><td>admin_status</td><td>{{ $sf->blogentry->admin_status }}</td></tr>
        <tr><td>comments_online</td><td>{{ $sf->blogentry->comments_online }}</td></tr>
        <tr><td>comments_offline</td><td>{{ $sf->blogentry->comments_offline }}</td></tr>
        <tr><td>feature</td><td>{{ $sf->blogentry->feature }}</td></tr>
        <tr><td>image</td><td><img src="{{ $sf->blogentry->images.90x90 }}" /></td></tr>
        </table>
       
        {{ if $sf->preview_blogcomment_action->defined }}
            <p>
            {{ if $sf->preview_blogcomment_action->ok }} 
                <b>Blogcomment preview:</b>
                <p>
                Title: {{ $sf->preview_blogcomment_action->title }}<br>
                Comment: {{ $sf->preview_blogcomment_action->content }}<br>
                Mood: {{ $sf->preview_blogcomment_action->mood }}
            {{ else }}
                <b><i>{{ $sf->preview_blogcomment_action->error_message }}</i></b> 
            {{ /if }}
                   
            {{ blogcomment_form submit_button="Submit" preview_button="Preview" }}
                <p>
                <table>
                <tr><th colspan="2" align="left">Add Comment:</th></tr>
                {{ if !$sf->user->identifier }}
                    <tr><td>Your Name</td><td>{{ blogcomment_edit attribute=user_name }}</td></tr>
                    <tr><td>Your EMail</td><td> {{ blogcomment_edit attribute=user_email }}</td></tr>
                {{ /if }}
                <tr><td>Title</td><td>{{ blogcomment_edit attribute=title  html_code="size=39" }}</td></tr>
                <tr><td>Comment</td><td> {{ blogcomment_edit attribute=content wysiwyg=1 html_code="rows=6 cols=30"}}</td></tr>
                <tr><td>Mood</td><td> {{ blogcomment_edit attribute=mood  html_code="size=39" }}</td></tr>
                <tr><td><img src="{{ captcha_image_link }}"></td><td>{{ camp_edit object="captcha" attribute="xxcode" }}</td></tr>
                </table>
            {{ /blogcomment_form }}
                
        {{ elseif $sf->submit_blogcomment_action->defined }}
    
            {{ if $sf->submit_blogcomment_action->ok }}
                <p>
                <i><b>Comment added</b></i>
            {{ else }}
                <p>
                <i><b>{{ $sf->submit_blogcomment_action->error_message }}</b></i>
                {{ blogcomment_form submit_button="Submit" preview_button="Preview" }}
                    <table>
                    {{ if !$sf->user->identifier }}
                        <tr><td>Your Name</td><td>{{ blogcomment_edit attribute=user_name }}</td></tr>
                        <tr><td>Your EMail</td><td> {{ blogcomment_edit attribute=user_email }}</td></tr>
                    {{ /if }}
                    <tr><td>Title</td><td>{{ blogcomment_edit attribute=title  html_code="size=39" }}</td></tr>
                    <tr><td>Comment</td><td> {{ blogcomment_edit attribute=content wysiwyg=1 html_code="rows=6 cols=30"}}</td></tr>
                    <tr><td>Mood</td><td> {{ blogcomment_edit attribute=mood  html_code="size=39" }}</td></tr>
                    <tr><td><img src="{{ captcha_image_link }}"></td><td>{{ camp_edit object="captcha" attribute="xxcode" }}</td></tr>
                    </table>
                {{ /blogcomment_form }}
            {{ /if }}
        {{ else }}
            <p>
            {{ blogcomment_form submit_button="Submit" preview_button="Preview" }}
                <table>
                <tr><th colspan="2" align="left">Add Comment:</th></tr>
                {{ if !$sf->user->identifier }}
                    <tr><td>Your Name</td><td>{{ blogcomment_edit attribute=user_name }}</td></tr>
                    <tr><td>Your EMail</td><td> {{ blogcomment_edit attribute=user_email }}</td></tr>
                {{ /if }}
                <tr><td>Title</td><td>{{ blogcomment_edit attribute=title html_code="size=39" }}</td></tr>
                <tr><td>Comment</td><td> {{ blogcomment_edit attribute=content  wysiwyg=1 html_code="rows=6 cols=30" }}</td></tr>
                <tr><td>Mood</td><td> {{ blogcomment_edit attribute=mood  html_code="size=39" }}</td></tr>
                <tr><td><img src="{{ captcha_image_link }}"></td><td>{{ camp_edit object="captcha" attribute="code" }}</td></tr>
                </table>
            {{ /blogcomment_form }}  
        {{ /if }} 
    {{ elseif $sf->blog->defined }}
    
    {{ $sf->url->set_parameter('f_blog_id', $sf->blog->identifier) }}
    
        <h3>Blog Details</h3>
          
        <table border=1>
        <tr><td>identifier</td><td>{{ $sf->blog->identifier }}</td></tr>
        <tr><td>language_id</td><td>{{ $sf->blog->language_id }}</td></tr>
        <tr><td>user_id</td><td>{{ $sf->blog->user_id }}</td></tr>
        <tr><td>title</td><td>{{ $sf->blog->title }}</td></tr>
        <tr><td>date</td><td>{{ $sf->blog->published|camp_date_format }}</td></tr>
        <tr><td>admin_remark</td><td>{{ $sf->blog->admin_remark }}</td></tr>
        <tr><td>request_text</td><td>{{ $sf->blog->request_text }}</td></tr>
        <tr><td>status</td><td>{{ $sf->blog->status }}</td></tr>
        <tr><td>admin_status</td><td>{{ $sf->blog->admin_status }}</td></tr>
        <tr><td>entries_online</td><td>{{ $sf->blog->entries_online }}</td></tr>
        <tr><td>entries_offline</td><td>{{ $sf->blog->entries_offline }}</td></tr>
        </table>

        {{ if $sf->user->identifier == $sf->blog->user_id }}
             {{ if $sf->blogentry_action->defined }}
        
                {{ if $sf->blogentry_action->ok }}
                
                    <p>
                    <i><b>Entry added</b></i>
                    
                {{ else }}
                
                    <p>
                    <i><b>{{ $sf->blogentry_action->error_message }}</b></i>
                    
                    {{ blogentry_form}}
                        <table>
                        <tr><td>Title</td><td>{{ blogentry_edit attribute=title html_code="size=39" }}</td></tr>
                        <tr><td>Entry</td><td> {{ blogentry_edit attribute=content wysiwyg=1 html_code="rows=6 cols=30" }}</td></tr>
                        <tr><td>Mood</td><td> {{ blogentry_edit attribute=mood html_code="size=39" }}</td></tr>
                        <tr><td></td><td><input type="submit"></td></tr>
                        </table>
                    {{ /blogentry_form }}
                    
                {{ /if }}
                
            {{ else }}
                <p>
                {{ blogentry_form}}
                    <table>
                    <tr><th colspan=2 align=left>Add entry:</th></tr>
                    <tr><td>Title</td><td>{{ blogentry_edit attribute=title  html_code="size=39" }}</td></tr>
                    <tr><td>Entry</td><td> {{ blogentry_edit attribute=content  wysiwyg=1 html_code="rows=6 cols=30" }}</td></tr>
                    <tr><td>Mood</td><td> {{ blogentry_edit attribute=mood  html_code="size=39" }}</td></tr>
                    <tr><td></td><td><input type="submit"></td></tr>
                    </table>
                {{ /blogentry_form }}
                    
            {{ /if }}
        {{ /if }}
    {{ /if }}
{{ /if }}    


{{ if $sf->user->identifier && !($smarty.request.f_blog_action || $sf->blog_action->defined) }}
    <p>  
    {{ list_blogs constraints="user_id is `$sf->user->identifier`" length=1 }}
        <a href="{{ url }}&amp;f_blog_action=edit">Edit</a> your Blog settings.
    {{ /list_blogs }}
        
    {{ if $sf->prev_list_empty }}
        <a href="{{ url }}?f_blog_action=create">Create</a> your Blog.
    {{ /if }}
    
{{ /if }}