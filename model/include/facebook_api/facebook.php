<?php
/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once "base_facebook.php";

/**
 * Extends the BaseFacebook class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */
class Facebook extends BaseFacebook
{
  /**
   * Identical to the parent constructor, except that
   * we start a PHP session to store the user ID and
   * access token if during the course of execution
   * we discover them.
   *
   * @param Array $config the application configuration.
   * @see BaseFacebook::__construct in facebook.php
   */
  private $info;
  
  
  public function __construct($config, $id = 'NULL') {
    if (!session_id()) session_start();
    unset($this->info);
    parent::__construct($config, $id);
  }

  protected static $kSupportedKeys =
    array('state', 'code', 'access_token', 'user_id');
    

  /**
   * Provides the implementations of the inherited abstract
   * methods.  The implementation uses PHP sessions to maintain
   * a store for authorization codes, user ids, CSRF states, and
   * access tokens.
   */
  protected function setPersistentData($key, $value) { //echo ' > '.$key.' = '.$value;
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to setPersistentData.');
      return;
    }
    
    Core::inst()->db->query('SELECT `api_fb_id` FROM `di_api_fb` WHERE `api_fb_di_user_id` = ? AND (`api_fb_user_id` = ? OR `api_fb_user_id` IS NULL);', Core::inst()->user->user_id, $this->user);
    if (Core::inst()->db->num_rows() != 1) {
      Core::inst()->db->query('INSERT INTO `di_api_fb` (`api_fb_di_user_id`) VALUES (?);', Core::inst()->user->user_id);
      $api_fb_id = Core::inst()->db->insert_id();
    } else
      $api_fb_id = Core::inst()->db->result(false, 0, 'api_fb_id');
    
    Core::inst()->db->query('UPDATE `di_api_fb` SET `api_fb_'.$key.'` = ? WHERE `api_fb_id` = ?;', $value, $api_fb_id);
    $this->info[$key] = $value;
  }

  protected function getPersistentData($key, $default = false) {  //echo ' < '.$key;
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to getPersistentData.');
      return $default;
    }

    if (isset($this->info[$key])) 
      return $this->info[$key];   
    else {
      Core::inst()->db->query('SELECT `api_fb_'.$key.'` FROM `di_api_fb` WHERE `api_fb_di_user_id` = ? AND (`api_fb_id` = ? OR `api_fb_user_id` IS NULL) AND `api_fb_'.$key.'` IS NOT NULL;', Core::inst()->user->user_id, $this->key_id);
      if (Core::inst()->db->num_rows() > 0)
        return $this->info[$key] = Core::inst()->db->result(false, 0, 'api_fb_'.$key);  
      else 
        return $default;
    }
  }

  protected function clearPersistentData($key) {
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to clearPersistentData.');
      return;
    }
    
    Core::inst()->db->query('UPDATE `di_api_fb` SET `api_fb_'.$key.'` = NULL WHERE `api_fb_di_user_id` = ? AND `api_fb_id` = ?;', Core::inst()->user->user_id, $this->key_id);
    unset($this->info[$key]);
  }

  protected function clearAllPersistentData() { 
    $sql = '';
    foreach (self::$kSupportedKeys as $key) {
      if ($sql != '') $sql .= ' , ';
      $sql .= '`api_fb_'.$key.'` = NULL';
    }
    Core::inst()->db->query('DELETE FROM `di_api_fb` WHERE `api_fb_di_user_id` = ? AND `api_fb_id` = ?;', Core::inst()->user->get_id(), $this->key_id);
    unset($this->info);
  }
}
