<!--
    Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

    Tip 2: you can also add an image using data-image tag
-->
<div class="sidebar" data-color="white" data-background-color="red" >

<div class="logo">
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




    </ul>
</div>
</div>