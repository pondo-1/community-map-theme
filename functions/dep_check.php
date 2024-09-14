<?
add_action('admin_init', 'check_required_plugin');
// Dependencies, 
function check_required_plugin()
{

  // Specify the required plugin by its folder and main file
  $required_plugin = 'advanced-custom-fields-pro/acf.php';

  // Check if the plugin is active
  if (!is_plugin_active($required_plugin)) {
    // Display a warning message in the admin area
    add_action('admin_notices', 'show_plugin_warning');
  }
}

function show_plugin_warning()
{
  // Customize your warning message
  echo '<div class="notice notice-error is-dismissible">
           <p><strong>Warning:</strong> This theme requires plugin <em>advanced custom fields pro</em>. But that is not installed or activated. Please install and activate the plugin to ensure proper functionality of the theme.</p>
       </div>';
}
