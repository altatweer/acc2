# AurSuite Accounting System — Installation Guide

Welcome to the installation guide for the AurSuite Accounting System. Please follow these steps carefully to ensure a smooth and secure setup.

---

## 1. System Requirements
- **PHP**: 8.0 or higher
- **Database**: MySQL 5.7+/MariaDB
- **Extensions**: PDO, MBSTRING, OPENSSL, FILEINFO, GD
- **Web Server**: Apache/Nginx (with mod_rewrite enabled)
- **Composer**: (for local development)

---

## 2. Uploading Files
1. Unzip the provided package on your local machine.
2. Upload all files and folders (including the `vendor` directory) to your web hosting using FTP, SFTP, or your hosting file manager.
3. Make sure `.env` and all files are transferred.

---

## 3. Setting Permissions
Set the following folders to **writable (0777)**:
- `storage`
- `bootstrap/cache`

You can do this via your hosting file manager or by running:
```sh
chmod -R 777 storage bootstrap/cache
```

---

## 4. Installation Wizard Steps
Open your website in the browser. The installer will guide you through:

### a) Purchase Code Verification
- Enter your valid Envato purchase code.
- The system will verify your code online before proceeding.

### b) Database Setup
- Enter your database host, name, username, and password.
- The installer will test the connection and update the `.env` file automatically.

### c) Database Migration
- The system will create all required tables.
- If the database is not empty, you will be asked to use a new/empty database.

### d) Super Admin Creation
- Enter the main admin's name, email, and password.
- This user will have full system access.

### e) Currency Selection
- Choose one or more currencies for your accounting system.
- Set the default currency.

### f) Chart of Accounts
- Import a ready-made chart (Arabic/English) or skip and add accounts later.

### g) Finish
- The system will finalize installation and lock the installer.
- You can now log in and start using the system.

---

## 5. Troubleshooting
- **Blank page or error?**
  - Check file permissions.
  - Ensure all PHP extensions are enabled.
  - Review `storage/logs/laravel.log` for details.
- **Purchase code not accepted?**
  - Make sure your server can connect to the internet.
  - Contact support if the problem persists.
- **Database connection failed?**
  - Double-check your credentials and database host.

---

## 6. Support
For help, contact:
- **Website**: [aursuite.com](https://aursuite.com)
- **Email**: support@aursuite.com

Thank you for choosing AurSuite! 