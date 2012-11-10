<?php
  class di_db {
    private $_last_resource;

    function __construct() {
      mysql_connect(Core::inst()->bdhost, Core::inst()->bdlogin, Core::inst()->bdpassword) or die (mysql_error());
      mysql_select_db(Core::inst()->bdname) or die (mysql_error());
      mysql_query("set character_set_client = 'utf8'");
      mysql_query("set character_set_results = 'utf8'");
      mysql_query("set collation_connection = 'utf8mb4_unicode_ci'");
    }

    function query() {
      $args = func_get_args();
      $args[0] = str_replace('%', '%%', $args[0]);
      $args[0] = str_replace('?', '%s', $args[0]);
      foreach ($args as $i=>$v) {
        if (!$i) continue;
        if (is_int($v)) continue;
        if ($v == 'NULL') continue;
        $args[$i] = "'".$this->real_escape_string($v)."'";
      }
      $query = call_user_func_array('sprintf', $args);
      $query_result = mysql_query($query) or die(mysql_error());
      $this->_last_resource = $query_result;
      return $query_result;
    }

    function real_escape_string($str = false) {
      return mysql_real_escape_string($str);    }

    function num_rows($result = false) {
      if (!is_resource($result)) $result = $this->_last_resource;
      if (is_resource($result)) return mysql_num_rows($result);
      else return false;
    }

    function result($result = false, $int, $name) {
      if (!is_resource($result)) $result = $this->_last_resource;
      if (is_resource($result)) return mysql_result($result, $int, $name);
      else return false;    }

    function fetch_array($result = false) {
      if (!is_resource($result)) $result = $this->_last_resource;
      if (is_resource($result)) return mysql_fetch_array($result);
      else return false;
    }

    function fetch_assoc($result = false) {
      if (!is_resource($result)) $result = $this->_last_resource;
      if (is_resource($result)) return mysql_fetch_assoc($result);
      else return false;
    }

    function fetch_row($result = false) {
      if (!is_resource($result)) $result = $this->_last_resource;
      if (is_resource($result)) return mysql_fetch_row($result);
      else return false;
    }

    function affected_rows() {
      return mysql_affected_rows();
    }

    function load_all($result = false) {
      if (!is_resource($result)) $result = $this->_last_resource;
      if (is_resource($result)) {
        $return_data = array();
        while($data = $this->fetch_assoc($result)) $return_data[] = $data;
        return $return_data;      
      }
      else return false;
    }

    function load_all_assoc($key_column, $result = false) {
      if (!is_resource($result)) $result = $this->_last_resource;
      if (is_resource($result)) {
        $return_data = array();
        while($data = $this->fetch_assoc($result)) $return_data[$data[$key_column]] = $data;
        return $return_data;
      }
      else return false;
    }

    function insert_id() {
      return mysql_insert_id();
    }
  }
?>