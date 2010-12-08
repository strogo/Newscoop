{{ include file="classic/tpl/header.tpl" }}

<body id="topics">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

{{ include file="classic/tpl/headernav.tpl" }}

<div class="colmask rightmenu">
    <div class="colleft">
        <div class="col1wrap">
            <div class="col1">
                <!-- Column 1 start -->


{{ if $sf->topic->defined }}

  {{ list_articles ignore_issue="true" ignore_section="true" name="articles_left" }}
{{ include file="classic/tpl/teaserframe_articlelistleft.tpl" }}
  {{ /list_articles }}
  
     {{ list_blogentries name="blogentries_list" length="20" order="byidentifier desc" }}
    
        {{ if $sf->current_list->at_beginning }}
          <h3>{{ $sf->blog->entries_online }}:</h3>
        {{ /if }}
        
        <div class="teaserframe teaserframebig teaserframe-{{ $sf->section->number }} teaserframebig-{{ $sf->section->number }}">
        <div class="teaserframebiginner">
          <div class="teaserhead">
          <div class="teaserheadinner">
            {{ $sf->blogentry->published|camp_date_format:'%M %D, %Y %h:%i:%s' }}
          </div><!-- .teaserheadinner -->
          </div><!-- .teaserhead -->
          
            {{ if $sf->blogentry->images.100x100 }}
            <!-- blogentry image -->
              <div class="blogentry_img">
              <a href="{{ url }}"><img src="{{ $sf->blogentry->images.100x100 }}" border="0" /></a>
              </div>
            <!-- /blogentry image -->
            {{ /if }}
        
          <div class="teasercontent content">
              <h2 class="title title_med"><a href="{{ uri }}">{{ $sf->blogentry->title }}</a></h2>
                <p class="text">{{ $sf->blogentry->content }}</p>
                {{ if strlen($sf->blogentry->mood->name ) }}
                    <p class="text">{{ if $sf->language->name == "English" }}Mood:{{ else }}Estado de ánimo:{{ /if }} {{ $sf->blogentry->mood->name }}</p>
                {{ /if }}
                <ul class="links">
                  <li><a href="{{ uri }}">{{ if $sf->language->name == "English" }}comments:{{ else }}comentarios:{{ /if }} {{ $sf->blogentry->comments_online }}</a>
                </ul>
          </div><!-- .teasercontent content -->
        </div><!-- .teaserframebiginner -->
        </div><!-- .teaserframebig -->  
        
    {{ /list_blogentries }}
    
{{ else }}

  <div class="list-nested" id="topics-tree">
    {{ list_subtopics }}
      {{ list_articles ignore_issue="true" ignore_section="true"  }}
        {{ if $sf->current_list->at_beginning }}
              <div class="topicblocktitle" id="topicid">{{ $sf->topic->name }}
                <a  href="javascript:toggleLayer('topicitemsid{{ $sf->topic->identifier }}');" class="toggleshowhide">{{ if $sf->language->name == "English" }}show/hide{{ else }}mostrar / ocultar{{ /if }}</a>
              </div>
            <div class="topicblockitems" id="topicitemsid{{ $sf->topic->identifier }}" style="display:none">
            <div class="topicblockitem">
              See all: <a href="{{ uri options="template classic/topic.tpl" }}" class="topicname">{{ $sf->topic->name }}</a>
            </div><!--  class="topicblockitem" -->
        {{ /if }}
            <div class="topicblockitem">
              <a href="{{ uri options="article" }}" class="article">{{ $sf->article->name }}</a>
              | (published {{ $sf->article->publish_date|camp_date_format:'%M %D, %Y %h:%i:%s' }})
            </div><!--  class="topicblockitem" -->
        {{ if $sf->current_list->at_end }}
            </div><!--  class="topicblockitems" -->
        {{ /if }}
      {{ /list_articles }}
      
      {{ list_blogentries }}

            {{ $sf->url->reset_parameter('f_blogentry_id') }}
              <div><a href="{{ uri options="template classic/topic.tpl" }}" class="topicname">{{ $sf->topic->name }}</a></div>
            {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}

            <div>
              <b>{{ if $sf->language->name == "English" }}Blogentry:{{ else }}Entrada de blog:{{ /if }}</b>
              <a href="{{ uri options="article" }}" class="article">{{ $sf->blogentry->name }}</a>
              |
              {{ $sf->blogentry->published|camp_date_format:'%M %D, %Y %h:%i:%s' }}
            </div>
      {{ /list_blogentries }}
       
      {{ list_subtopics  }}

        {{ list_articles ignore_issue="true" ignore_section="true"  }}
        {{ if $sf->current_list->at_beginning }}
              <div class="topicblocktitle" id="topicid">{{ $sf->topic->name }}
                <a  href="javascript:toggleLayer('topicitemsid{{ $sf->topic->identifier }}');" class="toggleshowhide">>{{ if $sf->language->name == "English" }}show/hide{{ else }}mostrar / ocultar{{ /if }}</a>
              </div>
            <div class="topicblockitems" id="topicitemsid{{ $sf->topic->identifier }}" style="display:none">
            <div class="topicblockitem">
              >{{ if $sf->language->name == "English" }}See all:{{ else }}Ver todos:{{ /if }} <a href="{{ uri options="template classic/topic.tpl" }}" class="topicname">{{ $sf->topic->name }}</a>
            </div><!--  class="topicblockitem" -->
        {{ /if }}
            <div class="topicblockitem">
              <a href="{{ uri options="article" }}" class="article">{{ $sf->article->name }}</a>
              | ({{ $sf->article->publish_date|camp_date_format:'%M %D, %Y %h:%i:%s' }})
            </div><!--  class="topicblockitem" -->
        {{ if $sf->current_list->at_end }}
            </div><!--  class="topicblockitems" -->
        {{ /if }}
        {{ /list_articles }}
        
        {{ list_blogentries }}

              {{ $sf->url->reset_parameter('f_blogentry_id') }}
                <div><a href="{{ uri options="template classic/topic.tpl" }}" class="topicname">{{ $sf->topic->name }}</a></div>
              {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}


              <div>
                <b>{{ if $sf->language->name == "English" }}Blog entry{{ else }}Entrada de blog{{ /if }}:</b>
                <a href="{{ uri options="article" }}" class="article">{{ $sf->blogentry->name }}</a>
                |
                {{ $sf->blogentry->published|camp_date_format:'%M %D, %Y %h:%i:%s' }}
              </div>
        {{ /list_blogentries }}

      
        {{ list_subtopics }}
          {{ list_articles ignore_issue="true" ignore_section="true"  }}
        {{ if $sf->current_list->at_beginning }}
              <div class="topicblocktitle" id="topicid">{{ $sf->topic->name }}
                <a  href="javascript:toggleLayer('topicitemsid{{ $sf->topic->identifier }}');" class="toggleshowhide">show/hide</a>
              </div>
            <div class="topicblockitems" id="topicitemsid{{ $sf->topic->identifier }}" style="display:none">
            <div class="topicblockitem">
              See all: <a href="{{ uri options="template classic/topic.tpl" }}" class="topicname">{{ $sf->topic->name }}</a>
            </div><!--  class="topicblockitem" -->
        {{ /if }}
            <div class="topicblockitem">
              <a href="{{ uri options="article" }}" class="article">{{ $sf->article->name }}</a>
              | (published {{ $sf->article->publish_date|camp_date_format:'%M %D, %Y %h:%i:%s' }})
            </div><!--  class="topicblockitem" -->
        {{ if $sf->current_list->at_end }}
            </div><!--  class="topicblockitems" -->
        {{ /if }}
          {{ /list_articles }}
          
          {{ list_blogentries }}
                {{ $sf->url->reset_parameter('f_blogentry_id') }}
                  <div><a href="{{ uri options="template classic/topic.tpl" }}" class="topicname">{{ $sf->topic->name }}</a></div>
                {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
                <div>
                  <b>Blogentry:</b>
                  <a href="{{ uri options="article" }}" class="article">{{ $sf->blogentry->name }}</a>
                  |
                  {{ $sf->blogentry->published|camp_date_format:'%M %D, %Y %h:%i:%s' }}
                </div>
          {{ /list_blogentries }}
      
          {{ list_subtopics }}
            {{ list_articles ignore_issue="true" ignore_section="true"  }}
        {{ if $sf->current_list->at_beginning }}
              <div class="topicblocktitle" id="topicid">{{ $sf->topic->name }}
                <a  href="javascript:toggleLayer('topicitemsid{{ $sf->topic->identifier }}');" class="toggleshowhide">>{{ if $sf->language->name == "English" }}show/hide{{ else }}mostrar / ocultar{{ /if }}</a>
              </div>
            <div class="topicblockitems" id="topicitemsid{{ $sf->topic->identifier }}" style="display:none">
            <div class="topicblockitem">
              {{ if $sf->language->name == "English" }}See all{{ else }}Ver todos{{ /if }}: <a href="{{ uri options="template classic/topic.tpl" }}" class="topicname">{{ $sf->topic->name }}</a>
            </div><!--  class="topicblockitem" -->
        {{ /if }}
            <div class="topicblockitem">
              <a href="{{ uri options="article" }}" class="article">{{ $sf->article->name }}</a>
              | ({{ $sf->article->publish_date|camp_date_format:'%M %D, %Y %h:%i:%s' }})
            </div><!--  class="topicblockitem" -->
        {{ if $sf->current_list->at_end }}
            </div><!--  class="topicblockitems" -->
        {{ /if }}
            {{ /list_articles }}
            
            {{ list_blogentries }}
                  {{ $sf->url->reset_parameter('f_blogentry_id') }}
                    <div<a href="{{ uri options="template classic/topic.tpl" }}" class="topicname">{{ $sf->topic->name }}</a></div>
                  {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}

                  <div>
                    <b>{{ if $sf->language->name == "English" }}Blogentry:{{ else }}Entrada de blog{{ /if }}</b>
                    <a href="{{ uri options="article" }}" class="article">{{ $sf->blogentry->name }}</a>
                    |
                    {{ $sf->blogentry->published|camp_date_format:'%M %D, %Y %h:%i:%s' }}
                  </div>
            {{ /list_blogentries }}
          {{ /list_subtopics }}
        {{ /list_subtopics }}
      {{ /list_subtopics }}
    {{ /list_subtopics }}
<hr>
  </div>
  
{{ /if }}

        <!-- Column 1 end -->
            </div>
        </div>
        <div class="col2">
            <!-- Column 2 start -->

{{ include file="classic/tpl/search-box.tpl" }}

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
