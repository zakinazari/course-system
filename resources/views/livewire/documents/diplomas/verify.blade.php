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

        /* ================= NEW PART (PHOTO + DETAILS) ================= */

        .student-box {
            margin-top: 10px;
            padding: 15px;
            border-radius: 12px;
            background: #fafafa;
        }

        .student-photo img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
            margin-bottom: 10px;
        }

        .student-details {
            font-size: 14px;
            color: #444;
            line-height: 1.6;
        }

        .student-details strong {
            color: #222;
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

        {{-- ================= STUDENT INFO ================= --}}
        <div class="student-box">

            <div class="student-photo">
                <img src="{{ $diploma->student?->photo?->file_url ?? asset('default.png') }}">
            </div>

            <div class="student-details">

                <p>
                    This is to certify that 
                    <strong>
                    @if($diploma->student?->gender_id == 1)
                        Mr. 
                    @else
                        Ms.
                    @endif

                    {{ $diploma->student?->name }} {{ $diploma->student?->last_name }}</strong>, {{ $diploma->student?->gender_id == 1 ? 'son of' : 'daughter of' }} <strong> Mr. {{ $diploma->student?->father_name }}</strong>,

                    born in {{ $diploma->student?->date_of_birth?->format('Y') }}, holding registration number <strong>{{ $diploma->student?->student_code }}</strong>, 

                    has successfully completed the one-year CELC-DEL Program, 

                    conducted in collaboration with UK Company House, London. 
                    </p>

                    <p>
                    In recognition of this achievement, 
                    this diploma is hereby awarded on {{ $diploma->graduated_at?->format('F d, Y') }}.
                    </p>

            </div>

        </div>

        {{-- ================= STATUS ================= --}}
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