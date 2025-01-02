<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Video</title>
</head>
<body>
<form action="{{ url('/process-video') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="video">Select Video:</label>
    <input type="file" name="video" id="video" accept="video/mp4" required><br><br>

    <label for="text">Custom Text:</label>
    <input type="text" name="text" id="text" required><br><br>

    <button type="submit">Process Video</button>
</form>
</body>
</html>
