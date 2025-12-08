# Aperture Studio Management System

## Overview

Aperture Studio is a comprehensive web-based management system designed for a photography and videography studio. It facilitates user bookings, admin management of events, invoicing, and photo gallery delivery.

## Setting Up

### Prerequisites

- **XAMPP** (or any PHP/MySQL stack) installed.
- **Web Browser** (Chrome, Firefox, Edge).

### Installation

1.  **Clone/Copy** the project files into your web server's root directory (e.g., `C:\xampp\htdocs\aperture`).
2.  **Database Setup**:
    - Open phpMyAdmin (`http://localhost/phpmyadmin`).
    - Create a new database named `aperture_db`.
    - Import the `aperture_db.sql` file (if available in the `db` folder) or ensure the schema is compliant with the codebase.
3.  **Configuration**:
    - Verify database credentials in `src/includes/functions/config.php` (default: root, empty password).

### Running the Application

1.  Start **Apache** and **MySQL** via XAMPP Control Panel.
2.  Access the application in your browser: `http://localhost/aperture` or `http://localhost/aperture/index.php`.

## Walkthrough

### User Features

- **Landing Page**: Browse portfolio, services, and packages.
- **Booking**:
  - Select packages and add-ons.
  - Real-time price calculation and coverage hours display.
  - Choose event date (validation prevents past dates or booking too far ahead).
  - Schedule consultation (system enforces consultation Date < Event Date).
- **My Account**:
  - View booking status.
  - **My Photos**: Access your private gallery once the booking is fully paid. (Restricted for unpaid bookings).

### Admin Features

- **Dashboard**: Overview of bookings, revenue, and activities.
- **Calendar**: Visual schedule of events.
- **Bookings Management**: View, approve, or cancel bookings.
- **Invoicing**: Generate PDF invoices with the correct company address ("Dasmariñas City, Cavite, Philippines").
- **Photo Uploads**:
  - Upload photos for events that are "Finished" (Completed or Post-Production).
  - _Note_: Admins can upload photos even if the user hasn't fully paid yet, preparing the gallery for release upon payment.

## Deployment Notes

- Ensure the `uploads` directory has write permissions.
- Production deployments should secure `config.php` and disable error reporting.
