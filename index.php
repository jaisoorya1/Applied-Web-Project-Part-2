<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlackVaultCyber | Home</title>
    <link rel="stylesheet" href="styles/styles.css">
    
    <!-- REQUIREMENT: Embedded CSS -->
    <style>
        .digital-highlight {
            background-color: #00ffcc;
            color: #000;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>

   <?php require __DIR__ . '/header.inc'; ?>

    <main class="index-bg">
        
        <div class="company-intro content-box">
            <h2>Welcome to the Vault</h2>
            <p style="text-align: justify; border-left: 4px solid #00ffcc; padding-left: 15px;">
                BlackVaultCyber is a premier <span class="digital-highlight">cybersecurity services company</span> dedicated to protecting digital assets in an increasingly volatile online landscape. We expand digital teams to support secure client portals, cyber awareness websites, incident reporting systems, and top-tier online security services. 
            </p>
            
            <div class="intro-image-container">
                <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=800&q=80" 
                     alt="Abstract digital matrix code representing cybersecurity data" 
                     class="cyber-image">
            </div>
        </div>

        <div class="services content-box">
            <h2>Our Security Tiers</h2>
            <table class="cyber-table">
                <caption>Overview of BlackVaultCyber Security Packages</caption>
                <thead>
                    <tr>
                        <th>Service Tier</th>
                        <th>Included Features</th>
                        <th>Target Audience</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Core Defense</td>
                        <td>Basic firewall, threat monitoring, secure portals</td>
                        <td>Small Businesses</td>
                    </tr>
                    <tr>
                        <td>Advanced Shield</td>
                        <td>Core + Penetration testing, incident reporting</td>
                        <!-- REQUIREMENT: Rowspan merge -->
                        <td rowspan="2">Enterprise & Government</td> 
                    </tr>
                    <tr>
                        <td>Quantum Vault</td>
                        <td>Advanced + Dedicated 24/7 SOC, full cyber awareness ops</td>
                    </tr>
                    <tr>
                        <!-- REQUIREMENT: Colspan merge -->
                        <td colspan="3" class="table-footer">All tiers include our mandatory Cyber Awareness Training module.</td> 
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="acknowledgement content-box">
            <h2>Acknowledgement of Country</h2>
            <p>
                BlackVaultCyber respectfully acknowledges the Traditional Custodians of the lands on which we operate. We pay our respects to Elders past and present, and celebrate the diversity of Aboriginal and Torres Strait Islander peoples and their ongoing connections to land, sea, and community. We are committed to inclusive employment practices and strongly encourage applications from First Nations peoples.
            </p>
        </div>
        
    </main>

    //footer
    <?php require __DIR__ . '/footer.inc'; ?>

</body>
</html>