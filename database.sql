-- Create the database
CREATE DATABASE IF NOT EXISTS logbook_visitor;
USE logbook_visitor;

-- Create entrance_locations table
CREATE TABLE entrance_locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create visitors table
CREATE TABLE visitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    face_photo VARCHAR(255),
    id_photo VARCHAR(255),
    reason TEXT NOT NULL,
    location_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES entrance_locations(id)
);

-- Insert default entrance location
INSERT INTO entrance_locations (location_name) VALUES 
('T3 Loading Dock International');