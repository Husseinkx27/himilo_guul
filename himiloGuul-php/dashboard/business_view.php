<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_admin();
require_once __DIR__ . '/../inc/header.php';

$id = intval($_GET['id'] ?? 0);
if (!$id){ echo '<p>Invalid business ID.</p>'; require_once __DIR__ . '/../inc/footer.php'; exit; }
$stmt = $pdo->prepare('SELECT b.*, u.username as owner FROM businesses b JOIN users u ON u.id = b.owner_id WHERE b.id = :id LIMIT 1');
$stmt->execute([':id'=>$id]);
$biz = $stmt->fetch();
if (!$biz){ echo '<p>Business not found.</p>'; require_once __DIR__ . '/../inc/footer.php'; exit; }
$imgs = $pdo->prepare('SELECT file_path FROM business_images WHERE business_id = :id ORDER BY id ASC');
$imgs->execute([':id'=>$id]);
$images = $imgs->fetchAll();
?>
<div class="layout">
  <?php include __DIR__ . '/../inc/sidebar.php'; ?>
  <section class="main">
    <h2>Business: <?php echo sanitize($biz['business_name']); ?> <a class="button small" href="businesses.php">Back</a> <a class="button small" href="business_edit.php?id=<?php echo intval($biz['id']); ?>">Edit</a></h2>

    <div class="card form-card" style="max-width:920px">
      <div class="form-grid">
        <div class="col-main">
          <div class="form-field full">
            <label>Name</label>
            <div class="text-muted"><?php echo sanitize($biz['business_name']); ?></div>
          </div>
          <div class="form-field">
            <label>Owner</label>
            <div class="text-muted"><?php echo sanitize($biz['owner']); ?></div>
          </div>
          <div class="form-field">
            <label>Price</label>
            <div class="text-muted"><?php echo number_format($biz['price'],2); ?></div>
          </div>
          <div class="form-field full">
            <label>Address</label>
            <div class="text-muted"><?php echo sanitize($biz['address']); ?></div>
          </div>
          <div class="form-field full">
            <label>Description</label>
            <div class="text-muted"><?php echo nl2br(sanitize($biz['description'])); ?></div>
          </div>
        </div>

        <div>
          <div class="profile-box">
            <div style="text-align:center;margin-bottom:8px;font-weight:600;color:var(--muted)">Gallery</div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
              <?php if (!empty($images)): ?>
                <?php foreach ($images as $img): ?>
                  <img src="<?php echo sanitize($img['file_path']); ?>" alt="img" style="width:160px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #e6e9ec">
                <?php endforeach; ?>
              <?php else: ?>
                <img src="/projects/himiloGuul-php/assets/img/photo_placeholder.svg" alt="no images" style="width:160px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #e6e9ec">
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>