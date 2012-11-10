<?php
  require('./include/smarty/Smarty.class.php');
  
  class di_smarty extends Smarty {
    function __construct() {
      parent::__construct();
      
      $this->setTemplateDir('../view/');
      $this->setCompileDir('../view/templates_c/');
      $this->setConfigDir('./include/smarty/configs/');
      $this->setCacheDir('../view/cache/');
      $this->setPluginsDir('./include/smarty/plugins/');
      
      $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
      spl_autoload_register('__autoload');
    }

  }
?>