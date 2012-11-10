<?php
  class di_album {
    function __construct() { }
    
    function add_gallery($name) {
      if (!isset($name{0})) return array('error' => true, 'error_code' => 0);
      if (mb_strlen($name, 'UTF-8') > 255) return array('error' => true, 'error_code' => 1);
      
      Core::inst()->db->query('SELECT * FROM `di.album_gallery` WHERE `album_gallery_user_id` = ? AND `album_gallery_name` = ? ORDER BY `album_gallery_created_date` DESC;', Core::inst()->user->get_id(), $name);
      if (Core::inst()->db->num_rows() != 0) return array('error' => true, 'error_code' => 2);
      
      Core::inst()->db->query('SELECT * FROM `di.album_gallery` WHERE `album_gallery_user_id` = ?;', Core::inst()->user->get_id());
      Core::inst()->db->query(
        'INSERT INTO `di.album_gallery` (
          `album_gallery_user_id`, `album_gallery_name`, `album_gallery_created_ip`, `album_gallery_created_date`, `album_gallery_priority`)
        VALUES (
          ?, ?, ?, ?, ?);',
        Core::inst()->user->get_id(), $name, $_SERVER['REMOTE_ADDR'], time(), Core::inst()->db->num_rows()
      );
      return array('Ok' => true, 'id' => Core::inst()->db->insert_id());
    }
    
    function get_gallery($album) {
      Core::inst()->db->query('SELECT * FROM `di.album_gallery` WHERE `album_gallery_user_id` = ? AND `album_gallery_id` = ? ORDER BY `album_gallery_created_date` DESC;', Core::inst()->user->get_id(), $album);
      if (Core::inst()->db->num_rows() == 0)
        return array('error' => true, 'error_code' => 0);
      $_return['id'] = Core::inst()->db->result(false, 0, 'album_gallery_id');
      $_return['name'] = Core::inst()->db->result(false, 0, 'album_gallery_name');
      Core::inst()->db->query('SELECT * FROM `di.album_photo` WHERE `album_photo_gallery_id` = ? ORDER BY `album_photo_created_date` DESC;', $_return['id']);
      $_return['photo'] = Core::inst()->db->load_all();
      return $_return;
    }
    
    function get_gallerys() {
      Core::inst()->db->query('SELECT * FROM `di.album_gallery` WHERE `album_gallery_user_id` = ? ORDER BY `album_gallery_priority`;', Core::inst()->user->get_id());
      if (Core::inst()->db->num_rows() == 0) return array('error' => true, 'error_code' => 0);
      
      return array('Ok' => true, 'albums' => Core::inst()->db->load_all());
    }
    
    function visibility_gallery($gallery, $status = 1) {
      if (!in_array($status, array(0, 1))) return array('error' => true, 'error_code' => 0);

      Core::inst()->db->query('UPDATE `di.album_gallery` SET `album_gallery_visible` = ? WHERE `album_gallery_id` = ? AND `album_gallery_user_id` = ?;', $status, (int)$gallery, Core::inst()->user->get_id());
      if (Core::inst()->db->affected_rows() != 0)
        return array('Ok' => true);
      else
        return array('error' => true, 'error_code' => 1);
    }
    
    function rename_gallery($id, $name) {
      if (mb_strlen($name, 'UTF-8') > 255) return array('error' => true, 'error_code' => 0);
      
      Core::inst()->db->query('UPDATE `di.album_gallery` SET `album_gallery_name` = ? WHERE `album_gallery_id` = ? AND `album_gallery_user_id` = ?;', $name, (int)$id, Core::inst()->user->get_id());
      if (Core::inst()->db->affected_rows() != 0)
        return array('Ok' => true);
      else
        return array('error' => true, 'error_code' => 1);
    }

    function get_public_gallerys() {
      require_once('include/cypher.class.php');
      $cypher = new Cypher();
      $cypher->key = Core::inst()->img_password;
      Core::inst()->db->query('
        SELECT 
          *,
          (SELECT `album_photo_name` FROM `di.album_photo` WHERE `album_photo_gallery_id` = `_di.album_gallery`.`album_gallery_id` ORDER BY `album_photo_created_date` LIMIT 1) AS `album_gallery_preview`
        FROM `di.album_gallery` `_di.album_gallery` 
        WHERE `_di.album_gallery`.`album_gallery_visible` = 1
        ORDER BY `album_gallery_priority`;
      ');
      if (Core::inst()->db->num_rows() == 0) return array('error' => true, 'error_code' => 0);
      
      $albums = Core::inst()->db->load_all_assoc('album_gallery_id');
      foreach ($albums as $key => $val) 
        $albums[$key]['album_gallery_preview'] = str_replace(preg_split('~~u', '+/=', null, PREG_SPLIT_NO_EMPTY), preg_split('~~u', '-_,', null, PREG_SPLIT_NO_EMPTY), base64_encode(Core::inst()->img_prefix . $cypher->encrypt($val['album_gallery_preview']) ));
      
      return array('Ok' => true, 'albums' => $albums);
    }

    function del_gallery($id) {
      Core::inst()->db->query('DELETE FROM `di.album_gallery` WHERE `album_gallery_id` = ? AND `album_gallery_user_id` = ? LIMIT 1;', $id, Core::inst()->user->get_id()); 
      if (Core::inst()->db->affected_rows() == 0) return array('error' => true, 'error_code' => 0);
      
      if (Core::inst()->db->affected_rows()) { 
        Core::inst()->db->query('SELECT `album_photo_name` FROM `di.album_photo` WHERE `album_photo_gallery_id` IS NULL;');
        foreach (Core::inst()->db->load_all() as $key => $value) 
          if (is_file('../view/upload/images/'.$value['album_photo_name'])) {
            unlink('../view/upload/images/'.$value['album_photo_name']);
            unlink('../view/upload/images/thumbnail/'.$value['album_photo_name']);
          }
        return 1;
      }
      return 0;
    }
    
    function add_photo($name, $gallery) {
      $_gallery = $this->get_gallery($gallery);
      if (!isset($_gallery['id'])) return array('error' => true);
      
      Core::inst()->db->query(
        'INSERT INTO `di.album_photo` (
          `album_photo_gallery_id`, `album_photo_name`, `album_photo_created_ip`, `album_photo_created_date`)
        VALUES (
          ?, ?, ?, ?);',
        $_gallery['id'], $name, $_SERVER['REMOTE_ADDR'], time()
      );
      return array('Ok' => true, 'id' => Core::inst()->db->insert_id());
    }
    
    function del_photo($name) {
      Core::inst()->db->query('SELECT * FROM `di.album_photo` WHERE `album_photo_name` = ? LIMIT 1;', $name);
      if (Core::inst()->db->num_rows() == 0) return array('error' => true, 'error_code' => 0);
      
      $gallery = $this->get_gallery(Core::inst()->db->result(false, 0, 'album_photo_gallery_id'));
      if (!isset($gallery['id'])) return array('error' => true, 'error_code' => 1);
      
      
      Core::inst()->db->query('DELETE FROM `di.album_photo` WHERE `album_photo_name` = ? LIMIT 1;', $name); 
      return Core::inst()->db->affected_rows();
    }
  }
?>