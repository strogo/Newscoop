{{ include file="classic/tpl/header.tpl" }}

<body id="article" class="section-{{ $sf->section->number }}">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

  {{ include file="classic/tpl/headernav.tpl" }}

  <div class="colmask rightmenu">
    <div class="colleft">
      <div class="col1wrap">
        <div class="col1">
        <!-- Column 1 start -->

{{ if $sf->search_articles_action->defined }}
  
    {{ if $sf->search_articles_action->is_error }}
      {{ $sf->search_articles_action->error_message }}
    {{ /if }}

    {{ if $sf->search_articles_action->ok }}

        {{ list_search_results name="results" length=9 }}
        {{ if $sf->current_list->at_beginning }}
            <p>{{ if $sf->language->name == "English" }}Found {{ $sf->current_list->count }} articles matching the condition.{{ else }}Se han encontrado {{ $sf->current_list->count }} artículos que coinciden con la condición.{{ /if }}</p>
        {{ /if }}
<div class="teaserframe teaserframebig teaserframe-{{ $sf->section->number }} teaserframebig-{{ $sf->section->number }}">
<div class="teaserframebiginner">
  <div class="teaserhead">
  <div class="teaserheadinner">
  </div><!-- .teaserheadinner -->
  </div><!-- .teaserhead -->
          <div class="teasercontent content">
          <h2 class="title title_big"><a href="{{ uri options="article" }}">{{ $sf->article->name }}</a></h2>
          <p class="text">{{ if $sf->language->name == "English" }}Section{{ else }}Sección{{ /if }} <a href="{{ uri options="section" }}">{{ $sf->section->name }}</a>, {{ $sf->article->Date }} {{ $sf->article->Time }}) {{ $sf->article->Deck }}</p>
          </div><!-- .teasercontent content -->
        </div><!-- .teaserframebiginner -->
        </div><!-- .teaserframebig -->
          {{ unset_section }}
            {{ include file="classic/tpl/pagination.tpl" }}
        {{ /list_search_results }}

        {{ if $sf->prev_list_empty }}
          <div class="error"><div class="errorinner">
          {{ if $sf->language->name == "English" }}There were no articles found.{{ else }}No se encontraron artículos encontrados.{{ /if }}
          </div></div>
        {{ /if }}
    {{ /if }}
{{ /if }}
        <!-- Column 1 end -->
        </div>
      </div>
      
      <div class="col2">
      <!-- Column 2 start -->
              
        {{ include file="classic/tpl/search-box.tpl" }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerrightcol.tpl" }}
       
      <!-- Column 2 end -->
    
      </div>
    </div>
  </div>

  {{ include file="classic/tpl/footer.tpl" }}

</div><!-- id="wrapper" -->
</div><!-- id="wrapbg" -->
</div><!-- id="container"-->
</body>
</html>