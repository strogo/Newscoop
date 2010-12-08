<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
<channel>
<title>{{$sf->publication->name}}</title>
<link>http://{{$sf->publication->site}}</link>
<description>{{$siteinfo.description}}</description>
<language>{{ $sf->language->code }}</language>
<copyright>Copyright {{$smarty.now|date_format:"%Y"}}, {{$sf->publication->name}}</copyright>
<lastBuildDate>{{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100</lastBuildDate>
<ttl>60</ttl>
<generator>Campsite</generator>
<image>
<url>http://{{$sf->publication->site}}/templates/classic/img/logo-rss.jpg</url>
<title>{{$sf->publication->name}}</title>
<link>http://{{$sf->publication->site}}</link>
<width>144</width>
<height>19</height>
</image>
<atom:link href="http://{{$sf->publication->site}}/templates/feed/index-en.rss" rel="self" type="application/rss+xml" />
{{list_articles length="20" order="bypublishdate desc"}}
<item>
<title>{{$sf->article->name|html_entity_decode|regex_replace:'/&(.*?)quo;/':'&quot;'}}</title>
<link>http://{{$sf->publication->site}}/ru/{{ $sf->issue->number }}/{{$sf->section->url_name}}/{{$sf->article->number}}</link>
<description>
{{if $sf->article->has_image(1)}}
&lt;img src="{{$sf->article->image1->thumbnailurl}}" border="0" align="left" hspace="5" /&gt;
{{/if}}
{{$sf->article->intro|strip_tags:false|strip|escape:'html':'utf-8'}}
&lt;br clear="all"&gt;
</description>
<category domain="http://{{$sf->publication->site}}/{{ $sf->language->code }}/{{ $sf->issue->number }}/{{$sf->section->url_name}}">{{$sf->section->name}}</category>

{{if $sf->article->author->name}}
<atom:author><atom:name>{{$sf->article->author->name}}</atom:name></atom:author>
{{/if}}
<pubDate>{{$sf->article->publish_date|date_format:"%a, %d %b %Y %H:%M:%S"}} +0100</pubDate>
<guid isPermaLink="true">http://{{$sf->publication->site}}/{{ $sf->language->code }}/{{ $sf->issue->number }}/{{$sf->section->url_name}}/{{$sf->article->number}}</guid>
</item>
{{/list_articles}}
</channel>
</rss>