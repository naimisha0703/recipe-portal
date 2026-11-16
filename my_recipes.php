<?php
require 'config.php';
require_login();
$pageTitle = 'My recipes';

$stmt = db()->prepare(recipe_select() . ' WHERE r.user_id = ? ORDER BY r.created_at DESC, r.recipe_id DESC');
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();

require 'includes/header.php';
?>
<section class="page-head">
  <div class="wrap head-row">
    <h1>My recipes</h1>
    <a class="btn" href="add_recipe.php">Add a recipe</a>
  </div>
</section>
<section class="section section-tint tight">
  <div class="wrap">
    <?php if ($rows): ?>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Recipe</th><th>Category</th><th>Total time</th><th>Added</th><th><span class="sr">Actions</span></th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td data-label="Recipe"><a class="t-title" href="recipe.php?id=<?= (int)$r['recipe_id'] ?>"><span class="t-plate" style="--c:<?= e($r['color']) ?>"><?= e($r['emoji']) ?></span><?= e($r['title']) ?></a></td>
              <td data-label="Category"><?= e($r['category']) ?></td>
              <td data-label="Total time"><?= e(total_time($r)) ?></td>
              <td data-label="Added"><?= e(date('j M Y', strtotime($r['created_at']))) ?></td>
              <td class="t-actions">
                <a class="btn btn-ghost btn-sm" href="edit_recipe.php?id=<?= (int)$r['recipe_id'] ?>">Edit</a>
                <form method="post" action="delete_recipe.php" data-confirm="Delete “<?= e($r['title']) ?>”? This cannot be undone.">
                  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['recipe_id'] ?>">
                  <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty">
        <div class="empty-emoji">📖</div>
        <h3>You have not added a recipe yet</h3>
        <p>Start with a dish you cook often. Ingredients and steps are all it needs.</p>
        <a class="btn" href="add_recipe.php">Add your first recipe</a>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require 'includes/footer.php'; ?>
