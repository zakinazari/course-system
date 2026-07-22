<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Diploma Verification</title>
    <link rel="icon" href="{{asset('favicon.png')}}" type="">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            width: 430px;
            background: #fff;
            border-radius: 18px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .logo {
            margin-bottom: 15px;
        }

        .logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 25px;
            color: #333;
        }

        .info {
            font-size: 15px;
            color: #555;
            margin: 10px 0;
            line-height: 1.7;
        }

        .badge {
            margin-top: 25px;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
        }

        .valid {
            background: #e7f8ee;
            color: #1a7f37;
            border: 1px solid #1a7f37;
        }

        .revoked {
            background: #fdeaea;
            color: #c62828;
            border: 1px solid #c62828;
        }

        .notfound {
            background: #f1f1f1;
            color: #666;
            border: 1px solid #bbb;
        }

        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>

<body>

<div class="card">

    {{-- LOGO --}}
    <div class="logo">
        <img src="{{ asset('logo.png') }}" alt="Logo">
    </div>

    <div class="title">
       🎓 Diploma Verification
    </div>

    @if(!$diploma)

        <div class="badge notfound">
            ❌ Diploma Not Found
        </div>

        <div class="info">
            This verification code is invalid.
        </div>

    @else

        <div class="info">
            <strong>Student:</strong><br>
            {{ $diploma->student?->name }}
            {{ $diploma->student?->last_name }}
        </div>

        <div class="info">
            <strong>Serial Number:</strong><br>
            {{ $diploma->serial_number }}
        </div>

        @if($diploma->is_revoked)

            <div class="badge revoked">
                ❌ Revoked
            </div>

            <div class="info">
                This diploma has been officially revoked.
            </div>

        @else

            <div class="badge valid">
                ✅ Verified
            </div>

            <div class="info">
                This diploma is authentic and valid.
            </div>

        @endif

    @endif

    <div class="footer">
        Verification System © {{ date('Y') }}
    </div>

</div>

</body>
</html>