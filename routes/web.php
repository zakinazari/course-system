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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/{menu_id?}', function ($menu_id = null) {

     return view('livewire.dashboard-page',['menu_id' => $menu_id]);

    })->name('dashboard');
});

//------------- start front routes--------------------

Route::get('/{menu_id?}', function ($menu_id = 1,$slug=null) {
    return view('livewire.front.front-page', ['menu_id' => $menu_id,'slug'=>$slug]);
})->whereNumber('menu_id')->name('home');

Route::get('article/{menu_id?}/{submission_id?}', function ($menu_id = null,$submission_id = null) {
    return view('livewire.front.article-page', ['menu_id' => $menu_id,'submission_id'=>$submission_id]);
})->whereNumber('menu_id')->name('article');

Route::get('post/{slug?}/{post_id?}', function ($slug = null,$post_id = null) {
    return view('livewire.front.single-post-page', ['slug' => $slug,'post_id'=>$post_id]);
})->name('post');    

Route::get('page/{menu_id?}/{slug?}', function ($menu_id = null,$slug= null) {
    return view('livewire.front.front-page', ['menu_id' => $menu_id,'slug'=>$slug]);
})->whereNumber('menu_id')->name('page');

Route::get('login-form', function ($menu_id = null) {
    return view('livewire.front.auth.login-form', ['menu_id' => $menu_id]);
})->whereNumber('menu_id')->middleware(PreventDirectAuthRoutes::class)->name('login-form');
Route::get('registration-form', function ($menu_id = null) {
    return view('livewire.front.auth.registration-form', ['menu_id' => $menu_id]);
})->whereNumber('menu_id')->middleware(PreventDirectAuthRoutes::class)->name('registration-form');


Route::get('/gazette/file/view/{id}', [GazetteFileController::class, 'view'])
    ->name('gazette.file.view');
Route::get('/gazette/ruling/file/view/{id}', [GazetteFileController::class, 'viewRulingFile'])
    ->name('gazette.ruling.file.view');

Route::post('/gazette/upload-file', [GazetteFileController::class, 'upload'])
     ->name('gazette.upload')
     ->middleware('auth');

Route::get('/web-page-file-show/{id}', [FileController::class, 'showWebPageFile'])
->name('web-page-file-show');
//----------- end front routes------------------------

    Route::get('locale/frontend/{locale}', function ($locale, \Illuminate\Http\Request $request) {
        if (!in_array($locale, ['fa','pa'])) abort(400);
        $request->session()->put('frontend_locale', $locale);
        return redirect()->back();
    })->name('locale.frontend');

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

    Route::get('/make-submission/{menu_id?}/{submission_id?}', function ($menu_id = null, $submission_id = null) {
    if (!read(Auth::user()->role_ids, $menu_id)) {
        abort(403, __('label.permission_message'));
    }

    return view('livewire.submissions.make-submission-page', [
        'menu_id' => $menu_id,
        'submission_id' => $submission_id
    ]);

    })->name('make-submission');

    Route::get('/submission-list/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.submissions.submission-list-page', ['menu_id' => $menu_id]);
    })->name('submission-list');

     Route::get('/submission-view-page/{menu_id?}/{submission_id?}', function ($menu_id = null, $submission_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.submissions.submission-view-page', [
            'menu_id' => $menu_id,
            'submission_id' => $submission_id
        ]);
    })->name('submission-view-page');

    Route::get('/assignments/reviewer/all/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.assignments.reviewer.all-reviewer-assignments-page', ['menu_id' => $menu_id]);
    })->name('assignments.reviewer.all');

    Route::get('/assignments/reviewer/pending/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
    return view('livewire.assignments.reviewer.pending-reviewer-assignments-page', ['menu_id' => $menu_id]);
    })->name('assignments.reviewer.pending');

    Route::get('/assignments/reviewer/completed/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.assignments.reviewer.completed-reviewer-assignments-page', ['menu_id' => $menu_id]);
    })->name('assignments.reviewer.completed');

    Route::get('/assignments/reviewer/declined/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.assignments.reviewer.declined-reviewer-assignments-page', ['menu_id' => $menu_id]);
    })->name('assignments.reviewer.declined');

    Route::get('/reviewer-assignment-view/{menu_id?}/{review_id?}', function ($menu_id = null, $review_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.assignments.reviewer.reviewer-assignment-view-page', [
            'menu_id' => $menu_id,
            'review_id' => $review_id
        ]);
    })->name('reviewer-assignment-view');


    // ------issues-----------------------------
    Route::get('/issue-list/{menu_id?}', function ($menu_id = null) {
        if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.issues.issue-list-page', ['menu_id' => $menu_id]);
    })->name('issue-list');



    // --------start website-----------------------
    Route::get('/web-page-list/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.web-page-list-page', ['menu_id' => $menu_id]);
    })->name('web-page-list');

    Route::get('/web-menu-list/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.web-menu-list-page', ['menu_id' => $menu_id]);
    })->name('web-menu-list');

    Route::get('/index-list/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.index-list-page', ['menu_id' => $menu_id]);
    })->name('index-list');

    Route::get('/post-list/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.post-list-page', ['menu_id' => $menu_id]);
    })->name('post-list');

    Route::get('/leadership-board/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.leadership-board-list-page', ['menu_id' => $menu_id]);
    })->name('leadership-board');

    Route::get('/accepted-abstracts/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.abstract.accepted-abstract-list-page', ['menu_id' => $menu_id]);
    })->name('accepted-abstracts');

    Route::get('/main-axes/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.axes.main-axis-list-page', ['menu_id' => $menu_id]);
    })->name('main-axes');

    Route::get('/sub-axes/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.axes.sub-axis-list-page', ['menu_id' => $menu_id]);
    })->name('sub-axes');

    Route::get('/gazettes/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.gazettes.gazette-list-page', ['menu_id' => $menu_id]);
    })->name('gazettes');

    Route::post('/gazette/upload-files', [GazetteFileController::class, 'upload'])
    ->name('gazette.file.upload');

    Route::get('/sientific-board/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.scientific-board-list-page', ['menu_id' => $menu_id]);
    })->name('sientific-board');

    Route::get('/decrees/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.ruling.decree-list-page', ['menu_id' => $menu_id]);
    })->name('decrees');

    Route::get('/orders/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.ruling.order-list-page', ['menu_id' => $menu_id]);
    })->name('orders');


     Route::get('/about-us/{menu_id?}', function ($menu_id = null) {
         if (!read(Auth::user()->role_ids, $menu_id)) {
            abort(403, __('label.permission_message'));
        }
        return view('livewire.website.about-us.about-us-list-page', ['menu_id' => $menu_id]);
    })->name('about-us');

    // --------end website-----------------------
    

    Route::get('/search-abstracts', function (\Illuminate\Http\Request $request) {

        $q = $request->get('q');

        $query = \DB::table('accepted_abstracts')
            ->select('id', 'title_fa','title_en');

        if ($q) {
            if(App::getLocale() =='fa'){
                $query->where('title_fa', 'like', "%{$q}%");
            }else{
                $query->where('title_en', 'like', "%{$q}%");
            }
        }
        return $query
            ->limit(20)
            ->get()
            ->map(fn ($item) => [
                'id'   => $item->id,
                'text' => (App::getLocale() =='fa')? $item->title_fa : $item->title_en,
            ]);
    });

});
// -----------------------end admin panel routes-------------------------------
require __DIR__.'/auth.php';
