<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Transaction</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        header {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        h1 {
            font-size: 2rem;
            margin: 0;
        }

        .container {
            max-width: 700px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 1rem;
            font-weight: bold;
            display: block;
            color: #333;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-size: 1rem;
            border: none;
            border-radius: 4px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .cart-table th, .cart-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .cart-table th {
            background-color: #007bff;
            color: white;
        }

        .total {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .reset-btn {
            background-color: red;
        }

        .reset-btn:hover {
            background-color: darkred;
        }

        .checkout-btn {
            background-color: green;
        }

        .checkout-btn:hover {
            background-color: darkgreen;
        }

        footer {
            text-align: center;
            margin-top: 20px;
        }

        .back-link {
            color: #007bff;
            text-decoration: none;
            font-size: 1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header>
        <h1>POS Transaction</h1>
    </header>

    <div class="container">
        <form id="pos-form">
            <div class="form-group">
                <label for="product">Product Name:</label>
                <input type="text" id="product" name="product" required>
            </div>

            <div class="form-group">
                <label for="price">Price (Rp):</label>
                <input type="number" id="price" name="price" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>

            <button type="button" onclick="addToCart()">Add to Cart</button>
        </form>

        <h2>Cart</h2>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (Rp)</th>
                    <th>Quantity</th>
                    <th>Subtotal (Rp)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-body">
                <tr><td colspan="5" style="text-align:center;">No items in cart</td></tr>
            </tbody>
        </table>

        <p class="total">Total: <span id="total-price">Rp 0</span></p>

        <div class="button-group">
            <button class="reset-btn" onclick="resetCart()">Reset</button>
            <button class="checkout-btn" onclick="checkout()">Checkout</button>
        </div>
    </div>

    <footer>
        <a href="{{ url('/') }}" class="back-link">Back to Home</a>
    </footer>

    <script>
        let cart = [];

        function addToCart() {
            const product = document.getElementById("product").value;
            const price = parseFloat(document.getElementById("price").value);
            const quantity = parseInt(document.getElementById("quantity").value);

            if (!product || !price || !quantity || price < 0 || quantity <= 0) {
                alert("Please enter valid product details.");
                return;
            }

            const subtotal = price * quantity;
            cart.push({ product, price, quantity, subtotal });
            updateCart();
        }

        function updateCart() {
            const cartBody = document.getElementById("cart-body");
            cartBody.innerHTML = "";
            let total = 0;

            cart.forEach((item, index) => {
                total += item.subtotal;
                cartBody.innerHTML += `
                    <tr>
                        <td>${item.product}</td>
                        <td>Rp ${item.price.toLocaleString()}</td>
                        <td>${item.quantity}</td>
                        <td>Rp ${item.subtotal.toLocaleString()}</td>
                        <td><button onclick="removeFromCart(${index})">❌</button></td>
                    </tr>
                `;
            });

            if (cart.length === 0) {
                cartBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">No items in cart</td></tr>`;
            }

            document.getElementById("total-price").innerText = `Rp ${total.toLocaleString()}`;
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
        }

        function resetCart() {
            cart = [];
            updateCart();
        }

        function checkout() {
            if (cart.length === 0) {
                alert("Your cart is empty!");
                return;
            }
            alert("Checkout successful! Thank you for your purchase.");
            resetCart();
        }
    </script>

</body>
</html>
