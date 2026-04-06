-- ============================================================
-- Tokyo Metro Railway Management System - Database Schema
-- Referenced from: https://www.tokyometro.jp/en/index.html
-- ============================================================

CREATE DATABASE IF NOT EXISTS tokyo_metro_db;
USE tokyo_metro_db;

-- ============================================================
-- 1. USERS TABLE (Admin, Staff, Passenger)
-- ============================================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    role ENUM('Admin', 'Staff', 'Passenger') NOT NULL DEFAULT 'Passenger',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. LINES TABLE (Metro Lines)
-- ============================================================
CREATE TABLE metro_lines (
    line_id INT AUTO_INCREMENT PRIMARY KEY,
    line_name VARCHAR(50) NOT NULL,
    line_code VARCHAR(5) NOT NULL UNIQUE,
    color_hex VARCHAR(7) NOT NULL,
    total_stations INT DEFAULT 0,
    status ENUM('Active', 'Suspended', 'Maintenance') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 3. STATIONS TABLE
-- ============================================================
CREATE TABLE stations (
    station_id INT AUTO_INCREMENT PRIMARY KEY,
    station_name VARCHAR(100) NOT NULL,
    station_code VARCHAR(10) NOT NULL UNIQUE,
    line_id INT NOT NULL,
    station_order INT NOT NULL,
    is_transfer TINYINT(1) DEFAULT 0,
    status ENUM('Open', 'Closed', 'Under Maintenance') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (line_id) REFERENCES metro_lines(line_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 4. TRAINS TABLE
-- ============================================================
CREATE TABLE trains (
    train_id INT AUTO_INCREMENT PRIMARY KEY,
    train_number VARCHAR(20) NOT NULL UNIQUE,
    line_id INT NOT NULL,
    capacity INT NOT NULL DEFAULT 1000,
    train_type ENUM('Local', 'Express', 'Rapid') DEFAULT 'Local',
    status ENUM('Running', 'Idle', 'Maintenance') DEFAULT 'Idle',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (line_id) REFERENCES metro_lines(line_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 5. SCHEDULES TABLE
-- ============================================================
CREATE TABLE schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    train_id INT NOT NULL,
    departure_station_id INT NOT NULL,
    arrival_station_id INT NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    days_of_week VARCHAR(50) DEFAULT 'Mon,Tue,Wed,Thu,Fri,Sat,Sun',
    status ENUM('On Time', 'Delayed', 'Cancelled') DEFAULT 'On Time',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (train_id) REFERENCES trains(train_id) ON DELETE CASCADE,
    FOREIGN KEY (departure_station_id) REFERENCES stations(station_id) ON DELETE CASCADE,
    FOREIGN KEY (arrival_station_id) REFERENCES stations(station_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 6. TICKETS TABLE
-- ============================================================
CREATE TABLE tickets (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    schedule_id INT NOT NULL,
    ticket_type ENUM('Single', 'Return', 'Day Pass') DEFAULT 'Single',
    price DECIMAL(10,2) NOT NULL,
    booking_date DATE NOT NULL,
    travel_date DATE NOT NULL,
    status ENUM('Booked', 'Used', 'Cancelled', 'Expired') DEFAULT 'Booked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(schedule_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 7. FARE TABLE
-- ============================================================
CREATE TABLE fares (
    fare_id INT AUTO_INCREMENT PRIMARY KEY,
    from_station_id INT NOT NULL,
    to_station_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (from_station_id) REFERENCES stations(station_id) ON DELETE CASCADE,
    FOREIGN KEY (to_station_id) REFERENCES stations(station_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SAMPLE DATA: Metro Lines (from Tokyo Metro website)
-- ============================================================
INSERT INTO metro_lines (line_name, line_code, color_hex, total_stations, status) VALUES
('Ginza Line',      'G',  '#FF9500', 19, 'Active'),
('Marunouchi Line', 'M',  '#F62E36', 25, 'Active'),
('Hibiya Line',     'H',  '#B5B5AC', 21, 'Active'),
('Tozai Line',      'T',  '#009BBF', 23, 'Active'),
('Chiyoda Line',    'C',  '#00BB85', 20, 'Active'),
('Yurakucho Line',  'Y',  '#C1A470', 24, 'Active'),
('Hanzomon Line',   'Z',  '#8F76D6', 14, 'Active'),
('Namboku Line',    'N',  '#00AC9B', 19, 'Active'),
('Fukutoshin Line', 'F',  '#9C5E31', 16, 'Active');

-- ============================================================
-- SAMPLE DATA: Stations (selected stations per line)
-- ============================================================
-- Ginza Line stations
INSERT INTO stations (station_name, station_code, line_id, station_order, is_transfer) VALUES
('Shibuya',          'G01', 1, 1,  1),
('Omotesando',       'G02', 1, 2,  1),
('Gaienmae',         'G03', 1, 3,  0),
('Aoyama-itchome',   'G04', 1, 4,  1),
('Akasaka-mitsuke',  'G05', 1, 5,  1),
('Tameike-sanno',    'G06', 1, 6,  1),
('Toranomon',        'G07', 1, 7,  0),
('Shimbashi',        'G08', 1, 8,  0),
('Ginza',            'G09', 1, 9,  1),
('Nihombashi',       'G10', 1, 10, 1),
('Ueno',             'G16', 1, 11, 1),
('Asakusa',          'G19', 1, 12, 0);

-- Marunouchi Line stations
INSERT INTO stations (station_name, station_code, line_id, station_order, is_transfer) VALUES
('Ogikubo',          'M01', 2, 1,  0),
('Shinjuku',         'M08', 2, 2,  1),
('Kasumigaseki',     'M15', 2, 3,  1),
('Ginza',            'M16', 2, 4,  1),
('Tokyo',            'M17', 2, 5,  1),
('Otemachi',         'M18', 2, 6,  1),
('Ikebukuro',        'M25', 2, 7,  1);

-- Hibiya Line stations
INSERT INTO stations (station_name, station_code, line_id, station_order, is_transfer) VALUES
('Naka-meguro',      'H01', 3, 1,  0),
('Ebisu',            'H02', 3, 2,  0),
('Roppongi',         'H04', 3, 3,  0),
('Kasumigaseki',     'H06', 3, 4,  1),
('Ginza',            'H08', 3, 5,  1),
('Ueno',             'H17', 3, 6,  1),
('Kita-senju',       'H21', 3, 7,  0);

-- ============================================================
-- SAMPLE DATA: Trains
-- ============================================================
INSERT INTO trains (train_number, line_id, capacity, train_type, status) VALUES
('G-101', 1, 1200, 'Local',   'Running'),
('G-102', 1, 1200, 'Express', 'Running'),
('G-103', 1, 1200, 'Local',   'Idle'),
('M-201', 2, 1400, 'Local',   'Running'),
('M-202', 2, 1400, 'Rapid',   'Running'),
('M-203', 2, 1400, 'Local',   'Maintenance'),
('H-301', 3, 1100, 'Local',   'Running'),
('H-302', 3, 1100, 'Express', 'Idle');

-- ============================================================
-- SAMPLE DATA: Schedules
-- ============================================================
INSERT INTO schedules (train_id, departure_station_id, arrival_station_id, departure_time, arrival_time, days_of_week, status) VALUES
(1, 1, 9,   '06:00:00', '06:28:00', 'Mon,Tue,Wed,Thu,Fri', 'On Time'),
(1, 9, 1,   '06:35:00', '07:03:00', 'Mon,Tue,Wed,Thu,Fri', 'On Time'),
(2, 1, 12,  '07:00:00', '07:35:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun', 'On Time'),
(2, 12, 1,  '07:45:00', '08:20:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun', 'On Time'),
(4, 13, 19, '06:15:00', '06:55:00', 'Mon,Tue,Wed,Thu,Fri', 'On Time'),
(4, 19, 13, '07:00:00', '07:40:00', 'Mon,Tue,Wed,Thu,Fri', 'On Time'),
(5, 13, 19, '08:00:00', '08:30:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun', 'On Time'),
(7, 20, 26, '06:30:00', '07:05:00', 'Mon,Tue,Wed,Thu,Fri', 'On Time'),
(7, 26, 20, '07:15:00', '07:50:00', 'Mon,Tue,Wed,Thu,Fri', 'On Time'),
(1, 1, 9,   '09:00:00', '09:28:00', 'Sat,Sun', 'On Time'),
(4, 13, 19, '09:30:00', '10:10:00', 'Sat,Sun', 'On Time');

-- ============================================================
-- SAMPLE DATA: Fares (in JPY)
-- ============================================================
INSERT INTO fares (from_station_id, to_station_id, price) VALUES
(1, 9,  170),   -- Shibuya -> Ginza
(1, 12, 210),   -- Shibuya -> Asakusa
(9, 12, 170),   -- Ginza -> Asakusa
(1, 11, 200),   -- Shibuya -> Ueno
(13, 17, 170),  -- Ogikubo -> Tokyo
(13, 19, 200),  -- Ogikubo -> Ikebukuro
(20, 24, 170),  -- Naka-meguro -> Ginza (Hibiya)
(20, 25, 200);  -- Naka-meguro -> Ueno (Hibiya)

-- ============================================================
-- SAMPLE DATA: Users
-- ============================================================
INSERT INTO users (username, password, full_name, email, phone, role) VALUES
('admin',     MD5('admin123'),     'System Administrator', 'admin@tokyometro.jp',   '03-0000-0001', 'Admin'),
('staff01',   MD5('staff123'),     'Tanaka Yuki',          'tanaka@tokyometro.jp',  '03-0000-0002', 'Staff'),
('staff02',   MD5('staff123'),     'Suzuki Hana',          'suzuki@tokyometro.jp',  '03-0000-0003', 'Staff'),
('passenger1', MD5('pass123'),     'John Smith',           'john@example.com',      '080-1234-5678', 'Passenger'),
('passenger2', MD5('pass123'),     'Maria Garcia',         'maria@example.com',     '080-9876-5432', 'Passenger');

-- ============================================================
-- SAMPLE DATA: Tickets
-- ============================================================
INSERT INTO tickets (user_id, schedule_id, ticket_type, price, booking_date, travel_date, status) VALUES
(4, 1, 'Single',   170.00, '2026-03-25', '2026-03-29', 'Booked'),
(4, 3, 'Return',   420.00, '2026-03-25', '2026-03-30', 'Booked'),
(5, 5, 'Day Pass', 600.00, '2026-03-26', '2026-03-29', 'Booked'),
(5, 8, 'Single',   170.00, '2026-03-27', '2026-03-31', 'Booked');
