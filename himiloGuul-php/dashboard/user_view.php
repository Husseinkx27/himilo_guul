<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_admin();
require_once __DIR__ . '/../inc/header.php';

$id = intval($_GET['id'] ?? 0);
if (!$id){ echo '<p>Invalid user ID.</p>'; require_once __DIR__ . '/../inc/footer.php'; exit; }
$stmt = $pdo->prepare('SELECT id,first_name,last_name,username,email,phone,role,status,created_at,profile_picture FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id'=>$id]);
$user = $stmt->fetch();
if (!$user){ echo '<p>User not found.</p>'; require_once __DIR__ . '/../inc/footer.php'; exit; }
?>
<div class="layout">
  <?php include __DIR__ . '/../inc/sidebar.php'; ?>
  <section class="main">
    <h2>User: <?php echo sanitize($user['username']); ?> <a class="button small" href="users.php">Back</a> <a class="button small" href="users_edit.php?id=<?php echo intval($user['id']); ?>">Edit</a></h2>

    <div class="card form-card" style="max-width:820px">
      <div class="form-grid">
        <div class="col-main">
          <div class="form-field full">
            <label>Full name</label>
            <div class="text-muted"><?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?></div>
          </div>
          <div class="form-field">
            <label>Username</label>
            <div class="text-muted"><?php echo sanitize($user['username']); ?></div>
          </div>
          <div class="form-field">
            <label>Email</label>
            <div class="text-muted"><?php echo sanitize($user['email']); ?></div>
          </div>
          <div class="form-field">
            <label>Phone</label>
            <div class="text-muted"><?php echo sanitize($user['phone']); ?></div>
          </div>
          <div class="form-field">
            <label>Role</label>
            <div class="text-muted"><?php echo sanitize($user['role']); ?></div>
          </div>
          <div class="form-field">
            <label>Status</label>
            <div class="text-muted"><?php echo sanitize($user['status']); ?></div>
          </div>
          <div class="form-field full">
            <label>Created</label>
            <div class="text-muted"><?php echo sanitize($user['created_at']); ?></div>
          </div>
        </div>
        <div>
          <div class="profile-box">
            <div style="text-align:center;margin-bottom:8px;font-weight:600;color:var(--muted)">Profile photo</div>
            <div style="text-align:center">
              <?php if (!empty($user['profile_picture'])): ?>
                <img src="<?php echo sanitize($user['profile_picture']); ?>" alt="profile" style="width:160px;height:160px;object-fit:cover;border-radius:8px;border:1px solid #e6e9ec">
              <?php else: ?>
                <img src="/projects/himiloGuul-php/assets/img/avatar_placeholder.svg" alt="profile" style="width:160px;height:160px;object-fit:cover;border-radius:8px;border:1px solid #e6e9ec">
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>