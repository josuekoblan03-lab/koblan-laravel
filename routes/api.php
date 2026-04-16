<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Department;
use App\Models\City;
use App\Models\Neighborhood;
use App\Http\Controllers\SearchController;

Route::get('/departments/{region}', fn($region) => Department::where('region_id', $region)->get());
Route::get('/cities/{department}', fn($department) => City::where('department_id', $department)->get());
Route::get('/neighborhoods/{city}', fn($city) => Neighborhood::where('city_id', $city)->get());
Route::get('/services/search', [SearchController::class, 'ajaxSearch'])->name('api.services.search');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', fn() => auth()->user()->notifications()->unread()->get());
});
