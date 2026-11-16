<?php
// expects: $pageTitle (string), optional $bodyClass
$pageTitle = $pageTitle ?? SITE_NAME;
$bodyClass = $bodyClass ?? '';
$current = basename($_SERVER['SCRIPT_NAME']);
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700;9..144,800&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip" href="#main">Skip to content</a>
<header class="site-header">
  <div class="wrap nav">
    <a class="brand" href="index.php" aria-label="<?= e(SITE_NAME) ?> home">
      <span class="brand-mark">🍲</span>
      <span class="brand-text">Recipe<br>Sharing Portal</span>
    </a>
    <button class="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="nav-links"><span></span><span></span><span></span></button>
    <nav id="nav-links" class="nav-links">
      <a href="index.php"   class="<?= $current === 'index.php' ? 'on' : '' ?>">Home</a>
      <a href="recipes.php" class="<?= in_array($current, ['recipes.php', 'recipe.php']) ? 'on' : '' ?>">Recipes</a>
      <?php if (logged_in()): ?>
        <a href="my_recipes.php" class="<?= $current === 'my_recipes.php' ? 'on' : '' ?>">My recipes</a>
        <a href="add_recipe.php" class="<?= $current === 'add_recipe.php' ? 'on' : '' ?>">Add recipe</a>
      <?php endif; ?>
      <span class="nav-sep"></span>
      <?php if (logged_in()): ?>
        <span class="who" title="Signed in as <?= e($_SESSION['username']) ?>"><i><?= e(strtoupper(substr($_SESSION['username'], 0, 1))) ?></i><?= e($_SESSION['username']) ?></span>
        <a class="btn btn-ghost btn-sm" href="logout.php">Log out</a>
      <?php else: ?>
        <a href="login.php" class="<?= $current === 'login.php' ? 'on' : '' ?>">Log in</a>
        <a class="btn btn-sm" href="register.php">Sign up</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<?php if ($flash): ?>
  <div class="wrap"><div class="alert alert-<?= e($flash[0]) ?>" role="status"><?= e($flash[1]) ?></div></div>
<?php endif; ?>
<main id="main">
