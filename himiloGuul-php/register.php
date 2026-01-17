<?php
require_once __DIR__ . '/inc/header.php';
require_once __DIR__ . '/connection.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!verify_csrf($_POST['csrf_token'] ?? '')){
        $errors[] = 'Invalid form submission.';
    }
    // Basic validation
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? 'Buyer',['Buyer','Seller']) ? $_POST['role'] : 'Buyer';

    if (!$first) $errors[] = 'First name required';
    if (!$last) $errors[] = 'Last name required';
    if (!$username) $errors[] = 'Username required';
    if (!$email || !is_valid_email($email)) $errors[] = 'Valid email required';
    if (!$password || strlen($password) < 6) $errors[] = 'Password min 6 chars';

    if (empty($errors)) {
        // check uniqueness
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :u OR email = :e');
        $stmt->execute([':u'=>$username,':e'=>$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Username or email already exists';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (first_name,last_name,username,email,password,role) VALUES (:f,:l,:u,:e,:p,:r)');
            $ins->execute([':f'=>$first,':l'=>$last,':u'=>$username,':e'=>$email,':p'=>$hash,':r'=>$role]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<div class="content">
  <div class="auth-page">
    <div class="auth-card card">
      <h2>Register</h2>
      <?php if (!empty($errors)): ?>
        <div class="alert error">
          <ul>
            <?php foreach ($errors as $e) echo '<li>'.sanitize($e).'</li>'; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="/projects/himiloGuul-php/register.php" enctype="multipart/form-data">
        <?php echo csrf_input(); ?>
        <label>First name
          <input type="text" name="first_name" value="<?php echo sanitize($_POST['first_name'] ?? ''); ?>">
        </label>
        <label>Last name
          <input type="text" name="last_name" value="<?php echo sanitize($_POST['last_name'] ?? ''); ?>">
        </label>
        <label>Username
          <input type="text" name="username" value="<?php echo sanitize($_POST['username'] ?? ''); ?>">
        </label>
        <label>Email
          <input type="email" name="email" value="<?php echo sanitize($_POST['email'] ?? ''); ?>">
        </label>
        <label>Password
          <input type="password" name="password">
        </label>
        <label>Role
          <select name="role">
            <option value="Buyer">Buyer</option>
            <option value="Seller">Seller</option>
          </select>
        </label>
        <button type="submit" class="button">Register</button>
      </form>
      <p style="text-align:center;margin-top:12px">Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>