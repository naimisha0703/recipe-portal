<?php
require 'config.php';
$pageTitle = 'Recipes';

$q    = trim($_GET['q'] ?? '');
$cat  = (int)($_GET['cat'] ?? 0);
$sort = $_GET['sort'] ?? 'new';

$where = [];
$args  = [];
if ($q !== '') {
    $where[] = '(r.title LIKE ? OR r.description LIKE ? OR r.ingredients LIKE ?)';
    $like = '%' . $q . '%';
    array_push($args, $like, $like, $like);
}
if ($cat > 0) { $where[] = 'r.category_id = ?'; $args[] = $cat; }

$order = match ($sort) {
    'quick' => '(r.prep_time + r.cook_time) ASC, r.title ASC',
    'az'    => 'r.title ASC',
    default => 'r.created_at DESC, r.recipe_id DESC',
};

$sql = recipe_select() . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY ' . $order;
$stmt = db()->prepare($sql);
$stmt->execute($args);
$rows = $stmt->fetchAll();
$cats = categories();

require 'includes/header.php';
?>
<section class="page-head">
  <div class="wrap">
    <h1>Recipes</h1>
    <form class="filters" method="get" action="recipes.php" role="search">
      <label class="sr" for="q">Search</label>
      <input id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="Search by name or ingredient">
      <label class="sr" for="sort">Sort</label>
      <select id="sort" name="sort">
        <option value="new"   <?= $sort === 'new' ? 'selected' : '' ?>>Newest first</option>
        <option value="quick" <?= $sort === 'quick' ? 'selected' : '' ?>>Quickest first</option>
        <option value="az"    <?= $sort === 'az' ? 'selected' : '' ?>>A to Z</option>
      </select>
      <input type="hidden" name="cat" value="<?= $cat ?>">
      <button class="btn" type="submit">Search</button>
    </form>
    <div class="pills">
      <?php $qs = fn($c) => 'recipes.php?' . http_build_query(array_filter(['q' => $q, 'sort' => $sort === 'new' ? '' : $sort, 'cat' => $c])); ?>
      <a href="<?= e($qs(0)) ?>" class="<?= $cat === 0 ? 'on' : '' ?>">All</a>
      <?php foreach ($cats as $c): ?>
        <a href="<?= e($qs((int)$c['category_id'])) ?>" class="<?= $cat === (int)$c['category_id'] ? 'on' : '' ?>"><?= e($c['emoji']) ?> <?= e($c['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-tint tight">
  <div class="wrap">
    <p class="count"><?= count($rows) ?> <?= count($rows) === 1 ? 'recipe' : 'recipes' ?><?= $q !== '' ? ' for “' . e($q) . '”' : '' ?></p>
    <?php if ($rows): ?>
      <div class="grid"><?php foreach ($rows as $r) { echo recipe_card($r); } ?></div>
    <?php else: ?>
      <div class="empty">
        <div class="empty-emoji">🔍</div>
        <h3>No recipes match that search</h3>
        <p>Check the spelling, try a single ingredient, or clear the category filter.</p>
        <a class="btn" href="recipes.php">Show all recipes</a>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require 'includes/footer.php'; ?>
