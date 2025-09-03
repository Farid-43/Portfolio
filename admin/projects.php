<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/portfolio.php';

$auth = new Auth();
$auth->requireAuth();

$portfolio = new Portfolio();
$message = '';
$messageType = '';

if ($_POST) {
    if (isset($_POST['create_project'])) {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'image_url' => $_POST['image_url'],
            'github_url' => $_POST['github_url'],
            'live_url' => $_POST['live_url'],
            'technologies' => explode(',', $_POST['technologies']),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'order_index' => $_POST['order_index']
        ];
        
        if ($portfolio->createProject($data)) {
            $message = 'Project created successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error creating project.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_project'])) {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'image_url' => $_POST['image_url'],
            'github_url' => $_POST['github_url'],
            'live_url' => $_POST['live_url'],
            'technologies' => explode(',', $_POST['technologies']),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'order_index' => $_POST['order_index']
        ];
        
        if ($portfolio->updateProject($_POST['project_id'], $data)) {
            $message = 'Project updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error updating project.';
            $messageType = 'error';
        }
    }
}

if (isset($_GET['delete']) && $_GET['delete']) {
    if ($portfolio->deleteProject($_GET['delete'])) {
        $message = 'Project deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error deleting project.';
        $messageType = 'error';
    }
}

$editProject = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $editProject = $portfolio->getProject($_GET['edit']);
}

$projects = $portfolio->getProjects();
$unreadMessages = count($portfolio->getContactMessages(true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Admin Panel</title>
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
                <a href="projects.php" class="nav-item active">
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
                <h1>Manage Projects</h1>
                <div class="user-info">
                    <a href="?action=create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Project
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
            
            <?php if (isset($_GET['action']) && $_GET['action'] == 'create' || $editProject): ?>
            
            <div class="form-container">
                <h2><?php echo $editProject ? 'Edit Project' : 'Add New Project'; ?></h2>
                
                <form method="POST">
                    <?php if ($editProject): ?>
                    <input type="hidden" name="project_id" value="<?php echo $editProject['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="title">Project Title</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editProject['title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($editProject['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_url">Image URL</label>
                        <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($editProject['image_url'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="github_url">GitHub URL</label>
                        <input type="url" id="github_url" name="github_url" value="<?php echo htmlspecialchars($editProject['github_url'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="live_url">Live Demo URL</label>
                        <input type="url" id="live_url" name="live_url" value="<?php echo htmlspecialchars($editProject['live_url'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="technologies">Technologies (comma separated)</label>
                        <input type="text" id="technologies" name="technologies" 
                               value="<?php echo $editProject ? implode(', ', json_decode($editProject['technologies'], true) ?? []) : ''; ?>"
                               placeholder="e.g., PHP, MySQL, JavaScript">
                    </div>
                    
                    <div class="form-group">
                        <label for="order_index">Display Order</label>
                        <input type="number" id="order_index" name="order_index" value="<?php echo $editProject['order_index'] ?? 0; ?>" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_featured" <?php echo ($editProject['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                            Featured Project
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="<?php echo $editProject ? 'update_project' : 'create_project'; ?>" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editProject ? 'Update' : 'Create'; ?> Project
                        </button>
                        <a href="projects.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            
            <div class="table-container">
                <?php if (empty($projects)): ?>
                <div style="padding: 2rem; text-align: center; color: #7f8c8d;">
                    <i class="fas fa-project-diagram" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No projects found. <a href="?action=create">Add your first project</a></p>
                </div>
                <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Technologies</th>
                            <th>Featured</th>
                            <th>Order</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                <br>
                                <small style="color: #7f8c8d;">
                                    <?php echo htmlspecialchars(substr($project['description'], 0, 80)) . '...'; ?>
                                </small>
                            </td>
                            <td>
                                <?php 
                                $techs = json_decode($project['technologies'], true) ?? [];
                                foreach ($techs as $tech): 
                                ?>
                                <span style="background: #e9ecef; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.8rem; margin: 0.1rem;">
                                    <?php echo htmlspecialchars($tech); ?>
                                </span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php if ($project['is_featured']): ?>
                                <span style="color: #f39c12;"><i class="fas fa-star"></i> Featured</span>
                                <?php else: ?>
                                <span style="color: #95a5a6;">Regular</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $project['order_index']; ?></td>
                            <td><?php echo date('M j, Y', strtotime($project['created_at'])); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="?edit=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $project['id']; ?>" class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this project?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
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
