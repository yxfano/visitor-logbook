# Logbook Visitor Installation Guide

## System Requirements
- XAMPP with PHP 7.4
- MySQL
- Web Browser with webcam support

## Installation Steps

1. Database Setup
   - The database has already been created using the provided SQL file

2. CodeIgniter Configuration
   Edit `application/config/database.php`:
   ```php
   $db['default'] = array(

      # Visitor Logbook

      A lightweight CRUD-based backup system for airport autogate operations. This web application allows staff to capture and store visitor information (including face image and ID card details) whenever the primary autogate system experiences errors. It ensures identity records continue to be collected seamlessly during system downtime.

      ## System Requirements
      - XAMPP with PHP 7.4
      - MySQL
      - Web Browser with webcam support

      ## Installation Steps
      1. **Database Setup**
         - The database has already been created using the provided SQL file

      2. **CodeIgniter Configuration**
         Edit `application/config/database.php`:
         ```php
         $db['default'] = array(
             'hostname' => 'localhost',
             'username' => 'root',
             'password' => '',
             'database' => 'logbook_visitor',
             // ... other settings remain default
         );
         ```

         Edit `application/config/config.php`:
         ```php
         $config['base_url'] = 'http://localhost/logbook_visitor';
         ```

      3. **Folder Permissions**
         Make sure these folders are writable:
         - application/cache
         - application/logs
         - uploads (create this folder for visitor photos)

      4. **Create Required Folders**
         ```
         mkdir uploads
         mkdir uploads/faces
         mkdir uploads/ids
         ```

      ## Project Structure
      The project will include:
      - Models: Visitor_model.php, Location_model.php
      - Controllers: Visitors.php, Locations.php
      - Views: visitor forms, lists, and location management

      ## Features
      1. **Visitor Management**
         - Input visitor information
         - Capture photos using webcam
         - Record entry date and time
         - Select entry location

      2. **Location Management**
         - View all locations
         - Add new locations
         - Edit existing locations
         - Delete locations

      ## Testing
      1. Start XAMPP (Apache and MySQL)
      2. Open browser and navigate to: http://localhost/logbook_visitor
      3. Test the webcam functionality
      4. Test CRUD operations for visitors and locations

      ## Security Notes
      - Implement proper input validation
      - Secure file upload handling
      - Add user authentication if needed