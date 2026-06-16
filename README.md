<div align="center">
  <img src="public/dashboard/assets/images/default/logo.png" alt="Tagsoft POS" width="180"/>

  <h1>Tagsoft POS</h1>

  <p><strong>A modern, multilingual Point of Sale & ERP system built with Laravel</strong></p>

  <p>
    <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
    <img src="https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white"/>
  </p>

  <p>
    <img src="https://img.shields.io/badge/status-in%20development-orange?style=flat-square"/>
    <img src="https://img.shields.io/badge/license-proprietary-red?style=flat-square"/>
    <img src="https://img.shields.io/badge/language-AR%20%7C%20EN-blue?style=flat-square"/>
  </p>
</div>

---

## 📌 Overview

**Tagsoft POS** is the foundation of a full-featured ERP system, currently in active development.
It is designed to handle multi-branch businesses with full **Arabic & English** language support,
a clean admin dashboard, and a modular architecture that scales as features grow.

> 🚧 This project is in early development — more modules are being added continuously.

---

## ✨ Features

### ✅ Completed Modules

| # | Module | Description |
|---|--------|-------------|
| 1 | ⚙️ **General Settings** | System-wide configuration and preferences |
| 2 | 🏦 **Treasuries** | Main and sub-treasury management |
| 3 | 🏷️ **Sales Invoice Categories** | Categorize and organize sales invoice types |
| 4 | 📐 **Units of Measurement** | Define and manage item measurement units |
| 5 | 🏭 **Warehouses & Stores** | Warehouse creation and management |

### 🔄 In Progress

| # | Module | Status |
|---|--------|--------|
| 6 | 📦 **Item Categories** | 🚧 Under Development |

### 🗓️ Planned

- 📦 Items & Inventory Management
- 🧾 Sales Invoices
- 🛒 Purchase Invoices
- 👥 Customers & Suppliers
- 📊 Reports & Analytics
- 💰 Accounting & Ledger

---

## 🌍 Multilingual Support

The system fully supports **Arabic** and **English** with:

- RTL layout for Arabic
- URL-based locale switching (`/ar/...` and `/en/...`)
- Translatable models using [Astrotomic Laravel Translatable](https://github.com/Astrotomic/laravel-translatable)

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 |
| Frontend | Bootstrap 5, Tabler Icons, DataTables (Yajra) |
| Database | MySQL 8 |
| Auth | Laravel Auth (Admin Guard) |
| i18n | mcamara/laravel-localization + Astrotomic Translatable |
| Charts | ApexCharts |

---

## 🚀 Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/tagsoft-pos.git
cd tagsoft-pos

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install && npm run build

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Configure your database in .env
DB_DATABASE=tagsoft_pos
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations and seeders
php artisan migrate --seed

# 7. Serve the application
php artisan serve
```

---

## 📁 Project Structure

```
app/
├── Enums/              # PHP Enums (Status, Types...)
├── Http/
│   ├── Controllers/    # Dashboard Controllers
│   └── Requests/       # Form Requests & Validation
├── Models/             # Eloquent Models (with Translations)
├── Repositories/
│   ├── Contracts/      # Repository Interfaces
│   └── Eloquents/      # Repository Implementations
└── DataTables/         # Yajra DataTable Classes

resources/
└── views/
    └── dashboard/
        └── admin/      # Blade Views per Module
```

---

## 🔐 Admin Access

After seeding, you can log in with:

```
URL:      http://127.0.0.1:8000/ar/admin/login
Email:    admin@tagsoft.com
Password: 123123
```

---

## 📄 License

This project is proprietary software owned by **Tagsoft**.  
All rights reserved © 2026 Tagsoft.

---

<div align="center">
  <sub>Built with ❤️ by the Tagsoft Team — Mostafa Ali</sub>
</div>