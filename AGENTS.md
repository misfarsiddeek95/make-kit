# AGENTS.md - Make-Kit Codebase Documentation

This file documents the complete codebase structure for AI assistants working on this project.

---

## 1. Project Overview

**Make-Kit (מייק-קיט)** is an educational management system (LMS) with e-commerce capabilities, built for Hebrew-speaking (RTL) educational institutions in Israel.

**Primary Functions:**
- Question bank management (MCQ, Structured, Essay)
- Exam paper generation with PDF export
- Student/Instructor management
- Class/Institute/Subject management
- Product catalog and order management (e-commerce)
- Role-based access control (RBAC)

---

## 2. Technology Stack

| Component | Technology |
|---|---|
| **Framework** | CodeIgniter 3.x |
| **Language** | PHP 7.4.27 |
| **Database** | MariaDB 10.4.21 (MySQL) |
| **Hosting** | XAMPP (Apache) on macOS |
| **Frontend CSS** | Bootstrap 3 + Custom "Cosmos" Theme |
| **JavaScript** | jQuery (extensive AJAX) |
| **PDF Generation** | mPDF |
| **Excel Handling** | PHPExcel |
| **Image Processing** | GD Library (thumbnails: 1400x1400, 500x500, 100x100) |
| **Password Hashing** | bcrypt via `password_hash()` + WordPress-compatible HMAC-SHA384 |
| **Session Storage** | Database-backed (`ci_sessions` table) |

---

## 3. Directory Structure

```
make-kit/
├── index.php                      # Main entry point (front controller)
├── .htaccess                      # Apache URL rewriting
├── makekit_db.sql                 # Database dump (English)
├── makekit_db_hebrew_local.sql    # Database dump (Hebrew - active)
├── *_hebrew.sql                   # Hebrew seed data scripts
├── application/                   # CodeIgniter application code
│   ├── base/                      # Base controllers (Admin_Controller)
│   ├── config/                    # Configuration files
│   ├── controllers/               # 15 controllers
│   ├── core/                      # Core overrides (MY_Controller)
│   ├── helpers/                   # Custom helpers (password_helper)
│   ├── hooks/                     # Hook definitions (MY_Autoloader)
│   ├── libraries/                 # Custom libraries (Aayusmain)
│   ├── models/                    # 15 models
│   ├── third_party/               # PHPExcel, Zend, GeoIP
│   └── views/                     # 46+ view files
│       ├── includes/              # Layout partials (sidebar, topbar, footer, etc.)
│       └── errors/                # Error templates
├── assets/                        # Frontend assets
│   ├── css/                       # cosmos.css (19,709 lines), application.css, vendor.css
│   ├── js/                        # application.js, vendor.js, commonScripts.js
│   ├── fonts/                     # Glyphicons, Material Design Iconic Font
│   ├── img/                       # Images, logos
│   └── cropper/                   # Cropper.js
├── photos/                        # Uploaded photos (products, students, staff)
├── ceated_photos/                 # Default/template photos
├── uploads/                       # CSV import files
└── system/                        # CodeIgniter core framework files
```

---

## 4. Application Architecture

### Controller Hierarchy

```
CI_Controller (CodeIgniter core)
  └── MY_Controller (application/core/MY_Controller.php)
        └── Admin_Controller (application/base/Admin_Controller.php)
```

**MY_Controller:**
- Sets company name ("Make-Kit | ")
- Sets timezone to `Asia/Colombo`
- Provides `get_encrypted_password()` utility (bcrypt, cost 10)

**Admin_Controller:**
- Extends `MY_Controller`
- Enforces authentication (redirects to login if no session)
- Loads access group permissions via `Group_options_modal`
- Sets currency symbol (`₪`)
- Sets document root folder path

### Controllers (15 total)

| Controller | Extends | Responsibility |
|---|---|---|
| `Welcome` | `MY_Controller` | Landing page / login redirect |
| `SystemLogin` | `MY_Controller` | Authentication (sign-in, sign-out) |
| `AdminMain` | `Admin_Controller` | Admin dashboard with statistics |
| `Users` | `Admin_Controller` | Staff user management (CRUD) |
| `ExternalUsers` | `Admin_Controller` | Students and Instructors management |
| `Products` | `Admin_Controller` | Product catalog management |
| `Orders` | `Admin_Controller` | Order management |
| `Customers` | `Admin_Controller` | Customer management |
| `Settings` | `Admin_Controller` | System settings (categories, attributes, brands, pages) |
| `GroupOptions` | `Admin_Controller` | Access group/permission management |
| `OtherOptions` | `Admin_Controller` | Currency rates, coupons, discounts |
| `Academic` | `Admin_Controller` | Classes, subjects, assignments |
| `Questionnaire` | `Admin_Controller` | Question bank, exam paper generation |
| `Reports` | `Admin_Controller` | Student reports |

### Models (15 total)

| Model | File | Purpose |
|---|---|---|
| `Common_modal` | `Common_modal.php` | Generic CRUD, photo management, category trees |
| `Admin_modal` | `Admin_modal.php` | Dashboard stats, access rights, user management |
| `System_login` | `System_login.php` | Login authentication queries |
| `Product_modal` | `Product_modal.php` | Product data operations |
| `Order_modal` | `Order_modal.php` | Order data operations |
| `Customers_Modal` | `Customers_Modal.php` | Customer data operations |
| `Settings_modal` | `Settings_modal.php` | Settings/configuration operations |
| `Group_options_modal` | `Group_options_modal.php` | Access group and permission operations |
| `Other_modal` | `Other_modal.php` | Currency, coupon operations |
| `Academic_model` | `Academic_model.php` | Academic (class/subject/teacher) operations |
| `ExternalUser_model` | `ExternalUser_model.php` | Student and instructor operations |
| `Questionnaire_Model` | `Questionnaire_Model.php` | Question bank and exam paper operations |
| `Access_groups_modal` | `Access_groups_modal.php` | Access group CRUD |
| `Reports_model` | `Reports_model.php` | Report generation queries |

**Note:** Models use "Modal" naming convention (typo used consistently).

---

## 5. Layout Structure (CRITICAL FOR UI CHANGES)

### HTML Wrapper Structure

Every page (except `login.php`) follows this exact DOM hierarchy:

```html
<html lang="en">  <!-- NOTE: Currently LTR, needs dir="rtl" for Hebrew -->
  <head>
    <?php $this->load->view('includes/head'); ?>
  </head>
  <body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
      <?php $this->load->view('includes/sidebar'); ?>
      <div class="site-content">
        <!-- PAGE CONTENT HERE -->
      </div>
      <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
  </body>
</html>
```

### Layout Component Positions (CSS-driven)

| Component | CSS Class | Position | Width |
|---|---|---|---|
| **Sidebar** | `.site-left-sidebar` | `position: absolute; left: 0; top: 0; bottom: 0` | 220px (60px collapsed) |
| **Content** | `.site-content` | `margin-left: 220px` | Remaining width |
| **Footer** | `.site-footer` | `margin-left: 220px` | Remaining width |
| **Navbar** | `.navbar-collapsible` | `margin-left: 220px` | Remaining width |
| **Backdrop** | `.sidebar-backdrop` | `position: fixed; left: 0; border-right` | 220px |
| **Header** | `.site-header` | `position: fixed; top: 0; width: 100%` | Full width |

### Key CSS Files

| File | Path | Lines | Purpose |
|---|---|---|---|
| **cosmos.css** | `assets/css/cosmos.css` | 19,709 | PRIMARY - All layout rules, sidebar positioning, grid system, utilities |
| **application.css** | `assets/css/application.css` | 99 | Custom app styles (no layout rules) |
| **vendor.css** | `assets/css/vendor.css` | 10,529 | animate.css animations |
| **waitMe.css** | `assets/css/waitMe.css` | 233 | Loading spinner |

**The only CSS file that matters for layout changes is `cosmos.css`.**

### JavaScript Files

| File | Path | Purpose |
|---|---|---|
| **application.js** | `assets/js/application.js` | Main layout controller - sidebar toggle/collapse logic |
| **vendor.js** | `assets/js/vendor.js` | Bundled libraries (jQuery, Bootstrap, DataTables) |

**Important:** `application.js` only toggles CSS classes, NOT directional properties. All directional behavior is CSS-driven.

---

## 6. Database Structure

### Connection Details

| Setting | Value |
|---|---|
| Host | `localhost` |
| User | `root` |
| Password | (empty) |
| Database | `makekit_db_hebrew_local` |
| Driver | `mysqli` |
| Charset | `utf8mb4` |

### Tables (60 total)

#### Access Control & Users (6 tables)

| Table | Purpose |
|---|---|
| `access_groups` | Permission groups (Super Admin, Instructors, Students) |
| `staff_users` | Admin/staff user accounts |
| `external_users` | Students & Instructors |
| `social_media` | OAuth social login accounts |
| `password_resets` | Password reset tokens |
| `ci_sessions` | CodeIgniter database sessions |

#### RBAC & Menu System (3 tables)

| Table | Purpose |
|---|---|
| `system_options` | Menu tree (157 items) with hierarchy |
| `group_progs` | Maps access groups to permitted menu items |
| `staff_sites` | Staff-to-website mapping |

#### Geography (3 tables)

| Table | Rows | Purpose |
|---|---|---|
| `country` | 239 | Countries with ISO codes |
| `regions` | ~3,889 | Regions/states |
| `cities` | ~2.5M | Cities with lat/lng |

#### E-Commerce Products (12 tables)

| Table | Purpose |
|---|---|
| `products` | Product catalog |
| `sub_product` | Product variants (SKUs) |
| `sub_pro_sepc` | Variant attribute specs |
| `attributes` | Attribute definitions (dropdown, multi-select, color) |
| `attribute_value` | Attribute values |
| `categories` | Hierarchical categories (self-referencing) |
| `category_attributes` | Category-attribute mapping |
| `category_brands` | Category-brand mapping |
| `brands` | Brand management |
| `product_attr_val` | Product attribute values |
| `product_categories` | Product-category mapping |
| `product_available_sites` | Product site availability |

#### E-Commerce Orders & Customers (8 tables)

| Table | Purpose |
|---|---|
| `orders` | Order header |
| `order_details` | Order line items |
| `order_payment_det` | Payment records |
| `order_product_specs` | Order item specifications |
| `order_statuses` | Status definitions |
| `order_status_det` | Order status history |
| `customers` | Customer accounts |
| `addresses` | Address book |

#### E-Commerce Support (5 tables)

| Table | Purpose |
|---|---|
| `coupons` | Coupon management |
| `delivery_charges` | Shipping rates |
| `discount_list` | Discount definitions |
| `product_discount` | Product-discount mapping |
| `credity_type` | Credit/payment types |

#### Currency (2 tables)

| Table | Purpose |
|---|---|
| `currency` | 112 currencies |
| `country_currency` | Country-currency mapping |

#### Academic / Education (12 tables)

| Table | Purpose |
|---|---|
| `class` | Institute/class definitions |
| `subjects` | Subject definitions |
| `class_subjects` | Class-subject mapping |
| `classsec_for_teacher` | Class section for teacher |
| `classec_teacher` | Section-teacher mapping |
| `subject_assign` | Teacher-subject assignment |
| `exam_types` | Exam type categories |
| `question_type` | MCQ, Structured, Essay |
| `questions` | Question bank |
| `question_answers` | Answer options |
| `question_paper_main` | Exam paper definitions |
| `question_paper_child` | Questions in papers |

#### Student Assessment (3 tables)

| Table | Purpose |
|---|---|
| `student_attempts` | Exam attempt tracking |
| `student_answers` | Student answer records |
| `student_points` | Points/grades |

#### CMS & Content (4 tables)

| Table | Purpose |
|---|---|
| `pages` | Pages, sliders, banners, galleries |
| `photo` | Polymorphic photo storage |
| `customer_messages` | Contact form messages |
| `subscription_email` | Newsletter subscribers |

#### Websites (1 table)

| Table | Purpose |
|---|---|
| `website` | Multi-site definitions (3 sites) |

---

## 7. Configuration Files

| File | Key Settings |
|---|---|
| `application/config/config.php` | `base_url` = `http://localhost/make-kit/`, timezone = `Asia/Colombo`, session driver = `database`, CSRF disabled, encryption key empty |
| `application/config/database.php` | MySQL on localhost, root, no password, database `makekit_db_hebrew_local`, `mysqli` driver |
| `application/config/routes.php` | 170+ custom routes, default controller = `Welcome`, 404 = `Welcome/not_found` |
| `application/config/autoload.php` | Libraries: `database`, `email`, `session`, `cart`. Helpers: `url`, `file` |
| `application/config/hooks.php` | Registers `MY_Autoloader` as `pre_system` hook |

---

## 8. Key Routes

| Route | Controller/Method |
|---|---|
| `/` | `Welcome::index()` |
| `/sign-in` | `SystemLogin::signin()` |
| `/back-office` | `AdminMain::index()` |
| `/logout` | `SystemLogin::logout()` |
| `/profile` | `Users::user_profile()` |

---

## 9. Hebrew Localization

- UI is entirely in **Hebrew** (RTL language)
- 9 SQL scripts apply Hebrew translations to lookup tables
- `cities` table has Hebrew city names for Israel
- `regions` table has Hebrew region names
- Currency symbol: `₪` (New Israeli Shekel)

---

## 10. Known Issues & Considerations

### Security Issues
- CSRF protection is **disabled**
- Encryption key is **empty**
- Database credentials use root with **no password**
- XSS filtering is disabled globally

### Technical Debt
- Model files use "Modal" naming convention (typo)
- Mixed charsets: most tables `utf8mb4`, some academic tables `latin1`
- Mixed storage engines: 42 InnoDB, 18 MyISAM
- No migrations - schema managed via raw SQL dumps
- No `composer.json` or `package.json` - dependencies manually managed
- No automated tests

### Layout Issues
- `<html lang="en">` needs `dir="rtl"` for proper Hebrew support
- Sidebar is positioned on the LEFT via CSS (`left: 0`)
- Content area uses `margin-left: 220px` to accommodate sidebar
- All directional CSS is in `cosmos.css` (lines 14056-15364)

---

## 11. Common Patterns

### View Loading Pattern
```php
<?php $this->load->view('includes/head'); ?>
<?php $this->load->view('includes/topbar'); ?>
<div class="site-main">
  <?php $this->load->view('includes/sidebar'); ?>
  <div class="site-content">
    <!-- Content -->
  </div>
</div>
```

### AJAX Response Pattern
Controllers return JSON for AJAX requests:
```php
echo json_encode(['status' => 'success', 'message' => '...']);
```

### Authentication Check
All admin controllers extend `Admin_Controller` which checks session:
```php
if($this->session->userdata('staff_logged_in') == null) {
    redirect(base_url());
}
```

---

## 12. File Locations Reference

| Description | Absolute Path |
|---|---|
| Main entry point | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/index.php` |
| Application config | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/config/config.php` |
| Database config | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/config/database.php` |
| Routes | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/config/routes.php` |
| Base controller (auth) | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/base/Admin_Controller.php` |
| Core controller | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/core/MY_Controller.php` |
| Login controller | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/controllers/SystemLogin.php` |
| Dashboard controller | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/controllers/AdminMain.php` |
| Questionnaire controller | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/controllers/Questionnaire.php` |
| Common model | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/models/Common_modal.php` |
| Sidebar view | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/views/includes/sidebar.php` |
| Topbar view | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/views/includes/topbar.php` |
| Dashboard view | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/application/views/dashboard.php` |
| Main CSS (layout) | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/assets/css/cosmos.css` |
| Main JS (layout) | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/assets/js/application.js` |
| Database schema | `/Applications/XAMPP/xamppfiles/htdocs/make-kit/makekit_db_hebrew_local.sql` |

---

## 13. RTL Layout Notes

### Current State (LTR)
- Sidebar positioned at `left: 0`
- Content has `margin-left: 220px`
- Navbar has `margin-left: 220px`
- `<html lang="en">` (no `dir` attribute)

### Required for RTL
To move sidebar to right side:
1. Change `.site-left-sidebar { left: 0 }` → `right: 0`
2. Change `.site-content { margin-left: 220px }` → `margin-right: 220px`
3. Change `.site-footer { margin-left: 220px }` → `margin-right: 220px`
4. Change `.navbar-collapsible { margin-left: 220px }` → `margin-right: 220px`
5. Change `.sidebar-backdrop { left: 0; border-right }` → `right: 0; border-left`
6. Flip sidebar menu internals (float, margin, padding, border)
7. Add `dir="rtl"` to all view files
8. Flip topbar toggle button classes (`pull-left` → `pull-right`)

**All CSS changes are in `cosmos.css` lines 14056-15364.**

---

*Last updated: 2026-07-14*
