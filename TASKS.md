# Links for Perfex - Future Development Tasks

This document tracks planned enhancements and features for the Links for Perfex module.

## High Priority Tasks

### 1. Automatic Link Title Fetching
**Description**: Automatically fetch and populate link titles when users paste URLs
- **Implementation**: Use ping/domain detection to fetch page titles
- **Method**: AJAX call to fetch `<title>` tag from target URL
- **Fallback**: If fetch fails, extract domain name as title
- **UX**: Show loading spinner while fetching, allow manual override
- **Security**: Validate URLs, prevent SSRF attacks, timeout handling

**Technical Approach**:
```php
// Controller method to fetch URL title
public function fetch_url_title() {
    $url = $this->input->post('url');
    // Validate URL
    // Fetch title using cURL with timeout
    // Return JSON response
}
```

### 2. Task Links in Project View
**Description**: Display all task-related links in project sections
- **Location**: Under project links section with "Task Related Links" heading
- **Grouping**: Group by task name/ID
- **Display**: Show task name + links in collapsible sections
- **Performance**: Lazy load or pagination for projects with many tasks
- **Permissions**: Respect task visibility permissions

**Implementation**:
- Add new helper function `get_project_task_links($project_id)`
- Modify project links view to include task links section
- Add task name as subtitle for each group

### 3. Links Section Redesign & Repositioning
**Description**: Better integration with existing Perfex UI
- **Position**: Move links section under task/project description
- **Design**: Minimal colors, reduced spacing, clean layout
- **Integration**: Use CodeIgniter view hooks for better positioning
- **Consistency**: Match existing Perfex design patterns

**Hook Integration**:
```php
// Use existing Perfex hooks for better integration
add_action('after_task_description', 'display_task_links');
add_action('after_project_description', 'display_project_links');
```

## Medium Priority Tasks

### 4. Link Categories & Tags
**Description**: Organize links with categories and tags
- Categories: Documentation, Resources, Files, External Tools
- Tags: Custom user-defined tags
- Filtering: Filter links by category/tag
- Color coding: Different colors for different categories

### 5. Link Validation & Status
**Description**: Check link accessibility and status
- **Status Checking**: Ping URLs to check if accessible (200, 404, etc.)
- **Visual Indicators**: Green/red status indicators
- **Automatic Checks**: Periodic background validation
- **Broken Link Alerts**: Notify when links become inaccessible

### 6. Link Import/Export
**Description**: Bulk operations for link management
- **Import**: CSV/JSON import for bulk link addition
- **Export**: Export project/task links to various formats
- **Templates**: Pre-defined link templates for common tools
- **Migration**: Import from other systems (Trello, Asana, etc.)

## Low Priority Tasks

### 7. Link Analytics & Tracking
**Description**: Track link usage and popularity
- Click tracking and analytics
- Most used links reporting
- Link usage statistics
- Integration with existing Perfex reports

### 8. Advanced Link Features
**Description**: Enhanced link functionality
- **Link Previews**: Show preview cards for common platforms
- **Shortened URLs**: Integration with URL shortening services
- **QR Codes**: Generate QR codes for links
- **Link Expiration**: Set expiration dates for temporary links

### 9. Mobile App Integration
**Description**: Extend functionality to Perfex mobile app
- Mobile-optimized link views
- Offline link caching
- Push notifications for link updates
- Mobile-specific UI/UX improvements

## Testing & Quality Assurance

### 10. Task Duplication Behavior
**Description**: Test and handle task duplication scenarios
- **Test**: How module reacts when tasks are duplicated in Perfex
- **Behavior**: Check if links are duplicated with tasks or remain with original
- **Expected**: Links should either duplicate with task or maintain clear relationships
- **Implementation**: May need hooks into Perfex's task duplication process
- **Edge Cases**: Handle permission changes, data integrity, relation_id updates

**Investigation Points**:
- Does Perfex provide hooks for task duplication?
- Should links be copied to new task or stay with original?
- How to handle relation_id updates in database?
- User experience expectations for duplicated task links

## Technical Debt & Improvements

### 11. Performance Optimization
- **Database Indexing**: Optimize queries with proper indexes
- **Caching**: Cache frequently accessed links
- **Lazy Loading**: Load links only when sections are expanded
- **Pagination**: Handle large numbers of links efficiently

### 12. Security Enhancements
- **URL Validation**: Stronger URL validation and sanitization
- **XSS Prevention**: Enhanced XSS protection for user input
- **Permission System**: Fine-grained permissions for link management
- **Audit Logging**: Track all link modifications

### 13. Code Quality
- **Unit Tests**: Add comprehensive test coverage
- **Code Documentation**: Improve inline documentation
- **Error Handling**: Better error handling and user feedback
- **Code Standards**: Ensure PSR compliance

## Implementation Notes

### Development Phases
1. **Phase 3**: Focus on high priority tasks (1-3)
2. **Phase 4**: Medium priority features (4-6)
3. **Phase 5**: Advanced features (7-9)
4. **Testing**: Quality assurance (10)
5. **Ongoing**: Technical improvements (11-13)

### Dependencies
- Some features may require additional PHP libraries
- Consider impact on existing Perfex installations
- Maintain backward compatibility
- Test with different Perfex versions

### Documentation Updates
- Update CLAUDE.md with new features
- Create user documentation for advanced features
- Update installation instructions if needed
- Maintain changelog for version tracking

---

**Note**: Priority levels may change based on user feedback and business requirements. Always test new features thoroughly before merging to master branch.