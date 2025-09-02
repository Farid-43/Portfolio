<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/portfolio.php';

$auth = new Auth();
$auth->requireAuth();

$portfolio = new Portfolio();

$totalProjects = count($portfolio->getProjects());
$totalSkills = count($portfolio->getSkills());
try {
    $totalAchievements = count($portfolio->getAchievements());
} catch (Exception $e) {
    $totalAchievements = 0;
}
$unreadMessages = count($portfolio->getContactMessages(true));
$featuredProjects = count($portfolio->getProjects(true));
$recentMessages = array_slice($portfolio->getContactMessages(), 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-wrapper">
        
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-cog"></i> Admin Panel</h2>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="projects.php" class="nav-item">
                    <i class="fas fa-project-diagram"></i> Projects
                </a>
                <a href="skills.php" class="nav-item">
                    <i class="fas fa-code"></i> Skills
                </a>
                <a href="achievements.php" class="nav-item">
                    <i class="fas fa-trophy"></i> Achievements
                </a>
                <a href="messages.php" class="nav-item">
                    <i class="fas fa-envelope"></i> Messages
                    <?php if ($unreadMessages > 0): ?>
                    <span class="badge"><?php echo $unreadMessages; ?></span>
                    <?php endif; ?>
                </a>
                <a href="settings.php" class="nav-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="../index.php" class="nav-item" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Site
                </a>
                <a href="logout.php" class="nav-item logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
                </div>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon projects">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $totalProjects; ?></h3>
                        <p>Total Projects</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon skills">
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $totalSkills; ?></h3>
                        <p>Skills Listed</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon achievements">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $totalAchievements; ?></h3>
                        <p>Achievements</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon messages">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $unreadMessages; ?></h3>
                        <p>Unread Messages</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-section">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <a href="projects.php?action=create" class="action-btn primary">
                        <i class="fas fa-plus"></i> Add New Project
                    </a>
                    <a href="skills.php?action=create" class="action-btn secondary">
                        <i class="fas fa-plus"></i> Add New Skill
                    </a>
                    <a href="messages.php" class="action-btn info">
                        <i class="fas fa-envelope-open"></i> Check Messages
                    </a>
                    <a href="settings.php" class="action-btn warning">
                        <i class="fas fa-cog"></i> Update Settings
                    </a>
                </div>
            </div>

            <?php if (!empty($recentMessages)): ?>
            <div class="dashboard-section">
                <h2>Recent Messages</h2>
                <div class="messages-preview">
                    <?php foreach ($recentMessages as $message): ?>
                    <div class="message-item <?php echo $message['is_read'] ? '' : 'unread'; ?>">
                        <div class="message-header">
                            <strong><?php echo htmlspecialchars($message['name']); ?></strong>
                            <span class="message-date"><?php echo date('M j, Y', strtotime($message['created_at'])); ?></span>
                        </div>
                        <div class="message-subject"><?php echo htmlspecialchars($message['subject']); ?></div>
                        <div class="message-preview">
                            <?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>...
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <a href="messages.php" class="view-all">View All Messages</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
