<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/portfolio.php';

$auth = new Auth();
$auth->requireAuth();

$portfolio = new Portfolio();
$message = '';
$messageType = '';

if ($_POST) {
    if (isset($_POST['create_achievement'])) {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'achievement_date' => $_POST['achievement_date'],
            'category' => $_POST['category'],
            'icon_class' => $_POST['icon_class'],
            'link_url' => $_POST['link_url'],
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'order_index' => $_POST['order_index']
        ];
        
        if ($portfolio->createAchievement($data)) {
            $message = 'Achievement created successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error creating achievement.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_achievement'])) {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'achievement_date' => $_POST['achievement_date'],
            'category' => $_POST['category'],
            'icon_class' => $_POST['icon_class'],
            'link_url' => $_POST['link_url'],
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'order_index' => $_POST['order_index']
        ];
        
        if ($portfolio->updateAchievement($_POST['achievement_id'], $data)) {
            $message = 'Achievement updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error updating achievement.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete_achievement'])) {
        if ($portfolio->deleteAchievement($_POST['id'])) {
            $message = 'Achievement deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error deleting achievement.';
            $messageType = 'error';
        }
    }
}

$achievements = $portfolio->getAchievements();
$unreadMessages = count($portfolio->getContactMessages(true));

$editAchievement = null;
if (isset($_GET['edit'])) {
    foreach ($achievements as $achievement) {
        if ($achievement['id'] == $_GET['edit']) {
            $editAchievement = $achievement;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Achievements - Admin Panel</title>
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
                <a href="achievements.php" class="nav-item active">
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
                <h1>Manage Achievements</h1>
                <div class="user-info">
                    <a href="?action=create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Achievement
                    </a>
                </div>
            </div>
            
            <?php if ($message): ?>
            <div style="margin: 0 2rem;">
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['action']) && $_GET['action'] == 'create' || $editAchievement): ?>
            
            <div class="form-container">
                <h2><?php echo $editAchievement ? 'Edit Achievement' : 'Add New Achievement'; ?></h2>
                
                <form method="POST">
                    <?php if ($editAchievement): ?>
                    <input type="hidden" name="achievement_id" value="<?php echo $editAchievement['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="title">Achievement Title</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editAchievement['title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($editAchievement['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="achievement_date">Achievement Date</label>
                        <input type="date" id="achievement_date" name="achievement_date" value="<?php echo $editAchievement['achievement_date'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="competitive_programming" <?php echo (($editAchievement['category'] ?? '') == 'competitive_programming') ? 'selected' : ''; ?>>Competitive Programming</option>
                            <option value="academic" <?php echo (($editAchievement['category'] ?? '') == 'academic') ? 'selected' : ''; ?>>Academic</option>
                            <option value="certification" <?php echo (($editAchievement['category'] ?? '') == 'certification') ? 'selected' : ''; ?>>Certification</option>
                            <option value="other" <?php echo (($editAchievement['category'] ?? '') == 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon_class">Icon Class</label>
                        <input type="text" id="icon_class" name="icon_class" value="<?php echo htmlspecialchars($editAchievement['icon_class'] ?? 'fas fa-trophy'); ?>" placeholder="fas fa-trophy">
                    </div>
                    
                    <div class="form-group">
                        <label for="link_url">Link URL (optional)</label>
                        <input type="url" id="link_url" name="link_url" value="<?php echo htmlspecialchars($editAchievement['link_url'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="order_index">Order Index</label>
                        <input type="number" id="order_index" name="order_index" value="<?php echo $editAchievement['order_index'] ?? '1'; ?>" min="1">
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_featured" <?php echo (($editAchievement['is_featured'] ?? 0) == 1) ? 'checked' : ''; ?>>
                            Featured Achievement
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="<?php echo $editAchievement ? 'update_achievement' : 'create_achievement'; ?>" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editAchievement ? 'Update Achievement' : 'Save Achievement'; ?>
                        </button>
                        <a href="achievements.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            
            <div class="table-container">
                <?php if (empty($achievements)): ?>
                <div style="padding: 2rem; text-align: center; color: #7f8c8d;">
                    <i class="fas fa-trophy" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No achievements found. <a href="?action=create">Add your first achievement</a></p>
                </div>
                <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Featured</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achievements as $achievement): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($achievement['title']); ?></strong>
                                <br>
                                <small style="color: #7f8c8d;">
                                    <?php echo htmlspecialchars(substr($achievement['description'], 0, 80)) . '...'; ?>
                                </small>
                            </td>
                            <td>
                                <span style="background: #e9ecef; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.8rem;">
                                    <?php echo ucfirst(str_replace('_', ' ', $achievement['category'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($achievement['achievement_date'])); ?></td>
                            <td>
                                <?php if ($achievement['is_featured']): ?>
                                <span style="color: #f39c12;"><i class="fas fa-star"></i> Featured</span>
                                <?php else: ?>
                                <span style="color: #95a5a6;">Regular</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $achievement['order_index']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $achievement['id']; ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this achievement?')">
                                    <input type="hidden" name="id" value="<?php echo $achievement['id']; ?>">
                                    <button type="submit" name="delete_achievement" class="btn btn-sm btn-danger" style="margin-left: 0.5rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
