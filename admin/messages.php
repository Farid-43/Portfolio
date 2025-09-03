<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/portfolio.php';

$auth = new Auth();
$auth->requireAuth();

$portfolio = new Portfolio();
$message = '';
$messageType = '';

if (isset($_GET['mark_read']) && $_GET['mark_read']) {
    if ($portfolio->markMessageAsRead($_GET['mark_read'])) {
        $message = 'Message marked as read!';
        $messageType = 'success';
    }
}

$messages = $portfolio->getContactMessages();
$unreadMessages = count($portfolio->getContactMessages(true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin Panel</title>
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
                <a href="messages.php" class="nav-item active">
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
                <h1>Contact Messages</h1>
                <div class="user-info">
                    <?php if ($unreadMessages > 0): ?>
                    <span style="color: #e74c3c; font-weight: 600;">
                        <?php echo $unreadMessages; ?> unread messages
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($message): ?>
            <div style="margin: 0 2rem;">
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="dashboard-section">
                <?php if (empty($messages)): ?>
                <div class="table-container">
                    <div style="padding: 2rem; text-align: center; color: #7f8c8d;">
                        <i class="fas fa-envelope" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                        <p>No messages received yet.</p>
                    </div>
                </div>
                <?php else: ?>
                <div class="messages-preview">
                    <?php foreach ($messages as $msg): ?>
                    <div class="message-item <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                        <div class="message-header">
                            <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                            <span style="color: #7f8c8d; margin-left: 1rem;">
                                <?php echo htmlspecialchars($msg['email']); ?>
                            </span>
                            <div style="margin-left: auto; display: flex; align-items: center; gap: 1rem;">
                                <span class="message-date">
                                    <?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?>
                                </span>
                                <?php if (!$msg['is_read']): ?>
                                <a href="?mark_read=<?php echo $msg['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check"></i> Mark Read
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="message-subject">
                            <strong>Subject:</strong> <?php echo htmlspecialchars($msg['subject']); ?>
                        </div>
                        <div class="message-preview" style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 5px; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                        </div>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e9ecef;">
                            <strong>Reply to:</strong>
                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" 
                               class="btn btn-success btn-sm" style="margin-left: 1rem;">
                                <i class="fas fa-reply"></i> Send Email Reply
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
