<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_once __DIR__ . '/../inc/header.php';
?>
<div class="layout">
  <?php include __DIR__ . '/../inc/sidebar.php'; ?>
  <section class="main">
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo sanitize($_SESSION['user']['username'] ?? ''); ?></p>

    <?php
      // fetch counts
      $counts = $pdo->query("SELECT 
        (SELECT COUNT(*) FROM users) AS users_count,
        (SELECT COUNT(*) FROM businesses) AS businesses_count,
        (SELECT COUNT(*) FROM contacts) AS contacts_count")->fetch();

      $recentContacts = $pdo->query("SELECT c.*, b.business_name FROM contacts c JOIN businesses b ON b.id = c.business_id ORDER BY c.created_at DESC LIMIT 5")->fetchAll();
      $recentBiz = $pdo->query("SELECT b.id,b.business_name,b.price,u.username as owner FROM businesses b JOIN users u ON u.id = b.owner_id ORDER BY b.created_at DESC LIMIT 5")->fetchAll();
    ?>

    <div class="cards-grid">
      <div class="card">
        <div class="card-metric">
          <div class="card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/></svg>
          </div>
          <div>
            <div class="metric"><?php echo intval($counts['users_count']); ?></div>
            <div class="label">Users</div>
            <div style="margin-top:8px;"><a href="users.php">View users</a></div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-metric">
          <div class="card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 9l9-6 9 6v9a1 1 0 0 1-1 1h-16a1 1 0 0 1-1-1zM12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
          </div>
          <div>
            <div class="metric"><?php echo intval($counts['businesses_count']); ?></div>
            <div class="label">Businesses</div>
            <div style="margin-top:8px;"><a href="businesses.php">View businesses</a></div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-metric">
          <div class="card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM20 8l-8 5-8-5"/></svg>
          </div>
          <div>
            <div class="metric"><?php echo intval($counts['contacts_count']); ?></div>
            <div class="label">Contact Requests</div>
            <div style="margin-top:8px;"><a href="contacts.php">View contacts</a></div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <h3>Recent contact requests</h3>
      <?php if ($recentContacts): ?>
        <table class="table">
          <thead><tr><th>ID</th><th>Business</th><th>Name</th><th>Email</th><th>Message</th><th>Created</th></tr></thead>
          <tbody>
            <?php foreach ($recentContacts as $c): ?>
              <tr>
                <td><?php echo intval($c['id']); ?></td>
                <td><?php echo sanitize($c['business_name']); ?></td>
                <td><?php echo sanitize($c['name']); ?></td>
                <td><?php echo sanitize($c['email']); ?></td>
                <td><?php echo sanitize(substr($c['message'],0,80)); ?></td>
                <td><?php echo sanitize($c['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted">No recent contacts.</p>
      <?php endif; ?>
    </div>

    <div style="height:12px"></div>

    <div class="card">
      <h3>Recent businesses</h3>
      <?php if ($recentBiz): ?>
        <table class="table">
          <thead><tr><th>ID</th><th>Name</th><th>Owner</th><th>Price</th></tr></thead>
          <tbody>
            <?php foreach ($recentBiz as $b): ?>
              <tr>
                <td><?php echo intval($b['id']); ?></td>
                <td><?php echo sanitize($b['business_name']); ?></td>
                <td><?php echo sanitize($b['owner']); ?></td>
                <td><?php echo number_format($b['price'],2); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted">No recent businesses.</p>
      <?php endif; ?>
    </div>

  </section>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>