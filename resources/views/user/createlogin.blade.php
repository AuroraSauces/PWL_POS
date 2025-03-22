<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Pengguna</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            width: 300px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-size: 14px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 3px;
        }
        button:hover {
            background: #218838;
        }
        .back-btn {
            text-align: center;
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Daftar Pengguna</h3>
    <form method="POST" action="{{ route('user.store') }}">
        @csrf

        <label>Level</label>
        <select id="level_id" name="level_id" required>
            <option value="">- Pilih Level -</option>
            @foreach($level as $item)
                <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
            @endforeach
        </select>

        <label>Username</label>
        <input type="text" id="username" name="username" value="{{ old('username') }}" required>

        <label>Nama</label>
        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required>

        <label>Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Simpan</button>
        <a href="{{ url('login') }}" class="back-btn">Kembali ke Login</a>
    </form>
</div>

</body>
</html>
