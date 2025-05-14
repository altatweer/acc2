# AurSuite - Modern Accounting & Business Management System

## Overview
AurSuite is a next-generation accounting and business management platform designed for modern companies of all sizes. It offers powerful financial tools, an intuitive user experience, and advanced security to help you manage every aspect of your business with confidence and ease.

---

## Folder Structure
```
AurSuite-Accounting-System/
│
├── documentation/           # Main documentation (PDF/HTML)
├── source/                  # All application source code
├── assets/
│   ├── images/              # Logos, illustrations, and images
│   └── demo-data/           # Sample/demo data (if any)
├── preview/
│   ├── index.html           # Landing page / live preview
│   └── screenshots/         # Screenshots for preview
├── LICENSE.txt              # License file
├── changelog.txt            # Changelog (if applicable)
└── README.md                # Quick start guide (this file)
```

---

## Requirements
- PHP 8.1+
- MySQL 5.7+/MariaDB
- Composer
- Node.js & npm (for asset compilation)
- Web server (Apache/Nginx)

---

## Installation
1. **Extract the package** to your web server directory.
2. Copy all files from `source/` to your desired web root.
3. Create a new database and update the `.env` file with your database credentials.
4. Run the following commands in your project root:
   ```bash
   composer install
   npm install && npm run build
   php artisan migrate --seed
   php artisan storage:link
   ```
5. Access the system via your browser and follow the installation wizard.

---

## Demo Login Credentials
- **Admin:**
  - Email: `admin@demo.com`
  - Password: `password`
- **User:**
  - Email: `user@demo.com`
  - Password: `password`

> You can change or add users from the admin panel after login.

---

## Customization
- All source code is open and well-commented for easy customization.
- Language files are located in `resources/lang/` (supports English & Arabic).
- To change branding, update images in `public/assets/` and `assets/images/`.
- For advanced customization, refer to the full documentation in `documentation/AurSuite-Documentation.pdf`.

---

## Assets & Licenses
- All included images, icons, and fonts are either open-source or have commercial redistribution licenses.
- If you use your own assets, ensure you have the right to redistribute them.
- See documentation for full asset credits and licenses.

---

## Support
For support, questions, or business inquiries, contact:
- Email: [info@aursuite.com](mailto:info@aursuite.com)
- Or visit the official website: [aursuite.com](https://aursuite.com)

---

## Changelog
See `changelog.txt` for version history and updates.

---

## Notes
- This package includes only demo/sample data. Please remove or replace it before using in production.
- Do not share your license or this package with unauthorized parties.

---

Thank you for choosing AurSuite!
