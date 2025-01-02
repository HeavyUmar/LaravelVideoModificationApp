<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trim Video</title>
</head>
<body>
<h1>Trim Video</h1>
<form action="{{ url('/trim-video') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="video">Select Video:</label>
    <input type="file" name="video" id="video" accept="video/mp4" required><br><br>

    <label for="start_time">Start Time (HH:MM:SS):</label>
    <input type="text" name="start_time" id="start_time" placeholder="00:00:10" required><br><br>

    <label for="duration">Duration (HH:MM:SS):</label>
    <input type="text" name="duration" id="duration" placeholder="00:00:20" required><br><br>

    <button type="submit">Trim Video</button>
</form>
</body>
</html>
