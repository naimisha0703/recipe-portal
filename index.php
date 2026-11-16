<?php
require 'config.php';
$pageTitle = 'Cook something worth sharing';
$bodyClass = 'home';

$cats    = categories();
$latest  = db()->query(recipe_select() . ' ORDER BY r.created_at DESC, r.recipe_id DESC LIMIT 6')->fetchAll();
$collage = db()->query(recipe_select() . ' ORDER BY RAND() LIMIT 3')->fetchAll();
$stats   = db()->query('SELECT (SELECT COUNT(*) FROM recipes) AS recipes,
                               (SELECT COUNT(*) FROM categories) AS cats,
                               (SELECT COUNT(*) FROM users) AS cooks')->fetch();

require 'includes/header.php';
?>
<section class="hero">
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <h1>Cook something worth sharing.</h1>
      <p class="lead">Browse <?= (int)$stats['recipes'] ?> home-tested recipes, see exactly what goes in and how long it takes, then add your own for others to cook.</p>
      <form class="hero-search" action="recipes.php" method="get" role="search">
        <label class="sr" for="hq">Search recipes</label>
        <input id="hq" type="search" name="q" placeholder="Try paneer, mango, pasta…" autocomplete="off">
        <select name="cat" aria-label="Category">
          <option value="0">Any category</option>
          <?php foreach ($cats as $c): ?><option value="<?= (int)$c['category_id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Find recipes</button>
      </form>
      <div class="chips">
        <?php foreach (['Paneer', 'Mango', 'Chocolate', 'Rice'] as $t): ?>
          <a href="recipes.php?q=<?= e(strtolower($t)) ?>"><?= e($t) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="hero-stack" aria-hidden="true">
      <?php foreach ($collage as $i => $r): ?>
        <a tabindex="-1" class="stack-card s<?= $i + 1 ?>" href="recipe.php?id=<?= (int)$r['recipe_id'] ?>">
          <div class="card-art" style="--c:<?= e($r['color']) ?>"><span class="plate"><?= e($r['emoji']) ?></span></div>
          <div class="stack-label"><strong><?= e($r['title']) ?></strong><span><?= e(total_time($r)) ?></span></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head">
      <h2>Start with a craving</h2>
    </div>
    <div class="cat-row">
      <?php foreach ($cats as $c): ?>
        <a class="cat" href="recipes.php?cat=<?= (int)$c['category_id'] ?>">
          <span class="cat-emoji"><?= e($c['emoji']) ?></span>
          <strong><?= e($c['name']) ?></strong>
          <small><?= (int)$c['total'] ?> recipes</small>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-tint">
  <div class="wrap">
    <div class="section-head">
      <h2>Fresh from the kitchen</h2>
      <a class="link" href="recipes.php">See all recipes</a>
    </div>
    <div class="grid">
      <?php foreach ($latest as $r) { echo recipe_card($r); } ?>
    </div>
    <div class="center"><a class="btn btn-ghost" href="recipes.php">Browse all <?= (int)$stats['recipes'] ?> recipes</a></div>
  </div>
</section>

<section class="section">
  <div class="wrap how">
    <div class="how-intro">
      <h2>Your recipes, kept in one place</h2>
      <p>Everything lives in a MySQL database, so every recipe you add can be found, edited or removed whenever you like.</p>
    </div>
    <ol class="steps-list">
      <li><strong>Create an account</strong><span>Sign up with a username, email and password.</span></li>
      <li><strong>Add a recipe</strong><span>List ingredients, steps, timings and servings.</span></li>
      <li><strong>Manage it</strong><span>Edit or delete your recipes from My recipes.</span></li>
    </ol>
  </div>
</section>
<?php require 'includes/footer.php'; ?>
