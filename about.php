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
              <figure class="team-figure" style="max-width: 720px; margin: 25px auto;">
                  <img src="images/group_photo.jpeg" alt="The four BlackVault Cyber team members" width="1600" height="1680">
                  <figcaption>BlackVaultCyber Core Deployment Team: Mohammad, Simran, Jai, and Dat.</figcaption>
              </figure>
          </section>
        <section class="contributions-section">
            <h2>Member Contributions</h2>
            <dl class="contribution-list">
                <dt>
                    <span class="member-name">Jai</span>                                                                                                                                             
                </dt>
                <dd>
                    <strong>Key Responsibilities:</strong> Led overall project management and tracking using Jira, managed repository integrity and collaboration workflows on GitHub, and developed about.html with all required structures, figures, and data tables.
                    <div class="member-quote">
                        <p class="native-text" dir="rtl" lang="ur">"محنت اتنی خاموشی سے کرو کہ تمہاری کامیابی شور مچا دے۔"</p>
                        <p class="trans-text"><strong>Translation (Urdu):</strong> "Work hard in silence, let your success be your noise."</p>
                    </div>
                </dd>
                <dt>
                    <span class="member-name">Mohammad</span>
                </dt>
                <dd>
                    <strong>Key Responsibilities:</strong> Engineered layout for index.html, established the cybersecurity branding, implemented the multi-tiered services comparison table, and configured the CSS background pattern.
                    <div class="member-quote">
                        <p class="native-text" dir="rtl" lang="ar">"امشي عدل يحتار عدوک فيك"</p>
                        <p class="trans-text"><strong>Translation (Arabic):</strong> "Walk the straight and narrow, and your enemies will have nothing on you."</p>
                    </div>
                </dd>
                <dt>
                    <span class="member-name">Dat</span>
                </dt>
                <dd>
                    <strong>Key Responsibilities:</strong> Structured jobs.html, authored detailed cybersecurity job descriptions and qualification matrices, and engineered the layout for the complementary &lt;aside&gt; content module.
                    <div class="member-quote">
                        <p class="native-text" lang="vi">"Cứ thử đi."</p>
                        <p class="trans-text"><strong>Translation (Vietnamese):</strong> "Just try it."</p>
                    </div>
                </dd>
                <dt>
                    <span class="member-name">Simran</span>
                </dt>
                <dd>
                    <strong>Key Responsibilities:</strong> Built the complete semantic recruitment form in apply.html, configured fieldsets, labels, and strict HTML5 client-side input validations, and structured the form layout using modern CSS techniques.
                    <div class="member-quote">
                        <p class="native-text" lang="hi">"मैं उड़ना चाहता हूँ, दौड़ना चाहता हूँ, गिरना भी चाहता हूँ ... बस रुकना नहीं चाहता।"</p>
                        <p class="trans-text"><strong>Translation (Hindi):</strong> "I want to fly, I want to run, I even want to fall ... I just don't want to stop."</p>
                    </div>
                </dd>

            </dl>
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
                        <tr>
                            <td>Simran</td>
                            <td><span class="student-id">106578257</span></td>
                            <td> Kindle </td>
                            <td>Owner of two dogs and a pet snake.</td>
                        </tr>
                        <tr>
                            <td>Mohammad</td>
                            <td><span class="student-id">106205050</span></td>
                            <td>Modern Bidet Attachment</td>
                            <td>Did you know that there are over 100 pyramids in Egypt?</td>
                        </tr>
                        <tr>
                            <td>Jai</td>
                            <td><span class="student-id">106206655</span></td>
                            <td>Automated Threat Scanners</td>
                            <td>Pakistan is home to the world's second-highest mountain peak (K2).</td>
                        </tr>
                        <tr>
                            <td>Dat</td>
                            <td><span class="student-id">106638766</span></td>
                            <td>Prosthetic Limb Engineering</td>
                            <td>Admittedly not a writer, but somehow always gets by.</td>
                        </tr>
                    </tbody>
                    <caption>BlackVaultCyber Security Personnel Interests and Background Facts</caption>
                </table>
            </div>
        </section>

    </main>

    <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>