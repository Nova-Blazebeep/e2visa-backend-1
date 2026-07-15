<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BlogController;
use App\Http\Controllers\api\BusinessInformationController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\ContactController;
use App\Http\Controllers\api\CountiesController;
use App\Http\Controllers\api\CountriesController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\PagesController;
use App\Http\Controllers\api\RegisterController;
use App\Http\Controllers\api\StatesController;
use App\Http\Controllers\api\SubscriberController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\api\ForumCommentsController;
use App\Http\Controllers\api\ForumCommentsRepliesController;
use App\Http\Controllers\api\ForumController;
use App\Models\ForumCommentReplies;
use App\Http\Controllers\portal\RealestateController;

// Auth Apis
Route::middleware(['api', 'session'])->post('login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail']);
Route::middleware('auth:sanctum')->group(function () {
// Categories & Sub Categories

//Route::post('/categories', [CategoryController::class, 'storeCategory']);
// Route::put('/categories/{id}', [CategoryController::class, 'updateCategory']);
// Route::delete('/categories/{id}', [CategoryController::class, 'destroyCategory']);
 //Route::post('/sub-categories', [CategoryController::class, 'storeSubcategory']);
// Route::put('/sub-categories/{id}', [CategoryController::class, 'updateSubcategory']);
// Route::delete('/sub-categories/{id}', [CategoryController::class, 'destroySubcategory']);

// Business Information

// Route::post('/business-information', [BusinessInformationController::class, 'store']);
// Route::get('/business-info/{userId}', [BusinessInformationController::class, 'show']);
// Route::put('/business-info/{userId}', [BusinessInformationController::class, 'update']);
// Route::delete('/business-info/{userId}', [BusinessInformationController::class, 'destroy']);

// Pages
Route::get('pages', [PagesController::class, 'index']);
Route::post('pages', [PagesController::class, 'store']);
Route::get('pages/{id}', [PagesController::class, 'show']);
Route::put('pages/{id}', [PagesController::class, 'update']);
Route::delete('pages/{id}', [PagesController::class, 'destroy']);

Route::prefix('forum')->group(function () {
    Route::post('/create', [ForumController::class, 'createForum']);
    Route::post('/comment/add', [ForumCommentsController::class, 'addComment']);
    Route::post('/reply/add', [ForumCommentsRepliesController::class, 'addReply']);
});

Route::post('/change-password', [AuthController::class, 'changePassword']);

Route::post('/user/profile', [RegisterController::class, 'updateProfile']);
Route::post('/update-profile-image-update', [AuthController::class, 'updateProfileImage']);
});


    Route::group(['prefix' => '/countries'], function () {
        Route::get('/list', [CountriesController::class, 'list'])->name('api.countries.list');
    });

    Route::group(['prefix' => '/states'], function () {
        Route::post('/list', [StatesController::class, 'list'])->name('api.states.list');
    });

    Route::group(['prefix' => '/counties'], function () {
        Route::post('/list', [CountiesController::class, 'list'])->name('api.counties.list');
    });
    Route::group(['prefix' => '/roles'], function () {
        Route::get('/list', [CountiesController::class, 'roleList'])->name('api.roles.list');
    });

    Route::group(['prefix' => '/business'], function () {
        Route::get('/find-business', [BusinessInformationController::class, 'find_business'])->name('api.business.find');
    });

    Route::group(['prefix' => '/professionals'], function () {
        Route::get('/list', [DashboardController::class,'getProfessionalRoles']);
        Route::post('/profession', [DashboardController::class,'findUsersWithProfession']);
        Route::post('/professional-detail', [DashboardController::class,'professional_detail']);
        Route::post('/send-email-to-professional', [DashboardController::class, 'sendEmailToProfessionals']);
        Route::get('/featured-professionals', [DashboardController::class, 'getFeaturedProfessionals']);
    });

Route::get('/new-business-listings', [DashboardController::class, 'getNewBusinessListings']);
// subscriber
Route::post('/subscriber', [SubscriberController::class, 'store']);
// Public Apis Without Authentication
// Public Forum Api
Route::prefix('forum')->group(function () {
    Route::get('/forum-detail/{id}',[ForumController::class,'detail']);
    Route::post('/list', [ForumController::class, 'list']);
    Route::post('/comment/list', [ForumCommentsController::class, 'listComments']);
    Route::post('/reply/list', [ForumCommentReplies::class, 'listReplies']);
    Route::get('/search-forums', [ForumController::class, 'searchForums']);
});
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/sub-categories', [CategoryController::class, 'listSubCategories']);
// contact
Route::post('contact', [ContactController::class, 'store']);
Route::get('/all-listing-types', [DashboardController::class, 'getAllListingTypes']);
Route::get('/all-business-types', [DashboardController::class, 'getAllBusinessTypes']);
Route::get('/brokers', [DashboardController::class, 'getBrokersDetails']);
Route::get('/dashboard-stats', [DashboardController::class, 'getDashboardStats']);
Route::get('/business-information/filter', [DashboardController::class, 'filterBusinessInformationRecords']);
Route::get('/business-information', [BusinessInformationController::class, 'index']);
Route::get('/business-detail/{id}', [BusinessInformationController::class, 'showBusinessDetail']);
// Blogs list
Route::get('/blogs-list', [BlogController::class, 'getBlogsList']);
Route::get('/blog/{title}', [BlogController::class, 'getBlogById']);
Route::get('/blogs/search', [BlogController::class, 'searchBlogs']);
Route::post('/blog/{title}/comment', [BlogController::class, 'addComment']);
// Reset Password
Route::post('/send-reset-password-email', [AuthController::class, 'sendResetPasswordEmail']);

Route::get('/property-types', [RealestateController::class, 'getPropertyTypes']);