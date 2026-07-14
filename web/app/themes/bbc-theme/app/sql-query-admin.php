<?php

add_action('admin_menu', function () {
  add_submenu_page(
    'tools.php',
    'SQL Query Runner',
    'SQL Query Runner',
    'manage_options',
    'sql-query-runner',
    'sql_query_runner_view'
  );
});

function sql_query_runner_view()
{
  global $wpdb;
?>

  <div class="wrap">
    <h1>SQL Query Runner</h1>

    <form method="post">
      <textarea name="sql_query" style="width:100%;height:150px;"><?php echo isset($_POST['sql_query']) ? esc_textarea($_POST['sql_query']) : ''; ?></textarea>
      <p><input type="submit" class="button button-primary" value="Ausführen"></p>
    </form>

    <?php
    if (!empty($_POST['sql_query'])) {

      $sql = wp_unslash($_POST['sql_query']);

      if (!preg_match('/^\s*select/i', $sql)) {
        echo '<div class="notice notice-error"><p>Nur SELECT Queries erlaubt.</p></div>';
        return;
      }

      echo '<h2>Ergebnis</h2>';

      $results = $wpdb->get_results($sql, ARRAY_A);

      if (empty($results)) {
        echo '<div class="notice notice-warning"><p>Keine Ergebnisse.</p></div>';
        return;
      }

      echo '<table class="widefat fixed striped"><thead><tr>';

      foreach (array_keys($results[0]) as $col) {
        echo '<th>' . esc_html($col) . '</th>';
      }

      echo '</tr></thead><tbody>';

      foreach ($results as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
          echo '<td>' . esc_html($cell) . '</td>';
        }
        echo '</tr>';
      }

      echo '</tbody></table>';
    }
    ?>
  </div>
<?php
}
