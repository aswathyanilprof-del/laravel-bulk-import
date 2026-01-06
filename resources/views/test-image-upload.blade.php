<!DOCTYPE html>
<html>
<head>
    <title>Test Chunk Image Upload</title>
</head>
<body>
    <h2>Test Chunk Image Upload</h2>

    @if ($errors->any())
        <div style="color:red">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <form method="POST" action="/test-image-upload" enctype="multipart/form-data">
        @csrf

        <label>Image:</label><br>
        <input type="file" name="file"><br><br>

        <label>File name:</label><br>
        <input type="text" name="file_name" value="image.jpg"><br><br>

        <label>Chunk index:</label><br>
        <input type="number" name="chunk_index" value="0"><br><br>

        <label>Total chunks:</label><br>
        <input type="number" name="total_chunks" value="1"><br><br>

        <button type="submit">Upload</button>
    </form>
</body>
</html>
