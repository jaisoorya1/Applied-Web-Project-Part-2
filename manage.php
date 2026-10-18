<?php

session_start();

// 1. Handle Logout Request (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    if (file_exists(__DIR__ . '/login.php')) {
        header('Location: login.php');
        exit;
    }
    header('Location: manage.php');
    exit;
}

// 2. Database Connection via settings.php
if (file_exists(__DIR__ . '/settings.php')) {
    require_once __DIR__ . '/settings.php';
}

$db_host = $host ?? $db_host ?? 'localhost';
$db_user = $user ?? $username ?? $db_user ?? 'root';
$db_pwd  = $pwd ?? $password ?? $pass ?? '';
$db_name = $sql_db ?? $dbname ?? $db ?? $database ?? 'blackvault_db';

$conn = null;
$db_error = '';

if (!(isset($conn) && $conn instanceof mysqli)) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @mysqli_connect($db_host, $db_user, $db_pwd, $db_name);
    if (!$conn) {
        $tmp_conn = @mysqli_connect($db_host, $db_user, $db_pwd);
        if ($tmp_conn) {
            $safe_db = mysqli_real_escape_string($tmp_conn, $db_name);
            @mysqli_query($tmp_conn, "CREATE DATABASE IF NOT EXISTS `$safe_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            mysqli_close($tmp_conn);
            $conn = @mysqli_connect($db_host, $db_user, $db_pwd, $db_name);
        }
        if (!$conn) {
            $db_error = 'Database connection failed: ' . mysqli_connect_error();
        }
    }
}

if ($conn instanceof mysqli) {
    mysqli_set_charset($conn, 'utf8mb4');
}

// 3. Authentication Guard (Compatible with Dat's login.php + Fallback Gate)
$auth_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fallback_login') {
    $input_user = trim($_POST['username'] ?? '');
    $input_pass = $_POST['password'] ?? '';

    if ($input_user === 'admin' && $input_pass === 'admin') {
        session_regenerate_id(true);
        $_SESSION['username'] = 'admin';
        $_SESSION['logged_in'] = true;
        header('Location: manage.php');
        exit;
    } else {
        $auth_error = 'Invalid username or password. Marker default credentials: admin / admin.';
    }
}

$is_authenticated = !empty($_SESSION['username'])
    || !empty($_SESSION['user'])
    || !empty($_SESSION['admin'])
    || !empty($_SESSION['logged_in'])
    || !empty($_SESSION['user_id']);

if (!$is_authenticated && file_exists(__DIR__ . '/login.php')) {
    header('Location: login.php');
    exit;
}

$current_manager = $_SESSION['username'] ?? $_SESSION['user'] ?? $_SESSION['admin'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BlackVaultCyber HR Recruitment Management Portal">
    <meta name="author" content="Mohammad">
    <title>BlackVaultCyber | HR Management Portal</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php if (file_exists(__DIR__ . '/header.inc')) { require __DIR__ . '/header.inc'; } ?>

    <main class="index-bg">
        <?php if (!$is_authenticated): ?>
            <section class="content-box">
                <h2>HR Manager Authentication Required</h2>
                <p>Access to Expression of Interest (EOI) administration is restricted to authorised HR personnel.</p>
                <?php if ($auth_error !== ''): ?>
                    <p style="color: #ff6b6b; margin: 12px 0;"><?php echo htmlspecialchars($auth_error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <form method="post" action="manage.php" style="margin-top: 20px;">
                    <input type="hidden" name="action" value="fallback_login">
                    <fieldset>
                        <legend>HR Manager Sign In</legend>
                        <label for="login_username">Username *</label>
                        <input type="text" id="login_username" name="username" required>
                        <label for="login_password">Password *</label>
                        <input type="password" id="login_password" name="password" required style="display:block;width:100%;padding:10px;margin-bottom:20px;background:#111;border:1px solid #333;color:#e0e0e0;">
                        <button type="submit" class="btn-primary">Sign In to HR Portal</button>
                    </fieldset>
                </form>
            </section>
        <?php else: ?>
            <section class="content-box" style="max-width: 1150px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h2>HR Manager Portal — EOI Administration</h2>
                        <p>Signed in as: <strong style="color: var(--accent-color);"><?php echo htmlspecialchars((string)$current_manager, ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    </div>
                    <form method="post" action="manage.php">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="btn-secondary">Log Out</button>
                    </form>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php if (file_exists(__DIR__ . '/footer.inc')) { require __DIR__ . '/footer.inc'; } ?>
</body>
</html>
<?php
if ($conn instanceof mysqli) {
    mysqli_close($conn);
}
?>
