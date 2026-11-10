<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$products=mysqli_query($conn,"SELECT * FROM products ORDER BY product_name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>POS Billing</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card{
box-shadow:0px 3px 10px rgba(0,0,0,.1);
}

table td{
vertical-align:middle;
}

#cartTable input{
width:80px;
}

.totalBox{

font-size:25px;

font-weight:bold;

color:green;

}

</style>

</head>

<body>

<div class="container mt-4">

<div class="card">

<div class="card-header bg-primary text-white">

<h3>Super Shop POS</h3>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-5">

<label>Select Product</label>

<select
id="product"
class="form-select">

<option value="">Choose Product</option>

<?php

while($row=mysqli_fetch_assoc($products))
{

?>

<option

value="<?php echo $row['id']; ?>"

data-name="<?php echo $row['product_name']; ?>"

data-price="<?php echo $row['selling_price']; ?>"

data-stock="<?php echo $row['stock']; ?>">

<?php

echo $row['product_name'];

?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-2">

<label>Quantity</label>

<input

type="number"

id="qty"

class="form-control"

value="1"

min="1">

</div>

<div class="col-md-2 d-grid">

<label>&nbsp;</label>

<button

class="btn btn-success"

id="addCart">

Add Item

</button>

</div>

<div class="col-md-3">

<label>Invoice No</label>

<input

type="text"

class="form-control"

value="<?php echo 'INV'.time();?>"

readonly

id="invoice">

</div>

</div>

<hr>

<table

class="table table-bordered"

id="cartTable">

<thead>

<tr>

<th>Product</th>

<th width="120">Price</th>

<th width="120">Qty</th>

<th width="150">Subtotal</th>

<th width="80">Action</th>

</tr>

</thead>

<tbody>

</tbody>

</table>

<div class="row">

<div class="col-md-8">
<form method="POST" action="process_sale.php" id="saleForm">
<div class="card">

<div class="card-header bg-success text-white">

Checkout

</div>

<div class="card-body">

<div class="mb-3">

<label>Customer Name</label>

<input
type="text"
id="customer"
name="customer_name"
class="form-control"
placeholder="Walk-in Customer">

</div>

<div class="mb-3">

<label>Payment Method</label>

<select id="payment" name="payment_method" class="form-select">

    <option value="Cash">Cash</option>
    <option value="Due">Due</option>
    <option value="Bkash">Bkash</option>
    <option value="Nagad">Nagad</option>
    <option value="Card">Card</option>

</select>

</div>

<div class="mb-3" id="dueSection" style="display:none;">

<label>Due Date</label>

<input
type="date"
name="due_date"
class="form-control">

</div>

<div class="mb-3">

<label>Cash Received</label>

<input
type="number"
id="received"
name="cash_received"
class="form-control">

</div>

<div class="mb-3">

<label>Change</label>

<input
type="text"
id="change"
name="change_amount"
class="form-control"
readonly>

</div>

<input type="hidden" id="cartData" name="cart">

<button
class="btn btn-primary w-100"
id="checkout">

Checkout

</button>

</div>

</div>
</form>

</div>

<div class="col-md-4">

<table class="table">

<tr>

<th>Total</th>

<td>

<span

class="totalBox"

id="grandTotal">

0

</span>

</td>

</tr>

</table>

</div>

</div>
<script>

let cart = [];

const product = document.getElementById("product");
const qty = document.getElementById("qty");
const addCart = document.getElementById("addCart");

const tbody = document.querySelector("#cartTable tbody");

const grandTotal = document.getElementById("grandTotal");

addCart.addEventListener("click",function(){

    if(product.value==""){
        alert("Select Product");
        return;
    }

    let option = product.options[product.selectedIndex];

    let id = product.value;
    let name = option.dataset.name;
    let price = parseFloat(option.dataset.price);
    let stock = parseInt(option.dataset.stock);
    let quantity = parseInt(qty.value);

    if(quantity > stock){
        alert("Not enough stock!");
        return;
    }

    let found = cart.find(item => item.id == id);

    if(found){

        found.qty += quantity;

        if(found.qty > stock){
            alert("Stock limit exceeded!");
            found.qty -= quantity;
            return;
        }

    }else{

        cart.push({

            id:id,
            name:name,
            price:price,
            qty:quantity

        });

    }

    loadCart();

});

function loadCart(){

tbody.innerHTML="";

let total=0;

cart.forEach(function(item,index){

let subtotal=item.price*item.qty;

total+=subtotal;

tbody.innerHTML +=`

<tr>

<td>${item.name}</td>

<td>${item.price.toFixed(2)}</td>

<td>${item.qty}</td>

<td>${subtotal.toFixed(2)}</td>

<td>

<button

class="btn btn-danger btn-sm"

onclick="removeItem(${index})">

X

</button>

</td>

</tr>

`;

});

grandTotal.innerHTML=total.toFixed(2);

}

function removeItem(index){

cart.splice(index,1);

loadCart();

}
const received = document.getElementById("received");
const change = document.getElementById("change");
const saleForm = document.getElementById("saleForm");
const cartInput = document.getElementById("cartData");
const payment = document.getElementById("payment");
const dueSection = document.getElementById("dueSection");

received.addEventListener("keyup", calculateChange);
received.addEventListener("change", calculateChange);

payment.addEventListener("change", function(){

    if(this.value == "Due"){

        dueSection.style.display = "block";

        received.disabled = true;
        received.value = "";

        change.value = "0.00";

    } else {

        dueSection.style.display = "none";

        received.disabled = false;

    }

});

function calculateChange(){

let total = parseFloat(grandTotal.innerHTML) || 0;

let cash = parseFloat(received.value) || 0;

change.value = (cash - total).toFixed(2);

}
document.getElementById("checkout").addEventListener("click", function(e){

    e.preventDefault();

    if(cart.length==0){

        alert("Cart is Empty");

        return;

    }

    if(payment.value == "Cash"){

        let total = parseFloat(grandTotal.innerHTML);

        let cash = parseFloat(received.value) || 0;

        if(cash < total){

            alert("Cash is less than Total.");

            return;

        }

    }

    document.getElementById("cartData").value =
        JSON.stringify(cart);

    document.getElementById("saleForm").submit();
});
</script>
</body>
</html>