<div id="print-area" dir="ltr">

    <style>

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            padding:0;
            background:#f5f5f5;
            /* /font-family:"Times New Roman", serif; */
        }

        #print-area{
            width:100%;
            background:#fff;
            padding:40px;
            position:relative;
            overflow:hidden;
            color:#000;

        }

        .diploma-wrapper{
            position:relative;
            /* min-height:1100px; */
        }

        /* ================= WATERMARK ================= */

        .watermark-logo{
            position:absolute;
            inset:0;
            display:flex;
            justify-content:center;
            align-items:center;
            opacity:0.05;
            z-index:0;
            pointer-events:none;
        }

        .watermark-logo img{
            width:450px;
        }

        .diploma-content{
            position:relative;
            z-index:2;
        }

        /* ================= HEADER ================= */

        .top-header{
            text-align:center;
        }

        .top-logo img{
            width:120px;
            height:120px;
            object-fit:contain;
        }

        .title-line{
            margin-top:6px;
            font-size:14.5px;
            font-weight:bold;
            line-height:1;
            font-family: 'Ray', sans-serif;
        }

        /* ================= DIPLOMA ROW ================= */

        .diploma-row{
            display:flex;
            width:100%;
            align-items:center;
        }

        /* 3 equal parts */
        .row-side{
            width:33.33%;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        /* LEFT */
        .left-side{
            justify-content:flex-start;
        }

        /* CENTER LOGO */
        .center-side{
            justify-content:center;
        }

        /* RIGHT */
        .right-side{
            justify-content:flex-end;
        }

        /* LOGO SIZE */
        .diploma-logo{
            width:200px;
            height:200px;
            object-fit:contain;
            margin-top:-60px;
        }

        /* PHOTO */
        
        .student-photo{
            
            width:115px;
            height:145px;
            overflow:hidden;   
            border:1px solid green;
            margin-top:-145px;
        }

        .student-photo img{
            
            width:100%;
            height:100%;
            object-fit:cover;
            display:block;
        }

        /* ================= LANGUAGE SECTION ================= */

            .language-section{
                margin-top:-60px;
                width:100%;
            }

            .language-grid{
                display:grid;
                grid-template-columns:repeat(3, 1fr);
                gap:25px;
                align-items:start;
            }

            .language-box{
                width:100%;
                min-width:0;
            }

            .language-title{
                text-align:center;
                font-size:18px;
                font-weight:bold;
                margin-bottom:20px;
            }

            .language-text{
                font-size:14.5px;
                line-height:1.7;

            
                white-space:normal;

            
                overflow-wrap:break-word;
                word-break:normal;

                text-align:justify;
            }

            /* RTL LANGUAGES */
            .language-text[dir="rtl"]{
                text-align:justify;
                direction:rtl;
                font-family: 'Ray', sans-serif;
                
            }

            .language-text[dir="ltr"]{
                font-family: 'Cambriab', sans-serif;
            }

            .language-text[dir="ltr"]{
                text-align: justify;
                hyphens: auto;
                line-height: 1.7;
                font-size: 13.5px;
            }

            /* RESPONSIVE */
            @media(max-width:992px){

                .language-grid{
                    grid-template-columns:1fr;
                }

            }

            /* grade */
            .marks-grade{
                margin-top:40px;

                display:flex;
                justify-content:space-between;
                align-items:center;

                gap:40px;
            }

            .marks-grade .item{
                /* font-weight:bold; */
                font-size:15px;
                font-family:"Times New Roman", serif;
            }

            .marks-grade .value{
                display:inline-block;

                min-width:60px;

                text-align:center;

                border-bottom:2px solid #000;

                padding-bottom:2px;

                margin-left:6px;
                padding-bottom:0;   
                line-height:1;     

                position:relative;
                top:-5px;

            }

        /* ================= FOOTER ================= */

        .footer-section{
            margin-top:20px;
        }

        .footer-wrapper{
            display:flex;
            justify-content:space-between;
            align-items:flex-end;
            gap:20px;
        }

        .signature-box{
            width:12%;
            text-align:center;
            flex-shrink:0;
            white-space: nowrap;
        }

        .signature-line{
            border-top:2px solid #0cacd8d7;
            padding-top:10px;
            margin-bottom:8px;
        }

        .signature-title{
            font-size:16px;
            /* font-weight:bold; */
            /* font-family:"Times New Roman", serif; */

            font-family: 'Ray', sans-serif;
        }

        .footer-logo{
            width:18%;
            text-align:bottom;
            flex-shrink:0;
        }

        .footer-logo img{
            width:200px;
            /* height:200px; */
            object-fit:contain;
            
        }

        /* ============qr code-------------- */
        .qr-box{
            width:18%;
            text-align:center;
            flex-shrink:0;
            
        }

        .qr-box svg{
            width:100px;
            height:100px;
            border:1px solid green ;
        }

    
        .qr-wrapper{
            position:relative;

            width:100px;
            height:100px;

            display:flex;
            justify-content:center;
            align-items:center;

            padding-left:10px;
            padding-top:40px;
        }

        /* QR */
        .qr-box{
            width:100px;
            height:100px;

            display:flex;
            justify-content:center;
            align-items:center;
        }

        .qr-box svg,
        .qr-box img{
            width:100px !important;
            height:100px !important;

            display:block;
        }

        /* CENTER LOGO */
        .qr-logo-overlay{
            position:absolute;

            top:50%;
            left:50%;

            transform:translate(-50%, -50%);

            margin-left:5px;
            margin-top:20px;

            width:26px;
            height:26px;

            z-index:5;

            display:flex;
            justify-content:center;
            align-items:center;
        }

        .qr-logo-overlay img{
            width:100%;
            height:100%;
            object-fit:contain;
        }
        /* ================= PRINT ================= */

        @media print{

            body *{
                visibility:hidden;
            }

            #print-area,
            #print-area *{
                visibility:visible;
            }

            #print-area{
                position:absolute;
                left:0;
                top:0;
                width:100%;
                padding:20px;
            }

        }

    </style>

    <div class="diploma-wrapper">

        <!-- WATERMARK -->
        <div class="watermark-logo">

            <img src="{{ url('logo/logo.png') }}">

        </div>

        <div class="diploma-content">

            <!-- ================= HEADER ================= -->

            <div class="top-header">

                <div class="top-logo">

                    <img src="{{ asset('assets/logos/emirate_logo.png') }}">

                </div>

                <div class="title-line">
                د افغانستان اسلامي امارت
                </div>

                <div class="title-line">
                    د پوهنې وزارت
                </div>

                <div class="title-line">
                    د خصوصي زده کړو ریاست
                </div>

                <div class="title-line">
                د خصوصي ښوونیزو بنسټونو او زده کوونکو چارو د تنظیم آمریت
                </div>

                <div class="title-line">
                    چېنج د انګلیسي ژبې او کمپیوټر مرکز
                </div>

            </div>

            <!-- ================= DIPLOMA ROW ================= -->

            <div class="diploma-row">

                <!-- LEFT -->
                <div class="row-side left-side">
                    <div class="student-photo">
                        <img src="{{ $diploma->student->photo?->file_url ?? asset('default.png') }}">
                    </div>
                </div>

                <!-- CENTER (LOGO) -->
                <div class="row-side center-side">

                    <img
                        src="{{ asset('assets/logos/diploma_title.png') }}"
                        class="diploma-logo"
                    >

                </div>

                <!-- RIGHT -->
                <div class="row-side right-side">
                    <!-- student photo یا هر چیز -->
                    
                </div>

            </div>

            <!-- ================= LANGUAGE SECTION ================= -->

            <div class="language-section">

                <div class="language-grid">

                    <!-- ENGLISH -->
                    <div class="language-box">



                        <div class="language-text" dir="ltr">
                            <p>
                            This is to certify that,

                            @if($diploma->student?->gender_id == 1)
                                Mr. 
                            @else
                                Ms.
                            @endif

                            {{ $diploma->student?->name }} {{ $diploma->student?->last_name }}, {{ $diploma->student?->gender_id == 1 ? 'son of' : 'daughter of' }} Mr. {{ $diploma->student?->father_name }},

                            born in {{ $diploma->student?->date_of_birth?->format('Y') }}, and holding registration No {{ $diploma->student?->student_code }}, 

                            has successfully completed the one-year CELC-DEL English Language Program, 

                            which is registered with the Educational Division of the UK Company House, London. 
                            </p>

                            <p>
                            In recognition of this achievement, this diploma was hereby awarded to him on  {{ $diploma->graduated_at?->format('F d, Y') }}.
                            </p>
                        </div>

                    </div>

                    <!-- DARI -->
                    <div class="language-box">


                        <div class="language-text" dir="rtl">

                        <p>
                            محترم {{ $diploma->student?->name_fa }} {{ $diploma->student?->last_name_fa }}،

                            فرزند {{ $diploma->student?->father_name_fa }}، 

                            متولد سال @if($diploma->student?->date_of_birth!='') {{ verta($diploma->student?->date_of_birth)?->format('Y') }} @endif و

                            دارنده شماره ثبت  {{ $diploma->student?->student_code }}، 
                            
                            برنامه یک‌ساله زبان انگلیسی CELC-DEL را که در بخش آموزشی UK Company House 

                            در شهر لندن ثبت بوده، با موفقیت به پایان رسانیده است.

                        </p>

                        <p>
                            به پاس این دستاورد و موفقیت، این دیپلوم به تاریخ 

                            @if($diploma->graduated_at!='') {{ verta($diploma->graduated_at)?->format('Y/m/d') }} @endif 
                            
                            به ایشان اعطا گردید.
                        </p>

                            
                        </div>

                        <p class="marks-grade">

                            <span class="item">
                                Marks:
                                <span class="value">{{ number_format($diploma->average,0) }}</span>
                            </span>

                            <span class="item">
                                Grade:
                                <span class="value">{{ $diploma->grade }}</span>
                            </span>

                        </p>

                    </div>

                    <!-- PASHTO -->
                    <div class="language-box">

                        <div class="language-text" dir="rtl">
                            <p>
                                
                            @if($diploma->student?->gender_id == 1)
                                    ښاغلی
                                @else
                                    آغلې
                                @endif

                                {{ $diploma->student?->name_pa }}
                                {{ $diploma->student?->last_name_pa }}،

                                @if($diploma->student?->gender_id == 1)
                                    د {{ $diploma->student?->father_name_pa }} زوی
                                @else
                                    د {{ $diploma->student?->father_name_pa }} لور
                                @endif

                                چې په کال @if($diploma->student?->date_of_birth!='') {{ verta($diploma->student?->date_of_birth)?->format('Y') }} @endif

                                    کې زېږېدلی، او د {{ $diploma->student?->student_code }}

                                    ثبت شمېرې لرونکی دی، نوموړي د انګلیسي ژبې یو کلن CELC-DEL 

                                    پروګرام چې د لندن د UK Company House 

                                   تعلیمي څانګې سره ثبت دی، په بریالیتوب سره بشپړ کړی دی.
                            </p>

                            <p>
                                    د نوموړي د دې لاسته راوړنې په ویاړ، دغه ډیپلوم په 
                                    
                                    @if($diploma->graduated_at!='') {{ verta($diploma->graduated_at)?->format('Y/m/d') }} @endif  نېټه ورکړل شو.

                                
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- ================= FOOTER ================= -->

            <div class="footer-section">

                <div class="footer-wrapper">

                    <div class="qr-wrapper">

                        <!-- LOGO -->
                        <div class="qr-logo-overlay">
                            <img src="{{ asset('logo.png') }}">
                        </div>

                        <!-- QR -->
                        <div class="qr-box">
                            {!! $diploma->qr_code !!}
                        </div>

                    </div>

                    <!-- SIGNATURE 1 -->
                    <div class="signature-box">

                        <div class="signature-line"></div>

                        <div class="signature-title">
                            عمومي رئیس
                        </div>

                    </div>

                    <!-- CENTER LOGO -->
                    <div class="footer-logo">
                    
                        <img src="{{ asset('assets/logos/uk_logo.png') }}">

                    </div>

                    <!-- SIGNATURE 2 -->
                    <div class="signature-box">

                        <div class="signature-line"></div>

                        <div class="signature-title">
                            د امتحانونو مسئول
                        </div>

                    </div>

                    <!-- QR -->
                

                </div>

            </div>

        </div>

    </div>

</div>