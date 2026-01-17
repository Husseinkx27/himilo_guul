<?php
require_once __DIR__ . '/inc/header.php';
require_once __DIR__ . '/connection.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT b.*, u.username as owner FROM businesses b JOIN users u ON u.id = b.owner_id WHERE b.id = :id LIMIT 1');
$stmt->execute([':id'=>$id]);
$biz = $stmt->fetch();
if (!$biz) {
  echo '<p>Business not found.</p>';
  require_once __DIR__ . '/inc/footer.php';
  exit;
}
$imgs = $pdo->prepare('SELECT * FROM business_images WHERE business_id = :id');
$imgs->execute([':id'=>$id]);
$images = $imgs->fetchAll();
?>
<div class="layout">
  <?php include __DIR__ . '/inc/sidebar.php'; ?>
  <section class="main">
    <h2><?php echo sanitize($biz['business_name']); ?></h2>
    <p><strong>Owner:</strong> <?php echo sanitize($biz['owner']); ?></p>
    <p><?php echo nl2br(sanitize($biz['description'])); ?></p>
    <p><strong>Price:</strong> $<?php echo number_format($biz['price'],2); ?></p>
    <?php if ($images): ?>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <?php foreach ($images as $im): ?>
          <img src="/<?php echo ltrim($im['file_path'],'/'); ?>" alt="<?php echo sanitize($im['alt_text'] ?? ''); ?>" style="width:200px;height:140px;object-fit:cover;border-radius:4px">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <h3>Contact Seller</h3>
    <form method="post" action="/projects/himiloGuul-php/contact_submit.php">
      <?php echo csrf_input(); ?>
      <input type="hidden" name="business_id" value="<?php echo intval($biz['id']); ?>">
      <label>Name<br><input type="text" name="name"></label><br>
      <label>Email<br><input type="email" name="email"></label><br>
      <label>Phone<br><input type="text" name="phone"></label><br>
      <label>Message<br><textarea name="message"></textarea></label><br>
      <button type="submit" class="button">Send</button>
    </form>
  </section>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>