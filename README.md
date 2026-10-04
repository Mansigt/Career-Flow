# CareerFlow — Smart Job Application & Follow-up Tracker

> A modern, clean, and beginner-friendly Laravel web application designed to help job seekers manage and track their job applications, interview pipelines, and critical follow-up deadlines in one place.

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Key Features](#key-features)
3. [Tech Stack](#tech-stack)
4. [Database Structure & Schema](#database-structure--schema)
5. [Installation & Setup Guide](#installation--setup-guide)
6. [Environment Configuration](#environment-configuration)
7. [Running Migrations & Seeders](#running-migrations--seeders)
8. [Running the Application](#running-the-application)
9. [Demo Credentials](#demo-credentials)
10. [Core Laravel Concepts Explained](#core-laravel-concepts-explained)
11. [Django to Laravel: A Comparative Guide](#django-to-laravel-a-comparative-guide)
12. [Running Automated Tests](#running-automated-tests)
13. [Project Directory Layout](#project-directory-layout)

---

## Project Overview

**CareerFlow** is built with modern PHP and Laravel, showcasing best practices in MVC architecture, Eloquent ORM modeling, database migrations, server-side validation, authentication, and cross-user data isolation. 


---

## Key Features

- **User Authentication & Session Management**:
  - Secure registration, login, and logout using hashed passwords (`bcrypt`).
  - Strict user-level data isolation (each user can only view, create, edit, or delete their own applications).
  - Protection against URL parameter tampering (trying to access another user's application ID returns `403 Forbidden`).

- **Dashboard & Analytics**:
  - Real-time statistics counters: Total Applications, Applied, Shortlisted, Interview, Selected, and Rejected.
  - **Smart Follow-up Reminders**: Highlights applications with follow-up dates due today or overdue (excluding closed ones like Selected/Rejected).
  - Recent Applications table showing the 5 most recent submissions.

- **Comprehensive Application Management (CRUD)**:
  - Add new job applications with comprehensive metadata (Company, Position, Location, Job Type, Salary, Applied Date, Follow-up Date, Status, Job URL, Notes).
  - Detailed view page displaying all application specifics and formatted timestamps.
  - Edit applications with pre-populated form fields and error highlights.
  - Delete applications with user confirmation prompt.

- **Search & Filter Pipeline**:
  - Search applications instantly by company name or job position.
  - Filter applications by recruitment status (Applied, Shortlisted, Interview, Selected, Rejected).
  - One-click filter reset button.
  - Paginated results preserving query parameters across pages.

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 11.x (PHP 8.2+) |
| **Language** | PHP 8.2 |
| **Database** | MySQL 8.0 |
| **ORM** | Laravel Eloquent ORM |
| **Templating Engine** | Blade |
| **Frontend Styling** | Bootstrap 5.3 & Bootstrap Icons |
| **Testing** | PHPUnit 10.x |
| **Dependency Manager** | Composer 2.x |

---

## Database Structure & Schema

The application relies on relational MySQL tables created via Laravel database migrations.

### Tables & Relationships

```
+----------------+          +-------------------------+
|     users      | 1      * |      applications       |
+----------------+----------+-------------------------+
| id (PK)        |          | id (PK)                 |
| name           |          | user_id (FK -> users.id)|
| email (UQ)     |          | company                 |
| password       |          | position                |
| remember_token |          | location                |
| created_at     |          | job_type                |
| updated_at     |          | salary                  |
+----------------+          | applied_date            |
                            | follow_up_date          |
                            | status                  |
                            | job_url                 |
                            | notes                   |
                            | created_at              |
                            | updated_at              |
                            +-------------------------+
```

### Foreign Key Constraints
- `applications.user_id` references `users.id` with `ON DELETE CASCADE`. If a user account is deleted, all their applications are automatically removed.

### Enum Values & Field Specifications
- **Status options**: `Applied`, `Shortlisted`, `Interview`, `Selected`, `Rejected`
- **Job Type options**: `Full-time`, `Part-time`, `Internship`, `Contract`
- **Salary**: Nullable numeric decimal (`12, 2`)
- **Dates**: `applied_date` (required date), `follow_up_date` (optional date)
- **Indexes**: Composite indexes on `(user_id, status)` and `(user_id, follow_up_date)` for fast query execution.

---

## Installation & Setup Guide

### Prerequisites
1. **PHP 8.2+** with extensions enabled: `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `zip`.
2. **Composer 2.x** installed.
3. **MySQL 8.0+** running locally.

### Steps
1. Navigate to the project root:
   ```bash
   cd C:\Users\lenovo\Desktop\php_project\career_flow
   ```

2. Install dependencies via Composer:
   ```bash
   composer install
   ```

3. Ensure `.env` is created (if not already present):
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

---

## Environment Configuration

Open `.env` and configure your MySQL database credentials:

```env
APP_NAME=CareerFlow
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=career_flow
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

SESSION_DRIVER=database
```

---

## Running Migrations & Seeders

Create the database in MySQL (if it doesn't exist yet):
```sql
CREATE DATABASE IF NOT EXISTS career_flow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations and seed the database with realistic sample data:
```bash
php artisan migrate:fresh --seed
```

This will run all migrations and populate the database with demo users, realistic job applications, interview notes, and follow-up deadlines.

---

## Running the Application

Start the built-in development server:
```bash
php artisan serve
```

The application will be live at:
**[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## Demo Credentials

The database seeder pre-configures two users to easily test all features and verify cross-user isolation:

### Primary Portfolio User
- **Email**: `demo@careerflow.test`
- **Password**: `password123`
- *Contains 6 realistic applications across all statuses, including an interview follow-up due today and an overdue reminder.*

### Secondary Test User (for authorization verification)
- **Email**: `other@careerflow.test`
- **Password**: `password123`
- *Contains 1 separate private application to test that users cannot see or modify each other's data.*

---

## Core Laravel Concepts Explained

1. **MVC (Model-View-Controller)**:
   - **Model (`app/Models/Application.php`)**: Encapsulates data structure, business logic, date casting, and database relationships.
   - **View (`resources/views/`)**: Blade templates responsible for rendering HTML, form inputs, and status badges.
   - **Controller (`app/Http/Controllers/ApplicationController.php`)**: Coordinates incoming HTTP requests, interacts with the model, and selects the appropriate view.

2. **Routing (`routes/web.php`)**:
   - Maps URL endpoints and HTTP verbs (`GET`, `POST`, `PUT`, `DELETE`) to controller actions.
   - Uses named routes (e.g. `route('applications.show', $application)`) for flexible refactoring.

3. **Controllers & RESTful Resources**:
   - `ApplicationController` implements standard CRUD actions: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.

4. **Eloquent ORM & Relationships**:
   - Object-Relational Mapper that maps database tables to PHP classes.
   - `User` has a `hasMany(Application::class)` relationship.
   - `Application` has a `belongsTo(User::class)` relationship.
   - Queries like `$request->user()->applications()->create(...)` ensure data is automatically linked to the logged-in user.

5. **Migrations**:
   - Version control for the database schema. Migrations allow creating, modifying, and rolling back tables using pure PHP.

6. **Middleware**:
   - Filters HTTP requests before they reach the controller. The `auth` middleware protects private pages and redirects unauthenticated users to `/login`.

7. **Authentication**:
   - Managed via Laravel's built-in `Auth` facade (`Auth::attempt`, `Auth::login`, `Auth::logout`) providing secure session handling.

8. **Server-Side Validation (`FormRequest`)**:
   - Handled cleanly via `ApplicationRequest`, isolating validation rules and custom error messages from controller logic.

9. **Blade Templating Engine**:
   - Supports template inheritance (`@extends`, `@section`, `@yield`), partials, components, loops (`@foreach`), conditionals (`@if`, `@auth`), and automatic XSS protection (`{{ $variable }}`).

10. **Route Model Binding**:
    - Automatically injects the `Application` model instance based on the ID passed in the route URL (`applications/{application}`).

---

## Django to Laravel: A Comparative Guide

For developers with a background in Python and Django, this table maps equivalent concepts:

| Concept | Python / Django | PHP / Laravel | CareerFlow Example |
|---|---|---|---|
| **URL Routing** | `urls.py` (`path('apps/', views.list)`) | `routes/web.php` (`Route::get(...)`) | `Route::resource('applications', ...)` |
| **Controllers / Views** | `views.py` (FBVs or CBVs) | `app/Http/Controllers/` | `ApplicationController@index` |
| **Models & ORM** | `models.Model`, Django ORM | `Illuminate\Database\Eloquent\Model` | `class Application extends Model` |
| **Relationships** | `models.ForeignKey(User, on_delete=models.CASCADE)` | `$this->belongsTo(User::class)` | `User::applications()` & `Application::user()` |
| **Database Migrations** | `makemigrations` / `migrate` | `make:migration` / `artisan migrate` | `2026_10_02_create_applications_table.php` |
| **Templating Engine** | Django DTL (`{% block %}`, `{{ var }}`) | Blade (`@section`, `@yield`, `{{ $var }}`) | `resources/views/layouts/app.blade.php` |
| **Route Protection** | `@login_required` decorator | `middleware('auth')` | `Route::middleware('auth')->group(...)` |
| **Form Validation** | `forms.ModelForm` or `serializers` | `FormRequest` or `$request->validate()` | `app/Http/Requests/ApplicationRequest.php` |
| **Password Hashing** | `make_password()` / PBKDF2 | `Hash::make()` / Bcrypt | `Hash::make('password123')` |
| **CLI Tool** | `python manage.py` | `php artisan` | `php artisan serve`, `php artisan test` |
| **Dependency Manager** | `pip` + `requirements.txt` | `composer` + `composer.json` | `composer install` |

---

## Running Automated Tests

CareerFlow includes a comprehensive automated test suite (23 test cases with 73 assertions) testing Authentication, CRUD operations, Search, Status filtering, Follow-up reminders, and cross-user authorization security.

Run the test suite:
```bash
php artisan test
```

### Test Coverage Highlights
- **Authentication**: Registration validation, login credentials, logout, guest route redirection.
- **Application CRUD**: Creating, reading, updating, and deleting applications.
- **Form Validation**: Strict checks for required fields, valid dates, URLs, and allowed enum values.
- **Search & Filtering**: Querying by company and position; filtering by status.
- **Follow-up Reminders**: Correct calculation of pending follow-ups.
- **Cross-User Authorization**: Verifies that User A cannot view, edit, update, or delete User B's applications (asserts `403 Forbidden`).

---

## Project Directory Layout

```
career_flow/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ApplicationController.php  # CRUD, search, filter, ownership checks
│   │   │   ├── AuthController.php         # Registration, login, logout logic
│   │   │   └── DashboardController.php    # Stats, reminders, recent applications
│   │   └── Requests/
│   │       └── ApplicationRequest.php     # Server-side validation rules & messages
│   └── Models/
│       ├── Application.php                # Eloquent model, scopes, badge helpers
│       └── User.php                       # User model with hasMany(Application)
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   └── 2026_10_02_121117_create_applications_table.php
│   └── seeders/
│       └── DatabaseSeeder.php             # Seeds demo users & realistic applications
├── resources/
│   └── views/
│       ├── auth/
│       │   ├── login.blade.php            # Login card
│       │   └── register.blade.php         # Registration card
│       ├── applications/
│       │   ├── create.blade.php           # Add application form
│       │   ├── edit.blade.php             # Edit application form
│       │   ├── index.blade.php            # Searchable, filterable list table
│       │   └── show.blade.php             # Detailed view card & notes
│       ├── dashboard.blade.php            # Metrics, due reminders, recent apps
│       └── layouts/
│           └── app.blade.php              # Base layout with Bootstrap 5 navbar & alerts
├── routes/
│   └── web.php                            # Public, guest, and auth-protected routes
├── tests/
│   └── Feature/
│       ├── ApplicationTest.php            # 13 tests covering CRUD, search, authz
│       └── AuthTest.php                   # 8 tests covering auth flows
├── .env                                   # MySQL and app configuration
└── composer.json                          # PHP dependencies
```
