<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/portfolio.php';

$auth = new Auth();
$auth->requireAuth();

$portfolio = new Portfolio();
$message = '';
$messageType = '';

if ($_POST) {
    if (isset($_POST['create_skill'])) {
        $data = [
            'name' => $_POST['name'],
            'category' => $_POST['category'],
            'icon_class' => $_POST['icon_class'],
            'proficiency_level' => $_POST['proficiency_level'],
            'order_index' => $_POST['order_index']
        ];
        
        if ($portfolio->createSkill($data)) {
            $message = 'Skill added successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error adding skill.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_skill'])) {
        $data = [
            'name' => $_POST['name'],
            'category' => $_POST['category'],
            'icon_class' => $_POST['icon_class'],
            'proficiency_level' => $_POST['proficiency_level'],
            'order_index' => $_POST['order_index']
        ];
        
        if ($portfolio->updateSkill($_POST['skill_id'], $data)) {
            $message = 'Skill updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error updating skill.';
            $messageType = 'error';
        }
    }
}

if (isset($_GET['delete']) && $_GET['delete']) {
    if ($portfolio->deleteSkill($_GET['delete'])) {
        $message = 'Skill deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error deleting skill.';
        $messageType = 'error';
    }
}

$editSkill = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $skills = $portfolio->getSkills();
    foreach ($skills as $skill) {
        if ($skill['id'] == $_GET['edit']) {
            $editSkill = $skill;
            break;
        }
    }
}

$skillsByCategory = $portfolio->getSkillsByCategory();
$unreadMessages = count($portfolio->getContactMessages(true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - Admin Panel</title>
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
                <a href="skills.php" class="nav-item active">
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
                <h1>Manage Skills</h1>
                <div class="user-info">
                    <a href="?action=create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Skill
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
            
            <?php if (isset($_GET['action']) && $_GET['action'] == 'create' || $editSkill): ?>
            
            <div class="form-container">
                <h2><?php echo $editSkill ? 'Edit Skill' : 'Add New Skill'; ?></h2>
                
                <form method="POST">
                    <?php if ($editSkill): ?>
                    <input type="hidden" name="skill_id" value="<?php echo $editSkill['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Skill Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($editSkill['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="programming" <?php echo ($editSkill['category'] ?? '') == 'programming' ? 'selected' : ''; ?>>Programming Languages</option>
                            <option value="frameworks" <?php echo ($editSkill['category'] ?? '') == 'frameworks' ? 'selected' : ''; ?>>Frameworks & Tools</option>
                            <option value="tools" <?php echo ($editSkill['category'] ?? '') == 'tools' ? 'selected' : ''; ?>>Tools & Software</option>
                            <option value="other" <?php echo ($editSkill['category'] ?? '') == 'other' ? 'selected' : ''; ?>>Other Skills</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon_class">Icon Class (FontAwesome)</label>
                        <input type="text" id="icon_class" name="icon_class" value="<?php echo htmlspecialchars($editSkill['icon_class'] ?? ''); ?>" 
                               placeholder="e.g., fab fa-php">
                    </div>
                    
                    <div class="form-group">
                        <label for="proficiency_level">Proficiency Level (0-100)</label>
                        <input type="number" id="proficiency_level" name="proficiency_level" 
                               value="<?php echo $editSkill['proficiency_level'] ?? 50; ?>" min="0" max="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="order_index">Display Order</label>
                        <input type="number" id="order_index" name="order_index" value="<?php echo $editSkill['order_index'] ?? 0; ?>" min="0">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="<?php echo $editSkill ? 'update_skill' : 'create_skill'; ?>" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editSkill ? 'Update' : 'Add'; ?> Skill
                        </button>
                        <a href="skills.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            
            <div class="dashboard-section">
                <?php if (empty($skillsByCategory)): ?>
                <div class="table-container">
                    <div style="padding: 2rem; text-align: center; color: #7f8c8d;">
                        <i class="fas fa-code" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                        <p>No skills found. <a href="?action=create">Add your first skill</a></p>
                    </div>
                </div>
                <?php else: ?>
                <?php 
                $categoryNames = [
                    'programming' => 'Programming Languages',
                    'frameworks' => 'Frameworks & Tools',
                    'tools' => 'Tools & Software',
                    'other' => 'Other Skills'
                ];
                ?>
                <?php foreach ($skillsByCategory as $category => $skills): ?>
                <h3><?php echo $categoryNames[$category] ?? ucfirst($category); ?></h3>
                <div class="table-container" style="margin-bottom: 2rem;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Skill</th>
                                <th>Icon</th>
                                <th>Proficiency</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($skills as $skill): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($skill['name']); ?></td>
                                <td>
                                    <i class="<?php echo htmlspecialchars($skill['icon_class']); ?>"></i>
                                    <small style="color: #7f8c8d; margin-left: 0.5rem;">
                                        <?php echo htmlspecialchars($skill['icon_class']); ?>
                                    </small>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <div style="width: 100px; height: 8px; background: #e9ecef; border-radius: 4px; margin-right: 1rem;">
                                            <div style="width: <?php echo $skill['proficiency_level']; ?>%; height: 100%; background: #3498db; border-radius: 4px;"></div>
                                        </div>
                                        <?php echo $skill['proficiency_level']; ?>%
                                    </div>
                                </td>
                                <td><?php echo $skill['order_index']; ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="?edit=<?php echo $skill['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $skill['id']; ?>" class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this skill?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
