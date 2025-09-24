# VAT App

A web application for validating, processing, and managing VAT numbers from CSV files and manual input.

## Features
- Upload CSV files containing VAT numbers and optional IDs
- Validate VAT numbers according to business rules
- Detect and mark duplicates
- Automatically correct and highlight fixed VAT numbers
- Replace IDs if they already exist in the database
- Manual VAT number validation and insertion
- Summary dashboard by status (valid, fixed, invalid, duplicate, replace)

## Technologies
- PHP (OOP, Hexagonal Architecture)
- MySQL (database)
- HTML, CSS, JavaScript (frontend)

## Folder Structure
```
vat-app/
├── public/
│   ├── index.php
│   └── css/
├── scripts/
├── src/
│   ├── Application/
│   ├── Domain/
│   ├── Infrastructure/
│   └── Presentation/
├── vendor/
├── composer.json
└── readme.md
```

## How to Use
1. Clone the repository 
2. Review and configure the database connection in `scripts/setup_database.php`, this will automatically create the database with its corresponding tables.
3. install dependencies with Composer (composer install).
2. Configure your database connection in `src/Infrastructure/Persistence/MySQLConnection.php`.
3. Start your local server (e.g., XAMPP) and access `http://localhost/vat-app/public/index.php` in your browser.
4. Upload a CSV file or enter a VAT number manually.
5. View results, corrections, and status summary on the dashboard.

## CSV Format
- Required columns: `vat_number` (and optionally `id`)
- Example:
  ```csv
  id,vat_number
  1,IT12345678901
  2,IT10987654321
  ```


## Author
Borja Outeiral Novo

