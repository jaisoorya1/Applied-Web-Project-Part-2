<?php
// Redirect visitors who open this page without submitting the form.

error_reporting(E_ALL);
ini_set('display_errors', '1');
// Redirect visitors who open this page without submitting the form.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: apply.php');
    exit;
}
//Read a text field safely and clean surrounding whitespace.
function clean_input($value)
{
    if (!is_string($value)) {
        return '';
    }

    return trim(stripslashes($value));
}

// Escape text when displaying it in HTML.
function escape_output($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$errors = [];

$position = clean_input($_POST['positions'] ?? '');
$job_ref = clean_input($_POST['jobRef'] ?? '');
$first_name = clean_input($_POST['firstName'] ?? '');
$last_name = clean_input($_POST['lastName'] ?? '');

// Check the reference format.
if (!preg_match('/^[A-Za-z0-9]{6}$/D', $job_ref)) {
    $errors[] = 'Job reference must contain exactly 6 letters or numbers.';
}

// Check that the two position fields agree.
if ($position === '' || $position !== $job_ref) {
    $errors[] = 'Select a position and enter its matching job reference.';
}

// Check both names independently.
if (!preg_match('/^[A-Za-z]{1,20}$/D', $first_name)) {
    $errors[] = 'First name must contain 1 to 20 letters only.';
}

if (!preg_match('/^[A-Za-z]{1,20}$/D', $last_name)) {
    $errors[] = 'Last name must contain 1 to 20 letters only.';
}

if (!empty($errors)) {
    http_response_code(422);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BlackVaultCyber | Application Processing</title>

    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php require __DIR__ . '/header.inc'; ?>

    <main class="content-box">
    <h2>Application Processing</h2>

    <?php if (!empty($errors)): ?>
        <p>Please correct these problems:</p>

        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo escape_output($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Job reference and name checks passed.</p>
        <p>Your application has not been saved yet.</p>
    <?php endif; ?>

    <p>
        Use your browser's Back button to edit your submitted details.
    </p>

    <a href="apply.php">Open application form</a>
</main>

    <<?php require __DIR__ . '/footer.inc'; ?>
</body>
</html>