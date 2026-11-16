<?php
require 'config.php';
require 'includes/recipe_save.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM recipes WHERE recipe_id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$recipe = $stmt->fetch();
if (!$recipe) {
    flash('error', 'You can only edit recipes you added.');
    header('Location: my_recipes.php');
    exit;
}
$recipe['image_url'] = (string)$recipe['image_url'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = read_recipe_input();
    $recipe = array_merge($recipe, $data);
    if (!$errors) {
        $upd = db()->prepare('UPDATE recipes SET category_id=?, title=?, description=?, ingredients=?, instructions=?,
                              prep_time=?, cook_time=?, servings=?, emoji=?, color=?, image_url=?
                              WHERE recipe_id=? AND user_id=?');
        $upd->execute([$data['category_id'], $data['title'], $data['description'], $data['ingredients'], $data['instructions'],
                       $data['prep_time'], $data['cook_time'], $data['servings'], $data['emoji'], $data['color'],
                       $data['image_url'] ?: null, $id, $_SESSION['user_id']]);
        flash('success', 'Changes saved.');
        header('Location: recipe.php?id=' . $id);
        exit;
    }
}

$pageTitle = 'Edit recipe';
$formTitle = 'Edit recipe';
$submitLabel = 'Save changes';
$action = 'edit_recipe.php?id=' . $id;
require 'includes/header.php';
require 'includes/recipe_form.php';
require 'includes/footer.php';
