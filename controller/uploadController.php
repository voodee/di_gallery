<?php
  class uploadController Extends Base {

    function index() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
    }
    
    function gallery() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      $this->get_param_feedback();
      $this('smarty')->caching = false;
      $this('smarty')->assign('title', 'Галереи');
      $this('smarty')->assign('albums', $this('album')->get_gallerys());
      $this('smarty')->assign('page', 'gallery');
      $this('smarty')->display('upload.gallery.tpl');
    }
    
    function gallery_add() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      if (isset($_POST['command']) && $_POST['command'] == 'add_gallery') {
        $res = $this('album')->add_gallery($_POST['name_gallery']);
        $feedback = array();
        if (isset($res['error'])) {
          switch ($res['error_code']) {
            case 0:
              $feedback = array('error' => true, 'error_title' => 'Ошибка', 'error_desc' => 'Имя не задано');
              break;
            case 1:
              $feedback = array('error' => true, 'error_title' => 'Ошибка', 'error_desc' => 'Имя длиновато');
              break;
            case 2:
              $feedback = array('error' => true, 'error_title' => 'Ошибка', 'error_desc' => 'Галлерея уже создана');
              break;
          }
        } else {
          $feedback = array('Ok' => true, 'Ok_title' => 'Ok', 'Ok_desc' => 'Галерея создана');
          header('Location: http://'.$this('script_url').'upload/gallery_edit/'.$res['id'].'/?'.http_build_query($feedback));
          exit();
        }
        header('Location: http://'.$this('script_url').'upload/gallery/?'.http_build_query($feedback));
        exit();
      }
    }
    
    function gallery_delete() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      switch ($this('album')->del_gallery($this->args[0])) {
        case 0:
          $feedback = array('error' => true, 'error_title' => 'Ошибка');
          break;
        case 1:
          $feedback = array('Ok' => true, 'Ok_title' => 'Ok', 'Ok_desc' => 'Галерея удалена');
          break;
      }
      header('Location: http://'.$this('script_url').'upload/gallery/?'.http_build_query($feedback));
      exit();
    }
    
    function gallery_edit() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      $gallery = $this('album')->get_gallery($this->args[0]);
      if (isset($gallery['error']{0}) && $gallery['error']) {
        header('Location: http://'.$this('script_url').'upload/gallery/');
        exit();
      }
      $this->get_param_feedback();
      $this('smarty')->caching = false;
      $this('smarty')->assign('title', $gallery['name'].' | Галереи');
      $this('smarty')->assign('gallery', $gallery);
      $this('smarty')->assign('page', 'gallery');
      $this('smarty')->display('upload.gallery_edit.tpl');
    }
    
    function gallery_priority() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      $i = 0;
      foreach ($_POST['id'] as $key => $val) 
        Core::inst()->db->query('UPDATE `di.album_gallery` SET `album_gallery_priority` = ? WHERE `album_gallery_id` = ? AND `album_gallery_user_id` = ?;', ++$i, (int)$val, Core::inst()->user->get_id());
      
    }
    
    function jquery_file_upload() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      require_once('include/jQuery-File-Upload/upload.class.php');
      $upload_handler = new UploadHandler();
      exit();
    }
    
    function gallery_show() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      $res = $this('album')->visibility_gallery($this->args[0], 1);
      if (isset($res['error'])) header('Location: http://'.$this('script_url').'upload/gallery/?'.http_build_query(array('error' => true, 'error_title' => 'Ошибка')));
      else header('Location: http://'.$this('script_url').'upload/gallery/?'.http_build_query(array('Ok' => true, 'Ok_title' => 'Ok', 'Ok_desc' => 'Статус галлереи изменён на видимый')));
      exit();
    }
    
    function gallery_hide() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      $res = $this('album')->visibility_gallery($this->args[0], 0);
      if (isset($res['error'])) header('Location: http://'.$this('script_url').'upload/gallery/?'.http_build_query(array('error' => true, 'error_title' => 'Ошибка')));
      else header('Location: http://'.$this('script_url').'upload/gallery/?'.http_build_query(array('Ok' => true, 'Ok_title' => 'Ok', 'Ok_desc' => 'Галерея скрыта')));
      exit();
    }
    
    function gallery_rename() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      if (isset($_POST['command']) && $_POST['command'] == 'rename_gallery') {
        $res = $this('album')->rename_gallery($_POST['gallery_id'], $_POST['name_gallery']);
        if (isset($res['error'])) header('Location: http://'.$this('script_url').'upload/gallery_edit/'.$_POST['gallery_id'].'/?'.http_build_query(array('error' => true, 'error_title' => 'Ошибка')));
        else header('Location: http://'.$this('script_url').'upload/gallery_edit/'.$_POST['gallery_id'].'/?'.http_build_query(array('Ok' => true, 'Ok_title' => 'Ok', 'Ok_desc' => 'Галерея переименована')));
        exit();
      }
      header('Location: http://'.$this('script_url').'upload/gallery_edit/'.$_POST['gallery_id']);
      exit();
    }
    
    protected function get_param_feedback() {
      if (!$this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'log/');
        exit();
      }
      if (isset($_GET['Ok']{0})) $this('smarty')->assign('feedback', array('Ok' => $_GET['Ok'], 'Ok_title' => $_GET['Ok_title'], 'Ok_desc' => $_GET['Ok_desc']));
      if (isset($_GET['error']{0})) $this('smarty')->assign('feedback', array('error' => $_GET['error'], 'error_title' => $_GET['error_title'], 'error_desc' => $_GET['error_desc']));
      return true;
    }
  }
?>