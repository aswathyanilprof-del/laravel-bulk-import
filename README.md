📌 Author

Aswathy Anilkumar

# Laravel Bulk Import & Chunked Image Upload

This project implements **Task A** of the Laravel assessment, focusing on **scalable CSV bulk import** and **resumable chunked image uploads** using Laravel APIs.  
The solution is designed with **performance, reliability, and testability** in mind.

---

## 🚀 Features

### 1. CSV Bulk Product Import
- Upload large CSV files via API
- Chunked processing to handle large datasets efficiently
- Product **upsert by SKU** (no duplicates)
- Graceful validation & error handling
- Unit-tested business logic

### 2. Chunked Image Upload (Resumable)
- Upload images in multiple chunks
- Supports resume & retry of failed chunks
- Automatically merges chunks after final upload
- Handles missing chunks gracefully (no application crash)
- Returns meaningful API error responses

---

## 🧱 Tech Stack

- **Laravel 12**
- **PHP 8.3**
- SQLite (for simplicity & portability)
- PHPUnit (Unit & Feature tests)

---

## 📂 Folder Structure (Key Parts)
```bash
app/
├── Http/Controllers
│ ├── ProductImportController.php
│ └── ChunkedImageUploadController.php
├── Services
│ └── ProductCsvImportService.php

tests/
├── Unit
│ └── ProductUpsertTest.php
└── Feature
└── ChunkedImageUploadTest.php
```
## 📦 Setup Instructions

### 1️⃣ Clone Repository
```bash
git clone https://github.com/aswathyanilprof-del/laravel-bulk-import.git
cd laravel-bulk-import
```
2️⃣ Install Dependencies
```bash
composer install
```
3️⃣ Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```
4️⃣ Database Setup

SQLite is used for ease of setup.
```bash
touch database/database.sqlite
php artisan migrate
```
5️⃣ Start Server
```bash
php artisan serve
```
📄 Task A – Bulk CSV Import

🔹 API Endpoint

     POST /api/import-products

🔹 Description
- Imports products from a CSV file
    
- Handles duplicates using SKU-based upsert logic
    
- Validates input data

- Idempotent (safe to re-upload the same CSV)

🔹 Demo UI

    GET /upload


A simple web interface is provided for uploading CSV files for testing and demo purposes.

🖼 Task A – Chunked Image Upload

🔹 API Endpoint
    
    POST /api/upload-image-chunk

🔹 Description

- Supports uploading large images in chunks

- Chunks are stored temporarily on the server

- Final image is assembled when all chunks are received

- Missing chunks are handled gracefully with a structured error response

- Supports resumable uploads (only missing chunks need to be retried)

🔹 Demo UI

    GET /test-image-upload


A lightweight web UI is provided to manually test chunk uploads and observe validation and error handling.

❗ Error Handling (Chunk Upload)

If a chunk is missing during merge, the API returns:

    {
      "message": "Upload incomplete",
      "error": "Missing chunk 0. Please retry uploading the missing chunk.",
      "missing_chunk": 0
    }


HTTP Status: 422 Unprocessable Entity

This ensures the application does not fail and allows the client to retry only the missing chunks.

🧪 Testing

🔹 Run Tests
    
    php artisan test

🔹 Covered Scenarios

- Product CSV upsert logic

- Successful chunked image upload & merge

- Graceful handling of missing chunks

Tests are written to be CI-friendly and do not rely on environment-specific extensions (e.g., GD).

🧠 Design Notes

- API-first design with optional minimal web UI for testing

- Chunk uploads accept raw binary data (final file validation only)

- SQLite chosen for simplicity and automated testing

- Clean separation of concerns and readable commit history
