# Udara NEWS Website

A modern, responsive news website built with HTML, CSS, JavaScript, and PHP. Features include news categorization, search functionality, featured articles, and an admin panel for content management.

## Features

- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **News Categories**: Politics, Sports, Technology, Business, and General
- **Search Functionality**: Search through news articles by title, summary, or content
- **Featured Articles**: Highlight important news stories
- **Interactive UI**: Smooth animations, hover effects, and mobile menu
- **Admin Panel**: Easy content management system
- **Modern Design**: Clean, professional appearance with gradient backgrounds

## File Structure

```
Udara/
├── index.html              # Main website homepage
├── styles.css              # CSS styling for the website
├── script.js               # JavaScript for interactivity
├── config.php              # Database configuration and initialization
├── get_news.php            # API endpoint to fetch news articles
├── get_featured_news.php   # API endpoint to fetch featured articles
├── search_news.php         # API endpoint for search functionality
├── admin.php               # Admin panel for content management
└── README.md               # This file
```

## Setup Instructions

### Prerequisites

- **Web Server**: Apache, Nginx, or any web server with PHP support
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Optional**: XAMPP, WAMP, or MAMP for local development

### Installation Steps

1. **Clone or Download the Project**
   ```bash
   # Clone the repository or download the files to your web server directory
   # For XAMPP: C:\xampp\htdocs\udara-news\
   # For WAMP: C:\wamp64\www\udara-news\
   ```

2. **Database Setup**
   - The database and tables will be created automatically when you first access any PHP file
   - Default database settings (can be modified in `config.php`):
     - Database Name: `udara_news`
     - Host: `localhost`
     - Username: `root`
     - Password: `` (empty)

3. **Configure Database Connection** (if needed)
   - Edit `config.php` and modify the database constants:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'udara_news');
   ```

4. **Start Your Web Server**
   - For XAMPP: Start Apache and MySQL services
   - For WAMP: Start all services
   - For live server: Ensure PHP and MySQL are running

5. **Access the Website**
   - Homepage: `http://localhost/udara-news/index.html`
   - Admin Panel: `http://localhost/udara-news/admin.php`

## Usage

### Viewing News
- Visit the homepage to see the latest news articles
- Use the filter buttons to view news by category
- Click "Load More News" to see additional articles
- Use the search bar in the header to search for specific content
- Click on any news card to view the full article in a modal

### Managing Content (Admin Panel)
1. Access the admin panel at `admin.php`
2. **Add New Articles**:
   - Fill out the form with title, summary, content, image URL, category, and author
   - Check "Featured Article" to highlight the article
   - Click "Add Article" to save

3. **Edit Articles**:
   - Click "Edit" next to any article in the table
   - Follow the prompts to update the article information

4. **Delete Articles**:
   - Click "Delete" next to any article
   - Confirm the deletion when prompted

## Customization

### Styling
- Modify `styles.css` to change colors, fonts, layout, or add new styles
- The website uses a blue and red color scheme that can be easily customized
- Responsive breakpoints are set at 768px and 480px

### Content
- Sample news data is automatically inserted when the database is first created
- Add your own news articles through the admin panel
- Replace placeholder images with actual news photos

### Features
- Add new news categories by modifying the database enum and updating the forms
- Implement user authentication for the admin panel
- Add comment system or social sharing features
- Integrate with external news APIs

## Database Schema

### `news` Table
```sql
id (INT, AUTO_INCREMENT, PRIMARY KEY)
title (VARCHAR(255), NOT NULL)
summary (TEXT, NOT NULL)
content (TEXT)
image (VARCHAR(500))
category (ENUM: politics, sports, technology, business, general)
author (VARCHAR(100), NOT NULL)
is_featured (BOOLEAN, DEFAULT FALSE)
created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
updated_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
```

## API Endpoints

### GET `/get_news.php`
- **Parameters**: 
  - `category` (optional): Filter by category (politics, sports, technology, business, general, all)
  - `page` (optional): Page number for pagination (default: 1)
- **Response**: JSON array of news articles

### GET `/get_featured_news.php`
- **Response**: JSON array of featured news articles (max 4)

### GET `/search_news.php`
- **Parameters**: 
  - `q` (required): Search query string
- **Response**: JSON array of matching news articles

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Internet Explorer 11+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For support or questions, please contact the development team or create an issue in the project repository.

## Version History

- **v1.0.0** - Initial release with core functionality
  - Responsive design
  - News categorization and filtering
  - Search functionality
  - Admin panel
  - Featured articles system