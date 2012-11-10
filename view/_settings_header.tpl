<div class='brand'>
  <a href='{$l}'><span class='name'>На сайт</span></a>
</div>

<ul>
  <li{if $page == 'gallery'} class='active'{/if}><a href='{$l}upload/gallery/'>Галереи</a></li>
  <li{if $page == 'text'} class='active'{/if}><a href='{$l}text/'>Описание</a></li>
  <li><a href='{$l}log/out/'>Выход</a></li>
</ul>