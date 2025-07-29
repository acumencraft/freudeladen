# FREUDELADEN.DE - Consolidated Technical Assignment

This document provides a complete technical specification and project plan for the development of the FREUDELADEN.DE e-commerce platform. It consolidates all project requirements, including the main site, administrator panel, and development roadmap.

---

## 1. Project Overview

The primary goal is to create a high-performance, secure, and SEO-optimized e-commerce platform for the German market. The platform will feature a minimalist design and a single-page application (SPA)-like user experience for seamless navigation.

| Item                | Value                                            |
| ------------------- | ------------------------------------------------ |
| **Project Name**    | FREUDELADEN.DE                                   |
| **Language**        | German                                           |
| **Git Repository**  | `https://github.com/acumencraft/freudeladen.git` |
| **Dev Environment** | DDEV, Visual Studio Code, AI agent integration   |

Export to Sheets

---

## 2. Technical Stack

The project will be built using the following technologies, incorporating the specified changes to the web server and backend framework.

| Component              | Technology                                       |
| ---------------------- | ------------------------------------------------ |
| **Backend Framework**  | **Yii Framework 2 Basic Project Template**       |
| **Frontend Framework** | Bootstrap 5.3+                                   |
| **JavaScript**         | Vanilla JS (ES6+) with Fetch API and History API |
| **Database**           | MariaDB 10.6+                                    |
| **PHP Version**        | PHP 8.2+                                         |
| **Web Server**         | **Apache2 (DDEV configuration)**                 |
| **Cloud Storage**      | AWS S3 (or compatible) for media                 |
| **Helpdesk/Live Chat** | Third-party integration (e.g., Tawk.to, Crisp)   |

Export to Sheets

---

## 3. Functional Requirements

### 3.1. Public-Facing Website

#### **UI/UX & Design**

- **Theme**: Minimalist with a white background (`#FFFFFF`) and black text (`#212529`).

- **Navigation**:
  
  - Asynchronous page loads using the History API for an SPA-like feel.
  
  - Main menu for e-commerce sections.
  
  - Secondary menu (footer/top) for static pages like *Impressum* and *AGB*.
  
  - Footer containing contact information and a live chat widget.

#### **Core Features**

- **Homepage**:
  
  - Carousel for "Popular Products".
  
  - Carousel for "Discounted Products".
  
  - Grid of the 12 latest blog posts with pagination.

- **E-commerce**:
  
  - Multi-level product categories with filtering and sorting.
  
  - Product detail pages with a media gallery (images/videos from S3), description, price, and social sharing buttons.
  
  - A fully asynchronous shopping cart and a one-page checkout process.

- **Blog**: A minimalist, SEO-optimized blog with post lists, pagination, and detailed post views.

- **Customer Support**:
  
  - Integrated Live Chat widget on all pages.
  
  - A helpdesk page (FAQ, tickets) integrated with the chat system.

- **Payment Systems**:
  
  - **Stripe**: Credit/debit card payments via Stripe Elements.
  
  - **PayPal**: Standard PayPal integration.
  
  - **Cryptocurrency**: Integration with a service like Coinbase Commerce or BitPay.
  
  - **Bank Transfer**: Display of bank details upon order completion.

### 3.2. Administrator Panel

The admin panel will provide comprehensive control over the entire platform.

- **Authentication**: Secure login with Role-Based Access Control (RBAC).

- **Dashboard**: A visual overview of key statistics (sales, users, orders).

- **Product Management**: Full CRUD for products, including variations, inventory, and SEO fields. Media uploads managed via AWS S3.

- **Category Management**: Interface for creating and managing a multi-level category hierarchy (drag-and-drop).

- **Order Management**: View, filter, and update order statuses.

- **Blog Management**: Full CRUD for blog posts and categories with a WYSIWYG editor and SEO controls.

- **Content Management**: Manage static pages (e.g., Privacy Policy), banners, and FAQs.

- **User Management**: View and edit customer and administrator accounts and roles.

- **Settings Management**: Configure site-wide parameters, social media links, and API keys for third-party services.

- **AI Integrations**:
  
  - AI-powered generator for product descriptions and SEO metadata.
  
  - Analytics insights presented in natural language.

---

## 4. SEO, Performance, and Accessibility

### 4.1. Technical SEO

- **URLs**: Clean, user-friendly URLs with auto-generated slugs.

- **Metadata**: Canonical URLs, `sitemap.xml`, and `robots.txt` generation.

- **Structured Data (JSON-LD)**: Implementation of `Product`, `Article`, `BlogPosting`, `BreadcrumbList`, and `Organization` schemas.

- **Redirects**: A management interface for 301/302 redirects.

### 4.2. Performance Optimization

- **Images**: Lazy loading and automatic conversion to modern formats like WebP on upload to S3.

- **Assets**: Minification and bundling of CSS/JS files using Yii's asset pipeline.

- **Caching**: Aggressive use of Yii's caching mechanisms for data and page fragments.

- **CDN**: Integration with a CDN like AWS CloudFront or Cloudflare for media and assets.

### 4.3. Accessibility (a11y)

- **Markup**: Use of semantic HTML5.

- **ARIA**: Proper implementation of ARIA attributes for dynamic components.

- **Navigation**: Full keyboard navigability for all interactive elements.

---

## 5. Database Structure

The following SQL schemas define the consolidated database structure for the application.

SQL

```
-- Core E-commerce Tables
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description TEXT,
    sku VARCHAR(64) UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    stock INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE product_variants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    name VARCHAR(255) NOT NULL, -- e.g., "Color: Red, Size: L"
    sku VARCHAR(64) UNIQUE,
    price_modifier DECIMAL(10,2) DEFAULT 0.00,
    stock INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_main TINYINT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    total DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status VARCHAR(50) DEFAULT 'pending',
    shipping_address TEXT,
    billing_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT,
    variant_id INT,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
);

-- User & Auth Tables
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    auth_key VARCHAR(32) NOT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Content Management Tables
CREATE TABLE blog_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    excerpt TEXT,
    author_id INT,
    status TINYINT DEFAULT 1, -- 0: draft, 1: published
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
);

CREATE TABLE static_pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    is_in_menu TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- System & SEO Tables
CREATE TABLE seo_meta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entity_type VARCHAR(50) NOT NULL, -- 'product', 'category', 'blog_post', 'static_page'
    entity_id INT NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    UNIQUE(entity_type, entity_id)
);

CREATE TABLE admin_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 6. Technical Implementation & Environment

### 6.1. DDEV Configuration

The local development environment will use DDEV with the following configuration:

YAML

```
name: freudeladen
type: yii2
docroot: web
php_version: "8.2"
webserver_type: apache-fpm
mariadb_version: "10.6"
nodejs_version: "18"
hooks:
  post-start:
    - exec: "composer install"
```

### 6.2. API and File Uploads

- **API**: A RESTful API will be developed using Yii2 to serve all data to the frontend, enabling the SPA-like experience.

- **File Uploads**: The `yii2-flysystem` component will be used to handle all media uploads directly to the configured AWS S3 bucket.

---

## 7. Deployment Plan (Hostinger.com)

1. **Preparation**: Run `composer install --no-dev --optimize-autoloader` and disable debug mode in the application's entry script.

2. **Hostinger Setup**: Create a MySQL/MariaDB database, ensure PHP 8.2+ is selected, and activate SSH access.

3. **File Deployment**: Use Git over SSH to pull the latest version of the main branch into the `public_html` directory.

4. **Configuration**:
   
   - Set the server's Document Root to point to the `public_html/web` directory.
   
   - Create a `.env` file in the project root with production database credentials and all necessary API keys.
   
   - Run database migrations using `php yii migrate`.
   
   - Set writable permissions (e.g., `CHMOD 775`) on the `runtime` and `web/assets` directories.

---

## 8. Project Timeline and Milestones

**Phase 1: Project Setup and Foundation**

- Tasks: Configure DDEV environment, install Yii2 Basic Template, set up initial database schema and migrations, implement basic layout templates.

- Milestone: A runnable local application with a basic structure.

**Phase 2: Core E-commerce Functionality**

- Tasks: Implement product catalog, multi-level categories, product detail pages, asynchronous shopping cart, and one-page checkout.

- Milestone: Users can browse products, add them to a cart, and complete a checkout.

**Phase 3: Admin Panel Development**

- Tasks: Implement RBAC, dashboard, and management interfaces for products, categories, orders, and users.

- Milestone: A fully functional admin panel for store management.

**Phase 4: Blog Implementation**

- Tasks: Develop blog models, views, and controllers; implement WYSIWYG editor and SEO features in the admin panel.

- Milestone: A fully functional blog that can be managed from the admin panel.

**Phase 5: Payment and Third-Party Integrations**

- Tasks: Integrate Stripe, PayPal, and a cryptocurrency payment gateway; set up the helpdesk/live chat system.

- Milestone: All payment and support systems are fully operational.

**Phase 6: SEO and Performance Optimization**

- Tasks: Implement structured data, sitemap generation, image optimization (WebP, lazy loading), asset bundling, caching, and CDN setup.

- Milestone: The site meets performance and technical SEO benchmarks.

**Phase 7: Testing and Bug Fixing**

- Tasks: Conduct cross-browser testing, mobile responsiveness checks, security audits, and load testing; resolve all identified issues.

- Milestone: A stable, secure, and bug-free application.

**Phase 8: Deployment and Launch**

- Tasks: Prepare the production environment on Hostinger, deploy the code, configure SSL, and perform final production tests.

- Milestone: The website is live and publicly accessible.