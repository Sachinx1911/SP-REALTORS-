<?php

use App\Http\Controllers\Admin\AdminEnquiryController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\AdminPropertyController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BrochureController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/* -------------------------------------------------------------------------
 * Public site
 * ---------------------------------------------------------------------- */
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/property/{property}', [PropertyController::class, 'show'])->name('properties.show');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/project/{project}', [ProjectController::class, 'show'])->name('projects.show');

// Brochure downloads are gated: details first, then the file.
Route::post('/project/{project}/brochure', [BrochureController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('projects.brochure.request');
Route::get('/project/{project}/brochure', [BrochureController::class, 'download'])
    ->name('projects.brochure.download');

Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/contact-us', [ContactController::class, 'index'])->name('contact');

Route::post('/enquiry', [EnquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('enquiry.store');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

/* -------------------------------------------------------------------------
 * Auth (admin login only — no public registration)
 * ---------------------------------------------------------------------- */
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'create'])->name('login');
    Route::post('/admin/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::post('/admin/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/* -------------------------------------------------------------------------
 * Admin panel
 * ---------------------------------------------------------------------- */
Route::middleware(['auth', 'panel'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Admin binds by ID so URLs stay stable when a slug is edited.
        Route::delete('properties/images/{image}', [AdminPropertyController::class, 'destroyImage'])
            ->name('properties.images.destroy');
        Route::resource('properties', AdminPropertyController::class)
            ->parameters(['properties' => 'property:id']);

        Route::delete('projects/images/{image}', [AdminProjectController::class, 'destroyImage'])
            ->name('projects.images.destroy');
        // Bound by id to match the projects resource routes below.
        Route::delete('projects/{project:id}/brochure', [AdminProjectController::class, 'destroyBrochure'])
            ->name('projects.brochure.destroy');
        Route::resource('projects', AdminProjectController::class)
            ->parameters(['projects' => 'project:id']);

        Route::resource('locations', AdminLocationController::class)
            ->except('show')
            ->parameters(['locations' => 'location:id']);
        Route::resource('testimonials', AdminTestimonialController::class)->except('show');

        Route::get('enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('enquiries/{enquiry}', [AdminEnquiryController::class, 'update'])->name('enquiries.update');
        Route::delete('enquiries/{enquiry}', [AdminEnquiryController::class, 'destroy'])->name('enquiries.destroy');

        // Team & Settings: full Admins only — Managers are blocked here.
        Route::middleware('admin')->group(function () {
            Route::resource('users', AdminUserController::class)
                ->except('show')
                ->parameters(['users' => 'user:id']);

            Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
        });
    });
