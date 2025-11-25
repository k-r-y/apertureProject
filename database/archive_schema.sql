-- Archive Tables Schema
-- Creates archive tables for old bookings to improve performance

-- Bookings Archive Table
CREATE TABLE IF NOT EXISTS bookings_archive (
    bookingID INT PRIMARY KEY,
    userID INT NOT NULL,
    packageID INT,
    EventType VARCHAR(100),
    EventDate DATE,
    EventTime TIME,
    EventLocation TEXT,
    NumGuests INT,
    SpecialRequests TEXT,
    TotalAmount DECIMAL(10, 2),
    BookingStatus VARCHAR(50),
    BookingReference VARCHAR(50) UNIQUE,
    PaymentStatus VARCHAR(50),
    CreatedAt DATETIME,
    UpdatedAt DATETIME,
    AdminNotes TEXT,
    archivedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_archive_user (userID),
    INDEX idx_archive_date (EventDate),
    INDEX idx_archive_status (BookingStatus),
    INDEX idx_archived_at (archivedAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Booking Logs Archive Table
CREATE TABLE IF NOT EXISTS booking_logs_archive (
    logID INT PRIMARY KEY,
    bookingID INT NOT NULL,
    Action VARCHAR(100),
    Description TEXT,
    UserID INT,
    CreatedAt DATETIME,
    archivedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_archive_log_booking (bookingID),
    INDEX idx_archive_log_date (CreatedAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
