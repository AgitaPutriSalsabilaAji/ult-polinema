<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Tidak Tersedia | ULT Polinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(180deg, #f7f4ff 0%, #ffffff 100%);
            font-family: Inter, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            color: #1f2937;
        }
        .error-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(91, 58, 163, 0.10);
            max-width: 560px;
            width: 100%;
            padding: 32px 28px;
            text-align: center;
        }
        .error-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(124, 58, 237, 0.10);
            color: #6d28d9;
            font-size: 32px;
            margin-bottom: 18px;
        }
        h1 {
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            font-weight: 800;
            margin-bottom: 12px;
        }
        p {
            margin: 0 0 20px;
            line-height: 1.7;
            color: #4b5563;
        }
        .btn-primary {
            background: #6d28d9;
            border-color: #6d28d9;
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-badge">!</div>
        <h1>Dokumen Tidak Tersedia</h1>
        <p>{{ $message ?? 'Lampiran yang Anda pilih tidak dapat dibuka saat ini.' }}</p>
        <a href="{{ url()->previous() ?: route('cek-status') }}" class="btn btn-primary">Kembali</a>
    </div>
</body>
</html>
