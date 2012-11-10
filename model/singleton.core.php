<?php
  class Core {
    protected static $instance;
    private static $data = array();

    private function __construct() { /* ... */ }

    private function __clone() { /* ... */ }

    public static function inst() {
      if (is_null(self::$instance))
        self::$instance = new self;
      return self::$instance;
    }

    public function __set($name, $value) {
      self::$data[$name] = $value;
    }

    public function __get($name) {
      if (array_key_exists($name, self::$data))
        return self::$data[$name];

      $trace = debug_backtrace();
      trigger_error(
        'Undefined property via __get(): ' . $name .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE);
      return null;
    }

    public function __invoke($name) {
      return $this->__get($name);
    }

    public function __isset($name) {
      return isset(self::$data[$name]);
    }

    public function __unset($name) {
      unset(self::$data[$name]);
    }

    public function __call($name, $arguments) { /* ... */ }

    public static function __callStatic($name, $arguments) { /* ... */ }
  }
?>