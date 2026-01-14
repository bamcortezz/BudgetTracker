# 🌸 Budget Tracker | Bloom

<div align="center">

**A beautiful, modern budget tracking application built with PHP and MySQL**

_"Bloom where you are planted" - Track your financial journey with elegance_

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind-3.0+-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

</div>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [Project Structure](#project-structure)
- [Usage](#usage)
- [Security Features](#security-features)
- [API Endpoints](#api-endpoints)
- [Future Improvements](#future-improvements)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 Overview

Budget Tracker is a full-featured personal finance management application that helps users track income and expenses with beautiful visualizations. Built with a clean MVC architecture, it provides a secure and intuitive way to manage your financial life.

### Why Budget Tracker?

- 🎨 **Beautiful UI** - Modern, responsive design with Tailwind CSS
- 🔒 **Secure** - CSRF protection, password hashing, SQL injection prevention
- 📊 **Visual Analytics** - Interactive charts powered by Chart.js
- 🚀 **Fast & Lightweight** - No heavy frameworks, pure PHP MVC
- 📱 **Mobile Friendly** - Responsive design works on all devices

---

## ✨ Features

### Core Features

- ✅ **User Authentication**

  - Secure registration with password hashing (bcrypt)
  - Login/logout functionality
  - Session-based authentication
  - CSRF token protection

- 💰 **Transaction Management**

  - Add income and expense transactions
  - Categorized transactions
  - Soft delete functionality
  - Transaction history with pagination (15 per page)
  - Real-time balance calculation

- 📊 **Dashboard Analytics**

  - Total balance, income, and expense overview
  - Expense breakdown by category (Pie Chart)
  - Income vs Expenses comparison (Bar Chart)
  - Recent transactions display
  - Category-based spending analysis

- 🎯 **Category System**
  - Pre-defined categories (Salary, Freelance, Food, Transport, etc.)
  - Category-based filtering and analytics

### User Interface

- 🌹 Rose-themed elegant design
- 🔔 Toast notifications for user feedback
- ⚠️ SweetAlert2 confirmations for critical actions
- 👁️ Password visibility toggle
- 📄 Paginated transaction views
- 🎨 Smooth animations and transitions

---

## 🛠️ Tech Stack

### Backend

- **PHP 8.0+** - Server-side logic
- **MySQL 8.0+** - Database management
- **PDO** - Database abstraction layer

### Frontend

- **HTML5** - Markup structure
- **Tailwind CSS** - Utility-first styling
- **JavaScript (ES6+)** - Client-side interactivity
- **Chart.js** - Data visualization
- **Font Awesome** - Icon library

### Libraries & Tools

- **SweetAlert2** - Beautiful alert modals
- **Toastify JS** - Toast notifications
- **XAMPP** - Local development environment

---

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.0 or higher**
- **MySQL 8.0 or higher**
- **Apache Web Server** (included in XAMPP)
- **Composer** (optional, for future dependency management)
- **Git** (for version control)

### Recommended Setup

- **XAMPP** - All-in-one package with PHP, MySQL, and Apache
  - Download: https://www.apachefriends.org/

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/budget-tracker.git
cd budget-tracker
```

### 2. Configure Web Server

#### For XAMPP (macOS)

```bash
# Move project to XAMPP htdocs
mv budget-tracker /Applications/XAMPP/xamppfiles/htdocs/

# Start XAMPP
sudo /Applications/XAMPP/xamppfiles/xampp start
```

#### For XAMPP (Windows)

```bash
# Move project to XAMPP htdocs
# Default location: C:\xampp\htdocs\

# Start XAMPP Control Panel
# Start Apache and MySQL services
```

### 3. Set Permissions (macOS/Linux)

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/budget-tracker
chmod -R 755 .
chmod -R 777 config/  # For error.log writing
```

---

## 🗄️ Database Setup

### 1. Create Database

Open phpMyAdmin: `http://localhost/phpmyadmin`

Or use MySQL CLI:

```sql
CREATE DATABASE budget_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE budget_tracker;
```

### 2. Create Tables

#### Users Table

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Categories Table

```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Transactions Table

```sql
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    status ENUM('active', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_user_date (user_id, date),
    INDEX idx_user_type (user_id, type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Insert Default Categories

```sql
INSERT INTO categories (name) VALUES
('Salary'),
('Freelance'),
('Investment'),
('Food'),
('Transport'),
('Entertainment'),
('Shopping'),
('Bills'),
('Healthcare'),
('Education'),
('Other');
```

---

## ⚙️ Configuration

### 1. Database Configuration

Edit `config/Database.php`:

```php
private $db_name = "budget_tracker";
private $username = "root";
private $password = "";  // Your MySQL password
private $socket = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";
```

**For Windows users:**

```php
private $host = "localhost";
private $port = "3306";
// Use host/port instead of socket
```

### 2. Session Configuration

Sessions are automatically configured in `config/Config.php`:

- Error logging enabled
- Errors logged to `config/error.log`
- Display errors disabled in production
- Timezone set to UTC

### 3. Environment Variables (Recommended)

Create `.env` file in project root:

```env
DB_HOST=localhost
DB_NAME=budget_tracker
DB_USER=root
DB_PASS=
DB_SOCKET=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock
APP_ENV=development
APP_DEBUG=true
```

---

## 📁 Project Structure

```
BudgetTracker/
├── assets/
│   └── js/
│       ├── dashboard.js          # Dashboard functionality
│       ├── toast.js              # Toast notifications
│       └── togglePassword.js     # Password visibility toggle
├── config/
│   ├── Config.php                # App configuration
│   ├── Database.php              # Database connection
│   └── error.log                 # Error log file
├── controller/
│   ├── TransactionController.php # Transaction business logic
│   └── UserController.php        # User authentication logic
├── middleware/
│   └── AuthMiddleware.php        # Authentication guard
├── model/
│   ├── Category.php              # Category data access
│   ├── Transaction.php           # Transaction data access
│   └── User.php                  # User data access
├── partials/
│   ├── Foot.php                  # Footer partial
│   ├── Head.php                  # Header/meta partial
│   └── Navbar.php                # Navigation bar partial
├── routes/
│   ├── TransactionRoutes.php     # Transaction API routes
│   └── UserRoutes.php            # User API routes
├── utils/
│   ├── CsrfUtil.php              # CSRF token management
│   └── ResponseUtil.php          # JSON response helper
├── tables/                        # Database schema files (ignored)
├── dashboard.php                  # Main dashboard view
├── index.php                      # Landing page
├── login.php                      # Login page
├── register.php                   # Registration page
├── transactions.php               # Transaction history page
├── .gitignore                     # Git ignore rules
└── README.md                      # This file
```

### Architecture Pattern: MVC

- **Models** (`model/`) - Data access and business logic
- **Views** (`*.php` pages) - User interface
- **Controllers** (`controller/`) - Request handling and coordination
- **Routes** (`routes/`) - API endpoint definitions
- **Middleware** (`middleware/`) - Authentication and request filtering
- **Utils** (`utils/`) - Helper functions and utilities

---

## 💻 Usage

### 1. Access the Application

Open your browser and navigate to:

```
http://localhost/budget-tracker/
```

### 2. Register an Account

1. Click "Get Started" or "Register"
2. Fill in your details:
   - Username
   - Email address
   - Password (min 6 characters recommended)
   - Confirm password
3. Click "Create Account"

### 3. Login

1. Navigate to Login page
2. Enter your email and password
3. Click "Sign In"

### 4. Add Transactions

1. From Dashboard, click "+ Add New" button
2. Fill in transaction details:
   - Type: Income or Expense
   - Amount: Transaction value
   - Category: Select from dropdown
   - Description: Optional note
   - Date: Transaction date (defaults to today)
3. Click "Save Transaction"

### 5. View Analytics

- **Dashboard**: Overview with charts and recent transactions
- **All Transactions**: Complete history with pagination
- Balance, income, and expense cards update automatically

### 6. Delete Transactions

1. Click trash icon on any transaction
2. Confirm deletion in popup
3. Transaction is soft-deleted (status = 'deleted')

---

## 🔒 Security Features

### Implemented Security Measures

✅ **Authentication & Authorization**

- Session-based user authentication
- Password hashing with bcrypt (PASSWORD_BCRYPT)
- Auth middleware protecting sensitive routes
- Automatic redirection for unauthenticated users

✅ **CSRF Protection**

- Token generation for all forms
- Token verification on all POST requests
- Tokens stored in session

✅ **SQL Injection Prevention**

- PDO prepared statements with parameter binding
- No raw SQL queries with user input
- Type casting for integer parameters

✅ **XSS Prevention**

- `htmlspecialchars()` on all user-generated content
- Output encoding in templates
- Safe JSON responses

✅ **Error Handling**

- Errors logged to file, not displayed
- Generic error messages to users
- Detailed logs for debugging

✅ **Data Validation**

- Server-side input validation
- Type checking with `filter_var()`
- Email format validation
- Amount validation (positive numbers)

✅ **Soft Deletes**

- Transactions marked as deleted, not removed
- Data recovery possible
- Audit trail maintained

### Security Best Practices Applied

- ✅ Separation of concerns (MVC)
- ✅ No sensitive data in version control
- ✅ HTTP response codes properly used
- ✅ Database connection error handling
- ✅ Clean code structure
- ✅ Consistent naming conventions

---

## 🌐 API Endpoints

### User Routes (`routes/UserRoutes.php`)

#### Register User

```
POST /routes/UserRoutes.php
Body:
{
  "register": "1",
  "username": "string",
  "email": "string",
  "password": "string",
  "confirm_password": "string",
  "csrf_token": "string"
}
```

#### Login

```
POST /routes/UserRoutes.php
Body:
{
  "login": "1",
  "email": "string",
  "password": "string",
  "csrf_token": "string"
}
```

#### Logout

```
POST /routes/UserRoutes.php
Body:
{
  "logout": "1"
}
```

### Transaction Routes (`routes/TransactionRoutes.php`)

#### Add Transaction

```
POST /routes/TransactionRoutes.php
Body:
{
  "add_transaction": "1",
  "type": "income|expense",
  "amount": "number",
  "category_id": "integer",
  "description": "string",
  "date": "YYYY-MM-DD",
  "csrf_token": "string"
}
```

#### Delete Transaction

```
POST /routes/TransactionRoutes.php
Body:
{
  "delete_transaction": "1",
  "id": "integer",
  "csrf_token": "string"
}
```

### Response Format

#### Success Response

```json
{
  "status": "success",
  "message": "Operation successful",
  "data": {}
}
```

#### Error Response

```json
{
  "status": "error",
  "message": "Error description",
  "data": null
}
```

---

## 🚧 Future Improvements

### Planned Features

- [ ] **Transaction Editing** - Modify existing transactions
- [ ] **Password Reset** - Email-based password recovery
- [ ] **Email Verification** - Verify user email addresses
- [ ] **Budget Goals** - Set and track spending limits
- [ ] **Recurring Transactions** - Automate regular income/expenses
- [ ] **Export Data** - CSV/PDF export functionality
- [ ] **Multi-Currency Support** - Handle different currencies
- [ ] **Dark Mode** - Toggle between light/dark themes
- [ ] **Notifications** - Budget alerts and reminders
- [ ] **Search & Filter** - Advanced transaction filtering

### Technical Improvements

- [ ] **Environment Variables** - Use `.env` for configuration
- [ ] **Rate Limiting** - Prevent brute force attacks
- [ ] **Password Strength Meter** - Visual password strength indicator
- [ ] **Input Validation Layer** - Centralized validation
- [ ] **Unit Tests** - PHPUnit test suite
- [ ] **API Documentation** - OpenAPI/Swagger docs
- [ ] **Composer Autoloading** - PSR-4 autoloader
- [ ] **Database Migrations** - Version control for schema
- [ ] **Caching Layer** - Redis/Memcached integration
- [ ] **Docker Support** - Containerized deployment
- [ ] **CI/CD Pipeline** - Automated testing and deployment

---

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/AmazingFeature`)
3. **Commit your changes** (`git commit -m 'Add some AmazingFeature'`)
4. **Push to the branch** (`git push origin feature/AmazingFeature`)
5. **Open a Pull Request**

### Development Guidelines

- Follow PSR-12 coding standards
- Write descriptive commit messages
- Add comments for complex logic
- Test your changes thoroughly
- Update documentation as needed

---

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

---

## 👨‍💻 Author

**BAM Cortez**

- GitHub: [@bamcortez](https://github.com/bamcortez)

---

## 🙏 Acknowledgments

- **Tailwind CSS** - For the beautiful utility-first CSS framework
- **Chart.js** - For the interactive charts
- **Font Awesome** - For the icon library
- **SweetAlert2** - For the elegant alert modals
- **Toastify JS** - For the toast notifications
- **PHP Community** - For excellent documentation and support

---

## 📞 Support

If you have any questions or need help, please:

1. Check the [Issues](https://github.com/bamcortez/budget-tracker/issues) page
2. Open a new issue if your question hasn't been answered
3. Reach out via email: your.email@example.com

---

<div align="center">

**Made with ❤️ and PHP**

_"Bloom where you are planted"_ 🌸

⭐ Star this repo if you find it helpful!

</div>
