<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Video</title>
</head>
<body>
<form action="{{ url('/process-logo-video') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="video">Select Video:</label>
    <input type="file" name="video" id="video" accept="video/mp4" required><br><br>

    <label for="text">Custom logo:</label>
    <input type="file" name="logo" id="text" required><br><br>

    <button type="submit">Process Video</button>
</form>
</body>
</html>
