<?php

//USE YOUR OWN WORDS FOR THE COMMENTS, THIS IS JUST A TEMPLATE
//USE YOUR OWN VARIABLE NAMES
//DONT INCLUDE THIS COMMENT OR THE COMMENTS ABOVE, USE COMMON SENSE
//YOU NEED TO SHOW PROGRESSION IN YOUR CODE, DO NOT KEEP THE TRY AND CATCH STATEMENTS AT FIRST, ADD THEM IN AFTER AND DOCUMENT IT

$error = ""; //creates an empty error message

if (isset($_SESSION["email"])) { //checks if the user has logged in already, redirects them if they have
        header("Location: dashboard.php");
}
    
if ($_SERVER["REQUEST_METHOD"] == "POST") { //checks if the form has been submitted
    $password_confimed = false;
    
    //gets all the submitted values from super global post, strips any harmful tags and then applies them to the correct variables
    //the names in the $_POST[] parameters, for example 'new_email', will have to be replaced with the names of the input fields on the form
    $email = $_POST['new_email'];
    $first_name = strip_tags($_POST['new_firstname']);
    $last_name = strip_tags($_POST['new_lastname']);
    $password = strip_tags($_POST['new_password']);
    $confirm_password = strip_tags($_POST['confirm_password']);
    //checks if the passwords are the same or not
    if ($password == $confirm_password) {
        $password_confimed = true;
    }

    //hashes the password and filters the email to make sure it is an email
    $password = password_hash($password, PASSWORD_BCRYPT);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $con = new mysqli("localhost", "root", "", "rigets_db"); //creates a new database connection to the user_information database
    //replace 'rigets_db' with the name of your database

    if ($con->connect_error) { //checks if the connection to the database has failed/resulted in an error
        die("Connection failed: " . $con->connect_error); //terminates the current script and sends the error in the console
    }
    //checks if there is a dupe and changes the 'dupe' variable to true if there is one
    $dupe = false;
    $stmt = $con->prepare("SELECT email FROM userinformation WHERE email = (?)");
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        $stmt->bind_result($copies); //binds the result to a new variable called 'copies'
        $stmt->fetch(); //aquires the result
        //if 'copies' doesnt have anything in it, being 'null', it says there are no duplicates
        if ($copies == null) {
            $dupe = false;
        }
        else { //if 'copies' has something in it, it says it has a dupe and sets an error message
            $dupe = true;
            $error = "Email in use";
        }
    }
    
    try { //trys to insert the values into the table, prevents errors crashing the program
        
        //checks if there is a dupe and if the passwords are confirmed or not, should be self-explanotory
        if ($dupe == false && $password_confimed == true) {
        $stmt = $con->prepare("INSERT INTO userinformation (email, first_name, last_name, password) VALUES (?, ?, ?, ?)"); //prepares the statement to insert the values into the table
        //replace userinformation with the name of the table you want to insert the values into
        $stmt->bind_param("ssss", $email, $first_name, $last_name, $password); //binds all the parameters with the correct values
        }
        else if ($dupe == true && $password_confimed == true) {
            throw new Exception("Email in use"); //throws an exception to say its resulted in an error
        }
        else if ($dupe == false && $password_confimed == false) {
            throw new Exception("Passwords aren't the same"); //throws an exception to say its resulted in an error
        }
        else {
            throw new Exception("Email in use and Passwords aren't the same"); //throws an exception to say its resulted in an error
        }

        if ($stmt->execute()) { //executes the statement
            echo "Registration successful!"; //sends confirmation message

            $_SESSION['email'] = $email; //sets the session email to the users email, makes them 'logged in'

            header("Location: ../home/home.php"); //redirects the user to the dashboard page
            
            exit(); //terminates the current script
        }
        else { //if the statement results with an error
            throw new Exception("Statement Crashed"); //throws an exception to say its resulted in an error
        }
    }
    catch (Exception $e) { //checks if the exception is thrown
        $error = $e->getMessage(); //sets an error message
    }

    //closes the statement and the connection to the database
    $stmt->close();
    $con->close();

    return $error;
    // when putting this on a page, give a text element this variable to display the error message. for example, <p><?php echo $error; {question mark}></p>
}

?>