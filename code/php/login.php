<?php

//USE YOUR OWN WORDS FOR THE COMMENTS, THIS IS JUST A TEMPLATE
//USE YOUR OWN VARIABLE NAMES
//DONT INCLUDE THIS COMMENT OR THE COMMENTS ABOVE, USE COMMON SENSE

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //checks if the form has been submitted
    $email = $_POST['email']; //makes a new variable and sets it to the value of the 'email' input that has been submitted
    $password = $_POST['password']; //makes a new variable and sets it to the value of the 'password' input that has been submitted

    $email = strip_tags($email); //strips the tags in email and password to prevent malicious text from getting in our system
    $password = strip_tags($password);

    $con = new mysqli("localhost", "root", "", "rigets_db"); //creates a new database connection to the user_information database
    //replace 'rigets_db' with the name of your database

    if ($con->connect_error) { //checks if the connection to the database has failed/resulted in an error
        die("Connection failed: " . $con->connect_error); //terminates the current script and sends the error in the console
    }

    $stmt = $con->prepare("SELECT password FROM userinformation WHERE email = ?"); //prepares an sql statement to get the password that is in the same row as the email
    $stmt->bind_param("s", $email); //puts the email in the statement
    $stmt->execute(); //executes the statement, runs it through the database
    $stmt->bind_result($hashed_password); //where the result will go, in a variable called 'hashed_password'
    $stmt->fetch(); //aquires the result

    if (password_verify($password, $hashed_password)) { //checks if the password entered matches the hashed password
        $_SESSION['email'] = $email; //tells the session that you are logged in with that email
    } else { //if the password and hashed password dont match
        $error = "Invalid credentials."; //sets an error message to be displayed
    }

    $stmt->close(); //closes the statement
    $con->close(); //closes the connection

    header("Location: ../home/home.php"); //sends the user to the dashboard
    //replace with your file path
}

?>