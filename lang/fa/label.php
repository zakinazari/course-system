<?php

return [
       // -----------start general-------------------
    'app_name' => 'سیستم کورس',
    'light' => 'روشن',
    'dark' => 'تاریک',
    'fa' => 'فارسی',
    'en' => 'انگلیسی',
    'pa' => 'پشتو',

    'delete_title'   => 'آیا مطمئن هستید؟',
    'delete_text'    => "این عملیات قابل بازگشت نیست!",
    'confirm_text'   => 'بله، حذف شود!',
    'cancel_text'    => 'لغو',
    'warning'    => 'هشدار',
    'yes'    => 'بله',
    'no'    => 'نخیر',
    'active' => 'فعال',
    'inactive' => 'غیر فعال',
    'select' => '-- لطفا یک گزینه را انتخاب نمایید --',

    'select_all' => 'انتخاب همه',
    'all' => 'همه',
    'cancel' => 'صرف نظر',
    'category' => 'کتگوری',
    'category.required' => 'کتگوری الزامی است',
    'status' => 'وضعیت',
    'view'=>'نمایش',
    'status.required' => 'وضعیت الزامی است',
    "successfully_done" => "موفقانه انجام شد!",
    "store_error" => "خطا در ثبت ریکارد!",
    "successfully_deleted" => "موفقانه حذف شد!",
    "delete_error" => "خطا در حذف ریکارد!",
    "successfully_updated" => "موفقانه ویرایش شد!",
    "update_error" => "خطا در ویرایش ریکارد!",
    'permission_message' => 'شما اجازه دسترسی ندارید',
    'create' => 'ساختن',
    'add' => 'اضافه',
    'edit' => 'ویرایش',
    'delete' => 'حذف',
    'save' => 'ثبت',
    'submit' => 'ارسال',
    'update' => 'به‌روزرسانی',
    'close' => 'بستن',
    'adding' => 'اضافه نمودن',
    'editing' => 'ویرایش نمودن',
    'updating' => 'به‌روزرسانی نمودن',
    'deleting' => 'حذف نمودن',
    'NO' => 'شماره',
    'actions' => 'عملیات',
    'print' => 'چاپ',
    'choose_file' => 'انتخاب فایل',
    'upload' => 'آپلود',
    'add_new_record' => 'ریکارد جدید',
    'search' => 'جستجو',
    'export' => 'دانلود',
    'export_to_excel' => 'دانلود اکسل',
    'export_to_pdf' => 'دانلود PDF',
    'show' => 'نمایش',
    'entries' => 'مورد',
    'prev' => 'قبلی',
    'next' => 'بعدی',
    'section' => 'بخش',
    'section.required' => 'انتخاب بخش الزامی است',
    // -----------end  general-------------------
    
    // -----start settings--------------------
        'access_role' => 'نقش',
        'role_name' => 'نام نقش',
        'role_name.required' => ' نام نقش الزامی است.',
        'role_name.string' => 'نام نقش باید متنی باشد.',
        'role_name.max' => 'نام نقش نباید بیش از 255 کاراکتر باشد.',
        'role_name.unique' => 'این نام نقش قبلاً ثبت شده است.',
        'admin' => 'ادمین',
    // -----end settings----------------------

    // -----start settings--------------------

    'menu' => 'منو',
    'menu_name' => 'نام منو',
    'menu_name_en' => 'نام انگلیسی منو',
    'url' => 'آدرس لینک',
    'icon' => 'آیکن',
    'order' => 'ترتیب نمایش',
    'menu_type' => 'نوع منو',
    'parent' => 'منوی والد',
    'grand_parent' => 'منوی والدِ والد',

    'menu_name_en.required' => 'وارد کردن نام انگلیسی منو الزامی است.',
    'menu_name_en.unique' => 'این نام منو قبلاً استفاده شده است.',
    'menu_type.required' => 'انتخاب نوع منو الزامی است.',
    'order.required' => 'ورود ترتیب نمایش الزامی است.',
    'parent.required' => 'انتخاب منوی والد الزامی است.',

    'is_just_viewed' => 'فقط قابل مشاهده',
    'has_operations' => 'دارای عملیات (مشاهده/افزودن/ویرایش/حذف)',
    'has_operations_confirm' => 'دارای عملیات (مشاهده/افزودن/ویرایش/حذف/تأیید)',

    'main_menu' => 'منوی اصلی',
    'sub_menu' => 'زیرمنو',

        
    // -----end settings----------------------


    // ----------start submission labels--------------
    'submission'=>'مقاله',
    'submission_submit'=>'ارسال مقاله',
    'title'=>'عنوان',
    'title.required'=>'عنوان الزامی است',
    'title_fa.max' => 'عنوان نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    'title_en.max' => 'عنوان نباید بیشتر از ۲۵۵ کاراکتر باشد.',
    'title_pa.max' => 'عنوان نباید بیشتر از ۲۵۵ کاراکتر باشد.',

    'keywords'=>'کلیدواژه ها',
    'keywords.placeholder'=>'کلمات کلیدی را تایپ کرده و Enter، ویرگول یا فاصله بزنید.',
    'abstract'=>'چکیده',
    'abstract_required' => 'فیلد چکیده الزامی است',
    'abstract_wordcount_min' => 'چکیده باید حداقل :min کلمه داشته باشد',
    'abstract_wordcount_max' => 'چکیده نباید بیشتر از :max کلمه باشد',
    'keywords_min' => 'حداقل :min کلیدواژه باید وارد شود',
    'keywords_max' => 'حداکثر :max کلیدواژه مجاز است',
    'file_name_too_long' => 'نام فایل نباید بیشتر از :max کاراکتر باشد',

    'contributor' => 'مشارکت‌کننده',
    'given_name' => 'نام',
    'family_name' => 'نام خانوادگی',
    'email' => 'ایمیل',
    'password' => 'رمز عبور',
    'confirm_password' => 'تأیید رمز عبور',
    'phone' => 'تلفن',
    'city' => 'شهر',
    'affiliation' => 'وابستگی / مؤسسه',
    'country' => 'کشور',
    'author_type' => 'نقش مشارکت‌کننده',
    
    'editor_comment' => 'نظر سردبیر',
    'author_comment' => 'نظر نویسنده',
    'comment' => 'نظر',
    'comments_for_editor' => 'نظرات برای سردبیر',
    'comments_for_author' => 'نظرات برای نویسنده',
    'comments_for_reviewer' => 'نظرات برای داور',
    
    'details' => 'جزئیات',
    'file_upload' => 'آپلود فایل',
    
    'files' => 'فایل ها',
    'download' => 'دانلود',
    'file_required' => 'لطفاً فایل را انتخاب کنید.',
    'file_invalid' => 'فایل انتخاب‌شده معتبر نیست.',
    'file_max' => 'حجم فایل نباید بیشتر از :value مگابایت باشد.',
    'file_mimes' => 'فقط فایل‌های :value مجاز هستند.',
    'uploaded_files' => 'فایل های آپلود شده',
    'contributors' => 'مشارکت کننده ها',
    'author' => 'نویسنده',

    'details_step_required_message' => 'لطفا تمام فیلدهای الزامی را پر کنید',
    'file_upload_step_required_message' => 'لطفا حداقل یک فایل آپلود نمایید',
    'contributors_step_required_message' => 'لطفا حداقل یک مشارکت کننده اضافه نمایید',

    'workflow' => 'گردش کار',
    'reviews' => 'داوری ها',
    'reviewers' => 'پوهنتون',
    'reviewer' => 'پوهنتون',
    'reviewer.required' => 'پوهنتون الزامی است.',

    'round' => 'دور',
    'screening' => 'بررسی اولیه',
    'send_for_review' => 'ارسال به داوری برای پوهنتون',
    'accept_skip_review' => ' پذیرفتن مقاله',
    'reject_submission' => 'رد مقاله',
    'revision_required' => 'نیاز به اصلاح',
    'assign_to_issue' => 'واگذاری به دوره نشر',
    
    'editor_decision' => 'تصمیم سردبیر',
    'reviewer_recommendations' => ' پیشنهادات داوران',
    'recommendations' => 'پیشنهادات',
    'reviewer_declined_reason' => 'دلیل رد داوری',
    'reviewer_declined_reasons' => 'دلایل رد داوری',

    'accept_review' => 'قبول داوری',
    'decline_review' => 'رد داوری',
    'make_recommendation' => 'ثبت پیشنهاد',
    'recommendation' => 'پیشنهاد', 
    'recommendation.required' => ' پیشنهاد الزامی است.', 

    'main_axes' => 'محورهای اصلی', 
    'sub_axes' => 'محورهای فرعی', 
    'main_axis.required' => 'محور اصلی الزامی است', 
    'sub_axis.required' => 'محور فرعی الزامی است', 
    'accepted_abstracts' => 'خلاصه های پذیرفته شده', 
    'accepted_abstract_id.required' => 'خلاصه های پذیرفته شده الزامی است.', 

    'processing' => 'در حال پردازش…',
    'upload_failed' => 'آپلود ناموفق بود!',

    'facebook' => 'فیسبوک',
    'website' => 'وبسایت',
    'address' => 'آدرس',
    'logo' => 'لوگو',
    // ----------end submission labels----------------

    // --------start issues------------------
    'issue'=>'دوره انتشار',
    'volume'=>'دوره',
    'number'=>'شماره',
    'date_published'=>'تاریخ نشر',
    'cover_image'=>'عکس پس زمینه',
    'image'=>'عکس',

    'volume.required'=>'جلد الزامی است.',
    'number.required'=>'صفحه الزامی است.',
    // --------end issues--------------------

     // --------start issues------------------
    'page'=>'صفحه',
    'content'=>'محتوا',
    'name'=>'نام',
    'name.required'=>'نام الزامی است',
    'title.required'=>'عنوان الزامی است',
    // --------end issues--------------------

    // --------start users label--------------------------
     
    // نام
    'name_required' => 'فیلد نام الزامی است.',
    'name_string'   => 'نام باید یک رشته معتبر باشد.',
    'name_max'      => 'نام نباید بیش از :max کاراکتر باشد.',
    'name_unique'   => 'این نام قبلاً ثبت شده است.',

    // ایمیل
    'email_required' => 'فیلد ایمیل الزامی است.',
    'email_invalid'  => 'لطفاً یک آدرس ایمیل معتبر وارد کنید.',
    'email_unique'   => 'این ایمیل قبلاً ثبت شده است.',

    // نقش‌ها
    'roles_required' => 'لطفاً حداقل یک نقش انتخاب کنید.',
    'roles_array'    => 'انتخاب نقش نامعتبر است.',

    // رمز عبور
    'password_required' => 'فیلد رمز عبور الزامی است.',
    'password_string'   => 'رمز عبور باید یک رشته معتبر باشد.',
    'password_min'      => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
    'password_regex'    => 'رمز عبور باید حداقل شامل یک حرف بزرگ، یک عدد و یک کاراکتر خاص باشد.',

    // CONFIRM PASSWORD
    'password_confirm_same' => 'تأیید رمز عبور مطابقت ندارد.',

    'main_page'    => 'صفحه اصلی',
    'profile'    => 'پروفایل',
    'my_account'    => 'حساب من',
    'register' => 'ثبت نام',
    'dashboard' => 'داشبورد',
    'login' => 'ورود',
    'user_login' => 'ورود کاربران',
    'user_name' => 'نام کاربری',
    'email_or_phone_no' => 'ایمیل یا شماره تماس',
    'forgot_password' => 'رمز عبور خود را فراموش کرده‌اید؟',
    'remember_me' => 'به خاطر سپاری نام کاربری و رمز عبور',
    'preferred_research_area' => 'حوزه تحقیق مورد علاقه',
    'education_degree' => 'تحصیلات',
    'academic_rank' => 'رتبه علمی',
    'province' => 'ولایت',
    'department' => 'رشته تخصصی',
    // --------start users label--------------------------

    'number_of_read'=>'تعداد خوانده شده ها',
    'indexes'=>'نمایه‌ها',
    'last_news'=>'آخرین خبرها',
    'all_posts'=>'همه‌ی خبرها',

 // start dashboard labels-----------------------
    'articles' => 'مقاله‌ها',
    'articles_overview' => 'وضعیت کلی مقاله‌ها',
    'submitted_articles'       => 'مقاله‌های ارسال‌شده',
    'initial_review_articles'  => 'مقاله‌های در حال بررسی اولیه',
    'under_review_articles'    => 'مقاله‌های درحال داوری',
    'revised_articles'         => 'مقاله‌های نیازمند بازنگری دوباره',
    'accepted_articles'        => 'مقاله‌های پذیرفته‌شده',
    'rejected_articles'        => 'مقاله‌های رد شده',
    'published_articles'       => 'مقاله‌های منتشر شده',
    
    'admins_count'      => 'تعداد مدیران',
    'reviewers_count'   => 'تعداد داوران',
    'authors_count'     => 'تعداد نویسندگان',
    'users_count'       => 'مجموع کاربران',
     // end dashboard labels-----------------------

     // start profile validation-------------------
    'phone_no.unique' => 'این شماره تلفن قبلاً ثبت شده است.',
    'phone_no.max'    => 'شماره تلفن نباید بیشتر از ۱۰ رقم باشد.',
    'phone_no.numeric'    => 'شماره تماس باید عددی باشد',

    'profile_photo_image' => 'فایل انتخاب شده باید یک تصویر معتبر باشد.',
    'profile_photo_max'   => 'حجم تصویر نباید بیشتر از ۱ مگابایت باشد.',

    'change_password'   => 'تغییر رمز عبور',
    'current_password'        => 'رمز عبور فعلی',
    'new_password'            => 'رمز عبور جدید',
    'password_confirmation'   => 'تایید رمز عبور',
    'password_uppercase'      => 'پسورد باید حداقل یک حرف بزرگ داشته باشد',
    'password_symbol'         => 'پسورد باید حداقل یک علامه داشته باشد',

    'is_required'             => 'الزامی است',
    'must_be_8'               => 'باید حداقل 8 کاراکتر باشد',
    'not_match'               => 'با پسورد جدید مطابقت ندارد',

    'password_requirements' => 'نیازمندی‌های پسورد:',
    'password_uppercase'      => 'پسورد باید حداقل یک حرف بزرگ داشته باشد',
    'password_symbol'         => 'پسورد باید حداقل یک علامه داشته باشد',
    // end profile validation---------------------
    'leadership_board'         => 'بورد رهبری',

    'articles_graph' => 'مقاله‌ها (بر اساس نمودار)',
    'my_articles_graph' => 'مقاله‌های من (بر اساس نمودار)',
    'submitted'          => 'ارسال شده',
    'screening'          => 'بررسی اولیه',
    'under_review'       => 'در حال داوری',
    'revision_required'  => 'نیاز به بازبینی',
    'accepted'           => 'پذیرفته شده',
    'rejected'           => 'رد شده',
    'published'          => 'منتشر شده',
    'unpublished'          => 'منتشر نشده',
    'publish'   => 'منتشر کردن',
    'unpublish' => 'عدم انتشار',

    'pending'   => 'در انتظار',
    'completed' => 'تکمیل شده',
    'declined'  => 'رد شده',

    'my_assigned_papers_status' => 'وضعیت مقالات محول‌شده به من',

    'my_articles' => 'مقاله‌های من',
    'all_articles' => 'همه‌ی مقاله‌ها',
    'latest_articles' => 'آخرین مقاله‌ها',
    'similar_articles' => 'مقالات مشابه',
    'official_journals' => 'جریده‌های رسمی',
    'search_by_title' => 'جستجو نظر به عنوان مقاله',
    'search_by_author' => 'جستجو نظر به نویسنده',
    'wardak_university' => [
        'name' => 'پوهنتون وردگ',
        'email' => 'ایمیل: info@wu.edu.af',
        'website' => 'وبسایت: www.wu.edu.af',
        'facebook' => 'صفحه فسبوک: وردګ پوهنتون (Wardak University)',
        'contact' => 'تماس: 0781785612 (۹۳+)',
        'address' => 'آدرس: بزرگراه کابل-قندهار، دشت توپ ، ولسوالی سیدآباد، میدان وردگ – افغانستان',
    ],
    'national_conference' => [
        'name' => 'کنفرانس داخلی فرامین امیرالمؤمنین حفظه الله تعالی و رعاه',
        'email' => 'ایمیل: national.conference@wu.edu.af',
        'website' => 'وبسایت: ',
        'facebook' => 'صفحه فسبوک: وردګ پوهنتون (Wardak University)',
        'contact' => 'تماس‌ها: 0787052050 - 0773805403 (93+)',
        'address' => 'آدرس: بزرگراه کابل-قندهار، دشت توپ ، ولسوالی سیدآباد، میدان وردگ – افغانستان',
    ],

    'follow_us_on'=>'ما را دنبال کنید در',
    'all_rights_reserved'=>' تمام حقوق محفوظ است',
    'members' => 'اعضا',
    'add_member' => 'افزودن عضو',
    'scientific_board_members' => 'اعضای بورد علمی',
    'file_type_not_allowed' => 'این نوع فایل مجاز نیست',


    'gazette' => 'جریده',
    'gazette_number' => 'شماره جریده',
    'gazette_number.required' => 'شماره جریده الزامی است',
    'gazette_number.unique' => 'شماره جریده تکرار است',
    'gazette.required' => 'جریده الزامی است',
    'gazette_date' => 'تاریخ  نشر جریده',
    'gazette_date.required' => 'تاریخ نشر جریده الزامی است',
    'gazette_date.size' => 'فرمت تاریخ باید به شکل YYYY/MM/DD باشد',
    'gazette_date.regex' => 'فرمت تاریخ باید به شکل YYYY/MM/DD باشد',

    'decree' => [
        'decree_list' => 'فهرست احکام',
        'decree_number' => 'شماره حکم',
        'decree_date' => 'تاریخ حکم',
        'decree_number.required' => 'شماره حکم الزامی است',
        'decree_number.unique' => 'شماره تکراری است',
        'decree_date.required' => 'تاریخ حکم الزامی است',
        'decree_date.size' => 'فرمت تاریخ باید به شکل YYYY/MM/DD باشد',
        'decree_date.regex' => 'فرمت تاریخ باید به شکل YYYY/MM/DD باشد',
    ],
    'ruling_order' => [
        'order_list' => 'فهرست فرامین',
        'order_number' => 'شماره فرمان',
        'order_date' => 'تاریخ فرمان',
        'order_number.required' => 'شماره فرمان الزامی است',
        'order_number.unique' => 'شماره تکراری است',
        'order_date.required' => 'تاریخ فرمان الزامی است',
        'order_date.size' => 'فرمت تاریخ باید به شکل YYYY/MM/DD باشد',
        'order_date.regex' => 'فرمت تاریخ باید به شکل YYYY/MM/DD باشد',
    ],
];
