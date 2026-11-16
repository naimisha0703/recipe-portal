<?php
// Reads and validates recipe form input. Returns [$data, $errors].
function read_recipe_input(): array {
    $d = [
        'title'        => trim($_POST['title'] ?? ''),
        'category_id'  => (int)($_POST['category_id'] ?? 0),
        'description'  => trim($_POST['description'] ?? ''),
        'ingredients'  => trim($_POST['ingredients'] ?? ''),
        'instructions' => trim($_POST['instructions'] ?? ''),
        'prep_time'    => max(0, (int)($_POST['prep_time'] ?? 0)),
        'cook_time'    => max(0, (int)($_POST['cook_time'] ?? 0)),
        'servings'     => max(1, (int)($_POST['servings'] ?? 1)),
        'emoji'        => trim($_POST['emoji'] ?? '🍽️'),
        'color'        => trim($_POST['color'] ?? '#F4C9A0'),
        'image_url'    => trim($_POST['image_url'] ?? ''),
    ];
    $errors = [];
    if ($d['title'] === '')        $errors[] = 'Give the recipe a name.';
    if ($d['description'] === '')  $errors[] = 'Add a one-line description.';
    if ($d['ingredients'] === '')  $errors[] = 'List at least one ingredient.';
    if ($d['instructions'] === '') $errors[] = 'Add at least one step.';
    if ($d['category_id'] < 1)     $errors[] = 'Choose a category.';
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $d['color'])) $d['color'] = '#F4C9A0';
    if ($d['emoji'] === '' || mb_strlen($d['emoji']) > 8) $d['emoji'] = '🍽️';
    if ($d['image_url'] !== '' && !preg_match('#^https?://#i', $d['image_url'])) {
        $errors[] = 'The photo link must start with http:// or https://';
    }
    return [$d, $errors];
}

function blank_recipe(): array {
    return ['recipe_id' => 0, 'title' => '', 'category_id' => 1, 'description' => '', 'ingredients' => '',
            'instructions' => '', 'prep_time' => 10, 'cook_time' => 15, 'servings' => 2,
            'emoji' => '🍛', 'color' => '#F4C9A0', 'image_url' => ''];
}
