<?php
require 'config.php';
require 'includes/recipe_save.php';
require_login();

$recipe = blank_recipe();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = read_recipe_input();
    $recipe = array_merge($recipe, $data);
    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO recipes (user_id, category_id, title, description, ingredients, instructions, prep_time, cook_time, servings, emoji, color, image_url)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $data['category_id'], $data['title'], $data['description'], $data['ingredients'],
                        $data['instructions'], $data['prep_time'], $data['cook_time'], $data['servings'], $data['emoji'],
                        $data['color'], $data['image_url'] ?: null]);
        flash('success', 'Recipe published. It is now visible to everyone.');
        header('Location: recipe.php?id=' . db()->lastInsertId());
        exit;
    }
}

$pageTitle = 'Add a recipe';
$formTitle = 'Add a recipe';
$submitLabel = 'Publish recipe';
$action = 'add_recipe.php';
require 'includes/header.php';
require 'includes/recipe_form.php';
require 'includes/footer.php';
