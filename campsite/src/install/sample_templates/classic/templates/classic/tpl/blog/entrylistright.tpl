<div class="teaserframe teaserframebig teaserframe-blog teaserframebig-blog">
<div class="teaserframebiginner">
  <div class="teaserhead">
  <div class="teaserheadinner">
<a href="{{ uri options="template classic/tpl/blog/section-blog.tpl" }}">Blogs</a>
</div><!-- .teaserheadinner -->
  </div><!-- .teaserhead -->

<div class="bloglist"><div class="bloglistinner">

    {{ list_blogentries name="blogentries" length=3 order="bydate desc" }}
{{ $sf->url->reset_parameter('f_blogentry_id') }}

<div class="bloglistitem"><div class="bloglistiteminner">

<div class="blogtitle"><div class="blogtitleinner">
    {{ $sf->url->set_parameter('f_blog_id', $sf->blogentry->blog_id) }}
    <a href="{{ uri options="template classic/tpl/blog/section-blog.tpl" }}">{{ $sf->blogentry->blog->title }}</a>
</div><!-- .blogtitleinner -->
</div><!-- .blogtitle -->

  <div class="teasercontent content">
      <div class="blogteaserimg">
        {{ if $sf->blogentry->images.100x100 }}
        <img src="{{ $sf->blogentry->images.100x100 }}">
      {{ /if }}
            </div>
  {{ $sf->url->reset_parameter('f_blog_id') }}
  {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
  <h2 class="title title_big title_blogteaser"><a href="{{ uri options="template classic/tpl/blog/section-blog.tpl" }}">{{ $sf->blogentry->name }}</a></h2>

  <p class="text" id="blog-teaser-body">{{ $sf->blogentry->content|teaser }}</p>
<!--div class="blogteaserauthor text">{{ $sf->blogentry->user->name }}</div-->
         
<ul class="links">
  {{ $sf->url->reset_parameter('f_blog_id') }}
  {{ $sf->url->set_parameter('f_blogentry_id', $sf->blogentry->identifier) }}
  <li><a href="{{ uri options="template classic/tpl/blog/section-blog.tpl" }}">{{ if $sf->language->name == "English" }}Read the story{{ else }}Leer la historia{{ /if }}</a></li>
  <li>{{ if $sf->language->name == "English" }}Comments{{ else }}Comentarios{{ /if }}: {{ $sf->blogentry->comments_online }}</li>
</ul>
</div><!-- .teasercontent content -->

</div><!-- .bloglistiteminner -->
</div><!-- .bloglistitem -->

    {{ /list_blogentries }}

</div><!-- .bloglistinner -->
</div><!-- .bloglist -->

</div><!-- .teaserframebiginner -->
</div><!-- .teaserframebig -->
