<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_admin();
require_once __DIR__ . '/../inc/header.php';

$users = $pdo->query('SELECT id,username,email,role,status,created_at,profile_picture FROM users ORDER BY created_at DESC')->fetchAll();
?>
<div class="layout">
  <?php include __DIR__ . '/../inc/sidebar.php'; ?>
  <section class="main">
    <h2>Users <a class="button small" href="users_edit.php">Add New User</a></h2>
    <div class="card">
      <table class="table">
        <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?php echo intval($u['id']); ?></td>
              <td><?php echo sanitize($u['username']); ?><?php if (!empty($u['profile_picture'])): ?> <img src="<?php echo sanitize($u['profile_picture']); ?>" alt="pp" style="height:24px;vertical-align:middle;border-radius:3px;margin-left:6px;"><?php endif; ?></td>
              <td><?php echo sanitize($u['email']); ?></td>
              <td><?php echo sanitize($u['role']); ?></td>
              <td><?php echo sanitize($u['status']); ?></td>
              <td><?php echo sanitize($u['created_at']); ?></td>
              <td>
                <a class="button small" href="user_view.php?id=<?php echo intval($u['id']); ?>">View</a>
                <a class="button small" href="users_edit.php?id=<?php echo intval($u['id']); ?>">Edit</a>
                <?php if ($u['status'] === 'active'): ?>
                  <form style="display:inline" method="post" action="users_action.php">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="id" value="<?php echo intval($u['id']); ?>">
                    <input type="hidden" name="action" value="deactivate">
                    <button class="button small" onclick="return confirm('Deactivate this user?');" type="submit">Deactivate</button>
                  </form>
                <?php else: ?>
                  <form style="display:inline" method="post" action="users_action.php">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="id" value="<?php echo intval($u['id']); ?>">
                    <input type="hidden" name="action" value="activate">
                    <button class="button small" type="submit">Activate</button>
                  </form>
                <?php endif; ?>
                <form style="display:inline" method="post" action="users_action.php">
                  <?php echo csrf_input(); ?>
                  <input type="hidden" name="id" value="<?php echo intval($u['id']); ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="button small danger" onclick="return confirm('Delete this user permanently?');" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>