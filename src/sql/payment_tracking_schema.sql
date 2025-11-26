-- Phase 1: Payment Tracking Schema
-- Add columns to track downpayment and final payment separately

ALTER TABLE `bookings` 
ADD COLUMN `balance_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `downpayment_amount`,
ADD COLUMN `downpayment_paid` TINYINT(1) DEFAULT 0 AFTER `balance_amount`,
ADD COLUMN `downpayment_paid_date` DATETIME NULL AFTER `downpayment_paid`,
ADD COLUMN `final_payment_paid` TINYINT(1) DEFAULT 0 AFTER `downpayment_paid_date`,
ADD COLUMN `final_payment_paid_date` DATETIME NULL AFTER `final_payment_paid`,
ADD COLUMN `refund_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `total_amount`,
ADD COLUMN `refund_processed_date` DATETIME NULL AFTER `refund_amount`;

-- Add indexes for better query performance
CREATE INDEX idx_downpayment_paid ON bookings(downpayment_paid);
CREATE INDEX idx_final_payment_paid ON bookings(final_payment_paid);
CREATE INDEX idx_booking_status ON bookings(booking_status);
