<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare(recipe_select() . ' WHERE r.recipe_id = ?');
$stmt->execute([$id]);
$r = $stmt->fetch();
if (!$r) {
    http_response_code(404);
    $pageTitle = 'Recipe not found';
    require 'includes/header.php';
    echo '<section class="section"><div class="wrap"><div class="empty"><div class="empty-emoji">🍽️</div><h3>That recipe is not here</h3><p>It may have been deleted by the person who shared it.</p><a class="btn" href="recipes.php">Browse recipes</a></div></div></section>';
    require 'includes/footer.php';
    exit;
}

$pageTitle = $r['title'];
$bodyClass = 'detail';
$owner = logged_in() && (int)$_SESSION['user_id'] === (int)$r['user_id'];
$img = safe_image($r['image_url']);

$rel = db()->prepare(recipe_select() . ' WHERE r.category_id = ? AND r.recipe_id <> ? ORDER BY RAND() LIMIT 3');
$rel->execute([$r['category_id'], $id]);
$related = $rel->fetchAll();

require 'includes/header.php';
?>
<article>
  <section class="detail-hero" style="--c:<?= e($r['color']) ?>">
    <div class="wrap detail-grid">
      <div class="detail-copy">
        <a class="crumb" href="recipes.php?cat=<?= (int)$r['category_id'] ?>">← <?= e($r['category']) ?></a>
        <h1><?= e($r['title']) ?></h1>
        <p class="lead"><?= e($r['description']) ?></p>
        <dl class="facts">
          <div><dt>Prep</dt><dd><?= (int)$r['prep_time'] ?> min</dd></div>
          <div><dt>Cook</dt><dd><?= (int)$r['cook_time'] ?> min</dd></div>
          <div><dt>Total</dt><dd><?= e(total_time($r)) ?></dd></div>
          <div><dt>Serves</dt><dd><?= (int)$r['servings'] ?></dd></div>
        </dl>
        <p class="by">Shared by <?= e($r['username']) ?> on <?= e(date('j M Y', strtotime($r['created_at']))) ?></p>
        <div class="actions">
          <button class="btn btn-light" type="button" data-print>Print recipe</button>
          <?php if ($owner): ?>
            <a class="btn btn-ghost-light" href="edit_recipe.php?id=<?= $id ?>">Edit</a>
            <form method="post" action="delete_recipe.php" data-confirm="Delete “<?= e($r['title']) ?>”? This cannot be undone.">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
              <button class="btn btn-danger" type="submit">Delete</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
      <div class="detail-art">
        <div class="big-plate"><span><?= e($r['emoji']) ?></span><?php if ($img): ?><img src="<?= e($img) ?>" alt="<?= e($r['title']) ?>" onerror="this.remove()"><?php endif; ?></div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="wrap method">
      <div class="panel">
        <h2>Ingredients</h2>
        <p class="hint">Tick items off as you gather them.</p>
        <ul class="checklist">
          <?php foreach (lines($r['ingredients']) as $i => $line): ?>
            <li><label><input type="checkbox"><span><?= e($line) ?></span></label></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="panel">
        <h2>Method</h2>
        <p class="hint">Tap a step to mark it done.</p>
        <ol class="method-steps">
          <?php foreach (lines($r['instructions']) as $line): ?>
            <li tabindex="0"><?= e($line) ?></li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <?php if ($related): ?>
  <section class="section section-tint">
    <div class="wrap">
      <div class="section-head"><h2>More in <?= e($r['category']) ?></h2></div>
      <div class="grid"><?php foreach ($related as $x) { echo recipe_card($x); } ?></div>
    </div>
  </section>
  <?php endif; ?>
</article>
<?php require 'includes/footer.php'; ?>
