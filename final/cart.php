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

        table {
            line-height: 25px;
            margin: 1rem 1rem;
            border-collapse: collapse;
            text-align: center;
            border-radius: 10px;
        }

        th {
            color: var(--muave);
        }

        tr:not(:last-child) {
            border-bottom: 2px solid var(--muave);
        }

        tr:not(:first-child) {
            border-top: 2px solid var(--muave);
        }

        input[type=text],input[type=password] {
            background-color: var(--barossa);
            /* background: none; */
            font-family: inherit;
            font-size: 1.2vw;
            padding: 15px;
            border: 2px solid var(--creme);
            color: var(--creme);
            margin: 0.5vw;
            box-sizing: border-box;
            width: 12vw; height: 2.5vw;
        }

        #address {
            width: 25.25vw;
        }

        #order_button {margin-bottom: 30px}


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

        #removebutton {
            background-color: var(--muave);
            color: var(--creme);
            border: none;
            width: 70px;
            margin: 10px 10px;
            height: 40px;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            transition-duration: 0.4s;
            font-weight: bold;
            font-family: var(--font_default);
            line-height: 20px;
        }
        
        #removebutton:hover,#submit_button:hover {
            box-shadow: 0 12px 16px 0 rgba(165, 127, 136, 0.24), 0 17px 50px 0 rgba(165, 127, 136, 0.19);
            cursor: pointer;
        }
        

        .totals {font-size: 1.5vw !important;
                 margin: 10px !important;}

        .totals:first-of-type {font-size: 1.5vw !important;
                               margin: 10px !important;}

        .totals:last-of-type {font-size: 1.75vw !important;
                               margin-bottom: 30px !important;
                               font-weight: bold}

        #page_content {display: flexbox; flex-direction: column;
                       justify-content: space-evenly; align-items: center;}

        table {width: 90%; table-layout: auto; }
        table * {height: fit-content; line-height: 2.2vw;
                 font-size: 2vw;}
        td.name,td.name * {font-size: 1.75vw;}
        td.servings select {font-size: 1.2vw !important;}
        td.servings select:hover {border: 0.2vw solid var(--clay);
                                  background-color: var(--maroon)}
    
        ::placeholder { color: var(--clay); }


    </style>

</head>

<body>
<?php
    if (empty($_SESSION['username'])) echo ("<script> alert(\"You must be logged in to view your cart!\");
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
            <h1>My Cart</h1>
        </div>
    </div>

    <script> 
        function validateLogin() {
            validForm = false;
            var name = document.getElementById("order_name").value;
            var address = document.getElementById("address").value;
            var cc = document.getElementById("cc_info").value;
            var valCC = validCC(cc);
            if (name == "" || address == "" || cc == "") {
                alert("Please fill out all fields of the form.");
            } else if (!valCC) {
                alert("Credit card information invalid. Try again.");
            }
            else {
                validLogin = true;
            }
            if (validLogin) {
                document.getElementById('order_form').submit();
            }
        }

        function validCC(cc) {
            cc = cc.replace(/[^\d]/g, '');
            if (cc.length == 16) {
                return true;
            }
            return false;
        }
    </script>

    <div class="text_block" id="page_content">

        <?php
            function get_cart($username){

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
                $sql = "SELECT * FROM orders WHERE username='$username' AND in_cart='1'";
                $result = $conn->query($sql);
                $items = $result->fetch_all(MYSQLI_ASSOC);

                if (sizeof($items) == 0) {
                    echo "<p1> There is nothing in your cart. Go to the recipes page 
                    to add ingredients!</p1>";
                }

                else {
                    echo "<p1><em>Click on the number of servings to change how much you want to order!</em></p1>
                        <table><tr>
                            <th>Recipe</th>
                            <th>Servings</th>
                            <th>Cost Per Serving</th>
                            <th>Total Cost</th>
                        </tr>";

                    // get each item and print to page
                    foreach($items as $index => $item) {
                        $title = $item["title"];
                        $cost = $item["serving_cost"];
                        $servings = $item["servings"];
                        $totalcost = number_format($cost * $servings, 2, '.', "");
                        
                        print_recipe($title, $index, $cost, $servings, $totalcost);
                    }

                    echo "</table>";

                    echo 
                    "<p class='totals'>Subtotal: $<span id='subtotal'></span></p>
                     <p class='totals'>Tax: $<span id='tax'></span></p>
                     <p class='totals'>Total: $<span id='total'></span></p>";

                    $formheader = "<form method='post' name='form' id='order_form' action='cart.php'>";
                    echo $formheader;
                    echo "<div> <input type='text' id='order_name' name='order_name' placeholder='Name'>
                        <input type='password' id='cc_info' name='cc_info' placeholder='Credit Card'> </div>
                        <input type='text' id='address' name='address' placeholder='Address'>
                        <div id='recipe_buttons'>
                            <input class='button' id='submit_button' type='button' value='Place Order' onclick='validateLogin()'>
                        </div> 
                    </form>";
                }

                // close database connection
                $conn->close();
            }
            
            function submit_order($username){

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
    
                // update order to "not in cart"
                $sql = "UPDATE orders SET in_cart='0' WHERE username='$username' AND in_cart='1'";
                $result = $conn->query($sql);

                // increment user's num orders
                $sql = "UPDATE users SET num_orders=num_orders+1 WHERE username='$username'";
                $result = $conn->query($sql);

                // close database connection
                $conn->close();
            }

            function delete_from_cart($username, $title){

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
    
                // update order to "not in cart"
                $sql = "DELETE FROM orders WHERE username='$username' AND title='$title'";
                $conn->query($sql);

                // close database connection
                $conn->close();
            }

            function print_recipe($title, $index, $cost, $servings, $totalcost){
                $s = "<tr>";
                $s .= td("<strong><em>$title</em></strong>", "name");
                $s .= td(makeSelect("quan".$index, 1, 20,$servings), "servings");
                $s .= td("$" . $cost, "cost");
                $s .= td("$" . $totalcost, "totalcost");
                $s .= td("<form method='post'><input type='hidden' name='title' value='$title'><input type='submit' name='remove'
                class='submit_button' id='removebutton' value='Remove'></form>", "remove");
                $s .= "</tr>";

                echo $s;
            }

            // functions to create select item and td item
            function makeSelect($name, $minRange, $maxRange, $selected) {
                $t = "<select name='$name' size='1'>";
                for ($i = $minRange; $i <= $maxRange; $i++) {
                    if ($i == $selected) {
                        $t .= "<option selected> $i </option>";
                    } else {
                        $t .= "<option> $i </option>";
                    }
                }
                $t .= "</select>"; 
                return $t;
            }
            function td($content, $className) {
                return "<td class = '$className'> $content </td>";
            }

            get_cart($_SESSION['username']);

            $info = $_REQUEST["address"];
            if (!empty($info)) {
                submit_order($_SESSION['username']);
                echo "<script>alert('Thank you for your order!'); location.href='./orders.php'; </script>";
            }

            if (isset($_POST['remove'])) {
                delete_from_cart($_SESSION['username'], $_REQUEST["title"]);
                echo "<script>location.href='./cart.php'; </script>";
            }
        ?>

        <script>
            itemcosts = document.getElementsByClassName("cost");
            totalcosts = document.getElementsByClassName("totalcost");
            subtotal = document.getElementById("subtotal");
            tax = document.getElementById("tax");
            total = document.getElementById("total");
            taxp = 0.0625;
            updateTotals();

            // add event listener for when item quantities are changed
            for (i = 0; i < totalcosts.length ; i++) {
                change = document.getElementsByName("quan" + i)[0];
                change.setAttribute("onchange", "updateItemTotal(" + i + ", this.selectedIndex + 1)");
            }

            // updates totals given the index of the modified item
            function updateItemTotal(index, quantity) 
            { 
                itemcost = itemcosts[index].innerHTML.split("$")[1];
                console.log("quant: " + quantity + "itemcost: " + itemcost + "total: " + quantity*itemcost);
                totalcosts[index].innerHTML = "$" + (quantity * itemcost).toFixed(2);
                updateTotals();
            }

            // updates subtotal, tax and total
            function updateTotals() 
            {
                subt = 0;
                for (i = 0; i < totalcosts.length; i++) {
                    subt += parseFloat(totalcosts[i].innerHTML.split("$")[1]);
                }
                subtotal.innerHTML = subt.toFixed(2);
                tax.innerHTML = (subtotal.innerHTML * taxp).toFixed(2);
                total.innerHTML = (parseFloat(subtotal.innerHTML) + parseFloat(tax.innerHTML)).toFixed(2);
            }

        </script>

    </div>

</body>

</html>