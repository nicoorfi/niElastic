<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
$launched = true;

Route::view('/', 'landing', ['launched' => $launched])->name('landing')->middleware('guest');

Broadcast::routes();

// Newsletter routes
Route::namespace('Newsletter')->prefix('newsletter')->name('newsletter.')->group(function () {

    Route::get('/cofirmation/{newsletterSubscription}', 'SubscriptionConfirmationController@store')->name('subscription.confirmation')->middleware(['signed', 'throttle:6,1']);

    Route::resource('/subscription', 'SubscriptionController');

    Route::view('/thank-you', 'newsletter.thankyou')->name('thankyou');

    Route::view('/confirmed', 'newsletter.confirmed')->name('confirmed');
});

// Github auth routes
Route::namespace('Auth')->prefix('github')->name('github.')->group(function () {

    Route::get('/redirect', 'GithubController@redirect')->name('redirect');

    Route::get('/login', 'GithubController@login')->name('login');

    Route::get('/register', 'GithubController@register')->name('register');
});


if ($launched === true) {

    Route::group(['middleware' => ['auth']], function () {

        Route::view('/dashboard', 'layouts.app')->name('dashboard');

        Route::view('/cluster/create', 'layouts.app')->name('cluster.create');

        Route::view('/bar', 'layouts.app');
    });

    // Auth routes
    Auth::routes();

    // Legal
    Route::name('legal.')->group(function () {

        Route::view('/terms', 'legal.terms')->name('terms');

        Route::view('/privacy', 'legal.privacy')->name('privacy');

        Route::view('/cookie', 'legal.cookie')->name('cookie');
    });
}
