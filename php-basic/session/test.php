<?php
ini_set('session.use_cookies',1);
ini_set('session.cookie_lifetime',60);
// session_set_cookie_params(30);
ini_set('session.gc_maxlifetime',20);
session_start();
echo '<br/>';
print_r($_SESSION);
