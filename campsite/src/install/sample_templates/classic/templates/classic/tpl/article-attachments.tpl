<style>
#attachments {
  border-top: 1px solid #999;
  border-bottom: 1px solid #999; 
  padding: 10px 0;
  margin: 10px 0; 
}

#attachments ul {
  list-style: none;
  padding: 0;
}

#attachments ul li {
  line-height: 50px;
  margin-left: 0;
  padding-left: 50px;
}
</style>

<div id="attachments">
          {{ list_article_attachments }}
          {{ if $sf->current_list->at_beginning }}
          <h4>{{ if $sf->language->name == "English" }}Downloads{{ else }}Descargas{{ /if }}:</h4>
          <ul>
          {{ /if }}
            <li style="background: url(/templates/classic/img/icons/file_{{ $sf->attachment->extension }}.png) no-repeat left center"><a href="{{ uri options="articleattachment" }}">{{ $sf->attachment->file_name }}, {{ $sf->attachment->mime_type }}</a> ({{ $sf->attachment->size_kb }}kb)</li>
          {{ if $sf->current_list->at_end }}  
          </ul>
          {{ /if }}
          {{ /list_article_attachments }}
</div>          