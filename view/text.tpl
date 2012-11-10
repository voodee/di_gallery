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
      <h1>{$title}</h1>
    </div>
  </div>
  
  <div class='page-region'>
    <div class='page-region-content' style='padding-left: 20px;'>
      <div id='wysihtml5-editor-toolbar'>
        <header>
          <ul class='commands'>
            <li data-wysihtml5-command='bold' title='Make text bold (CTRL + B)' class='command'></li>
            <li data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command"></li>
            <li data-wysihtml5-command="insertUnorderedList" title="Insert an unordered list" class="command"></li>
            <li data-wysihtml5-command="insertOrderedList" title="Insert an ordered list" class="command"></li>
            <li data-wysihtml5-command="createLink" title="Insert a link" class="command"></li>
            <li data-wysihtml5-command="insertImage" title="Insert an image" class="command"></li>
            <li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h1" title="Insert headline 1" class="command"></li>
            <li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Insert headline 2" class="command"></li>
            <li data-wysihtml5-command-group="foreColor" class="fore-color" title="Color the selected text" class="command">
              <ul>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="white"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="silver"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="gray"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="maroon"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="purple"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="olive"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="navy"></li>
                <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue"></li>
              </ul>
            </li>
            <li data-wysihtml5-command="insertSpeech" title="Insert speech" class="command"></li>
            <li data-wysihtml5-action="change_view" title="Show HTML" class="action"></li>
          </ul>
        </header>
        <div data-wysihtml5-dialog="createLink" style="display: none;">
          <label>Link:<input data-wysihtml5-dialog-field="href" value="http://"></label>
          <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
        </div>
        <div data-wysihtml5-dialog="insertImage" style="display: none;">
          <label>Image:<input data-wysihtml5-dialog-field="src" value="http://"></label>
          <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
        </div>
      </div>
      
      <form action='{$l}text/' method='POST'>
        <textarea id='wysihtml5-editor' name='text' spellcheck='false' wrap='off' autofocus placeholder='Текст описания...'>{$text}</textarea>
        <p style='margin: 1em 0;'>
          <input type='hidden' name='command' value='update_text' />
          <input type='submit' value='обновить' />
        </p>
      </form>
    </div>  
  </div>
</div>
{include file='_footer_easy.tpl'}