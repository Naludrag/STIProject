<?php
require_once('../class/dbManager.php');
session_start();
// If the user is not logged he will be redirected to the login page
if(!$_SESSION['logon']){
    header('Location: ../login.php');
}
$dbManager = new dbManager;

// If an id is received it means that a reply is desired by the user
if(isset($_GET['id'])){
    $message = $dbManager->findMessageByID($_GET['id']);
    $user = $dbManager->findUserByID($message['recipient']);
}

// Check that the form is completed
if (isset($_POST['recipient']) && isset($_POST['subject']) && isset($_POST['body'])) {
    $user = $dbManager->findUserByID($_SESSION['id']);
    // Check that the session has a valid id
    if($user != NULL) {
        $recipient = $dbManager->findUserByUsername($_POST['recipient']);
        // Check if the recipient wrote in the form exists in the database. If the user is unknown the site will do nothing
        if($recipient != false){
            $dbManager->addMessage($_POST['subject'], $_POST['body'], $_SESSION['id'], $recipient['id']);
            header('Location: ../inbox.php');
        } else {
            echo '<script language="javascript">';
            echo 'alert("User unknown please try another user")';
            echo '</script>';
        }
    }
    else header('Location: login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/signin.css">

        <meta charset="UTF-8">
        <title>New message</title>
    </head>
    <body>
        <form action="message.php" method="post">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-8 col-xl-6">
                        <div class="row">
                            <div class="col text-center">
                                <h1>New Message</h1>
                                <p class="text-h3">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia. </p>
                                <?php
                                if (isset($error) && !empty($error)) {
                                    echo '<p class="error">' . $error . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col mt-4">
                                <label for="recipient">Recipient</label>
                                <input type="text" id="recipient" name="recipient" class="form-control" placeholder="Recipient" required value="<?php if($message != NULL){ echo $user['username'];} else if($_POST['recipient']){ echo $_POST['recipient'];}?>">
                            </div>
                        </div>
                        <div class="row align-items-center mt-4">
                            <div class="col">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" placeholder="Subject" required value="<?php if($message != NULL){echo "RE: ".$message['subject'];} else if($_POST['recipient']){ echo $_POST['subject'];}?>">
                            </div>
                        </div>
                        <div class="row align-items-center mt-4">
                            <div class="col">
                                <label for="body">Body</label>
                                <textarea rows = "5" cols = "60"  class="form-control" placeholder="Enter details here..." name="body" required ><?php if($_POST['body']){ echo $_POST['body'];}?></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-start mt-4">
                            <div class="col">
                                <button type="submit" class="btn btn-primary mt-4">Submit</button>
                                <button type="submit" class="btn btn-secondary mt-4" formaction="../inbox.php" formnovalidate>Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>