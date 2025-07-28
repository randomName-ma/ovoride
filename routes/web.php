<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');
Route::get('cron', 'CronController@cron')->name('cron');

Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::post('subscribe', 'subscribe')->name('subscribe');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('blog', 'blog')->name('blog');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');
    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');
    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');
    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});

Route::get('/testCookie', function () {
    $lang = request()->cookie('lang', 'en'); // القيمة الافتراضية en
    $message = $lang === 'ar' ? 'مرحبًا' : 'Hello';

    return <<<HTML
        <html>
            <body>
                <h1>{$message}</h1>
                <a href="/lang/ar">العربية</a> |
                <a href="/lang/en">English</a>
            </body>
        </html>
    HTML;
});

Route::get('/lang/{lang}', function ($lang) {
    return redirect('/testCookie')
        ->withCookie(cookie('lang', $lang, 60 * 24 * 30)); // تخزين الكوكي لمدة شهر
});
