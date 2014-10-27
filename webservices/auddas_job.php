<?php
/* *********************************** JOB ********************************** */
/*////////////////////////////////////////////////////////////////////////////*/
// PROJECT:        AUDDAS Directory 
// TITLE:          Job 
// DESCRIPTION:    Class representing a job and its properties
// AUTHORS:        Marc Joos & Vincent Reverdy 
// DATE:           2013-2014
// LICENSE:        GNU GPL version 3 
/*////////////////////////////////////////////////////////////////////////////*/
include_once('auddas_category.php');
class Job {
/*////////////////////////////////////////////////////////////////////////////*/



//--------------------------------- CONSTANTS ------------------------------- //
const default_database = "auddas_jobs";
const default_fields_database = "auddas_job_fields";
const default_formtitle = "Exp&eacute;rience professionnelle";
const default_displaytitle = "Exp&eacute;rience professionnelle";
const default_date_end = "0000-00-00";
//--------------------------------------------------------------------------- //



//-------------------------------- ATTRIBUTES ------------------------------- //
var $id;
var $id_user;
var $id_category;
var $category;
var $title;
var $organization;
var $division;
var $city;
var $country;
var $contract;
var $date_begin;
var $date_end;
var $keywords;
var $description;
//--------------------------------------------------------------------------- //



//--------------------------------- LIFECYCLE ------------------------------- //
// Constructs the job from its properties
public function __construct($input_id = 0, 
                            $input_id_user = 0,
                            $input_id_category = NULL, 
                            $input_category = "",
                            $input_title = "",
                            $input_organization = "",
                            $input_division = "",
                            $input_city = "",
                            $input_country = "",
                            $input_contract = "", 
                            $input_date_begin = NULL,
                            $input_date_end = NULL,
                            $input_keywords = "",
                            $input_description = "") 
{
    if ($input_category === NULL) $input_category = new Category();
    if ($input_date_begin === NULL) $input_date_begin = new DateTime();
    if ($input_date_end === NULL) $input_date_end = new DateTime();
    $this->id = $input_id;
    $this->id_user = $input_id_user;
    $this->id_category = $input_id_category;
    $this->category = $input_category;
    $this->title = $input_title;
    $this->organization = $input_organization;
    $this->division = $input_division;
    $this->city = $input_city;
    $this->country = $input_country;
    $this->contract = $input_contract;
    $this->date_begin = $input_date_begin;
    $this->date_end = $input_date_end;
    $this->keywords = $input_keywords;
    $this->description = $input_description;
}
//--------------------------------------------------------------------------- //



//-------------------------------- MANAGEMENT ------------------------------- //
// Basic printing of the job contents
public function display($prefix = "[", $suffix = "]\n")
{
    return "{$prefix}id = {$this->id}, 
                     id_user = {$this->id_user},
                     id_category = {$this->id_category->display('[', ']')},
                     category = {$this->category},
                     title = {$this->title},
                     organization = {$this->organization},
                     division = {$this->division},
                     city = {$this->city},
                     country = {$this->country},
                     contract = {$this->contract},
                     date_begin = {$this->date_begin->format('c')},
                     date_end = {$this->date_end->format('c')},
                     keywords = {$this->keywords},
                     description = {$this->description}{$suffix}";
}

// Retrieves list of keywords from keywords attribute
public function get_keywords()
{
    $keywords_list = str_replace(";", ",", $this->keywords);
    return explode(",", $keywords_list);
}

// Gets time duration of a given job
public function get_duration()
{
    $today = new DateTime();
    $diff_time = array();
    $diff_today = array();
    $split_begin = explode('-', $this->date_begin->format('Y-m'));
    $split_end = explode('-', $this->date_end->format('Y-m'));
    $split_today = explode('-', $today->format('Y-m'));
    $diff_time[0] = $split_end[0] - $split_begin[0];
    $diff_time[1] = $split_end[1] - $split_begin[1];
    $diff_today[0] = $split_today[0] - $split_begin[0];
    $diff_today[1] = $split_today[1] - $split_begin[1];
    if ($diff_time[1] > 11) {
        $diff_time[0] += 1;
        $diff_time[1] += -12;
    }
    if ($diff_today[1] > 11) {
        $diff_today[0] += 1;
        $diff_today[1] += -12;
    }
    if ($diff_time[1] < 0) {
        $diff_time[0] += -1;
        $diff_time[1] += 12;
    }
    if ($diff_today[1] < 0) {
        $diff_today[0] += -1;
        $diff_today[1] += 12;
    }
    if ($diff_time[0] < 0 || (($diff_today[1] + 12*$diff_today[0]) < ($diff_time[1] + 12*$diff_time[0]))) $diff_time = $diff_today;
    return ($diff_time);
}

// Gets time duration of a given job in a text format
public function get_duration_text()
{
    $diff_time = $this->get_duration();
    $years = $diff_time[0];
    $months = $diff_time[1];
    return "".$years." an".($years > 1 ? "s" : "")."".($months > 0 ? " et ".$months." mois" : "");
}

// Creates a job in the database
public function create($input_dbname = self::default_database)
{
    include("opendb.php");
    $query = "INSERT INTO {$input_dbname} 
              (id, id_user, id_category, category, title, organization, division, city, country, contract, date_begin, date_end, keywords, description) 
              VALUES ('{$this->id}', 
                      '{$this->id_user}', 
                      '{$this->id_category->id}', 
                      '{$this->category}',
                      '{$this->title}',
                      '{$this->organization}',
                      '{$this->division}',
                      '{$this->city}',
                      '{$this->country}',
                      '{$this->contract}', 
                      '{$this->date_begin->format('c')}',
                      '{$this->date_end->format('c')}',
                      '{$this->keywords}',
                      '{$this->description}')";
    $results = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
}

// Deletes a job in the database
public function delete($input_dbname = self::default_database)
{
    include("opendb.php");
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$this->id} 
              ORDER BY id ASC";
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

// Retrieves a job with its id
public static function retrieve($input_id, $input_dbname = self::default_database)
{
    include("opendb.php");
    $job = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$input_id} 
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    if ($count === 1) {
        while ($row = mysql_fetch_array($results)) {
            $job_date_begin = new DateTime($row["date_begin"]);
            $job_date_end = new DateTime($row["date_end"]);
            $job_category = Category::retrieve($row["id_category"]);
            $job = new Job($row["id"],
                           $row["id_user"],
                           $job_category,
                           $row["category"],
                           $row["title"],
                           $row["organization"],
                           $row["division"],
                           $row["city"],
                           $row["country"],
                           $row["contract"],
                           $job_date_begin,
                           $job_date_end,
                           $row["keywords"],
                           $row["description"]);
        }
    } else {
        echo mysql_error();
    }
    include("closedb.php");
    return $job;
}

// Updates a job in the database
public function update($input_dbname = self::default_database)
{
    include("opendb.php");
    $query = "UPDATE {$input_dbname} 
            SET id_category='{$this->id_category->id}',
                category='{$this->category}',
                title='{$this->title}',
                organization='{$this->organization}',
                division='{$this->division}',
                city='{$this->city}',
                country='{$this->country}',
                contract='{$this->contract}',
                date_begin='{$this->date_begin->format('c')}',
                date_end='{$this->date_end->format('c')}',
                keywords='{$this->keywords}',
                description='{$this->description}'
            WHERE
                id='{$this->id}'";
    $results = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
}

public static function get_fields($input_dbname = self::default_fields_database)
{
    include("opendb.php");
    $query = "SELECT * FROM {$input_dbname} ORDER BY position";
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
    return $name;
}

public function produce_field($job, $field, $fieldposted, $isposted, $id_dpick=0)
{
    $fieldtype = $field['input_type'];
    $fieldname = $field['field_name'];
    $placeholder = $field['placeholder'];
    if ($fieldtype == 'text') {
        $displayfield = stripslashes(htmlspecialchars($isposted ? $fieldposted : $job->$fieldname, ENT_QUOTES));
        $input_field = "<input type='text' name='sub_".$fieldname."' placeholder='".$placeholder."' value='".$displayfield."'>";
    } else if ($fieldtype == 'textarea') {
        $displayfield = stripslashes(htmlspecialchars($isposted ? $fieldposted : $job->$fieldname));
        $input_field = "<textarea rows='6' cols='40' name='sub_".$fieldname."' placeholder='".$placeholder."'>".$displayfield."</textarea>";
    } else if ($fieldtype == 'radio') {
        $displayfield = ($isposted ? $fieldposted : $job->$fieldname);
        $input_field = "<input type='radio' name='sub_contract' value='CDD' ".($displayfield === "CDD" ? "checked" : "").">CDD <input type='radio' name='sub_contract' value='CDI' ".($displayfield === "CDI" ? "checked" : "").">CDI";
    } else if ($fieldtype == 'date') {
        $default_date = new DateTime(self::default_date_end);
        $originalfield = ($job->$fieldname->format('Y-m-d') === $default_date->format('Y-m-d') ? "" : $job->$fieldname->format('Y-m-d'));
        $displayfield = ($isposted ? $fieldposted : $originalfield);
        $input_field = "<input type='text' class='datepicker' id=datepicker".strval($id_dpick)." name='sub_".$fieldname."' placeholder='".$placeholder."' value='".$displayfield."'>";
    } else if ($fieldtype == "combobox") {
        $displayfield = ($isposted ? $fieldposted : $job->$fieldname);
        $idfield = ($isposted ? $_POST['sub_id_category'] : $job->id_category->id);
        $input_field = Category::produce_combobox_text(1, "sub_id_category", $idfield, 'sub_'.$fieldname, $displayfield);
    }
    return $input_field;
}

// Create form for professional experience -- alternative version
public static function produce_form($input_id, $input_formtitle = self::default_formtitle, $input_dbname = self::default_database, $input_fields_dbname = self::default_fields_database)
{
    $edit = (($_GET['user'] === $_SESSION['id']) | ($_SESSION['privilege'] == 2));
    if ($edit) {
        $default_date = new DateTime(self::default_date_end);
        $current_job = ($input_id != 0) ? self::retrieve($input_id, $input_dbname) : new Job();
        $fields = Job::get_fields($input_fields_dbname);
        echo "<i>Les champs suivis d'une ast&eacute;risque <font color='red'><b>*</b></font> sont obligatoires.<br/>
        Si c'est votre poste actuel, laissez le champ <b>Date de fin</b> vide, &agrave; moins que vous ne connaissiez la date de fin de votre contrat.</i>";
        echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
        echo "<fieldset><legend>".$input_formtitle."</legend>";
        echo "<table>";
        $id_dpick = 1;
        foreach ($fields as $field) {
            $disp_name = Job::produce_display_name($field, $_POST['sub_'.$field['field_name']], isset($_POST['submit']));
            $disp_field = Job::produce_field($current_job, $field, $_POST['sub_'.$field['field_name']], isset($_POST['submit']), $id_dpick);
            if ($field['input_type'] == 'date') {
                if ($field['field_name'] == 'date_begin') {
                    echo "<tr><td>C'est mon poste actuel</td><td>:</td><td><input type='checkbox' name='sub_today' ".(isset($_POST['sub_today']) ? "checked" : "")."></input></td></tr>";
                }
                echo "<tr><td>".$disp_name."</td><td>:</td><td>".$disp_field."</td></tr>";
                $id_dpick += 1;
            } else if ($field['field_name'] != 'id_user') {
                echo "<tr><td>".$disp_name."</td><td>:</td><td>".$disp_field."</td></tr>";
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
                if ($field['field_name'] == 'id_user') {
                    $current_job->id_user = $_GET['user'];
                } else if ($field['input_type'] == 'date') {
                    $current_job->$field['field_name'] = new DateTime(($_POST['sub_'.$field['field_name']] === "") ? self::default_date_end : $_POST['sub_'.$field['field_name']]);
                    if (isset($_POST['sub_today'])) $current_job->date_end = new DateTime(self::default_date_end);
                } else if ($field['input_type'] == 'combobox') {
                    if ($_POST['sub_id_category'] != -1) {
                        $current_job->id_category = Category::retrieve($_POST['sub_id_category']);
                    } else {
                        $current_job->id_category = 0;
                    }
                    $current_job->category = $_POST['sub_category'];
                } else {
                    $current_job->$field['field_name'] = $_POST['sub_'.$field['field_name']];
                }
           }
           if ($input_id != 0) {
               $current_job->update($input_dbname);
           } else {
               $current_job->create($input_dbname);
           }
           echo "<meta http-equiv='refresh' content='0;url=./index.php?page=list_job&user=".$_GET['user']."'>";
        }
    } else {
        echo "Vous n'avez pas les droits n&eacute;cessaires pour acc&eacute;der &agrave; cette page.";
    }
}

// Creates a box to display information on a given job -- alternative version
public static function produce_display($input_id, $input_displaytitle = self::default_displaytitle, $input_edit = FALSE, $input_dbname = self::default_database, $input_fields_dbname = self::default_fields_database)
{
    // if ($_GET['user'] != $_SESSION['id'] && $_SESSION['privilege'] < 1) {
    //     echo "Vous devez &ecirc;tre &agrave; jour de votre c&ocirc;tisation pour acc&eacute;der &agrave; cette page.";
    // } else {
    $default_date = new DateTime(self::default_date_end);
    $current_job = ($input_id != 0) ? self::retrieve($input_id, $input_dbname) : new Job();
    $fields = Job::get_fields($input_fields_dbname);
    if ($_POST['delete'] && ($_GET['job'] == $current_job->id)) {
        $current_job->delete($input_dbname);
    } else {
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query, $params);
        $params = array('job' => $current_job->id) + $params;
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?' . http_build_query($params);

        echo "<form method='POST' action='".htmlspecialchars($url, ENT_QUOTES)."'>";

        echo "<fieldset><legend>".$input_displaytitle."</legend>";
        echo "<table>";
        foreach ($fields as $field) {
            if ($field['display_name'] != "") {
                if ($field['input_type'] == 'date') {
                    if  ($field['field_name'] == 'date_begin') {
                        echo "<tr><td><b>P&eacute;riode</b></td><td>:</td><td>".$current_job->date_begin->format('d/m/Y')." - ".(($current_job->date_end->format('Y-m-d') === $default_date->format('Y-m-d')) ? "aujourd'hui" : $current_job->date_end->format('d/m/Y'))."</td></tr>";
                        echo "<tr><td><b>Dur&eacute;e actuelle</b></td><td>:</td><td>".$current_job->get_duration_text()."</td></tr>";
                    }
                } else if ($field['input_type'] == 'combobox') {
                    if ($current_job->id_category != 0) {
                        echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_job->id_category->get_full_name()."</td></tr>";
                    } else {
                        echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_job->category."</td></tr>";
                    }
                } else {
                    echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_job->$field['field_name']."</td></tr>";
                }      
            }
        }
        if ($input_edit) {
            echo "<tr><td><center><input type='submit' name='modify' value='Modifier'></center></td><td></td><td><center><input type='submit' name='delete' value='Supprimer'></center></td></tr>";
        }
        echo "</table>";
        echo "</fieldset>";
        echo "</form>";
        }
    // }

}
//--------------------------------------------------------------------------- //



/*////////////////////////////////////////////////////////////////////////////*/
} ?>
