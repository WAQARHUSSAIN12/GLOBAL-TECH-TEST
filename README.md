# Purchase System Project Test 

A professional Purchase Management System built with Laravel, Livewire, Alpine.js, and Laravel Breeze.

This project was developed as part of a technical assessment covering:

* Dynamic Purchase Entry Module
* Role-Based Access Control
* Legacy Data Migration
* Livewire + Alpine.js Integration
* Secure PHP Debugging Task
* Database Normalization
* CRUD Operations
* Validation & Transactions

---

# Technologies Used

* PHP 8.2+
* Laravel 12
* Livewire 4
* Alpine.js
* Laravel Breeze
* MySQL
* Tailwind CSS
* Vite

---

# Features

## Purchase Entry Module

* Dynamic purchase rows using Livewire
* Add / Remove line items dynamically
* Real-time total calculation
* Duplicate item + brand prevention
* Validation handling
* Transaction-based database saving

## Role-Based Access Control

Two roles implemented:

### Admin

* Create purchases
* View Item 
* Run legacy migration

### User

* View purchases only

---

# Database Structure

## Tables

### items

| Column | Type   |
| ------ | ------ |
| id     | bigint |
| name   | string |

### brands

| Column | Type   |
| ------ | ------ |
| id     | bigint |
| name   | string |

### purchases

| Column     | Type       |
| ---------- | ---------- |
| id         | bigint     |
| total      | decimal    |
| user_id    | bigint     |
| timestamps | timestamps |

### purchase_items

| Column      | Type    |
| ----------- | ------- |
| id          | bigint  |
| purchase_id | bigint  |
| item_id     | bigint  |
| brand_id    | bigint  |
| qty         | integer |
| price       | decimal |

---

# Project Setup

## 1. Clone Repository

```bash
git clone https://github.com/WAQARHUSSAIN12/GLOBAL-TECH-TEST.git
```

## 2. Move Into Project

```bash
cd GLOBAL-TECH-TEST
```

## 3. Install PHP Dependencies

```bash
composer install
```

## 4. Install Node Dependencies

```bash
npm install
```

## 5. Create Environment File

```bash
cp .env.example .env
```

If using Windows PowerShell:

```powershell
copy .env.example .env
```

---

# Database Configuration

Open `.env` file and configure:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=purchase_db
DB_USERNAME=root
DB_PASSWORD=
```

---

# Generate Application Key

```bash
php artisan key:generate
```

---

# Run Migrations

```bash
php artisan migrate
```

---

# Run Seeders

```bash
php artisan db:seed
```

---

# Import Database (Optional)

A database export file is included:

```text
purchase_db.sql
```

You can import it into MySQL using:

## phpMyAdmin

1. Open phpMyAdmin
2. Create database:

```text
purchase_db
```

3. Import:

```text
purchase_db.sql
```

---

# Run Legacy Data Migration

For the legacy migration task:

```bash
php artisan migration:legacy-purchases
```

This command:

* Reads legacy purchase data
* Creates missing items
* Creates missing brands
* Prevents duplicates
* Inserts normalized purchase records
* Is idempotent and safe to re-run

---

# Run Development Server

## Start Laravel Server

```bash
php artisan serve
```

Project URL:

```text
http://127.0.0.1:8000
```

---

# Run Frontend Assets

Start Vite development server:

```bash
npm run dev
```

This is required for:

* Laravel Breeze
* Livewire assets
* Alpine.js
* Tailwind CSS
* Vite compilation

---

# Authentication

Laravel Breeze authentication is installed.

Features:

* Login
* Registration
* Logout
* Protected routes

---

# Livewire + Alpine.js

This project uses:

* Livewire for reactive components
* Alpine.js for frontend interactivity
* Real-time form updates
* Dynamic calculations

---

# Legacy PHP Debugging Task

A corrected secure PHP MySQLi debugging solution is included in the project root.

Features fixed:

* SQL Injection prevention
* Prepared statements
* Input validation
* Error handling
* Output sanitization

---

# Security Improvements

Implemented:

* Route protection
* Role-based authorization
* Validation rules
* Database transactions
* SQL injection prevention
* XSS-safe output
* Clean code structure

---

# Important Commands

## Clear Config Cache

```bash
php artisan config:clear
```

## Clear Application Cache

```bash
php artisan cache:clear
```

## Clear View Cache

```bash
php artisan view:clear
```

---

# Assumptions

* MySQL service is running locally
* PHP 8.2+ installed
* Node.js and npm installed
* Composer installed
* Vite used for frontend assets

---

# Author

WAQAR HUSSAIN
923463859682
wakkar12@gmail.com

---

# Submission Notes

This project demonstrates:

* Laravel architecture
* Livewire implementation
* Dynamic form handling
* Database normalization
* Migration scripting
* Secure coding practices
* Role-based permissions
* Legacy system modernization

---

# Thank You

Thank you for reviewing this assessment project.
