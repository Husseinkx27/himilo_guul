<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_once __DIR__ . '/../inc/header.php';

$stmt = $pdo->query('SELECT c.*, b.business_name FROM contacts c JOIN businesses b ON b.id = c.business_id ORDER BY c.created_at DESC');
$contacts = $stmt->fetchAll();
?>
<div class="layout">
  <?php include __DIR__ . '/../inc/sidebar.php'; ?>
  <section class="main">
    <h2>Contacts</h2>
    <div class="card">
      <?php if (!$contacts) echo '<p>No contact requests.</p>'; ?>
      <table class="table">
        <thead><tr><th>ID</th><th>Business</th><th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
          <?php foreach ($contacts as $c): ?>
            <tr>
              <td><?php echo intval($c['id']); ?></td>
              <td><?php echo sanitize($c['business_name']); ?></td>
              <td><?php echo sanitize($c['name']); ?></td>
              <td><?php echo sanitize($c['email']); ?></td>
              <td><?php echo sanitize($c['phone']); ?></td>
              <td><?php echo sanitize($c['message']); ?></td>
              <td><?php echo sanitize($c['status']); ?></td>
              <td><?php echo sanitize($c['created_at']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>