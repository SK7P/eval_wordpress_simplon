<?php
/*
 * Plugin Name:       Alternance Form Plugin
 * Description:       Plugin to handle registration for alternance.
 * Version:           1
 * Author:            SK7P
 * Text Domain:       alternance-form-plugin
 */

register_activation_hook(__FILE__, 'alternance_form_install');


add_action('wp_enqueue_scripts', 'prefix_add_my_stylesheet');
function prefix_add_my_stylesheet()
{
  wp_register_style('prefix-style', plugins_url('style.css', __FILE__));
  wp_enqueue_style('prefix-style');
}


//Create registration table
function alternance_form_install()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'registrations';
  $sql = "DROP TABLE IF EXISTS $table_name";
  $wpdb->query($sql);

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		firstname tinytext NOT NULL,
    lastname tinytext NOT NULL,
    birth_date tinytext NOT NULL,
    degree tinytext NOT NULL,
    current_study_lvl tinytext NOT NULL,
    formation_goal text NOT NULL,
    city text NOT NULL,
		mobility mediumint(9) NOT NULL,
    free_notes text,
		PRIMARY KEY  (id)
	) $charset_collate;";

  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta($sql);
}

function add_registration()
{
  ob_start();
  var_dump($_POST);
  global $wpdb;

  $created_at = current_time('mysql');
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $birth_date = $_POST['birth_date'];
  $degree = $_POST['degree'];
  $current_study_lvl = $_POST['current_study_lvl'];
  $formation_goal = $_POST['formation_goal'];
  $city = $_POST['city'];
  $mobility = $_POST['mobility'];

  $table_name = $wpdb->prefix . 'registrations';

  $data_insert = $wpdb->insert(
    $table_name,
    array(
      'created_at' => $created_at,
      'firstname' => $firstname,
      'lastname' => $lastname,
      'birth_date' => $birth_date,
      'degree' => $degree,
      'current_study_lvl' => $current_study_lvl,
      'formation_goal' => $formation_goal,
      'city' => $city,
      'mobility' => $mobility,
    )
  );
  if ($data_insert) {
    echo '<h2> Votre inscription a bien été enregistrée ! Merci :) </h2>';
  } else {
    echo '<h2> Something gone wrong, please try again </h2>';
  }
  return ob_get_clean();
}
function form_func()
{
  ob_start();
?>

  <form action="http://localhost:8080/?page_id=21" method="post">
    <div>
      <label for="firstname">firstname:</label>
      <input type="text" id="firstname" name="firstname" required />
    </div>
    <div>
      <label for="lastname">lastname:</label>
      <input type="text" id="lastname" name="lastname" required />
    </div>
    <div>
      <label for="birth_date">birth_date:</label>
      <input type="text" id="birth_date" name="birth_date" required />
    </div>
    <div>
      <label for="degree">degree:</label>
      <input type="text" id="degree" name="degree" required />
    </div>
    <div>
      <label for="current_study_lvl">current_study_lvl:</label>
      <input type="text" id="current_study_lvl" name="current_study_lvl" required />
    </div>
    <div>
      <label for="formation_goal">formation_goal:</label>
      <input type="text" id="formation_goal" name="formation_goal" required />
    </div>
    <div>
      <label for="city">city:</label>
      <input type="text" id="city" name="city" required />
    </div>
    <div>
      <label for="mobility">mobility:</label>
      <input type="number" id="mobility" name="mobility" required />
    </div>
    <button type="submit">Submit</button>
  </form>
  <?php

  return ob_get_clean();
}

function get_registrations()
{
  ob_start();
  global $wpdb;
  $table_name = $wpdb->prefix . 'registrations';
  $registrations = $wpdb->get_results(
    "
      SELECT *
      FROM $table_name
    "
  );
  $columns = $wpdb->get_col("DESC {$table_name}", 0);
  if ($registrations) {
  ?>
    <table class="table">
      <?php
      foreach ($columns as $col) {
        echo '<th>' . $col . '</th>';
      }
      foreach ($registrations as $registration) {
        print_r($registration);
        echo
        '<tr>' .
          '<td>' . $registration->id . '</td>' .
          '<td>' . $registration->created_at . '</td>' .
          '<td>' . $registration->firstname . '</td>' .
          '<td>' . $registration->lastname . '</td>' .
          '<td>' . $registration->birth_date . '</td>' .
          '<td>' . $registration->degree . '</td>' .
          '<td>' . $registration->current_study_lvl . '</td>' .
          '<td>' . $registration->formation_goal . '</td>' .
          '<td>' . $registration->city . '</td>' .
          '<td>' . $registration->mobility . '</td>' .
          '<td>' . $registration->free_notes . '</td>' .
          '</tr>';
      }
      ?>
    </table>
<?php
  } else {
    echo '<h3> Pas de données enregistrées ! </h3>';
  }
  return ob_get_clean();
}

add_shortcode('form', 'form_func');
add_shortcode('get_registrations', 'get_registrations');
add_shortcode('add_registration', 'add_registration');
