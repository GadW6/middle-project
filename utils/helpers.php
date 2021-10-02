<?php 

require_once 'db_config.php';

if (!function_exists('old')) {
  function old($text){
    /**
     * 
     * Restore the last value of a field.
     * 
     * @param   string    $func   The field name
     * @return  string 
     * 
     */
     return $_REQUEST[$text] ?? '';
  }
}


if (!function_exists('csrf_token')) {
  function csrf_token(){
    /**
     * 
     * Set the csrf token for security purposes by randomizing a token.
     * 
     * @param   null              Takes no parameter
     * @return  string            Returns a 40 Chars long string
     * 
     */
    $token = sha1(rand(1, 1000) . date('Y.m.d.h.i.s'));
    $_SESSION['csrf_token'] = $token;
    return $token;
  }
}

if (!function_exists('auth_user')) {
  function auth_user(){
    /**
     * 
     * Verify if the user has been properly autorized/logged in.
     * It checks if the session has user_id/user_name/user_ip/user_agent which are set
     * at login.
     * 
     * @param   null             Takes no parameter
     * @return  bool             Returns a boolean value for the variable 'auth'
     * 
     */
    $auth = false;
    if (isset($_SESSION['user_id'])) {
      if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] == $_SERVER['REMOTE_ADDR']) {
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] == $_SERVER['HTTP_USER_AGENT']) {
          $auth = true;
        }
      }
    }
    return $auth;
  }
}

if (!function_exists('email_exist')) {
  function email_exist($email, $mysql){
    /**
     * 
     * Makes sure not to insert an email already 
     * existant inside the table user at signup.
     * 
     * @param   string  $email   Takes a string as first param, set user email
     * @param   string  $mysql   Takes a string as second param, set mysql connect
     * @return  bool             Returns a boolean value (True if user already exists)
     * 
     */
    $sql = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($mysql, $sql);
    if ($result && mysqli_num_rows($result) > 0) {return true;} 
    else {return false;}
  }
}

if (!function_exists('check_avatar')) {
  function check_avatar($file){
    /**
     * 
     * Validates the upload size, format, MIME of the avatar picture.
     * 
     * @param   string  $file   Takes a string as first param, inserting user email
     * @return  bool            Returns a boolean to variable $valid (True if file valid)
     * 
     */
    $valid = false;
    $allowed = [
      'max_file_size' => 1024 * 1024 * 5,
      'ex' => ['jpg', 'jpeg', 'gif', 'png', 'bmp'],
      'mimes' => ['image/jpeg', 'image/gif', 'image/png', 'image/bmp']
    ];

    if (isset($file['file-upload-field']['size']) && $file['file-upload-field']['size'] <= $allowed['max_file_size']) {

      if (isset($file['file-upload-field']['type']) && in_array(strtolower($file['file-upload-field']['type']), $allowed['mimes'])) {

        if (isset($file['file-upload-field']['name'])) {

          $file_detailed = pathinfo($file['file-upload-field']['name']);

          if (in_array(strtolower($file_detailed['extension']), $allowed['ex'])) {

            if (is_uploaded_file($file['file-upload-field']['tmp_name'])) {

              $valid = true;
            }
          }
        }
      }
    }

    return $valid;
  }
}

if (!function_exists('check_image_post')) {
  function check_image_post($file){
    /**
     * 
     * Validates the upload size, format, MIME of the post picture.
     * 
     * @param   string  $file   Takes a string as first param, inserting user email
     * @return  bool            Returns a boolean to variable $valid (True if file valid)
     * 
     */
    $valid = false;
    $allowed = [
      'max_file_size' => 1024 * 1024 * 5,
      'ex' => ['jpg', 'jpeg', 'gif', 'png', 'bmp'],
      'mimes' => ['image/jpeg', 'image/gif', 'image/png', 'image/bmp']
    ];

    if (isset($file['input-upload']['size']) && $file['input-upload']['size'] <= $allowed['max_file_size']) {

      if (isset($file['input-upload']['type']) && in_array(strtolower($file['input-upload']['type']), $allowed['mimes'])) {

        if (isset($file['input-upload']['name'])) {

          $file_detailed = pathinfo($file['input-upload']['name']);

          if (in_array(strtolower($file_detailed['extension']), $allowed['ex'])) {

            if (is_uploaded_file($file['input-upload']['tmp_name'])) {

              $valid = true;
            }
          }
        }
      }
    }

    return $valid;
  }
}


?>