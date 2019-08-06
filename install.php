<?php

// exit('halt'); // script not needed anymore after install and configuration are complete
/*
    This file is part of WebChess. http://webchess.sourceforge.net
	Copyright 2010 Jonathan Evraire, Rodrigo Flores, rigao

    WebChess is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WebChess is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WebChess.  If not, see <http://www.gnu.org/licenses/>.
*/

/*******************************************************************************
 *                                                                             *
 *        This block of functions creates the table-system of WebChess.        *
 *                                                                             *
 ******************************************************************************/
 
if (!isset($_POST['confirm']) && file_exists('config.php'))
     exit('Already installed.');
 
function createTables($server,$user,$pass,$DBname){

    $dbh = mysqli_connect($server, $user, $pass, $DBname);
    if (!$dbh) {
        exit('Connect Error: '. mysqli_connect_error().'<br>
            Check your databasse settings.');
    }

    echo "Table 'games' ";
	$SQLCreateTableGames = "CREATE TABLE IF NOT EXISTS games (
		gameID SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		whitePlayer MEDIUMINT NOT NULL,
		blackPlayer MEDIUMINT NOT NULL,
		gameMessage ENUM('', 'playerInvited', 'inviteDeclined', 'draw', 'playerResigned', 'checkMate') NULL,
		messageFrom ENUM('', 'black', 'white') NULL,
		dateCreated DATETIME NOT NULL,
		lastMove DATETIME NOT NULL
	);";
	$result = mysqli_query($dbh, $SQLCreateTableGames);
    if (!$result) exit(mysqli_error($dbh));
    else echo "has been created.<br>";

    echo "Table 'history' ";
	$SQLCreateTableHistory = "CREATE TABLE IF NOT EXISTS history (
		timeOfMove DATETIME NOT NULL,
		gameID SMALLINT NOT NULL,
		curPiece ENUM('pawn', 'bishop', 'knight', 'rook', 'queen', 'king') NOT NULL,
		curColor ENUM('white', 'black') NOT NULL,
		fromRow SMALLINT NOT NULL,
		fromCol SMALLINT NOT NULL,
		toRow SMALLINT NOT NULL,
		toCol SMALLINT NOT NULL,
		replaced ENUM('pawn', 'bishop', 'knight', 'rook', 'queen', 'king') NULL,
		promotedTo ENUM('pawn', 'bishop', 'knight', 'rook', 'queen', 'king') NULL,
		isInCheck BOOL NOT NULL,
		PRIMARY KEY(timeOfMove, gameID),
		INDEX idx_gameID (gameID)
	);";
	$result = mysqli_query($dbh, $SQLCreateTableHistory);
    if (!$result) exit(mysqli_error($dbh));
    else echo "has been created.<br>";

    echo "Table 'messages' ";
	$SQLCreateTableMessages = "CREATE TABLE IF NOT EXISTS messages (
		msgID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		gameID SMALLINT NOT NULL,
		msgType ENUM('undo', 'draw') NOT NULL,
		msgStatus ENUM('request', 'approved', 'denied') NOT NULL,
		destination ENUM('black', 'white') NOT NULL
	);";
	$result = mysqli_query($dbh, $SQLCreateTableMessages);
    if (!$result) exit(mysqli_error($dbh));
    else echo "has been created.<br>";

    echo "Table 'pieces' ";
	$SQLCreateTablePieces = "CREATE TABLE IF NOT EXISTS pieces (
		gameID SMALLINT NOT NULL,
		color ENUM('white','black') NOT NULL,
		piece ENUM('pawn','rook','knight','bishop','queen','king') NOT NULL,
		col SMALLINT NOT NULL,
		row SMALLINT NOT NULL,
		INDEX idx_gameID (gameID)
	);";
	$result = mysqli_query($dbh, $SQLCreateTablePieces);
    if (!$result) exit(mysqli_error($dbh));
    else echo "has been created.<br>";

    echo "Table 'players' ";
	$SQLCreateTablePlayers = "CREATE TABLE IF NOT EXISTS players (
		playerID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		password CHAR(60) NOT NULL,
		firstName varchar(64) NOT NULL,
		lastName varchar(64) NOT NULL,
		nick varchar(64) NOT NULL UNIQUE,
		userlevel tinyint(1) NOT NULL DEFAULT 1,
		lastAccess DATETIME
	);";
	$result = mysqli_query($dbh, $SQLCreateTablePlayers);
    if (!$result) exit(mysqli_error($dbh));
    else echo "has been created.<br>";

    echo "Table 'preferences' ";
	$SQLCreateTablePreferences = "CREATE TABLE IF NOT EXISTS preferences (
		playerID INT NOT NULL,
		preference CHAR(20) NOT NULL,
		value CHAR(50) NULL,
		PRIMARY KEY(playerID, preference)
	);";
	$result = mysqli_query($dbh, $SQLCreateTablePreferences);
    if (!$result) exit(mysqli_error($dbh));
    else echo "has been created.<br>";

    echo "Table 'communication' ";
	$SQLCreateTableCommunication = "CREATE TABLE IF NOT EXISTS communication (
	  commID smallint(6) NOT NULL auto_increment,
	  gameID smallint(6) default NULL,
	  fromID mediumint(9) default NULL,
	  toID mediumint(9) default NULL,
	  title varchar(255) default NULL,
	  text longtext,
	  postDate datetime NOT NULL default '0000-00-00 00:00:00',
	  expireDate datetime default '0000-00-00 00:00:00',
	  ack tinyint(4) default '0',
	  commType smallint(6) default '0',
	  PRIMARY KEY  (commID)
	);";
	$result = mysqli_query($dbh, $SQLCreateTableCommunication);
    if (!$result) exit(mysqli_error($dbh));
    else echo "has been created.";

    mysqli_close($dbh);
}

################################################################################
################################################################################
################################################################################
?>
<!DOCTYPE html>
<html lang="en">
<head>
     <title>Installing PHP7-Webchess</title>
</head>
<body>
<h1>PHP7-Webchess install</h1>
<?php
/* 
 * All mention to $logMsg has been commented out. Now the installer makes things
 * differently. Now it will show the result in the screen, because some times 
 * php won't have write-permissions and it will be problematic for the novice 
 * administrator to handle it.
 * It is possible, however, to have both ways: Show the info in the screen and then
 * write it to a log file. rigao (who is writing this) does rather be programing
 * something else in his available time, but feel free to code it yourself.
 */
/*$logMsg = "";
if(file_exists("install.log")) {
	// Enter this condition if file exists..
	echo "ERRROR: File \"install.log\" already exists!<br />";
	echo "  This probably means that Webchess is already installed.<br />";
	echo "  For security reasons, please remove the file first if you wish<br />";
	echo "  to proceed with install<br />";
	$logMsg.= "ERRROR: File \"install.log\" already exists!\n";
	$logMsg.= "  This probably means that Webchess is already installed.\n";
	$logMsg.= "  For security reasons, please remove the file first if you wish\n";
	$logMsg.= "  to proceed with install";
	writeLogFile($logMsg);
	exit(0);
}*/

/*
 * This installer will flow through this switch. It will start in the default,
 * then it will go to case 1, 2, 3 and finish. Each case has its own form which
 * will keep the values which need to be send to the next form and will change the
 * $_POST['confirm'] variable to access the correct case.
 *
 * The cases roughtly follow the steps shown in the installer. However, normally
 * the operations of each step will be made in the next case (so, the operations
 * which belong to step 1 will be made at the beginning of case 2).
 */
$postConfirm = '';
if (isset($_POST['confirm'])) {
    $postConfirm = $_POST['confirm'];
}
switch($postConfirm){
   //Step 1 of the installation procedure: Setup the database. The beginning of
   //case 2 has code of step 1 too.
   case 1:
      //Presentation of the installer.
      ?>
      <h2>Step 1: Submit the following values.</h2>

      <p>The database must have been created before this.</p>

      <?php // This form will be sent to case 2, to end step 1 and to start step 2. ?>
         <form action='install.php' method='POST' name='step1'><table>
            <tr><td>Server:</td><td><input type="text" name="server"/></td></tr>
            <tr><td>User:</td><td><input type="text" name="user"/></td></tr>
            <tr><td>Password:</td><td><input type="password" name="pass"/></td></tr>
            <tr><td>Database name:</td><td><input type="text" name="DBname"/></td></tr>
            <tr><td colspan="2"><input type='hidden' name='confirm' value='2' />
            <input type='submit' value='Continue' /></td></tr>
         </table></form>      
      <?php
      break;

/******************************************************************************/

   //This case features last part of the step 1 and the presentation of the Step 2
   // of the installation procedure.
   case 2:
      //We make the installation procedure from step 1.
      //If the database is not created yet, we will create it.
      //We start step 2 with its presentation. It will be completed in case 3.
      ?>
      <h2>Step 2</h2>
        <?php
        $dbh = mysqli_connect($_POST['server'], $_POST['user'], $_POST['pass']);
        if (!$dbh) {
            exit('Connect Error: '. mysqli_connect_error().'<br>
                Check your databasse settings.');
        }
        $result = mysqli_select_db($dbh, $_POST['DBname']);
        mysqli_close($dbh);
        if (!$result)
            exit('Database submitted does not exist.<br>
                You have to create a database.');
        ?>
      <p>You now have a database named '<?php echo $_POST['DBname']; ?>'<br>
          in which PHP7-Webchess will store its data.<br>
          In order to do so, we will now create the database tables.</p>

      <?php //This form will be sent to case 3, to end step 2 and to start step 3. ?>
         <form action='install.php' method='POST' name='step2'><table>
            <tr><td>
                  <input type="hidden" name="server" value="<?php echo $_POST['server']; ?>"/>
                  <input type="hidden" name="user" value="<?php echo $_POST['user']; ?>"/>
                  <input type="hidden" name="pass" value="<?php echo $_POST['pass']; ?>"/>
                  <input type="hidden" name="DBname" value="<?php echo $_POST['DBname']; ?>"/>
               </td></tr>
            <tr><td><input type='hidden' name='confirm' value='3' />
            <input type='submit' value='Continue' /></td></tr>
         </table></form>
      <?php 
      break;

/******************************************************************************/

   //This case features last part of the step 2 and the presentation of the Step 3
   // of the installation procedure.
   case 3:
      //We make the installation procedure from step 2.
      //Little code to handle if there is a new user to create the tables.

        $server = $_POST['server'];
        $user   = $_POST['user'];
        $pass   = $_POST['pass'];
        $DBname = $_POST['DBname'];

      //Once we know the user which will create the tables, we call the function
      //which will create them.
      createTables($server,$user,$pass,$DBname);

      // We start step 3 with this presentation. This step 3 is breaked into two
      // substeps. The first one will generate config.php, while the second one
      // will setup the user which will use WebChess to access MySQL.

      // Step 3.1:
      ?>
         <h2>Step 3: Configure your PHP7-Webchess installation.</h2>
         <p>Click the 'Generate config.php' button.<br>This will create a
             'config.php' file containing your configuration information.</p>
         <?php 
            //This form will generate the config.php file.
         ?>
        <form action='makeConfig.php' method='POST' name='generateConfigForm'><table>
            <input type="hidden" name="server" value="<?php echo $server; ?>"/>
            <input type="hidden" name="user" value="<?php echo $user; ?>"/>
            <input type="hidden" name="pass" value="<?php echo $pass; ?>"/>
            <input type="hidden" name="DBname" value="<?php echo $DBname; ?>"/>
            <tr><td colspan="2"><b><u>Server Settings:</u></b></td></tr>
            <tr><td>Time before session expires (seconds):</td><td><input type="text" name="timeout" value="900"/> (ex: 900)</td></tr>
            <tr><td>Time before a game expires (days):</td><td><input type="text" name="expire" value="14"/> (ex: 14)</td></tr>
            <tr><td>Minimum time interval for auto-reload (seconds):</td><td><input type="text" name="autoreload" value="10"/> (ex: 10)</td></tr>
            <tr><td>Use e-mail notification:</td><td><input type="checkbox" name="mail_not" value="1"/></td></tr>
            <tr><td>E-mail address:</td><td><input type="text" name="mail_adr"/> (ex: WebChess@example.com)</td></tr>
            <tr><td>Main Page address:</td><td><input type="text" name="url"/> (ex: http://webchess.sourceforge.net)</td></tr>
            <tr><td>Maximum active users:</td><td><input type="text" name="maxUsers" value="50"/> (ex: 50)</td></tr>
            <tr><td>Maximum active games:</td><td><input type="text" name="maxGames" value="50"/> (ex: 50)</td></tr>
            <tr><td>Nick changes allowed:</td><td><input type="checkbox" name="changeNick" value="1"/></td></tr>
            <tr><td>New users allowed:</td><td><input type="checkbox" name="newUsers" value="1" checked/></td></tr>
            <tr><td>Square size of the board (pixels):</td><td><input type="text" name="size" value="50"/>(ex: 50)</td></tr>
            <tr><td>Image extension:</td><td><select name="imageExtension" size="1">
                    <option value="png" selected>png<br>
                    <option value="gif">gif<br>
                </select></td></tr>
            <tr><td colspan="2"><input type='hidden' name='confirm' value='finish' />
            <input type='submit' value='Generate config.php' /></td></tr>
        </table></form>
        <?php 
        break;

/******************************************************************************/

   case 'finish':
      ?>
         <h2>The installation process has finished.</h2>

         <p>For secury reasons it is recommended that you now delete install.php
             and makeConfig.php from your web server.<br>While we tried to make
             sure there is no way to exploit it,<br>it is possible that we may have
             missed something,<br>therefore it is better to delete it once the
             installation is done.</p>

         <p>You <b>must</b> provide a link to the source
             code (even if you didn't change anything)<br>if you upload the
             application to a public server.</p>

         <p>Once any problems spotted during the installation process are
             solved,<br>go to the <a href='index.php'>login page</a> and create
             your first user!</p>

         <p><i>Thank you for playing PHP7-Webchess!</i></p>

      <?php 
      break;

/******************************************************************************/

    //Welcome page.
    default:
        //The begining of the installation process.
        if (version_compare(PHP_VERSION, '7.0.0', '<')) {
            exit('PHP7-Webchess requires at least <b>PHP 7</b><br>
                Your version is PHP '.PHP_VERSION);
        }
        ?>
        <p>In order to install PHP7-Webchess we will follow a 3-step procedure:</p>

        <ul><li>Step 1: Provide a username and password with database access 
            which will store all PHP7-Webchess data.</li>
        <li>Step 2: Provide a database name for to create
             the tables within the database.</li>
        <li>Step 3: Configure your PHP7-Webchess installation</li></ul>

        <form action="install.php" method='POST'>
        <input type="checkbox" name="confirm" value="1" /> I'm ready to proceed!
        <br />
        <input type="submit" value="Install Databases" />
        </form>
        <?php 
        break;
}
?>
</body>
</html>
