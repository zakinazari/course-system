
<div>
    
    <!-- title -->
      @section('title',(($active_menu?->parent?->name_en ?? '') ? $active_menu?->parent?->name_en . '-' : ''
        ) . $active_menu?->name_en . ' | '.__('label.app_name'))
    <!-- end title -->
    <!-- start header -->
    <h4 class="py-3 breadcrumb-wrapper mb-4">
    @if(!empty($active_menu?->grandParent?->name_en))
    <span class="text-muted fw-light"> @if(App::getLocale()=='en') {{ $active_menu?->grandParent?->name_en }} @else {{ $active_menu?->grandParent?->name }}  @endif  /</span>
    @endif
    @if(!empty($active_menu?->parent?->name_en))
    <span class="text-muted fw-light"> @if(App::getLocale()=='en') {{ $active_menu?->parent?->name_en }} @else {{ $active_menu?->parent?->name }}  @endif /</span>
    @endif
    @if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif
    </h4>
    <!-- end header -->

    <div class="card">
       
       <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ $active_menu?->name }}
                </h5>
            
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <!-- Export Button -->
                    
                    <!-- Add New Record Button -->
                    @if($diploma)
                    <button class="btn btn-secondary mb-3" onclick="printDiv('print-area')">
                        {{ __('label.print') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3 mb-5">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder=" {{ __('label.student_code') }}" wire:model="student_code">
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                </form>

                <!-- perPage -->
               
            </div>
            <div class=" text-nowrap mb-5">
                @if($diploma)
                   <div id="print-area" dir="ltr">

                        <style>

                            body{
                                background:#f5f5f5;
                            }

                            #print-area{
                                background:#fff;
                                padding:35px;
                                position:relative;
                                overflow:hidden;
                                font-family: "Times New Roman", serif;
                            }

                            .diploma-wrapper{
                                position:relative;
                                min-height:900px;
                                padding:10px 40px 40px;
                            }

                            /* ---------- watermark ---------- */

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
                                width:420px;
                            }

                            .diploma-content{
                                position:relative;
                                z-index:2;
                            }
                            /* ---------- top logos ---------- */

                            .top-header{
                                display:flex;
                                justify-content:space-between;
                                align-items:flex-start;
                                margin-bottom:20px;
                            }

                            .top-left-logo img,
                            .top-right-logo img{
                                width:100px;
                                height:100px;
                                object-fit:contain;
                            }

                            .top-center{
                                text-align:center;
                                flex:1;
                            }

                            .top-center img{
                                width:100px;
                                height:100px;
                                object-fit:contain;
                                margin-bottom:10px;
                            }

                            .top-center h2{
                                margin:0;
                                font-size:30px;
                                font-weight:bold;
                                letter-spacing:2px;
                            }

                            .top-center p{
                                margin:5px 0;
                                font-size:18px;
                            }

                            /* ---------- diploma row ---------- */

                            .diploma-row{
                                display:flex;
                                justify-content:space-between;
                                align-items:flex-start;
                                margin-top:30px;
                            }

                            .student-photo{
                                width:170px;
                                text-align:center;
                            }

                            .student-photo img{
                                width:150px;
                                height:170px;
                                object-fit:cover;
                            }

                            .center-name{
                                margin-top:10px;
                                font-size:22px;
                                font-weight:bold;
                                color:#000;
                                letter-spacing:1px;
                            }

                            .diploma-title-box{
                                flex:1;
                                text-align:center;
                            }

                            .diploma-title{
                                font-size:48px;
                                font-weight:bold;
                                letter-spacing:4px;
                                margin-bottom:10px;
                            }

                            .diploma-subtitle{
                                font-size:24px;
                            }

                            /* ---------- content ---------- */

                            .student-content{
                                margin-top:0px;
                            }

                            .content-row{
                                display:flex;
                                justify-content:space-between;
                                align-items:flex-end;
                                gap:30px;
                            }

                            /* left empty */
                            .left-side{
                                width:25%;
                            }

                            /* center content */
                            .center-side{
                                width:50%;
                                text-align:center;
                                min-width:0;
                                overflow-wrap:anywhere;
                                word-break:break-word;
                            }

                            .student-name{
                                font-size:15px;
                                font-weight:bold;
                                margin-bottom:25px;
                            }

                            .student-text{
                                font-size:15px;
                                line-height:2.2;
                                text-align:justify;
                                text-align-last:center;
                            }

                            /* right side */
                            .right-side{
                                width:18%;
                                text-align:center;
                            }

                            .right-center-name{
                                margin-bottom:100px;
                                font-size:35px;
                                font-weight:bold;
                                line-height:1.8;
                                word-break:break-word;
                            }

                            .serial-box{
                                margin-top:40px;
                                font-size:20px;
                            }

                            /* ---------- footer ---------- */

                            .footer-section{
                                margin-top:10px;
                            }

                            .footer-wrapper{
                                display:flex;
                                justify-content:space-between;
                                align-items:flex-end;
                            }

                            .signature-box{
                                width:250px;
                                text-align:center;
                            }

                            .signature-line{
                                border-top:1px solid #000;
                                margin-bottom:10px;
                                padding-top:8px;
                            }

                            .signature-title{
                                font-size:15px;
                                font-weight:bold;
                            }

                            .qr-box{
                                text-align:center;
                                border:2px solid lightgreen;
                            }

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
                                    min-height:100vh;
                                }

                            }

                        </style>

                        <div class="diploma-wrapper">

                            <!-- watermark -->
                            <div class="watermark-logo">
                                <img src="{{ asset('logo.png') }}">
                            </div>

                            <div class="diploma-content">

                                <!-- top header -->
                                <div class="top-header">

                                    <!-- left -->
                                    <div class="top-left-logo">
                                        <img src="{{ asset('logo.png') }}">
                                    </div>

                                    <!-- center -->
                                    <div class="top-center">

                                        <img src="{{ asset('logo.png') }}">

                                       <p> د افغانستان اسلامی امارت</p>
                                       <p> د پوهنی وزارت</p>
                                       <p> د خصوصی زده کړو ریاست</p>
                                       <p> د خصوصی شوونیزو بنسټونو او زده کوونکو چارو د تظیم آمریت</p>
                                       <p> چینج د انګلیسي ژبی او کمپیوټر مرکز</p>

                                    </div>

                                    <!-- right -->
                                    <div class="top-right-logo">
                                        <img src="{{ asset('logo.png') }}">
                                    </div>

                                </div>

                                <!-- diploma row -->
                                <!-- diploma row -->
                                <div class="diploma-row">

                                    <!-- student photo LEFT -->
                                    <div class="student-photo">

                                        <img src="{{ $diploma->student->photo?->thumbnail_url ?? asset('default.png') }}">

                                        <!-- <div class="center-name">
                                            Change
                                        </div> -->

                                    </div>

                                    <!-- center title -->
                                    <div class="diploma-title-box">

                                        <div class="diploma-title">
                                            DIPLOMA
                                        </div>

                                        <div class="diploma-subtitle">
                                            In English Language
                                        </div>

                                    </div>

                                    <!-- empty right side for balance -->
                                    <div style="width:170px;"></div>

                                </div>

                                <!-- content -->
                                <div class="student-content">

                                    <div class="content-row">

                                        <!-- left empty column -->
                                        <div class="left-side">

                                            <div class="right-center-name">
                                             چینج
                                            </div>

                                        </div>

                                        <!-- center content -->
                                        <div class="center-side">

                                            <div class="student-text">

                                                <p>
                                                  @if($diploma->student?->gender_id==1)  Mr. @else Ms.  @endif {{ $diploma->student?->name }}
                                                {{ $diploma->student?->last_name }},

                                                born in 2021, bearing registration number
                                                {{ $diploma->student?->student_code }},

                                                has successfully completed one-year CELC program,
                                                conducted in collaboration with UK Company House,
                                                London.

                                                </p>
                                                <p>
                                                The candidate has achieved an average score of 85,
                                                earning a Grade of B+.

                                                In recognition of this accomplishment,
                                                this diploma is hereby awarded on
                                                {{ $diploma->graduated_at?->format('Y/m/d') }}.
                                                </p>
                                            </div>

                                        </div>

                                        <!-- right center name -->
                                        <div class="right-side" style="width:25%;">
                                            &nbsp;
                                        </div>

                                    </div>

                                </div>

                                <!-- footer -->
                                <div class="footer-section">

                                    <div class="footer-wrapper">

                                        <!-- qr -->
                                        <div class="qr-box">

                                            {!! $diploma->qr_code !!}

                                        </div>

                                        <!-- signature 1 -->
                                        <div class="signature-box">

                                            <div class="signature-line"></div>

                                            <div class="signature-title">
                                                General Director
                                            </div>

                                        </div>

                                        <!-- signature 2 -->
                                        <div class="signature-box">

                                            <div class="signature-line"></div>

                                            <div class="signature-title">
                                                Head of Exam Department
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>

</div>


