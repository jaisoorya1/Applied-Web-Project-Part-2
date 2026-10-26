<?php
require_once "settings.php";
$conn->set_charset("utf8mb4");
$keyword = is_string($_GET["keyword"] ?? null)
    ? trim($_GET["keyword"])
    : "";

$type = is_string($_GET["type"] ?? null)
    ? trim($_GET["type"])
    : "";

$allowedTypes = ["Full-time", "Part-time", "Contract", "Internship"];

if (!in_array($type, $allowedTypes, true)) {
    $type = "";
}

$sql = "SELECT * FROM jobs WHERE 1=1";
$params = [];
$paramTypes = "";

// Search by title, reference, type, salary or description.
if ($keyword !== "") {
    $sql .= " AND (
        title LIKE ?
        OR ref_code LIKE ?
        OR job_type LIKE ?
        OR salary LIKE ?
        OR description LIKE ?
    )";

    $search = "%" . $keyword . "%";

    $params = array_fill(0, 5, $search);
    $paramTypes = "sssss";
}

// Apply the employment-type filter.
if ($type !== "") {
    $sql .= " AND job_type = ?";
    $params[] = $type;
    $paramTypes .= "s";
}

$sql .= " ORDER BY id ASC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Display text safely without treating it as HTML.
function jobs_escape($value)
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
function requirementList($text, $ordered = false) {
    $items = preg_split('/\r\n|\r|\n/', trim($text ?? ""));
    $items = array_filter($items, fn($item) => trim($item) !== "");

    if (!$items) {
        return;
    }

    $tag = $ordered ? "ol" : "ul";

    echo '<' . $tag . ' style="margin-left: 15px">';

    foreach ($items as $item) {
        echo '<li>' . jobs_escape(trim($item)) . '</li>';
    }

    echo '</' . $tag . '>';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Position Description Page">
        <meta name="keywords" content="cyber, security, jobs, availble">
        <meta name="author" content="Dat">
        <title>Position Description Page</title>
        <link rel="stylesheet" href="styles/styles.css">
        <style>
            .digital-highlight {
                background-color: #00ffcc;
                color: #000;
                padding: 2px 5px;
                border-radius: 3px;
                font-weight: bold;
            }
            .job-search {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-bottom: 25px;
            }

            .job-search input,
            .job-search select,
            .job-search button {
                padding: 12px;
                font: inherit;
                border-radius: 6px;
            }

            .job-search input {
                flex: 1;
                min-width: 200px;
            }

            .job-search button {
                cursor: pointer;
            }

            .job-search .clear-link {
                align-self: center;
            }

            .job-results-count {
                margin-bottom: 20px;
            }

            .job-card-list {
                display: grid;
                gap: 20px;
            }
        </style>
    </head>
    <body>
        <?php require __DIR__ . '/header.inc'; ?>

        <main class="index-bg">
            <section class="content-box">
                <h2>Job listings</h2>
                <form method="GET" action="" class="job-search">
                    <input type="search" name="keyword" placeholder="Search title, reference code or keyword" value="<?= jobs_escape($keyword) ?>">

                    <select name="type">
                        <option value="">All employment types</option>

                        <?php foreach ($allowedTypes as $jobType): ?>
                            <option
                                value="<?= jobs_escape($jobType) ?>"
                                <?= $type === $jobType ? "selected" : "" ?>
                            >
                                <?= jobs_escape($jobType) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Search Jobs</button>
                    <a class="clear-link" href="jobs.php">Clear filters</a>
                </form>
            </section>



            <?php while ($job = $result->fetch_assoc()): ?>

            <section class="content-box">
                <h2><?= jobs_escape($job["title"]) ?></h2>

                <aside>
                    reference code: <?= jobs_escape($job["ref_code"]) ?>
                </aside>

                <p style="margin-bottom: 15px">
                    <span style="font-size: 1.2rem; color: var(--accent-color);
                    text-transform: uppercase; letter-spacing: 2px;">
                        <strong>Employment Type: </strong>
                    </span>
                    <?= jobs_escape($job["job_type"]) ?>
                </p>

                <p style="margin-bottom: 15px">
                    <span style="font-size: 1.2rem; color: var(--accent-color);
                    text-transform: uppercase; letter-spacing: 2px;">
                        <strong>Salary: </strong>
                    </span>
                    <?= jobs_escape($job["salary"]) ?>
                </p>

                <h3 style="margin-bottom: 5px">Description:</h3>
                <p style="margin-bottom: 15px">
                    <?= nl2br(jobs_escape($job["description"])) ?>
                </p>

                <details>
                    <summary style="margin-bottom: 15px">Read more</summary>

                    <h3 style="margin-bottom: 5px">Key Responsibilities</h3>
                    <details>
                        <summary style="margin-bottom: 15px">Expand</summary>
                        <?php requirementList($job["key_responsibilities"]); ?>
                    </details>

                    <h3 style="margin-bottom: 5px">Essential Requirements</h3>
                    <details>
                        <summary style="margin-bottom: 15px">Expand</summary>
                        <?php requirementList($job["essential_req"]); ?>
                    </details>

                    <h3 style="margin-bottom: 5px">Preferable Requirements</h3>
                    <details>
                        <summary style="margin-bottom: 15px">Expand</summary>
                        <?php requirementList(
                            $job["preferable_req"], true
                        ); ?>
                    </details>

                    <aside>
                        <?= nl2br(jobs_escape($job["aside"])) ?>
                    </aside>
                </details>
            </section>

        <?php endwhile; ?>



        <?php
        $stmt->close();
        $conn->close();
        ?>
        

    
        </main>

        <?php require __DIR__ . '/footer.inc'; ?>
    </body>
</html>