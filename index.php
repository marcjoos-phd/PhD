<?php
/* ******************************** MAIN PAGE ******************************* */
/*////////////////////////////////////////////////////////////////////////////*/
// PROJECT:        AUDDAS Directory 
// TITLE:          Main page - index.php
// DESCRIPTION:    Main page to display all informations
// AUTHORS:        Marc Joos & Vincent Reverdy 
// DATE:           2013-2014
// LICENSE:        GNU GPL version 3 
/*////////////////////////////////////////////////////////////////////////////*/

session_start();
if (!isset($_GET['page'])) $_GET['page'] = 7;
define('_VALID_INCLUDE', TRUE);
include_once("./includes/paths.php");
include_once("auddas_category.php");
include_once("auddas_job.php");
include_once("auddas_user.php");
include_once("auddas_thesis.php");
include_once("auddas_account.php");

// Set localisation
setlocale(LC_ALL, 'fr_FR');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Annuaire AUDDAS --- Accueil</title>
  
        <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
        <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery-ui-timepicker.css" />
        <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.multiselect.css" />
        <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.multiselect.filter.css" />
        <link rel="stylesheet" type="text/css" href="./includes/css/ui-lightness/jquery.checkboxtree.min.css" />
        <link rel="stylesheet" type="text/css" href="./includes/style.css" />
        <script type="text/javascript" src="./includes/js/jquery.js"></script>
        <script type="text/javascript" src="./includes/js/jquery-ui.js"></script>
        <script type="text/javascript" src="./includes/js/jquery.ui.timepicker.js"></script>
        <script type="text/javascript" src="./includes/js/jquery.checkboxtree.min.js"></script>
        <script type="text/javascript" src="./includes/js/jquery.confirm.js"></script>
        <script type="text/javascript" src="./includes/js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="./includes/js/jquery.ui.datepicker-fr.js"></script>
        <script type="text/javascript" src="./includes/js/jquery.multiselect.min.js"></script>
        <script type="text/javascript" src="./includes/js/jquery.multiselect.filter.js"></script>
    </head>

    <body>
        <div id="top">
            <span id="title"><h1>Annuaire AUDDAS</h1></span>
            <div class=userdiv id=userdiv>
<?php
                if (!isset($_SESSION['login'])) {
                    echo "<a href=\"./index.php?page=login\">Connexion</a> | <a href=\"./index.php?page=registration\">Inscription</a>";
                } else {
                    echo "Connect&eacute; en tant que <b>".$_SESSION['login']."</b> | <a href=\"./index.php?page=logout\">D&eacute;connexion</a>";
                }
?>            
            </div>
            <ul id='main_menu'>
<?php
                if (isset($_SESSION['login'])) {
                    echo "<li><a href=\"./index.php?page=list_profile&user=".$_SESSION['id']."\">G&eacute;rer mon profil</a></li>";

                    echo "<li><a href=\"./index.php?page=list_job&user=".$_SESSION['id']."\">G&eacute;rer mon parcours</a></li>";
                    echo "<li><a href=\"./index.php?page=search\">Chercher</a></li>";
                    if ($_SESSION['privilege'] == 2) {
                        echo "<li><a href=\"./index.php?page=admin\">Administration</a></li>";
                    }
                    echo "<li><a href=\"./index.php?page=contact\">Nous contacter</a></li>";
                    echo "<li><a href=\"./index.php?page=credits\">Cr&eacute;dits</a></li>";
                    echo "<li><a href=\"./index.php\">Revenir &agrave; l'accueil</a></li>";
                } else {
                    echo "<li><a href=\"./index.php?page=contact\">Nous contacter</a></li>";
                    echo "<li><a href=\"./index.php?page=credits\">Cr&eacute;dits</a></li>";
                }
?>
            </ul>
        </div>

<?php
        if ($_GET['page'] == "login") {
            Account::login_form();
        } else if ($_GET['page'] == "logout") {
            Account::logout();
        } else if ($_GET['page'] == "registration") {
            Account::inscription_form();
        } else if ($_GET['page'] == "forgotten") {
            Account::reset_password();
        } else if ($_GET['page'] == "validate") {
            Account::validate();
        } else if ($_GET['page'] == "change_password") {
            Account::validate_password();
        } else if ($_GET['page'] == "confirm_registration") {
            echo "<b>Demande d'inscription enregistr&eacute;e !</b><br/>
            Vous recevrez un mail de confirmation de votre inscription dans quelques instants, vous invitant &agrave; vous connecter et &agrave; cr&eacute;er votre compte. Pensez &agrave; regarder dans vos spams si vous ne voyez pas le mail de confirmation dans les cinq minutes suivant votre inscription !<br/>
            Merci de rejoindre notre annuaire !";
        } else if ($_GET['page'] == "confirm_validation") {
            echo "<b>Votre compte est d&eacute;sormais valid&eacute; !</b><br/>
            Vous pouvez vous connecter pour remplir votre profil et vos exp&eacute;riences.";
        } else if ($_GET['page'] == "updated") {
            echo "<b>Votre compte &agrave; bien &eacute;t&eacute; mis &agrave; jour !</b>";
        } else if ($_GET['page'] == "confirm_password") {
            echo "<b>Votre demande de mot de passe a bien &eacute;t&eacute; prise en compte !</b><br/>
            Vous recevrez un mail de confirmation de changement de mot de passe dans quelques instants.";
        } else if ($_GET['page'] == "contact") {
            echo "<b>Nous contacter :</b><br/>
            <br/>
            <ul>
                <li>Webmaster : <a href=\"mailto:webmaster@auddas.fr\">webmaster [at] auddas.fr</a></li>
                <li>Contact g&eacute;n&eacute;ral : <a href=\"mailto:contact@auddas.fr\">contact [at] auddas.fr</a></li>
            </ul>";
        } else if ($_GET['page'] == "credits") {
            echo "<p>Cet annuaire utilise <a href=\"https://github.com/marcjoos/PhD\">PhD - Phone Directory for PhD</a>, un projet d'annuaire en ligne d&eacute;velopp&eacute; pour AUDDAS, sous <a href=\"http://www.gnu.org/licenses/gpl.html\">licence GNU/GPL version 3</a>.<br/>";
            echo "<i>Tous droits r&eacute;serv&eacute;s</i> 2013-2014, <b>Marc Joos</b> &amp; <b>Vincent Reverdy</b>.</p>";
            echo "<p>Le d&eacute;veloppement initial de ce projet s'est bas&eacute; sur <a href=\"https://code.google.com/p/fliker/\">Fliker</a>, un projet d'annuaire pour associations d&eacute;velopp&eacute; par <a href=\"https://code.google.com/p/fliker/people/list\">Philippe Marty et al.</a> que nous remer&ccedil;ions pour le coup de pouce initial !</p>";
        } else if ($_GET['page'] == "check_subscription") {
           Account::check_subscription_all(); 
        } else if (!isset($_SESSION['login'])) { 
            echo "<center><h1>Bienvenue sur l'annuaire d'AUDDAS !</h1>

            <p>Vous pouvez vous connecter ou vous inscrire en suivant les liens en haut &agrave; droite.</p>
            <p><br/></p>
           <img src=\"./includes/img/logo.png\"></center";
        }


        if (isset($_SESSION['login'])) {
            if ((isset($_GET['user']))) {
                $edit = (($_GET['user'] === $_SESSION['id']) | ($_SESSION['privilege'] == 2));
                if ($_GET['page'] == "list_profile") {        
                    User::produce_display($_GET['user'], $edit);
                    User::produce_theses_list($_GET['user'], $edit);
                } else if ($_GET['page'] == "list_complete_profile") {
                    User::produce_display($_GET['user'], $edit);
                    User::produce_theses_list($_GET['user'], $edit);
                    User::produce_jobs_list($_GET['user'], $edit);
                } else if ($_GET['page'] == "edit_profile") {
                    User::produce_form($_GET['user']);
                } else if ($_GET['page'] == "edit_thesis") {
                    if (isset($_GET['id'])) {
                        Thesis::produce_form($_GET['id']);
                    }
                } else if ($_GET['page'] == "delete_thesis") {
                    if (isset($_GET['id'])) {
                        Thesis::delete_thesis($_GET['id']);
                    }
                } else if ($_GET['page'] == "list_job") {
                    User::produce_jobs_list($_GET['user'], $edit);  
                    User::produce_theses_list($_GET['user'], $edit); 
                } else if ($_GET['page'] == "edit_job") {
                    if (isset($_GET['id'])) {
                        Job::produce_form($_GET['id']);
                    }
                } else if ($_GET['page'] == "delete_job") {
                    if (isset($_GET['id'])) {
                        Job::delete_job($_GET['id']);
                    }
                }
            } else if ($_GET['page'] == "thesis_deleted") {
                echo "Votre th&egrave;se a bien &eacute;t&eacute; supprim&eacute;e.";
            } else if ($_GET['page'] == "job_deleted") {
                echo "Votre exp&eacute;rience professionnelle a bien &eacute;t&eacute; supprim&eacute;e.";
            } else if ($_GET['page'] == "search") {
                //if ($_SESSION['privilege'] < 1) {
                //    echo "Vous devez &ecirc;tre &agrave; jour de votre c&ocirc;tisation pour acc&eacute;der &agrave; cette page.";    
                //} else {
                    echo "<p>La recherche est encore en construction... Merci de votre compr&eacute;hension !<br/>
                    En attendant, nous vous proposons d'acc&eacute;der &agrave; la liste de nos adh&eacute;rents directement :<br/></p> <p></p>";
                    User::list_all_users();
                //}
            } else if ($_GET['page'] == "admin") {
                if ($_SESSION['privilege'] == 2) {
                    Account::list_all_accounts();
                } else {
                    echo "Vous n'avez pas les droits n&eacute;cessaires pour acc&eacute;der &agrave; cette page.";
                }
            } else if ($_GET['page'] == "delete_account") {
                if ($_SESSION['privilege'] == 2) {
                    echo "<form method='POST' action='".$_SERVER['REQUEST_URI']."'>";
                    echo "&Ecirc;tes-vous s&ucirc;r de vouloir supprimer le profil #".$_GET["id"]."&nbsp;? <input type='submit' name='delete' value='Supprimer'></form><br/>";
                    echo "Pour m&eacute;moire, voici le profil que vous vous appr&ecirc;tez &agrave; supprimer :";
                    $edit = True;
                    User::produce_display($_GET['id'], $edit);
                    User::produce_theses_list($_GET['id'], $edit);
                    User::produce_jobs_list($_GET['id'], $edit);
                    if (isset($_POST['delete'])) {
                        Account::delete_whole_user($_GET['id']);
                        echo "<meta http-equiv='refresh' content='0;url=./index.php?page=account_deleted&id=".$_GET['id']."'>";
                    }
                } else {
                    echo "Vous n'avez pas les droits n&eacute;cessaires pour acc&eacute;der &agrave; cette page.";
                }
            } else if ($_GET['page'] == "account_deleted") {
                echo "Le compte #".$_GET['id']." a bien &eacute;t&eacute; supprim&eacute; !";
            //-- TESTS
            } else if ($_GET['page'] == "test") {
                // Account::check_subscription_date(2);
                // Account::delete_whole_user(33);
                // $fields = User::get_fields();
                // $fields = Job::get_fields();
                // foreach ($fields as $field) {
                //     echo $field['field_name']."<br/>";
                // }
            //-- TESTS
            } else if (!isset($_GET['user'])) {
                if (in_array($_GET['page'], array("contact", "credits"))) {
                } else {
                echo "<p><b>Bienvenue sur l'annuaire d'AUDDAS !</b></p>
                <p>Vous pouvez compl&eacute;ter et &eacute;diter votre profil, renseigner vos exp&eacute;riences professionnelles, et chercher dans notre base d'adh&eacute;rents.</p>
                <p><br/></p>
                <center><img src=\"./includes/img/logo.png\"></center>";
                }
            } else {
                echo "Vous n'avez pas les droits n&eacute;cessaires pour acc&egrave;der &agrave; cette page. Si vous tentez d'&eacute;diter une de vos exp&eacute;riences professionnelles et que vous obtenez ce message d'erreur, merci de contacter les administrateurs.";
            }   
        }
?>

    </body>
</html>

<script>
$(function() {
    $( "#datepicker" ).datepicker({ 
        changeYear: true , yearRange: "-100:+50" , changeMonth: true , dateFormat: "yy-mm-dd"  
    });
});
$(function() {
    $( ".datepicker" ).datepicker({ 
        changeYear: true , yearRange: "-100:+50" , changeMonth: true , dateFormat: "yy-mm-dd"  
    });
});
function combo(thelist, theinput) {
    theinput = document.getElementById(theinput);
    var idx = thelist.selectedIndex;
    var content = thelist.options[idx].innerHTML;
    theinput.value = content;
}
</script>
