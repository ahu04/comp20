// iterate through the select elements on page and add event listeners to them
// to update the corresponding total cost

select1 = document.getElementsByName("quan0")[0];
select1.addEventListener('change', function () {
        var quantity = select1.selectedIndex;
        var cost = menuItems[0].cost;
        var totalCost = document.getElementsByClassName("totalCost");
        var cell = totalCost[0].getElementsByTagName('input')[0];
        cell.value = (quantity * cost).toFixed(2);
        updateTotals();
});

select2 = document.getElementsByName("quan1")[0];
select2.addEventListener('change', function () {
        var quantity = select2.selectedIndex;
        var cost = menuItems[1].cost;
        var totalCost = document.getElementsByClassName("totalCost");
        var cell = totalCost[1].getElementsByTagName('input')[0];
        cell.value = (quantity * cost).toFixed(2);
        updateTotals();
});

select3 = document.getElementsByName("quan2")[0];
select3.addEventListener('change', function () {
        var quantity = select3.selectedIndex;
        var cost = menuItems[2].cost;
        var totalCost = document.getElementsByClassName("totalCost");
        var cell = totalCost[2].getElementsByTagName('input')[0];
        cell.value = (quantity * cost).toFixed(2);
        updateTotals();
});

select4 = document.getElementsByName("quan3")[0];
select4.addEventListener('change', function () {
        var quantity = select4.selectedIndex;
        var cost = menuItems[3].cost;
        var totalCost = document.getElementsByClassName("totalCost");
        var cell = totalCost[3].getElementsByTagName('input')[0];
        cell.value = (quantity * cost).toFixed(2);
        updateTotals();
});

select5 = document.getElementsByName("quan4")[0];
select5.addEventListener('change', function () {
        var quantity = select5.selectedIndex;
        var cost = menuItems[4].cost;
        var totalCost = document.getElementsByClassName("totalCost");
        var cell = totalCost[4].getElementsByTagName('input')[0];
        cell.value = (quantity * cost).toFixed(2);
        updateTotals();
});

function updateTotals() {

        var subtotal = 0;
        for (i = 0; i < 5; ++i) {
                var totalCost = document.getElementsByClassName("totalCost");
                var cell = totalCost[i].getElementsByTagName('input')[0];
                subtotal = subtotal + Number(cell.value);
        }
        document.getElementById("subtotal").value = subtotal.toFixed(2);
        document.getElementById("tax").value = (subtotal * .0625).toFixed(2);
        document.getElementById("total").value = (subtotal * 1.0625).toFixed(2);
}