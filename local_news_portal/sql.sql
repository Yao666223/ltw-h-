-- Tạo cơ sở dữ liệu
CREATE DATABASE local_news_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE local_news_portal;

-- Tạo bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE posts
  ADD COLUMN status ENUM('draft','pending','published')
    NOT NULL DEFAULT 'pending';

-- Tạo bảng danh mục
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Tạo bảng bài viết
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    category_id INT,
    user_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_name VARCHAR(100) NOT NULL,
  user_email VARCHAR(150) DEFAULT NULL,
  content TEXT NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id)
);
ALTER TABLE comments DROP COLUMN status;

INSERT INTO categories (name) VALUES ('Sự kiện'), ('Hành chính công'), ('An ninh – xã hội'), ('Đời sống'), ('Thời tiết');

INSERT INTO posts (title, content, image, category_id, user_id, created_at)
VALUES
('Trực thăng, tiêm kích bay khảo sát Quảng trường Ba Đình', 'Chiều 30/7, trực thăng… chuẩn bị đại lễ...', NULL, 1, 1, '2025-07-30 15:00:00'),
('15 tiện ích miễn phí tại điểm phục vụ hành chính công Hà Nội từ 1/8', 'Người dân… được sử dụng 15 tiện ích miễn phí từ 1/8...', NULL, 2, 1, '2025-07-30 13:00:00'),
('Nữ sinh đại học Hà Nội mất tích bí ẩn từ 28/7', 'Nữ sinh viên Trường Luật bị mất liên lạc…', NULL, 3, 1, '2025-07-31 10:00:00'),
('Tiêu thụ điện ở Hà Nội đạt đỉnh lịch sử', 'Công suất tiêu thụ điện tại Hà Nội đạt hơn 5.608 MW', NULL, 4, 1, '2025-07-30 14:00:00'),
('Hà Nội nắng dịu dần, chiều tối có mưa dông', 'Dự báo mưa rào và dông chiều tối, gió Tây Nam cấp 2–3', NULL, 5, 1, '2025-07-31 08:00:00');


ALTER TABLE posts ADD author_id INT;

SELECT posts.*, users.name AS author FROM posts JOIN users ON posts.author_id = users.id;

select * from posts;
