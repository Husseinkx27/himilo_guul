<?php require_once __DIR__ . '/inc/header.php'; require_once __DIR__ . '/connection.php'; ?>

<div class="layout">
  <?php include __DIR__ . '/inc/sidebar.php'; ?>
  <section class="main">
    <h2>Public Listings</h2>
    <?php
      // Fetch latest businesses (select one image per business without GROUP BY)
      $stmt = $pdo->query("SELECT b.id,b.business_name,b.price,b.description,
        (SELECT file_path FROM business_images bi WHERE bi.business_id = b.id LIMIT 1) as file_path
        FROM businesses b
        WHERE b.status = 'available'
        ORDER BY b.created_at DESC LIMIT 12");
      $businesses = $stmt->fetchAll();
      if (!$businesses) {
        echo '<p>No businesses listed yet.</p>';
      } else {
        echo '<div class="cards" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:12px">';
        foreach ($businesses as $biz) {
          $img = $biz['file_path'] ? '/' . ltrim($biz['file_path'], '/') : '/projects/himiloGuul-php/assets/img/placeholder.svg';
          echo '<article style="background:#fff;padding:12px;border-radius:6px;">';
          echo '<img src="'.sanitize($img).'" alt="'.sanitize($biz['business_name']).'" style="width:100%;height:150px;object-fit:cover;border-radius:4px;margin-bottom:8px">';
          echo '<h3>'.sanitize($biz['business_name']).'</h3>';
          echo '<p>'.nl2br(sanitize(substr($biz['description'],0,140))).'</p>';
          echo '<p><strong>Price:</strong> $'.number_format($biz['price'],2).'</p>';
          echo '<a class="button" href="/projects/himiloGuul-php/business.php?id='.intval($biz['id']).'">View</a>';
          echo '</article>';
        }
        echo '</div>';
      }
    ?>
  </section>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>