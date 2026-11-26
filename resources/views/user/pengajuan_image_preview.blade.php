<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $fileName }} | ULT Polinema</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            margin: 0;
            background: #f3f3f5;
            color: #1f2230;
            font-family: Inter, Arial, sans-serif;
        }

        header {
            display: flex;
            min-height: 64px;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 24px;
            background: #fff;
        }

        header {
            border-bottom: 1px solid #e5e7eb;
        }

        h1 {
            min-width: 0;
            flex: 1;
            overflow-wrap: anywhere;
            margin: 0;
            color: #302474;
            font-size: 15px;
        }

        main {
            display: grid;
            min-height: 0;
            flex: 1;
            place-items: center;
            padding: 24px;
        }

        .image-preview {
            position: relative;
            display: inline-block;
            max-width: 100%;
        }

        img {
            display: block;
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: calc(100vh - 160px);
            object-fit: contain;
        }

        .image-preview__download {
            display: inline-flex;
            flex: 0 0 auto;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            padding: 9px 16px;
            border-radius: 9px;
            background: #302474;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .image-preview__download:hover {
            background: #251b66;
        }

        @media (max-width: 600px) {
            header {
                padding-right: 16px;
                padding-left: 16px;
            }

            main {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>{{ $fileName }}</h1>
        <a class="image-preview__download" href="{{ $downloadUrl }}">Unduh Gambar</a>
    </header>
    <main>
        <div class="image-preview">
            <img src="{{ $imageUrl }}" alt="{{ $fileName }}">
        </div>
    </main>
</body>
</html>
