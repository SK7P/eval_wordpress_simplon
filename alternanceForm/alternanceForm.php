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
    birth_date datetime NOT NULL,
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
  $free_notes = $_POST['free_notes'];
  $table_name = $wpdb->prefix . 'registrations';
  $data = array(
    'created_at' => $created_at,
    'firstname' => $firstname,
    'lastname' => $lastname,
    'birth_date' => $birth_date,
    'degree' => $degree,
    'current_study_lvl' => $current_study_lvl,
    'formation_goal' => $formation_goal,
    'city' => $city,
    'mobility' => $mobility
  );
  if ($free_notes) {
    $data = array(
      'created_at' => $created_at,
      'firstname' => $firstname,
      'lastname' => $lastname,
      'birth_date' => $birth_date,
      'degree' => $degree,
      'current_study_lvl' => $current_study_lvl,
      'formation_goal' => $formation_goal,
      'city' => $city,
      'mobility' => $mobility,
      'free_notes' => $free_notes
    );
  }

  $data_insert = $wpdb->insert(
    $table_name,
    $data
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
    <div class="input-container padding-top">
      <label for="firstname">Prénom:</label>
      <input type="text" id="firstname" name="firstname" required />
    </div>
    <div class="input-container padding-top">
      <label for="lastname">Nom:</label>
      <input type="text" id="lastname" name="lastname" required />
    </div>
    <div class="input-container padding-top">
      <label for="birth_date">Date de naissance:</label>
      <input type="date" id="birth_date" name="birth_date" required />
    </div>
    <div class="input-container padding-top">
      <label for="degree">Libellé du diplôme actuel:</label>
      <input type="text" id="degree" name="degree" required />
    </div>
    <div class="input-container padding-top">
      <label for="current_study_lvl">Niveau d'études actuel:</label>
      <input type="text" id="current_study_lvl" name="current_study_lvl" required />
    </div>
    <div class="input-container padding-top">
      <label for="formation_goal">Formation visée:</label>
      <input type="text" id="formation_goal" name="formation_goal" required />
    </div>
    <div class="input-container padding-top">
      <label for="city">Ville d'habitation:</label>
      <input type="text" id="city" name="city" required />
    </div>
    <div class="input-container padding-top">
      <label for="mobility">Mobilité (en kilomètres depuis le lieu de vie):</label>
      <input type="number" id="mobility" name="mobility" required />
    </div>
    <div class="input-container padding-top">
      <label for="free_notes">Notes:</label>
      <textarea type="text" id="free_notes" name="free_notes">
      </textarea>
    </div>
    <div class="input-container padding-top">
      <input type="submit" value="Continuer" />
    </div>
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

function add_custom_menu()
{
  add_menu_page(
    'Gérer les Inscriptions',
    'Gérer les Inscriptions',
    'manage_options',
    'manage-registrations',
    'display_registration_list'
  );
}

add_action('admin_menu', 'add_custom_menu');

function display_registration_list()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'registrations';
  $registrations = $wpdb->get_results("SELECT * FROM $table_name");

  echo '<div class="wrap">';
  echo '<h2>Liste des Inscriptions</h2>';
  echo '<table class="widefat">';
  echo '<thead><tr>';
  echo '<th>ID</th>';
  echo '<th>CreatedAt</th>';
  echo '<th>Nom Prénom</th>';
  echo '<th>Date de Naissance</th>';
  echo "<th>Niveau d'études actuel</th>";
  echo '<th>Formation visée</th>';
  echo '</tr></thead>';
  echo '<tbody>';
  foreach ($registrations as $registration) {
    echo '<tr>';
    echo '<td>' . $registration->id . '</td>';
    echo '<td>' . $registration->created_at . '</td>';
    echo '<td>' . $registration->firstname . ' ' . $registration->lastname . '</td>';
    echo '<td>' . $registration->birth_date . '</td>';
    echo '<td>' . $registration->current_study_lvl . '</td>';
    echo '<td>' . $registration->formation_goal . '</td>';
    echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
}



add_shortcode('form', 'form_func');
add_shortcode('get_registrations', 'get_registrations');
add_shortcode('add_registration', 'add_registration');
