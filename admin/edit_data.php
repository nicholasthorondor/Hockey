<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Edit Water Hockey Data</title>
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel='stylesheet' type='text/css' href='../style/style.css'>
</head>
<body>
    <!-- Navigation bar -->
    <header>
        <nav>
            <ul>
                <li><a href="../WaterHockey.html">Home</a></li>
                <li><a href="../display/display_data.php">Display Data</a></li>
                <li><a href="schema.html">DB Schema</a></li>
            </ul>
        </nav>
    </header>
    <hr>

    <section>
        <!-- DB table selection buttons. Self referencing form. -->
        <form method="POST" action="edit_data.php" class="edit_form">
            <h2>Data Administration</h2>
            <p>Select the database table you want to edit below.</p>
            <button name="team" class="btn table_select_btn">Team</button>
            <button name="player" class="btn table_select_btn">Player</button>
            <button name="pool" class="btn table_select_btn">Pool</button>
            <button name="game" class="btn table_select_btn">Game</button>
        </form>
    </section>

    <section class="edit_container">
        <!-- Edit and display DB content form fields. Self referencing form-->
        <form method="POST" action="edit_data.php">
        <?php
            // Connects to the database.
            $db = new SQLite3("../database/underwater_hockey.db");

            // Sets the text of the add entry button depending on whether a entry is being added or edited.
            // Also sets a boolean value indicating wheher the user is in edit mode or not.
            if (isset($_POST['Teamedit']) || isset($_POST['Playeredit']) || isset($_POST['Pooledit']) || isset($_POST['Gameedit'])) {
                $editing = true;
                $btnText = "Edit";
            } else {
                $editing = false;
                $btnText = "Add Entry";
            }

            // Sets the table variable value to equal the table being viewed/edited.
            // By setting POST variable to certain values for each form input prevents the need for using GET, therefore preventing the user from directly manipulating GET data in the URL.
            // Each IF block relates to one of the four tables, and the variety of input available, eg. edit, cancel, delete, add, view etc.
            if (isset($_POST['team']) || isset($_POST['Teamedit']) || isset($_POST['teamCancel']) || isset($_POST['addTeamEntry']) || isset($_POST['teamSubmit']) || isset($_POST['Teamdelete'])) {
                $table = "Team";
            } else if (isset($_POST['player']) || isset($_POST['Playeredit']) || isset($_POST['playerCancel']) || isset($_POST['addPlayerEntry']) || isset($_POST['playerSubmit']) || isset($_POST['Playerdelete'])) {
                $table = "Player";
            } else if (isset($_POST['pool']) || isset($_POST['Pooledit']) || isset($_POST['poolCancel']) || isset($_POST['addPoolEntry']) || isset($_POST['poolSubmit']) || isset($_POST['Pooldelete'])) {
                $table = "Pool";
            } else if (isset($_POST['game']) || isset($_POST['Gameedit']) || isset($_POST['gameCancel']) || isset($_POST['addGameEntry']) || isset($_POST['gameSubmit']) || isset($_POST['Gamedelete'])) {
                $table = "Game";
            } else {
                $table = null;
            }

            // Create an Add DB entry button, using $table variable as part of the name to differentiate each button per DB table.
            if ($table != null) {
                echo '<button name=add'.$table.'Entry'. ' '.DisableBtn().' class="btn add_btn">Add Entry</button><br>';
            }

            //------------
            // SUBMIT ADDED/EDITED ENTRY TO DB
            //------------
            // If there is a submit value from one of the tables set in POST and that submit value is not empty, carry out DB insertion logic.
            if (isset($_POST['teamSubmit']) && !empty($_POST['teamSubmit']) || isset($_POST['playerSubmit']) && !empty($_POST['playerSubmit']) || isset($_POST['poolSubmit']) && !empty($_POST['poolSubmit']) || isset($_POST['gameSubmit']) && !empty($_POST['gameSubmit'])) {

                // If the user is submitting an edited field to the DB, pass in the associated rowid into the DBInsert() function according to the currently selected table, otherwise pass in null for adding a new entry into the DB.

                if ($table == "Team") {
                    // Editing a Team table row.
                    if ($_POST['teamSubmit'] == "Edit") {
                        // Uses the rowid hidden field value to recognise which row in the table to update.
                        // Hidden field is defined in GenerateEditFields() function.
                        DBInsert("Team", $_POST['rowid']);
                    } else {
                        // Adding a new Team table entry.
                        DBInsert("Team", null);
                    }

                } else if ($table == "Player") {
                    // Editing a Player table row.
                    if ($_POST['playerSubmit'] == "Edit") {
                        // Uses the rowid hidden field value to recognise which row in the table to update.
                        // Hidden field is defined in GenerateEditFields() function.
                        DBInsert("Player", $_POST['rowid']);
                    } else {
                        // Adding a new Player table entry.
                        DBInsert("Player", null);
                    }

                } else if ($table == "Pool") {
                    // Editing a Pool table row.
                    if ($_POST['poolSubmit'] == "Edit") {
                        // Uses the rowid hidden field value to recognise which row in the table to update.
                        // Hidden field is defined in GenerateEditFields() function.
                        DBInsert("Pool", $_POST['rowid']);
                    } else {
                        // Adding a new Pool table entry.
                        DBInsert("Pool", null);
                    }

                } else if ($table == "Game") {
                    // Editing a Game table row.
                    if ($_POST['gameSubmit'] == "Edit") {
                        // Uses the rowid hidden field value to recognise which row in the table to update.
                        // Hidden field is defined in GenerateEditFields() function.
                        DBInsert("Game", $_POST['rowid']);
                    } else {
                        // Adding a new Game table entry.
                        DBInsert("Game", null);
                    }
                }
            }

            //------------
            // DELETE RECORD FROM DB
            //------------
            // Checks to see if the POST data contains variables for each of the delete inputs for the four tables.
            if (isset($_POST['Teamdelete']) || isset($_POST['Playerdelete']) || isset($_POST['Pooldelete']) || isset($_POST['Gamedelete'])) {
                // Calls the DeleteDBEntry() funtion and passes in the currently selected table and the value of the POST delete variable.
                // The value of the POST delete variable is set to the rowid of the row that has been requested to be deleted.
                // This rowid is set in the DisplayDbTableContent() function when the html elements are dynamically created.
                if ($table == "Team") {
                    DeleteDBEntry("Team", $_POST['Teamdelete']);
                } else if ($table == "Player") {
                    DeleteDBEntry("Player", $_POST['Playerdelete']);
                } else if ($table == "Pool") {
                    DeleteDBEntry("Pool", $_POST['Pooldelete']);
                } else if ($table == "Game") {
                    DeleteDBEntry("Game", $_POST['Gamedelete']);
                }
            }

            //------------
            // DISPLAY EDIT/ADDENTRY FORM FIELDS
            //------------
            if (isset($_POST['Teamedit']) || isset($_POST['addTeamEntry'])) {

                // Call function to dynamically generate form fields for editing/adding an entry to the Team DB table.
                // Which function is called depends on if the user is in editing mode or not.
                if ($editing) {
                    GenerateEditFields("Team", SQLite3::escapeString($_POST['Teamedit']));
                } else {
                    GenerateAddEntryFields("Team");
                }
                // Submit and cancel button.
                echo '<input type="submit" name="teamSubmit" value="'.$btnText.'" class="btn edit_btn">';
                // Cancel button allows edit form to be cancelled using formnovalidate.
                echo "<button name='teamCancel' formnovalidate class='btn edit_btn del'>Cancel</button>";

            } else if (isset($_POST['Playeredit']) || isset($_POST['addPlayerEntry'])) {

                // Call function to dynamically generate form fields for editing/adding an entry to the Player DB table.
                // Which function is called depends on if the user is in editing mode or not.
                if ($editing) {
                    GenerateEditFields("Player", SQLite3::escapeString($_POST['Playeredit']));
                } else {
                    GenerateAddEntryFields("Player");
                }
                // Submit and cancel button.
                echo '<input type="submit" name="playerSubmit" value="'.$btnText.'" class="btn edit_btn">';
                // Cancel button allows edit form to be cancelled using formnovalidate.
                echo "<button name='playerCancel' formnovalidate class='btn edit_btn del'>Cancel</button>";

            } else if (isset($_POST['Pooledit']) || isset($_POST['addPoolEntry'])) {

                // Call function to dynamically generate form fields for editing/adding an entry to the Pool DB table.
                // Which function is called depends on if the user is in editing mode or not.
                if ($editing) {
                    GenerateEditFields("Pool", SQLite3::escapeString($_POST['Pooledit']));
                } else {
                    GenerateAddEntryFields("Pool");
                }
                // Submit and cancel button.
                echo '<input type="submit" name="poolSubmit" value="'.$btnText.'" class="btn edit_btn">';
                // Cancel button allows edit form to be cancelled using formnovalidate.
                echo "<button name='poolCancel' formnovalidate class='btn edit_btn del'>Cancel</button>";

            } else if (isset($_POST['Gameedit']) || isset($_POST['addGameEntry'])) {

                // Call function to dynamically generate form fields for editing/adding an entry to the Game DB table.
                // Which function is called depends on if the user is in editing mode or not.
                if ($editing) {
                    GenerateEditFields("Game", SQLite3::escapeString($_POST['Gameedit']));
                } else {
                    GenerateAddEntryFields("Game");
                }
                // Submit and cancel button.
                echo "<br>";
                echo "<div class='game_edit_btns'>";
                echo '<input type="submit" name="gameSubmit" value="'.$btnText.'" class="btn edit_btn">';
                // Cancel button allows edit form to be cancelled using formnovalidate.
                echo "<button name='gameCancel' formnovalidate class='btn edit_btn del'>Cancel</button>";
                echo "</div>";
            }

            //------------
            // DISPLAY DB TABLE CONTENT
            //------------
            // Display the DB content relating to the table the user selected.
            // as long as the table variable is not null.
            if ($table != null) {
                DisplayDBTableContent(SQLite3::escapeString($table));
            }

            // Closes the DB connection.
            $db->close();
        ?>
        </form>
    </section>

    <!-- JS code for simple deletion confirmation box. -->
    <script type="text/javascript">
        function DeleteConfirm() {
            return confirm("Are you sure you want to delete this record?");
        }
    </script>
</body>
</html>

<!-- PHP FUNCTIONS -->
<?php
    /*
    ** Dynamically generates an html table dispaying the content of the DB table being passed in as a parameter.
    ** @param $table the table the html fields will be relating to.
    */
    function DisplayDBTableContent ($table) {
        // Access the global DB connection.
        global $db;
        // Prepared query for selecting DB table data according to passed in table parameter.
        // Also gets each rows rowid using the built in SLQite rowid functionality.
        if ($table == "Team") {
            $query = $db->prepare("SELECT rowid, * FROM $table ORDER BY teamName ASC");
        } else if ($table == "Player") {
            $query = $db->prepare("SELECT rowid, * FROM $table ORDER BY playerTeam ASC");
        } else if ($table == "Pool") {
            $query = $db->prepare("SELECT rowid, * FROM $table ORDER BY poolName ASC");
        } else if ($table == "Game") {
            $query = $db->prepare("SELECT rowid, * FROM $table ORDER BY date ASC");
        }
        // $query = $db->prepare("SELECT rowid, * FROM $table");
        $result = $query->execute();

        // Creates an html table and sets the first row to display the DB tables headers according to the table passed as the parameter.
        echo "<table>";
        if ($table == "Team") {
            echo "<tr>";
            echo "<th>Team Name</th>";
            echo "<th>Manager</th>";
            echo "<th>Team Pool</th>";
        } else if ($table == "Player") {
            echo "<tr>";
            echo "<th>Given Name</th>";
            echo "<th>Family Name</th>";
            echo "<th>DOB</th>";
            echo "<th>Handed</th>";
            echo "<th>Player Team</th>";
        } else if ($table == "Pool") {
            echo "<tr>";
            echo "<th>Pool Name</th>";
            echo "<th>Length</th>";
            echo "<th>Address</th>";
        } else if ($table == "Game") {
            echo "<tr>";
            echo "<th>Game Pool</th>";
            echo "<th>Date (yyyy/mm/dd)</th>";
            echo "<th>Team A</th>";
            echo "<th>Team B</th>";
            echo "<th>Score A</th>";
            echo "<th>Score B</th>";
        }
        // Loops through each row of the table and outputs html to display table contents.
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            // Used to keep track of the row number by using the built in rowid functionality of SQLite.
            $rowid = $row['rowid'];
            // For all rows output rows according to the DB tables row data.
            echo "<tr>";
            foreach ($row as $key => $field) {
                // Display the fields data in a table column, except if it is the rowid.
                if ($key != 'rowid') {
                    echo "<td>" . $field . "</td>";
                }
            }
            // Outputs edit and delete buttons for each table row. Uses the DisableBtn() function to disable the button if the user is editing a record.
            // Value of each button is set to the associated DB entries rowid, allowing for queries to work of this rowid number.
            // The delete buttons incorporate the simple javascript function for confirming if the deletion should take place or not.
            echo '<td><button name='.$table.'edit value='.$rowid .' '.DisableBtn().' class="btn edit_btn">Edit</button></td>';
            echo '<td><button name='.$table.'delete value='.$rowid.' '.DisableBtn().' onclick="return DeleteConfirm()" class="btn edit_btn del">Delete</button></td>';
            echo "</tr>";
        }
        echo "</table>";
    }

    /*
    ** Dynamically generates form fields relating to the table the user has selected, fields are blank.
    ** @param $table the table the form fields will be relating to.
    */
    function GenerateAddEntryFields($table) {
        if ($table == "Team") {

            // Dynamically generate form fields for adding an entry to the Team DB table.
            echo "<span class='input_text'>Team Name:</span> <input type='text' name='teamName' required class='edit_input'>";
            echo "<span class='input_text'>Manager:</span> <input type='text' name='manager' required class='edit_input'>";
            echo "<span class='input_text'>Team Pool:</span> <input type='text' name='teamPool' required class='edit_input'>";

        } else if ($table == "Player") {

            // Dynamically generate form fields for adding an entry to the Player DB table.
            echo "<span class='input_text'>Given Name:</span> <input type='text' name='givenName' required class='edit_input'>";
            echo "<span class='input_text'>Family Name:</span> <input type='text' name='familyName' required class='edit_input'>";
            echo "<span class='input_text'>DOB:</span> <input type='date' name='dob' required class='edit_input'>";
            echo "<span class='input_text'>Handed:</span> <input type='text' name='handed' class='edit_input'>";
            echo "<span class='input_text'>Player Team:</span> <input type='text' name='playerTeam' required class='edit_input'>";

        } else if ($table == "Pool") {

            // Dynamically generate form fields for adding an entry to the Pool DB table.
            echo "<span class='input_text'>Pool Name:</span> <input type='text' name='poolName' required class='edit_input'>";
            echo "<span class='input_text'>Length:</span> <input type='number' name='length' required class='edit_input'>";
            echo "<span class='input_text'>Address:</span> <input type='text' name='address' required class='edit_input'>";

        } else if ($table == "Game") {

            // Dynamically generate form fields for adding an entry to the Game DB table.
            echo "<span class='input_text'>Game Pool:</span> <input type='text' name='gamePool' required class='edit_input'>";
            echo "<span class='input_text'>Date:</span> <input type='date' name='date' required class='edit_input'>";
            echo "<span class='input_text'>Team A:</span> <input type='text' name='teamA' required class='edit_input'>";
            echo "<span class='input_text'>Team B:</span> <input type='text' name='teamB' required class='edit_input'>";
            echo "<span class='input_text'>Score A:</span> <input type='number' name='scoreA' required class='edit_input'>";
            echo "<span class='input_text'>Score A:</span> <input type='number' name='scoreB' required class='edit_input'>";
        }
    }

    /*
    ** Dynamically generates form fields relating to the table the user has selected and fills with data relating to the entry the user selected to edit.
    ** @param $table the table the form fields will be relating to.
    ** @param $row the row of the DB table that will be edited.
    */
    function GenerateEditFields($table, $row) {
        // Access the global DB connection.
        global $db;
        // Prepared query for selecting DB table data.
        $query = $db->prepare("SELECT rowid, * FROM $table WHERE rowid = $row");
        // As there is only one row that matches the rowid, value is fetched as it is applied to the result variable.
        $result = $query->execute()->fetchArray(SQLITE3_ASSOC);

        // Generate form fields accoridng to the $table parameter.
        if ($table == "Team") {

            // Dynamically generate form fields for editing the Team DB table, prefilled with existing row data.
            echo "<input type=hidden name=rowid value=$row>"; // Hidden form field for storing rowid of edited field.
            echo '<span class="input_text">Team Name:</span> <input type=text name=teamName value="'.$result['teamName'].'" required class="edit_input">';
            echo '<span class="input_text">Manager:</span> <input type=text name=manager value="'.$result['manager'].'" required class="edit_input">';
            echo '<span class="input_text">Team Pool:</span> <input type=text name=teamPool value="'.$result['teamPool'].'" required class="edit_input">';

        } else if ($table == "Player") {

            // Dynamically generate form fields for editing the Player DB table, prefilled with existing row data.
            echo "<input type=hidden name=rowid value=$row>"; // Hidden form field for storing rowid of edited field.
            echo '<span class="input_text">Given Name:</span> <input type=text name=givenName value="'.$result['givenName'].'" required class="edit_input">';
            echo '<span class="input_text">Family Name:</span> <input type=text name=familyName value="'.$result['familyName'].'" required class="edit_input">';
            echo '<span class="input_text">DOB:</span> <input type=date name=dob value="'.$result['dob'].'" required class="edit_input">';
            echo '<span class="input_text">Handed:</span> <input type=text name=handed value="'.$result['handed'].'" class="edit_input">';
            echo '<span class="input_text">Player Team:</span> <input type=text name=playerTeam value="'.$result['playerTeam'].'" required class="edit_input">';

        } else if ($table == "Pool") {

            // Dynamically generate form fields for editing the Pool DB table, prefilled with existing row data.
            echo "<input type=hidden name=rowid value=$row>"; // Hidden form field for storing rowid of edited field.
            echo '<span class="input_text">Pool Name:</span> <input type=text name=poolName value="'.$result['poolName'].'" required class="edit_input">';
            echo '<span class="input_text">Length:</span> <input type=number name=length value="'.$result['length'].'" required class="edit_input">';
            echo '<span class="input_text">Address:</span> <input type=text name=address value="'.$result['address'].'" required class="edit_input">';

        } else if ($table == "Game") {

            // Dynamically generate form fields for editing the Game DB table, prefilled with existing row data.
            echo "<input type=hidden name=rowid value=$row>"; // Hidden form field for storing rowid of edited field.
            echo '<span class="input_text">Game Pool:</span> <input type=text name=gamePool value="'.$result['gamePool'].'" required class="edit_input">';
            echo '<span class="input_text">Date:</span> <input type=date name=date value="'.$result['date'].'" required class="edit_input">';
            echo '<span class="input_text">Team A:</span> <input type=text name=teamA value="'.$result['teamA'].'" required class="edit_input">';
            echo '<span class="input_text">Team B:</span> <input type=text name=teamB value="'.$result['teamB'].'" required class="edit_input">';
            echo '<span class="input_text">Score A:</span> <input type=number name=scoreA value="'.$result['scoreA'].'" required class="edit_input">';
            echo '<span class="input_text">Score B:</span> <input type=number name=scoreB value="'.$result['scoreB'].'" required class="edit_input">';
        }
    }

    /*
    ** Used to disable an html element if the user is editing or adding a table entry.
    */
    function DisableBtn () {
        if (isset($_POST['Teamedit']) || isset($_POST['Playeredit']) || isset($_POST['Pooledit']) || isset($_POST['Gameedit']) || isset($_POST['addTeamEntry']) || isset($_POST['addPlayerEntry']) || isset($_POST['addPoolEntry']) || isset($_POST['addGameEntry'])) {
            return "disabled";
        }
    }

    /*
    ** Inserts new or edited data into the DB according to the currently selected table and/or row being edited.
    ** @param $table the table the form fields will be relating to.
    ** @param $row the row of the DB table that will be edited.
    */
    function DBInsert($table, $row) {
        // Access the global DB connection.
        global $db;
        // Clean table variable, containing currently selected table.
        $insertTable = SQLite3::escapeString($table);
        // Clean rowid being passed in.
        $rowid = SQLite3::escapeString($row);

            if ($table == "Team") {
                //-------
                // Insert data into the Team table of the DB.
                //-------
                // Get clean input field values.
                $teamName = SQLite3::escapeString($_POST['teamName']);
                $manager = SQLite3::escapeString($_POST['manager']);
                $teamPool = SQLite3::escapeString($_POST['teamPool']);

                // Check if user is submitting edited team data or adding a new entry.
                if ($_POST['teamSubmit'] == "Edit") {
                    // Carry out edit team entry query.
                    // Prepared query for inserting edited DB table data.
                    $query = $db->prepare("UPDATE $insertTable SET teamName = '$teamName', manager = '$manager', teamPool = '$teamPool' WHERE rowid = $rowid");
                } else {
                    // Carry out add team entry query.
                    // Prepared query for inserting DB table data.
                    $query = $db->prepare("INSERT INTO $insertTable (teamName, manager, teamPool) VALUES ('$teamName', '$manager', '$teamPool')");
                }

            } else if ($table == "Player") {
                //-------
                // Insert data into the Player table of the DB.
                //-------
                // Get clean input field values.
                $givenName = SQLite3::escapeString($_POST['givenName']);
                $familyName = SQLite3::escapeString($_POST['familyName']);
                $dob = SQLite3::escapeString($_POST['dob']);
                $handed = SQLite3::escapeString($_POST['handed']);
                $playerTeam = SQLite3::escapeString($_POST['playerTeam']);

                // Check if user is submitting edited player data or adding a new entry.
                if ($_POST['playerSubmit'] == "Edit") {
                    // Carry out edit player entry query.
                    // Prepared query for inserting edited DB table data.
                    $query = $db->prepare("UPDATE $insertTable SET givenName = '$givenName', familyName = '$familyName', dob = '$dob', handed = '$handed', playerTeam = '$playerTeam' WHERE rowid = $rowid");
                } else {
                    // Carry out add player entry query.
                    // Prepared query for inserting DB table data.
                    $query = $db->prepare("INSERT INTO $insertTable (givenName, familyName, dob, handed, playerTeam) VALUES ('$givenName', '$familyName', '$dob', '$handed', '$playerTeam')");
                }

            } else if ($table == "Pool") {
                //-------
                // Insert data into the Pool table of the DB.
                //-------
                // Get clean input field values.
                $poolName = SQLite3::escapeString($_POST['poolName']);
                $length = SQLite3::escapeString($_POST['length']);
                $address = SQLite3::escapeString($_POST['address']);

                // Check if user is submitting edited pool data or adding a new entry.
                if ($_POST['poolSubmit'] == "Edit") {
                    // Carry out edit pool entry query.
                    // Prepared query for inserting edited DB table data.
                    $query = $db->prepare("UPDATE $insertTable SET poolName = '$poolName', length = $length, address = '$address' WHERE rowid = $rowid");
                } else {
                    // Carry out add pool entry query.
                    // Prepared query for inserting DB table data.
                    $query = $db->prepare("INSERT INTO $insertTable (poolName, length, address) VALUES ('$poolName', $length, '$address')");
                }

            } else if ($table == "Game") {
                //-------
                // Insert data into the Game table of the DB.
                //-------
                // Get clean input field values.
                $gamePool = SQLite3::escapeString($_POST['gamePool']);
                $date = SQLite3::escapeString($_POST['date']);
                $teamA = SQLite3::escapeString($_POST['teamA']);
                $teamB = SQLite3::escapeString($_POST['teamB']);
                $scoreA = SQLite3::escapeString($_POST['scoreA']);
                $scoreB = SQLite3::escapeString($_POST['scoreB']);

                // Check if user is submitting edited game data or adding a new entry.
                if ($_POST['gameSubmit'] == "Edit") {
                    // Carry out edit pool entry query.
                    // Prepared query for inserting edited DB table data.
                    $query = $db->prepare("UPDATE $insertTable SET gamePool = '$gamePool', date = '$date', teamA = '$teamA', teamB = '$teamB', scoreA = $scoreA, scoreB = $scoreB WHERE rowid = $rowid");
                } else {
                    // Carry out add pool entry query.
                    // Prepared query for inserting DB table data.
                    $query = $db->prepare("INSERT INTO $insertTable (gamePool, date, teamA, teamB, scoreA, scoreB) VALUES ('$gamePool', '$date', '$teamA', '$teamB', $scoreA, $scoreB)");
                }
            }

        // Execute DB insertion, and set appropriate message.
        if ($query->execute()) {
            $message = "<p class='message'>Database updated successfully.</p>";
        } else {
            $message = "<p class='message error'>An error occured, no change has taken place.</p>";
        }

        // Displays a DB insertion message.
        echo $message;
    }

    /*
    ** Deletes a row from the DB according to the selected table and row the user has chosen.
    ** @param $table the table the form fields will be relating to.
    ** @param $row the row of the DB table that will be edited.
    */
    function DeleteDBEntry($table, $row) {
        // Access the global DB connection.
        global $db;
        // Clean table variable, containing currently selected table.
        $deleteTable = SQLite3::escapeString($table);
        // Clean rowid being passed in.
        $rowid = SQLite3::escapeString($row);

        // Prepared query for deleting DB table data according to rowid passed into the function.
        $query = $db->prepare("DELETE FROM $deleteTable WHERE rowid = $rowid");

        // Execute DB insertion, and set appropriate message.
        if ($query->execute()) {
            $message = "<p class='message'>Record deleted successfully.</p>";
        } else {
            $message = "<p class='message error'>An error occured, no change has taken place.</p>";
        }

        // Displays a DB insertion message.
        echo $message;
    }
?>