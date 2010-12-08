{{ if $sf->article->comments_enabled }}
  {{ if $sf->article->comment_count }}
    {{ if $sf->article->comments_locked }}
        <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $sf->language->name == "English" }}Read comments{{ else }}Lea los comentarios{{ /if }} ({{ $sf->article->comment_count}})</a>
    {{ else }}
        <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $sf->language->name == "English" }}Read and Post comments{{ else }}Leer y escribir comentarios{{ /if }} ({{ $sf->article->comment_count}})</a>
    {{ /if }}
  {{ elseif !$sf->article->comments_locked }}
    <li><a href="{{ uri options="article template article.tpl" }}#comments">{{ if $sf->language->name == "English" }}Post comments{{ else }}Enviar comentarios{{ /if }}</a>
  {{ /if }}
{{ /if }}