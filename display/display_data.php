<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Water Hockey Team Data</title>
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel='stylesheet' type='text/css' href='../style/style.css'>
</head>
<body>
    <!-- Navigation bar -->
    <header>
        <nav>
            <ul>
                <li><a href="../WaterHockey.html">Home</a></li>
                <li><a href="../admin/edit_data.php">Data Admin</a></li>
            </ul>
        </nav>
    </header>
    <hr>

    <!-- Form content. Self referencing form. -->
    <section>
        <h2>Water Hockey Data Display</h2>
        <form method="POST" action="display_data.php" class="display_form">
            <p class="data_para">Select the team and type of data you want to display in relation to this team from the following options.</p>
            <!-- Radio buttons dictating which data to display -->
            <fieldset>
                <legend>Type of Data</legend>
                <input type="radio" name="datatype" value="teamData" required=""> Team Data
                <input type="radio" name="datatype" value="playerData"> Player Data
            </fieldset>
            <!-- Drop down list of each team. -->
            <fieldset>
                <legend>Team Selection</legend>
                <select name="team">
                    <optgroup label="Teams">
                    <!-- Blank option for first selection -->
                    <option></option>
                    <?php
                        // Connects to the database.
                        $db = new SQLite3("../database/underwater_hockey.db");

                        // For each team in the db, create an option tag with the value set to that team name.
                        $query = "SELECT teamName FROM Team";
                        $result = $db->query($query);
                        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                            echo '<option value="' . $row['teamName'] . '">' . $row['teamName'] . '</option>';
                        }
                        // Close the DB connection.
                        $db->close();
                    ?>
                    </optgroup>
                </select>
            </fieldset>
            <input type="submit" name="submit" value="Display Data" class="btn">
        </form>
    </section>

    <!-- Data display logic -->
    <?php
        // Checks if there is form data being submitted.
        if(isset($_POST['submit'])) {
            // Connects to the database.
            $db = new SQLite3("../database/underwater_hockey.db");
            
            // Checks to see if a radio button has been checked via server side validation, or if a value has been selected from the drop down list, if not display a message informing the user, otherwise carry out data display logic.
            if (!isset($_POST['datatype']) || empty($_POST['team'])) {
                echo "<h3 class='error'>Please select which data you want to display via the radio buttons and drop down list.</h3>";
            } else {
                // Stores the values of the radio buttons and drop down box values.
                $dataType = SQLite3::escapeString($_POST['datatype']);
                $team = SQLite3::escapeString($_POST['team']);

                // Begins html output.
                echo "<section>";

                if ($dataType == "teamData") {
                    //---------------------------
                    // DISPLAYS INDIVIDUAL TEAM DATA.
                    //---------------------------
                    // Prepared query for selecting team data according to user requested team.
                    $teamQuery = $db->prepare("SELECT * FROM Team WHERE teamName = :t");
                    $teamQuery->bindParam(":t", $team);
                    // As there is only one team that matches the user input, value is fetched as it is applied to the result variable.
                    $teamResult = $teamQuery->execute()->fetchArray(SQLITE3_ASSOC);
                    // Stores the team's pool name.
                    $teamPool = $teamResult['teamPool'];

                    // Prepared query for selecting pool data according to team's pool.
                    $poolQuery = $db->prepare("SELECT poolName, length, address FROM Pool WHERE poolName = :p");
                    $poolQuery->bindParam(":p", $teamPool);
                    // As there is only one pool that matches, value is fetched as it is applied to the result variable.
                    $poolResult = $poolQuery->execute()->fetchArray(SQLITE3_ASSOC);

                    // Prepared query for selecting game data according to team.
                    $gameQuery = $db->prepare("SELECT * FROM Game WHERE teamA = :t OR teamB = :t");
                    $gameQuery->bindParam(":t", $team);
                    $gameResult = $gameQuery->execute();
                    
                    // Display team data with output html.
                    echo "<h2>$team</h2>";
                    echo "<div class='display_container'>";
                    echo "<h4 class='display_h4'>Manager</h4>";
                    echo "<p>" . $teamResult['manager'] . "</p>";
                    echo "<hr class='display_hr'>";
                    echo "<h4 class='display_h4'>Home Pool</h4>";
                    echo "<p><span class='display_span'>Name:</span> " . $poolResult['poolName'] . "</p>";
                    echo "<p><span class='display_span'>Length:</span> " . $poolResult['length'] . " metres</p>";
                    echo "<p><span class='display_span'>Address:</span> " . $poolResult['address'] . "</p>";
                    echo "<hr class='display_hr'>";
                    echo "<h4 class='display_h4'>Team Games</h4>";
                    // Loop through gameResult query array and output each games data.
                    while ($row = $gameResult->fetchArray(SQLITE3_ASSOC)) {
                        // Checks to see which team listed in the row is the opposing team and stores in a variable.
                        if ($row['teamA'] == $team) {
                            $opposingTeam = $row['teamB'];
                            $opposingTeamScore = $row['scoreB'];
                            $teamScore = $row['scoreA'];
                        } else {
                            $opposingTeam = $row['teamA'];
                            $opposingTeamScore = $row['scoreA'];
                            $teamScore = $row['scoreB'];
                        }

                        echo "<p><span class='display_span'>Game Pool:</span> " . $row['gamePool'] . "</p>";
                        // Gets the game date and formats to dd/mm/YYY. Uses the strtotime() funtction as the date column is stored as text in the DB.
                        echo "<p><span class='display_span'>Date:</span> " . strftime('%d/%m/%Y', strtotime($row['date'])) . "</p>";
                        echo "<p><span class='display_span'>Opposing Team:</span> " . $opposingTeam . "</p>";
                        echo "<p><span class='display_span'>Team Score:</span> " . $teamScore . "</p>";
                        echo "<p><span class='display_span'>Opposing Team Score:</span> " . $opposingTeamScore . "</p>";
                        echo "<p>-----------------</p>";
                    }

                } else if ($dataType == "playerData") {
                    //---------------------------
                    // DISPLAYS ALL MEMBERS OF AN INDIVIDUAL TEAM.
                    //---------------------------
                    // Prepared queries for selecting player names and the team manager according to user requested team.
                    $playerQuery = $db->prepare("SELECT givenName, familyName FROM Player WHERE playerTeam = :t");
                    $teamQuery = $db->prepare("SELECT manager FROM Team WHERE teamName = :t");
                    $playerQuery->bindParam(":t", $team);
                    $teamQuery->bindParam(":t", $team);

                    // Results of the prepared queries.
                    $playerResult = $playerQuery->execute();
                    // As there is only one manager per team, value is fetched as it is applied to the result variable.
                    $teamResult = $teamQuery->execute()->fetchArray(SQLITE3_ASSOC);

                    // Display team members with output html.
                    echo "<div class='display_container'>";
                    echo "<h2>" . $team . " Members</h2>";
                    echo "<h4 class='display_h4'>Manager</h4>";
                    echo "<p>" . $teamResult['manager'] . "</p>";
                    echo "<hr class='display_hr'>";
                    echo "<h4 class='display_h4'>Players</h4>";
                    // Loop through playerResult query array and output each player's name.
                    while ($row = $playerResult->fetchArray(SQLITE3_ASSOC)) {
                        echo "<p>" . $row['givenName'] . " " . $row['familyName'] . "</p>";
                    }
                }
            }
            echo "</div>";
            // Ends html output.
            echo "</section>";
            // Close the DB connection.
            $db->close();
        }
    ?>    
</body>
</html>