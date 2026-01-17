<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_once __DIR__ . '/../inc/header.php';

$businesses = $pdo->query('SELECT b.*,u.username as owner, (SELECT file_path FROM business_images bi WHERE bi.business_id = b.id LIMIT 1) AS thumbnail FROM businesses b JOIN users u ON u.id = b.owner_id ORDER BY b.created_at DESC')->fetchAll();
?>
<div class="layout">
  <?php include __DIR__ . '/../inc/sidebar.php'; ?>
  <section class="main">
    <h2>Businesses</h2>
    <p><a class="button" href="business_edit.php">Add New Business</a></p>
    <div class="card">
      <table class="table">
        <thead><tr><th>ID</th><th>Name</th><th>Image</th><th>Owner</th><th>Price</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($businesses as $b): ?>
            <tr>
              <td><?php echo intval($b['id']); ?></td>
              <td><?php echo sanitize($b['business_name']); ?></td>
              <td><?php if (!empty($b['thumbnail'])): ?><img src="<?php echo sanitize($b['thumbnail']); ?>" alt="thumb" style="height:48px;width:64px;object-fit:cover;border-radius:6px;border:1px solid #e6e9ec"><?php else: ?><img src="/projects/himiloGuul-php/assets/img/photo_placeholder.svg" alt="noimg" style="height:48px;width:64px;object-fit:cover;border-radius:6px;border:1px solid #e6e9ec"><?php endif; ?></td>
              <td><?php echo sanitize($b['owner']); ?></td>
              <td><?php echo number_format($b['price'],2); ?></td>
              <td><?php echo sanitize($b['status']); ?></td>
              <td><?php echo sanitize($b['created_at']); ?></td>
              <td class="actions"><a class="button small" href="business_view.php?id=<?php echo intval($b['id']); ?>">View</a> <a class="button small" href="business_edit.php?id=<?php echo intval($b['id']); ?>">Edit</a> <a class="button small danger" href="business_delete.php?id=<?php echo intval($b['id']); ?>" onclick="return confirm('Delete?')">Delete</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>