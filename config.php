<?php
// Recipe Sharing Portal | shared configuration and helper functions

// Local XAMPP defaults; hosted deployments can provide DB_* variables or
// the TIDB_* variables exposed by Vercel's TiDB Cloud integration.
define('DB_HOST', getenv('DB_HOST') ?: getenv('TIDB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: getenv('TIDB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: getenv('TIDB_DATABASE') ?: 'recipe_portal');
define('DB_USER', getenv('DB_USER') ?: getenv('TIDB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: getenv('TIDB_PASSWORD') ?: '');
const SITE_NAME = 'Recipe Sharing Portal';

function db_connection_options(): array {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    // TiDB Cloud Starter requires TLS. Its public certificates chain to the
    // system CA bundle in the Vercel container.
    if (getenv('TIDB_HOST') || getenv('DB_SSL_CA')) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = getenv('DB_SSL_CA') ?: '/etc/ssl/cert.pem';
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    }

    return $options;
}

function db_dsn(): string {
    return 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
}

// Store sessions in MySQL so users keep their login when Vercel routes requests
// to different or newly started container instances.
final class DatabaseSessionHandler implements SessionHandlerInterface {
    private ?PDO $pdo = null;

    private function connection(): PDO {
        if ($this->pdo === null) {
            $this->pdo = new PDO(
                db_dsn(),
                DB_USER,
                DB_PASS,
                db_connection_options()
            );
        }
        return $this->pdo;
    }

    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { $this->pdo = null; return true; }

    public function read(string $id): string|false {
        try {
            $stmt = $this->connection()->prepare('SELECT session_data FROM app_sessions WHERE session_id = ? AND expires_at > ?');
            $stmt->execute([$id, time()]);
            $data = $stmt->fetchColumn();
            return $data === false ? '' : (string)$data;
        } catch (PDOException $ex) {
            return '';
        }
    }

    public function write(string $id, string $data): bool {
        try {
            $expiry = time() + (int)ini_get('session.gc_maxlifetime');
            $stmt = $this->connection()->prepare(
                'INSERT INTO app_sessions (session_id, session_data, expires_at) VALUES (?, ?, ?) '
                . 'ON DUPLICATE KEY UPDATE session_data = VALUES(session_data), expires_at = VALUES(expires_at)'
            );
            return $stmt->execute([$id, $data, $expiry]);
        } catch (PDOException $ex) {
            return false;
        }
    }

    public function destroy(string $id): bool {
        try {
            $stmt = $this->connection()->prepare('DELETE FROM app_sessions WHERE session_id = ?');
            return $stmt->execute([$id]);
        } catch (PDOException $ex) {
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false {
        try {
            $stmt = $this->connection()->prepare('DELETE FROM app_sessions WHERE expires_at <= ?');
            $stmt->execute([time()]);
            return $stmt->rowCount();
        } catch (PDOException $ex) {
            return false;
        }
    }
}

session_set_save_handler(new DatabaseSessionHandler(), true);
session_set_cookie_params([
    'httponly' => true,
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),
    'samesite' => 'Lax',
]);
session_start();

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                db_dsn(),
                DB_USER, DB_PASS, db_connection_options()
            );
        } catch (PDOException $ex) {
            http_response_code(500);
            echo '<body style="font-family:sans-serif;max-width:560px;margin:15vh auto;padding:0 20px">'
               . '<h1>Database not found</h1><p>Start Apache and MySQL in XAMPP, then import <code>schema.sql</code> '
               . 'in phpMyAdmin. For a hosted deployment, configure the database environment variables.</p></body>';
            exit;
        }
    }
    return $pdo;
}

function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function logged_in(): bool { return !empty($_SESSION['user_id']); }

function flash(string $type, string $message): void { $_SESSION['flash'] = [$type, $message]; }

function require_login(): void {
    if (!logged_in()) {
        flash('info', 'Log in to continue.');
        $_SESSION['after_login'] = basename($_SERVER['SCRIPT_NAME']) . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
        header('Location: login.php');
        exit;
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('This form expired. Go back, refresh the page and try again.');
    }
}

function total_time(array $r): string {
    $m = (int)$r['prep_time'] + (int)$r['cook_time'];
    if ($m >= 60) { return intdiv($m, 60) . ' hr' . ($m % 60 ? ' ' . ($m % 60) . ' min' : ''); }
    return $m . ' min';
}

function lines(string $text): array {
    return array_values(array_filter(array_map('trim', preg_split('/\R/', $text)), fn($l) => $l !== ''));
}

function recipe_select(): string {
    return 'SELECT r.*, c.name AS category, c.emoji AS cat_emoji, u.username
            FROM recipes r
            JOIN categories c ON c.category_id = r.category_id
            JOIN users u ON u.user_id = r.user_id';
}

function safe_image(?string $url): string {
    $url = trim((string)$url);
    return preg_match('#^https?://#i', $url) ? $url : '';
}

function recipe_card(array $r): string {
    $img = safe_image($r['image_url'] ?? '');
    $photo = $img ? '<img src="' . e($img) . '" alt="" loading="lazy" onerror="this.remove()">' : '';
    return '<a class="card" href="recipe.php?id=' . (int)$r['recipe_id'] . '">'
         . '<div class="card-art" style="--c:' . e($r['color']) . '"><span class="plate">' . e($r['emoji']) . '</span>' . $photo
         . '<span class="card-tag">' . e($r['category']) . '</span></div>'
         . '<div class="card-body"><h3>' . e($r['title']) . '</h3>'
         . '<p>' . e($r['description']) . '</p>'
         . '<div class="card-meta"><span>Prep ' . (int)$r['prep_time'] . ' min</span>'
         . '<span>Cook ' . (int)$r['cook_time'] . ' min</span>'
         . '<span>Serves ' . (int)$r['servings'] . '</span></div></div></a>';
}

function categories(): array {
    return db()->query('SELECT c.*, COUNT(r.recipe_id) AS total
                        FROM categories c LEFT JOIN recipes r ON r.category_id = c.category_id
                        GROUP BY c.category_id ORDER BY c.category_id')->fetchAll();
}
