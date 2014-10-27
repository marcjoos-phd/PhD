<?php
/* ********************************** USER ********************************** */
/*////////////////////////////////////////////////////////////////////////////*/
// PROJECT:        AUDDAS Directory 
// TITLE:          User 
// DESCRIPTION:    Class representing a user of the auddas directory
// AUTHORS:        Marc Joos & Vincent Reverdy 
// DATE:           2013-2014
// LICENSE:        GNU GPL version 3 
/*////////////////////////////////////////////////////////////////////////////*/
include_once('auddas_job.php');
class User {
/*////////////////////////////////////////////////////////////////////////////*/



//--------------------------------- CONSTANTS ------------------------------- //
const default_database = "auddas_users";
const default_fields_database = "auddas_user_fields";
const default_jobs_database = "auddas_jobs";
const default_theses_database = "auddas_theses";
const default_users_database = "auddas_accounts";
const default_formtitle = "Profil";
const default_displaytitle = "Profil";
const default_date = "0000-00-00";
//--------------------------------------------------------------------------- //



//-------------------------------- ATTRIBUTES ------------------------------- //
var $id;
var $name;
var $firstname;
var $mail_address;
var $phone_perso;
var $phone_pro;
var $phone_mobile;
var $address_perso;
var $city_perso;
var $country_perso;
var $birth_date;
var $sex;
var $nationality;
var $webpage;
var $linkedin;
var $viadeo;
var $mailinglist;
//--------------------------------------------------------------------------- //



//--------------------------------- LIFECYCLE ------------------------------- //
// Constructs the user from its properties
public function __construct($input_id = 0,
                            $input_name = "",
                            $input_firstname = "",
                            $input_mail_address = "",
                            $input_phone_perso = "",
                            $input_phone_pro = "",
                            $input_phone_mobile = "",
                            $input_address_perso = "",
                            $input_city_perso = "",
                            $input_country_perso = "",
                            $input_birth_date = NULL,
                            $input_sex = "F",
                            $input_nationality = "",
                            $input_webpage = "",
                            $input_linkedin = "",
                            $input_viadeo = "",
                            $input_mailinglist = 0) 
{
    if ($input_birth_date === NULL) $input_birth_date = new DateTime();
    $this->id = $input_id;
    $this->name = $input_name;
    $this->firstname = $input_firstname;
    $this->mail_address = $input_mail_address;
    $this->phone_perso = $input_phone_perso;
    $this->phone_pro = $input_phone_pro;
    $this->phone_mobile = $input_phone_mobile;
    $this->address_perso = $input_address_perso;
    $this->city_perso = $input_city_perso;
    $this->country_perso = $input_country_perso;
    $this->birth_date = $input_birth_date;
    $this->sex = $input_sex;
    $this->nationality = $input_nationality;
    $this->webpage = $input_webpage;
    $this->linkedin = $input_linkedin;
    $this->viadeo = $input_viadeo;
    $this->mailinglist = $input_mailinglist;
}
//--------------------------------------------------------------------------- //



//-------------------------------- MANAGEMENT ------------------------------- //
// Basic printing of the user contents
public function display($prefix = "[", $suffix = "]\n")
{
    return "{$prefix}id = {$this->id}, 
                     name = {$this->name},
                     firstname = {$this->firstname},
                     mail_address = {$this->mail_address},
                     phone_perso = {$this->phone_perso},
                     phone_pro = {$this->phone_pro},
                     phone_mobile = {$this->phone_mobile},
                     address_perso = {$this->address_perso},
                     city_perso = {$this->city_perso},
                     country_perso = {$this->country_perso},
                     birth_date = {$this->birth_date->format('c')},
                     sex = {$this->sex},
                     nationality = {$this->nationality},
                     webpage = {$this->webpage},
                     linkedin = {$this->linkedin},
                     viadeo = {$this->viadeo},
                     mailinglist = {$this->mailinglist}";
}

// Creates a user in the database
public function create($input_dbname = self::default_database)
{
    include("opendb.php");
    $query = "INSERT INTO {$input_dbname}
              (id, name, firstname, mail_address, phone_perso, phone_pro, phone_mobile, address_perso, city_perso, country_perso, birth_date, sex, nationality, webpage, linkedin, viadeo, mailinglist)
              VALUES ('{$this->id}', 
                      '{$this->name}',
                      '{$this->firstname}',
                      '{$this->mail_address}',
                      '{$this->phone_perso}',
                      '{$this->phone_pro}',
                      '{$this->phone_mobile}',
                      '{$this->address_perso}',
                      '{$this->city_perso}',
                      '{$this->country_perso}',
                      '{$this->birth_date->format('c')}',
                      '{$this->sex}',
                      '{$this->nationality}',
                      '{$this->webpage}',
                      '{$this->linkedin}',
                      '{$this->viadeo}',
                      '{$this->mailinglist}')";
    $results = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
}

// Deletes a user in the database
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

// Retrieves a user with its id
public function retrieve($input_id, $input_dbname = self::default_database)
{
    include("opendb.php");
    $user = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$input_id} 
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    if ($count === 1) {
        while ($row = mysql_fetch_array($results)) {
            $user_birth_date = new DateTime($row["birth_date"]);
            $user = new User($row["id"], 
                      $row["name"],
                      $row["firstname"],
                      $row["mail_address"],
                      $row["phone_perso"],
                      $row["phone_pro"],
                      $row["phone_mobile"],
                      $row["address_perso"],
                      $row["city_perso"],
                      $row["country_perso"],
                      $user_birth_date,
                      $row["sex"],
                      $row["nationality"],
                      $row["webpage"],
                      $row["linkedin"],
                      $row["viadeo"],
                      $row["mailinglist"]);
        }
    } else {
        echo mysql_error();
    }
    include("closedb.php");
    return $user;
}

// Updates a user in the database
public function update($input_dbname = self::default_database)
{
    include("opendb.php");
    $query = "UPDATE {$input_dbname}
              SET name='{$this->name}',
                  firstname='{$this->firstname}',
                  mail_address='{$this->mail_address}',
                  phone_perso='{$this->phone_perso}',
                  phone_pro='{$this->phone_pro}',
                  phone_mobile='{$this->phone_mobile}',
                  address_perso='{$this->address_perso}',
                  city_perso='{$this->city_perso}',
                  country_perso='{$this->country_perso}',
                  birth_date='{$this->birth_date->format('c')}',
                  sex='{$this->sex}',
                  nationality='{$this->nationality}',
                  webpage='{$this->webpage}',
                  linkedin='{$this->linkedin}',
                  viadeo='{$this->viadeo}',
                  mailinglist='{$this->mailinglist}'
              WHERE 
                  id='{$this->id}'";
    $results = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
}

public static function get_fields($input_dbname = self::default_fields_database)
{
    include("opendb.php");
    $query = "SELECT * FROM {$input_dbname}";
    $results = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
    $fields = array();
    while ($row = mysql_fetch_array($results)) {
        $fields[$row['field_name']] = $row;
    }
    return $fields;
}

public function produce_display_name($field, $fieldposted, $isposted)
{
    $name = $field['display_name'];
    if ($field['required'] == 1) {
        $name = $name."<font color='red'><b>*</b></font>";
        if ($isposted) {
            if ($fieldposted == "") {
                $name = "<font color='red'><i>".$name."</i></font>";
            }
        }
    }
    if ($field['other_viewable'] == 0) {
        $name = "<font color='grey'>".$name."</font>";
    }
    return $name;
}

public function produce_field($user, $field, $fieldposted, $isposted, $id_dpick=0)
{
    $fieldtype = $field['input_type'];
    $fieldname = $field['field_name'];
    $placeholder = $field['placeholder'];
    if ($fieldtype == 'text') {
        $displayfield = stripslashes(htmlspecialchars($isposted ? $fieldposted : $user->$fieldname, ENT_QUOTES));
        $input_field = "<input type='text' name='sub_".$fieldname."' placeholder='".$placeholder."' value='".$displayfield."'>";
    } else if ($fieldtype == 'textarea') {
        $displayfield = stripslashes(htmlspecialchars($isposted ? $fieldposted : $user->$fieldname));
        $input_field = "<textarea rows='6' cols='40' name='sub_".$fieldname."' placeholder='".$placeholder."'>".$displayfield."</textarea>";
    } else if ($fieldtype == 'radio') {
        if ($fieldname == 'mailinglist') {
            $displayfield = ($isposted ? $fieldposted : ($user->$fieldname ? 'true' : 'false'));
            $input_field = "<input type='radio' name='sub_mailinglist' value='true' ".($displayfield === "true" ? "checked" : "").">oui <input type='radio' name='sub_mailinglist' value='false' ".($displayfield === "false" ? "checked" : "").">non";
        } else if ($fieldname == "sex") {
            $displayfield = ($isposted ? $fieldposted : $user->$fieldname);
            $input_field = "<input type='radio' name='sub_sex' value='F' ".($displayfield === "F" ? "checked" : "").">F <input type='radio' name='sub_sex' value='M' ".($displayfield === "M" ? "checked" : "").">M";
        }
    } else if ($fieldtype == 'date') {
        $default_date = new DateTime(self::default_date);
        $originalfield = ($user->$fieldname != NULL ? ($user->$fieldname->format('Y-m-d') === $default_date->format('Y-m-d') ? "" : $user->$fieldname->format('Y-m-d')) : "");
        $displayfield = ($isposted ? $fieldposted : $originalfield);
        $input_field = "<input type='text' class='datepicker' id=datepicker".strval($id_dpick)." name='sub_".$fieldname."' placeholder='".$placeholder."' value='".$displayfield."'>";
    } else if ($fieldtype == 'weblink') {
        if ($fieldname == "linkedin") {
            $displayfield = ($isposted ? $fieldposted : ($user->$fieldname == "" ? "http://" : $user->$fieldname));        
        } else {
            $displayfield = ($isposted ? $fieldposted : $user->$fieldname);
        }
        $input_field = "<input type='text' name='sub_".$fieldname."' placeholder='".$placeholder."' value='".$displayfield."'>";
    }
    return $input_field;
}

// Creates form for user profile -- alternative version
public static function produce_form($input_id, $input_formtitle = self::default_formtitle, $input_dbname = self::default_database, $input_fields_dbname = self::default_fields_database)
{
    $edit = (($_GET['user'] === $_SESSION['id']) | ($_SESSION['privilege'] == 2));
    if ($edit) {
       $current_user = ($input_id != 0) ? self::retrieve($input_id, $input_dbname) : new User();
        if ($current_user->id != $input_id) {
            $current_user = new User();
            $input_id = 0;
        }
       $fields = User::get_fields($input_fields_dbname);
       echo "<i>Les champs suivis d'une ast&eacute;risque <font color='red'><b>*</b></font> sont obligatoires.<br/>
       Les champs gris&eacute;s ne sont pas visibles par les autres membres de l'annuaire.</i>";
       echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
       echo "<fieldset><legend>".$input_formtitle."</legend>";
       echo "<table>";
       $id_dpick = 1;
       foreach ($fields as $field) {
            $disp_name = User::produce_display_name($field, $_POST['sub_'.$field['field_name']], isset($_POST['submit']));
            $disp_field = User::produce_field($current_user, $field, $_POST['sub_'.$field['field_name']], isset($_POST['submit']), $id_dpick);
            if ($field['field_name'] != 'id') {
                echo "<tr><td>".$disp_name."</td><td>:</td><td>".$disp_field."</td></tr>";
                if ($field['input_type'] == 'date') $_id_dpick += 1;
            }
       }
       echo "<tr><td colspan=3><center><input type='submit' name='submit' value='Enregistrer'></center></td></tr>";
       echo "</table>";
       echo "</fieldset>";
       echo "</form>";
       if (isset($_POST['submit'])) {
           foreach ($fields as $field) {
               //echo "post[".$field['field_name']."]: ".$_POST['sub_'.$field['field_name']]."<br/>";
               if ($field['required'] == 1 && $_POST['sub_'.$field['field_name']] == "") {
                    echo "<b>Attention :</b> tous les champs obligatoires ne sont pas remplis !";
                    return;
               }
               if ($field['field_name'] == 'id') {
                   $current_user->id = $_GET['user'];
               } else if ($field['input_type'] == 'date') {
                   $current_user->$field['field_name'] = new DateTime($_POST['sub_'.$field['field_name']]);
               } else if ($field['field_name'] == 'mailinglist') {
                   $current_user->mailinglist = $_POST['sub_mailinglist'] == 'true' ? true : false;
               } else if ($field['field_name'] == 'linkedin') {
                   if ($_POST['sub_linkedin'] != 'http://') {
                       $current_user->linkedin = $_POST['sub_linkedin'];
                   } else {
                       $current_user->linkedin = "";
                   }
               } else {
                   $current_user->$field['field_name'] = $_POST['sub_'.$field['field_name']];
               }
           }
            echo $input_id;
           if ($input_id != 0) {
               $current_user->update($input_dbname);
           } else {
               $current_user->create($input_dbname);
           }     
           echo "<meta http-equiv='refresh' content='0;url=./index.php?page=list_profile&user=".$_GET['user']."'>";
       }
    } else {
        echo "Vous n'avez pas les droits n&eacute;cessaires pour acc&eacute;der &agrave; cette page.";
    }
}

// Creates a box to display information on a given user
public static function produce_display($input_id, $input_edit = FALSE, $input_displaytitle = self::default_displaytitle, $input_dbname = self::default_database, $input_fields_dbname = self::default_fields_database)
{
    // if ($_GET['user'] != $_SESSION['id'] && $_SESSION['privilege'] < 1) {
    //     echo "Vous devez &ecirc;tre &agrave; jour de votre c&ocirc;tisation pour acc&eacute;der &agrave; cette page.";
    // } else {
    $default_date = new DateTime(self::default_date);
    $current_user = ($input_id != 0) ? self::retrieve($input_id, $input_dbname) : new User();
    $fields = User::get_fields($input_fields_dbname);
    if ($_POST['modify'] && $_GET['user']) {
         echo "<meta http-equiv='refresh' content='0;url=index.php?page=edit_profile&user=".$_GET['user']."'>";
     } else {
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query, $params);
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?' . http_build_query($params);

        echo "<form method='POST' action='".htmlspecialchars($url, ENT_QUOTES)."'>";
        echo "<fieldset><legend>".$input_displaytitle."</legend>";  
        echo "<table>";
        foreach ($fields as $field) {
            if ($input_edit) {
                if (($_GET['user'] == $_SESSION['id'] && $field['user_viewable'] == 1) || ($_GET['user'] != $_SESSION['id'] && $field['other_viewable'] == 1) || $_SESSION['privilege'] == 2) {
                    if ($current_user->$field['field_name'] != "" && $field['input_type'] != 'weblink') {
                        if ($field['input_type'] == 'date') {
                            echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_user->$field['field_name']->format('Y-m-d')."</td></tr>";
                        } else if ($field['input_type'] == 'radio') {
                            if ($field['field_name'] == 'sex') {
                                echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_user->$field['field_name']."</td></tr>";
                            } else {
                                echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".($current_user->$field['field_name'] == 1 ? "Oui" : "Non")."</td></tr>";
                            }
                        } else if ($field['display_name'] != "") {
                            echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_user->$field['field_name']."</td></tr>";
                        }
                    }    
                }
            } else if ($field['other_viewable'] == 1) {
                if ($current_user->$field['field_name'] != "" && $field['input_type'] != 'weblink') {
                    if ($field['input_type'] == 'date') {
                        echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_user->$field['field_name']->format('Y-m-d')."</td></tr>";
                    } else if ($field['input_type'] == 'radio') {
                        if ($field['field_name'] == 'sex') {
                            echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_user->$field['field_name']."</td></tr>";
                        } else {
                            echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".("oui" ? $current_user->$field['field_name'] == 1 : "Non")."</td></tr>";
                        }
                    } else {
                        echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_user->$field['field_name']."</td></tr>";
                    }
                }    
            }
        }
        if ($current_user->webpage != "" || $current_user->linkedin != "" || $current_user->viadeo != "") {
            echo "<tr><td colspan=3><p align=center>".($current_user->webpage != "" ? "<a href='".$current_user->webpage."'>site perso</a>" : "")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".($current_user->linkedin != "" ? "<a href='".$current_user->linkedin."'><img src='./includes/img/linkedin.ico'></a>" : "")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".($current_user->viadeo != "" ? "<a href='".$current_user->viadeo."'><img src='./includes/img/viadeo.ico'></a>" : "")."</p></td></tr>";
        }
        if ($input_edit) {
            echo "<tr><td colspan=3><center><input type='submit' name='modify' value='Modifier'></center></td></tr>";
        }
        echo "</table>";
        echo "</fieldset>";
        echo "</form>";
    }
    // }
}

// Lists all users of the database
public static function list_all_users($input_dbname = self::default_database)
{
    include("opendb.php");
    $user = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              ORDER BY name ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    echo "Il y a actuellement ".$count." adh&eacute;rents inscrits dans notre annuaire.<br/>
    <ul>";
    while ($row = mysql_fetch_array($results)) {
        echo "<li><a href=\"./index.php?page=list_complete_profile&user=".$row['id']."\">".$row['name'].", ".$row['firstname']."</a></li>";    

    }
    echo "</ul>";
    include("closedb.php");
}
//--------------------------------------------------------------------------- //



//----------------------------------- JOBS ---------------------------------- //
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

// Produces a page with a list of jobs of a given user
public static function produce_jobs_list($input_id, $input_edit = FALSE, $input_database = self::default_database, $input_jobs_database = self::default_jobs_database, $input_users_database = self::default_users_database)
{
    if ($_GET['user'] == $_SESSION['id']) {
        echo "Vous visualisez votre propre parcours professionnel.";
    } else {
        include("opendb.php");
        $firstname = "";
        $name = "";
        $count = 0;
        $query = "SELECT * FROM {$input_database}
                  WHERE id = {$input_id}
                  ORDER BY id ASC";
        $results = mysql_query($query);
        if ($results) $count = mysql_num_rows($results);
        if ($count === 1) {
            while ($row = mysql_fetch_array($results)) {
                $firstname = $row["firstname"];
                $name = $row["name"];
            }
        } else {
            echo mysql_error();
        }
        include("closedb.php");
        echo "Vous visualisez le parcours professionnel de ".$firstname." ".$name.".";
    }
    $current_user = new User($input_id); // WARNING: the user should be taken from the database
    $jobs_array = $current_user->get_jobs($input_jobs_database);
    if ($_POST['add_job'] && ($_GET['job'] == 0)) {
        echo "<meta http-equiv='refresh' content='0;url=index.php?page=edit_job&user=".$input_id."&id=0'>";
    } else if ($_POST['modify'] && ($_GET['job'])) {
        echo "<meta http-equiv='refresh' content='0;url=index.php?page=edit_job&user=".$input_id."&id=".$_GET['job']."'>";
    } else {
        if ($input_edit) {
            echo "<form method='POST' class='job_display_class' action='".$_SERVER['REQUEST_URI']."&job=0'>";
            echo "<center><input type='submit' name='add_job' value='Ajouter une exp&eacute;rience'></center>";
            echo "</form>";
        }
        $i = 0;
        $n = count($jobs_array);
        while ($i < $n) {
            Job::produce_display($jobs_array[$i]->id, "Exp&eacute;rience professionnelle #".($n - $i)."", $input_edit, $input_jobs_database);
            $i += 1; 
        }
    }
}
//--------------------------------------------------------------------------- //



//-------------------------------- THESIS ----------------------------------- //
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

// Produces a page with a list of theses of a given user
public static function produce_theses_list($input_id, $input_edit = FALSE, $input_database = self::default_database, $input_theses_database = self::default_theses_database, $input_users_database = self::default_users_database)
{
    $current_user = new User($input_id); // WARNING: the user should be taken from the database
    $theses_array = $current_user->get_theses($input_theses_database);
    if ($_POST['add_thesis'] && ($_GET['thesis'] == 0)) {
        echo "<meta http-equiv='refresh' content='0;url=index.php?page=edit_thesis&user=".$input_id."&id=0'>";
    } else if ($_POST['modify'] && ($_GET['thesis'])) {
        echo "<meta http-equiv='refresh' content='0;url=index.php?page=edit_thesis&user=".$input_id."&id=".$_GET['thesis']."'>";
    } else {
        if ($input_edit) {
            echo "<form method='POST' class='thesis_display_class' action='".$_SERVER['REQUEST_URI']."&thesis=0'>";
            echo "<center><input type='submit' name='add_thesis' value='Ajouter une th&egrave;se'></center>";
            echo "</form>";
        }
        $i = 0;
        $n = count($theses_array);
        while ($i < $n) {
            Thesis::produce_display($theses_array[$i]->id, "Th&egrave;se #".($n - $i)."", $input_edit, $input_theses_database);
            $i += 1; 
        }
    }
}
//--------------------------------------------------------------------------- //



/*////////////////////////////////////////////////////////////////////////////*/
} ?>
