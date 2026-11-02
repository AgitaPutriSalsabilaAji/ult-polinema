<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen {{ $fileType }} | ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: grid;
            min-height: 100vh;
            place-items: center;
            margin: 0;
            padding: 24px;
            background: #f3f3f5;
            color: #1f2230;
            font-family: Inter, Arial, sans-serif;
        }

        .preview-card {
            width: min(100%, 560px);
            padding: 32px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 14px 30px rgba(34, 19, 62, 0.08);
            text-align: center;
        }

        .preview-icon {
            display: grid;
            width: 68px;
            height: 76px;
            margin: 0 auto 20px;
            place-items: center;
            border-radius: 14px;
            background: #f3ecff;
            color: #4a1d7a;
            font-size: 1.15rem;
            font-weight: 800;
        }

        h1 {
            margin-bottom: 10px;
            color: #2d124f;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .file-name {
            overflow-wrap: anywhere;
            margin-bottom: 12px;
            color: #4a1d7a;
            font-weight: 700;
        }

        p {
            margin-bottom: 22px;
            color: #6b7280;
            line-height: 1.6;
        }

        .preview-download {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: 10px;
            background: #4a1d7a;
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
        }

        .preview-download:hover {
            background: #2d124f;
            color: #fff;
        }
    </style>
</head>
<body>
    <main class="preview-card">
        <div class="preview-icon" aria-hidden="true">{{ $fileType }}</div>
        <h1>Dokumen belum mendukung preview</h1>
        <div class="file-name">{{ $fileName }}</div>
        <p>{{ $message }}</p>
        <p>Jenis file: {{ $mimeType ?: 'tidak diketahui' }}</p>
        <a class="preview-download" href="{{ $downloadUrl }}">Unduh Dokumen</a>
    </main>
</body>
</html>
