<!DOCTYPE html>
<html lang="id">
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
        <h1>Baby & Kid</h1>
        <p>Produk untuk bayi dan anak-anak yang aman dan berkualitas</p>
    </header>

    <div class="container">
        <div class="card">
            <h3>Popok Bayi</h3>
            <a href="#">Masuk</a>
        </div>
        <div class="card">
            <h3>Ranjang Bayi</h3>
            <a href="#">Masuk</a>
        </div>
        <div class="card">
            <h3>Mainan Anak</h3>
            <a href="#">Masuk</a>
        </div>
        <div class="card">
            <h3>Perawatan Bayi</h3>
            <a href="#">Masuk</a>
        </div>
        <div class="card">
            <h3>Minuman Bayi</h3>
            <a href="#">Masuk</a>
        </div>
        <div class="card">
            <h3>Tempat Makan Anak</h3>
            <a href="#">Masuk</a>
        </div>
        <div class="card">
            <h3>Stroller Bayi</h3>
            <a href="#">Masuk</a>
        </div>
        <div class="card">
            <h3>Ransel Anak</h3>
            <a href="#">Masuk</a>
        </div>
    </div>

    <footer>
        <a style="text-decoration: none; color:#f8f9fa;" href="{{ url('/') }}">Back to Home</a>
    </footer>

</body>
</html>
