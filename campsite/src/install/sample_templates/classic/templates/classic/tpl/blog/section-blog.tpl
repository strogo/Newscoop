{{ include file="classic/tpl/header.tpl" }}

<body id="blog" class="section-{{ $sf->section->number }}">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

{{ include file="classic/tpl/headernav.tpl" }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerleaderboard.tpl" }}

<div class="colmask rightmenu">
    <div class="colleft">
        <div class="col1wrap">
            <div class="col1">
                <!-- Column 1 start -->

{{ if !$sf->blog->defined && !$sf->blogentry->defined }}  
          
    {{ list_blogs name="blogs_list" length="20" order="byidentifier desc"}}
        <div class="teaserframe teaserframebig teaserframe-{{ $sf->section->number }} teaserframebig-{{ $sf->section->number }}">
        <div class="teaserframebiginner">
          <div class="teaserhead">
          <div class="teaserheadinner">
            {{ $sf->blog->published|camp_date_format:'%Y-%M-%D' }}
          </div><!-- .teaserheadinner -->
          </div><!-- .teaserhead -->

            {{ if $sf->blog->images.100x100 }}
            <!-- blog image -->
              <div class="blog_img">
              <a href="{{ url options="template classic/tpl/blog/section-blog.tpl" }}"><img src="{{ $sf->blog->images.100x100 }}" border="0" /></a>
              </div>
            <!-- /blog image -->
            {{ /if }}
        
          <div class="teasercontent content">
                <h3 class="deck deck_med">{{ $sf->blog->user->name }}</h3>
                <h2 class="title title_med"><a href="{{ url options="template classic/tpl/blog/section-blog.tpl" }}">{{ $sf->blog->name }}</a></h2>
                <p class="text">{{ $sf->blog->info|truncate:250 }}</p>
                <p class="text">{{ if $sf->language->name == "English" }}Entries{{ else }}Mensajes{{ /if }}: {{ $sf->blog->entries_online }}</p>
                <p class="text">{{ if $sf->language->name == "English" }}Comments{{ else }}Comentarios{{ /if }}: {{ $sf->blog->comments_online }}</p>
                <ul class="links">
                  <li><a href="{{ url options="template classic/tpl/blog/section-blog.tpl" }}">{{ if $sf->language->name == "English" }}Read the blog{{ else }}Lea el blog{{ /if }}</a>
              </ul>
          </div><!-- .teasercontent content -->
        </div><!-- .teaserframebiginner -->
        </div><!-- .teaserframebig -->
    {{ /list_blogs }}

{{ $sf->url->set_parameter('f_blog_id', $sf->blog->identifier) }}
{{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
<form action="" name="Permform" id="Permform"> 
Permalink:
<input name="Permalink" type="text" value="{{ url options="template classic/tpl/blog/section-blog.tpl" }}" onClick="javascript:document.Permform.Permalink.focus();document.Permform.Permalink.select();" readonly>
<div id="permalinkURI">{{ url options="template classic/tpl/blog/section-blog.tpl" }}</div>
</form>
{{ $sf->url->reset_parameter('f_blog_id') }}
{{ $sf->url->reset_parameter('f_blogentry_id') }}

{{ elseif !$sf->blogentry->defined }}  

    <div class="teaserframe teaserframebig teaserframe-{{ $sf->section->number }} teaserframebig-{{ $sf->section->number }}">
    <div class="teaserframebiginner">
      <div class="teaserhead">
      <div class="teaserheadinner">
        {{ $sf->blog->published|camp_date_format:'%Y-%M-%D' }}
      </div><!-- .teaserheadinner -->
      </div><!-- .teaserhead -->
      
        {{ if $sf->blog->images.100x100 }}
        <!-- blog image -->
          <div class="blog_img">
             <img src="{{ $sf->blog->images.100x100 }}" border="0" />
          </div>
        <!-- /blog image -->
        {{ /if }}

      <div class="teasercontent content">
            <h3 class="deck deck_med">{{ $sf->blog->user->name }}</h3>
            <h2 class="title title_med">{{ $sf->blog->name }}</h2>
            <p class="text">{{ $sf->blog->info|truncate:250 }}</p>
            <p class="text">{{ if $sf->language->name == "English" }}Comments{{ else }}Comentarios{{ /if }}: {{ $sf->blog->comments_online }}</p>
            <p class="text">
            <!-- tags -->
             {{ if $sf->language->name == "English" }}Tags{{ else }}Etiquetas{{ /if }}:
             {{ list_blog_topics }}
                 {{ $sf->topic->name }}&nbsp;&nbsp;
             {{ /list_blog_topics }}
            </p>
      </div><!-- .teasercontent content -->
    </div><!-- .teaserframebiginner -->
    </div><!-- .teaserframebig -->

{{ $sf->url->set_parameter('f_blog_id', $sf->blog->identifier) }}
{{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
<form action="" name="Permform" id="Permform"> 
Permalink:
<input name="Permalink" type="text" value="{{ url options="template classic/tpl/blog/section-blog.tpl" }}" onClick="javascript:document.Permform.Permalink.focus();document.Permform.Permalink.select();" readonly>
<div id="permalinkURI">{{ url options="template classic/tpl/blog/section-blog.tpl" }}</div>
</form>
{{ $sf->url->reset_parameter('f_blog_id') }}
{{ $sf->url->reset_parameter('f_blogentry_id') }}
    
    {{ list_blogentries name="blogentries_list" length="20" order="byidentifier desc" }}
    
        {{ if $sf->current_list->at_beginning }}
          <h3>{{ if $sf->language->name == "English" }}Entries{{ else }}Mensajes{{ /if }}:{{ $sf->blog->entries_online }}</h3>
        {{ /if }}
        
        <div class="teaserframe teaserframebig teaserframe-{{ $sf->section->number }} teaserframebig-{{ $sf->section->number }}">
        <div class="teaserframebiginner">
          <div class="teaserhead">
          <div class="teaserheadinner">
            {{ $sf->blogentry->published|camp_date_format:'%Y-%M-%D' }}
          </div><!-- .teaserheadinner -->
          </div><!-- .teaserhead -->
          
            {{ if $sf->blogentry->images.100x100 }}
            <!-- blogentry image -->
              <div class="blogentry_img">
              <a href="{{ url options="template classic/tpl/blog/section-blog.tpl" }}"><img src="{{ $sf->blogentry->images.100x100 }}" border="0" /></a>
              </div>
            <!-- /blogentry image -->
            {{ /if }}
        
          <div class="teasercontent content">
              <h2 class="title title_med"><a href="{{ url options="template classic/tpl/blog/section-blog.tpl" }}">{{ $sf->blogentry->title }}</a></h2>
                <p class="text">{{ $sf->blogentry->content }}</p>
                {{ if strlen($sf->blogentry->mood->name ) }}
                    <p class="text">{{ if $sf->language->name == "English" }}Mood{{ else }}Estado de ánimo{{ /if }}: {{ $sf->blogentry->mood->name }}</p>
                {{ /if }}
                <ul class="links">
                  <li><a href="{{ url options="template classic/tpl/blog/section-blog.tpl" }}">{{ if $sf->language->name == "English" }}Comments{{ else }}Comentarios{{/if}}: {{ $sf->blogentry->comments_online }}</a>
                </ul>
          </div><!-- .teasercontent content -->
        </div><!-- .teaserframebiginner -->
        </div><!-- .teaserframebig -->  
        
    {{ /list_blogentries }}

{{ else }}  
    
    {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
    <div class="teaserframe teaserframebig teaserframe-{{ $sf->section->number }} teaserframebig-{{ $sf->section->number }}">
      <div class="teaserframebiginner">
        <div class="teaserhead">
        <div class="teaserheadinner">
          {{ $sf->blogentry->published|camp_date_format:'%Y-%M-%D' }}
        </div><!-- .teaserheadinner -->
        </div><!-- .teaserhead -->
        
        {{ if $sf->blogentry->images.100x100 }}
        <!-- blogentry image -->
          <div class="blogentry_img">
          <a href="{{ url options="template classic/tpl/blog/section-blog.tpl" }}" ><img src="{{ $sf->blogentry->images.100x100 }}" border="0" /></a>
          </div>
        <!-- /blogentry image -->
        {{ /if }}
      
        <div class="teasercontent content">
        
            <h3 class="deck deck_med">
             {{ $sf->blog->user->name }}
             |
                 {{ $sf->url->reset_parameter("f_blogentry_id") }}
                 {{ $sf->url->set_parameter("f_blog_id", $sf->blog->identifier) }}
             <a href="{{ uri options="template classic/tpl/blog/section-blog.tpl" }}">{{ $sf->blog->title }}</a>
                 {{ $sf->url->reset_parameter("f_blogentry_id", $sf->blog->identifier) }}
                 {{ $sf->url->set_parameter("f_blogentry_id") }}
            </h3>
            <h2 class="title title_med">{{ $sf->blogentry->title }}</h2>
            <p class="text">{{ $sf->blogentry->content }}</p>
            <p class="text">
             {{ if $sf->language->name == "English" }}Tags{{ else }}Etiquetas{{ /if }}:
             {{ list_blogentry_topics }}
                 {{ $sf->topic->name }}&nbsp;&nbsp;
             {{ /list_blogentry_topics }}
            </p>
            {{ if strlen($sf->blogentry->mood->name ) }}
                <p class="text">{{ if $sf->language->name == "English" }}Mood{{ else }}Estado de ánimo{{ /if }}: {{ $sf->blogentry->mood->name }}</p>
            {{ /if }}
        </div><!-- .teasercontent content -->
      </div><!-- .teaserframebiginner -->
    </div><!-- .teaserframebig -->  
    
    {{ $sf->url->reset_parameter('f_blogentry_id') }}

{{ $sf->url->set_parameter('f_blog_id', $sf->blog->identifier) }}
{{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
<form action="" name="Permform" id="Permform"> 
Permalink:
<input name="Permalink" type="text" value="{{ url options="template classic/tpl/blog/section-blog.tpl" }}" onClick="javascript:document.Permform.Permalink.focus();document.Permform.Permalink.select();" readonly>
<div id="permalinkURI">{{ url options="template classic/tpl/blog/section-blog.tpl" }}</div>
</form>
{{ $sf->url->reset_parameter('f_blog_id') }}
{{ $sf->url->reset_parameter('f_blogentry_id') }}

    {{ list_blogcomments name="blogcomments_list" length="100" }}
    
        {{ if $sf->current_list->at_beginning }}
          <h3>{{ if $sf->language->name == "English" }}Comments{{ else }}Comentarios{{ /if }}: {{ $sf->blogentry->comments_online }}</h3>
        {{ /if }}
        
        <div class="teaserframe teaserframebig teaserframe-{{ $sf->section->number }} teaserframebig-{{ $sf->section->number }}">
        <div class="teaserframebiginner">
          <div class="teaserhead">
          <div class="teaserheadinner">
            {{ $sf->blogcomment->published|camp_date_format:'%Y-%M-%D' }}
          </div><!-- .teaserheadinner -->
          </div><!-- .teaserhead -->
        
          <div class="teasercontent content">
              <h3 class="deck deck_med">{{ $sf->blogcomment->user->name }}</h3>
              <p class="text">{{ $sf->blogcomment->content }}</p>
              <p class="text">
              {{ if strlen($sf->blogcomment->mood->name ) }}
                    <p class="text">{{ if $sf->language->name == "English" }}Mood{{ else }}Estado de ánimo{{ /if }}: {{ $sf->blogcomment->mood->name }}</p>
                {{ /if }}
              </p>
          </div><!-- .teasercontent content -->
        </div><!-- .teaserframebiginner -->
        </div><!-- .teaserframebig -->  
        
    {{ /list_blogcomments }}
    
    {{ include file="classic/tpl/blog/comment-handler.tpl" }}

{{ /if }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerleftcol.tpl" }}

        <!-- Column 1 end -->
            </div>
        </div>
        <div class="col2">
            <!-- Column 2 start -->

{{ include file="classic/tpl/search-box.tpl" }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerrightcol.tpl" }}

{{ list_articles name="articles_right" constraints="OnSection is on" ignore_issue="true" length="9" }}
{{ include file="classic/tpl/teaserframe_articlelistright.tpl" }}
{{ local }}
{{ unset_article }}
{{ include file="classic/tpl/pagination.tpl" }}
{{ /local }}

{{ /list_articles }}


      <!-- Column 2 end -->

        </div>
    </div>
</div>

{{ include file="classic/tpl/footer.tpl" }}

</div><!-- id="wrapbg"-->
</div><!-- id="wrapper"-->
</div><!-- id="container"-->
</body>
</html>
