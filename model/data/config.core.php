<?php
  Core::inst()->server_name = $_SERVER['SERVER_NAME'];
  Core::inst()->script_url = Core::inst()->server_name.preg_replace('/^(.*?)model\/index\.php$/is', '$1', $_SERVER['SCRIPT_NAME']);  
?>