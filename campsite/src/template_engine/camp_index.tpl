<html>
<head>
  <title>Camp Smarty</title>
</head>
<body>

<p>default language: {{ $sf->default_language->name }}
	(in url: {{ $sf->default_url->language->name }})</p>
<p>default publication: {{ $sf->default_publication->name }}
	(in url: {{ $sf->default_url->publication->name }})</p>
<p>default issue: {{ $sf->default_issue->name }}
	(in url: {{ $sf->default_url->issue->name }})</p>
<p>default section: {{ $sf->default_section->name }}
	(in url: {{ $sf->default_url->section->name }})</p>
<p>default article: {{ $sf->default_article->name }}
	(in url: {{ $sf->default_url->article->name }})</p>
<p>default topic: {{ $sf->default_topic->name }}</p>
<p>default url: {{ $sf->default_url->url }}</p>

<p>url template "article.tpl": {{ uri options="template article.tpl" }}</p>


<h3>issues list</h3>
{{ list_issues length="2" columns="3" name='sample_name' constraints="name greater a" order='byDate asc' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>issue: <b>{{ $sf->current_issues_list->current->name }}</b>/<b>{{ $sf->current_list->current->name }}</b>/<b>{{ $sf->issue->name }}</b>,
   list index: <b>{{ $sf->current_issues_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_issues_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current issues list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_issues }}
<li>previous list empty: {{ $sf->prev_list_empty }}</li>

<h3>sections list</h3>
{{ list_sections length="3" columns="2" name='sample_name' constraints="name greater a number greater 0" }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>section: <b>{{ $sf->current_sections_list->current->name }}</b>/<b>{{ $sf->current_list->current->name }}</b>/<b>{{ $sf->section->name }}</b>,
   list index: <b>{{ $sf->current_sections_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_sections_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current sections list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_sections }}


<h3>articles list</h3>
{{ list_articles length="3" columns="2" name='sample_name' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>article: <b>{{ $sf->current_articles_list->current->name }}</b>/<b>{{ $sf->current_list->current->name }}</b>/<b>{{ $sf->article->name }}</b>,
   list index: <b>{{ $sf->current_articles_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_articles_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current articles list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_articles }}


<h3>article attachments list</h3>
{{ list_article_attachments length="3" columns="2" name='sample_name' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>article attachment: <b>{{ $sf->current_article_attachments_list->current->file_name }}</b>/<b>{{ $sf->current_list->current->file_name }}</b>/<b>{{ $sf->attachment->file_name }}</b>,
   list index: <b>{{ $sf->current_article_attachments_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_article_attachments_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current article attachments list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_article_attachments }}
<li>previous list empty: {{ $sf->prev_list_empty }}</li>


<h3>article comments list</h3>
{{ list_article_comments length="3" columns="2" name='sample_name' order='byDate asc' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>article comment: <b>{{ $sf->current_article_comments_list->current->subject }}</b>/<b>{{ $sf->current_list->current->subject }}</b>/<b>{{ $sf->comment->subject }}</b>,
   list index: <b>{{ $sf->current_article_comments_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_article_comments_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current article comments list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_article_comments }}


<h3>article images list</h3>
{{ list_article_images length="3" columns="2" name='sample_name' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>article image: <b>{{ $sf->current_article_images_list->current->description }}</b>/<b>{{ $sf->current_list->current->description }}</b>/<b>{{ $sf->image->description }}</b>,
   list index: <b>{{ $sf->current_article_images_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_article_images_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current article images list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_article_images }}


<h3>article topics list</h3>
{{ list_article_topics length="3" columns="2" name='sample_name' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>article topic: <b>{{ $sf->current_article_topics_list->current->name }}</b>/<b>{{ $sf->current_list->current->name }}</b>/<b>{{ $sf->topic->name }}</b>,
   list index: <b>{{ $sf->current_article_topics_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_article_topics_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current article topics list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_article_topics }}


<h3>article audio attachments list</h3>
{{ list_article_audio_attachments length="3" columns="2" name='sample_name' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>article audio attachment: <b>{{ $sf->current_article_audio_attachments_list->current->title }}</b>/<b>{{ $sf->current_list->current->title }}</b>/<b>{{ $sf->audioclip->title }}</b>,
   list index: <b>{{ $sf->current_article_audio_attachments_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_article_audio_attachments_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current article audio attachments list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_article_audio_attachments }}


<h3>search results list</h3>
{{ list_search_results length="3" columns="2" name='sample_name' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>search result: <b>{{ $sf->current_search_results_list->current->name }}</b>/<b>{{ $sf->current_list->current->name }}</b>/<b>{{ $sf->article->name }}</b>,
   list index: <b>{{ $sf->current_search_results_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_search_results_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current search results list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_search_results }}


{{ local }}
{{ unset_topic }}
{{ if $sf->topic->defined }}
    <h3>subtopics of topic {{ $sf->topic->name }}</h3>
{{ else }}
    <h3>root topics</h3>
{{ /if }}
{{ list_subtopics length="4" columns="2" name='sample_name' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>subtopic: <b>{{ $sf->current_subtopics_list->current->name }}</b>/<b>{{ $sf->current_list->current->name }}</b>/<b>{{ $sf->topic->name }}</b>,
   list index: <b>{{ $sf->current_subtopics_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_subtopics_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current subtopics list/current list/context)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_subtopics }}
{{ /local }}


<h3>subtitles list</h3>
{{ list_subtitles length="2" columns="2" name='sample_name' field_name='Full_text' }}
{{ if $sf->current_list->at_beginning }}
<li>count: {{ $sf->current_list->count }}</li>
{{ /if }}
<li>subtitle: <b>{{ $sf->current_subtitles_list->current->name }}</b>/<b>{{ $sf->current_list->current->name }}</b>/<b>{{ $sf->subtitle->name }}</b>,
   list index: <b>{{ $sf->current_subtitles_list->index }}</b>/<b>{{ $sf->current_list->index }}</b>,
   column: <b>{{ $sf->current_subtitles_list->column }}</b>/<b>{{ $sf->current_list->column }}</b>
   (current subtitles list/current list)
</li>
{{ if $sf->current_list->at_end }}
    <li>has next elements: {{ $sf->current_list->has_next_elements }}</li>
{{ /if }}
{{ /list_subtitles }}


{{ if $sf->has_property('invalid_property') }}
	<h3>Context error: 'invalid_property' was reported as valid.</h3>
{{ /if }}


{{ invalid_tag }}


{{ $smarty.invalid_reference }}


{{ $sf->article->invalid_property }}


{{ set_language invalid_property="1" }}


{{ set_publication invalid_property="6" }}


{{ set_publication identifier="invalid_value" }}


{{ set_issue invalid_property="1" }}


{{ set_section invalid_property="1" }}


{{ set_article invalid_property="143" }}


{{ $sf->invalid_property }}


{{** HTMLEncoding **}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">HTMLEncoding</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#9cf0ff">
    Default HTMLEncoding value:
  </td>
  <td bgcolor="#9cf0ff" align="center">
    {{ if $sf->htmlencoding eq false }}
      false
    {{ else }}
      {{ $sf->htmlencoding }}
    {{ /if }}
  </td>
  <td>
    {{ literal }}
      {{ $sf->htmlencoding }}
    {{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#9cf0ff">
    Enabling HTMLEncoding:
  </td>
  <td bgcolor="#9cf0ff" align="center">
    {{ enable_html_encoding }}

    {{ if $sf->htmlencoding eq false }}
      false
    {{ else }}
      {{ $sf->htmlencoding }}
    {{ /if }}
  </td>
  <td>
    {{ literal }}
      {{ enable_html_encoding }}
    {{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#9cf0ff">
    Disabling HTMLEncoding:
  </td>
  <td bgcolor="#9cf0ff" align="center">
    {{ disable_html_encoding }}

    {{ if $sf->htmlencoding eq false }}
      false
    {{ else }}
      {{ $sf->htmlencoding }}
    {{ /if }}
  </td>
  <td>
    {{ literal }}
      {{ disable_html_encoding }}
    {{ /literal }}
  </td>
</tr>
</table><br />


{{**** Language ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Language</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->language->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->language->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->language->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->language->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">English Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->language->english_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->language->english_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Code:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->language->code }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->language->code }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->language->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->language->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_language }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_language }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->language->english_name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->language->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_language name="English" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_language name="English" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->language->english_name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->language->defined }}
  </td>
</tr>
</table>
<br />


{{**** Publication ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Publication</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->publication->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->publication->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->publication->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->publication->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Site:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->publication->site }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->publication->site }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->publication->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->publication->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{ assign var="defaultIssueNumber" value=$sf->issue->number }}
{{ assign var="defaultSectionNumber" value=$sf->section->number }}
{{ assign var="defaultArticleNumber" value=$sf->article->number }}


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_publication }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_publication }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->publication->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->publication->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_publication identifier="1" }}
    {{ set_issue number=$defaultIssueNumber }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_publication identifier="1" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->publication->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->publication->defined }}
  </td>
</tr>
</table>
<br />


{{**** Issue ****}}
<table>
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Issue</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->issue->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->issue->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->mon_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->mon_name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->wday_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->wday_name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->issue->date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Publish Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->issue->publish_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->publish_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Is Current:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->issue->is_current }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->issue->is_current }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_issue }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_issue }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->issue->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->issue->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_issue number=$defaultIssueNumber }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_issue number="{{ /literal }}{{ $defaultIssueNumber }}{{ literal }}" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->issue->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->issue->defined }}
  </td>
</tr>
</table>
<br />


{{ set_section number=$defaultSectionNumber }}
{{**** Section ****}}
<table>
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Section</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->section->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->section->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->section->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->section->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->section->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->section->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->section->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->section->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->section->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->section->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_section }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_section }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->section->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->section->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_section number=$defaultSectionNumber }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_section number="{{ /literal }}{{ $defaultSectionNumber }}{{ literal }}" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->section->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->section->defined }}
  </td>
</tr>
</table>
<br />


{{ set_article number=$defaultArticleNumber }}
{{**** Article ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Article</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Keywords:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->keywords }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->keywords }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Type:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->type_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->type_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->mon_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->mon_name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->wday_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->wday_name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Creation Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->creation_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->creation_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Publish Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->publish_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->publish_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Template:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Intro:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->type->Article->intro }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->type->Article->intro }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Body:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->type->Article->full_text }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->type->Article->full_text }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">URL Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->url_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->url_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Comments Enabled:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->comments_enabled }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->comments_enabled }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Comments Locked:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->comments_locked }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->comments_locked }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Last Update:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->article->last_update }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->last_update }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">On Front Page:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->on_front_page }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->on_front_page }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">On Section Page:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->on_section_page }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->on_section_page }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Published:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->is_published }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->is_published }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Public:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->is_public }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->is_public }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Indexed:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->is_indexed }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->is_indexed }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Publication:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->publication->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->publication->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Issue:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->issue->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->issue->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Section:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->section->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->section->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Language:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->language->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->language->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Owner:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->owner->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->owner->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Comment Count:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->comment_count }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->comment_count }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Subtitles Count:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->subtitles_count("Full_text") }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->subtitles_count("Full_text") }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Translated to:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->translated_to("ro") }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->translated_to("ro") }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Has Keyword:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->has_keyword("fsf") }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->has_keyword("fsf") }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Has Attachments:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->has_attachments }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->has_attachments }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Content accessible:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->article->content_accessible }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->article->content_accessible }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_article }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_article }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->article->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->article->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_article number="140" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_article number="140" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->article->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->article->defined }}
  </td>
</tr>
</table>
<br />


{{**** Image ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Image</font></td>
</tr>
</table>
{{ list_article_images length="1" }}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Number:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->image->number }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->number }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Photographer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->image->photographer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->photographer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Place:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->image->place }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->place }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->image->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->image->date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>


<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->year }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->mon }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->mon }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->mon_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->mon_name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->wday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->wday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Week Day Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->wday_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->wday_name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Month Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->mday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->mday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Year Day:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->yday }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->yday }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Hour:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->hour }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->hour }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Minute:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->min }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->min }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Second:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->sec }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->sec }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>

<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->image->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->image->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>
{{ /list_article_images }}


{{ set_article number="140" }}
{{**** Attachment ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Attachment</font></td>
</tr>
</table>
{{ list_article_attachments length="1" }}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">File Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->attachment->file_name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->attachment->file_name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Mime Type:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->attachment->mime_type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->attachment->mime_type }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Extension:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->attachment->extension }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->attachment->extension }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Description:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->attachment->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->attachment->description }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Size B:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->attachment->size_b }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->attachment->size_b }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Size KB:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->attachment->size_kb }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->attachment->size_kb }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Size MB:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->attachment->size_mb }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->attachment->size_mb }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->attachment->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->attachment->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>
{{ /list_article_attachments }}


{{**** Topic ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Topic</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->topic->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->topic->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Name:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->topic->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->topic->name }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->topic->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->topic->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ unset_topic }}
    Unset by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ unset_topic }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->topic->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->topic->defined }}
  </td>
</tr>
<tr>
  <td bgcolor="#dfdfdf" nowrap valign="top">
    {{ set_topic name="Open Source:en" }}
    Set by
  </td>
  <td bgcolor="#dfdfdf">
    {{ literal }}{{ set_topic name="Open Source:en" }}{{ /literal }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Name:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->topic->name }}
  </td>
</tr>
<tr>
  <td bgcolor="#ffcc66" nowrap valign="top">Defined:</td>
  <td bgcolor="#ffcc66" valign="top">
    {{ $sf->topic->defined }}
  </td>
</tr>
</table>
<br />


{{**** User ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">User</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">User Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->uname }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->uname }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">EMail:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->email }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->email }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">City:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->city }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->city }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Street Address:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->str_address }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->str_address }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">State:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->state }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->state }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Country:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->country }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->country }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Phone:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->phone }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->phone }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Contact:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->contact }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->contact }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Postal Code:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->postal_code }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->postal_code }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Employer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->employer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->employer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Position:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->position }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->position }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Interests:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->interests }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->interests }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">How:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->how }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->how }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Languages:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->languages }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->languages }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Improvements:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->improvements }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->improvements }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 1:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->field1 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->field1 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 2:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->field2 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->field2 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 3:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->field3 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->field3 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 4:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->field4 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->field4 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Field 5:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->field5 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->field5 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 1:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->text1 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->text1 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 2:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->text2 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->text2 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Text 3:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->text3 }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->text3 }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Title:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->title }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->title }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Age:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->age }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->age }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Blocked From Comments:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->blocked_from_comments }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->blocked_from_comments }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{**** Subscription ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Subscription</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->subscription->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Currency:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->user->subscription->currency }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->currency }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Type:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->subscription->type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->type }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Start Date:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->subscription->start_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->start_date }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Expiration Date:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->subscription->expiration_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->expiration_date }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Active:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->subscription->is_active }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->is_active }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Valid:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->subscription->is_valid }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->is_valid }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Publication Identifier:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->subscription->publication->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->publication->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Has Section:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->user->subscription->has_section(40) }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->user->subscription->has_section(40) }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>


{{ set_section number=$defaultSectionNumber }}
{{ set_article number=$defaultArticleNumber }}
{{**** Audioclip ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Audioclip</font></td>
</tr>
</table>
{{ list_article_audio_attachments length="1" }}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Title:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->title }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->title }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Creator:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->creator }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->creator }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Genre:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->genre }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->genre }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Length:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->length }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->length }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Year:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->year }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->year }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Bitrate:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->bitrate }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->bitrate }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Samplerate:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->samplerate }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->samplerate }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Album:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->album }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->album }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Description:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->description }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->description }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Format:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->format }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->format }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Label:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->label }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->label }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Composer:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->composer }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->composer }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Channels:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->channels }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->channels }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Rating:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->rating }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->rating }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Track No:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->track_no }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->track_no }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Disk No:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->disk_no }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->disk_no }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Lyrics:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->lyrics }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->lyrics }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Copyright:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->audioclip->copyright }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->copyright }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->audioclip->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->audioclip->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>
{{ /list_article_audio_attachments }}


{{ list_article_comments length="1" columns="2" name='sample_name' order='byDate asc' }}
{{**** Article Comment ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Article Comment</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Identifier:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->comment->identifier }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->comment->identifier }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Reader EMail:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->comment->reader_email }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->comment->reader_email }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Submit Date:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->comment->submit_date }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->comment->submit_date }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Subject:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->comment->subject }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->comment->subject }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Content:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->comment->content }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->comment->content }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Level:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->comment->level }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->comment->level }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->comment->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->comment->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>
{{ /list_article_comments }}


{{**** Template ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Template</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="Aqua" align="center" colspan="3">Fields</td>
  <td bgcolor="Aqua" align="center">Type</td>
</tr>
<tr>
  <td bgcolor="#9cf0ff" nowrap valign="top">Name:</td>
  <td bgcolor="#9cf0ff" valign="top">
    {{ $sf->template->name }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->template->name }}{{ /literal }}</td>
  <td nowrap valign="top">base</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Type:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->template->type }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->template->type }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
<tr>
  <td bgcolor="#d4ffa2" nowrap valign="top">Defined:</td>
  <td bgcolor="#d4ffa2" valign="top">
    {{ $sf->template->defined }}
  </td>
  <td nowrap valign="top">{{ literal }}{{ $sf->template->defined }}{{ /literal }}</td>
  <td nowrap valign="top">custom</td>
</tr>
</table>

</body>
</html>
