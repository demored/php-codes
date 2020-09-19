<?php
// ini_set('session.save_path', '2;e:\tmp');
ini_set('session.use_cookies',1);
ini_set('session.cookie_lifetime',60);
// session_set_cookie_params(30);
ini_set('session.gc_maxlifetime',20);
session_start();
// session_regenerate_id(true);
$_SESSION['obj'] = ['db' => 'mysql','cache' => 'memcached'];
echo 'session set ok';
