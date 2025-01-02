<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Video</title>
</head>
<body>
<h2>Upload and Crop Video</h2>
<form action="{{ url('/process-crop-video') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="video">Select Video:</label>
    <input type="file" name="video" id="video" accept="video/mp4" required><br><br>

    <label for="crop_width">Crop Width (px):</label>
    <input type="number" name="crop_width" id="crop_width" required><br><br>

    <label for="crop_height">Crop Height (px):</label>
    <input type="number" name="crop_height" id="crop_height" required><br><br>

    <label for="crop_x_offset">Crop X Offset (px):</label>
    <input type="number" name="crop_x_offset" id="crop_x_offset" required><br><br>

    <label for="crop_y_offset">Crop Y Offset (px):</label>
    <input type="number" name="crop_y_offset" id="crop_y_offset" required><br><br>

    <button type="submit">Crop Video</button>
</form>
</body>
</html>
