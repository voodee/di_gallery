<?php
  class logController Extends Base {

    function index() {
      header('Location: http://'.$this('script_url').'log/in/');
      exit();
    }
    
    function in() {
      if ($this('user')->is_loggedin()) {
        header('Location: http://'.$this('script_url').'upload/gallery/');
        exit();
      }
      
      if (!session_id()) session_start();
      if (!isset($_SESSION['di_captcha_count'])) $_SESSION['di_captcha_count'] = 0;
      if (isset($_POST['action']{14}) && $_POST['action'] == 'captcha_refresh') {
        define('THE_NUMBER_OF_LETTERS', 6);
        require_once('include/di_captcha.class.php');
        $captcha = new di\captcha();
        $captcha->set('noise', 0);
        echo json_encode(array(THE_NUMBER_OF_LETTERS, $captcha->get()));
        exit();
      }
      
      if (isset($_REQUEST['login']{0}) && isset($_REQUEST['password']{0})) {
        ++$_SESSION['di_captcha_count'];
        require_once('include/di_captcha.class.php');
        $captcha = new di\captcha();
        if ($captcha->check($_POST['captcha']) || $_SESSION['di_captcha_count'] < 4) {
          $feedback = $this('user')->login($_REQUEST['login'], $_REQUEST['password']);
          if ($feedback[0]) {
            header('Location: http://'.$this('script_url').'upload/gallery/');
            $_SESSION['di_captcha_count'] = 0;
          } else
            header('Location: http://'.$this('script_url').'log/in/?'.http_build_query(array('error'=>true, 'error_msg'=>$feedback[1], 'login'=>$_REQUEST['login'], 'pass'=>$_REQUEST['password'], 'captcha'=>$_POST['captcha'])));
        } else 
          header('Location: http://'.$this('script_url').'log/in/?'.http_build_query(array('error'=>true, 'error_msg'=>4, 'login'=>$_REQUEST['login'], 'pass'=>$_REQUEST['password'], 'captcha'=>$_POST['captcha']))); 
        exit();
      }
       
      if (isset($_GET['error']{0})) {
        switch ($_GET['error_msg']) {
          case 1:
            $error = 'Пароль или ник не коректны';
            break;
          case 2:
            $error = 'Ник или пароль введены не верно';
            break;
          case 3:
            $error = 'Аккаунт не подтверждён';
            break;
          case 4:
            $error = 'Символы проверки не верны';
            break;
        }
        $this('smarty')->assign('feedback', array('error' => 1, 'error_title' => $error));
      }
      
      $this('smarty')->caching = false;
      $this('smarty')->assign('di_captcha_count', $_SESSION['di_captcha_count']);
      $this('smarty')->assign('login', (isset($_GET['login']))?$_GET['login']:'');
      $this('smarty')->assign('pass', (isset($_GET['pass']))?$_GET['pass']:'');
      $this('smarty')->assign('captha', (isset($_GET['captcha']))?$_GET['captcha']:'');
      $this('smarty')->assign('title', 'Вход');
      $this('smarty')->assign('page', 'login');
      $this('smarty')->display('log.in.tpl');
    }

    function out() {
      $this('user')->logout();
      header('Location: http://'.$this('script_url'));
      exit();
    }
    
  }
?>