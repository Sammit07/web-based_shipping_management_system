# Web-Based Shipping Management System

## Project Overview

This is a web-based application designed for customers to register, log in, and place shipping requests, while administrators can manage and view request records. It provides an easy-to-use interface for customers and a powerful admin panel to track and analyze requests.

---

## Features

- **Homepage**:
  - Provides links for new user registration, customer login, and admin page.
- **Customer Registration**:
  - Allows customers to create an account with their name, password, email, and phone number.
- **Login System**:
  - Secure login for customers to access their account and place shipping requests.
- **Request Page**:
  - Enables customers to submit shipping requests with details like item description, weight, pickup/delivery addresses, and dates.
  - Sends a confirmation email upon successful request submission.
- **Admin Panel**:
  - Allows administrators to view shipping requests by date (request or pick-up).
  - Displays a table with total requests, revenue, or weight for analysis.

---

## Technologies Used

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Email Integration**: For request confirmations

---

## File Structure

1. **shiponline.php**:
   - The main homepage with links to registration, login, and admin pages.
2. **register.php**:
   - Handles new customer registrations, validates inputs, and stores data in the database.
3. **login.php**:
   - Manages customer logins, validates credentials, and redirects to the request page.
4. **request.php**:
   - Allows customers to submit shipping requests and sends confirmation emails.
5. **admin.php**:
   - Provides a dashboard for administrators to view and analyze shipping requests by date.

---

## How to Use

1. **Homepage**:
   - Navigate to the homepage to access:
     - Customer Registration
     - Login
     - Admin Page

2. **Customer Registration**:
   - Click "New User Registration" and fill out:
     - Name, Password, Email, Contact Phone
   - Click "Register" to complete the process.

3. **Login**:
   - Click "Log-In" and enter your credentials (Customer Number and Password).
   - After login, you'll be redirected to the "Request Page."

4. **Request Page**:
   - Fill in the shipping request form with:
     - Item description, weight, pickup/delivery addresses, dates, and times.
   - Click "Request" to submit.
   - A confirmation email will be sent to your registered email.

5. **Admin Panel**:
   - Navigate to the "Admin Page" to:
     - View requests by Request Date or Pick-Up Date.
     - View totals for requests, revenue, or weight.

---

## Setup and Installation

### Prerequisites
- A server capable of running PHP (e.g., XAMPP, WAMP).
- MySQL for database storage.

### Installation Steps
## Step 1: Navigate to the Project Directory
cd path/to/your/project-directory

## Step 2: Set Up Database
- Update database credentials in all PHP files (e.g., `register.php`, `login.php`).

## Step 3: Run the Application
- Start your PHP server (e.g., using XAMPP or WAMP).
- Open `shiponline.php` in your browser to access the system.
