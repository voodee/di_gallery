{include file='_header_upload.gallere_edit.tpl'}
<div role='main' class='page secondary'>
  <div class='navigation-bar'>
    <div class='navigation-bar-inner'>
      <span class='menu-pull'></span>
      {include file='_settings_header.tpl'}
    </div>
  </div>
  {include file='_feedback.tpl'}
  <div class='page-header'>
    <div class='page-header-content'>
      <h1>Просмотр галлереи</h1>
      <a href='{$l}upload/gallery/' class='back-button big page-back'></a>
    </div>
  </div>
  
  <div class='page-region'>
    <div class='page-region-content'>
      <form action='{$l}upload/gallery_rename/' method='POST'>
        <p>
          <input type='text' name='name_gallery' placeholder='название галереи' value='{$gallery['name']}' style='vertical-align: middle;' />
          <input type='submit' value='переименовать' />
        </p>
        <p>
          <input type='hidden' name='gallery_id' value='{$gallery['id']}' />
          <input type='hidden' name='command' value='rename_gallery' />
        </p>
      </form>
      
      <form id='fileupload' action='{$l}upload/jquery_file_upload/' method='POST' enctype='multipart/form-data'>
        <input type='hidden' name='gallery_id' value='{$gallery['id']}' />
        <div class='row fileupload-buttonbar'>
          <div class='span7' style='width: auto;'>
            <span class='fileinput-button'>
              <span style='vertical-align: middle;'>Добавить файлы...</span>
              <input type='file' name='files[]' multiple>
            </span>
            <button type='submit' class='start'>
              <span>Начать загрузку</span>
            </button>
            <button type='reset' class='cancel'>
              <span>Отменить загрузку</span>
            </button>
            <button type='button' class='delete'>
              <span>Удалить отмеченные</span>
            </button>
            <label class='checkbox' style='display: inline-block; vertical-align: middle;'>
              <input type='checkbox' class='toggle'>
              <span>Выделить все</span>
            </label>
          </div>
          <div class='span5 fileupload-progress fade' style='width: auto;'>
            <div class='progress progress-success progress-striped active' role='progressbar' aria-valuemin='0' aria-valuemax='100'>
              <div class='bar' style='width:0%;'></div>
            </div>
            <div class='progress-extended'>&nbsp;</div>
          </div>
        </div>
        <div class='fileupload-loading'></div>
        <br />
        <table role='presentation' class='table table-striped'>
          <tbody class='files' data-toggle='modal-gallery' data-target='#modal-gallery'>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
{include file='_footer_upload.gallere_edit.tpl'}