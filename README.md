# HR-PHP Project Documentation

## 1. Introduction

HR-PHP is a web-based Human Resource Management (HRM) system built with PHP. It provides a simple and efficient way to manage employees, leaves, and payroll. The application is designed to be easy to use and customize, making it suitable for small to medium-sized businesses.

## 2. Features

### 2.1. Authentication

-   **User Registration:** New users can register for an account.
-   **Login:** Registered users can log in to the system.
-   **Role-Based Access Control:** The system supports two user roles:
    -   **Admin:** Has full access to all features.
    -   **Employee:** Has limited access to their own information.

### 2.2. Admin Features

-   **Dashboard:** An overview of the system.
-   **User Management:**
    -   View a list of all users.
    -   Add, edit, and delete users.
    -   Upload and display user photos.
-   **Leave Management:**
    -   View and manage all leave requests.
    -   Define and manage different types of leave.
-   **Payroll Management:**
    -   Create and manage payroll for employees.
    -   View payroll history.
-   **Bonuses and Deductions:**
    -   Add and manage bonuses for employees.
    -   Add and manage deductions for employees.

### 2.3. Employee Features

-   **Dashboard:** An overview of their personal information.
-   **Leave Management:**
    -   Submit leave requests.
    -   View their own leave history.
-   **Payroll:**
    -   View their own payroll information.
-   **Profile Management:**
    -   View and update their own profile information.
    -   Change their password.
    -   Upload and change their profile photo.

## 3. Setup

### 3.1. Requirements

-   PHP 7.4 or higher
-   SQLite
-   Node.js and npm

### 3.2. Installation

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/your-username/hr-php.git
    ```

2.  **Install dependencies:**

    ```bash
    npm install
    ```

3.  **Create the database:**

    ```bash
    touch database/database.sqlite
    ```

4.  **Run the database migrations:**

    ```bash
    php database/migrate.php
    ```

5.  **Seed the database:**

    ```bash
    php database/seeder.php
    ```

6.  **Start the development server:**

    ```bash
    php -S localhost:8000 -t public
    ```

7.  **Build the CSS:**

    ```bash
    npm run build:css
    ```

## 4. Usage

-   Access the application at `http://localhost:8000`.
-   Log in with one of the default admin or employee accounts.

### 4.1. Default Admin Accounts

-   **Email:** `admin1@example.com`, `admin2@example.com`, `admin3@example.com`
-   **Password:** `password123`

### 4.2. Default Employee Accounts

-   **Email:** `employee1@example.com` to `employee10@example.com`
-   **Password:** `password123`
