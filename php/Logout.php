<?php

session_start();

//destroi todas as variaveis de sessao
session_unset();

//Destroi a sessao
session_destroy();

//redireciona o usuario para a pagina de login apos o logout
header('Location: login.php');
exit();

?>
