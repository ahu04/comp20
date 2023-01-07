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
            background-image: url("./media/header/orders.jpg");
        }

        #overlay {
            display: none;
            position: absolute;
            top: 0;
            bottom: 0;
            background: #999;
            width: 100%;
            height: 100%;
            opacity: 0.8;
            z-index: 100;
        }

        #popup {
            border-radius: 40px 40px 40px 40px;
            padding: 25px;
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            background: var(--taupe);
            width: 80vw;
            height: 80vh;
            margin-left: -40vw; 
            margin-top: -40vh;
            z-index: 200;
            overflow: auto;
            text-align: left;
        }

        #popup p{
            text-align: left;
            font-size: 16px;
        }

        #popup h3{
            color: var(--muave)
        }

        #popup li{
            text-align: left;
        }

        #popupclose {
            float: right;
            padding: 10px;
            cursor: pointer;
        }

        .order {
            text-align: center;
            font-size: 16px;
        }

        .recipe:hover {
            color: var(--muave);
        }

        .ordertitle {
            color: var(--muave);
            font-weight: 60px;
            font-size: 24px;
        }

        hr {
            border: 2px solid var(--creme);
        }
        .order h3 {color: var(--creme); font-weight: bold;}
        .order p,.order p:first-of-type {font-size: 1.5vw;}

        .ordertitle {margin-bottom: 0.45vw;}

        p.recipe {display: inline-block; width: fit-content;}
        p.recipe:hover {
            background-color: var(--creme);
            color: var(--taupe);
            text-decoration: underline;
        }

    </style>

</head>

<body>
<?php
    if (empty($_SESSION['username'])) echo ("<script> alert(\"You must be logged in to view your orders!\");
                                             location.href='./index.php'; </script>");
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
                <li><a href="./recipes.php">Recipes</a></li>
                <li><a href="./cart.php">My Cart</a></li>
                <li><a href="./orders.php">My Orders</a></li>
                <li><a href="./logout.php">Log Out</a></li>
            </ul>
        </div>
    </header>

    <div class="backdrop">
        <div class="multi-drop">
            <h1>My Orders</h1>
        </div>
    </div>

    <div class="text_block" id="page_content">

        <?php
            function get_orders($username){

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

                // get user's num_orders
                $sql1 = "SELECT num_orders FROM users WHERE username='$username'";
                $result1 = $conn->query($sql1);
                $results = $result1->fetch_all(MYSQLI_ASSOC);
                $num_orders = $results[0]["num_orders"];
    
                // get user's orders 
                $sql = "SELECT order_id, title, description, steps, ingredients FROM orders WHERE username='$username' AND in_cart='0'";
                $result = $conn->query($sql);
                $items = $result->fetch_all(MYSQLI_ASSOC);

                if (sizeof($items) == 0) {
                    echo "<p1> You haven't placed any orders yet. Go to the recipes page to browse!";
                }

                else {
                    echo "<h3>Your Recipes</h3>
                    <p><em>Click on a recipe to see all the details!</em></p> <br> <hr><br>";

                    $js_items = json_encode($items);
                    echo "<script>var recipes = $js_items;</script>";

                    for ($x = 1; $x <= $num_orders; $x++) {

                        $inorder = function($currItem) use ($x) {
                            return ($currItem["order_id"] == $x);
                        };

                        $i = array_filter($items, $inorder);

                        echo "<div class='order'><h3 class='ordertitle'>Order $x:</h3>";

                        foreach($i as $item) {
                            $title = $item["title"];
                            echo "<p class='recipe' onclick='displayPopup(this.innerHTML)'>$title</p></br>";
                        }
                        echo "</div><br><hr><br>";
                    } 
                    
                }

                // close database connection
                $conn->close();
            }

            get_orders($_SESSION['username']);
        ?>

        <div id="overlay"></div>
        <div id="popup">
            <div class="popupcontrols">
                <span id="popupclose">X</span>
            </div>
            <div class="popupcontent"> 
                <span id="popupcontent"></span>
            </div>
        </div>

        <script>
            var closePopup = document.getElementById("popupclose");
            var overlay = document.getElementById("overlay");
            var popup = document.getElementById("popup");
            var popupcontent = document.getElementById("popupcontent");

            closePopup.onclick = function() {
                overlay.style.display = 'none';
                popup.style.display = 'none';
            };

            function displayPopup(recipename) {
                
                recipe = recipes.find(recipe => recipe["title"] == recipename);
                popupcontent.innerHTML = "<h3>" + recipename + "</h3>";
                popupcontent.innerHTML += "<h4> Description: </h4>";
                popupcontent.innerHTML += "<p>" + recipe["description"] + "</p><br>";
                popupcontent.innerHTML += "<h4> Ingredients: </h4>";
                popupcontent.innerHTML += "<p>" + recipe["ingredients"] + "</p><br>";
                popupcontent.innerHTML += "<h4> Instructions: </h4>";
                popupcontent.innerHTML += "<p>" + recipe["steps"] + "</p><br>";

                overlay.style.display = 'block';
	            popup.style.display = 'block';
            }

        </script>

    </div>

</body>

</html>