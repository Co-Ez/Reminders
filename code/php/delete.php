<?php

//USE YOUR OWN WORDS FOR THE COMMENTS, THIS IS JUST A TEMPLATE
//USE YOUR OWN VARIABLE NAMES
//DONT INCLUDE THIS COMMENT OR THE COMMENTS ABOVE, USE COMMON SENSE

$con = new mysqli("localhost", "root", "", "rigets_db"); //creates a new database connection to the user_information database
    //replace 'rigets_db' with the name of your database

if ($con->connect_error) { //checks if the connection to the database has failed/resulted in an error
    die("Connection failed: " . $con->connect_error); //terminates the current script and sends the error in the console
}

$stmt = $con->prepare("DELETE FROM userInformation WHERE {condition}");

if ($stmt->execute()) {
    //put something here for its success, for example:
    echo "Successfully deleted!";
}
else {
    //put something here for its failure, for example:
    echo "Unsuccessful, Try again later";
}

?>