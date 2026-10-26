<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$jobs = [];
$load_error = '';
// Read the job reference supplied by an Apply link.
$selected_ref = is_string($_GET['ref_code'] ?? null)
    ? trim($_GET['ref_code'])
    : '';

try {
    require_once __DIR__ . '/settings.php';
    $conn->set_charset('utf8mb4');

    $result = $conn->query(
        'SELECT ref_code, title FROM jobs ORDER BY id'
    );

    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }

    $result->free();
    $conn->close();
} catch (mysqli_sql_exception $error) {
    error_log('Unable to load application jobs: ' . $error->getMessage());

    http_response_code(500);
    $load_error = 'Unable to load available positions. Please try again later.';
}

function apply_escape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
          content="Apply for a cybersecurity position at BlackVaultCyber">

    <title>BlackVaultCyber | Apply</title>

    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
    <?php require __DIR__ . '/header.inc'; ?>

    <main class="page-wrap">
        <section class="page-intro" aria-labelledby="apply-heading">
            <h2 id="apply-heading">Apply to BlackVaultCyber</h2>

            <p>
                Complete the application form below.
                Fields marked with an asterisk (*) are required.
            </p>
        </section>

        <?php if ($load_error !== ''): ?>

            <p role="alert">
                <?php echo apply_escape($load_error); ?>
            </p>

        <?php elseif (empty($jobs)): ?>

            <p>No positions are currently available.</p>

        <?php else: ?>

            <form class="apply-form"
                  action="process_eoi.php"
                  method="post">
 .
                <!-- POSITION DETAILS -->
                <fieldset>
                    <legend>Position Details</legend>

                    <p>
                        <label for="positions">
                            Position Applying For *
                        </label>

                        <select id="positions" name="positions" required>
                            <option value="">
                                -- Select a position --
                            </option>

                            <?php foreach ($jobs as $job): ?>
                                <option value="<?php
                                    echo apply_escape($job['ref_code']);
                                ?>">
                                    <?php
                                    echo apply_escape(
                                        $job['ref_code'] . ' – ' . $job['title']
                                    );
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="jobRef">Job Reference Number *</label>

                        <input
                            type="text"
                            id="jobRef"
                            name="jobRef"
                            size="6"
                            minlength="6"
                            maxlength="6"
                            pattern="[A-Za-z0-9]{6}"
                            placeholder="SOC026"
                            title="Enter exactly 6 letters or numbers"
                            aria-describedby="job-ref-help"
                            required
                        >
                    </p>

                    <p id="job-ref-help">
                        Enter the exact reference shown beside your selected
                        position. For example, select SOC026 – SOC Analyst
                        and enter SOC026.
                    </p>
                </fieldset>

                <!-- PERSONAL DETAILS -->
                <fieldset>
                    <legend>Personal Details</legend>

                    <p>
                        <label for="firstName">First Name *</label>

                        <input
                            type="text"
                            id="firstName"
                            name="firstName"
                            size="20"
                            maxlength="20"
                            pattern="[A-Za-z]{1,20}"
                            autocomplete="given-name"
                            title="Enter 1 to 20 letters only"
                            required
                        >
                    </p>

                    <p>
                        <label for="lastName">Last Name *</label>

                        <input
                            type="text"
                            id="lastName"
                            name="lastName"
                            size="20"
                            maxlength="20"
                            pattern="[A-Za-z]{1,20}"
                            autocomplete="family-name"
                            title="Enter 1 to 20 letters only"
                            required
                        >
                    </p>

                    <p>
                        <label for="dob">Date of Birth *</label>

                        <input
                            type="text"
                            id="dob"
                            name="dob"
                            size="10"
                            maxlength="10"
                            pattern="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/[0-9]{4}"
                            placeholder="dd/mm/yyyy"
                            title="Enter your date of birth as dd/mm/yyyy"
                            required
                        >
                    </p>

                    <fieldset class="sub-fieldset">
                        <legend>Gender *</legend>

                        <p>
                            <input
                                type="radio"
                                id="gender-female"
                                name="gender"
                                value="female"
                                required
                            >
                            <label for="gender-female">Female</label>
                        </p>

                        <p>
                            <input
                                type="radio"
                                id="gender-male"
                                name="gender"
                                value="male"
                                required
                            >
                            <label for="gender-male">Male</label>
                        </p>

                        <p>
                            <input
                                type="radio"
                                id="gender-other"
                                name="gender"
                                value="other"
                                required
                            >
                            <label for="gender-other">Other</label>
                        </p>

                        <p>
                            <input
                                type="radio"
                                id="gender-private"
                                name="gender"
                                value="prefer-not-to-say"
                                required
                            >
                            <label for="gender-private">
                                Prefer not to say
                            </label>
                        </p>
                    </fieldset>
                </fieldset>

                <!-- CONTACT AND ADDRESS -->
                <fieldset>
                    <legend>Contact and Address</legend>

                    <p>
                        <label for="street">Street Address *</label>

                        <input
                            type="text"
                            id="street"
                            name="street"
                            size="40"
                            maxlength="40"
                            autocomplete="address-line1"
                            required
                        >
                    </p>

                    <p>
                        <label for="suburb">Suburb/Town *</label>

                        <input
                            type="text"
                            id="suburb"
                            name="suburb"
                            size="40"
                            maxlength="40"
                            autocomplete="address-level2"
                            required
                        >
                    </p>

                    <p>
                        <label for="state">State *</label>

                        <select
                            id="state"
                            name="state"
                            autocomplete="address-level1"
                            required
                        >
                            <option value="">-- Select a state --</option>
                            <option value="VIC">VIC</option>
                            <option value="NSW">NSW</option>
                            <option value="QLD">QLD</option>
                            <option value="NT">NT</option>
                            <option value="WA">WA</option>
                            <option value="SA">SA</option>
                            <option value="TAS">TAS</option>
                            <option value="ACT">ACT</option>
                        </select>
                    </p>

                    <p>
                        <label for="postcode">Postcode *</label>

                        <input
                            type="text"
                            id="postcode"
                            name="postcode"
                            size="4"
                            maxlength="4"
                            pattern="[0-9]{4}"
                            inputmode="numeric"
                            autocomplete="postal-code"
                            title="Enter exactly 4 digits"
                            required
                        >
                    </p>

                    <p>
                        <label for="email">Email Address *</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            maxlength="100"
                            pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                            autocomplete="email"
                            placeholder="name@example.com"
                            title="Enter a valid email address, such as name@example.com"
                            required
                        >
                    </p>

                    <p>
                        <label for="phone">Phone Number *</label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            size="12"
                            minlength="8"
                            maxlength="12"
                            pattern="[0-9]{8,12}"
                            inputmode="numeric"
                            autocomplete="tel"
                            title="Enter 8 to 12 digits without spaces"
                            required
                        >
                    </p>
                </fieldset>

                <!-- TECHNICAL SKILLS -->
                <fieldset aria-describedby="skills-help">
                    <legend>Technical Skills *</legend>

                    <p id="skills-help">
                        Select at least one skill.
                    </p>

                    <div class="skills-grid">
                        <p>
                            <input
                                type="checkbox"
                                id="skill-network"
                                name="skills[]"
                                value="network-security"
                            >
                            <label for="skill-network">
                                Network Security
                            </label>
                        </p>

                        <p>
                            <input
                                type="checkbox"
                                id="skill-response"
                                name="skills[]"
                                value="incident-response"
                            >
                            <label for="skill-response">
                                Incident Response
                            </label>
                        </p>

                        <p>
                            <input
                                type="checkbox"
                                id="skill-testing"
                                name="skills[]"
                                value="penetration-testing"
                            >
                            <label for="skill-testing">
                                Penetration Testing
                            </label>
                        </p>

                        <p>
                            <input
                                type="checkbox"
                                id="skill-siem"
                                name="skills[]"
                                value="siem"
                            >
                            <label for="skill-siem">
                                SIEM and Log Analysis
                            </label>
                        </p>

                        <p>
                            <input
                                type="checkbox"
                                id="skill-cloud"
                                name="skills[]"
                                value="cloud-security"
                            >
                            <label for="skill-cloud">
                                Cloud Security
                            </label>
                        </p>

                        <p>
                            <input
                                type="checkbox"
                                id="skill-scripting"
                                name="skills[]"
                                value="scripting"
                            >
                            <label for="skill-scripting">
                                Scripting (Python/Bash)
                            </label>
                        </p>
                    </div>

                    <p>
                        <label for="otherSkills">Other Skills (optional)</label>

                        <textarea
                            id="otherSkills"
                            name="otherSkills"
                            rows="5"
                            cols="40"
                            placeholder="Describe any other relevant skills"
                        ></textarea>
                    </p>
                </fieldset>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        Submit Application
                    </button>

                    <button type="reset" class="btn-secondary">
                        Clear Form
                    </button>
                </div>
            </form>

        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/footer.inc'; ?>
</body>
</html>