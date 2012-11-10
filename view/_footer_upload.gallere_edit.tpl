{literal}
<script id='template-upload' type='text/x-tmpl'>
{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class='template-upload fade'>
    <td class='preview'><span class='fade'></span></td>
    <td class='name'><span>{%=file.name%}</span></td>
    <td class='size'><span>{%=o.formatFileSize(file.size)%}</span></td>
    {% if (file.error) { %}
    <td class='error' colspan='2'><span class='label label-important'>Ошибка</span> {%=file.error%}</td>
    {% } else if (o.files.valid && !i) { %}
    <td>
      <div class='progress progress-success progress-striped active' role='progressbar' aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'><div class='bar' style='width:0%;'></div></div>
    </td>
    <td class='start'>{% if (!o.options.autoUpload) { %}
      <button>
        <span>Загрузка</span>
      </button>
    {% } %}</td>
    {% } else { %}
    <td colspan='2'></td>
    {% } %}
    <td class='cancel'>{% if (!i) { %}
      <button>
        <span>Отмена</span>
    </button>
    {% } %}</td>
  </tr>
{% } %}
</script>
<script id='template-download' type='text/x-tmpl'>
{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class='template-download fade'>
  {% if (file.error) { %}
    <td></td>
    <td class='name'><span>{%=file.name%}</span></td>
    <td class='size'><span>{%=o.formatFileSize(file.size)%}</span></td>
    <td class='error' colspan='2'><span class='label label-important'>Ошибка</span> {%=file.error%}</td>
    {% } else { %}
    <td class='preview'>{% if (file.thumbnail_url) { %}
      <a href='{%=file.url%}' title='{%=file.name%}' rel='gallery' download='{%=file.name%}'><img src='{%=file.thumbnail_url%}'></a>
    {% } %}</td>
    <td class='name'>
      <a href='{%=file.url%}' title='{%=file.name%}' rel='{%=file.thumbnail_url&&'gallery'%}' download='{%=file.name%}'>{%=file.name%}</a>
    </td>
    <td class='size'><span>{%=o.formatFileSize(file.size)%}</span></td>
    <td colspan='2'></td>
    {% } %}
    <td class='delete'>
    <button style='margin-bottom: 0;' data-type='{%=file.delete_type%}' data-url='{%=file.delete_url%}'>
      <span>Удалить</span>
    </button>
    <label class='checkbox' style='display: inline-block;'><input type='checkbox' name='delete' value='1'><span></span></label>
  </td>
</tr>
{% } %}
</script>
{/literal}

  <script src='//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js'></script>
  <script>window.jQuery || document.write('<script src="{$l}view/include/js/vendor/jquery-1.8.0.min.js"><\/script>')</script>
  
  <script src='{$l}view/include/js/libs/jquery.proximity.js'></script>
  
  <script>var base_url = '{$l}';</script>
  
  <script src='{$l}view/include/js/metro/accordion.js'></script>
  <script src='{$l}view/include/js/metro/buttonset.js'></script>
  <script src='{$l}view/include/js/metro/dropdown.js'></script>
  <script src='{$l}view/include/js/metro/pagecontrol.js'></script>
  
  <!-- File upload -->
  <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
  <script src='{$l}view/include/js/vendor/jquery.ui.widget.js'></script>
  <!-- The Templates plugin is included to render the upload/download listings -->
  <script src='http://blueimp.github.com/JavaScript-Templates/tmpl.min.js'></script>
  <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
  <script src='http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js'></script>
  <!-- The Canvas to Blob plugin is included for image resizing functionality -->
  <script src='http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js'></script>
  <!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
  <script src='http://blueimp.github.com/cdn/js/bootstrap.min.js'></script>
  <script src='http://blueimp.github.com/Bootstrap-Image-Gallery/js/bootstrap-image-gallery.min.js'></script>
  <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
  <script src='{$l}view/include/js/jquery.fileupload/jquery.iframe-transport.js'></script>
  <!-- The basic File Upload plugin -->
  <script src='{$l}view/include/js/jquery.fileupload/jquery.fileupload.js'></script>
  <!-- The File Upload file processing plugin -->
  <script src='{$l}view/include/js/jquery.fileupload/jquery.fileupload-fp.js'></script>
  <!-- The File Upload user interface plugin -->
  <script src='{$l}view/include/js/jquery.fileupload/jquery.fileupload-ui.js'></script>
  <!-- The main application script -->
  <script src='{$l}view/include/js/jquery.fileupload/main.js'></script>
  <!-- End File upload -->
  <!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
  <!--[if gte IE 8]><script src='{$l}view/include/js/cors/jquery.xdr-transport.js'></script><![endif]-->
  
  <script src='{$l}view/include/js/plugins.js'></script>
  <script src='{$l}view/include/js/admin.js'></script>
 
  {literal}
  <script>
    var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
    s.parentNode.insertBefore(g,s)}(document,'script'));
  </script>
  {/literal}
</body>
</html>