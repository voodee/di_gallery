{include file='_header.tpl'}
<div id='BG' class='jThumbnailScroller'>
  <div class='jTscrollerContainer' > 
    <div class='jTscroller'></div>
  </div>
  <div class='BGNoScroll'></div>
  <div class='BGFilter'></div>
  <a href='#' class='BGButtonZoom BGButtonZoomFull'>Зум</a>
</div>
<div id='BGLoading'></div>
<div role='main' id='Main'>
  <section id='AreaCube'>
    <article>
      <ul class='ListPhoto3D'>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li class='ListPhoto3DButton'><a href='#about' id='MoveOnAbout'><p class='CubeZoom'>об авторе<span>&#8592;</span></p></a></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li class='ListPhoto3DButton'><a href='#album' id='MoveOnAlbum'><p class='CubeZoom'>все галереи<span>&#8594;</span></p></a></li>
      </ul>
    </article>
    <article id='CubeAlbum'>
      <ul id='ListGallerys'>
      {if isset($gallerys['albums'])}
      {foreach from=$gallerys['albums'] item=gallery}
        <li>
          <a href='#gallery={$gallery.album_gallery_id}' title='{$gallery.album_gallery_name}' data-id_gallery='{$gallery.album_gallery_id}'>
            <img src='{$l}view/images/{$gallery.album_gallery_preview}/280/150/' alt='{$gallery.album_gallery_name}' title='{$gallery.album_gallery_name}' />
            <h3>{$gallery.album_gallery_name}</h3>
            <div class='ListGallerysFilters'></div>
          </a>
        </li>
      {/foreach}
      {/if}
      </ul>
      <p><a href='#' class='MoveOnMain'>&#8592; вернуться к просмотру</a></p>
    </article>
    <article id='CubeAbout'>
      <div class='CubeText'>
        {$text}
        <p><a href='#' class='MoveOnMain'>вернуться к просмотру &#8594;</a></p>
      </div>
    </article>
  </section>
</div>
<div id='CubeStart'>
  <span class='BoxButton'><a href='#'>Включить полноэкранный режим</a></span>
</div>
<div id='EasyMain'>
  <ul id='EasyMainPhoto' class='ir'>
  </ul>
  <ul id='EasyMainGalleryList' class='ir'>
    {if isset($gallerys['albums'])}
    {foreach from=$gallerys['albums'] item=gallery}
    <li>
      <a href='#gallery={$gallery.album_gallery_id}' title='{$gallery.album_gallery_name}' data-id_gallery='{$gallery.album_gallery_id}'>
        <img src='{$l}view/images/{$gallery.album_gallery_preview}/295/150/' alt='{$gallery.album_gallery_name}' title='{$gallery.album_gallery_name}' />
        <h3>{$gallery.album_gallery_name}</h3>
        <div class='EasyMainGalleryListFilters'></div>
      </a>
    </li>
    {/foreach}
    {/if}
  </ul>
  <div id='EasyMainAbout' class='ir CubeText' style='padding: 1em;'>{$text}</div>
</div>
{include file='_footer.tpl'}