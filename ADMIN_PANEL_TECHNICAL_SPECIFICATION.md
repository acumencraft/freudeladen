# FREUDELADEN.DE - Administrator Panel Technical Specification

**Document Created:** 2025-07-28
**Author:** acumencraft

## Overview

This technical specification outlines the development requirements for the web-based administration panel for the FREUDELADEN.DE online store. The admin panel will provide comprehensive management of products, orders, users, content, shipping, payments, SEO parameters, and analytics.

## 1. 🔐 Authentication and Authorization

### 1.1 Login System

**Technical Requirements:**
- Implement using Yii2's built-in authentication system with RBAC (Role-Based Access Control)
- Login form with CSRF protection and rate limiting (max 5 failed attempts before temporary lockout)
- Session timeout after 30 minutes of inactivity
- Remember me functionality with secure cookie implementation

**Database Structure:**
```sql
CREATE TABLE admin_user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    auth_key VARCHAR(32) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    password_reset_token VARCHAR(255) UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    status SMALLINT NOT NULL DEFAULT 10,
    created_at INT NOT NULL,
    updated_at INT NOT NULL,
    last_login_at INT
);

CREATE TABLE auth_assignment (
    item_name VARCHAR(64) NOT NULL,
    user_id VARCHAR(64) NOT NULL,
    created_at INT,
    PRIMARY KEY (item_name, user_id)
);

CREATE TABLE auth_item (
    name VARCHAR(64) NOT NULL PRIMARY KEY,
    type SMALLINT NOT NULL,
    description TEXT,
    rule_name VARCHAR(64),
    data TEXT,
    created_at INT,
    updated_at INT
);

CREATE TABLE auth_item_child (
    parent VARCHAR(64) NOT NULL,
    child VARCHAR(64) NOT NULL,
    PRIMARY KEY (parent, child),
    FOREIGN KEY (parent) REFERENCES auth_item (name) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (child) REFERENCES auth_item (name) ON DELETE CASCADE ON UPDATE CASCADE
);
```

### 1.2 Password Recovery

**Technical Requirements:**
- Secure token-based password reset system
- Time-limited reset tokens (valid for 24 hours)
- Email notifications for password reset requests
- Password strength requirements (min 8 characters, requiring uppercase, lowercase, numbers)

### 1.3 Activity Logging

**Technical Requirements:**
- Log all admin actions with timestamps, IP addresses, and user IDs
- Detailed logs for critical operations (order status changes, payment processing, user management)
- Searchable and filterable log interface
- Log retention policy (90 days for regular logs, 1 year for security-related logs)

**Database Structure:**
```sql
CREATE TABLE admin_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    object_type VARCHAR(255),
    object_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_user(id) ON DELETE SET NULL
);
```

## 2. 🛍️ Product Management

### 2.1 Product Data Structure

**Database Tables:**
```sql
CREATE TABLE product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    short_description TEXT,
    sku VARCHAR(64) UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    stock INT DEFAULT 0,
    category_id INT,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL
);

CREATE TABLE product_image (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_main TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
);

CREATE TABLE product_variant (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    sku VARCHAR(64),
    size VARCHAR(50),
    color VARCHAR(50),
    price DECIMAL(10,2),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
);

CREATE TABLE product_seo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL UNIQUE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
);
```

### 2.2 Product Management UI

**Technical Requirements:**
- CRUD operations for all product data
- Rich text editor (TinyMCE) for product descriptions
- Multi-file upload for product images with drag-and-drop functionality
- Asynchronous validation of SKU uniqueness
- Variant management interface (add/edit/remove sizes, colors, prices)
- Real-time inventory tracking
- Batch operations for multiple products (update status, categories, prices)

### 2.3 Product Search and Filtering

**Technical Requirements:**
- Elasticsearch integration for advanced product search
- Filters by category, status, price range, stock level
- Bulk actions on filtered results
- Export functionality (CSV, Excel)

### 2.4 Product Cloning

**Technical Requirements:**
- One-click product duplication with unique SKU generation
- Option to copy all or select attributes (images, variants, descriptions)
- Post-clone edit view for immediate modifications

## 3. 📦 Order Management

### 3.1 Order Data Structure

```sql
CREATE TABLE `order` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    payment_status VARCHAR(50) DEFAULT 'pending',
    shipping_status VARCHAR(50) DEFAULT 'pending',
    total DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0,
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    payment_method VARCHAR(50),
    tracking_number VARCHAR(100),
    shipping_address TEXT,
    billing_address TEXT,
    customer_notes TEXT,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL
);

CREATE TABLE order_item (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT,
    variant_id INT,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(64),
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    options TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES `order`(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE SET NULL,
    FOREIGN KEY (variant_id) REFERENCES product_variant(id) ON DELETE SET NULL
);

CREATE TABLE order_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    comment TEXT,
    admin_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES `order`(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admin_user(id) ON DELETE SET NULL
);
```

### 3.2 Order Management Interface

**Technical Requirements:**
- Comprehensive order view with customer details, items, payment and shipping information
- Order status management (Processing, Shipped, Completed, Cancelled)
- Internal notes system for admin comments
- Customer communication system (email templates for status updates)
- Order history timeline view
- Quick filters for order status

### 3.3 Invoice Generation

**Technical Requirements:**
- PDF invoice generation using mPDF or TCPDF library
- Customizable invoice template
- Auto-generated invoice numbers
- Option for digital delivery of invoices to customers

## 4. 👥 User Management

### 4.1 User Data Structure

```sql
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(50),
    phone_verified TINYINT DEFAULT 0,
    email_verified TINYINT DEFAULT 0,
    password_hash VARCHAR(255) NOT NULL,
    auth_key VARCHAR(32) NOT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE user_address (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('billing', 'shipping') NOT NULL,
    is_default TINYINT DEFAULT 0,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    company VARCHAR(100),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);
```

### 4.2 User Management Interface

**Technical Requirements:**
- User listing with search and filters
- Detailed user profile view
- Order history per user
- Account status management (Active, Inactive, Blocked)
- Manual verification options for email and phone
- Export functionality (CSV)

## 5. 🚚 Shipping Management

### 5.1 Shipping Data Structure

```sql
CREATE TABLE shipping_method (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE shipping_zone (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    countries TEXT,
    regions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE shipping_rate (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shipping_method_id INT NOT NULL,
    shipping_zone_id INT NOT NULL,
    min_order_total DECIMAL(10,2) DEFAULT 0,
    max_order_total DECIMAL(10,2) DEFAULT 999999.99,
    rate DECIMAL(10,2) NOT NULL,
    is_free_shipping TINYINT DEFAULT 0,
    free_shipping_min_amount DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shipping_method_id) REFERENCES shipping_method(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_zone_id) REFERENCES shipping_zone(id) ON DELETE CASCADE
);
```

### 5.2 Shipping Management Interface

**Technical Requirements:**
- Shipping method configuration
- Geographic zone management with country/region selection
- Conditional rate setting (based on order total, weight, destination)
- Free shipping threshold configuration
- Tracking link template configuration for major carriers

## 6. 💳 Payment Management

### 6.1 Payment Data Structure

```sql
CREATE TABLE payment_method (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT DEFAULT 1,
    provider VARCHAR(50),
    configuration TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE payment_transaction (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    payment_method_id INT,
    transaction_id VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES `order`(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_method(id) ON DELETE SET NULL
);
```

### 6.2 Payment Management Interface

**Technical Requirements:**
- Payment method activation and configuration
- API key management for payment providers (Stripe, PayPal)
- Transaction monitoring dashboard
- Refund processing interface
- Payment reconciliation tools

## 7. 🧾 Content Management (CMS)

### 7.1 Banner Management

**Database Structure:**
```sql
CREATE TABLE banner (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    image_path VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    position VARCHAR(50),
    start_date DATE,
    end_date DATE,
    is_active TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Technical Requirements:**
- Banner creation with image upload
- Scheduling functionality (start/end dates)
- Positioning options (homepage slider, category pages, sidebar)
- Sorting mechanism for banner display order

### 7.2 Static Page Management

**Database Structure:**
```sql
CREATE TABLE page (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    is_in_menu TINYINT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Technical Requirements:**
- WYSIWYG editor for page content
- HTML mode for advanced editing
- Menu inclusion option
- SEO metadata management

### 7.3 Blog Management

**Database Structure:**
```sql
CREATE TABLE blog_category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE blog_post (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    excerpt TEXT,
    category_id INT,
    featured_image VARCHAR(255),
    meta_title VARCHAR(255),
    meta_description TEXT,
    status TINYINT DEFAULT 0, -- 0: draft, 1: published
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_category(id) ON DELETE SET NULL
);

CREATE TABLE blog_tag (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blog_post_tag (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES blog_post(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES blog_tag(id) ON DELETE CASCADE
);
```

**Technical Requirements:**
- Blog post editor with WYSIWYG capabilities
- Featured image upload and management
- Category and tag management
- Post scheduling functionality
- Draft and published states

### 7.4 FAQ Management

**Database Structure:**
```sql
CREATE TABLE faq_category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE faq (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES faq_category(id) ON DELETE SET NULL
);
```

**Technical Requirements:**
- FAQ category management
- Question and answer editor
- Drag-and-drop reordering
- Active/inactive toggling

## 8. 📈 Analytics

### 8.1 Dashboard Analytics

**Technical Requirements:**
- Sales overview charts (daily/weekly/monthly)
- Revenue metrics with comparison to previous periods
- Top-selling products display
- Recent order activity feed
- Customer registration statistics

**Implementation Details:**
- Server-side data aggregation using SQL queries
- Client-side visualization using Chart.js
- Data caching for performance optimization
- Customizable date ranges for all analytics

### 8.2 Sales Reports

**Technical Requirements:**
- Detailed sales reports by date range
- Product performance analysis
- Category performance analysis
- Payment method distribution
- Shipping method distribution
- Export functionality (CSV, Excel, PDF)

### 8.3 Customer Analytics

**Technical Requirements:**
- New vs. returning customer metrics
- Customer acquisition channels
- Geographic distribution of customers
- Browser and device usage statistics
- Average order value per customer

### 8.4 Inventory Reports

**Technical Requirements:**
- Low stock alerts and reporting
- Stock movement history
- Most/least active inventory items
- Projected inventory needs based on sales velocity

## 9. 🌐 SEO Management

### 9.1 Global SEO Settings

**Database Structure:**
```sql
CREATE TABLE seo_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    site_name VARCHAR(255) NOT NULL,
    meta_title_template VARCHAR(255),
    meta_description_template TEXT,
    default_robots VARCHAR(50) DEFAULT 'index,follow',
    google_analytics_id VARCHAR(50),
    google_site_verification VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE redirect (
    id INT PRIMARY KEY AUTO_INCREMENT,
    source_url VARCHAR(255) NOT NULL,
    target_url VARCHAR(255) NOT NULL,
    redirect_type ENUM('301', '302') DEFAULT '301',
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Technical Requirements:**
- Global site meta information configuration
- Template-based meta title and description generation
- Google Analytics and Search Console integration
- Default robots meta tag settings

### 9.2 Entity-specific SEO

**Technical Requirements:**
- Individual SEO fields for products, categories, blog posts, and static pages
- Automatic slug generation with manual override option
- Canonical URL management
- Structured data (JSON-LD) configuration for products and content

### 9.3 Sitemap Management

**Technical Requirements:**
- Automated XML sitemap generation
- Configurable update frequency for different content types
- Manual regeneration option
- Automatic submission to search engines

### 9.4 SEO Audit Tool

**Technical Requirements:**
- Integration with AI-based SEO analysis service
- On-page SEO recommendations
- Content optimization suggestions
- Duplicate content detection
- Missing metadata alerts

## 10. ⚙️ System Settings

### 10.1 Store Information

**Database Structure:**
```sql
CREATE TABLE store_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_name VARCHAR(255) NOT NULL,
    store_email VARCHAR(255),
    store_phone VARCHAR(50),
    store_address TEXT,
    currency_code VARCHAR(3) DEFAULT 'EUR',
    timezone VARCHAR(100) DEFAULT 'Europe/Berlin',
    working_hours TEXT,
    logo_path VARCHAR(255),
    favicon_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Technical Requirements:**
- Basic store information management
- Working hours configuration
- Currency and timezone settings
- Logo and favicon upload

### 10.2 Email Templates

**Database Structure:**
```sql
CREATE TABLE email_template (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables TEXT,
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Technical Requirements:**
- Email template editor with HTML support and variable placeholders
- Template preview functionality
- Test email sending capability
- Support for all system notifications (order confirmation, shipping, password reset)

### 10.3 Tax Settings

**Database Structure:**
```sql
CREATE TABLE tax_rate (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    rate DECIMAL(5,2) NOT NULL,
    country VARCHAR(100),
    state VARCHAR(100),
    is_default TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Technical Requirements:**
- Tax rate configuration by geographic region
- Default tax rate setting
- Tax calculation method selection (inclusive/exclusive)

## 11. 🤖 AI Integrations

### 11.1 Product Description Generator

**Technical Requirements:**
- Integration with OpenAI API or similar service
- Product description generation from basic product information
- Style and tone configuration
- Edit and review interface for generated content
- Batch generation capability

### 11.2 SEO Content Generator

**Technical Requirements:**
- AI-powered generation of meta titles and descriptions
- Keyword optimization suggestions
- Content readability analysis
- Alternative text suggestions for product images

### 11.3 Analytics Insights

**Technical Requirements:**
- Natural language summaries of sales and customer data
- Actionable recommendations based on performance metrics
- Anomaly detection and alerting
- Trend prediction

## Technical Implementation Guidelines

### Backend Architecture

- Follow Yii2 MVC architecture
- Implement RESTful API for all admin operations
- Use Yii2 RBAC for access control
- Implement comprehensive data validation
- Maintain audit logs for all critical operations

### Frontend Implementation

- Bootstrap 5.3 for responsive admin UI
- AJAX for asynchronous data operations
- Chart.js for data visualization
- Progressive enhancement for all JavaScript functionality
- Cross-browser compatibility (Chrome, Firefox, Safari, Edge)

### Security Measures

- Input validation for all form fields
- XSS protection
- CSRF protection
- SQL injection prevention
- Rate limiting for sensitive operations
- Two-factor authentication option for admin users

### Performance Considerations

- Database query optimization
- Data caching where appropriate
- Pagination for large data sets
- Asynchronous processing for resource-intensive operations
- Image optimization for uploaded content

## UI/UX Guidelines

### Admin Dashboard Layout

- Clean, minimalist design with white background (#FFFFFF) and dark text (#212529)
- Left sidebar navigation with collapsible menu
- Top header with quick access to notifications and user profile
- Responsive design for desktop and tablet use
- Consistent card-based content layout

### Interactive Elements

- Drag-and-drop interfaces for ordering items
- In-place editing for quick updates
- Modal dialogs for form inputs
- Toast notifications for action confirmations
- Loading indicators for asynchronous operations

### Accessibility

- ARIA attributes for all interactive elements
- Keyboard navigation support
- Sufficient color contrast (WCAG AA compliance)
- Screen reader compatibility
- Focus indicators for interactive elements