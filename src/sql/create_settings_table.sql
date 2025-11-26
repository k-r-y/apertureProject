CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default values if they don't exist
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Aperture'),
('admin_email', 'admin@aperture.com'),
('contact_phone', '+63 912 345 6789'),
('maintenance_mode', '0'),
('primary_color', '#D4AF37'),
('smtp_host', 'smtp.example.com'),
('smtp_user', 'user@example.com'),
('smtp_pass', ''),
('logo_path', '../assets/logo.png'),
('favicon_path', '../assets/camera.png');
