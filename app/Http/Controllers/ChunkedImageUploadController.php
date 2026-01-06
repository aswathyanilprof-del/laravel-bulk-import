<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChunkedImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // 1️⃣ Validate request
        $request->validate([
            'file' => 'required|file',
            'file_name' => 'required|string',
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
        ]);

        $fileName = $request->file_name;
        $chunkIndex = (int) $request->chunk_index;
        $totalChunks = (int) $request->total_chunks;

        // 2️⃣ Chunk directory (single source of truth)
        $chunkDir = storage_path('app/uploads/chunks/' . $fileName);

        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0777, true);
        }

        // 3️⃣ Save chunk
        $chunkFilePath = $chunkDir . '/' . $chunkIndex;

        file_put_contents(
            $chunkFilePath,
            file_get_contents($request->file('file'))
        );

        // 4️⃣ If not last chunk → stop here
        if ($chunkIndex + 1 < $totalChunks) {
            return response()->json([
                'message' => 'Chunk uploaded',
                'completed' => false,
            ]);
        }

        // 5️⃣ Merge chunks
        $finalDir = storage_path('app/uploads/images');

        if (!is_dir($finalDir)) {
            mkdir($finalDir, 0777, true);
        }

        $finalPath = $finalDir . '/' . $fileName;
        $finalFile = fopen($finalPath, 'ab');

        for ($i = 0; $i < $totalChunks; $i++) {
            $path = $chunkDir . '/' . $i;

            if (!file_exists($path)) {
                fclose($finalFile);
                if (file_exists($finalPath)) {
                    unlink($finalPath);
                }
                 return response()->json([
                    'message' => 'Upload incomplete',
                    'error' => "Missing chunk {$i}. Please retry uploading the missing chunk.",
                    'missing_chunk' => $i,
                ], 422);
            }

            $chunkFile = fopen($path, 'rb');
            stream_copy_to_stream($chunkFile, $finalFile);
            fclose($chunkFile);
        }

        fclose($finalFile);

        // 6️⃣ Cleanup chunks
        array_map('unlink', glob($chunkDir . '/*'));
        rmdir($chunkDir);

        // 7️⃣ Success response
        return response()->json([
            'message' => 'Image upload completed',
            'completed' => true,
            'path' => 'storage/app/uploads/images/' . $fileName,
        ]);
    }
}
