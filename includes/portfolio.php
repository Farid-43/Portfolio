<?php
require_once __DIR__ . '/../config/database.php';

class Portfolio {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getProjects($featured_only = false) {
        $where = $featured_only ? "WHERE is_featured = 1 AND status = 'active'" : "WHERE status = 'active'";
        $query = "SELECT * FROM projects $where ORDER BY order_index ASC, created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProject($id) {
        $query = "SELECT * FROM projects WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createProject($data) {
        $query = "INSERT INTO projects (title, description, image_url, github_url, live_url, technologies, is_featured, order_index) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['image_url'] ?? null,
            $data['github_url'] ?? null,
            $data['live_url'] ?? null,
            json_encode($data['technologies'] ?? []),
            $data['is_featured'] ?? 0,
            $data['order_index'] ?? 0
        ]);
    }
    
    public function updateProject($id, $data) {
        $query = "UPDATE projects SET title = ?, description = ?, image_url = ?, github_url = ?, live_url = ?, technologies = ?, is_featured = ?, order_index = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['image_url'] ?? null,
            $data['github_url'] ?? null,
            $data['live_url'] ?? null,
            json_encode($data['technologies'] ?? []),
            $data['is_featured'] ?? 0,
            $data['order_index'] ?? 0,
            $id
        ]);
    }
    
    public function deleteProject($id) {
        $query = "DELETE FROM projects WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getSkills($category = null) {
        $where = $category ? "WHERE category = ? AND status = 'active'" : "WHERE status = 'active'";
        $query = "SELECT * FROM skills $where ORDER BY order_index ASC, name ASC";
        $stmt = $this->conn->prepare($query);
        if ($category) {
            $stmt->execute([$category]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSkillsByCategory() {
        $skills = $this->getSkills();
        $categorized = [];
        foreach ($skills as $skill) {
            $categorized[$skill['category']][] = $skill;
        }
        return $categorized;
    }
    
    public function createSkill($data) {
        $query = "INSERT INTO skills (name, category, icon_class, proficiency_level, order_index) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['category'],
            $data['icon_class'] ?? '',
            $data['proficiency_level'] ?? 50,
            $data['order_index'] ?? 0
        ]);
    }
    
    public function updateSkill($id, $data) {
        $query = "UPDATE skills SET name = ?, category = ?, icon_class = ?, proficiency_level = ?, order_index = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['category'],
            $data['icon_class'] ?? '',
            $data['proficiency_level'] ?? 50,
            $data['order_index'] ?? 0,
            $id
        ]);
    }
    
    public function deleteSkill($id) {
        $query = "DELETE FROM skills WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getStatistics() {
        $query = "SELECT * FROM statistics WHERE status = 'active' ORDER BY order_index ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatistic($id, $data) {
        $query = "UPDATE statistics SET label = ?, value = ?, icon_class = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['label'],
            $data['value'],
            $data['icon_class'],
            $id
        ]);
    }

    public function getSocialLinks() {
        $query = "SELECT * FROM social_links WHERE status = 'active' ORDER BY order_index ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveContactMessage($data) {
        $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message']
        ]);
    }
    
    public function getContactMessages($unread_only = false) {
        $where = $unread_only ? "WHERE is_read = 0" : "";
        $query = "SELECT * FROM contact_messages $where ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function markMessageAsRead($id) {
        $query = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getSetting($key) {
        $query = "SELECT setting_value FROM site_settings WHERE setting_key = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : null;
    }
    
    public function updateSetting($key, $value) {
        $query = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$key, $value, $value]);
    }
    
    public function getAllSettings() {
        $query = "SELECT * FROM site_settings";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function getAboutInfo($section = null) {
        if ($section) {
            $query = "SELECT content FROM about_info WHERE section = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$section]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['content'] : '';
        } else {
            $query = "SELECT * FROM about_info";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    public function updateAboutInfo($section, $content) {
        $query = "INSERT INTO about_info (section, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$section, $content, $content]);
    }

    public function getAchievements($featured_only = false) {
        $where = $featured_only ? "WHERE is_featured = 1 AND status = 'active'" : "WHERE status = 'active'";
        $query = "SELECT * FROM achievements $where ORDER BY order_index ASC, achievement_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAchievement($id) {
        $query = "SELECT * FROM achievements WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createAchievement($data) {
        $query = "INSERT INTO achievements (title, description, achievement_date, category, icon_class, link_url, is_featured, order_index) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['achievement_date'] ?? null,
            $data['category'] ?? 'other',
            $data['icon_class'] ?? 'fas fa-trophy',
            $data['link_url'] ?? null,
            $data['is_featured'] ?? 0,
            $data['order_index'] ?? 0
        ]);
    }
    
    public function updateAchievement($id, $data) {
        $query = "UPDATE achievements SET title = ?, description = ?, achievement_date = ?, category = ?, icon_class = ?, link_url = ?, is_featured = ?, order_index = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['achievement_date'] ?? null,
            $data['category'] ?? 'other',
            $data['icon_class'] ?? 'fas fa-trophy',
            $data['link_url'] ?? null,
            $data['is_featured'] ?? 0,
            $data['order_index'] ?? 0,
            $id
        ]);
    }
    
    public function deleteAchievement($id) {
        $query = "DELETE FROM achievements WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
    
    public function getAchievementsByCategory() {
        $achievements = $this->getAchievements();
        $categorized = [];
        foreach ($achievements as $achievement) {
            $categorized[$achievement['category']][] = $achievement;
        }
        return $categorized;
    }
}
?>
