<?php
require_once __DIR__ . '/inc/header.php';
require_once __DIR__ . '/connection.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // CSRF check
    if (!verify_csrf($_POST['csrf_token'] ?? '')){
        $errors[] = 'Invalid form submission.';
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);
    if (!$username || !$password) $errors[] = 'Enter username and password';

    if (empty($errors)){
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
        $stmt->execute([':u'=>$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])){
            // Login success
            session_regenerate_id(true);
            $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
            // Remember me
            if ($remember){
                create_remember_token($user['id']);
            }
            header('Location: dashboard/index.php');
            exit;
        } else {
            $errors[] = 'Invalid credentials';
        }
    }
}
?>
<div class="content">
  <div class="auth-page">
    <div class="auth-card card">
      <h2>Login</h2>
      <?php if (!empty($_GET['registered'])): ?>
        <div class="alert success">Registration complete. Please login.</div>
      <?php endif; ?>
      <?php if (!empty($_SESSION['expired'])): unset($_SESSION['expired']); ?>
        <div class="alert error">Your session expired due to inactivity. Please login again.</div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert error">
          <ul>
            <?php foreach ($errors as $e) echo '<li>'.sanitize($e).'</li>'; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="/projects/himiloGuul-php/login.php">
        <?php echo csrf_input(); ?>
        <label>Username
          <input type="text" name="username" value="<?php echo sanitize($_POST['username'] ?? ''); ?>">
        </label>
        <label>Password
          <input type="password" name="password">
        </label>
        <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="remember"> Remember me</label>
        <button type="submit" class="button">Login</button>
      </form>
      <p style="text-align:center;margin-top:12px">Don't have an account? <a href="register.php">Register</a></p>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>