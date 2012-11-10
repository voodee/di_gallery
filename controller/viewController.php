<?php

class viewController Extends Base {

  function index() {}

  function images() {
    ini_set('memory_limit', '128M');
    require('include/cypher.class.php');
    require('include/timthumb/timthumb.class.php');
    $ecode_str = base64_decode( str_replace(preg_split('~~u', '-_,', null, PREG_SPLIT_NO_EMPTY), preg_split('~~u', '+/=', null, PREG_SPLIT_NO_EMPTY), $this->args[0]) );
    $cypher = new Cypher();
    $cypher->key = Core::inst()->img_password;
    $images_param['src'] = '../view/upload/images/' . $cypher->decrypt(mb_substr($ecode_str, mb_strlen(Core::inst()->img_prefix, 'UTF-8') - mb_strlen($ecode_str, 'UTF-8')));
    
    $this('db')->query('SELECT * FROM `di.album_gallery`, `di.album_photo` WHERE `di.album_gallery`.`album_gallery_id` = `di.album_photo`.`album_photo_gallery_id` AND `di.album_photo`.`album_photo_name` = ? AND `di.album_gallery`.`album_gallery_visible` = 1;', $cypher->decrypt(mb_substr($ecode_str, mb_strlen(Core::inst()->img_prefix, 'UTF-8') - mb_strlen($ecode_str, 'UTF-8'))));
    if ($this('db')->affected_rows() == 0) exit(0);
    
    if (isset($this->args[1]) && $this->args[1] != 0) $images_param['w'] = $this->args[1];
    if (isset($this->args[2]) && $this->args[2] != 0) $images_param['h'] = $this->args[2];
    $images_param['q'] = 100;
    timthumb::start($images_param);
  }

}
?>
