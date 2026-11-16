</main>
<footer class="site-footer">
  <div class="footer-top">
    <div class="wrap footer-cta">
      <div>
        <h2>Got a dish worth passing on?</h2>
        <p>Add your recipe in two minutes. Anyone can cook it, and you can edit it any time.</p>
      </div>
      <a class="btn btn-light" href="<?= logged_in() ? 'add_recipe.php' : 'register.php' ?>"><?= logged_in() ? 'Add a recipe' : 'Create a free account' ?></a>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="wrap footer-grid">
      <div class="footer-brand">
        <strong><?= e(SITE_NAME) ?></strong>
        <p>Discover, share and manage recipes in one place.</p>
      </div>
      <div>
        <h4>Cooking</h4>
        <a href="recipes.php">All recipes</a>
        <a href="recipes.php?cat=1">Breakfast</a>
        <a href="recipes.php?cat=5">Desserts</a>
      </div>
      <div>
        <h4>Account</h4>
        <?php if (logged_in()): ?>
          <a href="my_recipes.php">My recipes</a>
          <a href="add_recipe.php">Add recipe</a>
          <a href="logout.php">Log out</a>
        <?php else: ?>
          <a href="login.php">Log in</a>
          <a href="register.php">Sign up</a>
        <?php endif; ?>
      </div>
      <div>
        <h4>Project</h4>
        <span>DBMS Mini Project</span>
        <span>PHP · MySQL · XAMPP</span>
      </div>
    </div>
    <div class="wrap footer-base"><span>© <?= date('Y') ?> <?= e(SITE_NAME) ?></span><span>DBMS Mini Project</span></div>
  </div>
</footer>
<script src="assets/js/main.js"></script>
</body>
</html>
