<?php

use App\Http\Controllers\portal\BadgeController;
use App\Http\Controllers\portal\BlogCategoryController;
use App\Http\Controllers\portal\BlogCommentController;
use App\Http\Controllers\portal\BlogController;
use App\Http\Controllers\portal\BusinessController;
use App\Http\Controllers\portal\CategoryController;
use App\Http\Controllers\portal\ContactController;
use App\Http\Controllers\portal\DashboardController;
use App\Http\Controllers\portal\MediaGalleryController;
use App\Http\Controllers\portal\PagesController;
use App\Http\Controllers\portal\RealestateController;
use App\Http\Controllers\portal\SubscriberController;
use App\Http\Controllers\portal\UserController;
use App\Models\County;
use App\Models\State;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //  return view('welcome');
    return redirect()->route('dashboard');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:view-dashboard');
    Route::group(['prefix' => '/users'], function () {
        Route::get('/', [UserController::class, 'index'])->name('portal.users.index')->middleware('permission:view-users');
        Route::get('/list', [UserController::class, 'list'])->name('portal.users.list')->middleware('permission:view-users');
        Route::get('/details/{id}', [UserController::class, 'details'])->name('portal.users.details')->middleware('permission:view-users');
        Route::get('/live-listings/{id}', [UserController::class, 'liveListings'])->name('portal.users.liveListings')->middleware('permission:view-users');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('portal.users.edit')->middleware('permission:view-users');
        Route::post('/store-or-update/{id?}', [UserController::class, 'storeOrUpdate'])->name('portal.users.storeOrUpdate')->middleware('permission:add-users');
        Route::get('/create', [UserController::class, 'create'])->name('portal.users.create')->middleware('permission:add-users');
        Route::post('/delete/{id}', [UserController::class, 'destroy'])->name('portal.users.delete')->middleware('permission:delete-users');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoryController::class, 'index'])->name('portal.categories.index');
        Route::get('/list', [CategoryController::class, 'list'])->name('portal.categories.list');
        Route::get('/details/{id}', [CategoryController::class, 'details'])->name('portal.categories.details');
        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('portal.categories.edit');
        Route::post('/store-or-update/{id?}', [CategoryController::class, 'storeOrUpdate'])->name('portal.categories.storeOrUpdate');
        Route::get('/create', [CategoryController::class, 'create'])->name('portal.categories.create');
        Route::post('/destroy/{id}', [CategoryController::class, 'destroyCategory'])->name('portal.categories.destroy');
    });

    Route::prefix('subcategories')->group(function () {
        Route::get('/', [CategoryController::class, 'subCategoryIndex'])->name('portal.subcategories.index');
        Route::get('/list', [CategoryController::class, 'subCategoryList'])->name('portal.subcategories.list');
        Route::get('/create', [CategoryController::class, 'subCategoryCreate'])->name('portal.subcategories.create');
        Route::get('/edit/{id}', [CategoryController::class, 'subCategoryEdit'])->name('portal.subcategories.edit');
        Route::post('/store-or-update/{id?}', [CategoryController::class, 'subCategoryStoreOrUpdate'])->name('portal.subcategories.storeOrUpdate');
        Route::post('/destroy/{id}', [CategoryController::class, 'subCategoryDestroy'])->name('portal.subcategories.destroy');
    });

    Route::group(['prefix' => '/pages'], function () {
        Route::get('/', [PagesController::class, 'index'])->name('portal.pages.index');
        // Route::get('/{slug}', [PagesController::class, 'index'])->name('pages.index');
        Route::post('/update/{slug}', [PagesController::class, 'update'])->name('pages.update');
        Route::post('/uploadCroppedImage', [PagesController::class, 'uploadCroppedImage'])->name('pages.uploadCroppedImage');
    });

    Route::prefix('blog-categories')->name('portal.blog-categories.')->group(function () {
        Route::get('/', [BlogCategoryController::class, 'index'])->name('index')->middleware('permission:view-users');
        Route::get('/list', [BlogCategoryController::class, 'list'])->name('list')->middleware('permission:view-users');
        Route::get('/create', [BlogCategoryController::class, 'create'])->name('create')->middleware('permission:view-users');
        Route::post('/store', [BlogCategoryController::class, 'store'])->name('store')->middleware('permission:view-users');
        Route::get('/{id}/edit', [BlogCategoryController::class, 'edit'])->name('edit')->middleware('permission:view-users');
        Route::post('/{id}/update', [BlogCategoryController::class, 'update'])->name('update')->middleware('permission:view-users');
        Route::post('/{id}/delete', [BlogCategoryController::class, 'destroy'])->name('destroy')->middleware('permission:view-users');
    });

    Route::group(['prefix' => '/forums'], function () {
        Route::get('/', [\App\Http\Controllers\portal\ForumController::class, 'index'])->name('portal.forums.index');
        Route::get('/list', [\App\Http\Controllers\portal\ForumController::class, 'list'])->name('portal.forums.list');
        Route::get('/delete/{id}', [\App\Http\Controllers\portal\ForumController::class, 'delete'])->name('portal.forums.delete');
    });

    Route::group(['prefix' => '/blogs'], function () {
        Route::get('/', [BlogController::class, 'index'])->name('portal.blog.index')->middleware('permission:view-users');
        Route::get('/add/{editor}', [BlogController::class, 'add'])->name('portal.blog.add')->middleware('permission:view-users');
        Route::post('/store', [BlogController::class, 'store'])->name('portal.blog.store')->middleware('permission:view-users');
        Route::get('/list', [BlogController::class, 'list'])->name('portal.blog.list')->middleware('permission:view-users');
        Route::get('/edit/{title}', [BlogController::class, 'edit'])->name('portal.blog.edit')->middleware('permission:view-users');
        Route::post('/update/{title}', [BlogController::class, 'update'])->name('portal.blog.update')->middleware('permission:view-users');
        Route::get('/delete/{id}', [BlogController::class, 'delete'])->name('portal.blog.delete')->middleware('permission:view-users');
        Route::get('/comments', [BlogCommentController::class, 'index'])->name('portal.blog.comments.index')->middleware('permission:view-users');
        Route::get('/comments/{id}/delete', [BlogCommentController::class, 'destroy'])->name('portal.blog.comments.delete')->middleware('permission:view-users');
    });

    Route::group(['prefix' => '/setting'], function () {
        Route::get('/', [UserController::class, 'setting'])->name('setting.index');
        Route::post('/update/password', [UserController::class, 'updatePassword'])->name('setting.update');
    });

    Route::group(['prefix' => '/business'], function () {
        Route::get('/', [BusinessController::class, 'index'])->name('business.index');
        Route::get('/list', [BusinessController::class, 'list'])->name('business.list');
        Route::get('/create', [BusinessController::class, 'showForm'])->name('business.show.form');
        Route::post('/create', [BusinessController::class, 'store'])->name('business.create');
        Route::get('/{businessId}', [BusinessController::class, 'show'])->name('business.show');
        // Route::put('/{businessId}', [BusinessController::class, 'update'])->name('business.update');
        Route::post('/update', [BusinessController::class, 'update'])->name('business.update');
        Route::delete('/{businessId}', [BusinessController::class, 'destroy'])->name('business.destroy');
    });

    Route::prefix('badges')->name('portal.badges.')->group(function () {
        Route::get('/', [BadgeController::class, 'index'])->name('index');
        Route::get('/list', [BadgeController::class, 'list'])->name('list');
        Route::get('/create', [BadgeController::class, 'create'])->name('create');
        Route::post('/store', [BadgeController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BadgeController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [BadgeController::class, 'update'])->name('update');
        Route::post('/{id}/delete', [BadgeController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => '/media/gallery'], function () {
        Route::get('/', [MediaGalleryController::class, 'index'])->name('media_gallery.index');
        Route::get('/list', [MediaGalleryController::class, 'list'])->name('media_gallery.list');
        Route::post('/delete/{id}', [MediaGalleryController::class, 'delete'])->name('media_gallery.delete');
        Route::get('/details/{id}', [MediaGalleryController::class, 'showDetails'])->name('media_gallery.details');
        Route::post('/store-or-update/{id?}', [MediaGalleryController::class, 'storeOrUpdate'])->name('media_gallery.storeOrUpdate');
        Route::get('/create', [MediaGalleryController::class, 'create'])->name('media_gallery.create');
        Route::get('/edit/{id}', [MediaGalleryController::class, 'edit'])->name('media_gallery.edit');
        Route::post('/uploadCroppedImage', [MediaGalleryController::class, 'uploadCroppedImage'])->name('media_gallery.uploadCroppedImage');
    });

    Route::group(['prefix' => '/contact'], function () {
        Route::get('/', [ContactController::class, 'index'])->name('contact.index');
        Route::get('/list', [ContactController::class, 'list'])->name('contact.list');
        Route::post('/delete/{id}', [ContactController::class, 'delete'])->name('contact.delete');
        Route::get('/details/{id}', [ContactController::class, 'showDetails'])->name('contact.details');
        Route::post('/delete-multiple', [ContactController::class, 'deleteMultiple'])->name('contact.deleteMultiple');
    });

    Route::group(['prefix' => '/subscriber'], function () {
        Route::get('/', [SubscriberController::class, 'index'])->name('subscriber.index');
        Route::get('/list', [SubscriberController::class, 'list'])->name('subscriber.list');
        Route::post('/delete/{id}', [SubscriberController::class, 'delete'])->name('subscriber.delete');
        Route::post('/delete-multiple', [SubscriberController::class, 'deleteMultiple'])->name('subscriber.deleteMultiple');
    });

    Route::group(['prefix' => '/real-estate'], function () {
        Route::get('/', [RealestateController::class, 'index'])->name('real-estate.index');
        Route::get('/list', [RealestateController::class, 'list'])->name('real-estate.list');
        Route::get('/create', [RealestateController::class, 'showForm'])->name('real-estate.show.form');
        Route::post('/create', [RealestateController::class, 'store'])->name('real-estate.create');
        Route::get('/{businessId}', [RealestateController::class, 'show'])->name('real-estate.show');
        Route::post('/update', [RealestateController::class, 'update'])->name('real-estate.update');
        Route::delete('/{businessId}', [RealestateController::class, 'destroy'])->name('real-estate.destroy');
    });
});
Route::get('/states/{country}', function ($countryId) {
    return State::where('country_id', $countryId)->get();
});

Route::get('/counties/{state}', function ($stateId) {
    return County::where('state_id', $stateId)->get();
});

Auth::routes();
