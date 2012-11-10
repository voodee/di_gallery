{if isset($feedback['Ok']) && $feedback['Ok'] == 1}
    <div class='notices' style='position: relative; float: right; z-index: 1001;'>
      <div class='bg-color-green'>
        <div class='notice-header fg-color-white'>{$feedback['Ok_title']}</div>
        <a href='#' class='close'></a>
        <div class='notice-text'>{if isset($feedback['Ok_desc'])}{$feedback['Ok_desc']}{/if}</div>
      </div>
    </div>
{/if}
{if isset($feedback['error']) && $feedback['error'] == 1}
    <div class='notices' style='position: relative; float: right; z-index: 1001;'>
      <div class='bg-color-red'>
        <div class='notice-header fg-color-white'>{$feedback['error_title']}</div>
        <a href='#' class='close'></a>
        <div class='notice-text'>{if isset($feedback['error_desc'])}{$feedback['error_desc']}{/if}</div>
      </div>
    </div>
{/if}