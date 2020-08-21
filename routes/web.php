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

use App\Http\Middleware\AssignProject;
use App\Http\Middleware\MustBeSubscribed;
use App\Http\Middleware\NeedsCluster;
use App\Http\Middleware\RedirectIfSubscribed;
use App\Http\Middleware\ShareProjectToView;

$launched = true;

Route::get('/', 'LandingController')->name('landing')->middleware('guest');

// Newsletter routes
Route::namespace('Newsletter')->prefix('newsletter')->name('newsletter.')->group(function () {

    Route::get('/confirmation/{newsletterSubscription}', 'SubscriptionConfirmationController@store')->name('subscription.confirmation')->middleware(['signed', 'throttle:6,1']);

    Route::resource('/subscription', 'SubscriptionController');

    Route::get('/thank-you', 'SubscriptionController@thankyou')->name('thankyou');
    Route::get('/confirmed', 'SubscriptionController@confirmed')->name('confirmed');
});

// Legal
Route::name('legal.')->group(function () {
    Route::get('/about-us', 'LegalController@about')->name('about');
    Route::get('/terms-of-service', 'LegalController@terms')->name('terms');
    Route::get('/privacy-policy', 'LegalController@privacy')->name('privacy');
    Route::get('/imprint', 'LegalController@imprint')->name('imprint');
    Route::get('/disclaimer', 'LegalController@disclaimer')->name('disclaimer');
});

// Auth routes
Route::namespace('Auth')->middleware('feature:auth')->group(function () {

    Route::get('/sign-up', 'RegisterController@showRegistrationForm')->name('sign-up');
    Route::get('/sign-in', 'LoginController@showLoginForm')->name('sign-in');

    Route::post('/register', 'RegisterController@createUser')->name('register');
    Route::get('/login', 'LoginController@showLoginForm')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::post('/logout', 'LoginController@logout')->name('logout');

    Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm');
    Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail');
    Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm');
    Route::post('/password/reset', 'ResetPasswordController@reset');

    Route::prefix('github')->name('github.')->group(function () {

        Route::get('/redirect', 'GithubController@redirect')->name('redirect');
        Route::get('/handle', 'GithubController@handle')->name('handle');
    });
});

Route::namespace('Subscription')->prefix('subscription')->name('subscription.')->middleware(['user', RedirectIfSubscribed::class])->group(function () {
    Route::get('/await', 'SubscriptionController@await')->name('await');
    Route::get('/create', 'SubscriptionController@create')->name('create');
    Route::get('/missing', 'SubscriptionController@missing')->name('missing');
    Route::get('/expired', 'SubscriptionController@expired')->name('expired');
});

Route::group(['middleware' => ['auth', 'user', 'projects']], function () {


    Route::group(['middleware' => [MustBeSubscribed::class, ShareProjectToView::class]], function () {

        Route::resource('project', 'ProjectController');

        Route::get('/dashboard/{project?}', 'DashboardController')->name('dashboard')->middleware([AssignProject::class, NeedsCluster::class]);

        Route::get('/tokens/{project?}', 'ClusterTokenController@index')->name('token.index')->middleware(AssignProject::class);

        Route::get('/settings/{project?}', 'SettingsController@index')->name('settings')->middleware(AssignProject::class);

        Route::get('/playground', 'DashboardController')->name('playground');

        Route::get('/monitoring', 'DashboardController')->name('monitoring');

        Route::get('/support', 'SupportController@index')->name('support');

        Route::get('/cluster/create', 'ClusterController@create')->name('cluster.create');
        Route::get('/cluster/edit/{cluster}', 'ClusterController@edit')->name('cluster.edit');
        Route::post('/cluster', 'ClusterController@store')->name('cluster.store');
        Route::put('/cluster/{cluster}', 'ClusterController@update')->name('cluster.update');
        Route::delete('/cluster/{cluster}', 'ClusterController@destroy')->name('cluster.destroy');
    });
});


Route::bind('cluster', function ($id) {
    return App\Models\Cluster::withTrashed()->where('id', $id)->first();
});

Broadcast::routes();
