-- Phase 4: Database Optimization - Composite Indexes
-- This script adds indexes to improve query performance

-- Bookings table indexes
CREATE INDEX IF NOT EXISTS idx_bookings_user_status ON bookings(userID, booking_status);
CREATE INDEX IF NOT EXISTS idx_bookings_status_date ON bookings(booking_status, event_date);
CREATE INDEX IF NOT EXISTS idx_bookings_created ON bookings(created_at);
CREATE INDEX IF NOT EXISTS idx_bookings_event_date ON bookings(event_date);

-- Booking logs indexes
CREATE INDEX IF NOT EXISTS idx_booking_logs_booking ON booking_logs(bookingID, created_at);
CREATE INDEX IF NOT EXISTS idx_booking_logs_action ON booking_logs(action);

-- Users table indexes
CREATE INDEX IF NOT EXISTS idx_users_email ON users(Email);
CREATE INDEX IF NOT EXISTS idx_users_role_status ON users(Role, Status);
CREATE INDEX IF NOT EXISTS idx_users_created ON users(created_at);

-- Client notes indexes
CREATE INDEX IF NOT EXISTS idx_client_notes_user ON client_notes(userID, created_at);

-- User tags indexes
CREATE INDEX IF NOT EXISTS idx_user_tags_user ON user_tags(userID);
CREATE INDEX IF NOT EXISTS idx_user_tags_tag ON user_tags(tagID);

-- Communication log indexes
CREATE INDEX IF NOT EXISTS idx_comm_log_user ON communication_log(userID, communication_date);
CREATE INDEX IF NOT EXISTS idx_comm_log_type ON communication_log(type);

-- Package bookings (for analytics)
CREATE INDEX IF NOT EXISTS idx_bookings_package ON bookings(packageID, booking_status);

-- Payment proofs
CREATE INDEX IF NOT EXISTS idx_bookings_payment ON bookings(payment_proof);
