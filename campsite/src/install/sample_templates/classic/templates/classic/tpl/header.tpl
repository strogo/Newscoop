{{ set_default_publication }}
{{ $sf->url->reset_parameter("tpl") }}
{{ $sf->url->reset_parameter("tpid") }}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="generator" content="Campsite 3.x" /> <!-- leave this for stats -->
  <meta name="description" content="{{ if $sf->article->defined }}{{ $sf->article->Deck }}{{ else }}{{$siteinfo.description}}{{ /if }}">
  <meta name="keywords" content="{{if $sf->article->keywords}}{{$sf->article->keywords}},{{/if}}{{$siteinfo.keywords}}" />

 <link rel="alternate" type="application/rss+xml" title="{{$sf->publication->name}}" href="http://{{$sf->publication->site}}/templates/feed/index-{{ $sf->language->code }}.rss" />

  <title>{{if $sf->article->defined}} {{$sf->article->name}} | {{elseif $sf->section->defined}} {{$sf->section->name}} | {{/if}}{{ $siteinfo.title }}</title>

  <link href="http://{{ $sf->publication->site }}/templates/classic/css/cleanblue/style.css" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="http://{{ $sf->publication->site }}/templates/classic/js/javascript.js"></script>

  <!-- jquery -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  
  <!-- fancybox -->
  <script type="text/javascript" src="/templates/classic/js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
  <script type="text/javascript" src="/templates/classic/js/fancybox/jquery.easing-1.3.pack.js"></script>
  <script type="text/javascript" src="/templates/classic/js/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
  <link rel="stylesheet" href="/templates/classic/js/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />    

  <!-- jquery tools: Tabs, Tooltip, Scrollable and Overlay (4.05 Kb) -->
  <script src="http://cdn.jquerytools.org/1.2.5/tiny/jquery.tools.min.js"></script>
  <!-- jquery tools: Navigator plugin -->
  <script src="/templates/classic/js/scrollable.navigator.js"></script>
</head>
