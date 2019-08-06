<?php

if (file_exists('config.php'))
    exit('Already installed.');
if (!isset($_POST['url']))
    exit();
if (empty($_POST['url']))
    exit('<b>Main Page address</b> was not submitted.<br>Try again.');

if (isset($_POST['mail_not']) && $_POST['mail_not'] == '1') $useemail = 'true';
else $useemail = 'false';
if (isset($_POST['changeNick']) && $_POST['changeNick'] == '1') $nickchange = 'true';
else $nickchange = 'false';
if (isset($_POST['newUsers']) && $_POST['newUsers'] == '1') $newusers = 'true';
else $newusers = 'false';

$write = <<<PART
<?php\n
\$_CONFIG = true;\n
/* database settings */
\$CFG_SERVER = "{$_POST['server']}";
\$CFG_USER = "{$_POST['user']}";
\$CFG_PASSWORD = "{$_POST['pass']}";
\$CFG_DATABASE = "{$_POST['DBname']}";
/* server settings */
\$CFG_SESSIONTIMEOUT = "{$_POST['timeout']}";
\$CFG_EXPIREGAME = "{$_POST['expire']}";
\$CFG_MINAUTORELOAD = "{$_POST['autoreload']}";
\$CFG_USEEMAILNOTIFICATION = $useemail;
\$CFG_MAILADDRESS = "{$_POST['mail_adr']}";
\$CFG_MAINPAGE = "{$_POST['url']}";
\$CFG_MAXUSERS = "{$_POST['maxUsers']}";
\$CFG_MAXACTIVEGAMES = "{$_POST['maxGames']}";
\$CFG_NICKCHANGEALLOWED = $nickchange;
\$CFG_NEW_USERS_ALLOWED = $newusers;
\$CFG_BOARDSQUARESIZE = "{$_POST['size']}";
\$CFG_IMAGE_EXT = "{$_POST['imageExtension']}";

/* Application constants */
define('APP_NAME', 'PHP7-Webchess'); // The name of the app
define('APP_VERSION', '1.1.0'); // The version of the app

/* I18N constants */
define('I18N_GETTEXT_SUPPORT', false); // enable gettext for fetching translations
define('I18N_LOCALE', 'de_DE'); // locale to use (requires the webchess.mo file)

/* mysql table names */
define('communication', 'communication');
define('history', 'history');
define('games', 'games');
define('messages', 'messages');
define('pieces', 'pieces');
define('preferences', 'preferences');
define('players', 'players');

/* mysql table names */
\$CFG_TABLE[communication] = "communication";
\$CFG_TABLE[games] = "games";
\$CFG_TABLE[history] = "history";
\$CFG_TABLE[messages] = "messages";
\$CFG_TABLE[pieces] = "pieces";
\$CFG_TABLE[players] = "players";
\$CFG_TABLE[preferences] = "preferences";

?>
PART;

file_put_contents('config.php', $write);

?>
         <p>Now the config.php is created<br>
              click the 'Finish' button to end the installation.</p>

         <form action='install.php' method='POST' name='finish'>
            <input type='hidden' name='confirm' value='finish' />
            <input type='submit' value='Finish' />
         </form>
