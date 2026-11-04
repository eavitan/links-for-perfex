# Links for Perfex

A powerful Perfex CRM module that adds comprehensive link management functionality to both tasks and projects.

## Features

- ✅ **Task Links**: Add, edit, and delete links within individual tasks
- ✅ **Project Links**: Full link management with dedicated "Links" tab in project views
- ✅ **Rich Link Data**: URL, title, description, and automatic icon detection
- ✅ **Smart UI**: Integrated forms (no popups), real-time updates, responsive design
- ✅ **Permissions**: Respects Perfex CRM's existing permission system
- ✅ **Demo Fallback**: Shows sample links when no real links exist

## Installation

1. **Upload Module**: Copy the `links_for_perfex` folder to your Perfex CRM's `modules/` directory

2. **Activate Module**: Go to **Setup → Modules** and activate "Links for Perfex"

3. **Database Setup**: The database table is created automatically during activation

## Uninstall

**⚠️ Important**: Uninstall behavior depends on your environment:

- **Development Environment** (`ENVIRONMENT === 'development'`): Complete uninstall with database table deletion and all data removal
- **Production/Other Environments**: Safe uninstall that preserves all data (only deactivates the module)

To uninstall:
1. Go to **Setup → Modules**
2. Find "Links for Perfex" and click **Uninstall**
3. Confirm the action

**Note**: This environment-based behavior ensures data safety in production while allowing complete cleanup in development.

## Usage

### Task Links
- Navigate to any task view
- Scroll to the "Task Links" section
- Click "Add Link" to add URLs, documentation, repositories, etc.
- Links appear inline with edit/delete options

### Project Links
- Open any project
- Click the "Links" tab (appears next to Overview, Tasks, etc.)
- Manage all project-related links in one organized location
- Perfect for project documentation, repositories, design files

## Database Schema

The module creates a `tbllinks_for_perfex` table with the following structure:

```sql
CREATE TABLE `tbllinks_for_perfex` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(500) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `relation` enum('task','project') NOT NULL,
  `relation_id` int NOT NULL,
  `dateadded` datetime NOT NULL,
  `addedfrom` int NOT NULL,
  `last_modified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_relation` (`relation`, `relation_id`),
  INDEX `idx_addedfrom` (`addedfrom`),
  INDEX `idx_dateadded` (`dateadded`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Smart Features

### Automatic Icon Detection
Links automatically display appropriate icons based on the URL:
- 🐙 GitHub repositories → GitHub icon
- 📄 Google Docs/Drive → Google icon
- 🎨 Figma designs → Paint brush icon
- 📺 YouTube videos → YouTube icon
- 📦 Dropbox files → Dropbox icon
- 🔗 Other links → Generic link icon

### Real-time Updates
- No page refreshes after adding/editing/deleting links
- Instant UI updates using AJAX
- Smooth animations and user feedback

### Security Features
- CSRF token protection on all forms
- Input validation and sanitization
- Permission-based access control
- SQL injection prevention

## File Structure

```
modules/links_for_perfex/
├── links_for_perfex.php          # Main module file
├── install.php                   # Database setup & activation
├── controllers/
│   └── Links_for_perfex.php      # AJAX endpoints & demos
├── models/
│   └── Links_model.php           # Database operations
├── views/
│   ├── admin/
│   │   ├── projects/
│   │   │   └── project_links.php # Project links tab
│   │   └── tasks/
│   │       └── _links_section.php # Task links section
├── helpers/
│   ├── links_for_perfex_helper.php         # Helper functions
│   └── links_for_perfex_install_helper.php # Install utilities
└── language/
    └── english/
        └── links_for_perfex_lang.php       # Language strings
```

## Technical Details

- **Framework**: Built for CodeIgniter 3.x (Perfex CRM's framework)
- **Database**: MySQL/MariaDB compatible
- **PHP**: Requires PHP 7.4+ (same as Perfex CRM)
- **Integration**: Uses Perfex CRM's native hook and tab systems
- **No Core Modifications**: Completely self-contained module
- **Environment Detection**: Uses PHP's `ENVIRONMENT` constant (defined in index.php)

## Hooks Used

- `admin_init` → Menu items and project tabs
- `get_task` → Add links data to task objects
- `before_task_description_section` → Inject task links section

## Permissions

The module respects existing Perfex CRM permissions:
- **Task Links**: Requires task view/edit permissions
- **Project Links**: Requires project view/edit permissions
- **Admin Features**: Admin users have full access
- **User Restrictions**: Users can only edit their own links

## Browser Support

- ✅ Chrome/Chromium
- ✅ Firefox
- ✅ Safari
- ✅ Edge
- 📱 Mobile responsive

## Version History

### v2.0.1 (2026-10-30, current)
- A task with no links shows only the add and refresh buttons: the title, the help text and
  "no links found" said nothing the buttons did not

### v2.0.0
- Complete rewrite with new module structure
- Database creation moved to install.php (no migrations)
- Improved UI with integrated forms
- Better error handling and user feedback
- Enhanced security features

### v1.x.x (Legacy)
- Initial implementation as "Extended Task Manager"
- Migration-based database setup
- Popup-based forms

## Support

For issues, questions, or feature requests, please check:
1. The browser console for JavaScript errors
2. Perfex CRM logs for PHP errors
3. Database table creation in phpMyAdmin

## License

This module is provided as-is for Perfex CRM installations.

---

**Links for Perfex** - Making link management in Perfex CRM simple and powerful! 🔗