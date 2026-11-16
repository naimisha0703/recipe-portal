<?php
require 'config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: my_recipes.php'); exit; }
csrf_check();

$stmt = db()->prepare('DELETE FROM recipes WHERE recipe_id = ? AND user_id = ?');
$stmt->execute([(int)($_POST['id'] ?? 0), $_SESSION['user_id']]);

flash($stmt->rowCount() ? 'success' : 'error', $stmt->rowCount() ? 'Recipe deleted.' : 'You can only delete recipes you added.');
header('Location: my_recipes.php');
exit;
