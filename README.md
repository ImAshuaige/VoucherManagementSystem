# Voucher-Management-System
This is a PHP assignment that primarily focuses on practicing CRUD operations for the Voucher object.

### Tools and Technologies Used 
1. Server Environment
   - XAMPP
2. Server-Side Language
   - PHP
3. Database Management
   - MySQL
4. Client-Side Technologies
   - HTML
   - CSS
   - JS
   - Bootstrap

### MySQL Database Setup
- dbName: voucher
- create table query: 
```
CREATE TABLE vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    voucher_type ENUM('GST', 'Rebate', 'CDAC') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    issued_date DATE NOT NULL,
    expiration_date DATE NOT NULL
);
```

- [phpMyAdmin](http://localhost/phpmyadmin)

### Steps to Run the Project
1. Start manage-osx
2. Go to Manage Servers, start:
- Apache Web Server 
- MySQL Database 
3. Access the pages from the following links:
- [Add Voucher](http://localhost/voucher/process_voucher.php)
- [Display Voucher](http://localhost/voucher/display_voucher.php) 
- [Import Vouchers From CSV](http://localhost/voucher/import_from_csv.php)

### Partial Interface Screenshots
![Voucher Listing](https://i.ibb.co/XshvKPZ/Voucher-Listing.png)
![Add Voucher](https://i.ibb.co/hLN22Yd/add-Voucher.png)
![ImportCSV](https://i.ibb.co/sH6Vwv9/import-Vouchers-From-CSV.png)
![Edit Voucher](https://i.ibb.co/kxXrDHk/Voucher-Editing.png)


