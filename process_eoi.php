<?php
// Redirect visitors who open this page without submitting the form.

error_reporting(E_ALL);
ini_set('display_errors', '1');
// Redirect visitors who open this page without submitting the form.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: apply.php');
    exit;
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

        <p>
            Your form reached the processor.
            Validation and database saving will be added next.
        </p>

        <a href="apply.php">Return to application form</a>
    </main>

    <<?php require __DIR__ . '/footer.inc'; ?>
</body>
</html>