-- Fix for existing rejected refunds with incorrect booking status
-- This updates bookings that have rejected refunds but are still showing as cancellation_pending

UPDATE bookings b
INNER JOIN refunds r ON b.bookingID = r.bookingID
SET b.booking_status = 'cancelled',
    b.refund_amount = 0
WHERE r.status = 'rejected' 
  AND b.booking_status = 'cancellation_pending';

-- Check results
SELECT 
    b.bookingID,
    b.booking_status,
    b.refund_amount,
    r.status as refund_status
FROM bookings b
INNER JOIN refunds r ON b.bookingID = r.bookingID
WHERE r.status = 'rejected';
