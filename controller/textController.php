<?php
  class textController Extends Base {

    function index() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      
      if (isset($_POST['command']{2}) && $_POST['command'] == 'update_text') {
        $this('db')->query('INSERT INTO `di.usermeta` (`usermeta_user_id`, `usermeta_key`, `usermeta_val`) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE `usermeta_val` = VALUES(`usermeta_val`);', $this('user')->get_id(), 'text', $_POST['text']);
        if ($this('db')->affected_rows() != 0)
          $feedback = array('Ok' => true, 'Ok_title' => 'Ok', 'Ok_desc' => 'Описание обновлено');
        else
          $feedback = array('error' => true, 'error_title' => 'Ошибка', 'error_desc' => 'Случилась неприятность');
        header('Location: http://'.$this('script_url').'text/?'.http_build_query($feedback));
        exit();
      }
      
      if (isset($_GET['Ok']{0})) $this('smarty')->assign('feedback', array('Ok' => $_GET['Ok'], 'Ok_title' => $_GET['Ok_title'], 'Ok_desc' => $_GET['Ok_desc']));
      if (isset($_GET['error']{0})) $this('smarty')->assign('feedback', array('error' => $_GET['error'], 'error_title' => $_GET['error_title'], 'error_desc' => $_GET['error_desc']));
      
      $this('db')->query('SELECT `usermeta_val` FROM `di.usermeta` WHERE `usermeta_user_id` = ? AND `usermeta_key` = ?;', $this('user')->get_id(), 'text');
      if ($this('db')->num_rows() == 0) $this('smarty')->assign('text', '');
      else $this('smarty')->assign('text', $this('db')->result(false, 0, 'usermeta_val'));
      
      $this('smarty')->caching = false;
      $this('smarty')->assign('title', 'Описание');
      $this('smarty')->assign('page', 'text');
      $this('smarty')->display('text.tpl');
    }
  }
?>