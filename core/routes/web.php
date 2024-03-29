<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LanguageController;

// Route to switch locale
Route::get('/switch-locale/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put('locale', $locale);
    return back();
});

// Corrected route
Route::get('/language/{lang}', [LanguageController::class, 'switchLang'])->name('language.switch');


Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron', 'CronController@cron')->name('cron');

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
});

Route::controller('SiteController')->group(function () {
    Route::get('contact', 'contact')->name('contact');
    Route::post('contact', 'contactSubmit');

    Route::get('change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::post('check/referral', 'CheckUsername')->name('check.referral');

    Route::get('blog', 'blog')->name('blog');
    Route::get('blog/{id}/{slug}', 'blogDetails')->name('blog.details');


    Route::get('plan/index', 'plan')->name('plan.index');

    Route::post('subscriber', 'subscriber')->name('subscriber');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
