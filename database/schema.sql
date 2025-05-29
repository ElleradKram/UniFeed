-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS bullboard_db;
USE bullboard_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    role ENUM('student', 'admin', 'org_officer') DEFAULT 'student',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Organizations table
CREATE TABLE IF NOT EXISTS organizations (
    org_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    logo_url VARCHAR(255) DEFAULT NULL,
    contact_email VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#1B5E20',
    icon VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    org_id INT DEFAULT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    post_type ENUM('announcement', 'event', 'memo', 'discussion') DEFAULT 'discussion',
    image_url VARCHAR(255) DEFAULT NULL,
    event_date DATETIME DEFAULT NULL,
    is_pinned BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    comment_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (org_id) REFERENCES organizations(org_id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_comment_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (parent_comment_id) REFERENCES comments(comment_id) ON DELETE CASCADE
);

-- Votes table (for posts and comments)
CREATE TABLE IF NOT EXISTS votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT DEFAULT NULL,
    comment_id INT DEFAULT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (comment_id) REFERENCES comments(comment_id) ON DELETE CASCADE,
    UNIQUE KEY unique_post_vote (user_id, post_id),
    UNIQUE KEY unique_comment_vote (user_id, comment_id)
);

-- Saved posts table
CREATE TABLE IF NOT EXISTS saved_posts (
    save_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    UNIQUE KEY unique_save (user_id, post_id)
);

-- Organization members table
CREATE TABLE IF NOT EXISTS organization_members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    org_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('member', 'officer', 'president') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (org_id) REFERENCES organizations(org_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_membership (org_id, user_id)
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('comment', 'vote', 'mention', 'event', 'announcement') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_post_id INT DEFAULT NULL,
    related_comment_id INT DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (related_post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (related_comment_id) REFERENCES comments(comment_id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name, description, color, icon) VALUES
('General', 'General discussions and announcements', '#1B5E20', 'general'),
('Events', 'Upcoming events and activities', '#2196F3', 'calendar'),
('Academic', 'Academic-related discussions', '#FF9800', 'academic'),
('Sports', 'Sports and recreational activities', '#4CAF50', 'sports'),
('Technology', 'Tech-related discussions', '#9C27B0', 'tech'),
('Arts & Culture', 'Arts, culture, and creative activities', '#E91E63', 'arts');

-- Insert sample organizations
INSERT INTO organizations (name, description, logo_url, contact_email) VALUES
('Computer Science Society', 'Organization for Computer Science students', '/images/avatar.png', 'css@cvsu.edu.ph'),
('Student Government', 'Official student government body', '/images/avatar.png', 'sg@cvsu.edu.ph'),
('Engineering Society', 'Organization for Engineering students', '/images/avatar.png', 'es@cvsu.edu.ph'),
('Business Club', 'Organization for Business students', '/images/avatar.png', 'bc@cvsu.edu.ph');

-- Insert sample admin user (password: admin123)
INSERT INTO users (student_id, email, password, full_name, role) VALUES
('2021-00001', 'admin@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Insert sample student user (password: student123)
INSERT INTO users (student_id, email, password, full_name, role) VALUES
('2021-12345', 'student@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'student');

-- Insert test user (password: test123)
INSERT INTO users (student_id, email, password, full_name, role) VALUES
('2021-54321', 'test@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User', 'student');

-- Insert sample posts
INSERT INTO posts (user_id, org_id, category_id, title, content, post_type) VALUES
(1, 1, 1, 'Welcome to UniFeed!', 'This is the official bulletin board system for CvSU Silang Campus. Share announcements, events, and connect with fellow students!', 'announcement'),
(2, 2, 2, 'Upcoming Intramurals 2024', 'Join us for the annual intramurals! Registration starts next week. Various sports categories available.', 'event'),
(1, 1, 3, 'Programming Workshop', 'Free programming workshop for beginners. Learn the basics of web development with HTML, CSS, and JavaScript.', 'event');
