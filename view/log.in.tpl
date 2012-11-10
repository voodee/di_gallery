{include file='_header_easy.tpl'}
<div role='main' class='page secondary'>
  {include file='_feedback.tpl'}
  <div class='page-header'>
    <div class='page-header-content'>
      <h1>Сим-сим, откройся!</h1>
      <a href='{$l}' class='back-button big page-back'></a>
    </div>
  </div>
  <div class='page-region'>
    <div class='page-region-content'>
      <div class='grid'>
        <div class='row'>
          <div class='span5'>
            <form action='{$l}log/in/' method='POST'>
              <div class='input-control text'>
                <input type='text' name='login' placeholder='Мобильный телефон' value='{$login}' />
                <span class='helper'></span>
              </div>
              <div class='input-control password'>
                <input type='password' name='password' placeholder='Пароль' value='{$pass}' />
                <span class='helper'></span>
              </div>
              <div id='DICaptchaPic'{if $di_captcha_count < 3} style='display: none;'{/if}></div>
              <div class='input-control text' style='clear: both;{if $di_captcha_count < 3} display: none;{/if}'>
                <input type='text' name='captcha' placeholder='Символы с картинки &#8593;' value='' />
                <span class='helper'></span>
              </div>
              <input type='submit' value='Войти' />
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{include file='_footer_easy.tpl'}