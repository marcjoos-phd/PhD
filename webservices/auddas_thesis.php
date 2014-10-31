<?php
/* ********************************* THESIS ********************************* */
/*////////////////////////////////////////////////////////////////////////////*/
// PROJECT:        AUDDAS Directory 
// TITLE:          Thesis 
// DESCRIPTION:    Class representing a Ph.D. thesis and its properties
// AUTHORS:        Marc Joos & Vincent Reverdy 
// DATE:           2013-2014
// LICENSE:        GNU GPL version 3 
/*////////////////////////////////////////////////////////////////////////////*/
include_once('auddas_thesis.php');
include_once('auddas_category.php');
class Thesis extends Job {
/*////////////////////////////////////////////////////////////////////////////*/



//--------------------------------- CONSTANTS ------------------------------- //
const default_database = "auddas_theses";
const default_fields_database = "auddas_thesis_fields";
const default_formtitle = "Th&egrave;se";
const default_displaytitle = "Th&egrave;se";
const default_date_end = "0000-00-00";
//--------------------------------------------------------------------------- //



//-------------------------------- ATTRIBUTES ------------------------------- //
var $university;
var $doc_school;
var $advisors;
var $cotutelle;
var $organization1;
var $division1;
var $city1;
var $country1;
//--------------------------------------------------------------------------- //



//--------------------------------- LIFECYCLE ------------------------------- //
// Constructs the thesis from its properties
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
                            $input_description = "",
                            $input_university = "",
                            $input_doc_school = "",
                            $input_advisors = "",
                            $input_cotutelle = FALSE,
                            $input_organization1 = "",
                            $input_division1 = "",
                            $input_city1 = "",
                            $input_country1 = "")
{
    parent::__construct($input_id, 
                        $input_id_user,
                        $input_id_category,
                        $input_category, 
                        $input_title,
                        $input_organization,
                        $input_division,
                        $input_city,
                        $input_country,
                        $input_contract, 
                        $input_date_begin,
                        $input_date_end,
                        $input_keywords,
                        $input_description);
    $this->university = $input_university;
    $this->doc_school = $input_doc_school;
    $this->advisors = $input_advisors;
    $this->cotutelle = $input_cotutelle;
    $this->organization1 = $input_organization1;
    $this->division1 = $input_division1;
    $this->city1 = $input_city1;
    $this->country1 = $input_country1;
}
//--------------------------------------------------------------------------- //



//-------------------------------- MANAGEMENT ------------------------------- //
// Basic printing of the thesis contents
public function display($prefix = "[", $suffix = "]\n")
{
    return parent::diplay($prefix, $suffix)."
           university = {$this->university},
           doc_school = {$this->doc_school},
           advisors = {$this->advisors},
           cotutelle = {$this->cotutelle},
           organization1 = {$this->organization1},
           division1 = {$this->input_division1},
           city1 = {$this->city1},
           country1 = {$this->input_country1}";
}

// Creates a thesis in the database
public function create($input_dbname = self::default_database)
{
    include("opendb.php");
    $query = "INSERT INTO {$input_dbname} 
              (id, id_user, id_category, category, title, organization, division, city, country, contract, date_begin, date_end, keywords, description, university, doc_school, advisors, cotutelle, organization1, division1, city1, country1) 
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
                      '{$this->description}',
                      '{$this->university}',
                      '{$this->doc_school}',
                      '{$this->advisors}',
                      '{$this->cotutelle}',
                      '{$this->organization1}',
                      '{$this->input_division1}',
                      '{$this->city1}',
                      '{$this->input_country1}')";
    $results = mysql_query($query);
    if (!$results) echo mysql_error();
    include("closedb.php");
}

// Deletes a thesis in the database
public function delete($input_dbname = self::default_database)
{
    parent::delete($input_dbname);
}

// Retrieves a thesis with its id
public static function retrieve($input_id, $input_dbname = self::default_database)
{
    include("opendb.php");
    $thesis = NULL;
    $count = 0;
    $query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$input_id} 
              ORDER BY id ASC";
    $results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    if ($count === 1) {
        while ($row = mysql_fetch_array($results)) {
            $thesis_date_begin = new DateTime($row["date_begin"]);
            $thesis_date_end = new DateTime($row["date_end"]);
            $thesis_category = Category::retrieve($row["id_category"]);
            $thesis = new Thesis($row["id"],
                                 $row["id_user"],
                                 $thesis_category,
                                 $row["category"],
                                 $row["title"],
                                 $row["organization"],
                                 $row["division"],
                                 $row["city"],
                                 $row["country"],
                                 $row["contract"],
                                 $thesis_date_begin,
                                 $thesis_date_end,
                                 $row["keywords"],
                                 $row["description"],
                                 $row["university"],
                                 $row["doc_school"],
                                 $row["advisors"],
                                 $row["cotutelle"],
                                 $row["organization1"],
                                 $row["input_division1"],
                                 $row["city1"],
                                 $row["input_country1"]);
        }
    } else {
        echo mysql_error();
    }
    include("closedb.php");
    return $thesis;
}

// Updates a thesis in the database
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
                description='{$this->description}',
                university='{$this->university}',
                doc_school='{$this->doc_school}',
                advisors='{$this->advisors}',
                cotutelle='{$this->cotutelle}',
                organization1='{$this->organization1}',
                division1='{$this->division1}',
                city1='{$this->city1}',
                country1='{$this->country1}'
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

public function produce_field($thesis, $field, $fieldposted, $isposted, $id_dpick=0)
{
    $fieldtype = $field['input_type'];
    $fieldname = $field['field_name'];
    $placeholder = $field['placeholder'];
    if ($fieldtype == 'text') {
        $displayfield = stripslashes(htmlspecialchars($isposted ? $fieldposted : $thesis->$fieldname, ENT_QUOTES));
        $input_field = "<input type='text' name='sub_".$fieldname."' placeholder='".$placeholder."' value='".$displayfield."'>";
    } else if ($fieldtype == 'textarea') {
        $displayfield = stripslashes(htmlspecialchars($isposted ? $fieldposted : $thesis->$fieldname));
        $input_field = "<textarea rows='6' cols='40' name='sub_".$fieldname."' placeholder='".$placeholder."'>".$displayfield."</textarea>";
    } else if ($fieldtype == 'radio') {
        $displayfield = ($isposted ? $fieldposted : ($thesis->$fieldname ? 'true' : 'false'));
        $input_field = "<input type='radio' name='sub_cotutelle' value='true' ".($displayfield === "true" ? "checked" : "").">oui <input type='radio' name='sub_cotutelle' value='false' ".($displayfield === "false" ? "checked" : "").">non";
    } else if ($fieldtype == 'date') {
        $default_date = new DateTime(self::default_date_end);
        $originalfield = ($thesis->$fieldname->format('Y-m-d') === $default_date->format('Y-m-d') ? "" : $thesis->$fieldname->format('Y-m-d'));
        $displayfield = ($isposted ? $fieldposted : $originalfield);
        $input_field = "<input type='text' class='datepicker' id=datepicker".strval($id_dpick)." name='sub_".$fieldname."' placeholder='".$placeholder."' value='".$displayfield."'>";
    }
    return $input_field;
}

// Create form for thesis -- alternative version
public static function produce_form($input_id, $input_formtitle = self::default_formtitle, $input_dbname = self::default_database, $input_fields_dbname = self::default_fields_database)
{
    $edit = (($_GET['user'] === $_SESSION['id']) | ($_SESSION['privilege'] == 2));
    if ($edit) {
        $default_date = new DateTime(self::default_date_end);
        $current_thesis = ($input_id != 0) ? self::retrieve($input_id, $input_dbname) : new Job();
        $fields = Thesis::get_fields($input_fields_dbname);
        echo "<i>Les champs suivis d'une ast&eacute;risque <font color='red'><b>*</b></font> sont obligatoires.<br/>
        Si vous &ecirc;tes encore en th&egrave;se, laissez le champ <b>Date de fin</b> vide.</i>";
        echo "<form method='POST' class='form_class' action='".$_SERVER['REQUEST_URI']."'>";
        echo "<fieldset><legend>".$input_formtitle."</legend>";
        echo "<table rules=groups>";
        echo "<tbody>";
        $id_dpick = 1;
        foreach ($fields as $field) {
            $disp_name = Thesis::produce_display_name($field, $_POST['sub_'.$field['field_name']], isset($_POST['submit']));
            $disp_field = Thesis::produce_field($current_thesis, $field, $_POST['sub_'.$field['field_name']], isset($_POST['submit']), $id_dpick);
            if ($field['input_type'] == 'date') {
                if ($field['field_name'] == 'date_begin') {
                    echo "<tr><td>Ma th&egrave;se est en cours</td><td>:</td><td><input type='checkbox' name='sub_today' ".(isset($_POST['sub_today']) ? "checked" : "")."></input></td></tr>";
                }
                echo "<tr><td>".$disp_name."</td><td>:</td><td>".$disp_field."</td></tr>";
                $id_dpick += 1;
            } else if (!in_array($field['field_name'], array('id_user', 'category'))) {
                if ($field['field_name'] == 'organization1') {
                    echo "</tbody><tbody>";
                    echo "<tr><td colspan=3><b>Si la th&egrave;se est/a &eacute;t&eacute; faite en co-tutelle :</b></td></tr>";
                }
                echo "<tr><td>".$disp_name."</td><td>:</td><td>".$disp_field."</td></tr>";
                if ($field['field_name'] == 'country1') {
                    echo "</tbody><tbody>";
                }
            }
        }
        echo "<tr><td colspan=3><center><input type='submit' name='submit' value='Enregistrer'></center></td></tr>";
        echo "</tbody></table>";
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
                    $current_thesis->id_user = $_GET['user'];
                } else if ($field['input_type'] == 'date') {
                    $current_thesis->$field['field_name'] = new DateTime(($_POST['sub_'.$field['field_name']] === "") ? self::default_date_end : $_POST['sub_'.$field['field_name']]);
                    if (isset($_POST['sub_today'])) $current_thesis->date_end = new DateTime(self::default_date_end);
                } else if ($field['input_type'] == 'radio') {
                    $current_thesis->cotutelle = $_POST['sub_cotutelle'] == 'true' ? true : false;
                } else {
                    $current_thesis->$field['field_name'] = $_POST['sub_'.$field['field_name']];
                }
           }
           if ($input_id != 0) {
               $current_thesis->update($input_dbname);
           } else {
               $current_thesis->create($input_dbname);
           }
           echo "<meta http-equiv='refresh' content='0;url=./index.php?page=list_profile&user=".$_GET['user']."'>";
        }
    } else {
        echo "Vous n'avez pas les droits n&eacute;cessaires pour acc&eacute;der &agrave; cette page.";
    }
}

// Creates a box to display information on a given thesis -- alternative version
public static function produce_display($input_id, $input_displaytitle = self::default_displaytitle, $input_edit = FALSE, $input_dbname = self::default_database, $input_fields_dbname = self::default_fields_database)
{
    // if ($_GET['user'] != $_SESSION['id'] && $_SESSION['privilege'] < 1) {
    //     echo "Vous devez &ecirc;tre &agrave; jour de votre c&ocirc;tisation pour acc&eacute;der &agrave; cette page.";
    // } else {
    $default_date = new DateTime(self::default_date_end);
    $current_thesis = ($input_id != 0) ? self::retrieve($input_id, $input_dbname) : new Job();
    $fields = Thesis::get_fields($input_fields_dbname);
    if ($_POST['delete'] && ($_GET['thesis'] == $current_thesis->id)) {
        //$current_thesis->delete($input_dbname);
        echo "<meta http-equiv='refresh' content='0;url=./index.php?page=delete_thesis&user=".$_GET['user']."&id=".$current_thesis->id."'>";
    } else {
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query, $params);
        $params = array('thesis' => $current_thesis->id) + $params;
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?' . http_build_query($params);

        echo "<form method='POST' action='".htmlspecialchars($url, ENT_QUOTES)."'>";

        echo "<fieldset><legend>".$input_displaytitle."</legend>";
        echo "<table>";
        foreach ($fields as $field) {
            if ($field['display_name'] != "" && $current_thesis->$field['field_name'] != "") {
                if ($field['input_type'] == 'date') {
                    if  ($field['field_name'] == 'date_begin') {
                        echo "<tr><td><b>P&eacute;riode</b></td><td>:</td><td>".$current_thesis->date_begin->format('d/m/Y')." - ".(($current_thesis->date_end->format('Y-m-d') === $default_date->format('Y-m-d')) ? "aujourd'hui" : $current_thesis->date_end->format('d/m/Y'))."</td></tr>";
                        echo "<tr><td><b>Dur&eacute;e actuelle</b></td><td>:</td><td>".$current_thesis->get_duration_text()."</td></tr>";
                    }
                } else if ($field['input_type'] == "radio") {
                    if ($field['field_name'] == 'cotutelle') {
                        echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".(($current_thesis->cotutelle == true) ? "oui" : "non")."</td></tr>";
                    }
                } else if ($field['input_type'] != "combobox") {
                    echo "<tr><td><b>".$field['display_name']."</b></td><td>:</td><td>".$current_thesis->$field['field_name']."</td></tr>";
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

// Delete a given thesis
public static function delete_thesis($input_id, $input_dbname = self::default_database)
{
    $edit = (($_GET['user'] === $_SESSION['id']) | ($_SESSION['privilege'] == 2));
    if ($edit) {
        $current_thesis = self::retrieve($input_id, $input_dbname);
        echo "<form method='POST' action='".$_SERVER['REQUEST_URI']."'>";
        echo "&Ecirc;tes-vous s&ucirc;r de vouloir supprimer cette th&egrave;se&nbsp;? <input type='submit' name='delete' value='Supprimer'></form><br/>";
        echo "Pour m&eacute;moire, voici la th&egrave;se en question&nbsp;:";
        self::produce_display($current_thesis->id);
        if (isset($_POST['delete'])) {
            $current_thesis->delete($input_dbname);
            echo "<meta http-equiv='refresh' content='0;url=./index.php?page=thesis_deleted'>";
        }
    }
}
//--------------------------------------------------------------------------- //



/*////////////////////////////////////////////////////////////////////////////*/
} ?>
