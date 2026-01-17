<?php if (empty($_SESSION['user'])) { return; } ?>
<aside class="sidebar">
  <h4>Menu</h4>
  <div class="text-muted" style="margin-bottom:12px">Signed in as <strong><?php echo htmlspecialchars($_SESSION['user']['username']); ?></strong></div>
  <ul class="menu">
    <li><a href="/projects/himiloGuul-php/dashboard/index.php"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>Dashboard</a></li>
    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Admin'): ?>
      <li><a href="/projects/himiloGuul-php/dashboard/users.php"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/></svg>Users</a></li>
      <li><a href="/projects/himiloGuul-php/dashboard/businesses.php"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 9l9-6 9 6v9a1 1 0 0 1-1 1h-16a1 1 0 0 1-1-1zM12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>Businesses</a></li>
      <li><a href="/projects/himiloGuul-php/dashboard/contacts.php"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM20 8l-8 5-8-5"/></svg>Contacts</a></li>
    <?php else: ?>
      <li><a href="/projects/himiloGuul-php/dashboard/businesses.php"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 9l9-6 9 6v9a1 1 0 0 1-1 1h-16a1 1 0 0 1-1-1zM12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>My Businesses</a></li>
    <?php endif; ?>
    <li><a href="/projects/himiloGuul-php/logout.php"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>Logout</a></li>
  </ul>
</aside>