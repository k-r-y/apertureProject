-- Bookings Table for Aperture Event Photography System
CREATE TABLE IF NOT EXISTS bookings (
    bookingID INT PRIMARY KEY AUTO_INCREMENT,
    userID INT NOT NULL,

    -- Event Details
    event_type VARCHAR(50) NOT NULL,
    event_name VARCHAR(100),
    event_date DATE NOT NULL,
    event_time_start TIME NOT NULL,
    event_time_end TIME,
    event_duration INT, -- in hours
    number_of_guests INT,

    -- Location
    venue_name VARCHAR(100),
    venue_address TEXT,
    venue_type ENUM('Indoor', 'Outdoor', 'Both'),

    -- Service Selection
    service_type ENUM('Photography', 'Videography', 'Both') NOT NULL,
    package_type VARCHAR(50), -- Basic, Standard, Premium, Custom
    add_ons JSON, -- Store add-ons as JSON array

    -- Special Requirements
    special_requirements TEXT,
    reference_files VARCHAR(255), -- Path to uploaded files

    -- Pricing
    total_price DECIMAL(10,2) NOT NULL,
    downpayment DECIMAL(10,2),
    balance DECIMAL(10,2),

    -- Status
    status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
    payment_status ENUM('Unpaid', 'Partially Paid', 'Paid') DEFAULT 'Unpaid',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign Key
    FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE
);

-- Add index for better query performance
CREATE INDEX idx_user_bookings ON bookings(userID);
CREATE INDEX idx_booking_status ON bookings(status);
CREATE INDEX idx_event_date ON bookings(event_date);
