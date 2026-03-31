<?php
session_start();
require_once dirname(__DIR__).'/config.php';
session_unset();
session_destroy();
header('Location: '.BASE.'/front/index.php');
exit;
