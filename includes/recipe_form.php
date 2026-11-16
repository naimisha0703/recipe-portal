<?php
// expects: $recipe (array), $errors (array), $formTitle, $submitLabel, $action
$cats = db()->query('SELECT * FROM categories ORDER BY category_id')->fetchAll();
$emojis = ['🍳','🥞','🍛','🍲','🍝','🍚','🥗','🍅','🌯','🍟','🍢','🍕','🍔','🍫','🍮','🥭','🍰','🥛','☕','🍋'];
$colors = ['#F7D488','#F2A65A','#F4C9A0','#F28B7A','#B9D9A7','#E5E58F','#CDAA8A','#F6C1C7'];
?>
<section class="page-head"><div class="wrap"><h1><?= e($formTitle) ?></h1></div></section>
<section class="section section-tint tight">
  <div class="wrap">
    <form class="recipe-form" method="post" action="<?= e($action) ?>" novalidate>
      <?= csrf_field() ?>
      <?php if (!empty($recipe['recipe_id'])): ?><input type="hidden" name="id" value="<?= (int)$recipe['recipe_id'] ?>"><?php endif; ?>
      <?php if ($errors): ?>
        <div class="alert alert-error" role="alert"><strong>Fix these before saving:</strong>
          <ul><?php foreach ($errors as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <div class="form-grid">
        <div class="field plain wide">
          <label for="title">Recipe name</label>
          <input id="title" name="title" maxlength="120" value="<?= e($recipe['title']) ?>" placeholder="e.g. Lemon Rice" required>
        </div>
        <div class="field plain">
          <label for="category_id">Category</label>
          <select id="category_id" name="category_id" required>
            <?php foreach ($cats as $c): ?>
              <option value="<?= (int)$c['category_id'] ?>" <?= (int)$recipe['category_id'] === (int)$c['category_id'] ? 'selected' : '' ?>><?= e($c['emoji'] . ' ' . $c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field plain">
          <label for="servings">Servings</label>
          <input id="servings" name="servings" type="number" min="1" max="50" value="<?= (int)$recipe['servings'] ?>" required>
        </div>
        <div class="field plain">
          <label for="prep_time">Prep time (minutes)</label>
          <input id="prep_time" name="prep_time" type="number" min="0" max="1440" value="<?= (int)$recipe['prep_time'] ?>" required>
        </div>
        <div class="field plain">
          <label for="cook_time">Cook time (minutes)</label>
          <input id="cook_time" name="cook_time" type="number" min="0" max="1440" value="<?= (int)$recipe['cook_time'] ?>" required>
        </div>
        <div class="field plain wide">
          <label for="description">One-line description</label>
          <input id="description" name="description" maxlength="255" value="<?= e($recipe['description']) ?>" placeholder="What makes this dish good?" required>
        </div>
        <div class="field plain">
          <label for="ingredients">Ingredients <small>(one per line)</small></label>
          <textarea id="ingredients" name="ingredients" rows="9" placeholder="2 cups rice&#10;1 tsp turmeric" required><?= e($recipe['ingredients']) ?></textarea>
        </div>
        <div class="field plain">
          <label for="instructions">Method <small>(one step per line)</small></label>
          <textarea id="instructions" name="instructions" rows="9" placeholder="Boil the rice.&#10;Temper the spices." required><?= e($recipe['instructions']) ?></textarea>
        </div>
        <div class="field plain wide">
          <span class="label">Plate icon</span>
          <div class="swatches emojis">
            <?php foreach ($emojis as $em): ?>
              <label><input type="radio" name="emoji" value="<?= e($em) ?>" <?= $recipe['emoji'] === $em ? 'checked' : '' ?>><span><?= e($em) ?></span></label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="field plain wide">
          <span class="label">Card colour</span>
          <div class="swatches colors">
            <?php foreach ($colors as $col): ?>
              <label><input type="radio" name="color" value="<?= e($col) ?>" <?= strcasecmp($recipe['color'], $col) === 0 ? 'checked' : '' ?>><span style="background:<?= e($col) ?>"></span></label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="field plain wide">
          <label for="image_url">Photo link <small>(optional, starts with https://)</small></label>
          <input id="image_url" name="image_url" type="url" maxlength="500" value="<?= e($recipe['image_url']) ?>" placeholder="https://…">
        </div>
      </div>
      <div class="form-actions">
        <button class="btn" type="submit"><?= e($submitLabel) ?></button>
        <a class="btn btn-ghost" href="my_recipes.php">Cancel</a>
      </div>
    </form>
  </div>
</section>
