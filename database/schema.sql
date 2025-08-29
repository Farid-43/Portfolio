
CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255),
    github_url VARCHAR(255),
    live_url VARCHAR(255),
    technologies JSON,
    is_featured BOOLEAN DEFAULT FALSE,
    order_index INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category ENUM('programming', 'frameworks', 'tools', 'other') NOT NULL,
    icon_class VARCHAR(100),
    proficiency_level INT DEFAULT 50,
    order_index INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE about_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE statistics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    label VARCHAR(100) NOT NULL,
    value INT NOT NULL,
    icon_class VARCHAR(100),
    order_index INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE social_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon_class VARCHAR(100) NOT NULL,
    order_index INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    achievement_date DATE,
    category ENUM('competitive_programming', 'academic', 'certification', 'award', 'other') DEFAULT 'other',
    icon_class VARCHAR(100),
    link_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    order_index INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


INSERT INTO skills (name, category, icon_class, proficiency_level, order_index) VALUES
('C/C++', 'programming', 'fab fa-cuttlefish', 90, 1),
('Java', 'programming', 'fab fa-java', 85, 2),
('HTML', 'programming', 'fab fa-html5', 95, 3),
('CSS', 'programming', 'fab fa-css3-alt', 90, 4),
('JavaScript', 'programming', 'fab fa-js', 80, 5),
('PHP', 'programming', 'fab fa-php', 75, 6),
('Shell Script', 'programming', 'fas fa-terminal', 70, 7),
('Arduino', 'programming', 'fas fa-microchip', 60, 8),
('Tailwind CSS', 'frameworks', 'fas fa-wind', 80, 1),
('Git', 'frameworks', 'fab fa-git-alt', 85, 2),
('GitHub', 'frameworks', 'fab fa-github', 90, 3),
('VS Code', 'frameworks', 'fas fa-code', 95, 4),
('Android Studio', 'frameworks', 'fab fa-android', 70, 5),
('IntelliJ IDEA', 'frameworks', 'fas fa-brain', 75, 6);

INSERT INTO projects (title, description, github_url, technologies, is_featured, order_index) VALUES
('Rush Road', 'A simple car game built using Java Swing and Firebase for authentication and data management. Features multiple levels, player authentication, F1 car showcase, and session management.', 'https://github.com/Farid-43/Rush_Road', '["Java", "Swing", "Firebase"]', TRUE, 1),
('Numerical Methods Console App', 'A comprehensive console application implementing various numerical methods including solution of linear/non-linear equations, differential equations, and matrix inversion.', 'https://github.com/Farid-43/Console_Application_Development_Using_Numerical_Methods', '["C++", "Numerical Methods", "Mathematics"]', TRUE, 2),
('Transportation Management System', 'A comprehensive system to manage transportation with different routes, timing, and ticket fares. Reduces manual work and helps in efficient management using OOP principles.', 'https://github.com/Farid-43/Transportation_management_system-', '["Java", "OOP", "Management System"]', TRUE, 3);

INSERT INTO statistics (label, value, icon_class, order_index) VALUES
('Projects Completed', 3, 'fas fa-project-diagram', 1),
('Problems Solved', 900, 'fas fa-code', 2),
('Technologies', 8, 'fas fa-tools', 3);

INSERT INTO social_links (platform, url, icon_class, order_index) VALUES
('GitHub', 'https://github.com/Farid-43', 'fab fa-github', 1),
('LinkedIn', 'https://www.linkedin.com/in/farid-ahmed-2446bb245/', 'fab fa-linkedin', 2),
('Codeforces', 'https://codeforces.com/profile/void_Farid', 'fas fa-code', 3),
('VJudge', 'https://vjudge.net/user/ELECTRO_F', 'fas fa-trophy', 4);

INSERT INTO about_info (section, content) VALUES
('intro', 'I''m a passionate Computer Science and Engineering student at Khulna University of Engineering & Technology (KUET). My journey in programming started with competitive programming, where I developed strong problem-solving skills and algorithmic thinking.'),
('description', 'I love tackling challenging problems on platforms like Codeforces and VJudge. Currently, I''m expanding my skill set by diving into web development, learning modern technologies to build amazing web applications.');

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_title', 'Farid Ahmed Patwary - Portfolio'),
('hero_title', 'Hi, I''m Farid Ahmed Patwary'),
('hero_subtitle', 'CSE Student & Competitive Programmer'),
('hero_description', 'I am currently studying CSE in KUET. I am a competitive programmer who loves to solve problems and I''m learning web development.'),
('profile_image', 'Farid.jpeg'),
('email', 'faridpatwary2020@gmail.com'),
('phone', '01841912110'),
('location', 'Dhaka, Bangladesh'),
('university', 'CSE Student at KUET'),
('role', 'Competitive Programmer');

INSERT INTO achievements (title, description, achievement_date, category, icon_class, link_url, is_featured, order_index) VALUES
('Codeforces Pupil Achievement', 'Achieved Pupil rank in Codeforces with a maximum rating of 1382. Solved 800+ problems demonstrating strong algorithmic thinking and problem-solving skills across various difficulty levels.', '2024-01-01', 'competitive_programming', 'fas fa-trophy', 'https://codeforces.com/profile/void_Farid', TRUE, 1),
('Regional Programming Contest', 'Secured 11th position at Khulna Regional Inter University Programming Contest. Competed against top programming teams from universities across the region, showcasing teamwork and competitive programming expertise.', '2024-06-01', 'competitive_programming', 'fas fa-medal', NULL, TRUE, 2),
('Problem Solving Milestone', 'Successfully solved over 900 programming problems across various online judges including Codeforces, VJudge, and other platforms. Demonstrates consistency and dedication to competitive programming.', '2024-12-01', 'competitive_programming', 'fas fa-code', 'https://vjudge.net/user/ELECTRO_F', FALSE, 3),
('Academic Excellence', 'Maintaining strong academic performance in Computer Science and Engineering at Khulna University of Engineering & Technology (KUET) while balancing competitive programming and project development.', '2024-01-01', 'academic', 'fas fa-graduation-cap', NULL, FALSE, 4);
