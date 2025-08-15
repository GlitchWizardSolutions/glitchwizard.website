
 

        CREATE TABLE accounts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255),
            email VARCHAR(255),
            full_name VARCHAR(255),
            document_path TEXT
        );
		
		CREATE TABLE draft_locks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  document_title VARCHAR(255) NOT NULL,
  client_id INT NOT NULL,
  locked_until DATETIME NOT NULL,
  UNIQUE (document_title)
);

		
 -- Add this SQL to create the new tables for Scope of Work functionality

CREATE TABLE scope (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    fee DECIMAL(10, 2) NOT NULL,
    frequency VARCHAR(50),
    update_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    attachment_path TEXT
);


CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scope_id INT NOT NULL,
    mine TEXT,
    yours TEXT,
    file_path TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    due_date DATE,
    FOREIGN KEY (scope_id) REFERENCES scope(id) ON DELETE CASCADE
);

-- Updated table for logging document generation with enhanced versioning and signing
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    client_name VARCHAR(255),
    document_title VARCHAR(255),
    file_type ENUM('pdf', 'docx', 'csv', 'xlsx'),
    file_path TEXT,
    output_path TEXT,
    version_number INT DEFAULT 1,
    version_notes TEXT,
    version_tags VARCHAR(255),
    signed BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payment_status ENUM('pending','paid') DEFAULT 'pending',
    FOREIGN KEY (client_id) REFERENCES accounts(id) ON DELETE SET NULL
);

-- New table for storing reusable client signatures
CREATE TABLE client_signatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    signature_path TEXT,
    initials_path TEXT,
    thumbnail_path TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES accounts(id) ON DELETE CASCADE
);

-- Optional: Sample data for demonstration purposes
INSERT INTO scope (title, description, fee, frequency) VALUES
('Basic Web Maintenance', 'Ongoing updates, plugin checks, and backups.', 150.00, 'monthly'),
('Website Redesign', 'Complete UI/UX redesign with mobile-first layout.', 1200.00, 'one-time');

INSERT INTO tasks (scope_id, mine, yours, status, due_date) VALUES
(1, 'Monitor uptime and updates weekly', 'Report any issues within 24h', 'pending', '2025-07-15'),
(2, 'Monthly plugin and security updates', 'Provide access to hosting control panel', 'in-progress', '2025-07-20'),
(3, 'Design new layout concepts', 'Approve mockups before implementation', 'pending', '2025-08-01');
 ALTER TABLE audit_log ADD COLUMN payment_status ENUM('pending','paid') DEFAULT 'pending';