# 🚇 Tokyo Metro Railway Management System

**University Project — Team Shinkansen**  
Referenced from: [https://www.tokyometro.jp/en/index.html](https://www.tokyometro.jp/en/index.html)

---

## 📋 Project Overview

A **web-based Railway (Train Station) Management System** inspired by the Tokyo Metro subway network. The system manages train schedules, stations, passengers, and ticket bookings through a centralized database-driven application.

### Technologies Used
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Server:** XAMPP (Apache + MySQL)

### User Roles
| Role      | Capabilities                                                |
|-----------|-------------------------------------------------------------|
| Admin     | Full access: manage trains, stations, schedules, users      |
| Staff     | Manage trains, stations, schedules (no user management)     |
| Passenger | View schedules & stations, book tickets, manage own tickets |

---

## 🗂️ Project File Structure

```
tokyo-metro-system/
├── index.php                  # Homepage
├── login.php                  # Login page
├── register.php               # Passenger registration
├── logout.php                 # Logout handler
├── schedules.php              # Public schedules view
├── stations.php               # Public stations directory
├── book_ticket.php            # Ticket booking (Passenger)
├── my_tickets.php             # My tickets (Passenger)
├── css/
│   └── style.css              # Main stylesheet
├── js/
│   └── main.js                # JavaScript functions
├── includes/
│   ├── db_connect.php         # Database connection config
│   ├── header.php             # Common header/navigation
│   └── footer.php             # Common footer
├── admin/
│   ├── dashboard.php          # Admin dashboard with stats
│   ├── manage_trains.php      # CRUD for trains
│   ├── manage_stations.php    # CRUD for stations
│   ├── manage_schedules.php   # CRUD for schedules
│   └── manage_users.php       # User management (Admin only)
├── sql/
│   └── database.sql           # Database schema + sample data
└── README.md                  # This file
```

---

## 🚀 How to Run the Project (Step-by-Step)

### STEP 1: Install XAMPP

1. Download XAMPP from: [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)
2. Choose the version for your OS (Windows / macOS / Linux)
3. Install XAMPP with default settings
4. After installation, open the **XAMPP Control Panel**

### STEP 2: Start Apache and MySQL

1. Open **XAMPP Control Panel**
2. Click **"Start"** next to **Apache** (this is the web server for PHP)
3. Click **"Start"** next to **MySQL** (this is the database server)
4. Both should show green status indicating they are running
5. If port 80 is busy, XAMPP will prompt you to change the port

> ⚠️ **Both Apache and MySQL must be running** for the project to work.

### STEP 3: Copy Project Files to XAMPP

1. Navigate to your XAMPP installation folder:
   - **Windows:** `C:\xampp\htdocs\`
   - **macOS:** `/Applications/XAMPP/htdocs/`
   - **Linux:** `/opt/lampp/htdocs/`
2. Copy the entire `tokyo-metro-system` folder into the `htdocs` folder
3. The result should be: `htdocs/tokyo-metro-system/index.php`

### STEP 4: Create the Database

**Option A — Using phpMyAdmin (Recommended, easiest):**

1. Open your browser and go to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click on the **"SQL"** tab at the top
3. Open the file `sql/database.sql` in a text editor (e.g., Notepad)
4. **Copy ALL the contents** of `database.sql`
5. **Paste** it into the SQL query box in phpMyAdmin
6. Click **"Go"** button to execute
7. You should see a success message and the `tokyo_metro_db` database will appear on the left sidebar

**Option B — Using MySQL Command Line:**

1. Open Command Prompt / Terminal
2. Navigate to your XAMPP MySQL:
   - **Windows:** `cd C:\xampp\mysql\bin`
   - **macOS/Linux:** `cd /opt/lampp/bin`
3. Run:
   ```bash
   mysql -u root < /path/to/tokyo-metro-system/sql/database.sql
   ```
   (Replace `/path/to/` with the actual path to the project)

### STEP 5: Open the Website

1. Open your web browser (Chrome, Firefox, Edge, etc.)
2. Go to: **[http://localhost/tokyo-metro-system/](http://localhost/tokyo-metro-system/)**
3. The homepage should load with all 9 Tokyo Metro lines displayed

### STEP 6: Login with Demo Accounts

| Role      | Username    | Password  |
|-----------|-------------|-----------|
| Admin     | `admin`     | `admin123`|
| Staff     | `staff01`   | `staff123`|
| Passenger | `passenger1`| `pass123` |
| Passenger | `passenger2`| `pass123` |

Or register a new Passenger account using the **Register** page.

---

## 🔧 Troubleshooting

| Problem                          | Solution                                                   |
|----------------------------------|------------------------------------------------------------|
| "Database Connection Failed"     | Make sure MySQL is running in XAMPP Control Panel           |
| Page shows blank/error           | Make sure Apache is running in XAMPP Control Panel          |
| "Table doesn't exist" errors     | Re-import `sql/database.sql` in phpMyAdmin                 |
| Cannot access localhost          | Try `http://127.0.0.1/tokyo-metro-system/`                 |
| Port 80 conflict                 | Change Apache port in XAMPP config, then use `localhost:8080`|

---

## 📊 Database Schema (ER Diagram Tables)

The database `tokyo_metro_db` contains 7 tables:

1. **users** — User accounts with roles (Admin/Staff/Passenger)
2. **metro_lines** — The 9 Tokyo Metro lines with colors and codes
3. **stations** — Stations belonging to each metro line
4. **trains** — Train units assigned to lines
5. **schedules** — Departure/arrival times linking trains and stations
6. **tickets** — Passenger bookings linked to schedules
7. **fares** — Pricing between station pairs

### Key Relationships:
- `metro_lines` → `stations` (one-to-many)
- `metro_lines` → `trains` (one-to-many)
- `trains` → `schedules` (one-to-many)
- `stations` → `schedules` (departure & arrival, one-to-many)
- `users` → `tickets` (one-to-many)
- `schedules` → `tickets` (one-to-many)

---

## 📝 Features Summary

### Public Pages (No login required)
- Homepage with system stats and metro lines overview
- View all train schedules with line filter
- Browse station directory by metro line

### Passenger Features
- Register and login
- Book tickets (Single / Return / Day Pass)
- View and cancel own tickets

### Admin/Staff Features
- Dashboard with statistics and revenue
- CRUD operations for Trains
- CRUD operations for Stations
- CRUD operations for Schedules
- User management (Admin only)

---

## 👥 Team Shinkansen

University Database Systems Project — 2026

**Reference Website:** [https://www.tokyometro.jp/en/index.html](https://www.tokyometro.jp/en/index.html)
