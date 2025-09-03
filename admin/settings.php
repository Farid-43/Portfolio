<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/portfolio.php';

$auth = new Auth();
$auth->requireAuth();

$portfolio = new Portfolio();
$message = '';
$messageType = '';

if ($_POST && isset($_POST['update_settings'])) {
    $settings = [
        'site_title' => $_POST['site_title'],
        'hero_title' => $_POST['hero_title'],
        'hero_subtitle' => $_POST['hero_subtitle'],
        'hero_description' => $_POST['hero_description'],
        'profile_image' => $_POST['profile_image'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'location' => $_POST['location'],
        'university' => $_POST['university'],
        'role' => $_POST['role']
    ];
    
    $updated = true;
    foreach ($settings as $key => $value) {
        if (!$portfolio->updateSetting($key, $value)) {
            $updated = false;
            break;
        }
    }
    
    if ($updated) {
        $message = 'Settings updated successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error updating settings.';
        $messageType = 'error';
    }
}

$currentSettings = $portfolio->getAllSettings();
$unreadMessages = count($portfolio->getContactMessages(true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
                <a href="index.php" class="nav-item">
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
                <a href="settings.php" class="nav-item active">
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
                <h1>Portfolio Settings</h1>
            </div>
            
            <?php if ($message): ?>
            <div style="margin: 0 2rem;">
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-container">
                <h2>General Settings</h2>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="site_title">Site Title</label>
                        <input type="text" id="site_title" name="site_title" 
                               value="<?php echo htmlspecialchars($currentSettings['site_title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="hero_title">Hero Title</label>
                        <input type="text" id="hero_title" name="hero_title" 
                               value="<?php echo htmlspecialchars($currentSettings['hero_title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="hero_subtitle">Hero Subtitle</label>
                        <input type="text" id="hero_subtitle" name="hero_subtitle" 
                               value="<?php echo htmlspecialchars($currentSettings['hero_subtitle'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="hero_description">Hero Description</label>
                        <textarea id="hero_description" name="hero_description" rows="3" required><?php echo htmlspecialchars($currentSettings['hero_description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="profile_image">Profile Image Filename</label>
                        <input type="text" id="profile_image" name="profile_image" 
                               value="<?php echo htmlspecialchars($currentSettings['profile_image'] ?? ''); ?>"
                               placeholder="e.g., profile.jpg">
                    </div>
                    
                    <h3 style="margin: 2rem 0 1rem; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 0.5rem;">Contact Information</h3>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($currentSettings['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($currentSettings['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" 
                               value="<?php echo htmlspecialchars($currentSettings['location'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="university">University/Education</label>
                        <input type="text" id="university" name="university" 
                               value="<?php echo htmlspecialchars($currentSettings['university'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Professional Role</label>
                        <input type="text" id="role" name="role" 
                               value="<?php echo htmlspecialchars($currentSettings['role'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="update_settings" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
