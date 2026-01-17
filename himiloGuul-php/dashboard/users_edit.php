<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_admin();
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../lib/uploads.php';

$id = intval($_GET['id'] ?? 0);
$editing = $id > 0;
$errors = [];
if ($editing){
  $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
  $stmt->execute([':id'=>$id]);
  $user = $stmt->fetch();
  if (!$user){
    echo '<p>User not found.</p>';
    require_once __DIR__ . '/../inc/footer.php';
    exit;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!verify_csrf($_POST['csrf_token'] ?? '')){
    $errors[] = 'Invalid form submission.';
  }
  $first = trim($_POST['first_name'] ?? '');
  $last = trim($_POST['last_name'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $role = in_array($_POST['role'] ?? 'Buyer',['Buyer','Seller','Admin']) ? $_POST['role'] : 'Buyer';
  $status = in_array($_POST['status'] ?? 'active',['active','not_active']) ? $_POST['status'] : 'active';

  if (!$first) $errors[] = 'First name required';
  if (!$last) $errors[] = 'Last name required';
  if (!$username) $errors[] = 'Username required';
  if (!$email || !is_valid_email($email)) $errors[] = 'Valid email required';

  // uniqueness
  $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE (username = :u OR email = :e)'.($editing?' AND id != :id':''));
  $params = [':u'=>$username,':e'=>$email];
  if ($editing) $params[':id'] = $id;
  $stmt->execute($params);
  if ($stmt->fetchColumn() > 0) $errors[] = 'Username or email already exists';

  if (empty($errors)){
    if ($editing){
      $updParams = [':f'=>$first,':l'=>$last,':u'=>$username,':e'=>$email,':r'=>$role,':s'=>$status,':id'=>$id];
      if (!empty($password)){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET first_name=:f,last_name=:l,username=:u,email=:e,role=:r,status=:s,password=:p WHERE id=:id')
            ->execute(array_merge($updParams,[':p'=>$hash]));
      } else {
        $pdo->prepare('UPDATE users SET first_name=:f,last_name=:l,username=:u,email=:e,role=:r,status=:s WHERE id=:id')
            ->execute($updParams);
      }
      // handle profile image
      if (!empty($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE){
        $path = store_profile_image($_FILES['profile_picture'],$id);
        if ($path){
          $pdo->prepare('UPDATE users SET profile_picture = :p WHERE id = :id')->execute([':p'=>$path,':id'=>$id]);
        }
      }
    } else {
      if (!$password || strlen($password) < 6){
        $errors[] = 'Password required (min 6 chars)';
      } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (first_name,last_name,username,email,password,role,status,phone) VALUES (:f,:l,:u,:e,:p,:r,:s,:ph)');
        $ins->execute([':f'=>$first,':l'=>$last,':u'=>$username,':e'=>$email,':p'=>$hash,':r'=>$role,':s'=>$status,':ph'=>trim($_POST['phone'] ?? '')]);
        $newId = $pdo->lastInsertId();
        if (!empty($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE){
          $path = store_profile_image($_FILES['profile_picture'],$newId);
          if ($path){
            $pdo->prepare('UPDATE users SET profile_picture = :p WHERE id = :id')->execute([':p'=>$path,':id'=>$newId]);
          }
        }
      }
    }
    if (empty($errors)){
      header('Location: users.php'); exit;
    }
  }
}
?>
<div class="layout">
  <?php include __DIR__ . '/../inc/sidebar.php'; ?>
  <section class="main">
    <h2><?php echo $editing? 'Edit User':'Add New User'; ?></h2>
    <?php if (!empty($errors)): ?><div class="alert error"><ul><?php foreach ($errors as $e) echo '<li>'.sanitize($e).'</li>'; ?></ul></div><?php endif; ?>

    <div class="card form-card">
      <form method="post" action="" enctype="multipart/form-data">
        <?php echo csrf_input(); ?>
        <div class="form-grid">
          <div class="col-main">
            <div class="form-field">
              <label>First name</label>
              <input type="text" name="first_name" value="<?php echo sanitize($_POST['first_name'] ?? ($user['first_name'] ?? '')); ?>">
            </div>
            <div class="form-field">
              <label>Last name</label>
              <input type="text" name="last_name" value="<?php echo sanitize($_POST['last_name'] ?? ($user['last_name'] ?? '')); ?>">
            </div>
            <div class="form-field">
              <label>Username</label>
              <input type="text" name="username" value="<?php echo sanitize($_POST['username'] ?? ($user['username'] ?? '')); ?>">
            </div>
            <div class="form-field">
              <label>Email</label>
              <input type="email" name="email" value="<?php echo sanitize($_POST['email'] ?? ($user['email'] ?? '')); ?>">
            </div>
            <div class="form-field full">
              <label>Phone</label>
              <input type="text" name="phone" value="<?php echo sanitize($_POST['phone'] ?? ($user['phone'] ?? '')); ?>">
            </div>
            <div class="form-field">
              <label>Password <?php if ($editing) echo '<small>(leave blank to keep current)</small>'; ?></label>
              <input type="password" name="password">
            </div>
            <div class="form-field">
              <label>Role</label>
              <select name="role">
                <option value="Buyer" <?php echo (($_POST['role'] ?? ($user['role'] ?? 'Buyer')) === 'Buyer')? 'selected':''; ?>>Buyer</option>
                <option value="Seller" <?php echo (($_POST['role'] ?? ($user['role'] ?? 'Buyer')) === 'Seller')? 'selected':''; ?>>Seller</option>
                <option value="Admin" <?php echo (($_POST['role'] ?? ($user['role'] ?? 'Buyer')) === 'Admin')? 'selected':''; ?>>Admin</option>
              </select>
            </div>
            <div class="form-field">
              <label>Status</label>
              <select name="status">
                <option value="active" <?php echo (($_POST['status'] ?? ($user['status'] ?? 'active')) === 'active')? 'selected':''; ?>>Active</option>
                <option value="not_active" <?php echo (($_POST['status'] ?? ($user['status'] ?? 'active')) === 'not_active')? 'selected':''; ?>>Not active</option>
              </select>
            </div>
            <div class="form-field full form-actions">
              <button class="button" type="submit">Save</button>
              <a class="button small" href="users.php">Cancel</a>
              <?php if ($editing): ?>
                <a class="button small" href="users_edit.php?id=<?php echo intval($user['id']); ?>">Refresh</a>
              <?php endif; ?>
            </div>
          </div>

          <div>
            <div class="profile-box">
              <div class="profile-preview" id="userProfilePreview">
                <?php if (!empty($user['profile_picture'])): ?>
                  <img src="<?php echo sanitize($user['profile_picture']); ?>" alt="profile">
                <?php else: ?>
                  <img src="/projects/himiloGuul-php/assets/img/avatar_placeholder.svg" alt="profile">
                <?php endif; ?>
              </div>
              <div style="margin-top:8px">
                <label style="display:block;margin-bottom:6px;font-weight:600;color:var(--muted)">Profile picture</label>
                <input id="userProfileInput" type="file" name="profile_picture" accept="image/*">
                <div class="text-muted" style="font-size:.9rem;margin-top:6px">Selected file preview:</div>
                <div id="userSelectedPreview" style="margin-top:8px"></div>
              </div>
              <?php if ($editing): ?>
                <div style="margin-top:10px;color:var(--muted);font-size:.9rem">Current ID: <?php echo intval($user['id']); ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </form>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        // initialize previews
        previewSingleImage('#userProfileInput','#userSelectedPreview');
      });
    </script>
  </section>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>