<!--
    Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

    Tip 2: you can also add an image using data-image tag
-->
<?php
$newProjectButton = "";

if(in_array($_SESSION['role'], ['editor', 'adminEditor'])){
    $activeTag = basename($_SERVER['SCRIPT_FILENAME'], '.php') == 'newProject'  ? 'active' : '';
    $newProjectButton = "       <li class='nav-item $activeTag'>
    <a class='nav-link' data-toggle='collapse' href='#newProject' aria-expanded='true'>
        <i class='material-icons'>library_add</i>
        <p> Nový projekt
            <b class='caret'></b>
        </p>
    </a>           <div class='collapse' id='newProject' style=''>
    <ul class='nav'>
        <li class='nav-item ' >
            <a class='nav-link' href='newProject.php?projectType=namet' >
                <span class='sidebar-mini'> NP </span>
                <span class='sidebar-normal'> Námět projektu </span>
            </a>
        </li>
        <li class='nav-item ' >
            <a class='nav-link' href='newProject.php?projectType=projekt' >
                <span class='sidebar-mini'> PS </span>
                <span class='sidebar-normal'> Projekt stavby (s PD)</span>
            </a>
        </li>
        <li class='nav-item ' >
            <a class='nav-link' href='newProject.php?projectType=lite' >
                <span class='sidebar-mini'> PÚ </span>
                <span class='sidebar-normal'> Projekt údržby (bez PD)</span>
            </a>
        </li>
    </ul>
    </div>

</li>
";
}

?>

<div class="sidebar" data-color="white" data-background-color="red">

<div class="logo">
    <h1>!!! DEV !!!</h1>
    <a href="home.php" class="simple-text logo-mini">
        <img src="/img/mostAdmisWhiteMini.png" alt="logo admis - white mini" class="img-fluid"/>
    </a>

    <a href="home.php" class="simple-text logo-normal">
        <img src="/img/textAdmisWhiteMini.png" alt="text admis - white mini" class="img-fluid"/>
    </a>
</div>

<div class="sidebar-wrapper">
    <div class="user">
        <div class="photo">
            <img alt="sidebar bacground" src="data:image/png;base64,<?php print createAvatar(getInitialsFromName($_SESSION['jmeno'])) ?>"/>
        </div>
        <div class="user-info">
            <a data-toggle="collapse" href="#collapseExample" class="username">
                    <span>
                       <?php echo $_SESSION['jmeno']; ?>
                        <b class="caret"></b>
                    </span>
            </a>
            <div class="collapse" id="collapseExample">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <span class="sidebar-mini"> MP </span>
                            <span class="sidebar-normal"> Můj profil </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../vip/collaborators.php">
                            <span class="sidebar-mini"> <i class="material-icons">people</i> </span>
                            <span class="sidebar-normal"> Spolupracovníci </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <ul class="nav">
        <li class="nav-item <?php if (basename($_SERVER["SCRIPT_FILENAME"], '.php') == "home") echo "active"; ?>">
            <a class="nav-link" href="home.php">
                <i class="material-icons">dashboard</i>
                <p> Dashboard </p>
            </a>
        </li>

        <?php  echo $newProjectButton; ?>
        <li class="nav-item <?php if (basename($_SERVER["SCRIPT_FILENAME"], '.php') == "zadanky") echo "active"; ?>">
            <a class="nav-link" href="zadanky.php">
                <i class="material-icons">task</i>
                <p> Žádanky</p>
            </a>
        </li>

        <li class="nav-item <?php if (basename($_SERVER["SCRIPT_FILENAME"], '.php') == "vypis") echo "active"; ?>">
            <a class="nav-link" href="vypis.php">
                <i class="material-icons">grid_on</i>
                <p> Výpis</p>
            </a>
        </li>
        <li class="nav-item <?php if (basename($_SERVER["SCRIPT_FILENAME"], '.php') == "detail") echo "active"; ?>">
            <a class="nav-link"
               href="detail.php<?php if (isset($_GET["idProject"])) echo "?idProject=$_GET[idProject]"; ?>">
                <i class="material-icons">
                    details
                </i>
                <p> Detail </p>
            </a>
        </li>
        <li class="nav-item <?php if (basename($_SERVER["SCRIPT_FILENAME"], '.php') == "mapa") echo "active"; ?>">
            <a class="nav-link" href="mapa.php?editor=<?php echo $_SESSION['username']; ?>">
                <i class="material-icons">map</i>
                <p> mapa </p>
            </a>
        </li>
        <li class="mt-5 nav-item <?php if (basename($_SERVER["SCRIPT_FILENAME"], '.php') == "helpdesk") echo "active"; ?>">
            <a class="nav-link" href="helpdesk.php">
                <i class="material-icons">mood_bad</i>
                <p> O aplikaci </p>
            </a>
        </li>
        <?php
        $activeFirmy = '';
        $activeUzivatele = '';
        $activeLogy = '';
        $activeSoubory = '';
        $settShow = '';
        $activeButt = '';
        $activePriority = '';
        $activeKontakty = '';
        $activeZadanky = '';

        if ($_SESSION['role'] == 'adminEditor' OR $_SESSION['role'] == 'editor') {//sekce pro adminy
            if (basename($_SERVER["SCRIPT_FILENAME"], '.php') == "nastaveni") {
                $activeButt = "active";
                $settShow = 'show';
                if (isset($_GET['sprava'])) {
                    switch ($_GET['sprava']) {
                        case "firmy":
                            $activeFirmy = 'active';
                            break;
                        case "uzivatele":
                            $activeUzivatele = 'active';
                            break;
                        case "zadanky":
                            $activeZadanky = 'active';
                            break;
                        case "logy":
                            $activeLogy = 'active';
                            break;
                        case "soubory":
                            $activeSoubory = 'active';
                            break;
                        case "priority":
                            $activePriority = 'active';
                            break;
                        case "kontakty":
                            $activeKontakty = 'active';
                            break;
                    }
                }
            }
            $html = "
        <li class='nav-item $activeButt'>
         <a class=\"nav-link\" data-toggle=\"collapse\" href=\"#nastaveni\" aria-expanded=\"true\">
                <i class=\"material-icons\">settings</i>
                <p> Globální Nastavení
                    <b class=\"caret\"></b>
                </p>
            </a>           <div class=\"collapse $settShow\" id=\"nastaveni\" style=\"\">
                <ul class=\"nav\">
                    <li class=\"nav-item $activeFirmy\" >
                        <a class=\"nav-link\" href='nastaveni.php?sprava=firmy' >
                            <span class=\"sidebar-mini\"> SF </span>
                            <span class=\"sidebar-normal\"> Správa firem </span>
                        </a>
                    </li>
                     <li class=\"nav-item $activeKontakty\" >
                        <a class=\"nav-link\" href='nastaveni.php?sprava=kontakty' >
                            <span class=\"sidebar-mini\"> SK </span>
                            <span class=\"sidebar-normal\"> Správa kontaktů </span>
                        </a>
                    </li>
                    <li class=\"nav-item $activeZadanky\" >
                        <a class=\"nav-link\" href='nastaveni.php?sprava=zadanky' >
                            <span class=\"sidebar-mini\"> SŽ </span>
                            <span class=\"sidebar-normal\"> Správa žádanek </span>
                        </a>
                    </li>
                    <li class=\"nav-item $activePriority\" >
                        <a class=\"nav-link\" href='nastaveni.php?sprava=priority' >
                            <span class=\"sidebar-mini\"> PR </span>
                            <span class=\"sidebar-normal\"> Váhy priorit </span>
                        </a>
                    </li>
                    ";
            if ($_SESSION['role'] == 'adminEditor')
                $html .=
                    "<li class=\"nav-item $activeUzivatele\" >
                        <a class=\"nav-link\" href=\"nastaveni.php?sprava=uzivatele\" >
                            <span class=\"sidebar-mini\"> SU </span>
                            <span class=\"sidebar-normal\"> Správa uživatelů </span>
                        </a>
                    </li>
                    <li class=\"nav-item $activeLogy\" >
                        <a class=\"nav-link\" href=\"nastaveni.php?sprava=logy\" >
                            <span class=\"sidebar-mini\"> PU </span>
                            <span class=\"sidebar-normal\"> Přehled událostí </span>
                        </a>
                    </li>
                    <li class=\"nav-item $activeSoubory\" >
                        <a class=\"nav-link\" href=\"nastaveni.php?sprava=soubory\" >
                            <span class=\"sidebar-mini\"> SS </span>
                            <span class=\"sidebar-normal\"> Správa souborů </span>
                        </a>
                    </li>";

            $html .= "        
                </ul>
            </div>
          
        </li>
            ";
            echo $html;
        }

        ?>



    </ul>
</div>
</div>