<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Final Project</title>
    <link rel="stylesheet" type = "text/css" href="./style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="script.js"></script>
    <style>
        .backdrop {
            background-image: url("./media/header/home.jpg");
        }

        .formcontainer {
        background-color: var(--creme);
        height: auto;
        padding: 20px;
        }

        .form {
            display: flex;
            justify-content: center;
        }

        .formcol {
            margin: 20px;
        }

        input {
            background: none;
            font-family: inherit;
            font-size: 100%;
            padding: 10px;
            border: 2px solid var(--taupe);
            box-sizing: border-box;
            color: var(--taupe);
            width: 100%;
        }

        .prompt {
            margin: 0 auto;
            width: 60%;
            text-align: center;
            font-size: 1.75vw;
        }

        select {
            appearance: none;
            background: transparent;
            cursor: pointer;
            font-family: inherit;
            border: 2px solid var(--taupe);
            padding: 10px;
            width: 100%;
            font-size: 100%;
        }

        .button {
            background: var(--taupe) !important;
            color: var(--creme) !important;
            cursor: pointer;
        }

        .button:hover {
            background: var(--muave) !important;
        }

        #submit_button {
            display: block;
            margin: 0 auto;
            width: 40%;
        }

    </style>
</head>

<body>
    <?php
        $username = $_REQUEST["user"];
        $password = $_REQUEST["pass"];

        // If the username is empty, they are coming from another page, because
        // we force them to submit the username with a value from this page. 
        // if they come from another page we shouldn't try to log them in.
        if (!empty($username)) {
            // if user has not logged in though they would have to actively navigate
            // to this page as we don't display it anymore once you log in.
            if (empty($_SESSION['username'])) {
                login_user($username, $password);
            }
        }

        function login_user($username, $password){

            // database info
            $server = "localhost";
            $userid = "u0m7cp7iogobo";
            $pw = "finalprojectpass";
            $db = "dbsikj01q12d1d";
            
            // connect to database
            $conn = new mysqli($server, $userid, $pw, $db);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // check if user is in database
            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = $conn->query($sql);
            $results = $result->fetch_all(MYSQLI_ASSOC);

            if ($result->num_rows == 0) {
                //add user to DB, create session
                $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
                $result = $conn->query($sql);
                
                if ($result == TRUE) {
                    $_SESSION['username'] = $username;
                    echo "<script> alert(\"Created a new user, you are now logged in\"); location.href='./orders.php'; </script>";
                } else {
                    echo "<script> alert(\"We ran into an issue, please try again soon\"); console.log(Error: "
                            . $sql . "<br>" . $conn->error . ") </script>";
                }
            } 
            else {
                $db_pass = $results[0]["password"];

                if ($db_pass == $password) {
                    $_SESSION['username'] = $username;
                    echo "<script> alert(\"You are now logged in\"); location.href='./orders.php'; </script>";
                }
                else {
                    echo "<script> alert(\"Incorrect password, please try again\"); </script>";
                }
            }
            // close database connection
            $conn->close();
        }
    
    ?>
    <header>
        <div class="nav_bar">
            <div class="name_logo">
                <a href="./index.html" class="logo"><img src="./media/header/logo.jpg" height="50px"></a>
                <div class="bakery_name">
                    Hain's Delivery
                </div>
            </div>

            <ul class="nav_bar_ul">
                <li><a href="./index.php">Home</a></li>
                <?php
                    if (empty($_SESSION['username'])) echo ("<li><a href=\"./login.php\">Log In</a></li>");
                    else echo("<li><a href=\"./recipes.php\">Recipes</a></li>
                               <li><a href=\"./cart.php\">My Cart</a></li>
                               <li><a href=\"./orders.php\">My Orders</a></li>
                               <li><a href=\"./logout.php\">Log Out</a></li>");
                ?>
            </ul>
        </div>
    </header>

    <div class="backdrop">
        <div class="multi-drop">
            <h1>Log in</h1>
        </div>
    </div>


    <script> 
        function validateLogin() {
            validLogin = false;
            var user = document.getElementById("user").value;
            var pass = document.getElementById("pass").value;
            if (user == "" || pass == "") {
                alert("Please enter your first and last name.");
            } else if (user.length > 20 || pass.length > 40) {
                alert("Username or password is too long. There is a 20/40 character\
                limit respectively on user/pass");
            }
            else {
                validLogin = true;
            }
            if (validLogin) {
                document.getElementById('login_form').submit();
            }
        }
    </script>

    <div class="formcontainer">
        <form method="post" name="form" id="login_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="prompt">If you are a returning user please enter your information,
                 if you are a new user please enter a username and password to generate an account</div>
            <div class="form">
                <div class="formcol">
                    <input type="text" id="user" name="user" placeholder="username">
                </div>
                <div class="formcol">
                    <input type="password" id="pass" name="pass" placeholder="password">
                </div>
            </div>
            <input class="button" id="submit_button" type="button" value="Login" onclick="validateLogin()">
        </form>
    </div>

    <!-- <footer>
        <p>&copy; Hain's Delivery 2020</p>
        test junk here
    </footer> -->

</body>

</html>