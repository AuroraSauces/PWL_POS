<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - E-Commerce</title>
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
            font-size: 2.5rem;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 24%;
            margin-bottom: 30px;
            transition: transform 0.3s ease-in-out;
            padding: 20px;
            text-align: center;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .card a {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }

        .card a:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Toko Jaya Makmur</h1>
        <p>Anda Mager? kita untung</p>
    </header>

    <div class="container">
        <div class="card">
            <h3>Food & Beverage</h3>
            <p>Explore our wide selection of food and beverages.</p>
            <a href="{{ route('category.foodBeverage') }}">Browse</a>
        </div>

        <div class="card">
            <h3>Beauty & Health</h3>
            <p>Find the best beauty and health products.</p>
            <a href="{{ route('category.beautyHealth') }}">Browse</a>
        </div>

        <div class="card">
            <h3>Home & Care</h3>
            <p>Shop for home and care essentials.</p>
            <a href="{{ route('category.homeCare') }}">Browse</a>
        </div>

        <div class="card">
            <h3>Baby & Kid</h3>
            <p>Discover the best products for babies and kids.</p>
            <a href="{{ route('category.babyKid') }}">Browse</a>
        </div>
    </div>

    <footer>
        <a href="{{ route('user.profile', ['id' => 1, 'name' => 'satria']) }}" style="color: white; text-decoration: none;">Go to User Profile</a> |
        <a href="{{ route('transaction') }}" style="color: white; text-decoration: none;">Go to Transaction POS</a>
    </footer>
</body>
</html>
