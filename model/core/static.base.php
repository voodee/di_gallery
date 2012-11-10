<?php
  Abstract Class Base {
    abstract function index();

    function __invoke($name) {
      return Core::inst()->$name;
    }

    public function __set($name, $value) {
      Core::inst()->$name = $value;
    }

    public function __get($name) {
      if (isset(Core::inst()->$name))
        return Core::inst()->$name;
      return null;
    }

  }
?>