<div class="breadcrumbs"><div class="breadcrumbsinner">
<a href="{{ uri options="publication" }}">{{ if $sf->language->name == "English" }}Home{{ else }}Portada{{ /if }}</a>
&gt;
{{ if $sf->template->name == "classic/topic.tpl" }}
    {{ if $sf->topic->defined }}
        {{ if $sf->language->name == "English" }}Topic{{ else }}Tema{{ /if }}: {{ $sf->topic->name }}
    {{ else }}
        {{ if $sf->language->name == "English" }}Topics{{ else }}Temas{{ /if }}
    {{ /if }}
{{ elseif $sf->template->name == "classic/archive.tpl" }}
    {{ if $sf->language->name == "English" }}Archive{{ else }}Archivo{{ /if }}
{{ elseif $sf->section->defined }}
    <a href="{{ uri options="section" }}">{{ $sf->section->name }}</a>
{{ /if }}

</div><!-- .breadcrumbsinner -->
</div><!-- .breadcrumbs -->