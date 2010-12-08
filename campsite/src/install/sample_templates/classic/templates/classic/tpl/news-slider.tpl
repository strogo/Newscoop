<!-- documentation for plugin: http://flowplayer.org/tools/scrollable/ -->
<!-- all necessary js files are included in header.tpl -->
<script type="text/javascript">
$(document).ready(function(){   // jQuery magic
$("#section-scrollable .scrollable").scrollable({mousewheel:true,circular: true}).navigator();
}); // end of jQuery magic
</script>

<style>
  .section-scr { position: relative; zoom:1; padding: 0;}
  .left-button, .right-button { display: block; position: absolute; top: 50%; height: 13px; width: 13px; cursor: pointer; z-index: 100}
  .right-button { right: 7px; background: url(/templates/classic/img/prev.png) 0 0}
  .left-button { left: 7px; background: url(/templates/classic/img/next.png) 0 0}
  
   #section-scrollable .scrollable {position:relative;overflow:hidden;width:350px;height:270px;}
   #section-scrollable .scrollable .items {width:20000em;position:absolute;}
   #section-scrollable .items div {display: block; float: left; width: 350px; height: 270px; text-align: center;}
   #section-scrollable .items p {display: block; position: absolute; bottom: 0; background: url(/templates/classic/js/fancybox/fancy_title_over.png); width: 350px; padding: 3px; opacity: 0.75;}
   #section-scrollable .items a {font-size: 1.07em; font-weight: bold; text-decoration: none; color: #fff;}

  .navi { text-align: center; font-size: 0; height: 20px; width: 350px; margin-bottom: 10px;}
  .navi a { height: 8px; width: 8px; margin: 10px 11px 0 0; background: url(/templates/classic/img/bullet-off.png) 0 0; display: inline-block; font-size: 8px;}
  .navi .active {background: url(/templates/classic/img/bullet-on.png) 0 0;}
</style>

{{ set_section number="40" }}
<h3>{{ if $sf->language->name == "English" }}Newest <a href="{{ uri options="section" }}">{{ $sf->section->name }}</a> articles{{ else }}Lo nuevo <a href="{{ uri options="section" }}">{{ $sf->section->name }}</a> artículos{{ /if }}</h3>
  <div id="section-scrollable" style="margin: 0 0 20px 0">
    <div class="section-scr">
      <a class="prev left-button" title="Previous"></a>
      <a class="next right-button" title="Next"></a>
      <div class="scrollable"><div class="items">
{{list_articles order="bypublishdate desc"}}
          <div>
              <a href="{{ uri options="article" }}">
                  <img src="{{uri options="image 1 width 350 height 270"}}" alt="{{ $sf->article->name }}" title="{{ $sf->article->name }}" />
                  <p>{{ $sf->article->name }}</p>
              </a>
          </div>
{{/list_articles}}
      </div></div>
      <div class="navi"></div><!-- this generates bullets navigation -->
    </div>
    
  </div>
