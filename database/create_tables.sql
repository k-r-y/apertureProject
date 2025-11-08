-- ============================================
-- Aperture Studios - Database Schema
-- Tables for Packages and Add-ons Management
-- ============================================

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS addons;
DROP TABLE IF EXISTS packages;

-- ============================================
-- TABLE: packages
-- Stores photography/videography packages
-- ============================================
CREATE TABLE packages (
    packageID INT AUTO_INCREMENT PRIMARY KEY,
    packageName VARCHAR(100) NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    coverage_hours INT DEFAULT 0,
    extra_hour_rate DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    INDEX idx_package_name (packageName),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: addons
-- Stores add-ons for each package
-- ============================================
CREATE TABLE addons (
    addID INT AUTO_INCREMENT PRIMARY KEY,
    packageID INT NOT NULL,
    Description VARCHAR(255) NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    FOREIGN KEY (packageID) REFERENCES packages(packageID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_package_id (packageID),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inclusion (
    inclusionID VARCHAR PRIMARY KEY,
    packageID VARCHAR NOT NULL,
    Description VARCHAR(255) NOT NULL,
    

    FOREIGN KEY (packageID) REFERENCES packages(packageID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT SAMPLE DATA FROM pricing.json
-- ============================================

-- Insert Packages
INSERT INTO packages (packageID, packageName, Price, description) VALUES
('basic', 'Essential Package', 7500.00, 'Perfect for small celebrations or short events that need professional documentation.'),
('elite', 'Elite Package', 15000.00, 'A balanced package for most events — professional coverage, storytelling, and cinematic editing.'),
('premium', 'Premium Package', 25000.00, 'For clients who want a full cinematic experience and top-tier service.');

-- Insert Add-ons for Essential Package (packageID = 1)
INSERT INTO addons (packageID, Description, Price) VALUES
('basic', 'Extra Hour', 1000.00),
('basic', 'Drone Shots', 2000.00),
('basic', 'Full-Length Video (5–8 minutes)', 2500.00),
('basic', 'USB Copy with Case', 500.00);

-- Insert Add-ons for Premium Package (packageID = 2)
INSERT INTO addons (packageID, Description, Price) VALUES
('elite', 'Drone Shots', 2000.00),
('elite', 'Same-Day Edit (SDE)', 3500.00),
('elite', 'Photo Album (30 Pages)', 2000.00),
('elite', 'Extra Photographer', 2000.00),
('elite', 'Short BTS (Behind-the-Scenes Reel)', 1500.00);

-- Insert Add-ons for Elite Package (packageID = 3)
INSERT INTO addons (packageID, Description, Price) VALUES
('premium', 'Extra Hour', 1500.00),
('premium', 'Livestream Setup', 3000.00),
('premium', 'Extra Location', 1000.00),
('premium', '4K Upgrade', 2000.00);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- View all packages
SELECT * FROM packages;

-- View all add-ons with package names
SELECT
    a.addID,
    a.packageID,
    p.packageName,
    a.Description,
    a.Price
FROM addons a
JOIN packages p ON a.packageID = p.packageID
ORDER BY a.packageID, a.addID;

-- Count add-ons per package
SELECT
    p.packageID,
    p.packageName,
    COUNT(a.addID) as total_addons
FROM packages p
LEFT JOIN addons a ON p.packageID = a.packageID
GROUP BY p.packageID, p.packageName;


INSERT INTO inclusion (inclusionID, packageID, Description) VALUES
('BSC001', 'basic', '1 Photographer + 1 Videographer'),
('BSC002', 'basic', 'Full Event Coverage (Up to 5 hours)'),
('BSC003', 'basic', '100+ Professionally Edited Photos'),
('BSC004', 'basic', '3–5 Minute Highlight Video'),
('BSC005', 'basic', 'Full Event Video (10–15 minutes)'),
('BSC006', 'basic', 'On-site Lighting Setup'),
('BSC007', 'basic', 'Audio Recording for Key Moments'),
('BSC008', 'basic', 'Online Gallery Access (2 months)'),

('ELT001', 'elite', '1 Photographer + 1 Videographer + Assistant'),
('ELT002', 'elite', 'Full Event Coverage (Up to 8 hours)'),
('ELT003', 'elite', '150+ Professionally Edited Photos'),
('ELT004', 'elite', '5–7 Minute Highlight Film (Full HD)'),
('ELT005', 'elite', 'Full Event Video (15–20 minutes)'),
('ELT006', 'elite', 'Drone Coverage Included'),
('ELT007', 'elite', 'Audio Recording for Vows, Speeches & Messages'),
('ELT008', 'elite', 'Cinematic Color Grading'),
('ELT009', 'elite', 'On-site Lighting & Audio Setup'),
('ELT010', 'elite', 'Online Gallery Access (3 months)'),
('ELT011', 'elite', 'Personalized USB Copy'),

('PRM001', 'premium', '2 Photographers + 2 Videographers'),
('PRM002', 'premium', 'Full Event Coverage (Up to 10 hours)'),
('PRM003', 'premium', 'Unlimited Shots + 250+ Edited Photos'),
('PRM004', 'premium', '7–10 Minute Cinematic Highlight Film (Full HD or 4K)'),
('PRM005', 'premium', 'Full Event Video (25+ minutes)'),
('PRM006', 'premium', 'Drone Coverage (Included)'),
('PRM007', 'premium', 'Same-Day Edit (SDE) Included'),
('PRM008', 'premium', 'Audio Mixing & Cinematic Color Grading'),
('PRM009', 'premium', 'Livestream Setup (Optional)'),
('PRM010', 'premium', 'Personalized Online Gallery (1 Year Access)'),
('PRM011', 'premium', 'Premium USB Copy + Custom Box'),
('PRM012', 'premium', 'Printed Photo Album (40 Pages)'),
('PRM013', 'premium', 'Free Save-The-Date Teaser (30 seconds)');
