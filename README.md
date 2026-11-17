
# Walayta Sodo Kalehiwot Church Website

A bilingual church website developed for Walayta Sodo Kalehiwot Church to share church information, worship resources, announcements, and activities with its community.

## About the Project

The website provides an online platform where visitors and church members can access church information in English and Amharic.

## Features

- Bilingual support (English and Amharic)
- Church homepage
- Sermons and Bible teaching resources
- Events and announcements
- Live streaming and live programs
- Contact form
- Member registration
- Online donations
- Administrator dashboard
- Content management for administrators

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap (if used in the project)
- WampServer
- Git and GitHub

## Project Structure

```text
walayta-church-website/
│
├── admin/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   └── db.example.php
├── includes/
├── index.php
├── sermons.php
├── events.php
├── announcements.php
├── live.php
├── contact.php
└── README.md
```

## Installation and Setup

### 1. Install Requirements

Install:
- WampServer
- PHP
- MySQL
- A web browser

### 2. Clone the Repository

```bash
git clone https://github.com/yisotad/walayta-church-website.git
```

### 3. Configure the Database

- Create a MySQL database using phpMyAdmin.
- Copy `config/db.example.php` to `config/db.php`.
- Update the database name, username, and password in `config/db.php`.
- Import your database tables if you have a database SQL script.

### 4. Run the Project

Place the project inside your WampServer `www` directory.

Start Apache and MySQL, then visit:

```text
http://localhost/walayta-church-website/
```

## Security

- Database credentials are excluded from GitHub.
- The `.gitignore` file prevents sensitive files from being tracked.
- Never upload real database passwords or private database backups.

## Project Purpose

This project aims to improve communication between Walayta Sodo Kalehiwot Church and its community by providing accessible church information and online worship resources.

## Developer

**Project:** Walayta Sodo Kalehiwot Church Website

**GitHub:** https://github.com/yisotad/walayta-church-website

---

Thank you for visiting this project!
