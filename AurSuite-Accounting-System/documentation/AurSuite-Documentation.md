# AurSuite Documentation

## Table of Contents
1. Introduction
2. System Requirements
3. Installation Guide
4. Getting Started
5. System Modules Overview
6. Customization
7. Demo Data & Login
8. Assets & Licenses
9. Support & Contact
10. Folder Structure
11. Changelog

---

## 1. Introduction
AurSuite is a modern, powerful, and user-friendly accounting and business management system. It is designed to help businesses of all sizes manage their finances, operations, and reporting efficiently. With multilingual support, advanced security, and a modular structure, AurSuite is the perfect solution for digital transformation.

---

## 2. System Requirements
- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB
- Composer
- Node.js & npm (for asset compilation)
- Apache or Nginx web server
- Recommended: SSL certificate for secure access

---

## 3. Installation Guide
1. Extract the package to your web server directory.
2. Copy all files from the `source/` folder to your web root.
3. Create a new database and update the `.env` file with your database credentials.
4. Run the following commands:
   ```bash
   composer install
   npm install && npm run build
   php artisan migrate --seed
   php artisan storage:link
   ```
5. Open your browser and follow the installation wizard.

---

## 4. Getting Started
- Login using the demo credentials provided below.
- Explore the dashboard and modules.
- Access settings to configure company info, currencies, and languages.

---

## 5. System Modules Overview
- **Dashboard:** Real-time statistics, quick links, and business overview.
- **Accounts:** Manage your chart of accounts, balances, and categories.
- **Vouchers:** Create and manage payment/receipt vouchers.
- **Customers:** Add, edit, and track customers and their transactions.
- **Invoices:** Issue, manage, and track invoices and payments.
- **Employees & Payroll:** Manage employee records, salaries, and payroll batches.
- **Reports:** Generate financial and operational reports.
- **Settings:** Configure system preferences, branding, and integrations.
- **User Management:** Manage users, roles, and permissions.
- **Audit Log:** Track all system activities for transparency.
- **Notifications:** Stay updated with system alerts and messages.
- **Multi-language & Multi-currency:** Switch between supported languages and currencies.

> _[Insert screenshots here: e.g., Dashboard, Accounts, Invoices, etc.]_

---

## 6. Customization
- **Branding:** Replace logos and images in `public/assets/` and `assets/images/`.
- **Languages:** Edit or add translations in `resources/lang/`.
- **Modules:** Extend or modify modules in the `app/` directory.
- **Styling:** Customize styles in `resources/css/` or `resources/sass/`.

---

## 7. Demo Data & Login
- **Admin:**
  - Email: `admin@demo.com`
  - Password: `password`
- **User:**
  - Email: `user@demo.com`
  - Password: `password`

> You can add or edit users from the admin panel.

---

## 8. Assets & Licenses
- All included images, icons, and fonts are open-source or have commercial redistribution licenses.
- If you add your own assets, ensure you have the right to redistribute them.
- See the README and this documentation for asset credits.

---

## 9. Support & Contact
For support or business inquiries:
- Email: info@aursuite.com
- Website: https://aursuite.com

---

## 10. Folder Structure
```
AurSuite-Accounting-System/
├── documentation/
├── source/
├── assets/
├── preview/
├── LICENSE.txt
├── changelog.txt
└── README.md
```

---

## 11. Changelog
See `changelog.txt` for version history and updates.

---

Thank you for choosing AurSuite! 