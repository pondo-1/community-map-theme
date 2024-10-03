<?
class WelcomePopup
{
  public function __construct()
  {
    wp_enqueue_script('welcome_popup',                    get_template_directory_uri() . '/js/welcome_popup.js',  array('jquery'), false, false);
  }
}
new WelcomePopup;
