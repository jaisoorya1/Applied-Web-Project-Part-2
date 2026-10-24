<?php
// 1. Block direct access and empty POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    header('Location: apply.php');
    exit;
}

// Avoid caching application responses.
header('Cache-Control: no-store');

// 2. Helpers for cleaning input and safely displaying output.
function clean_input($value)
{
    if (!is_string($value)) {
        return '';
    }

    return trim(stripslashes($value));
}

function escape_output($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// 3. Read the form fields.
// These names must match the name attributes in apply.php.
$position = clean_input($_POST['positions'] ?? '');
$job_ref = clean_input($_POST['jobRef'] ?? '');
$first_name = clean_input($_POST['firstName'] ?? '');
$last_name = clean_input($_POST['lastName'] ?? '');
$dob = clean_input($_POST['dob'] ?? '');
$gender = clean_input($_POST['gender'] ?? '');
$street = clean_input($_POST['street'] ?? '');
$suburb = clean_input($_POST['suburb'] ?? '');
$state = clean_input($_POST['state'] ?? '');
$postcode = clean_input($_POST['postcode'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$phone = clean_input($_POST['phone'] ?? '');
$other_skills = clean_input($_POST['otherSkills'] ?? '');

$errors = [];
$eoi_number = null;
$database_error = false;
$dob_database = null;

// Reject array input for fields that should contain one text value.
$text_fields = [
    'positions', 'jobRef', 'firstName', 'lastName',
    'dob', 'gender', 'street', 'suburb', 'state',
    'postcode', 'email', 'phone', 'otherSkills'
];

foreach ($text_fields as $field) {
    if (isset($_POST[$field]) && !is_string($_POST[$field])) {
        $errors[] = 'Invalid form data. Please use the application form.';
        break;
    }
}

// 4. Validate the position and job reference.
if (!preg_match('/^[A-Za-z0-9]{6}$/D', $job_ref)) {
    $errors[] = 'Job reference must contain exactly 6 letters or numbers.';
}

if ($position === '' || $position !== $job_ref) {
    $errors[] = 'Select a position and enter its matching job reference.';
}

// 5. Validate names.
if (!preg_match('/^[A-Za-z]{1,20}$/D', $first_name)) {
    $errors[] = 'First name must contain 1 to 20 letters only.';
}

if (!preg_match('/^[A-Za-z]{1,20}$/D', $last_name)) {
    $errors[] = 'Last name must contain 1 to 20 letters only.';
}

// 6. Validate the date format and actual calendar date.
if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/D', $dob, $parts)) {
    $day = (int) $parts[1];
    $month = (int) $parts[2];
    $year = (int) $parts[3];

    if ($year < 1000 || !checkdate($month, $day, $year)) {
        $errors[] = 'Enter a real date of birth.';
    } else {
        // MySQL DATE fields use yyyy-mm-dd.
        $dob_database = sprintf('%04d-%02d-%02d', $year, $month, $day);

        $today = new DateTimeImmutable(
            'today',
            new DateTimeZone('Australia/Melbourne')
        );

        if ($dob_database > $today->format('Y-m-d')) {
            $errors[] = 'Date of birth cannot be in the future.';
        }
    }
} else {
    $errors[] = 'Date of birth must use dd/mm/yyyy.';
}

// 7. Validate gender.
$allowed_genders = [
    'female',
    'male',
    'other',
    'prefer-not-to-say'
];

if (!in_array($gender, $allowed_genders, true)) {
    $errors[] = 'Select a valid gender option.';
}

// 8. Validate address fields.
// The u modifier supports UTF-8 characters.
if (!preg_match('/^.{1,40}$/usD', $street)) {
    $errors[] = 'Street address is required and must not exceed 40 characters.';
}

if (!preg_match('/^.{1,40}$/usD', $suburb)) {
    $errors[] = 'Suburb/town is required and must not exceed 40 characters.';
}

$allowed_states = [
    'VIC', 'NSW', 'QLD', 'NT',
    'WA', 'SA', 'TAS', 'ACT'
];

if (!in_array($state, $allowed_states, true)) {
    $errors[] = 'Select a valid Australian state or territory.';
}

if (!preg_match('/^[0-9]{4}$/D', $postcode)) {
    $errors[] = 'Postcode must contain exactly 4 digits.';
}

// 9. Validate contact details.
if (
    strlen($email) > 100 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    $errors[] = 'Enter a valid email address of no more than 100 characters.';
}

if (!preg_match('/^[0-9]{8,12}$/D', $phone)) {
    $errors[] = 'Phone number must contain 8 to 12 digits without spaces.';
}

// 10. Validate the checkbox selections.
$allowed_skills = [
    'network-security',
    'incident-response',
    'penetration-testing',
    'siem',
    'cloud-security',
    'scripting'
];

$submitted_skills = $_POST['skills'] ?? [];
$skills = [];

if (!is_array($submitted_skills) || empty($submitted_skills)) {
    $errors[] = 'Select at least one technical skill.';
} else {
    foreach ($submitted_skills as $skill) {
        if (
            !is_string($skill) ||
            !in_array($skill, $allowed_skills, true)
        ) {
            $errors[] = 'An invalid technical skill was submitted.';
            break;
        }

        $skills[] = $skill;
    }
}

$skills = array_unique($skills);
$skills_database = implode(', ', $skills);

// Other skills are optional. Enforce the TEXT column's byte limit.
if (strlen($other_skills) > 65535) {
    $errors[] = 'The other skills entry is too long. Please shorten it.';
}

// 11. Only access the database after input validation passes.
if (empty($errors)) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        // settings.php already creates $conn.
        require_once __DIR__ . '/settings.php';
        $conn->set_charset('utf8mb4');

        // Create the complete EOI table if it does not exist.
        // Optional older columns are kept for manage.php compatibility.
        $conn->query("
            CREATE TABLE IF NOT EXISTS eoi (
                EOInumber INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                job_ref VARCHAR(30) NOT NULL,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                start_date DATE DEFAULT NULL,
                experience VARCHAR(30) DEFAULT NULL,
                linkedin VARCHAR(255) DEFAULT NULL,
                cover_letter TEXT DEFAULT NULL,
                status ENUM('New', 'Current', 'Final')
                    NOT NULL DEFAULT 'New',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                dob DATE DEFAULT NULL,
                gender VARCHAR(20) DEFAULT NULL,
                street VARCHAR(40) DEFAULT NULL,
                suburb VARCHAR(40) DEFAULT NULL,
                state VARCHAR(3) DEFAULT NULL,
                postcode CHAR(4) DEFAULT NULL,
                skills TEXT DEFAULT NULL,
                other_skills TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Confirm that the submitted reference belongs to an advertised job.
        $job_statement = $conn->prepare(
            'SELECT id FROM jobs WHERE ref_code = ? LIMIT 1'
        );

        $job_statement->bind_param('s', $job_ref);
        $job_statement->execute();
        $job_statement->store_result();

        $job_exists = $job_statement->num_rows > 0;
        $job_statement->close();

        if (!$job_exists) {
            $errors[] = 'That job reference is not currently advertised.';
        } else {
            // 12. Save the validated application with a prepared statement.
            // Status is omitted so the database applies its New default.
            $statement = $conn->prepare("
                INSERT INTO eoi (
                    job_ref,
                    first_name,
                    last_name,
                    email,
                    phone,
                    dob,
                    gender,
                    street,
                    suburb,
                    state,
                    postcode,
                    skills,
                    other_skills
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            // There are 13 string values.
            $parameter_types = str_repeat('s', 13);

            $statement->bind_param(
                $parameter_types,
                $job_ref,
                $first_name,
                $last_name,
                $email,
                $phone,
                $dob_database,
                $gender,
                $street,
                $suburb,
                $state,
                $postcode,
                $skills_database,
                $other_skills
            );

            $statement->execute();

            // Read the number generated by this successful INSERT.
            $eoi_number = $conn->insert_id;

            $statement->close();
        }

        $conn->close();
    } catch (mysqli_sql_exception $error) {
        // Keep technical details in the server log.
        error_log('EOI processing failed: ' . $error->getMessage());

        $database_error = true;
        $errors[] = 'We could not save your application. Please try again later.';
    }
}

// Set the response status before outputting HTML.
if ($database_error) {
    http_response_code(500);
} elseif (!empty($errors)) {
    http_response_code(422);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BlackVaultCyber | Application Result</title>

    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
    <?php require __DIR__ . '/header.inc'; ?>

    <main class="content-box">
        <?php if ($eoi_number !== null): ?>

            <h2>Application Submitted Successfully</h2>

            <p>
                Thank you,
                <?php echo escape_output($first_name); ?>.
                Your application has been saved.
            </p>

            <p>
                <strong>EOI Number:</strong>
                <?php echo escape_output($eoi_number); ?>
            </p>

            <p>
                <strong>Job Reference:</strong>
                <?php echo escape_output($job_ref); ?>
            </p>

            <p><strong>Status:</strong> New</p>

            <p>Please keep your EOI number for future reference.</p>

            <p><a href="jobs.php">Return to job listings</a></p>

        <?php else: ?>

            <h2>Application Not Submitted</h2>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo escape_output($error); ?></li>
                <?php endforeach; ?>
            </ul>

            <p>
                Use your browser's Back button to return to your
                entered details and correct any problems.
            </p>

            <p><a href="apply.php">Open application form</a></p>

        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/footer.inc'; ?>
</body>
</html>