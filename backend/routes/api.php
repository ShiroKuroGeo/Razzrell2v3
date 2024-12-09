<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController as UserController;
use App\Http\Controllers\Global\PackageController as PackageController;
use App\Http\Controllers\Admin\GuestController as GuestController;
use App\Http\Controllers\Global\BookingController as BookingController;
use App\Http\Controllers\Global\PortfolioController as PortfolioController;
use App\Http\Controllers\Global\RateController as RateController;
use App\Http\Controllers\Admin\DashboardControler as DashboardControler;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Booking
Route::post('/add-booking', [BookingController::class, 'addBooking']);
Route::get('/get-all-bookings', [BookingController::class, 'index']);
Route::get('/get-booking-by-id/{id}', [BookingController::class, 'getBookingById']);
Route::get('/bookings/user/{id}', [BookingController::class, 'getBookingByUserId']);
Route::post('/update-booking', [BookingController::class, 'updateBooking']);

//Guest
Route::post('/add-guest', [GuestController::class, 'addGuest']);
Route::get('/get-all-guests', [GuestController::class, 'index']);

// Users
Route::post('/add-user', [UserController::class, 'addUser']);
Route::get('/get-all-users', [UserController::class, 'getAllUsers']);
Route::get('/get-user-by-id/{id}', [UserController::class, 'getUserById']);
Route::post('/update-user-info', [UserController::class, 'updateUserInfo']);
Route::post('/contact-us', [UserController::class, 'contactUs']);
Route::post('/update-user-password', [UserController::class, 'updateUserPassword']);
Route::get('/guests', [GuestController::class, 'index']);
Route::get('/my-info', [UserController::class, 'getMyInfo']);
Route::get('/get-all-guest', [GuestController::class, 'index']);
Route::post('/delete-user/{id}', [GuestController::class, 'deleteGuest']);
Route::post('/admin/block-user/{id}', [UserController::class, 'blockUser']);

// Authentication
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);

// Packages
Route::get('/get-all-packages', [PackageController::class, 'index']);
Route::post('/add-package', [PackageController::class, 'addPackage']);
Route::get('/get-package-by-id/{id}', [PackageController::class, 'getPackageById']);
Route::post('/delete-package/{id}', [PackageController::class, 'deletePackage']);
Route::post('/update-package', [PackageController::class, 'updatePackage']);
//Booking

// Portfolio
Route::get('/get-all-portfolios', [PortfolioController::class, 'allPortfolios']);
Route::post('/add-portfolio', [PortfolioController::class, 'addPortfolio']);
Route::post('/delete-portfolio/{id}', [PortfolioController::class, 'deletePortfolio']);
Route::post('/update-portfolio', [PortfolioController::class, 'updatePortfolio']);

// Contact Us
Route::get('/get-all-contact-us', [UserController::class, 'getAllContactUs']);

// Rating
Route::post('/add-rating', [RateController::class, 'addRating']);
Route::get('/get-all-ratings/{id}', [RateController::class, 'getAllRatings']);

// Dashboard
Route::get('/get-event-type-stats', [DashboardControler::class, 'getEventTypeStats']);
Route::get('/get-monthly-revenue', [DashboardControler::class, 'getMonthlyRevenue']);
Route::get('/get-satisfaction-rate', [DashboardControler::class, 'getSatisfactionRate']);