# Udara NEWS - Database Setup Guide

## PHPMyAdmin Setup Instructions

### 1. Start Your Local Server
- Start XAMPP, WAMP, or MAMP
- Make sure Apache and MySQL services are running

### 2. Access PHPMyAdmin
- Open your browser and go to: `http://localhost/phpmyadmin`
- Login with your MySQL credentials (default: username: `root`, password: empty)

### 3. Database Creation
The database will be created automatically when you first visit the website, but you can also create it manually:

1. Click "New" in the left sidebar
2. Enter database name: `udara_news`
3. Select collation: `utf8mb4_unicode_ci`
4. Click "Create"

### 4. Database Structure
The system will automatically create the following table:

```sql
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `content` text,
  `image` varchar(500) DEFAULT NULL,
  `category` enum('politics','sports','technology','business','general') DEFAULT 'general',
  `author` varchar(100) NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. Configuration
Update the database configuration in `config.php` if needed:

```php
define('DB_HOST', 'localhost');     // Your MySQL host
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password
define('DB_NAME', 'udara_news');    // Database name
```

### 6. Admin Panel Access
- Visit: `http://localhost/your-project-folder/admin_login.php`
- **Default Credentials:**
  - Username: `admin`
  - Password: `udara123`
- **Security Features:**
  - Session-based authentication
  - 24-hour session timeout
  - Protected admin pages
  - Secure logout functionality

## Features Available

### ✅ Complete CRUD Operations
- **Create**: Add new articles with title, summary, content, images, categories
- **Read**: View all articles in admin panel and individual article view
- **Update**: Edit existing articles with dedicated edit page
- **Delete**: Remove articles with confirmation

### ✅ Admin Panel Features
- Dashboard with statistics (total articles, featured articles, today's articles)
- Article management table with sortable columns
- Quick actions (View, Edit, Delete)
- Responsive design for mobile and desktop

### ✅ Database Features
- Automatic database and table creation
- Sample data insertion on first run
- Proper indexing for performance
- UTF-8 support for international content

### ✅ Article Management
- Categories: Politics, Sports, Technology, Business, General
- Featured article system
- Author attribution
- Automatic timestamps
- Image URL support
- Rich content support

## Security Features ✅

1. **✅ Authentication System**: Secure login with session management
2. **✅ Session Security**: 24-hour timeout and session validation
3. **✅ Protected Pages**: All admin pages require authentication
4. **✅ SQL Injection Protection**: Prepared statements used throughout
5. **✅ Input Validation**: Server-side validation on all forms
6. **✅ Secure Logout**: Proper session destruction

## Additional Security for Production

1. **Change Default Password**: Update credentials in `admin_login.php`
2. **Database Security**: Use environment variables for credentials
3. **File Upload**: Replace URL input with secure file upload
4. **HTTPS**: Use SSL certificates in production
5. **Rate Limiting**: Add login attempt restrictions
6. **Password Hashing**: Implement bcrypt for password storage

## Troubleshooting

### Common Issues:

1. **Database Connection Failed**
   - Check if MySQL service is running
   - Verify credentials in config.php
   - Ensure database exists

2. **Permission Denied**
   - Check file permissions
   - Ensure web server has read/write access

3. **Images Not Loading**
   - Verify image URLs are accessible
   - Check for HTTPS/HTTP mixed content issues

### File Structure:
```
/project-folder/
├── config.php              # Database configuration
├── admin_login.php         # Admin login page (START HERE)
├── admin.php               # Main admin panel (protected)
├── edit_article.php        # Edit article page (protected)
├── view_article.php        # View article page (protected)
├── admin_logout.php        # Logout handler
├── get_news.php            # API for fetching news
├── get_featured_news.php   # API for featured news
├── index.html              # Main website
├── styles.css              # Styles
├── script.js               # JavaScript
└── DATABASE_SETUP.md       # This file
```

### Admin Access Flow:
1. Visit: `admin_login.php` 
2. Login with credentials (admin/udara123)
3. Access admin panel features
4. Logout when finished