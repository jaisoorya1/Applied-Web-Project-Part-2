<?php
// Load the shared database settings.
require_once __DIR__ . '/settings.php';

// Make database errors available to the catch block.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Connect to MySQL using settings.php.
        $conn = mysqli_connect($host, $user, $pwd, $sql_db);

        // Support multilingual text.
        mysqli_set_charset($conn, 'utf8mb4');
        // Read the first team record.
        $result = mysqli_query($conn,"SELECT * FROM about_team ORDER BY team_id LIMIT 1");

        $team = mysqli_fetch_assoc($result);

        if (!$team) {exit('Please add team information to the database first.');}
            // Get the members belonging to this team.
            $statement = mysqli_prepare(
                $conn,
                "SELECT * FROM team_members
                WHERE team_id = ?
                ORDER BY display_order, member_id"
            );

            $team_id = (int) $team['team_id'];

            mysqli_stmt_bind_param($statement, 'i', $team_id);
            mysqli_stmt_execute($statement);

            $member_result = mysqli_stmt_get_result($statement);

            $members = [];

            while ($member = mysqli_fetch_assoc($member_result)) {
                $members[] = $member;
            }

            mysqli_free_result($member_result);
            mysqli_stmt_close($statement);
    }
    catch (mysqli_sql_exception $error) 
    {
        // Record technical details in the server log.
        error_log($error->getMessage());

        // Show visitors a simple message.
        http_response_code(500);exit('Unable to load team information. Please try again later.');
    }

    // Display database text safely.
    function about_escape($text)
    {
        return htmlspecialchars(
            (string) $text,
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
    <title>BlackVaultCyber | About the Team</title>
    <link rel="stylesheet" href="styles/styles.css">
    
    <style>
        .member-quote {
            border-left: 3px solid #00ffcc;
            background-color: #121212;
            padding: 12px 18px;
            margin: 12px 0 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .native-text {
            font-style: italic;
            color: #ffffff;
            font-size: 1.05rem;
            margin-bottom: 4px;
        }
        .trans-text {
            color: #888888;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <?php require __DIR__ . '/header.inc'; ?>

    <main class="content-box">
        
    <section class="team-info">
            <h2>Team Information</h2>

            <ul class="nested-group-list">
                <li>
                    <strong>Group Allocation:</strong>
                    <?php echo about_escape($team['group_allocation']); ?>

                    <ul>
                        <li>
                            <strong>Team Identifier:</strong>
                            <?php echo about_escape($team['team_name']); ?>
                        </li>

                        <li>
                            <strong>Tutorial Day:</strong>
                            <?php echo about_escape($team['tutorial_day']); ?>
                        </li>

                        <li>
                            <strong>Tutorial Time:</strong>
                            <?php echo about_escape($team['tutorial_time']); ?>
                        </li>
                    </ul>
                </li>
            </ul>
    </section>
          
    <section class="team-visual">

        <h2>The Operations Team</h2>

        <figure class="team-figure"
                style="max-width: 720px; margin: 25px auto;">

            <img
                src="<?php echo about_escape($team['photo_path']); ?>"
                alt="<?php echo about_escape($team['photo_alt']); ?>"
                width="1600"
                height="1680"
                style="max-width: 100%; height: auto;"
            >

            <figcaption>
                <?php echo about_escape($team['photo_caption']); ?>
            </figcaption>

        </figure>
    </section>
        
    <section class="contributions-section">
    
        <h2>Member Contributions</h2>

            <?php if (empty($members)): ?>
                <p>No member information has been added yet.</p>
            <?php else: ?>

                <dl class="contribution-list">

                    <?php foreach ($members as $member): ?>

                        <dt>
                            <span class="member-name">
                                <?php echo about_escape($member['member_name']); ?>
                            </span>
                        </dt>

                        <dd>
                            <p>
                                <strong>Project Part 1:</strong>
                                <?php echo about_escape($member['part1_contribution']); ?>
                            </p>

                            <p>
                                <strong>Project Part 2:</strong>
                                <?php echo about_escape($member['part2_contribution']); ?>
                            </p>

                            <div class="member-quote">
                                <p
                                    class="native-text"
                                    lang="<?php echo about_escape($member['quote_language_code']); ?>"
                                    dir="<?php echo $member['quote_direction'] === 'rtl' ? 'rtl' : 'ltr'; ?>"
                                >
                                    <?php echo about_escape($member['native_quote']); ?>
                                </p>

                                <p class="trans-text">
                                    <strong>
                                        Translation
                                        (<?php echo about_escape($member['quote_language']); ?>):
                                    </strong>

                                    <?php echo about_escape($member['quote_translation']); ?>
                                </p>
                            </div>
                        </dd>

                    <?php endforeach; ?>

                </dl>

            <?php endif; ?>
    </section>

        <section class="team-facts-section">
            <h2>Team Profile &amp; Demographics</h2>
            <div class="table-container">
                <table class="team-table">
                    <thead>
                        <tr>
                            <th scope="col">Member Name</th>
                            <th scope="col">Student ID</th>
                            <th scope="col">Favorite Technology</th>
                            <th scope="col">Fun Fact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td>
                                    <?php echo about_escape($member['member_name']); ?>
                                </td>

                                <td>
                                    <span class="student-id">
                                        <?php echo about_escape($member['student_id']); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo about_escape($member['favourite_technology']); ?>
                                </td>

                                <td>
                                    <?php echo about_escape($member['fun_fact']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <caption>BlackVaultCyber Security Personnel Interests and Background Facts</caption>
                </table>
            </div>
        </section>

    </main>

    <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>