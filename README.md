# Aperture Studio Management System 📸

A professional, comprehensive web-based management solution designed for photography and videography studios. **Aperture Studio** streamlines the entire workflow from client booking and scheduling to invoicing and secure photo gallery delivery.

---

## 🌟 Key Features

### 👤 User Module (Client Facing)

- **Secure Authentication**: User registration with email verification (OTP) and secure login.
- **Service Browsing**: Explore available photography and videography packages with detailed descriptions.
- **Smart Booking System**:
  - Real-time availability checking.
  - Constraint-based scheduling (blocks past dates, prevents double-booking).
  - Add-on selection with dynamic pricing.
  - Consultation scheduling logic (ensures consultation happens before the event).
- **Dashboard**: View booking history and status updates (Pending, Approved, Finished).
- **Private Gallery**: Access high-quality photo downloads securely. **Note:** Access is restricted until full payment is verified.
- **Invoicing**: View and download official invoices.

### 🛡️ Admin Module (Management)

- **Dashboard Analytics**: Visual overview of revenue, total bookings, and monthly activity.
- **Booking Management**: Full control to **Approve**, **Reject**, or **Cancel** bookings.
- **Calendar View**: Interactive calendar for tracking upcoming events and consultations.
- **Photo Management**:
  - Upload photos for specific events.
  - Gallery preparation mode (upload before payment).
  - Automated watermark/protection (logic handled via secure serving).
- **Financials**:
  - Track payments and refund status.
  - Generate PDF invoices with branded company details ("Dasmariñas City, Cavite, Philippines").
- **Activity Logs**: Detailed system logs for security and tracking admin actions.

### 🔐 Security & Technical Highlights

- **CSRF Protection**: Secure form handling tokens.
- **Rate Limiting**: Brute-force protection on login and API endpoints.
- **Input Validation**: Strict server-side validation for all user inputs.
- **Environment Configuration**: Secure `.env` management for credentials.

---

## 🛠️ Technology Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5 (Icons)
- **Dependencies**:
  - `vlucas/phpdotenv`: Environment variable management.
  - `phpmailer/phpmailer`: SMTP email handling.
  - `mpdf/mpdf` (if applicable) or native PDF generation logic.

---

## 🚀 Installation Guide

### Prerequisites

- **Web Server**: XAMPP, WAMP, or any Apache/PHP/MySQL stack.
- **Composer**: For managing PHP dependencies.
- **Node.js & NPM**: For frontend package management (optional but recommended for icons/assets).

### Step-by-Step Setup

1.  **Clone the Repository**

    ```bash
    git clone https://github.com/your-repo/aperture.git
    cd aperture
    # Or place the folder in C:\xampp\htdocs\aperture
    ```

2.  **Install PHP Dependencies**
    Navigate to the project root and run:

    ```bash
    composer install
    ```

3.  **Install Frontend Assets**

    ```bash
    npm install
    ```

4.  **Database Configuration**

    1.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
    2.  Create a new database named `aperture` (or matches your config).
    3.  Import the schema file:
        - Locate `database/aperture_full_schema.sql` (Recommended) or `database/aperture_complete.sql`.
        - Import it into your newly created database.

5.  **Environment Setup**

    1.  Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    2.  Open `.env` and configure your database and mail credentials:

        ```ini
        DB_HOST=localhost
        DB_NAME=aperture
        DB_USER=root
        DB_PASS=

        # Email Configuration (Required for OTP/Notifications)
        SMTP_HOST=smtp.gmail.com
        SMTP_PORT=587
        SMTP_USERNAME=your_email@gmail.com
        SMTP_PASSWORD=your_app_password

        APP_ENV=development
        APP_URL=http://localhost/aperture
        ```

6.  **Directory Permissions**
    Ensure the following folders are writable:
    - `uploads/` (For photo galleries)
    - `logs/` (For error logging)

---

## 📖 Usage Guide

### Getting Started

1.  Start **Apache** and **MySQL** in XAMPP.
2.  Open your browser and navigate to `http://localhost/aperture`.

### For Users

- **Sign Up**: Create an account and verify your email via OTP.
- **Book an Event**: Go to "Book Now", choose a package (e.g., Wedding, Debut), select a date, and submit.
- **Pay**: Wait for admin approval, then proceed with payment instructions.
- **Download**: Once the event is done and paid, go to "My Photos" to download your memories.

### For Admins

- **Login**: Access `/admin` (or the specific admin login URL configured).
- **Manage**: Use the sidebar to switch between "Bookings", "Calendar", and "Invoices".
- **Upload**: Go to a finished booking and upload gallery images.

---

## 📂 Project Structure

```text
aperture/
├── database/           # SQL schema files
├── src/
│   ├── admin/          # Admin portal scripts
│   ├── user/           # User portal scripts
│   ├── includes/       # Shared functions (Auth, DB, Config)
│   │   └── functions/  # Core logic (config.php, auth.php)
│   └── assets/         # Static assets (Images, CSS)
├── uploads/            # Dynamic user uploads (Galleries)
├── vendor/             # Composer dependencies
├── .env                # Environment variables (Git-ignored)
└── index.php           # Landing page entry point
```

---

_Developed for Aperture Studio._
