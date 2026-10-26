-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 07:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blackvault_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_team`
--

CREATE TABLE `about_team` (
  `team_id` int(10) UNSIGNED NOT NULL,
  `group_allocation` varchar(150) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `tutorial_day` varchar(20) NOT NULL,
  `tutorial_time` varchar(50) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `photo_alt` varchar(255) NOT NULL,
  `photo_caption` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_team`
--

INSERT INTO `about_team` (`team_id`, `group_allocation`, `team_name`, `tutorial_day`, `tutorial_time`, `photo_path`, `photo_alt`, `photo_caption`) VALUES
(1, 'G01 - Cybersecurity Services', 'Team Blaze', 'Wednesday', '2:30 PM - 4:30 PM', 'images/group_photo.jpeg', 'The four BlackVault Cyber team members', 'BlackVaultCyber Core Deployment Team: Mohammad, Simran, Jai, and Dat.');

-- --------------------------------------------------------

--
-- Table structure for table `eoi`
--

CREATE TABLE `eoi` (
  `EOInumber` int(11) NOT NULL,
  `job_ref` varchar(30) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `start_date` date DEFAULT NULL,
  `experience` varchar(30) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('New','Current','Final') NOT NULL DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `dob` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `street` varchar(40) DEFAULT NULL,
  `suburb` varchar(40) DEFAULT NULL,
  `state` varchar(3) DEFAULT NULL,
  `postcode` char(4) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `other_skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eoi`
--

INSERT INTO `eoi` (`EOInumber`, `job_ref`, `first_name`, `last_name`, `email`, `phone`, `start_date`, `experience`, `linkedin`, `cover_letter`, `status`, `created_at`, `dob`, `gender`, `street`, `suburb`, `state`, `postcode`, `skills`, `other_skills`) VALUES
(1, 'SOC026', 'Test', 'Applicant', 'sooriyajeswani1@gmail.com', '047544466', NULL, NULL, NULL, NULL, 'New', '2026-10-24 07:20:24', '2007-04-13', 'female', '1 Flinders St', 'Hawthorn East', 'VIC', '3123', 'network-security, incident-response', '');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `ref_code` varchar(150) NOT NULL,
  `job_type` varchar(50) NOT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `key_responsibilities` text DEFAULT NULL,
  `essential_req` text DEFAULT NULL,
  `preferable_req` text DEFAULT NULL,
  `aside` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `ref_code`, `job_type`, `salary`, `description`, `key_responsibilities`, `essential_req`, `preferable_req`, `aside`) VALUES
(1, 'SOC Analyst', 'SOC026', 'Full-time', '$85,000–$120,000 + superannuation, depending on experience', 'We are seeking a SOC Analyst to monitor, investigate and respond to cybersecurity threats across our technology environment. You will work with SIEM, EDR/XDR and other security tools to identify threats, investigate incidents and support continuous improvement of our security operations.', 'Monitor and triage security alerts and events.\r\nInvestigate phishing, malware, suspicious activity and potential breaches.\r\nPerform initial incident response, containment and escalation.\r\nAnalyse logs and security events across networks, endpoints and cloud environments.\r\nUse SIEM, EDR/XDR and threat-intelligence tools.\r\nDocument incidents and maintain accurate investigation records.\r\nSupport threat hunting, detection tuning and security improvements.\r\nEscalate high-risk incidents to senior security or technical teams.', 'Relevant experience or qualifications in Cybersecurity, IT, Computer Science or a related field.\r\nExperience in SOC, cybersecurity, IT security or a similar role.\r\nGood understanding of networking, Windows/Linux and cybersecurity fundamentals.\r\nExperience with a SIEM platform such as Microsoft Sentinel, Splunk or QRadar.\r\nUnderstanding of EDR/XDR and incident-response processes.\r\nFamiliarity with MITRE ATT&CK and common cyber attack techniques.\r\nStrong analytical, problem-solving and communication skills.', '1–3+ years\' SOC/cybersecurity experience.\r\nExperience with Microsoft Sentinel, Defender, Splunk, CrowdStrike or similar.\r\nKnowledge of Azure/AWS security.\r\nExperience with KQL, SPL, Python, PowerShell or Bash.\r\nCybersecurity certifications such as Security+, CySA+, GIAC or CISSP.\r\nKnowledge of Essential Eight, NIST or ISO 27001.\r\nThreat-hunting or digital-forensics experience.', 'The SOC Analyst reports directly to the SOC Manager and is part of the broader Cybersecurity function. The role works closely with Cybersecurity, IT, and other relevant teams to monitor security events, identify and investigate potential threats, and support the timely response and resolution of security incidents. The SOC Analyst will also collaborate with internal stakeholders to strengthen security monitoring, improve incident response processes, and contribute to the organisation’s overall cybersecurity posture.'),
(2, 'Security Engineer', 'SEC026', 'Full-time', '$100,000–$140,000 + superannuation, depending on experience', 'We are seeking a Security Engineer to design, implement and maintain security controls that protect our systems, networks, applications and cloud environments. You will work closely with IT and engineering teams to identify risks, strengthen security and respond to emerging threats.', 'Design, implement and maintain cybersecurity solutions and controls.\r\nConfigure and manage security technologies including firewalls, EDR/XDR, SIEM, IAM and vulnerability-management platforms.\r\nMonitor security systems and investigate potential threats and vulnerabilities.\r\nSupport security incident response and remediation activities.\r\nConduct vulnerability assessments and coordinate remediation.\r\nImplement security controls across cloud, network, endpoint and application environments.\r\nDevelop security policies, standards, procedures and technical documentation.\r\nAutomate security processes and improve monitoring and detection capabilities.\r\nSupport security assessments, audits and compliance requirements.\r\nWork with infrastructure, cloud, DevOps and application teams to embed security into technology solutions.', 'Relevant experience in Cybersecurity, Security Engineering, Network Security or a related field.\r\nStrong understanding of cybersecurity principles and security architecture.\r\nHands-on experience with security technologies such as SIEM, EDR/XDR, firewalls and IAM.\r\nGood knowledge of networking, Windows/Linux and cloud environments.\r\nExperience with vulnerability management and security remediation.\r\nUnderstanding of identity, access management and security controls.\r\nStrong analytical, problem-solving and troubleshooting skills.\r\nExcellent communication and collaboration skills.', '3+ years\' experience in security engineering or a related cybersecurity role.\r\nExperience with Microsoft Azure/AWS security.\r\nExperience with Microsoft Sentinel, Defender, CrowdStrike, Splunk or similar platforms.\r\nKnowledge of Zero Trust, DevSecOps and cloud security.\r\nScripting or automation experience using PowerShell, Python or Bash.\r\nKnowledge of NIST, ISO 27001 and Essential Eight.\r\nRelevant certifications such as Security+, CySA+, CCSP, CISSP or cloud-security certifications.\r\nExperience with security architecture, penetration testing or threat modelling.\r\nAustralian Government security clearance, where applicable.', 'The Security Engineer reports directly to the Security Engineering Manager and is part of the broader Cybersecurity function. The role works closely with Cybersecurity, IT, infrastructure, and other relevant teams to design, implement, and maintain security solutions across the organisation. The Security Engineer will collaborate with key stakeholders to address security risks, improve security controls and capabilities, and contribute to strengthening the organisation’s overall security posture.'),
(3, 'Incident Response Engineer', 'IRE026', 'Full-time', '$100,000–$140,000 + superannuation, depending on experience', 'We are seeking an experienced Incident Response Engineer to detect, investigate and respond to cybersecurity incidents across our technology environment. You will lead technical investigations, contain threats, support recovery and help strengthen our incident-response capabilities.', 'Investigate and respond to cybersecurity incidents and breaches.\r\nLead technical investigations from detection through containment and recovery.\r\nAnalyse endpoint, network, cloud and application logs and forensic evidence.\r\nPerform malware, phishing, credential-compromise and intrusion investigations.\r\nConduct threat hunting and identify indicators of compromise (IOCs).\r\nUse EDR/XDR, SIEM, forensic and threat-intelligence tools.\r\nCoordinate incident containment, eradication and recovery activities.\r\nDevelop and improve incident-response playbooks, processes and procedures.\r\nPerform root-cause analysis and provide post-incident recommendations.\r\nWork closely with SOC, infrastructure, cloud and engineering teams during incidents.\r\nSupport incident-response exercises and security improvement initiatives.', 'Relevant experience in Incident Response, Cybersecurity, SOC, Digital Forensics or a related field.\r\nStrong understanding of incident-response methodologies and cybersecurity principles.\r\nHands-on experience with EDR/XDR and SIEM platforms.\r\nStrong knowledge of Windows and Linux environments.\r\nGood understanding of networking, Active Directory and cloud security.\r\nExperience investigating malware, phishing, credential compromise and unauthorised access.\r\nFamiliarity with MITRE ATT&CK and common attack techniques.\r\nStrong analytical, problem-solving and communication skills.\r\nAbility to work effectively under pressure during security incidents.', '3+ years\' experience in incident response or cybersecurity.\r\nExperience with Microsoft Defender, Sentinel, CrowdStrike, Splunk or similar platforms.\r\nDigital forensics and malware-analysis experience.\r\nThreat-hunting experience.\r\nExperience with Azure/AWS security investigations.\r\nScripting skills in PowerShell, Python or Bash.\r\nKnowledge of NIST, ISO 27001 and Essential Eight.\r\nCertifications such as GCIH, GCFA, GCIA, CISSP or equivalent.\r\nExperience developing automated incident-response and SOAR workflows.\r\nAustralian Government security clearance, where applicable.', 'The Incident Response Engineer reports directly to the Incident Response Manager and is part of the broader Cybersecurity function. The role works closely with the SOC, Cybersecurity, IT, and infrastructure teams to investigate, contain, and remediate security incidents across the organisation. The Incident Response Engineer will collaborate with key stakeholders during incident investigations, contribute to the development and improvement of incident response processes and capabilities, and support ongoing initiatives to strengthen the organisation’s overall security resilience.'),
(4, 'Penetration Tester', 'PEN026', 'Full-time', '$100,000–$145,000 + superannuation, depending on experience', 'We are seeking a skilled Penetration Tester to identify and assess security weaknesses across applications, networks, infrastructure and cloud environments. You will conduct authorised security testing, validate vulnerabilities and provide practical recommendations to strengthen our security posture.', 'Conduct authorised penetration tests across networks, systems, applications and cloud environments.\r\nIdentify, validate and assess security vulnerabilities and misconfigurations.\r\nPerform web application, API, network and infrastructure security testing.\r\nConduct vulnerability exploitation and controlled proof-of-concept testing.\r\nPerform reconnaissance, enumeration and security assessments.\r\nAnalyse findings and determine risk and potential business impact.\r\nPrepare clear technical reports and remediation recommendations.\r\nPresent findings to technical and non-technical stakeholders.\r\nWork with engineering and security teams to validate remediation.\r\nConduct retesting to confirm vulnerabilities have been resolved.\r\nMaintain awareness of emerging vulnerabilities, attack techniques and security tools.\r\nContribute to improving penetration-testing methodologies, tooling and processes.', 'Relevant experience in Penetration Testing, Offensive Security, Cybersecurity or a related field.\r\nHands-on experience conducting penetration tests in authorised environments.\r\nStrong understanding of networking, operating systems, web applications and common attack techniques.\r\nExperience with tools such as Burp Suite, Nmap, Metasploit and Kali Linux.\r\nUnderstanding of OWASP Top 10 and common web application vulnerabilities.\r\nAbility to identify, validate and clearly document security findings.\r\nStrong analytical, problem-solving and technical writing skills.\r\nExcellent communication and stakeholder-management skills.', '2–4+ years\' experience in penetration testing or offensive security.\r\nExperience with web applications, APIs, Active Directory, cloud and mobile applications.\r\nKnowledge of Azure/AWS security.\r\nExperience with red teaming, adversary simulation or vulnerability research.\r\nScripting/programming skills in Python, PowerShell, Bash, JavaScript or similar.\r\nExperience with Active Directory exploitation and privilege escalation.\r\nCertifications such as OSCP, OSWE, OSEP, PNPT, CREST or GPEN.\r\nKnowledge of MITRE ATT&CK, PTES and OWASP testing methodologies.\r\nExperience producing client-facing penetration-test reports.\r\nAustralian Government security clearance, where applicable.', 'The Penetration Tester reports directly to the Offensive Security Manager and is part of the broader Cybersecurity function. The role works closely with Cybersecurity, IT, application development, and infrastructure teams to identify and assess security vulnerabilities across the organisation. The Penetration Tester will collaborate with key stakeholders to communicate findings, support remediation efforts, and contribute to improving the organisation’s overall security posture.');

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `member_id` int(10) UNSIGNED NOT NULL,
  `team_id` int(10) UNSIGNED NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `favourite_technology` varchar(150) NOT NULL,
  `fun_fact` text NOT NULL,
  `part1_contribution` text NOT NULL,
  `part2_contribution` text DEFAULT NULL,
  `native_quote` text NOT NULL,
  `quote_language` varchar(30) NOT NULL,
  `quote_language_code` varchar(10) NOT NULL,
  `quote_direction` enum('ltr','rtl') NOT NULL DEFAULT 'ltr',
  `quote_translation` text NOT NULL,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`member_id`, `team_id`, `member_name`, `student_id`, `favourite_technology`, `fun_fact`, `part1_contribution`, `part2_contribution`, `native_quote`, `quote_language`, `quote_language_code`, `quote_direction`, `quote_translation`, `display_order`) VALUES
(1, 1, 'Jai', '106206655', 'Automated Threat Scanners', 'Pakistan is home to the world\'s second-highest mountain peak (K2).', 'Led project management using Jira, managed GitHub collaboration, and developed about.html with the required structures, figures and data tables.', 'Implemented shared PHP components and database settings. Currently developing the database-driven About page. Responsible for presentation order and timing.', 'محنت اتنی خاموشی سے کرو کہ تمہاری کامیابی شور مچا دے۔', 'Urdu', 'ur', 'rtl', 'Work hard in silence, let your success be your noise.', 1),
(2, 1, 'Mohammad', '106205050', 'Modern Bidet Attachment', 'Did you know that there are over 100 pyramids in Egypt?', 'Developed index.html, established cybersecurity branding, implemented the services comparison table and configured the CSS background pattern.', 'Implemented HR management code for listing and filtering EOIs, deleting by job reference, updating status and sorting results. Authentication integration, fixes and testing remain.', 'امشي عدل يحتار عدوک فيك', 'Arabic', 'ar', 'rtl', 'Walk the straight and narrow, and your enemies will have nothing on you.', 2),
(3, 1, 'Dat', '106638766', 'Prosthetic Limb Engineering', 'Admittedly not a writer, but somehow always gets by.', 'Structured jobs.html, authored cybersecurity job descriptions and qualification requirements, and developed the complementary aside content layout.', 'Assigned the jobs table, database-driven job descriptions and search. Also responsible for the user table and login page. Completion is pending verification.', 'Cứ thử đi.', 'Vietnamese', 'vi', 'ltr', 'Just try it.', 3),
(4, 1, 'Simran', '106578257', 'Kindle', 'Owner of two dogs and a pet snake.', 'Built the recruitment form in apply.html, configured fieldsets, labels and HTML5 client-side validation, and styled the form using CSS.', 'Assigned the EOI table and application processing, including server-side validation, sanitisation, record insertion, direct-access protection and EOI confirmation. Completion is pending verification.', 'मैं उड़ना चाहता हूँ, दौड़ना चाहता हूँ, गिरना भी चाहता हूँ ... बस रुकना नहीं चाहता।', 'Hindi', 'hi', 'ltr', 'I want to fly, I want to run, I even want to fall ... I just don\'t want to stop.', 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_team`
--
ALTER TABLE `about_team`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `eoi`
--
ALTER TABLE `eoi`
  ADD PRIMARY KEY (`EOInumber`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `fk_members_team` (`team_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `unique_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_team`
--
ALTER TABLE `about_team`
  MODIFY `team_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eoi`
--
ALTER TABLE `eoi`
  MODIFY `EOInumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `member_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `team_members`
--
ALTER TABLE `team_members`
  ADD CONSTRAINT `fk_members_team` FOREIGN KEY (`team_id`) REFERENCES `about_team` (`team_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
