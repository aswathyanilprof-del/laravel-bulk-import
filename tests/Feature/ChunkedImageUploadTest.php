<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChunkedImageUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_uploads_and_merges_image_chunks_successfully()
    {
        Storage::fake('local');

        $fileName = 'test.jpg';

        // Fake image chunks
        $chunk1 = UploadedFile::fake()->create('chunk1.bin', 100);
        $chunk2 = UploadedFile::fake()->create('chunk2.bin', 100);

        // Upload chunk 0
        $this->post('/api/upload-image-chunk', [
            'file' => $chunk1,
            'file_name' => $fileName,
            'chunk_index' => 0,
            'total_chunks' => 2,
        ])->assertStatus(200)
          ->assertJson(['completed' => false]);

        // Upload chunk 1 (last)
        $this->post('/api/upload-image-chunk', [
            'file' => $chunk2,
            'file_name' => $fileName,
            'chunk_index' => 1,
            'total_chunks' => 2,
        ])->assertStatus(200)
          ->assertJson(['completed' => true]);

        // Final image exists
        $this->assertFileExists(
            storage_path('app/uploads/images/' . $fileName)
        );
    }

    /** @test */
    public function it_returns_error_when_chunk_is_missing()
    {
        $response = $this->post('/api/upload-image-chunk', [
            'file' => UploadedFile::fake()->create('chunk2.bin', 100),
            'file_name' => 'missing.jpg',
            'chunk_index' => 1,
            'total_chunks' => 2,
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'error',
                     'missing_chunk'
                 ]);
    }
}
