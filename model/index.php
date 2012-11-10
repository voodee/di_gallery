<?php
  // проверяем версию PHP
  if (version_compare(PHP_VERSION, '5.3.0', '<')) {die ('Must PHP 5.3 or higher.');}
  // настраиваем автоматическую подгрузку классов
  function __autoload($class_name) {
    //sscanf($class_name, "di_%s", $file_name);
    $file_name = str_replace('di_', '', $class_name);
    if (is_file('core/class.'.$file_name.'.php'))
      require('core/class.'.$file_name.'.php');
    else
      throw new Exception('Class '.$class_name.' not found.');
  }
  // стат
  $_start_time = microtime(true);
  // настраиваем вывод ошибок в файл
  function write2log($code, $message, $file, $line) {
    unset($GLOBALS['tmp_buf']);    
    $dst = fopen('error.log', 'a');
    fputs($dst, $code.': '.$message.' '.$file.' '.$line."\r\n");
    fclose($dst);
  }
  function fatalerror() {
    $last_error = error_get_last();
    if ($last_error['type'] === E_ERROR)
      write2log(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
  }
  set_error_handler('write2log');
  register_shutdown_function('fatalerror');
  // устанавливаем include path
  set_include_path(get_include_path() . PATH_SEPARATOR . realpath('./'));
  // разбираем запрос
  // получаем путь размещения скрипта
  $_URL = preg_replace('/^(.*?)model\/index\.php$/is', '$1', $_SERVER['SCRIPT_NAME']);
  // получаем параметры, переданные скрипту
  $_URL = preg_replace('/^'.preg_quote($_URL, '/').'/is', '', urldecode($_SERVER['REQUEST_URI']));
  // обрезаем GET запросы и убираем обязательный конечный слеш
  $_URL = preg_replace('/(\/?)(\?.*)?$/is', '', $_URL);
  // убираем ненужное
  $_URL = preg_replace('/[^=0-9A-Za-zА-Яа-яЁё\\/\Q ,._-:+`\E]/su', '', $_URL);
  $_URL = explode('/', $_URL);
  // обрезаем окончание
  $_URL[count($_URL) - 1] = preg_replace('/\.(?:html|xml|php)$/is', '', $_URL[count($_URL) - 1]);
  // создаем класс для хранения информации
  require 'singleton.core.php'; 
  Core::inst()->controller = (isset($_URL[0])) ? array_shift($_URL) : '';
  Core::inst()->action = (isset($_URL[0])) ? array_shift($_URL) : 'index';
  Core::inst()->args = $_URL;
  unset($_URL);
  // установка системы
  if (!is_file('data/config.bd.php')) require('install.php');
  // подгружаем конфиг файлы 
  foreach (scandir('data/') as $config_file)    
    if (is_file('data/'.$config_file) && $config_file[0] != '.')
      require('data/'.$config_file);
  // подгружае библиотеки
  foreach (scandir('core/') as $class_file)
    if (is_file('core/'.$class_file))
      if (strstr($class_file, 'class'))
        try {
          $class_file = 'di_'.str_replace(array('class.', '.php'), '', $class_file);
          $name_for_core = substr($class_file, 3);
          Core::inst()->$name_for_core = new $class_file();
        } catch (Exception $e) {
          write2log(0, $e->getMessage(), 'index.php', 80);
        }
      else
        require('core/'.$class_file);
  // загружаем фильтр для сжатия данных
  Core::inst()->smarty->loadfilter('output','gzip');
  // расширяем область видимости
  Core::inst()->smarty->assign('_', Core::inst());
  Core::inst()->smarty->assign('l', 'http://'.Core::inst()->script_url);
  // добавляем плюшки
  Core::inst()->smarty->registerPlugin('modifier', 'ss', 'stripslashes');
  // передаём управление контролеру, если его нет, то подключаем контроллер по умолчанию
  // если контроллер существует
  if (is_file($file_controller = '../controller/'.Core::inst()->controller.'Controller.php')) {
    // он подключаеться
    require $file_controller;
    $controller_class_name = Core::inst()->controller.'Controller';
    // если действие определено(или пусто, по умолчанию), то оно вызываеться, если оно не определено, то управление передаёться контроллеру ошибок
    $controller_action_name = (Core::inst()->action == '')?'index':(!is_callable(array($controller_class_name, Core::inst()->action)))?'errorController':Core::inst()->action;
    if ($controller_action_name == 'errorController') {
      require '../controller/errorController.php';
      $controller_class_name = 'errorController';
      $controller_action_name = 'index';
    }
  // если контролер не существует то управление передаёться контролеру по умолчанию
  } else {
    require '../controller/Controller.php';
    $controller_class_name = 'Controller';
    $controller_action_name = 'index';
  }
  $controller = new $controller_class_name();  
  $controller->$controller_action_name();
  // энд стат
  printf ("\n<!-- Время выполнения: %f сек. -->", microtime(true) - $_start_time);
?>