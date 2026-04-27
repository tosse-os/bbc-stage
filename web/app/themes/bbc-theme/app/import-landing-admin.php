<?php

namespace App;

add_action('admin_menu', function () {
  add_submenu_page(
    'tools.php',
    'Landing Import',
    'Landing Import',
    'manage_options',
    'landing-import',
    function () {
?>
    <div class="wrap">
      <h1>Landing Import</h1>

      <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="import_landing">

        <p>
          <label>Post ID (EN Seite)</label><br>
          <input type="number" name="post_id" required>
        </p>

        <p>
          <input type="file" name="file" accept="application/json" required>
        </p>

        <p>
          <button class="button button-primary">Import JSON</button>
        </p>
      </form>
    </div>
<?php
    }
  );
});
