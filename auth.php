<?php
// Shared login + sign-up screen. login.php and register.php set $mode and include this file.
require_once 'config.php';

if (logged_in()) { header('Location: index.php'); exit; }

$mode   = $mode ?? 'login';
$errors = ['login' => '', 'register' => ''];
$old    = ['login_id' => '', 'username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $mode = 'login';
        $id = trim($_POST['login_id'] ?? '');
        $pw = $_POST['password'] ?? '';
        $old['login_id'] = $id;
        $stmt = db()->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$id, $id]);
        $u = $stmt->fetch();
        if ($u && password_verify($pw, $u['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = (int)$u['user_id'];
            $_SESSION['username'] = $u['username'];
            flash('success', 'Welcome back, ' . $u['username'] . '.');
            $next = $_SESSION['after_login'] ?? 'index.php';
            unset($_SESSION['after_login']);
            header('Location: ' . (preg_match('#^[a-z_]+\.php(\?[\w=&%-]*)?$#i', $next) ? $next : 'index.php'));
            exit;
        }
        $errors['login'] = 'That username or password is not right. Check both and try again.';
    }

    if ($action === 'register') {
        $mode = 'register';
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $pw       = $_POST['password'] ?? '';
        $pw2      = $_POST['confirm'] ?? '';
        $old['username'] = $username;
        $old['email']    = $email;

        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
            $errors['register'] = 'Username must be 3 to 20 characters: letters, numbers or underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['register'] = 'Enter a valid email address, like name@example.com.';
        } elseif (strlen($pw) < 8 || !preg_match('/[A-Za-z]/', $pw) || !preg_match('/\d/', $pw)) {
            $errors['register'] = 'Password needs at least 8 characters, with a letter and a number.';
        } elseif ($pw !== $pw2) {
            $errors['register'] = 'The two passwords do not match.';
        } else {
            $check = db()->prepare('SELECT username, email FROM users WHERE username = ? OR email = ?');
            $check->execute([$username, $email]);
            if ($row = $check->fetch()) {
                $errors['register'] = strcasecmp($row['username'], $username) === 0
                    ? 'That username is taken. Choose another one.'
                    : 'An account with that email already exists. Log in instead.';
            } else {
                $ins = db()->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
                $ins->execute([$username, $email, password_hash($pw, PASSWORD_DEFAULT)]);
                session_regenerate_id(true);
                $_SESSION['user_id']  = (int)db()->lastInsertId();
                $_SESSION['username'] = $username;
                flash('success', 'Account created. Add your first recipe whenever you are ready.');
                header('Location: add_recipe.php');
                exit;
            }
        }
    }
}

$pageTitle = $mode === 'register' ? 'Create account' : 'Log in';
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
<body class="auth-body">
<?php $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); ?>
<div class="auth" data-mode="<?= e($mode) ?>">

  <aside class="stage" id="stage" aria-hidden="true">
    <a class="stage-brand" href="index.php" tabindex="-1"><span class="brand-mark">🍲</span> <?= e(SITE_NAME) ?></a>
    <?php
      $floaters = [
        ['🍛', '8%', '14%', 1.0, '7s', '#F2A65A'],  ['🥗', '62%', '8%', .8, '9s', '#B9D9A7'],
        ['🍰', '72%', '32%', 1.1, '8s', '#F6C1C7'], ['🍝', '10%', '40%', .9, '10s', '#EBC9A4'],
        ['🥭', '44%', '26%', .7, '6s', '#F8C35C'],  ['🍳', '32%', '48%', .9, '9s', '#F7D488'],
        ['🍋', '68%', '54%', .75, '7s', '#E5E58F'],
      ];
      foreach ($floaters as $i => [$em, $x, $y, $s, $d, $c]): ?>
      <div class="plate-wrap" style="left:<?= $x ?>;top:<?= $y ?>;--depth:<?= $i % 3 + 1 ?>">
        <div class="float-plate" style="--s:<?= $s ?>;--d:<?= $d ?>;--c:<?= $c ?>;animation-delay:-<?= $i * 1.3 ?>s"><?= $em ?></div>
      </div>
    <?php endforeach; ?>
    <div class="stage-copy">
      <h2>Your next favourite dish is waiting.</h2>
      <p class="ticker">Now cooking: <b id="ticker">Paneer Butter Masala</b></p>
    </div>
  </aside>

  <section class="auth-panel">
    <a class="back" href="index.php">← Back to recipes</a>
    <div class="auth-card">
      <div class="tabs" role="tablist">
        <button type="button" role="tab" data-mode="login" aria-selected="<?= $mode === 'login' ? 'true' : 'false' ?>">Log in</button>
        <button type="button" role="tab" data-mode="register" aria-selected="<?= $mode === 'register' ? 'true' : 'false' ?>">Create account</button>
        <span class="tab-ind"></span>
      </div>

      <?php if ($flash): ?><div class="alert alert-<?= e($flash[0]) ?>" role="status"><?= e($flash[1]) ?></div><?php endif; ?>

      <!-- Log in -->
      <form class="form-panel" data-panel="login" method="post" action="login.php" novalidate>
        <h1>Welcome back</h1>
        <p class="sub">Log in to add recipes and manage your own.</p>
        <?php if ($errors['login']): ?><div class="alert alert-error" role="alert"><?= e($errors['login']) ?></div><?php endif; ?>
        <?= csrf_field() ?><input type="hidden" name="action" value="login">

        <div class="field">
          <input id="login_id" name="login_id" type="text" placeholder=" " autocomplete="username" value="<?= e($old['login_id']) ?>" required>
          <label for="login_id">Username or email</label>
          <small class="err"></small>
        </div>
        <div class="field">
          <input id="login_pw" name="password" type="password" placeholder=" " autocomplete="current-password" required>
          <label for="login_pw">Password</label>
          <button class="eye" type="button" data-toggle-pw aria-label="Show password">Show</button>
          <small class="err"></small>
        </div>
        <button class="btn btn-block" type="submit" data-loading="Logging in…">Log in</button>
        <button class="demo-fill" type="button" data-demo>Use the demo account</button>
        <p class="switch">New here? <button type="button" data-go="register">Create an account</button></p>
      </form>

      <!-- Sign up -->
      <form class="form-panel" data-panel="register" method="post" action="register.php" novalidate>
        <h1>Join the kitchen</h1>
        <p class="sub">Free account. Share recipes and keep them organised.</p>
        <?php if ($errors['register']): ?><div class="alert alert-error" role="alert"><?= e($errors['register']) ?></div><?php endif; ?>
        <?= csrf_field() ?><input type="hidden" name="action" value="register">

        <div class="field">
          <input id="r_user" name="username" type="text" placeholder=" " autocomplete="username" maxlength="20" value="<?= e($old['username']) ?>" required>
          <label for="r_user">Username</label>
          <small class="err"></small>
        </div>
        <div class="field">
          <input id="r_email" name="email" type="email" placeholder=" " autocomplete="email" value="<?= e($old['email']) ?>" required>
          <label for="r_email">Email</label>
          <small class="err"></small>
        </div>
        <div class="field">
          <input id="r_pw" name="password" type="password" placeholder=" " autocomplete="new-password" required>
          <label for="r_pw">Password</label>
          <button class="eye" type="button" data-toggle-pw aria-label="Show password">Show</button>
          <small class="err"></small>
        </div>
        <div class="meter" data-s="0" aria-live="polite"><span></span><span></span><span></span><span></span><em>Use 8+ characters with a letter and a number</em></div>
        <div class="field">
          <input id="r_pw2" name="confirm" type="password" placeholder=" " autocomplete="new-password" required>
          <label for="r_pw2">Confirm password</label>
          <small class="err"></small>
        </div>
        <button class="btn btn-block" type="submit" data-loading="Creating account…">Create account</button>
        <p class="switch">Already cooking with us? <button type="button" data-go="login">Log in</button></p>
      </form>
    </div>
  </section>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>
