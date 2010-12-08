<div class="teaserframe teaserframe-{{ $sf->section->number }}">
<div class="teaserframeinner">
  <div class="teaserhead">
  <div class="teaserheadinner">
  <div class="teaserheadsection">
    <a href="{{ uri options="section" }}">{{ $sf->section->name }}</a> |
  </div><!-- class="teaserheadsection" -->
    {{ $sf->article->publish_date|camp_date_format:'%M %D, %Y %h:%i' }}
    {{ include file="classic/tpl/topic-list.tpl" }}
  </div><!-- .teaserheadinner -->
  </div><!-- .teaserhead -->
  
  {{ if $sf->current_sections_list->index == 1 }}
  {{ if $sf->current_articles_list->at_beginning }}
  
{{ if $sf->article->has_image(1) }}
<!-- Big banner image -->
  <div class="teaserimg_big">
  <a href="{{ uri options="template article.tpl" }}"><img src="{{ uri options="image 1 width 560" }}"/></a>
  </div><!-- .teaserimg_big -->
{{ /if }}

  {{ else }}
  
{{ if $sf->article->has_image(1) }}
<!-- Big square image -->
  <div class="teaserimg_med">
  <a href="{{ uri options="template article.tpl" }}"><img src="{{ uri options="image 1 width 188" }}"/></a>
  </div><!-- .teaserimg_med -->
{{ /if }}

  {{ /if }}
  
  {{ else }}
  
{{ if $sf->article->has_image(1) }}
<!-- Big square image -->
  <div class="teaserimg_med">
  <a href="{{ uri options="template article.tpl" }}"><img src="{{ uri options="image 1 width 188" }}"/></a>
  </div><!-- .teaserimg_med -->
{{ /if }}    
  
  {{ /if }}

  <div class="teasercontent content">
  <h2 class="title title_big">{{ include file="classic/tpl/if-video-then-icon.tpl" }}{{ include file="classic/tpl/if-audio-then-icon.tpl" }}<a href="{{ uri options="article template article.tpl" }}">{{ $sf->article->name }}</a></h2>
  <p class="text">{{ $sf->article->Lead_and_SMS }}</p>
  <ul class="links">
  <li><a href="{{ uri options="article" }}">{{ if $sf->language->name == "English" }}Read more{{ else }}Leer más{{ /if }}</a>
  {{ include file="classic/tpl/comments-link.tpl" }}
  </ul>
  </div><!-- .teasercontent content -->
</div><!-- .teaserframeinner -->
</div><!-- .teaserframe -->
