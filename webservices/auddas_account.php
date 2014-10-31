<?php
/* ********************************* ACCOUNT ******************************** */
/*////////////////////////////////////////////////////////////////////////////*/
// PROJECT:        AUDDAS Directory 
// TITLE:          Account
// DESCRIPTION:    Class representing an account related to an user
// AUTHORS:        Marc Joos & Vincent Reverdy 
// DATE:           2013-2014
// LICENSE:        GNU GPL version 3 
/*////////////////////////////////////////////////////////////////////////////*/
class Account {
/*////////////////////////////////////////////////////////////////////////////*/



//--------------------------------- CONSTANTS ------------------------------- //
const default_database = "auddas_accounts";
const default_users_database = "auddas_users";
const default_configuration_database = "auddas_configuration";
const default_jobs_database = "auddas_jobs";
const default_theses_database = "auddas_theses";
const default_date = "0000-00-00";
const default_insc_formtitle = "Inscription";
const default_valid_formtitle = "Validation";
const default_login_formtitle = "Connexion";
const default_manage_formtitle = "G&eacute;rer votre compte";
const default_reset_formtitle = "R&eacute;initialiser votre mot de passe";
//--------------------------------------------------------------------------- //



//-------------------------------- ATTRIBUTES ------------------------------- //
var $id;
var $privilege;
var $email;
var $login;
var $salt;
var $password;
var $activation_key;
var $creation_date;
var $last_modification;
var $last_connection;
var $subscription_date;
//--------------------------------------------------------------------------- //



//--------------------------------- LIFECYCLE ------------------------------- //
// Constructs the account from its properties
public function __construct($input_id = 0,
                            $input_privilege = 0,
                            $input_email = "",
                            $input_login = "",
                            $input_salt = "",
                            $input_password = "",
                            $input_activation_key = 0,
                            $input_creation_date = NULL,
                            $input_last_modification = NULL,
                            $input_last_connection = NULL,
                            $input_subscription_date = NULL) 
{
    if ($input_creation_date === NULL) $input_creation_date = new DateTime();
    if ($input_last_modification === NULL) $input_last_modification = new DateTime();
    if ($input_last_connection === NULL) $input_last_connection = new DateTime();
    if ($input_subscription_date === NULL) $input_subscription_date = new DateTime();
    $this->id = $input_id;
    $this->privilege = $input_privilege;
    $this->email = $input_email;
    $this->login = $input_login;
    $this->salt = $input_salt;
    $this->password = $input_password;
    $this->activation_key = $input_activation_key;
    $this->creation_date = $input_creation_date;
    $this->last_modification = $input_last_modification;
    $this->last_connection = $input_last_connection;
    $this->subscription_date = $input_subscription_date;
}
//--------------------------------------------------------------------------- //



//-------------------------------- MANAGEMENT ------------------------------- //
// Creates a account in the database
public function create($input_dbname = self::default_database)
{
    include("opendb.php");
    $query = "INSERT INTO {$input_dbname}
              (id, privilege, email, login, salt, password, activation_key, creation_date, last_modification, last_connection)
              VALUES ('{$this->id}', 
                      '{$this->privilege}',
                      '{$this->email}',
                      '{$this->login}',
                      '{$this->salt}',
                      '{$this->password}',
                      '{$this->activation_key}',
                      '{$this->creation_date->format('c')}',
                      '{$this->last_modification->format('c')}',
                      '{$this->last_connection->format('c')}'
                      '{$this->subscription_date->format('c')}')";
    $results = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
}

// Deletes a account in the database
public function delete($input_dbname = self::default_database)
{
    include("opendb.php");
    $count = 0;
    $query = "SELECT * FROM {$input_dbname}
              WHERE id = {$this->id}
              ORDER by id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    if ($count === 1) {
        $query = "DELETE FROM {$input_dbname} 
                  WHERE id = {$this->id} 
                  ORDER BY id ASC";
        $results = mysql_query($query);
    }
    include("closedb.php");
}

// Retrieves an account with its id
public static function retrieve($input_id, $input_dbname = self::default_database)
{
    include("opendb.php");
    $account = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$input_id} 
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    if ($count === 1) {
        while ($row = mysql_fetch_array($results)) {
            $account_creation_date = new DateTime($row["creation_date"]);
            $account_last_modification = new DateTime($row["last_modification"]);
            $account_last_connection = new DateTime($row["last_connection"]);
            $account_subscription_date = new DateTime($row["subscription_date"]);
            $account = new Account($row["id"], 
                                   $row["privilege"],
                                   $row["email"],
                                   $row["login"],
                                   $row["salt"],
                                   $row["password"],
                                   $row["activation_key"],
                                   $account_creation_date,
                                   $account_last_modification,
                                   $account_last_connection,
                                   $account_subscription_date);
        }
    } else {
        echo mysql_error();
    }
    include("closedb.php");
    return $account;
}

// Retrieves an account with its activation key
public static function retrieve_from_key($input_key, $input_dbname = self::default_database)
{
    include("opendb.php");
    $account = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              WHERE activation_key = '{$input_key}'
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    if ($count === 1) {
        while ($row = mysql_fetch_array($results)) {
            $account_creation_date = new DateTime($row["creation_date"]);
            $account_last_modification = new DateTime($row["last_modification"]);
            $account_last_connection = new DateTime($row["last_connection"]);
            $account_subscription_date = new DateTime($row["subscription_date"]);
            $account = new Account($row["id"], 
                                   $row["privilege"],
                                   $row["email"],
                                   $row["login"],
                                   $row["salt"],
                                   $row["password"],
                                   $row["activation_key"],
                                   $account_creation_date,
                                   $account_last_modification,
                                   $account_last_connection,
                                   $account_subscription_date);
        }
    } else {
        echo mysql_error();
    }
    include("closedb.php");
    return $account;
}

// Retrieves an account with its email address
public static function retrieve_from_email($input_email, $input_dbname = self::default_database)
{
    include("opendb.php");
    $account = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              WHERE email = '{$input_email}'
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    if ($count === 1) {
        while ($row = mysql_fetch_array($results)) {
            $account_creation_date = new DateTime($row["creation_date"]);
            $account_last_modification = new DateTime($row["last_modification"]);
            $account_last_connection = new DateTime($row["last_connection"]);
            $account_subscription_date = new DateTime($row["subscription_date"]);
            $account = new Account($row["id"], 
                                   $row["privilege"],
                                   $row["email"],
                                   $row["login"],
                                   $row["salt"],
                                   $row["password"],
                                   $row["activation_key"],
                                   $account_creation_date,
                                   $account_last_modification,
                                   $account_last_connection,
                                   $account_subscription_date);
        }
    } else {
        echo mysql_error();
    }
    include("closedb.php");
    return $account;
}

// Retrieves an account with its login
public static function retrieve_from_login($input_login, $input_dbname = self::default_database)
{
    include("opendb.php");
    $account = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              WHERE login = '{$input_login}'
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    if ($count === 1) {
        while ($row = mysql_fetch_array($results)) {
            $account_creation_date = new DateTime($row["creation_date"]);
            $account_last_modification = new DateTime($row["last_modification"]);
            $account_last_connection = new DateTime($row["last_connection"]);
            $account_subscription_date = new DateTime($row["subscription_date"]);
            $account = new Account($row["id"], 
                                   $row["privilege"],
                                   $row["email"],
                                   $row["login"],
                                   $row["salt"],
                                   $row["password"],
                                   $row["activation_key"],
                                   $account_creation_date,
                                   $account_last_modification,
                                   $account_last_connection,
                                   $account_subscription_date);
        }
    } else {
        echo mysql_error();
    }
    include("closedb.php");
    return $account;
}

// Updates an account in the database
public function update($input_dbname = self::default_database)
{
    include("opendb.php");
    $query = "UPDATE {$input_dbname}
              SET id='{$this->id}',
                  privilege='{$this->privilege}',
                  email='{$this->email}',
                  login='{$this->login}',
                  salt='{$this->salt}',
                  password='{$this->password}',
                  activation_key='{$this->activation_key}',
                  creation_date='{$this->creation_date->format('c')}',
                  last_modification='{$this->last_modification->format('c')}',
                  last_connection='{$this->last_connection->format('c')}',
                  subscription_date='{$this->subscription_date->format('c')}'
              WHERE 
                  id='{$this->id}'";
    $results = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
}
//--------------------------------------------------------------------------- //



//-------------------------- PASSWORD MANAGEMENT ----------------------------- //
// Generates salt for password
public static function generate_salt() 
{
    $salt = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
    return $salt;
}

// Generates hash from string and salt
public static function generate_hash($string, $salt)
{
    $hash = sha1($string.$salt);
    return $hash;
}

// Checks password
public static function check_password($password, $salt, $hashed_password)
{
    $new_hash = self::generate_hash($password, $salt);
    return ($new_hash === $hashed_password);
}

// Stores password and salt for the given user
public function store_password($new_password, $input_dbname = self::default_database)
{
    $this->salt = bin2hex(self::generate_salt());
    $hash = self::generate_hash($new_password, $this->salt);
    include("opendb.php");
    $query = "UPDATE {$input_dbname}
            SET salt='{$this->salt}',
                password='{$hash}'
            WHERE
                id='{$this->id}'";
    $result = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
}
//--------------------------------------------------------------------------- //



//------------------------------ INSCRIPTION -------------------------------- //
// Generates inscription form and create a new account in the database 
public static function inscription_form($input_formtitle = self::default_insc_formtitle, $input_dbname = self::default_database, $input_conf_dbname = self::default_configuration_database)
{
    $current_account = new Account();
    $publickey = "6LcfRvUSAAAAANb7b2PfyHFUodQGqcVlLdTY1CoO";
    // Generates form
    require_once('includes/recaptchalib.php');
    echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
    echo "<fieldset><legend>".$input_formtitle."</legend>";
    echo "<table>";
    echo "<tr><td>Adresse mail</td><td>:</td><td><input type='text' name='sub_email' placeholder='albert.einstein@princeton.edu' value='".$_POST['sub_email']."'></td></tr>";    
    echo "<tr><td colspan=3><center>".recaptcha_get_html($publickey)."</center></td></tr>";
    echo "<tr><td colspan=3><center><input type='submit' name='submit' value='Envoyer'></center></td></tr>";
    echo "</table>";
    echo "</fieldset>";
    echo "</form>";
    $privatekey = "6LcfRvUSAAAAADxr-QG2vjmc34uiO1gU64d_7Z6e";
    $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);

    if (isset($_POST['submit'])) {
        if ($resp->is_valid) {
            if ($_POST['sub_email'] === '') {
                echo "<b>Vous devez rentrer une adresse e-mail !</b>";
            } else {
                $temp_account = self::retrieve_from_email($_POST['sub_email'], $input_dbname);
                if ($temp_account->id != 0) {
                    echo "<b>Un compte est d&eacute;j&agrave; associ&eacute; &agrave; cette adresse mail, merci d'en choisir une autre !</b>";
                } else {
                    $current_account->email = $_POST['sub_email'];
                    // Generates activation key
                    $current_account->activation_key = mt_rand().mt_rand().mt_rand().mt_rand().mt_rand();
                    $ccurent_account->creation_date = new DateTime();
                    $current_account->create($input_dbname);
                    // Send verification mail
                    $root = ($_SERVER['HTTPS'] ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/';
                    $receiver = $current_account->email;
                    $subject = "[Annuaire AUDDAS]". // should be retrieve from configuration_db...
                        " Validation de votre inscription";
                    $message = "<p>Bonjour et bienvenue !</p>
        
                <p>Vous, ou quelqu'un utilisant votre adresse e-mail, &ecirc;tes pr&eacute;inscrit sur notre annuaire. Pour confirmer votre inscription, merci de cliquer sur le lien suivant :<br/>
                <a href='".$root."annuaire/index.php?page=validate&activation_key=".$current_account->activation_key."'>".$root."annuaire/index.php?page=validate&activation_key=".$current_account->activation_key."</a><br/>
                Apr&egrave;s activation, vous pourrez renseigner votre parcours et effectuer des recherches dans notre annuaire.</p>
                
                <p>Merci de votre int&eacute;r&ecirc;t pour AUDDAS !</p>
                
                <p>Si ce mail ne vous &eacute;tait pas destin&eacute;, merci de l'ignorer.</p>";
                    $headers = "From: contact@auddas.fr"."\r\n".
                        "Content-type: text/html; charset=utf-8"."\r\n".
                        "Reply-to: contact@auddas.fr"."\r\n".
                        "Return-Path: contact@auddas.fr"."\r\n".
                        "X-mailer: PHP/".phpversion();
                    mail($receiver, $subject, $message, $headers);

                    // Send admin email
                    $root = ($_SERVER['HTTPS'] ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/';
                    $receiver_adm = "webmaster@auddas.fr";
                    $subject_adm = "[Annuaire AUDDAS] Nouvelle inscription : ".$current_account->email;
                    $message_adm = "<p>Bonjour,</p>
                    <p>Une personne vient de s'inscrire sur l'annuaire avec l'adresse ".$current_account->email.".</p>";
                    $headers_adm = "From: contact@auddas.fr"."\r\n".
                        "Content-type: text/html; charset=utf-8"."\r\n".
                        "Reply-to: contact@auddas.fr"."\r\n".
                        "Return-Path: contact@auddas.fr"."\r\n".
                        "X-mailer: PHP/".phpversion();
                    mail($receiver_adm, $subject_adm, $message_adm, $headers_adm);

                    echo "<meta http-equiv='refresh' content='0;url=./index.php?page=confirm_registration'>";
               }
            }
        } else {
            echo "<b>Attention : captcha invalide !</b>";
        }
    }
}

// Validate a new user
public static function validate($input_formtitle = self::default_valid_formtitle, $input_dbname = self::default_database)
{
    $current_account = new Account();
    if ($_GET['activation_key']) {
        $current_account = self::retrieve_from_key($_GET['activation_key'], $input_dbname);
        if (!$current_account->id) {
            echo "<b>Attention :</b> la cl&eacute; d'activation que vous avez fournie est invalide. <br/>
Si vous n'avez pas encore activ&eacute; votre compte mais que vous obtenez ce message d'erreur, merci de contacter le webmaster.";
        } else {
            echo "Merci de votre inscription !<br/>";
            echo "Vous vous &ecirc;tes inscrit avec l'adresse : ".$current_account->email;
            echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
            echo "<fieldset><legend>".$input_formtitle."</legend>";
            echo "<table>";
            echo "<tr><td>Identifiant</td><td>:</td><td><input type='text' name='sub_login'></td></tr>";
            echo "<tr><td>Mot de passe</td><td>:</td><td><input type='password' name='sub_password'></td></tr>";
            echo "<tr><td>Confirmez votre mot de passe</td><td>:</td><td><input type='password' name='sub_password_verif'></td></tr>";
            echo "<tr><td colspan=3><center><input type='submit' name='submit' value='Envoyer'></center></td></tr>";
            echo "</table>";
            echo "</fieldset>";
            echo "</form>";
            if (isset($_POST['submit'])) {
                $current_account->login = $_POST['sub_login'];
                $temp_account = self::retrieve_from_login($_POST['sub_login'], $input_dbname);
                if ($temp_account->id != 0) {
                    echo "<b>Ce nom d'utilisateur existe d&eacute;j&agrave;, merci d'en choisir un autre !</b>";
                } else {
                    if ($_POST['sub_password'] === '') {
                        echo "<b>Vous n'avez pas choisi de mot de passe !</b>";
                    } else {
                        if ($_POST['sub_password'] != $_POST['sub_password_verif']) {
                            echo "<b>Mot de passe erron&eacute; ! Merci d'enter &agrave; nouveau votre mot de passe.</b>";
                        } else {
                            $current_account->activation_key = "";
                            $current_account->update($input_dbname);
                            $new_password = $_POST['sub_password'];
                            $current_account->store_password($new_password, $input_dbname);
                            // Send confirmation mail
                            $root = ($_SERVER['HTTPS'] ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/';
                            $receiver = $current_account->email;
                            $subject = "[Annuaire AUDDAS]". // should be retrieve from configuration_db...
                                " Confirmation de votre inscription";
                            $message = "<p>Bonjour !</p>
        
                        <p>Nous vous confirmons la cr&eacute;ation de votre compte sur l'annuaire d'AUDDAS, avec l'identifiant : ".$current_account->login."<br/>
                        Vous seul connaissez votre mot de passe.</p>

                        <p>Vous pouvez d&eacute;sormais vous connecter &agrave; l'annuaire &agrave; l'addresse suivante : <a href='".$root."annuaire/'>".$root."annuaire/<a/></P>
                        
                        <p>Merci de votre int&eacute;r&ecirc;t pour AUDDAS !</p>
                        
                        <p>Si ce mail ne vous &eacute;tait pas destin&eacute;, merci de l'ignorer.</p>";
                            $headers = "From: contact@auddas.fr"."\r\n".
                                "Content-type: text/html; charset=utf-8"."\r\n".
                                "Reply-to: contact@auddas.fr"."\r\n".
                                "Return-Path: contact@auddas.fr"."\r\n".
                                "X-mailer: PHP/".phpversion();
                            mail($receiver, $subject, $message, $headers);

                            echo "<meta http-equiv='refresh' content='0;url=./index.php?page=confirm_validation'>";
                        }
                    }
                }
            }
        }
    } else {
        echo "<b>Il n'y a pas de clef d'activation !</b> Si vous aviez bien re&ccedil;u un mail de validation de votre inscription, merci de contacter le webmaster.";
    }
}
//--------------------------------------------------------------------------- //



//--------------------------------- LOGIN ----------------------------------- //
// Log-in
public static function login_form($input_formtitle = self::default_login_formtitle, $input_dbname = self::default_database)
{
    echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
    echo "<fieldset><legend>".$input_formtitle."</legend>";
    echo "<table>";
    echo "<tr><td>Identifiant</td><td>:</td><td><input type='text' name='sub_login'></td></tr>";
    echo "<tr><td>Mot de passe</td><td>:</td><td><input type='password' name='sub_password'></td></tr>";
    echo "<tr><td colspan=3><center><a href=\"index.php?page=forgotten\">Mot de passe oubli&eacute; ?</a> | <input type='submit' name='submit' value='Envoyer'></center></td></tr>";
    echo "</table>";
    echo "</fieldset>";
    echo "</form>";
    if (isset($_POST['submit'])) {
        $current_account = self::retrieve_from_login($_POST['sub_login'], $input_dbname);        
        if ($current_account->id == 0) {
            echo "<b>Identifiant erron&eacute; !</b>";
        } else {
            $authorized = self::check_password($_POST['sub_password'], $current_account->salt, $current_account->password);
            if ($authorized == 1) {
                $current_account->last_connection = new DateTime();
                $current_account->update($input_dbname);
                $_SESSION['id'] = $current_account->id;
                $_SESSION['login'] = $current_account->login;
                $_SESSION['privilege'] = $current_account->privilege;
                echo "<meta http-equiv='refresh' content='0;url=./index.php'>";
            } else {
                echo "<b>Mot de passe erron&eacute; !</b>";
            }
        }
    }
}

// Log-out
public static function logout() {
    $_SESSION = array();
    session_destroy();
    echo "<meta http-equiv='refresh' content='0;url=./index.php'>";
}
//--------------------------------------------------------------------------- //



//--------------------------- ACCOUNT MANAGEMENT ----------------------------- //
// Manages user account
public static function management_form($input_id, $input_formtitle = self::default_manage_formtitle, $input_dbname = self::default_database)
{
    $current_account = self::retrieve($input_id, $input_dbname);
    echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
    echo "<fieldset><legend>".$input_formtitle."</legend>";
    echo "<table>";
    echo "<tr><td>Adresse e-mail</td><td>:</td><td><input type='text' name='sub_email', value='".$current_account->email."'></td></tr>";
    echo "<tr><td>Ancien mot de passe</td><td>:</td><td><input type='password' name='sub_old_password'></td></tr>";
    echo "<tr><td>Nouveau mot de passe</td><td>:</td><td><input type='password' name='sub_new_password'></td></tr>";
    echo "<tr><td>Confirmez votre nouveau mot de passe</td><td>:</td><td><input type='password' name='sub_new_password_verif'></td></tr>";
    echo "<tr><td colspan=3><center><input type='submit' name='submit' value='Envoyer'></center></td></tr>";
    echo "</table>";
    echo "</fieldset>";
    echo "</form>";
    if (isset($_POST['submit'])) {
        $current_account->email = $_POST['sub_email'];
        $current_account->update($input_dbname);
        if ($_POST['sub_new_password'] != '') {
            $check_old = self::check_password($_POST['sub_old_password'], $current_account->salt, $current_account->password);
            if ($check_old == 1) {
                if ($_POST['sub_new_password'] != $_POST['sub_new_password_verif']) {
                    echo "<b>Mot de passe erron&eacute; ! Merci d'enter &agrave; nouveau votre mot de passe.</b>";
                } else {
                    $new_password = $_POST['sub_new_password'];
                    $current_account->store_password($new_password, $input_dbname);
                    echo "<meta http-equiv='refresh' content='0;url=./index.php?page=updated'>";
                }
            } else {
                echo "<b>Votre mot de passe initial est erron&eacute; !</b>";
            }
        }
    }
}

// Resets password if forgotten 
public static function reset_password($input_formtitle = self::default_reset_formtitle, $input_dbname = self::default_database, $input_conf_dbname = self::default_configuration_database)
{
    // Generates form
    echo "Vous vous appr&ecirc;ter &agrave; r&eacute;initialiser votre mot de passe ; un e-mail vous sera envoyer pour proc&eacute;der &agrave; la r&eacute;initialisation.";
    echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
    echo "<fieldset><legend>".$input_formtitle."</legend>";
    echo "<table>";
    echo "<tr><td>Adresse mail</td><td>:</td><td><input type='text' name='sub_email' placeholder='albert.einstein@princeton.edu'></td></tr>";    
    echo "<tr><td colspan=3><center><input type='submit' name='submit' value='Envoyer'></center></td></tr>";
    echo "</table>";
    echo "</fieldset>";
    echo "</form>";
    if (isset($_POST['submit'])) {
        $current_account = self::retrieve_from_email($_POST['sub_email'], $input_dbname);
        if ($current_account->email == "") {
            echo "<b>Cette adresse mail n'est pas dans notre base de donn&eacute;es !</b>";
        } else {
            // Generates activation key
            $current_account->activation_key = mt_rand().mt_rand().mt_rand().mt_rand().mt_rand();
            $current_account->update($input_dbname);
            // Send verification mail
            $root = ($_SERVER['HTTPS'] ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/';
            $receiver = $current_account->email;
            $subject = "[Annuaire AUDDAS]". // should be retrieve from configuration_db...
                " R&eacute;initialisation de votre mot de passe";
            $message = "<p>Bonjour,</p>
    
        <p>Vous, ou quelqu'un utilisant votre adresse e-mail, a demand&eacute; une r&eacute;initialisation de votre mot de passe. Pour confirmer cette r&eacute;initialisation, merci de cliquer sur le lien suivant :<br/>
        <a href='".$root."annuaire/index.php?page=change_password&activation_key=".$current_account->activation_key."'>".$root."annuaire/index.php?page=change_password&activation_key=".$current_account->activation_key."</a><br/>
        
        <p>Si ce mail ne vous &eacute;tait pas destin&eacute;, merci de l'ignorer.</p>";
            $headers = "From: contact@auddas.fr"."\r\n".
                "Content-type: text/html; charset=utf-8"."\r\n".
                "Reply-to: contact@auddas.fr"."\r\n".
                "Return-Path: contact@auddas.fr"."\r\n".
                "X-mailer: PHP/".phpversion();
            mail($receiver, $subject, $message, $headers);
            echo "<meta http-equiv='refresh' content='0;url=./index.php?page=confirm_password'>";
        }
    }
}

// Validate a new password
public static function validate_password($input_formtitle = self::default_valid_formtitle, $input_dbname = self::default_database)
{
    $current_account = new Account();
    if ($_GET['activation_key']) {
        $current_account = self::retrieve_from_key($_GET['activation_key'], $input_dbname);
        if (!$current_account->id) {
            echo "<b>Attention :</b> la cl&eacute; d'activation que vous avez fournie est invalide. <br/>
Si vous n'avez pas encore activ&eacute; votre compte mais que vous obtenez ce message d'erreur, merci de contacter le webmaster.";
        } else {
            echo "Vous allez changer le mot de passe correspondant &agrave; l'adresse : ".$current_account->email;
            echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
            echo "<fieldset><legend>".$input_formtitle."</legend>";
            echo "<table>";
            echo "<tr><td>Mot de passe</td><td>:</td><td><input type='password' name='sub_password'></td></tr>";
            echo "<tr><td>Confirmez votre mot de passe</td><td>:</td><td><input type='password' name='sub_password_verif'></td></tr>";
            echo "<tr><td colspan=3><center><input type='submit' name='submit' value='Envoyer'></center></td></tr>";
            echo "</table>";
            echo "</fieldset>";
            echo "</form>";
            if (isset($_POST['submit'])) {
                if ($_POST['sub_password'] === '') {
                    echo "<b>Vous n'avez pas choisi de mot de passe !</b>";
                } else {
                    if ($_POST['sub_password'] != $_POST['sub_password_verif']) {
                        echo "<b>Mot de passe erron&eacute; ! Merci d'enter &agrave; nouveau votre mot de passe.</b>";
                    } else {
                        $current_account->activation_key = "";
                        $current_account->update($input_dbname);
                        $new_password = $_POST['sub_password'];
                        $current_account->store_password($new_password, $input_dbname);
                        echo "<meta http-equiv='refresh' content='0;url=./index.php?page=updated'>";
                    }
                }
            }
        }
    } else {
        echo "<b>Il n'y a pas de clef d'activation !</b> Si vous aviez bien re&ccedil;u un mail de r&eacute;initialisation de votre mot de passe, merci de contacter le webmaster.";
    }
}
//--------------------------------------------------------------------------- //



//---------------------------- ADIMINISTRATION ------------------------------ //

// Returns an array of the jobs of the user
public function get_jobs($input_jobs_database = self::default_jobs_database)
{
    include("opendb.php");
    $jobs_array = array();
    $count = 0;
    $query = "SELECT * FROM {$input_jobs_database} 
              WHERE id_user = {$this->id} 
              ORDER BY date_begin DESC";
    $results = mysql_query($query);
    $i = 0;
    if ($results) {
        $count = mysql_num_rows($results);
        if ($count >= 1) {
            while ($row = mysql_fetch_array($results)) {
                array_push($jobs_array, Job::retrieve($row["id"], $input_jobs_database));
            }
        } else {
            echo mysql_error();
        }
    }
    include("closedb.php");
    return $jobs_array;
}

// Returns an array of the theses of the user
public function get_theses($input_theses_database = self::default_theses_database)
{
    include("opendb.php");
    $theses_array = array();
    $count = 0;
    $query = "SELECT * FROM {$input_theses_database} 
              WHERE id_user = {$this->id} 
              ORDER BY date_begin DESC";
    $results = mysql_query($query);
    $i = 0;
    if ($results) {
        $count = mysql_num_rows($results);
        if ($count >= 1) {
            while ($row = mysql_fetch_array($results)) {
                array_push($theses_array, Thesis::retrieve($row["id"], $input_theses_database));
            }
        } else {
            echo mysql_error();
        }
    }
    include("closedb.php");
    return $theses_array;
}


// Delete an entire user (with its account, user profile, jobs and theses) of the database
public static function delete_whole_user($input_id, $input_dbname = self::default_database, $input_users_database = self::default_users_database, $input_jobs_database = self::default_jobs_database, $input_theses_database = self::default_theses_database)
{
    $current_account = self::retrieve($input_id, $input_dbname);
    $current_user = User::retrieve($input_id, $input_users_database);
    $jobs_array = $current_account->get_jobs($input_jobs_database);
    $theses_array = $current_account->get_theses($input_theses_database);
    // Delete jobs
    foreach ($jobs_array as $job) {
        $job->delete($input_jobs_database);
    }
    // Delete theses
    foreach ($theses_array as $thesis) {
        $thesis->delete($input_theses_database);
    }
    // Delete user profile
    if ((bool)$current_user) $current_user->delete($input_users_database);
    // Delete account
    $current_account->delete($input_dbname);
}

// Lists all accounts of the database
public static function list_all_accounts($input_dbname = self::default_database)
{
    $default_date = new DateTime(self::default_date);
    $now = new DateTime();
    $now = strtotime($now->format('c'));
    include("opendb.php");
    $user = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    echo "Il y a actuellement ".$count." adh&eacute;rents inscrits dans notre annuaire.<br/>
    Pour les c&ocirc;tisations, voici le code couleur sur les ID :
    <ul>
    <li><font color='yellow'>jaune</font> : l'adh&eacute;sion expire dans un mois ;</li>
    <li><font color='orange'>orange</font> : l'adh&eacute;sion expire dans 15 jours ;</li>
    <li><font color='red'>rouge</font> : l'adh&eacute;sion a expir&eacute;e.</li>
    </ul>";
    echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
    echo "<table>";
    echo "<tr><th>ID #</th><th>e-mail</th><th>login</th><th>rang</th><th>date de c&ocirc;tisation</th><th>nettoyage</th></tr>";
    $ranks = array("inscrit", "adh&eacute;rent", "administrateur");
    $rows = array();
    while ($row = mysql_fetch_array($results)) {
        array_push($rows, $row);
        $sub_date = new DateTime($row['subscription_date']);
        $one_month = clone $sub_date;
        $one_month->modify('+11 month');
        $one_month = strtotime($one_month->format('c'));   
        $fortnight = clone $sub_date;
        $fortnight->modify('+11 month +15 day');
        $fortnight = strtotime($fortnight->format('c'));
        $today = clone $sub_date;
        $today->modify('+1 year');
        $today = strtotime($today->format('c'));
        $color = "<font>";
        if (($one_month - $now) < (60*60*24*30)) {
            $color = "<font color='yellow'>";
        } 
        if (($fortnight - $now) < (60*60*24*15)) {
            $color = "<font color='orange'>";
        }
        if (($today - $now) < (60*60*24)) {
            $color = "<font color='red'>";
        }
        if ($sub_date == $default_date) {
            $color = "<font color='black'>";
        }
        echo "<tr><td>".$color.$row['id']."</font></td><td><a href=\"./index.php?page=list_complete_profile&user=".$row['id']."\">".$row['email']."</a></td><td>".$row['login']."</td><td>
        <select name='sub_rank_".$row['id']."'>";
        for ($i = 0; $i <= 2; $i++) {
            echo "<option value='".$i."' ".($row['privilege'] == $i ? "selected='selected'" : "").">".$ranks[$i]."</option>";
        }
        $sub_date = $sub_date->format('Y-m-d');
        echo "</td><td><input type='text' class='datepicker' id=datepicker_".$row['id']." name='sub_date_".$row['id']."' value='".($sub_date === $default_date->format('Y-m-d') ? "" : $sub_date)."'></td>";
        echo "<td><input type='submit' name='delete_".$row['id']."' value='supprimer cet utilisateur'></td></tr>";
    }
    echo "<tr><td colspan=6><center><input type='submit' name='submit' value='Enregistrer'></center></td></tr>";
    echo "</table>";
    echo "</form>";
    include("closedb.php");
    if (isset($_POST['submit'])) {
        $i = 0;
        while ($i < $count) {
            $current_id = $rows[$i]['id'];
            $current_account = new Account();
            $current_account = self::retrieve($current_id, $input_dbname);
            $current_account->privilege = $_POST['sub_rank_'.$current_id];
            $current_account->subscription_date = new DateTime(($_POST['sub_date_'.$current_id] == "" ? self::default_date : $_POST['sub_date_'.$current_id]));
            $current_account->update($input_dbname);
            $i++;
        }
    } else {
        $i = 0;
        while ($i < $count) {
            if (isset($_POST['delete_'.$i])) {
                echo "<meta http-equiv='refresh' content='0;url=./index.php?page=delete_account&id=".$i."'>";
            }
            $i++;
        }
    }
}

// Check subscription date
public static function check_subscription_date($input_id, $input_dbname = self::default_database)
{
    $now = new DateTime();
    $now = $now->format('Y-m-d');
    $current_account = self::retrieve($input_id);
    $one_month = clone $current_account->subscription_date;
    $one_month->modify('+11 month');
    $one_month = $one_month->format('Y-m-d');   
    $fortnight = clone $current_account->subscription_date;
    $fortnight->modify('+11 month +15 day');
    $fortnight = $fortnight->format('Y-m-d');
    $today = clone $current_account->subscription_date;
    $today->modify('+1 year');
    $today = $today->format('Y-m-d');
    if($now == $one_month || $now == $fortnight || $now == $today) {
        echo $current_account->login." va recevoir un mail de rappel\n";
        if ($now == $one_month) {
            $last_date = "dans un mois";
        } else if ($now == $fortnight) {
            $last_date = "dans deux semaines";
        } else if ($now == $today) {
            $last_date = "aujourd'hui";
            }
        // Send reminder mail
        $root = ($_SERVER['HTTPS'] ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/';
        $receiver = $current_account->email;
        $subject = "[Annuaire AUDDAS]". // should be retrieve from configuration_db...
            " Votre cotisation expire bientot ! ";
        $message = "<p>Bonjour,</p>
    
        <p>Vous recevez ce message car vous &ecirc;tes inscrit avec cette adresse mail (et avec l'identifiant ".$current_account->login.") dans l'annuaire d'AUDDAS.
        Votre c&ocirc;tisation expire ".$last_date." ; si vous voulez continuer de profiter de tous les services de l'annuaire d'AUDDAS, nous vous invitons &acirc; r&eacute;adh&eacute;rer rapidement !<br/>
       Retrouver l'annuaire d'AUDDAS &acirc; cette <a href='".$root."annuaire/index.php'>addresse</a>.</p>

        <p>Amicalement,<br/>
        Le webmaster de l'annuaire d'AUDDAS</p>
       
        Si ce mail ne vous &eacute;tait pas destin&eacute;, merci de l'ignorer.</p>";
        $headers = "From: contact@auddas.fr"."\r\n".
            "Content-type: text/html; charset=utf-8"."\r\n".
            "Reply-to: contact@auddas.fr"."\r\n".
            "Return-Path: contact@auddas.fr"."\r\n".
            "X-mailer: PHP/".phpversion();
        mail($receiver, $subject, $message, $headers);

        // Send admin email
        $receiver = "webmaster@auddas.fr";
        $subject = "[Annuaire AUDDAS]". // should be retrieve from configuration_db...
            " Cotisation a expiration";
        $message = "<p>Bonjour,</p>
    
        <p>Une personne avec les informations suivantes :<br/>
        &nbsp;&nbsp;&nbsp; login : ".$current_account->login."<br/>
        &nbsp;&nbsp;&nbsp; adresse : ".$current_account->email."<br/>
        a sa c&ocirc;tisation qui expire ".$last_date."</p>";
        $headers = "From: contact@auddas.fr"."\r\n".
            "Content-type: text/html; charset=utf-8"."\r\n".
            "Reply-to: contact@auddas.fr"."\r\n".
            "Return-Path: contact@auddas.fr"."\r\n".
            "X-mailer: PHP/".phpversion();
        mail($receiver, $subject, $message, $headers);
    }
}

// Check subscription date for all users
public static function check_subscription_all($input_dbname = self::default_database)
{
    include("opendb.php");
    $user = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    $rows = array();
    while ($row = mysql_fetch_array($results)) {
        array_push($rows, $row);
    }
    $i = 0;
    while ($i < $count) {
        $current_id = $rows[$i]['id'];
        Account::check_subscription_date($current_id, $input_dbname);
        $i++;
    }
    include("closedb.php");
}
//--------------------------------------------------------------------------- //



/*////////////////////////////////////////////////////////////////////////////*/
} ?>
