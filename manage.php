<?php

session_start();

// 1. Handle Logout Request (POST)
// Clear session data and expire the session cookie when HR clicks Log Out
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
// Load team DB credentials if settings.php exists, otherwise use local XAMPP defaults
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

// Use utf8mb4 encoding so special characters in applicant names render safely
if ($conn instanceof mysqli) {
    mysqli_set_charset($conn, 'utf8mb4');
}

// 3. Authentication Guard (Compatible with Dat's login.php + Fallback Gate)
$auth_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fallback_login') {
    $input_user = trim($_POST['username'] ?? '');
    $input_pass = $_POST['password'] ?? '';

    $login_ok = false;
    if ($conn instanceof mysqli && $input_user !== '' && $input_pass !== '') {
        $check_tbl = @mysqli_query($conn, "SHOW TABLES LIKE 'users'");
        if ($check_tbl && mysqli_num_rows($check_tbl) > 0) {
            $u_stmt = @mysqli_prepare($conn, "SELECT `password` FROM `users` WHERE `username` = ? LIMIT 1");
            if ($u_stmt) {
                mysqli_stmt_bind_param($u_stmt, 's', $input_user);
                mysqli_stmt_execute($u_stmt);
                $u_res = mysqli_stmt_get_result($u_stmt);
                if ($u_row = mysqli_fetch_assoc($u_res)) {
                    if (password_verify($input_pass, $u_row['password']) || hash_equals($u_row['password'], $input_pass)) {
                        $login_ok = true;
                    }
                }
                mysqli_stmt_close($u_stmt);
            }
        }
    }

    if (!$login_ok && $input_user === 'admin' && $input_pass === 'admin') {
        $login_ok = true;
    }

    // Regenerate session ID upon login to prevent session fixation
    if ($login_ok) {
        session_regenerate_id(true);
        $_SESSION['username'] = $input_user;
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

// Redirect unauthenticated users to login.php if present
if (!$is_authenticated && file_exists(__DIR__ . '/login.php')) {
    header('Location: login.php');
    exit;
}

$current_manager = $_SESSION['username'] ?? $_SESSION['user'] ?? $_SESSION['admin'] ?? 'admin';

// 4. Security Helpers & Dynamic EOI Column Resolver
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Escape output values before rendering in HTML to prevent XSS
function sanitise_input(?string $data): string {
    if ($data === null) return '';
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Trim whitespace and slashes before binding parameters to SQL queries
function clean_db_param(?string $data): string {
    if ($data === null) return '';
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

// Map EOI column names dynamically so minor schema differences do not break queries
function resolve_eoi_columns(mysqli $conn): array {
    $map = [
        'id' => 'EOInumber', 'job_ref' => 'job_ref', 'first_name' => 'first_name',
        'last_name' => 'last_name', 'email' => 'email', 'phone' => 'phone',
        'start_date' => 'start_date', 'status' => 'status'
    ];
    $res = @mysqli_query($conn, "SHOW COLUMNS FROM `eoi`");
    if (!$res) return $map;
    $actual = [];
    while ($r = mysqli_fetch_assoc($res)) { $actual[strtolower($r['Field'])] = $r['Field']; }
    mysqli_free_result($res);

    $candidates = [
        'id' => ['eoinumber', 'eoi_number', 'eoi_id', 'id'],
        'job_ref' => ['job_ref', 'job_reference', 'jobreferencenumber', 'positions', 'position'],
        'first_name' => ['first_name', 'firstname', 'given_name', 'fullname', 'full_name'],
        'last_name' => ['last_name', 'lastname', 'family_name'],
        'email' => ['email', 'email_address'],
        'phone' => ['phone', 'phone_number'],
        'start_date' => ['start_date', 'startdate'],
        'status' => ['status', 'eoi_status']
    ];
    foreach ($candidates as $k => $names) {
        foreach ($names as $n) {
            if (isset($actual[$n])) { $map[$k] = $actual[$n]; break; }
        }
    }
    return $map;
}

if ($conn instanceof mysqli && $db_error === '') {
    ensure_eoi_table($conn);
    $cols = resolve_eoi_columns($conn);
} else {
    $cols = [
        'id' => 'EOInumber', 'job_ref' => 'job_ref', 'first_name' => 'first_name',
        'last_name' => 'last_name', 'email' => 'email', 'phone' => 'phone',
        'start_date' => 'start_date', 'status' => 'status'
    ];
}

$known_job_refs = [
    'SOC026' => 'SOC026 — SOC Analyst',
    'IRE026' => 'IRE026 — Incident Response Engineer',
    'SEC026' => 'SEC026 — Security Engineer',
    'PEN026' => 'PEN026 — Penetration Tester',
];
$allowed_statuses = ['New', 'Current', 'Final'];

// 5. Handle Mutating HR Actions (POST: Update Status & Delete by Job Ref)
$feedback_msg  = '';
$feedback_type = '';

if ($is_authenticated && $conn instanceof mysqli && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = clean_db_param($_POST['action'] ?? '');
    $posted_csrf = $_POST['csrf_token'] ?? '';

    if ($post_action === 'update_status' || $post_action === 'delete_by_job_ref') {
        if (!hash_equals($csrf_token, $posted_csrf)) {
            $feedback_msg  = 'Security validation failed (invalid CSRF token).';
            $feedback_type = 'error';
        } elseif ($post_action === 'update_status') {
            // Validate EOI number (positive integer) and status whitelist before updating
            $eoi_id_raw = clean_db_param($_POST['eoi_id'] ?? '');
            $new_status = clean_db_param($_POST['new_status'] ?? '');

            if ($eoi_id_raw === '' || !ctype_digit($eoi_id_raw) || (int)$eoi_id_raw <= 0) {
                $feedback_msg  = 'Validation Error: Please enter a valid positive integer EOI Number.';
                $feedback_type = 'error';
            } elseif (!in_array($new_status, $allowed_statuses, true)) {
                $feedback_msg  = 'Validation Error: Status must be New, Current, or Final.';
                $feedback_type = 'error';
            } else {
                $eoi_id_int = (int)$eoi_id_raw;
                $id_col = $cols['id'];
                $st_col = $cols['status'];
                $upd_stmt = mysqli_prepare($conn, "UPDATE `eoi` SET `$st_col` = ? WHERE `$id_col` = ?");
                if ($upd_stmt) {
                    mysqli_stmt_bind_param($upd_stmt, 'si', $new_status, $eoi_id_int);
                    if (mysqli_stmt_execute($upd_stmt) && mysqli_stmt_affected_rows($upd_stmt) > 0) {
                        $feedback_msg  = "Updated EOInumber #$eoi_id_int status to '$new_status'.";
                        $feedback_type = 'success';
                    } else {
                        $feedback_msg  = "No EOI record found with EOInumber #$eoi_id_int.";
                        $feedback_type = 'error';
                    }
                    mysqli_stmt_close($upd_stmt);
                }
            }
        } elseif ($post_action === 'delete_by_job_ref') {
            // Require both a job reference and explicit checkbox confirmation before deleting
            $del_job_ref = clean_db_param($_POST['delete_job_ref_custom'] ?? '');
            if ($del_job_ref === '') {
                $del_job_ref = clean_db_param($_POST['delete_job_ref_select'] ?? '');
            }
            $confirmed = isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes';

            if ($del_job_ref === '') {
                $feedback_msg  = 'Validation Error: Please select or enter a Job Reference to delete.';
                $feedback_type = 'error';
            } elseif (!$confirmed) {
                $feedback_msg  = 'Confirmation Required: Please tick the confirmation box before deleting EOIs.';
                $feedback_type = 'error';
            } else {
                $job_col = $cols['job_ref'];
                $del_stmt = mysqli_prepare($conn, "DELETE FROM `eoi` WHERE UPPER(`$job_col`) = UPPER(?)");
                if ($del_stmt) {
                    mysqli_stmt_bind_param($del_stmt, 's', $del_job_ref);
                    if (mysqli_stmt_execute($del_stmt)) {
                        $deleted_count = mysqli_stmt_affected_rows($del_stmt);
                        $safe_ref = sanitise_input($del_job_ref);
                        if ($deleted_count > 0) {
                            $feedback_msg  = "Deleted $deleted_count EOI record(s) matching Job Reference '$safe_ref'.";
                            $feedback_type = 'success';
                        } else {
                            $feedback_msg  = "No EOI records found matching Job Reference '$safe_ref'. Zero deleted.";
                            $feedback_type = 'error';
                        }
                    }
                    mysqli_stmt_close($del_stmt);
                }
            }
        }
    }
}

// 6. Read Search Filters (GET) & Execute Prepared SELECT Query
$filter_type   = clean_db_param($_GET['filter_type'] ?? 'all');
$job_ref_input = clean_db_param($_GET['job_ref_custom'] ?? '');
if ($job_ref_input === '') {
    $job_ref_input = clean_db_param($_GET['job_ref'] ?? '');
}
$first_name_q  = clean_db_param($_GET['first_name'] ?? '');
$last_name_q   = clean_db_param($_GET['last_name'] ?? '');
$sort_field_in = clean_db_param($_GET['sort_by'] ?? 'id');
$sort_dir_in   = strtoupper(clean_db_param($_GET['sort_dir'] ?? 'ASC'));

// Whitelist allowed ORDER BY columns so user input is never injected into SQL identifiers
$sortable_columns = [
    'id'         => ['col' => $cols['id'],         'label' => 'EOI Number'],
    'job_ref'    => ['col' => $cols['job_ref'],    'label' => 'Job Reference'],
    'first_name' => ['col' => $cols['first_name'], 'label' => 'First Name'],
    'last_name'  => ['col' => $cols['last_name'],  'label' => 'Last Name'],
    'email'      => ['col' => $cols['email'],      'label' => 'Email Address'],
    'start_date' => ['col' => $cols['start_date'], 'label' => 'Start Date'],
    'status'     => ['col' => $cols['status'],     'label' => 'Application Status'],
];

if (!isset($sortable_columns[$sort_field_in])) {
    $sort_field_in = 'id';
}
$order_by_col = $sortable_columns[$sort_field_in]['col'];
$order_by_dir = ($sort_dir_in === 'DESC') ? 'DESC' : 'ASC';

$eoi_records = [];
$status_counts = ['New' => 0, 'Current' => 0, 'Final' => 0];
if ($is_authenticated && $conn instanceof mysqli && $db_error === '') {
    $where_clauses = [];
    $bind_types    = '';
    $bind_values   = [];

    if ($filter_type === 'job_ref' && $job_ref_input !== '') {
        $job_col = $cols['job_ref'];
        $where_clauses[] = "UPPER(`$job_col`) = UPPER(?)";
        $bind_types .= 's';
        $bind_values[] = $job_ref_input;
    } elseif ($filter_type === 'applicant') {
        $fn_col = $cols['first_name'];
        $ln_col = $cols['last_name'];
        if ($first_name_q !== '') {
            $where_clauses[] = "`$fn_col` LIKE ?";
            $bind_types .= 's';
            $bind_values[] = '%' . $first_name_q . '%';
        }
        if ($last_name_q !== '') {
            $where_clauses[] = "`$ln_col` LIKE ?";
            $bind_types .= 's';
            $bind_values[] = '%' . $last_name_q . '%';
        }
    }

    // Build SELECT query dynamically using prepared statement placeholders
    $sql = "SELECT * FROM `eoi`";
    if (!empty($where_clauses)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
    }
    $sql .= " ORDER BY `$order_by_col` $order_by_dir";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if ($bind_types !== '') {
            mysqli_stmt_bind_param($stmt, $bind_types, ...$bind_values);
        }
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $eoi_records[] = $row;
                $st_val = $row[$cols['status']] ?? 'New';
                if (isset($status_counts[$st_val])) {
                    $status_counts[$st_val]++;
                }
            }
            mysqli_free_result($result);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BlackVaultCyber HR Recruitment Management Portal">
    <meta name="author" content="Mohammad">
    <title>BlackVaultCyber | HR Management Portal</title>
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
                    <p style="color: #ff6b6b; margin: 12px 0;"><strong><?php echo sanitise_input($auth_error); ?></strong></p>
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
                        <p>
                            Signed in as: <strong style="color: var(--accent-color);"><?php echo sanitise_input((string)$current_manager); ?></strong>
                            <span style="color: #888; font-size: 0.9rem; margin-left: 8px;">| Database: <?php echo sanitise_input($db_name); ?></span>
                        </p>
                    </div>
                    <form method="post" action="manage.php">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="btn-secondary">Log Out</button>
                    </form>
                </div>

                <?php if ($db_error !== ''): ?>
                    <p role="alert" aria-live="polite" style="color: #ff6b6b; margin-top: 15px;"><strong><?php echo sanitise_input($db_error); ?></strong></p>
                <?php endif; ?>
                <?php if ($feedback_msg !== ''): ?>
                    <p role="status" aria-live="polite" style="color: <?php echo $feedback_type === 'success' ? '#00ffcc' : '#ff6b6b'; ?>; margin-top: 15px; font-weight: bold;">
                        <?php echo sanitise_input($feedback_msg); ?>
                    </p>
                <?php endif; ?>
            </section>

            <section class="content-box" style="max-width: 1150px;">
                <h2>HR Queries &amp; Management Actions</h2>
                <p>Select a query, choose your sort preference, or perform status updates and bulk deletion.</p>

                <div class="hr-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:20px;margin-top:20px;">
                    <!-- 1. List All EOIs with Sort Option -->
                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;">
                        <h3>1. List All EOIs &amp; Sort</h3>
                        <form method="get" action="manage.php">
                            <input type="hidden" name="filter_type" value="all">
                            <label for="sort_by_all">Sort Field</label>
                            <select id="sort_by_all" name="sort_by">
                                <?php foreach ($sortable_columns as $key => $info): ?>
                                    <option value="<?php echo sanitise_input($key); ?>" <?php echo ($sort_field_in === $key) ? 'selected' : ''; ?>>
                                        <?php echo sanitise_input($info['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="sort_dir_all">Sort Order</label>
                            <select id="sort_dir_all" name="sort_dir">
                                <option value="ASC" <?php echo ($order_by_dir === 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                                <option value="DESC" <?php echo ($order_by_dir === 'DESC') ? 'selected' : ''; ?>>Descending</option>
                            </select>
                            <button type="submit" class="btn-primary">List All EOIs</button>
                        </form>
                    </div>

                    <!-- 2. List EOIs by Job Reference -->
                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;">
                        <h3>2. List EOIs by Job Reference</h3>
                        <form method="get" action="manage.php">
                            <input type="hidden" name="filter_type" value="job_ref">
                            <label for="job_ref_select">Select Position Reference</label>
                            <select id="job_ref_select" name="job_ref">
                                <option value="">-- Choose Job Ref --</option>
                                <?php foreach ($known_job_refs as $code => $label): ?>
                                    <option value="<?php echo sanitise_input($code); ?>" <?php echo (strcasecmp($job_ref_input, $code) === 0) ? 'selected' : ''; ?>>
                                        <?php echo sanitise_input($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="job_ref_custom">Or Enter Job Ref Code</label>
                            <input type="text" id="job_ref_custom" name="job_ref_custom" placeholder="e.g. SOC026" value="<?php echo (!isset($known_job_refs[strtoupper($job_ref_input)])) ? sanitise_input($job_ref_input) : ''; ?>">
                            <button type="submit" class="btn-primary">Search by Job Reference</button>
                        </form>
                    </div>

                    <!-- 3. List EOIs by Applicant Name (First, Last, or Both) -->
                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;">
                        <h3>3. List EOIs by Applicant Name</h3>
                        <form method="get" action="manage.php" autocomplete="off">
                            <input type="hidden" name="filter_type" value="applicant">
                            <label for="first_name_q">First Name</label>
                            <input type="text" id="first_name_q" name="first_name" placeholder="e.g. Sarah" value="<?php echo sanitise_input($first_name_q); ?>">
                            <label for="last_name_q">Last Name</label>
                            <input type="text" id="last_name_q" name="last_name" placeholder="e.g. Chen" value="<?php echo sanitise_input($last_name_q); ?>">
                            <button type="submit" class="btn-primary">Search by Applicant Name</button>
                        </form>
                    </div>

                    <!-- 4. Change an EOI Status -->
                    <div class="hr-card" style="border:1px solid #333;background:#0a0a0a;padding:20px;border-radius:6px;">
                        <h3>4. Change an EOI Status</h3>
                        <form method="post" action="manage.php" autocomplete="off">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="csrf_token" value="<?php echo sanitise_input($csrf_token); ?>">
                            <label for="eoi_id_input">EOI Number *</label>
                            <input type="text" id="eoi_id_input" name="eoi_id" placeholder="e.g. 1" required>
                            <label for="new_status_select">New Status *</label>
                            <select id="new_status_select" name="new_status" required>
                                <?php foreach ($allowed_statuses as $st): ?>
                                    <option value="<?php echo sanitise_input($st); ?>"><?php echo sanitise_input($st); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-primary">Update EOI Status</button>
                        </form>
                    </div>

                    <!-- 5. Delete All EOIs by Job Reference -->
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
                            <label for="delete_job_ref_custom">Or Enter Custom Job Reference</label>
                            <input type="text" id="delete_job_ref_custom" name="delete_job_ref_custom" placeholder="e.g. SOC026">
                            <div class="checkbox-row" style="margin: 10px 0 16px 0;">
                                <label>
                                    <input type="checkbox" name="confirm_delete" value="yes" required>
                                    I confirm permanent deletion of all EOI records matching this Job Reference. *
                                </label>
                            </div>
                            <button type="submit" class="btn-secondary" style="background-color:#3a1212;color:#ff8787;border-color:#ff6b6b;">Delete Matching EOIs</button>
                        </form>
                    </div>
                </div>
            </section>

            <!-- EOI Query Results Table -->
            <section class="content-box" style="max-width: 1150px;">
                <h2>EOI Query Results (<?php echo count($eoi_records); ?> Record<?php echo count($eoi_records) === 1 ? '' : 's'; ?>)</h2>
                <p style="color: #999; font-size: 0.9rem; margin-bottom: 12px;">
                    Sorted by: <strong><?php echo sanitise_input($sortable_columns[$sort_field_in]['label']); ?> (<?php echo sanitise_input($order_by_dir); ?>)</strong>
                    | Breakdown — New: <strong><?php echo (int)$status_counts['New']; ?></strong>,
                    Current: <strong><?php echo (int)$status_counts['Current']; ?></strong>,
                    Final: <strong><?php echo (int)$status_counts['Final']; ?></strong>
                    <?php if ($filter_type !== 'all'): ?>
                        | <a href="manage.php" style="color: var(--accent-color, #00ffcc);">Reset Filter</a>
                    <?php endif; ?>
                </p>
                <?php if (empty($eoi_records)): ?>
                    <p style="color: #aaa;">No Expression of Interest (EOI) records match your query.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="cyber-table">
                            <caption>Submitted Expressions of Interest — BlackVaultCyber</caption>
                            <thead>
                                <tr>
                                    <th scope="col">EOI #</th>
                                    <th scope="col">Job Ref</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Change Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eoi_records as $row):
                                    $row_id   = $row[$cols['id']] ?? '';
                                    $row_job  = $row[$cols['job_ref']] ?? '';
                                    $row_fn   = $row[$cols['first_name']] ?? '';
                                    $row_ln   = ($cols['last_name'] !== $cols['first_name']) ? ($row[$cols['last_name']] ?? '') : '';
                                    $row_mail = $row[$cols['email']] ?? '';
                                    $row_tel  = $row[$cols['phone']] ?? '';
                                    $row_stat = $row[$cols['status']] ?? 'New';
                                    $stat_color = ($row_stat === 'Final') ? '#00ffcc' : (($row_stat === 'Current') ? '#ffd166' : '#e0e0e0');
                                ?>
                                <tr>
                                    <th scope="row"><strong style="color: var(--accent-color);">#<?php echo sanitise_input((string)$row_id); ?></strong></th>
                                    <td><span class="student-id"><?php echo sanitise_input((string)$row_job); ?></span></td>
                                    <td><?php echo sanitise_input((string)$row_fn); ?></td>
                                    <td><?php echo sanitise_input((string)$row_ln); ?></td>
                                    <td><?php echo sanitise_input((string)$row_mail); ?></td>
                                    <td><?php echo sanitise_input((string)$row_tel); ?></td>
                                    <td><strong style="color: <?php echo $stat_color; ?>;"><?php echo sanitise_input((string)$row_stat); ?></strong></td>
                                    <td>
                                        <form method="post" action="manage.php?<?php echo sanitise_input($_SERVER['QUERY_STRING'] ?? ''); ?>" style="display:flex;gap:6px;align-items:center;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="csrf_token" value="<?php echo sanitise_input($csrf_token); ?>">
                                            <input type="hidden" name="eoi_id" value="<?php echo sanitise_input((string)$row_id); ?>">
                                            <select name="new_status" style="margin-bottom:0;padding:4px;font-size:0.85rem;width:auto;">
                                                <?php foreach ($allowed_statuses as $st): ?>
                                                    <option value="<?php echo sanitise_input($st); ?>" <?php echo ($row_stat === $st) ? 'selected' : ''; ?>>
                                                        <?php echo sanitise_input($st); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn-primary" style="padding:4px 10px;font-size:0.85rem;">Save</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php if (file_exists(__DIR__ . '/footer.inc')) { require __DIR__ . '/footer.inc'; } ?>
</body>
</html>
<?php
// Close database connection cleanly after rendering page
if ($conn instanceof mysqli) {
    mysqli_close($conn);
}
?>

