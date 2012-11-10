  <script src='//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js'></script>
  <script>window.jQuery || document.write('<script src="{$l}view/include/js/vendor/jquery-1.8.0.min.js"><\/script>')</script>
  <script src='//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'></script>
  <script>!window.jQuery.ui && document.write(unescape('%3Cscript src="http://localhost/cube/view/include/js/libs/jquery-ui-1.8.23.custom.min.js"%3E%3C/script%3E'))</script>

  <script src='{$l}view/include/js/libs/wysihtml5/advanced.js'></script>
  <script src='{$l}view/include/js/libs/wysihtml5/wysihtml5-0.3.0.min.js'></script>
  
  <script src='{$l}view/include/js/libs/jquery.proximity.js'></script>
  
  <script src='{$l}view/include/js/metro/accordion.js'></script>
  <script src='{$l}view/include/js/metro/buttonset.js'></script>
  <script src='{$l}view/include/js/metro/dropdown.js'></script>
  <script src='{$l}view/include/js/metro/pagecontrol.js'></script>
  
  <script>var base_url = '{$l}';</script>
  <script src='{$l}view/include/js/plugins.js'></script>
  <script src='{$l}view/include/js/admin.js'></script>
  
  {if $page == 'text'}
    {literal}
    <script>
      var editor = new wysihtml5.Editor("wysihtml5-editor", {
        toolbar:     "wysihtml5-editor-toolbar",
        stylesheets: ["http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css", base_url + "view/include/css/wysihtml5.css"],
        parserRules: wysihtml5ParserRules
      });
    </script>
    {/literal}
  {/if}
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