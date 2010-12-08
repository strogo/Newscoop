<!-- topic-list.tpl article topics start -->
{{ list_article_topics }}
{{ if $sf->current_list->at_beginning }}
<div class="relatedtopics">
<div class="relatedtopicsinner">
{{ if $sf->language->name == "English" }}Related topics {{ else }}Temas relacionados {{ /if }}
{{ /if }}
: <a href="{{ uri options="template classic/topic.tpl" }}" class="topic">{{$sf->topic->name }}</a>
{{ if $sf->current_list->at_end }}
</div><!-- class="relatedtopicsinner"-->
</div><!-- class="relatedtopics" -->
{{ /if }}
{{ /list_article_topics }}
<!-- topic-list.tpl article topics end -->
