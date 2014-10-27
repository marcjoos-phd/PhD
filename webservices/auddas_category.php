<?php
/* ********************************* CATEGORY ******************************* */
/*////////////////////////////////////////////////////////////////////////////*/
// PROJECT:        AUDDAS Directory 
// TITLE:          Category 
// DESCRIPTION:    Class representing a job category
// AUTHORS:        Marc Joos & Vincent Reverdy 
// DATE:           2013-2014
// LICENSE:        GNU GPL version 3 
/*////////////////////////////////////////////////////////////////////////////*/
class Category {
/*////////////////////////////////////////////////////////////////////////////*/



//--------------------------------- CONSTANTS ------------------------------- //
const default_database = "auddas_categories";
//--------------------------------------------------------------------------- //



//-------------------------------- ATTRIBUTES ------------------------------- //
public $id;
public $id_parent;
public $name;
public $description;
//--------------------------------------------------------------------------- //



//--------------------------------- LIFECYCLE ------------------------------- //
// Constructs the category from its properties
public function __construct($input_id = 0, 
                            $input_id_parent = 0, 
                            $input_name = "", 
                            $input_description = "") 
{
    $this->id = $input_id;
    $this->id_parent = $input_id_parent;
    $this->name = $input_name;
    $this->description = $input_description;
}
//--------------------------------------------------------------------------- //



//-------------------------------- MANAGEMENT ------------------------------- //
// Basic printing of the category contents
public function display($prefix = "[", $suffix = "]\n")
{
    return "{$prefix}id = {$this->id}, 
                     id_parent = {$this->id_parent},
                     name = {$this->name},
                     description = {$this->description}{$suffix}";
}

// Inserts the current category into the database
public function create($input_dbname = self::default_database)
{
    $count = 0;
    if ($this->id_parent > 0) {
        $count = $this->count_parents($input_dbname);
        if ($count < 1) {
            echo "No existing parent!";
            echo mysql_error();
        } else if ($count > 1) {
            echo "Conflicting parents!";
            echo mysql_error();
        }
        $count = 0;
    }
    include("opendb.php");
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$this->id} 
              OR (name = '{$this->name}' 
              AND id_parent = {$this->id_parent})
              ORDER BY id ASC";
    $result = mysql_query($query);
    if ($result) $count = mysql_num_rows($result);
    if ($count > 0) {
        echo "Cannot insert due to collision with an existing category!";
        echo mysql_error();
    } else {
        $query = "INSERT INTO {$input_dbname} 
                  (id, id_parent, name, description) VALUES ('{$this->id}', 
                   '{$this->id_parent}', 
                   '{$this->name}', 
                   '{$this->description}')";
        $results = mysql_query($query);
        if (!$results) echo mysql_error();
    }
    include("closedb.php");    
}

// Deletes the current category from the database
public function delete($input_dbname = self::default_database)
{
    $count = 0;
    if ($this->is_existing($input_dbname)) {
        if ($this->is_leaf($input_dbname)) {
            include("opendb.php");
            $query = "DELETE FROM {$input_dbname} 
                      WHERE id = {$this->id} 
                      AND id_parent = {$this->id_parent}
                      AND name = '{$this->name}'";
            $result = mysql_query($query);
            include("closedb.php");    
        } else {
            include("opendb.php");
            echo "Cannot delete due to existing children!";
            echo mysql_error();
            include("closedb.php");    
        }
    }
}

// Retrieves a category from a given id
public static function retrieve($input_id, $input_dbname = self::default_database)
{
	include("opendb.php");
    $count = 0;
    $category = NULL;
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$input_id} 
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) {
        $count = mysql_num_rows($results);
        if ($count == 1) {
            while ($row = mysql_fetch_array($results)) {
                $category = new Category($row["id"],
                                         $row["parent_id"],
                                         $row["name"],
                                         $row["description"]);
            }
        } else {
            echo mysql_error();
        }
    }
	include("closedb.php");
	return $category; 
}

// Produces a combobox of categories -- alternative version
public static function produce_combobox_text($input_combobox_id, $input_combobox_name, $input_selected_id = -1, $input_name, $cat_name, $input_dbname = self::default_database)
{
    $results = self::produce_combobox_arrays($input_dbname);
    $result_id = $results[0];
    $result_name = $results[1];
    $i = 0;
    if ($cat_name == "") {
        while ($i < count($result_id)) {
            if ($result_id[$i] == $input_selected_id) $cat_name = $result_name[$i];
            ++$i;
        }
    }
    $combotext = "<select id='".$input_combobox_id."' name='".$input_combobox_name."' onChange=\"combo(this, '".$input_name."')\">";
    $i = 0;
    while ($i < count($result_id)) {
        if ($i == 0) $combotext .= "<option value=-1>-- Autre (pr&eacute;cisez)</option>";
        $combotext .= "<option value='".$result_id[$i]."' ".($result_id[$i] == $input_selected_id ? "selected=\"selected\"" : "").">".$result_name[$i]."</option>";
        ++$i;
    }
    $combotext .= "</select>";
    $combotext .= "<input type='text' id='".$input_name."' name='".$input_name."' value='".$cat_name."'>";
    return $combotext;
}

// Produces a combobox of categories
public static function produce_combobox($input_combobox_id, $input_combobox_name, $input_selected_id = -1, $input_dbname = self::default_database)
{
    echo "<select id='".$input_combobox_id."' name='".$input_combobox_name."'>";
    $results = self::produce_combobox_arrays($input_dbname);
    $result_id = $results[0];
    $result_name = $results[1];
    $i = 0;
    while ($i < count($result_id)) {
        echo "<option value='".$result_id[$i]."' ".($result_id[$i] == $input_selected_id ? "selected=\"selected\"" : "").">".$result_name[$i]."</option>";
        ++$i;
    }
    echo "</select>";
}

// Produces an array of categories compatible with a combo box
public static function produce_combobox_arrays($input_dbname = self::default_database)
{
    $result_id = array();
    $result_name = array();
    $coarse_array = self::get_coarse($input_dbname);
    foreach ($coarse_array as $coarse_category) {
        array_push($result_id, $coarse_category->id);
        array_push($result_name, $coarse_category->name);
        self::produce_combobox_recursively($coarse_category, $coarse_category->name, $result_id, $result_name, $input_dbname);
    }
    return array($result_id, $result_name);
}

// Recursively produces an array of categories compatible with a combo box
public static function produce_combobox_recursively($current_category, $current_prefix, &$result_id, &$result_name, $input_dbname = self::default_database)
{
    $current_children = $current_category->get_children($input_dbname);
    foreach ($current_children as $current_child) {
        self::produce_combobox_recursively($current_child, $current_prefix."/".$current_child->name, $result_id, $result_name, $input_dbname);
    }
    if (count($current_children) == 0) {
        array_push($result_id, $current_category->id);
        array_push($result_name, $current_prefix);
    }
}

// 
public function get_full_name($input_dbname = self::default_database)
{
    $full_name = "";
    $results = self::produce_combobox_arrays($input_dbname);
    $result_id = $results[0];
    $result_name = $results[1];
    $i = 0;
    while ($i < count($result_id)) {
        if ($this->id == $result_id[$i]) $full_name = $result_name[$i];
        ++$i;
    }
    return $full_name;
}
//--------------------------------------------------------------------------- //



//------------------------------ BOOLEAN TESTS ------------------------------ //
// Returns whether all the contents of the category is empty
public function is_empty()
{
    return ($this->id == 0) 
        && ($this->id_parent == 0) 
        && ($this->name === "")
        && ($this->description === "");
}

// Returns true if the category exists in the database
public function is_existing($input_dbname = self::default_database)
{
	return $this->count_matching($input_dbname) > 0;
}

// Returns true if the category has no parent in the database
public function is_coarse($input_dbname = self::default_database)
{
	return $this->count_parents($input_dbname) == 0;
}

// Returns true if the category has no child in the database
public function is_leaf($input_dbname = self::default_database)
{
    return $this->count_children($input_dbname) == 0;
}

// Returns true if the category is neither coarse nor leaf
public function is_intermediate($input_dbname = self::default_database)
{
    return !($this->is_coarse($input_dbname)) && !($this->is_leaf($input_dbname));
}

// Returns true if the category is an orphan (broken database)
public function is_orphan($input_dbname = self::default_database)
{
	return ($this->count_parents($input_dbname) == 0) && ($this->id_parent > 0);
}
//--------------------------------------------------------------------------- //



//------------------------------ COUNT ELEMENTS ----------------------------- //
// Counts the number of exact matching categories in the database
public function count_matching($input_dbname = self::default_database)
{
	include("opendb.php");
    $count = 0;
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$this->id} 
              AND id_parent = {$this->id_parent}
              AND name = '{$this->name}'
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    include("closedb.php");
	return $count;
}

// Counts the number of coarse categories in the database
public static function count_coarse($input_dbname = self::default_database)
{
	include("opendb.php");
    $count = 0;
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id_parent = 0
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    include("closedb.php");
	return $count;
}

// Counts the number of parent of the current category in the database
public function count_parents($input_dbname = self::default_database)
{
	include("opendb.php");
    $count = 0;
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$this->id_parent} 
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    include("closedb.php");
	return $count;
}

// Counts the number of children of the current category in the database
public function count_children($input_dbname = self::default_database)
{
	include("opendb.php");
    $count = 0;
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id_parent = {$this->id} 
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    include("closedb.php");
	return $count;
}

// Counts the number of neighbours of the current category in the database
public function count_neighbours($input_dbname = self::default_database)
{
	include("opendb.php");
    $count = 0;
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id_parent = {$this->id_parent} 
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) $count = mysql_num_rows($results);
    include("closedb.php");
	return $count;
}
//--------------------------------------------------------------------------- //



//------------------------------ TREE NAVIGATION ---------------------------- //
// Get level in the database
public function get_level($input_dbname = self::default_database)
{
    $level = 0;
    $category = $this;
    while ($category->count_parents($input_dbname) > 0) {
        $category = $category->get_parent($input_dbname);
        ++$level;
    }
    return $level;
}

// Returns an array of the coarse categories from the database
public static function get_coarse($input_dbname = self::default_database) 
{
	include("opendb.php");
    $coarse = array();
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id_parent = 0
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) {
        if (mysql_num_rows($results) > 0) {
            while ($row = mysql_fetch_array($results)) {
                array_push($coarse, new Category($row["id"],
                                                 $row["parent_id"],
                                                 $row["name"],
                                                 $row["description"]));
            }
        }
    }
	include("closedb.php");
    return $coarse;
}

// Returns the parent of the current category from the database
public function get_parent($input_dbname = self::default_database) 
{
	include("opendb.php");
    $count = 0;
    $category = NULL;
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id = {$this->id_parent} 
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) {
        $count = mysql_num_rows($results);
        if ($count == 1) {
            while ($row = mysql_fetch_array($results)) {
                $category = new Category($row["id"],
                                         $row["parent_id"],
                                         $row["name"],
                                         $row["description"]);
            }
        } else if ($count == 0) {
            $category = new Category();
        } else {
            echo mysql_error();
        }
    }
	include("closedb.php");
	return $category;
}

// Returns an array of the children of the current category from the database
public function get_children($input_dbname = self::default_database) 
{
	include("opendb.php");
    $children = array();
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id_parent = {$this->id} 
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) {
        if (mysql_num_rows($results) > 0) {
            while ($row = mysql_fetch_array($results)) {
                array_push($children, new Category($row["id"],
                                                   $row["parent_id"],
                                                   $row["name"],
                                                   $row["description"]));
            }
        }
    }
	include("closedb.php");
    return $children;
}

// Returns an array of the neighbours of the current category from the database
public function get_neighbours($input_dbname = self::default_database) 
{
	include("opendb.php");
    $neighbours = array();
	$query = "SELECT * FROM {$input_dbname} 
              WHERE id_parent = {$this->id_parent} 
              ORDER BY id ASC";
	$results = mysql_query($query);
    if ($results) {
        if (mysql_num_rows($results) > 0) {
            while ($row = mysql_fetch_array($results)) {
                array_push($neighbours, new Category($row["id"],
                                                     $row["parent_id"],
                                                     $row["name"],
                                                     $row["description"]));
            }
        }
    }
	include("closedb.php");
    return $neighbours;
}
//--------------------------------------------------------------------------- //



/*////////////////////////////////////////////////////////////////////////////*/
} ?>
