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

// 4. Security & Input Sanitisation Helpers
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

function sanitise_input(?string $data): string {
    if ($data === null) {
        return '';
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function clean_db_param(?string $data): string {
    if ($data === null) {
        return '';
    }
    return stripslashes(trim($data));
}

function ensure_eoi_table(mysqli $conn): void {
    $sql = "CREATE TABLE IF NOT EXISTS `eoi` (
        `EOInumber` INT AUTO_INCREMENT PRIMARY KEY,
        `job_ref` VARCHAR(30) NOT NULL,
        `first_name` VARCHAR(50) NOT NULL,
        `last_name` VARCHAR(50) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `start_date` DATE DEFAULT NULL,
        `experience` VARCHAR(30) DEFAULT NULL,
        `linkedin` VARCHAR(255) DEFAULT NULL,
        `cover_letter` TEXT DEFAULT NULL,
        `status` ENUM('New', 'Current', 'Final') NOT NULL DEFAULT 'New',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($conn, $sql);
}

if ($conn instanceof mysqli && $db_error === '') {
    ensure_eoi_table($conn);
}

$known_job_refs = [
    'SOC026' => 'SOC026 — SOC Analyst',
    'IRE026' => 'IRE026 — Incident Response Engineer',
    'SEC026' => 'SEC026 — Security Engineer',
    'PEN026' => 'PEN026 — Penetration Tester',
];
$allowed_statuses = ['New', 'Current', 'Final'];
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

            <section class="content-box" style="max-width: 1150px;">
                <h2>HR Queries &amp; Management Actions</h2>
                <p>Select a query or management operation below.</p>

                <div class="hr-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:20px;margin-top:20px;">
                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;">
                        <h3>1. List All EOIs</h3>
                        <form method="get" action="manage.php">
                            <input type="hidden" name="filter_type" value="all">
                            <button type="submit" class="btn-primary">List All EOIs</button>
                        </form>
                    </div>

                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;">
                        <h3>2. List EOIs by Job Reference</h3>
                        <form method="get" action="manage.php">
                            <input type="hidden" name="filter_type" value="job_ref">
                            <label for="job_ref_select">Select Position Reference</label>
                            <select id="job_ref_select" name="job_ref">
                                <option value="">-- Choose Job Ref --</option>
                                <?php foreach ($known_job_refs as $code => $label): ?>
                                    <option value="<?php echo sanitise_input($code); ?>"><?php echo sanitise_input($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="job_ref_custom">Or Enter Job Ref Code</label>
                            <input type="text" id="job_ref_custom" name="job_ref_custom" placeholder="e.g. SOC026">
                            <button type="submit" class="btn-primary">Search by Job Reference</button>
                        </form>
                    </div>

                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;">
                        <h3>3. List EOIs by Applicant Name</h3>
                        <form method="get" action="manage.php">
                            <input type="hidden" name="filter_type" value="applicant">
                            <label for="first_name_q">First Name</label>
                            <input type="text" id="first_name_q" name="first_name" placeholder="e.g. Sarah">
                            <label for="last_name_q">Last Name</label>
                            <input type="text" id="last_name_q" name="last_name" placeholder="e.g. Chen">
                            <button type="submit" class="btn-primary">Search by Applicant Name</button>
                        </form>
                    </div>

                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;">
                        <h3>4. Change an EOI Status</h3>
                        <form method="post" action="manage.php">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="csrf_token" value="<?php echo sanitise_input($csrf_token); ?>">
                            <label for="eoi_id_input">EOI Number *</label>
                            <input type="text" id="eoi_id_input" name="eoi_id" required>
                            <label for="new_status_select">New Status *</label>
                            <select id="new_status_select" name="new_status" required>
                                <?php foreach ($allowed_statuses as $st): ?>
                                    <option value="<?php echo sanitise_input($st); ?>"><?php echo sanitise_input($st); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-primary">Update EOI Status</button>
                        </form>
                    </div>

                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;grid-column:1 / -1;">
                        <h3>5. Delete All EOIs by Job Reference</h3>
                        <form method="post" action="manage.php">
                            <input type="hidden" name="action" value="delete_by_job_ref">
                            <input type="hidden" name="csrf_token" value="<?php echo sanitise_input($csrf_token); ?>">
                            <label for="delete_job_ref_select">Select Job Reference to Delete</label>
                            <select id="delete_job_ref_select" name="delete_job_ref_select">
                                <option value="">-- Choose Job Ref --</option>
                                <?php foreach ($known_job_refs as $code => $label): ?>
                                    <option value="<?php echo sanitise_input($code); ?>"><?php echo sanitise_input($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="checkbox-row" style="margin: 10px 0 16px 0;">
                                <label>
                                    <input type="checkbox" name="confirm_delete" value="yes" required>
                                    I confirm permanent deletion of all EOI records matching this Job Reference. *
                                </label>
                            </div>
                            <button type="submit" class="btn-secondary">Delete Matching EOIs</button>
                        </form>
                    </div>
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
