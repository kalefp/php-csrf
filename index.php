<?php
session_start();


require 'class/csrf.php';
echo '<pre>';

$csrf = new CSRF();
// $csrf->generate(2);


var_dump($csrf);
$validation = $csrf->validate('53ed24edd35569883f4a41c25ae3a8153c683137e55a2bec4193ab683ee1cc27');
echo '<hr>';

// var_dump($validation);

// var_dump($csrf);


// var_dump($_SESSION);
echo '</pre>';

// unset($_SESSION['csrfTokens']);
