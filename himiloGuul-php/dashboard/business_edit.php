<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_once __DIR__ . '/../inc/header.php';

$id = intval($_GET['id'] ?? 0);
$editing = $id > 0;
$errors = [];
if ($editing){
  $stmt = $pdo->prepare('SELECT * FROM businesses WHERE id = :id LIMIT 1');
  $stmt->execute([':id'=>$id]);
  $biz = $stmt->fetch();
  if (!$biz) { echo '<p>Not found.</p>'; require_once __DIR__ . '/../inc/footer.php'; exit; }
  // load existing images for preview
  $imgsStmt = $pdo->prepare('SELECT file_path FROM business_images WHERE business_id = :id ORDER BY id ASC');
  $imgsStmt->execute([':id'=>$id]);
  $existing_images = $imgsStmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  // CSRF
  if (!verify_csrf($_POST['csrf_token'] ?? '')){
    $errors[] = 'Invalid form submission.';
  }

  $business_name = trim($_POST['business_name'] ?? '');
  $price = floatval($_POST['price'] ?? 0);
  $address = trim($_POST['address'] ?? '');
  $description = trim($_POST['description'] ?? '');
  if (!$business_name) $errors[] = 'Business name required';
  if ($price <= 0) $errors[] = 'Price must be > 0';

  if (empty($errors)){
    if ($editing){
      $upd = $pdo->prepare('UPDATE businesses SET business_name=:n,price=:p,address=:a,description=:d WHERE id = :id');
      $upd->execute([':n'=>$business_name,':p'=>$price,':a'=>$address,':d'=>$description,':id'=>$id]);
    } else {
      $owner_id = $_SESSION['user']['id'];
      $ins = $pdo->prepare('INSERT INTO businesses (owner_id,business_name,address,price,description) VALUES (:o,:n,:a,:p,:d)');
      $ins->execute([':o'=>$owner_id,':n'=>$business_name,':a'=>$address,':p'=>$price,':d'=>$description]);
      $id = $pdo->lastInsertId();
    }    // handle images upload if provided
    if (!empty($_FILES['images'])){
      require_once __DIR__ . '/../lib/uploads.php';
      store_business_images($_FILES['images'],$id);
    }    header('Location: businesses.php'); exit;
  }
}

$users = $pdo->query('SELECT id,username FROM users ORDER BY username ASC')->fetchAll();
?>
<div class="layout">
  <?php include __DIR__ . '/../inc/sidebar.php'; ?>
  <section class="main">
    <h2><?php echo $editing? 'Edit Business':'Add New Business'; ?></h2>
    <?php if (!empty($errors)): ?><div class="alert error"><ul><?php foreach ($errors as $e) echo '<li>'.sanitize($e).'</li>'; ?></ul></div><?php endif; ?>

    <div class="card form-card">
      <form method="post" action="" enctype="multipart/form-data">
        <?php echo csrf_input(); ?>
        <div class="form-grid">
          <div class="col-main">
            <div class="form-field">
              <label>Business name</label>
              <input type="text" name="business_name" value="<?php echo sanitize($_POST['business_name'] ?? ($biz['business_name'] ?? '')); ?>">
            </div>

            <div class="form-field">
              <label>Price</label>
              <input type="text" name="price" value="<?php echo sanitize($_POST['price'] ?? ($biz['price'] ?? '')); ?>">
            </div>

            <div class="form-field full">
              <label>Address</label>
              <input type="text" name="address" value="<?php echo sanitize($_POST['address'] ?? ($biz['address'] ?? '')); ?>">
            </div>

            <div class="form-field full">
              <label>Description</label>
              <textarea name="description"><?php echo sanitize($_POST['description'] ?? ($biz['description'] ?? '')); ?></textarea>
            </div>

            <div class="form-field full">
              <label>Images</label>
              <input id="businessImagesInput" type="file" name="images[]" accept="image/*" multiple>
              <div class="text-muted" style="margin-top:8px;font-size:.9rem">You can upload multiple images; thumbnails will be generated.</div>
              <div class="text-muted" style="font-size:.9rem;margin-top:6px">Selected files preview:</div>
              <div id="businessSelectedPreview" style="display:flex;flex-wrap:wrap;margin-top:6px"></div>
            </div>

            <div class="form-field full form-actions">
              <button class="button" type="submit">Save</button>
              <a class="button small" href="businesses.php">Cancel</a>
            </div>
          </div>

          <div>
            <div class="profile-box">
              <div style="text-align:center;margin-bottom:8px;font-weight:600;color:var(--muted)"><?php echo $editing? 'Images / Gallery':'Preview'; ?></div>
              <div class="profile-preview" id="businessGalleryPreview" style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;align-items:start;justify-items:center">
                <?php if (!empty($existing_images)): ?>
                  <?php foreach ($existing_images as $img): ?>
                    <img src="<?php echo sanitize($img['file_path']); ?>" alt="img" style="width:120px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #e6e9ec">
                  <?php endforeach; ?>
                <?php else: ?>
                  <img src="/projects/himiloGuul-php/assets/img/photo_placeholder.svg" alt="no images" style="width:120px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #e6e9ec">
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        previewMultipleImages('#businessImagesInput','#businessSelectedPreview');
      });
    </script>
  </section>
</div>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>