<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;

class VideoController extends Controller
{
    public function processVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4|max:10240',
            'text' => 'required|string',
        ]);

        try {
            $video = $request->file('video');
            $customText = $request->input('text');

            $inputPath = 'videos/input/';
            $outputPath = 'videos/output/';
            $inputVideoPath = $inputPath . $video->getClientOriginalName();
            $outputVideoPath = $outputPath . 'output_' . time() . '.mp4';

            $video->storeAs($inputPath, $video->getClientOriginalName(), 'public');

            FFMpeg::fromDisk('public')
                ->open($inputVideoPath)
                ->addFilter(function ($filters) use ($customText) {
                    // Apply drawtext filter with center alignment and font size of 20
                    $filters->custom('drawtext=text=\'' . $customText . '\':fontcolor=white:fontsize=20:x=(w-text_w)/2:y=(h-text_h)/2');
                })
                ->export()
                ->toDisk('public')
                ->inFormat(new \FFMpeg\Format\Video\X264)
                ->save($outputVideoPath);

            $outputVideoUrl = asset('storage/' . $outputVideoPath);

            return response()->json([
                'success' => true,
                'message' => 'Video processed successfully!',
                'video_url' => str_replace('\\', '', $outputVideoUrl)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process video: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function processLogoVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mkv,avi,webm',
            'logo' => 'required|image|mimes:png,jpg,jpeg,gif',
        ]);

        $videoFile = $request->file('video');
        $logoFile = $request->file('logo');

        $videoPath = $videoFile->storeAs('videos', $videoFile->getClientOriginalName(), 'public');
        $logoPath = $logoFile->storeAs('logos', $logoFile->getClientOriginalName(), 'public');


        $logoPath = storage_path('app/public/' . $logoPath);
        $videoPath = storage_path('app/public/' . $videoPath);


        $outputDirectory = storage_path('app/public/videos/output');
        if (!file_exists($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }

        $outputPath = 'videos/output/output_' . uniqid() . '.mp4';
        $fullPath = $outputDirectory . '/' . basename($outputPath);

        $rawCommand = "ffmpeg -i {$videoPath} -i {$logoPath} -filter_complex \"overlay=W-w-10:H-h-10\" -c:v libx264 -y {$fullPath}";

        $output = [];
        $return_var = 0;
        exec($rawCommand . ' 2>&1', $output, $return_var);

        $outputVideoUrl = asset('storage/' . $outputPath);

        return response()->json([
            'message' => 'Video processed successfully.',
            'video_url' => str_replace('\\', '', $outputVideoUrl)
        ]);
    }

    public function trimVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mkv,avi,webm',
            'start_time' => 'required|date_format:H:i:s',
            'duration' => 'required|date_format:H:i:s',
        ]);

        $videoFile = $request->file('video');
        $startTime = $request->input('start_time');
        $duration = $request->input('duration');

        $videoPath = $videoFile->storeAs('videos', $videoFile->getClientOriginalName(), 'public');
        $videoPath = storage_path('app/public/' . $videoPath);

        $outputDirectory = storage_path('app/public/videos/output');
        if (!file_exists($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }

        $outputFileName = 'trimmed_' . uniqid() . '.mp4';
        $fullOutputPath = $outputDirectory . '/' . $outputFileName;

        $videoPath = escapeshellarg($videoPath);
        $fullOutputPath = escapeshellarg($fullOutputPath);

        $rawCommand = "ffmpeg -i {$videoPath} -ss {$startTime} -t {$duration} -c:v libx264 -c:a aac -threads 12 -y {$fullOutputPath}";

        Log::info('Executing FFmpeg command: ' . $rawCommand);

        $output = [];
        $returnVar = 0;
        exec($rawCommand . ' 2>&1', $output, $returnVar);

        Log::info('FFmpeg Output: ' . implode("\n", $output));

        if ($returnVar !== 0) {
            return response()->json([
                'message' => 'An error occurred during video trimming.',
                'error' => implode("\n", $output)
            ], 500);
        }

        $outputVideoUrl = asset('storage/videos/output/' . $outputFileName);

        return response()->json([
            'message' => 'Video trimmed successfully.',
            'video_url' => str_replace('\\', '', $outputVideoUrl)
        ]);
    }

    public function processCropVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mkv,avi,webm',
            'crop_width' => 'required|integer|min:1',
            'crop_height' => 'required|integer|min:1',
            'crop_x_offset' => 'required|integer|min:0',
            'crop_y_offset' => 'required|integer|min:0',
        ]);

        $videoFile = $request->file('video');
        $cropWidth = $request->input('crop_width');
        $cropHeight = $request->input('crop_height');
        $cropXOffset = $request->input('crop_x_offset');
        $cropYOffset = $request->input('crop_y_offset');

        $videoPath = $videoFile->storeAs('videos', $videoFile->getClientOriginalName(), 'public');
        $videoPath = storage_path('app/public/' . $videoPath);

        $outputDirectory = storage_path('app/public/videos/output');
        if (!file_exists($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }

        $outputFileName = 'cropped_' . uniqid() . '.mp4';
        $fullOutputPath = $outputDirectory . '/' . $outputFileName;

        $videoPath = escapeshellarg($videoPath);
        $fullOutputPath = escapeshellarg($fullOutputPath);

        $rawCommand = "ffmpeg -i {$videoPath} -vf \"crop={$cropWidth}:{$cropHeight}:{$cropXOffset}:{$cropYOffset}\" -c:v libx264 -c:a aac -threads 12 -y {$fullOutputPath}";

        \Log::info('Executing FFmpeg command: ' . $rawCommand);

        $output = [];
        $returnVar = 0;
        exec($rawCommand . ' 2>&1', $output, $returnVar);

        \Log::info('FFmpeg Output: ' . implode("\n", $output));

        if ($returnVar !== 0) {
            return response()->json([
                'message' => 'An error occurred during video cropping.',
                'error' => implode("\n", $output)
            ], 500);
        }

        $outputVideoUrl = asset('storage/videos/output/' . $outputFileName);

        return response()->json([
            'message' => 'Video cropped successfully.',
            'video_url' => $outputVideoUrl
        ]);
    }


    public function resizeVideo(Request $request)
    {

        $request->validate([
            'video' => 'required|file|mimes:mp4,mkv,avi,webm|max:20000',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
        ]);

        $videoFile = $request->file('video');

        $width = $request->input('width');
        $height = $request->input('height');

        $videoPath = $videoFile->storeAs('videos', $videoFile->getClientOriginalName(), 'public');

        $videoPathForFFMpeg = public_path('storage/' . $videoPath);

        if (!file_exists($videoPathForFFMpeg)) {
            \Log::error('Video file does not exist at path: ' . $videoPathForFFMpeg);
            return response()->json([
                'message' => 'Video file does not exist.',
                'error' => 'File path is incorrect or file is missing.',
            ], 500);
        }

        $outputDirectory = public_path('storage/videos/output');

        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }

        $outputFileName = 'resized_' . uniqid() . '.mp4';
        $outputPublicPath = 'videos/output/' . $outputFileName;

        try {
            FFMpeg::fromDisk('public')
                ->open($videoPath)
                ->addFilter(function ($filters) use ($width, $height) {
                    $filters->resize(new Dimension($width, $height));
                })
                ->export()
                ->toDisk('public')
                ->inFormat(new X264())
                ->save($outputPublicPath);

            $outputVideoUrl = asset('storage/' . $outputPublicPath);

            return response()->json([
                'message' => 'Video resized successfully.',
                'video_url' => str_replace('\\', '', $outputVideoUrl),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error during video resizing: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred during video resizing.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
