<?php
require 'config.php';
$_SESSION = [];
session_destroy();
session_start();
flash('success', 'You are logged out.');
header('Location: index.php');
exit;
