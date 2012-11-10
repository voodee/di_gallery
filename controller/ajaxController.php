<?php
  class ajaxController Extends Base {

    function index() {
    }
    
    function get_list_photo() {
      if (!isset($_POST['gallery_id'])) {
        echo json_encode(array());
        exit();
      }
      require_once('include/cypher.class.php');
      $cypher = new Cypher();
      $cypher->key = Core::inst()->img_password;
 
      $this('db')->query('SELECT `album_photo_name` FROM `di.album_photo` WHERE `album_photo_gallery_id` = ?;', $_POST['gallery_id']);
      $_echo = array();
      foreach ($this('db')->load_all() AS $photo) 
        $_echo[] = str_replace(preg_split('~~u', '+/=', null, PREG_SPLIT_NO_EMPTY), preg_split('~~u', '-_,', null, PREG_SPLIT_NO_EMPTY), base64_encode(Core::inst()->img_prefix . $cypher->encrypt($photo['album_photo_name']) ));
      echo json_encode($_echo);
      exit();
    }
  }
?>