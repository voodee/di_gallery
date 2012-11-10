{include file='_header_easy.tpl'}
<div role='main' class='page'>
  <div class='navigation-bar'>
    <div class='navigation-bar-inner'>
      <span class='menu-pull'></span>
      {include file='_settings_header.tpl'}
    </div>
  </div>
  {include file='_feedback.tpl'}
  <div class='page-header'>
    <div class='page-header-content'>
      <h1>Галереи</h1>
    </div>
  </div>
  
  <div class='page-region'>
    <div class='page-region-content' style='padding-left: 20px;'>
      <form action='{$l}upload/gallery_add/' method='POST'>
        <p>
          <input type='text' name='name_gallery' placeholder='название галереи' style='vertical-align: middle;' />
          <input type='submit' value='создать' />
        </p>
        <p>
          <input type='hidden' name='command' value='add_gallery' />
        </p>
      </form>
      <ul class='GalleryListEdite'>
      {if isset($albums['albums'])}
        {foreach from=$albums['albums'] item=album}
        <li data-priority='{$album.album_gallery_priority}' data-id='{$album.album_gallery_id}'>
          <a href='{$l}upload/gallery_edit/{$album.album_gallery_id}/'>{$album.album_gallery_name}</a>
            <div class='horizontal-menu'>
              <ul>
                <li><a href='{$l}upload/gallery_delete/{$album.album_gallery_id}/' onclick='return confirm("Удалить?")'>Удалить</a></li>
                <li>
                  {if $album.album_gallery_visible == 0}
                  <a href='{$l}upload/gallery_show/{$album.album_gallery_id}/'>Показать</a>
                  {else}
                  <a href='{$l}upload/gallery_hide/{$album.album_gallery_id}/'>Скрыть</a>
                  {/if}
                </li>
              </ul>
            </div>
        </li>
        {/foreach}
      {/if}
      </ul>
    
    </div>  
  </div>
</div>
{include file='_footer_easy.tpl'}