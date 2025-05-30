<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover; /* Ini yang bikin gambar tidak gepeng */
            border: 3px solid #007bff;
            margin-bottom: 20px;
        }

        .profile-details {
            font-size: 1.2rem;
            margin-bottom: 20px;
            text-align: left;
        }

        .profile-details p {
            margin: 10px 0;
        }

        .profile-details strong {
            font-weight: bold;
            color: #333;
        }

        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }

        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <header>
        <h1>Profil Pengguna</h1>
    </header>

    <div class="container">
        <!-- Gambar Profil -->
        <img src="{{ $profile_picture_url ?? asset('default-avatar.png') }}" alt="Foto Profil" class="profile-pic">

        <!-- Detail Profil -->
        <div class="profile-details">
            <p><strong>ID Pengguna:</strong> {{ $id }}</p>
            <p><strong>Nama:</strong> {{ $name }}</p>
        </div>

        <!-- Link Balik -->
        <a href="{{ url('/') }}" class="back-link">Back To Home</a>
    </div>

</body>
</html>
