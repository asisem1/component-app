<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminLoginController; 
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\SubCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

        // Category Routes
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');
        
        
        // Sub Category Routes


        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index'); // GET
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create'); // GET
        Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store'); // POST
        Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit'); // GET
        Route::put('/sub-categories/{subCategory}', [SubCategoryController::class, 'update'])->name('sub-categories.update'); // PUT
        Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete'); // DELETE
      
        

        Route::get('/getSlug',function(Request $request){
            $slug = '';
            if (!empty($request->title)){
                $slug = Str::slug($request->title);
            }

            return response()->json([
                  'status' => true,
                  'slug' => $slug
            ]);

        })->name('getSlug');
    });
});

