-- Update courses table to include thumbnail column
-- Run this in phpMyAdmin or MySQL to add the missing column

-- Check if courses table exists, if not create it
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `level` enum('intermediate','advanced','beginner') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'beginner',
  `thumbnail` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add thumbnail column if it doesn't exist
ALTER TABLE `courses` ADD COLUMN IF NOT EXISTS `thumbnail` varchar(255) DEFAULT NULL AFTER `level`;

-- Add folder_path column if you want to track folder paths (optional)
ALTER TABLE `courses` ADD COLUMN IF NOT EXISTS `folder_path` varchar(255) DEFAULT NULL AFTER `thumbnail`;

-- Sample data for testing (optional - you can delete this section)
INSERT IGNORE INTO `courses` (`id`, `title`, `description`, `level`, `thumbnail`, `created_at`) VALUES
(1, '7 Hard Challenge', 'Intense 7-day yoga challenge for advanced practitioners', 'advanced', NULL, NOW()),
(2, 'Morning Yoga Basics', 'Gentle morning yoga routine for beginners', 'beginner', NULL, NOW()),
(3, 'Advanced Power Yoga', 'High-intensity power yoga for experienced yogis', 'advanced', NULL, NOW()),
(4, 'Meditation for Beginners', 'Learn the fundamentals of meditation and mindfulness', 'beginner', NULL, NOW()),
(5, 'Flexibility & Stretching', 'Improve flexibility with targeted stretching exercises', 'intermediate', NULL, NOW());