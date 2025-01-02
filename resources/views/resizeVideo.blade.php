<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resize Video</title>
</head>
<body>
<form action="{{ url('/process-resize-video') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="video">Select Video to Resize:</label>
    <input type="file" name="video" id="video" accept="video/*" required><br><br>

    <label for="width">Width (px):</label>
    <input type="number" name="width" id="width" placeholder="Enter width" required><br><br>

    <label for="height">Height (px):</label>
    <input type="number" name="height" id="height" placeholder="Enter height" required><br><br>

    <button type="submit">Resize Video</button>
</form>
</body>
</html>
