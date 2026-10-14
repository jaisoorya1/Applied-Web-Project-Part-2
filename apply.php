<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Position Description Page">
    <meta name="keywords" content="cyber, security, jobs, availble">
    <meta name="author" content="Dat">
    <title>BlackVaultCyber | Application</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="main-header">
        <!-- Logo strictly on the left -->
        <div class="logo-area">
            <h1>🛡️ BlackVaultCyber</h1>
            <p class="slogan">Securing the Digital Frontier.</p>
        </div>
        
        <!-- Navigation strictly on the right -->
        <nav class="nav-area">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="jobs.html">Careers</a></li>
                <li><a href="apply.html">Apply Now</a></li>
                <li><a href="about.html">About Us</a></li>
            </ul>
        </nav>
    </header>


    <main class="page-wrap">
    <section class="page-intro">
        <h2>Apply to Black Vault Cyber</h2>
        <p>Complete the form below to submit your application. Fields marked with an asterisk (*) are required. Our team reviews every application and will contact you within two weeks.</p>
    </section>

    <div class="box-row">
        <div class="box">
            <h2>Real Impact</h2>
            <p>Make a tangible difference in protecting our clients' digital assets.</p>
        </div>
        <div class="box">
            <h2>Growth Focused</h2>
            <p>We invest in your professional development with training and mentorship.</p>
        </div>
        <div class="box">
            <h2>Trusted Team</h2>
            <p>Join a team of experienced analysts in a security-first culture.</p> 
        </div>     
    </div>
    
    <form class="apply-form" action="http://mercury.swin.edu.au/it000000/formtest.php" method="post">
  
    <fieldset>
        <legend>Personal Details</legend>
        <p><label for="fullName">Full Name *</label>
            <input type="text" id="fullName" name="fullName" required>
        </p>
        <p><label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required>
        </p>
        <p><label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" required>
        </p>
    </fieldset>

    <fieldset>
        <legend>Position Details</legend>
        
        <label for="positions">Positions Applying for *</label>
        <select id="positions" name="positions" required>
            <option value="">-- Select a position --</option>
            <option value="SOC-analyst">SOC Analyst</option>
            <option value="incident-responder">Incident Response Engineer</option>
            <option value="security-engineer">Security Engineer</option>
            <option value="cyber-security-engineer">Cyber Security Engineer</option>
            <option value="penetration-tester">Penetration Tester</option>
        </select>
       
        <label for="start-date">Earliest Start Date *</label>
        <input type="date" id="start-date" name="start-date" required>

    <fieldset class="sub-fieldset">
        <legend>Experience Level*</legend>
        <label><input type="radio" name="experience" value="entry" required> Entry Level (0-1 years)</label>
        <label><input type="radio" name="experience" value="mid" required> Mid Level (2-5 years)</label>
        <label><input type="radio" name="experience" value="senior" required> Senior Level (5+ years)</label>
    </fieldset>
    </fieldset>

    <fieldset>
        <legend>Documents & Details</legend>
        
        <label for="resume">Upload resume/CV *</label>
        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>

        <label for="linkedin">LinkedIn or portfolio URL</label>
        <input type="url" id="linkedin" name="linkedin" placeholder="https://">
  
        <label for="cover-letter">Cover letter / why you want to join</label>
        <textarea id="cover-letter" name="cover-letter" rows="6"></textarea>
 
    </fieldset>

        
    <fieldset>
        <legend>Consent</legend>
  
        <label>
        <input type="checkbox" name="consent" required>
        I consent to Black Vault Cyber storing and processing my application details. *
        </label>
 
    </fieldset>
    
    <div class="form-actions">
        <button type="submit" class="btn-primary">Submit application</button>
        <button type="reset" class="btn-secondary">Clear form</button>
    </div>

    </form>  

    </main>

<footer>
        <div class="footer-links">
            <p>Project Resources:</p>
            <ul>
                <li><a href="https://jaiwebproject.atlassian.net/jira/software/projects/WA/boards/69/backlog?atlOrigin=eyJpIjoiNGJlNjE3NTBhYWRjNDkwNDhkNDMzZmNmNmE3YmRiYTAiLCJwIjoiaiJ9" target="_blank" rel="noopener noreferrer">Jira Board</a></li>
                <li><a href="https://github.com/jaisoorya1/Applied-Web-Project-Part-1.git" target="_blank" rel="noopener noreferrer">GitHub Repository</a></li>
                <li><a href="mailto:blackvaultcyber@gmail.com">Contact Team</a></li>
            </ul>
        </div>
        <p>&copy; 2026 BlackVaultCyber. All rights reserved.</p>
</footer>
</body>
</html>