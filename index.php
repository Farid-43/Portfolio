<?php
require_once __DIR__ . '/includes/portfolio.php';

$portfolio = new Portfolio();

$cookieOptions = [
    'expires' => time() + (86400 * 30), // 30 days
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
];

$defaultTheme = $_COOKIE['theme_preference'] ?? 'light';

if (!isset($_COOKIE['visitor_id'])) {
    $visitorId = uniqid('visitor_', true);
    setcookie('visitor_id', $visitorId, $cookieOptions);

    $isNewVisitor = true;
} else {
    $isNewVisitor = false;
    $visitorId = $_COOKIE['visitor_id'];
}

$lastVisit = $_COOKIE['last_visit'] ?? time();
setcookie('last_visit', time(), $cookieOptions);

$pageViews = (int)($_COOKIE['page_views'] ?? 0) + 1;
setcookie('page_views', $pageViews, $cookieOptions);

$projects = $portfolio->getProjects(true); // Get featured projects only
$skills = $portfolio->getSkillsByCategory();
$statistics = $portfolio->getStatistics();
$socialLinks = $portfolio->getSocialLinks();
$settings = $portfolio->getAllSettings();
$aboutIntro = $portfolio->getAboutInfo('intro');
$aboutDescription = $portfolio->getAboutInfo('description');

try {
    $achievements = $portfolio->getAchievements(true); // Get featured achievements
} catch (Exception $e) {
    $achievements = []; // Empty array if table doesn't exist yet
}

$message = '';
$messageType = '';
if ($_POST && isset($_POST['contact_submit'])) {
    $contactData = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'subject' => $_POST['subject'] ?? '',
        'message' => $_POST['message'] ?? ''
    ];
    
    if ($portfolio->saveContactMessage($contactData)) {
        $message = 'Thank you for your message! I\'ll get back to you soon.';
        $messageType = 'success';
    } else {
        $message = 'Sorry, there was an error sending your message. Please try again.';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($settings['site_title'] ?? 'Farid Ahmed Patwary - Portfolio'); ?></title>
    <meta
      name="description"
      content="Farid Ahmed Patwary - CSE Student, Competitive Programmer & Web Developer"
    />
    <link rel="stylesheet" href="styles.css" />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body data-theme="<?php echo htmlspecialchars($defaultTheme); ?>">
    
    <nav class="navbar">
      <div class="nav-container">
        <div class="nav-logo">
          <a href="#home">Farid Ahmed</a>
        </div>
        <ul class="nav-menu">
          <li class="nav-item">
            <a href="#home" class="nav-link">Home</a>
          </li>
          <li class="nav-item">
            <a href="#about" class="nav-link">About</a>
          </li>
          <li class="nav-item">
            <a href="#skills" class="nav-link">Skills</a>
          </li>
          <li class="nav-item">
            <a href="#achievements" class="nav-link">Achievements</a>
          </li>
          <li class="nav-item">
            <a href="#projects" class="nav-link">Projects</a>
          </li>
          <li class="nav-item">
            <a href="#contact" class="nav-link">Contact</a>
          </li>
        </ul>
        <div class="nav-controls">
          <button
            class="theme-toggle"
            id="themeToggle"
            title="Toggle theme"
            aria-label="Toggle dark/light theme"
          >
            <i class="theme-icon fas fa-moon"></i>
          </button>
          <div class="nav-toggle" id="navToggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
          </div>
        </div>
      </div>
      <div class="scroll-progress" id="scrollProgress"></div>
    </nav>

    <section id="home" class="hero">
      <div class="hero-container">
        <div class="hero-content">
          <div class="hero-text">
            <h1 class="hero-title">
              <?php 
              $heroTitle = $settings['hero_title'] ?? 'Hi, I\'m Farid Ahmed Patwary';
              echo str_replace('Farid Ahmed Patwary', '<span class="gradient-text">Farid Ahmed Patwary</span>', htmlspecialchars($heroTitle, ENT_NOQUOTES));
              ?>
            </h1>
            <p class="hero-subtitle"><?php echo htmlspecialchars($settings['hero_subtitle'] ?? 'CSE Student & Competitive Programmer'); ?></p>
            <?php if ($isNewVisitor): ?>
            <p class="visitor-greeting">👋 Welcome to my portfolio! Thanks for visiting.</p>
            <?php else: ?>
            <p class="visitor-greeting">🎉 Welcome back! You've visited <?php echo $pageViews; ?> times.</p>
            <?php endif; ?>
            <p class="hero-description">
              <?php echo htmlspecialchars($settings['hero_description'] ?? 'I am currently studying CSE in KUET. I am a competitive programmer who loves to solve problems and I\'m learning web development.'); ?>
            </p>
            <div class="hero-buttons">
              <a href="Farid_CV.pdf" class="btn btn-primary" download="Farid_Ahmed_Patwary_CV.pdf" target="_blank">
                <i class="fas fa-download"></i> Download CV
              </a>
              <a href="#contact" class="btn btn-secondary">
                <i class="fas fa-envelope"></i> Contact Info
              </a>
            </div>
            <div class="social-links">
              <?php foreach ($socialLinks as $social): ?>
              <a
                href="<?php echo htmlspecialchars($social['url']); ?>"
                target="_blank"
                title="<?php echo htmlspecialchars($social['platform']); ?>"
                rel="noopener noreferrer"
              >
                <i class="<?php echo htmlspecialchars($social['icon_class']); ?>"></i>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="hero-image">
            <div class="image-container">
              <img
                src="<?php echo htmlspecialchars($settings['profile_image'] ?? 'Farid.jpeg'); ?>"
                alt="Farid Ahmed Patwary"
                class="profile-img"
                loading="lazy"
              />
              <div class="image-overlay"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="scroll-indicator">
        <div class="scroll-arrow"></div>
      </div>
    </section>

    <section id="about" class="about">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">About Me</h2>
          <div class="section-divider"></div>
        </div>
        <div class="about-content">
          <div class="about-text">
            <h3>Hello! I'm Farid Ahmed Patwary</h3>
            <p><?php echo nl2br(htmlspecialchars($aboutIntro)); ?></p>
            <p><?php echo nl2br(htmlspecialchars($aboutDescription)); ?></p>
            <div class="about-info">
              <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo htmlspecialchars($settings['location'] ?? 'Dhaka, Bangladesh'); ?></span>
              </div>
              <div class="info-item">
                <i class="fas fa-graduation-cap"></i>
                <span><?php echo htmlspecialchars($settings['university'] ?? 'CSE Student at KUET'); ?></span>
              </div>
              <div class="info-item">
                <i class="fas fa-code"></i>
                <span><?php echo htmlspecialchars($settings['role'] ?? 'Competitive Programmer'); ?></span>
              </div>
            </div>
          </div>
          <div class="about-stats">
            <?php foreach ($statistics as $stat): ?>
            <div class="stat-item">
              <div class="stat-number" data-target="<?php echo $stat['value']; ?>">0</div>
              <div class="stat-label"><?php echo htmlspecialchars($stat['label']); ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <section id="skills" class="skills">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Skills & Technologies</h2>
          <div class="section-divider"></div>
          <p class="section-subtitle">Here are the technologies I work with</p>
        </div>
        <div class="skills-content">
          <?php 
          $categoryNames = [
            'programming' => 'Programming Languages',
            'frameworks' => 'Frameworks & Tools',
            'tools' => 'Tools & Software',
            'other' => 'Other Skills'
          ];
          
          $categoryIcons = [
            'programming' => 'fas fa-code',
            'frameworks' => 'fas fa-tools',
            'tools' => 'fas fa-laptop-code',
            'other' => 'fas fa-star'
          ];
          ?>
          <?php foreach ($skills as $category => $skillList): ?>
          <div class="skills-category">
            <h3 class="category-title">
              <i class="<?php echo $categoryIcons[$category] ?? 'fas fa-code'; ?>"></i>
              <?php echo $categoryNames[$category] ?? ucfirst($category); ?>
              <span class="skill-count">(<?php echo count($skillList); ?>)</span>
            </h3>
            <div class="skills-grid">
              <?php foreach ($skillList as $skill): ?>
              <div class="skill-item" data-skill="<?php echo htmlspecialchars($skill['name']); ?>">
                <div class="skill-header">
                  <div class="skill-icon">
                    <i class="<?php echo htmlspecialchars($skill['icon_class']); ?>"></i>
                  </div>
                  <div class="skill-info">
                    <span class="skill-name"><?php echo htmlspecialchars($skill['name']); ?></span>
                    <span class="skill-level"><?php echo $skill['proficiency_level']; ?>%</span>
                  </div>
                </div>
                <div class="skill-progress">
                  <div class="progress-bar">
                    <div class="progress-fill" data-progress="<?php echo $skill['proficiency_level']; ?>"></div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>

          <div class="skills-summary">
            <div class="summary-item">
              <div class="summary-icon">
                <i class="fas fa-chart-line"></i>
              </div>
              <div class="summary-text">
                <h4>Always Learning</h4>
                <p>Continuously improving and learning new technologies</p>
              </div>
            </div>
            <div class="summary-item">
              <div class="summary-icon">
                <i class="fas fa-lightbulb"></i>
              </div>
              <div class="summary-text">
                <h4>Problem Solver</h4>
                <p>Love tackling complex challenges with creative solutions</p>
              </div>
            </div>
            <div class="summary-item">
              <div class="summary-icon">
                <i class="fas fa-users"></i>
              </div>
              <div class="summary-text">
                <h4>Team Player</h4>
                <p>Great at collaborating and sharing knowledge with others</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="achievements" class="achievements">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">My Achievements</h2>
          <div class="section-divider"></div>
          <p class="section-subtitle">
            Milestones and recognition in my programming journey
          </p>
        </div>
        <?php if (!empty($achievements)): ?>
        <div class="achievements-grid">
          <?php foreach ($achievements as $achievement): ?>
          <div class="achievement-card">
            <div class="achievement-header">
              <div class="achievement-icon">
                <i class="<?php echo htmlspecialchars($achievement['icon_class']); ?>"></i>
              </div>
              <div class="achievement-meta">
                <?php if (isset($achievement['achievement_date']) && $achievement['achievement_date']): ?>
                <div class="achievement-date">
                  <i class="fas fa-calendar-alt"></i>
                  <?php echo date('M Y', strtotime($achievement['achievement_date'])); ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="achievement-content">
              <h3 class="achievement-title"><?php echo htmlspecialchars($achievement['title']); ?></h3>
              <p class="achievement-description">
                <?php echo htmlspecialchars($achievement['description']); ?>
              </p>
              <div class="achievement-details">
                <?php if (isset($achievement['link_url']) && $achievement['link_url']): ?>
                <div class="achievement-detail">
                  <i class="fas fa-external-link-alt"></i>
                  <a href="<?php echo htmlspecialchars($achievement['link_url']); ?>" target="_blank" rel="noopener">
                    View Achievement
                  </a>
                </div>
                <?php endif; ?>
                <?php if (isset($achievement['category']) && $achievement['category']): ?>
                <div class="achievement-detail">
                  <i class="fas fa-tag"></i>
                  <span><?php echo ucfirst(str_replace('_', ' ', $achievement['category'])); ?></span>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
          <i class="fas fa-trophy" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
          <p>Achievements will appear here once the database is set up.</p>
          <p><small>Please run <code>setup-achievements.php</code> to create the achievements table.</small></p>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <section id="projects" class="projects">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Featured Projects</h2>
          <div class="section-divider"></div>
        </div>
        <div class="projects-grid">
          <?php foreach ($projects as $project): ?>
          <div class="project-card">
            <div class="project-image <?php echo !$project['image_url'] ? 'no-image' : ''; ?>">
              <?php if ($project['image_url']): ?>
                <img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
              <?php endif; ?>
              <div class="project-overlay">
                <div class="project-links">
                  <?php if ($project['github_url']): ?>
                  <a
                    href="<?php echo htmlspecialchars($project['github_url']); ?>"
                    target="_blank"
                    class="project-link"
                  >
                    <i class="fab fa-github"></i>
                  </a>
                  <?php endif; ?>
                  <?php if ($project['live_url']): ?>
                  <a
                    href="<?php echo htmlspecialchars($project['live_url']); ?>"
                    target="_blank"
                    class="project-link"
                  >
                    <i class="fas fa-external-link-alt"></i>
                  </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="project-content">
              <h3 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h3>
              <p class="project-description">
                <?php echo htmlspecialchars($project['description']); ?>
              </p>
              <div class="project-tech">
                <?php 
                $technologies = json_decode($project['technologies'], true) ?? [];
                foreach ($technologies as $tech): 
                ?>
                <span class="tech-tag"><?php echo htmlspecialchars($tech); ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section id="contact" class="contact">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Get In Touch</h2>
          <div class="section-divider"></div>
          <p class="section-subtitle">
            Feel free to reach out if you want to collaborate on a project or
            just want to connect!
          </p>
        </div>
        <div class="contact-content">
          <div class="contact-info">
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="contact-details">
                <h4>Email</h4>
                <p><?php echo htmlspecialchars($settings['email'] ?? 'faridpatwary2020@gmail.com'); ?></p>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fas fa-phone"></i>
              </div>
              <div class="contact-details">
                <h4>Phone</h4>
                <p><?php echo htmlspecialchars($settings['phone'] ?? '01841912110'); ?></p>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div class="contact-details">
                <h4>Location</h4>
                <p><?php echo htmlspecialchars($settings['location'] ?? 'Dhaka, Bangladesh'); ?></p>
              </div>
            </div>
          </div>
          <div class="contact-form">
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
              <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            <form method="POST">
              <div class="form-group">
                <input
                  type="text"
                  id="name"
                  name="name"
                  placeholder="Your Name"
                  required
                />
              </div>
              <div class="form-group">
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="Your Email"
                  required
                />
              </div>
              <div class="form-group">
                <input
                  type="text"
                  id="subject"
                  name="subject"
                  placeholder="Subject"
                  required
                />
              </div>
              <div class="form-group">
                <textarea
                  id="message"
                  name="message"
                  rows="5"
                  placeholder="Your Message"
                  required
                ></textarea>
              </div>
              <button type="submit" name="contact_submit" class="btn btn-primary btn-full">
                Send Message
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <p>&copy; 2025 Farid Ahmed Patwary. All rights reserved.</p>
          <div class="footer-social">
            <?php foreach ($socialLinks as $social): ?>
            <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank">
              <i class="<?php echo htmlspecialchars($social['icon_class']); ?>"></i>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </footer>

    <script src="script.js"></script>
    <script>

      document.addEventListener('DOMContentLoaded', function() {

        const scrollProgress = document.getElementById('scrollProgress');

        if (!scrollProgress) {
          console.error('Scroll progress element not found!');
          return;
        }
        
        console.log('Scroll progress bar initialized');
        
        function updateScrollProgress() {
          const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
          const documentHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
          const scrollPercentage = (scrollTop / documentHeight) * 100;

          const finalPercentage = Math.min(Math.max(scrollPercentage, 0), 100);
          
          if (scrollProgress) {
            scrollProgress.style.width = finalPercentage + '%';

            if (finalPercentage > 0) {
              scrollProgress.style.opacity = '1';
            } else {
              scrollProgress.style.opacity = '0.3';
              scrollProgress.style.width = '2%'; // Small visible indicator at top
            }
          }
        }

        window.addEventListener('scroll', updateScrollProgress);

        window.addEventListener('resize', updateScrollProgress);

        setTimeout(updateScrollProgress, 100);

        const skillItems = document.querySelectorAll('.skill-item');
        
        const skillObserver = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const progressFill = entry.target.querySelector('.progress-fill');
              const progress = progressFill.dataset.progress;
              
              setTimeout(() => {
                progressFill.style.width = progress + '%';
              }, 200);
              
              skillObserver.unobserve(entry.target);
            }
          });
        }, { threshold: 0.3 });
        
        skillItems.forEach(item => {
          skillObserver.observe(item);
        });

        skillItems.forEach(item => {
          item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
          });
          
          item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
          });
        });
      });
    </script>
  </body>
</html>
