
<h1 align="center">Noshguard</h1>


## 📑 Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Usage](#usage)
- [Demo / Screenshots](#-walkthrough)
- [Project Structure](#project-structure)
- [Technologies Used](#technologies-used)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

---

## Introduction

**Noshguard** is a PHP-based web application that helps users detect vulnerabilities and risks in web uploads or links. It leverages a remote MySQL database (via freesqldatabase.com) and offers a user-friendly dashboard, scan results, and activity history.

---

## Features

- 🔐 **User Authentication**
- 🛡️ **File/Web Scanning for Risk Detection**
- 📊 **Risk Summary with Severity Levels**
- 🕓 **Scan History Tracking**
- 👤 **Profile Management**
- ⚙️ **Remote DB Configuration via `freesqldatabase.com`**

---


### Prerequisites

- Apache/Nginx with PHP 7.4+
- A [freesqldatabase.com](https://www.freesqldatabase.com/) account

### Steps

```bash
git clone https://github.com/YourGitHubUsername/Noshguard.git
cd Noshguard
```
Upload files to htdocs (Apache) or www (Nginx).

Import the .sql file into your freesqldatabase.com database.

Update config.php:
define('DB_HOST', 'sql6.freesqldatabase.com');
define('DB_USER', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'your_db_name');

## Usage
Start your local server (e.g., using XAMPP).

Open in browser:
http://localhost/noshguard/splash.php
Register/login → Use dashboard → Start scanning.

## Project Structure
Noshguard/
├── css/               # Stylesheets
├── js/                # JavaScript
├── images/            # Logos, icons
├── uploads/           # Uploaded files
├── config.php         # DB config
├── register.php       # Registration
├── login.php          # Login
├── dashboard.php      # User dashboard
├── scan.php           # Scan handler
├── risk_summary.php   # Results
└── history.php        # User scan history
## Technologies Used
Layer	Technology
Backend	PHP
Frontend	HTML, CSS, JS
Database	MySQL (Remote)
Hosting	freesqldatabase.com
Server	Apache / Nginx

## Contributing
Fork the repo

Create your feature branch:
git checkout -b feature/YourFeature
Commit, push, and open a Pull Request

## License
This project is licensed under the MIT License. See the LICENSE file for details.

📬 Contact
Developer: Venkateswara Reddy
Email: venkateswarareddy2005@gmail.com
GitHub: github.com/venkatesh020705
