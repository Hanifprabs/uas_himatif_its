<?php 
    session_start();
    
    if (empty($_SESSION['login'])) {
      header('Location: login');
      exit;
    }
    
    session_unset();
    session_destroy();

    setcookie('login', '', time() - 3600);
    setcookie('level', '', time() - 3600);
    setcookie('id', '', time() - 3600);
    setcookie('key', '', time() - 3600);
    
    header('Location: index');
?>