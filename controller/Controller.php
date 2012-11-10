<?php
  class Controller Extends Base {

    function index() {
      
      $this('db')->query('SELECT `usermeta_val` FROM `di.usermeta` WHERE `usermeta_key` = ? LIMIT 1;', 'text');
      if ($this('db')->num_rows() == 0) $this('smarty')->assign('text', '');
      else $this('smarty')->assign('text', $this('db')->result(false, 0, 'usermeta_val'));
      
      $this('smarty')->caching = false;
      $this('smarty')->assign('gallerys', $this('album')->get_public_gallerys());
      $this('smarty')->assign('title', '');
      $this('smarty')->display('index.tpl');
    }
    
  }
?>