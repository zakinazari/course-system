<?php
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\GazetteFileController;
use App\Http\Controllers\FileController;
use App\Livewire\Settings\AccessRoles\AccessRoleList;
use App\Livewire\Notes;
use App\Http\Middleware\PreventDirectAuthRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return redirect('login');
});

// ------------start admin panel route----------------------------
Route::middleware(['auth'])->group(function () {
    
    Route::get('locale/admin/{locale}', function ($locale, \Illuminate\Http\Request $request) {
        if (!in_array($locale, ['en','fa','pa'])) abort(400);
        $request->session()->put('admin_locale', $locale);
        return redirect()->back();
    })->name('locale.admin');


    Route::get('/theme/{style}', [ThemeController::class, 'setTheme'])->name('set-theme');
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('/dashboard/{menu_id?}', function ($menu_id = null) {

        return view('livewire.dashboard-page',['menu_id' => $menu_id]);

    })->name('dashboard');

    // -----------start access roles -----------------------
    Route::get('/access-roles/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.settings.access-roles.access-role-page',['menu_id' => $menu_id]);
    })->name('access-roles');
    // -----------end access roles -------------------------
    
    // -----------start users roles -----------------------
    Route::get('/users/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.settings.users.user-list-page',['menu_id' => $menu_id]);
    })->name('users');
    // -----------end users roles -------------------------

     Route::get('/my-account/{menu_id?}', function ($menu_id = null) {
        // if (!read(Auth::user()->role_ids, $menu_id)) {
        //     abort(403, __('label.permission_message'));
        // }
        return view('livewire.settings.my-account.my-account-page',['menu_id' => $menu_id]);
    })->name('my-account');

    Route::get('/menus/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.settings.menus.menus-page', ['menu_id' => $menu_id]);
    })->name('menus');

    Route::get('/permissions/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.settings.permissions.permissions-page', ['menu_id' => $menu_id]);
    })->name('permissions');

    
    Route::get('/branches/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.center-settings.branches.branch-list-page',['menu_id' => $menu_id]);
    })->name('branches');

    Route::get('/programs/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.center-settings.programs.program-list-page',['menu_id' => $menu_id]);
    })->name('programs');

    Route::get('/books/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.center-settings.books.book-list-page',['menu_id' => $menu_id]);
    })->name('books');

    Route::get('/classrooms/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.center-settings.classrooms.classroom-list-page',['menu_id' => $menu_id]);
    })->name('classrooms');

    Route::get('/placement-test-settings/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.center-settings.placement-test-settings.placement-test-settings-list-page',['menu_id' => $menu_id]);
    })->name('placement-test-settings');

    Route::get('/discount-providers/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.center-settings.discount-providers.discount-providers-list-page',['menu_id' => $menu_id]);
    })->name('discount-providers');

    // -----start academic---------------------------
    Route::get('/visitors/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.visitors.visitor-list-page',['menu_id' => $menu_id]);
    })->name('visitors');

    Route::get('/meetings/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.meetings.meeting-list-page',['menu_id' => $menu_id]);
    })->name('meetings');

    Route::get('/students/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.students.student-list-page',['menu_id' => $menu_id]);
    })->name('students');

    Route::get('/search-visitors', function (Request $request) {

        $q = $request->q;
        $branch_id = Auth::user()->branch_id;

        return DB::table('visitors')
            ->select('id','name','last_name','father_name','phone_no')
             ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone_no', 'like', "%{$q}%");
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->limit(20)
            ->get()
            ->map(fn ($item) => [
                'id'   => $item->id,
                'text' => trim("{$item->name} {$item->last_name}") .
                ($item->father_name ? " - Son/Daughter of {$item->father_name}" : '') .
                ($item->phone_no ? " - 📞 {$item->phone_no}" : ''),
            ]);
    });

    Route::get('/course-list/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.courses.course-list-page',['menu_id' => $menu_id]);
    })->name('course-list');

    Route::get('/courses/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.enrollments.course-list-page',['menu_id' => $menu_id]);
    })->name('courses');

    Route::get('course-enrollments/{menu_id?}/{course_id?}/{student_id?}', function ($menu_id = null,$course_id = null,$student_id=null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.enrollments.course-enrollments-page', ['menu_id' => $menu_id,'course_id'=>$course_id,'student_id'=>$student_id]);
    })->whereNumber('menu_id')->name('course-enrollments');

    Route::get('/search-students{menu_id?}', function (Request $request,$menu_id=null) {

        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        $q = $request->q;  

        return DB::table('students')
            ->select('id','name','last_name','father_name','phone_no')
             ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone_no', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get()
            ->map(fn ($item) => [
                'id'   => $item->id,
                'text' => trim("{$item->name} {$item->last_name}") .
                ($item->father_name ? " - Son/Daughter of {$item->father_name}" : '') .
                ($item->phone_no ? " - 📞 {$item->phone_no}" : ''),
            ]);
    });

    Route::get('waiting-list/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.course-waiting.waiting-students-page', ['menu_id' => $menu_id]);
    })->whereNumber('menu_id')->name('waiting-list');

    Route::get('placement-test/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.placement-tests.placement-test-list-page', ['menu_id' => $menu_id]);
    })->whereNumber('menu_id')->name('placement-test');

    Route::get('special-course-list/{menu_id?}/{student_id?}', function ($menu_id = null,$student_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.enrollments.special-course-list-page', ['menu_id' => $menu_id,'student_id'=>$student_id]);
    })->whereNumber('menu_id')->name('special-course-list');
    
    Route::get('special-enrollment/{menu_id?}/{student_id?}', function ($menu_id = null,$student_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.academic.enrollments.special-enrollment-page', ['menu_id' => $menu_id,'student_id'=>$student_id]);
    })->whereNumber('menu_id')->name('special-enrollments');
    
    

    // ------------end academic-------------------------

    

    // -----------start assessment-------------------
    
    Route::get('student-attendance/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.assessment.attendance.student-attendance-list-page', ['menu_id' => $menu_id]);
    })->whereNumber('menu_id')->name('student-attendance');

    Route::get('mark-entry/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.assessment.mark-entry.student-course-result-entry-page', ['menu_id' => $menu_id]);
    })->whereNumber('menu_id')->name('mark-entry');

    Route::get('student-course-result-list/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.assessment.student-result.student-course-result-list-page', ['menu_id' => $menu_id]);
    })->whereNumber('menu_id')->name('student-course-result-list');

    // -----------end assessment-------------------

    // ----------start Financial-----------------------------
    Route::get('student-fees/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.financial.student-fees.student-fees-search-page', ['menu_id' => $menu_id]);
    })->whereNumber('menu_id')->name('student-fees');

    Route::get('student-financial-profile/{menu_id?}/{student_id?}', function ($menu_id = null,$student_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.financial.student-fees.student-financial-profile-page', ['menu_id' => $menu_id,'student_id'=>$student_id]);
    })->whereNumber('menu_id')->name('student-financial-profile');

    // ----------end Financial-----------------------------

    // -------start Hr----------------------
    Route::get('/employees/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.hr.employees.employee-list-page',['menu_id' => $menu_id]);
    })->name('employees');
    
    // -------end Hr------------------------

});
// -----------------------end admin panel routes-------------------------------
require __DIR__.'/auth.php';
