<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\v1\AuthController;

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

Auth::routes();

// Public
Route::get('/', [App\Http\Controllers\PublicController::class, 'index'])->name('home_public');
Route::get('/docs', [App\Http\Controllers\PublicController::class, 'docs'])->name('docs_public');

Route::group(['middleware' => ['auth']], function() {
    // Home
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/stat/balances/data', [App\Http\Controllers\HomeController::class, 'cashesbalances'])->name('home_cashes_balances');
    Route::get('/stat/amounts/data', [App\Http\Controllers\HomeController::class, 'cashesamounts'])->name('home_cashes_amounts');
    Route::get('/stat/categories/data', [App\Http\Controllers\HomeController::class, 'transactionscategories'])->name('home_transactions_categories');
    Route::get('/stat/counts', [App\Http\Controllers\HomeController::class, 'counts'])->name('home_counts');
    // Users
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('my_profile');
    Route::put('/profile/save', [App\Http\Controllers\ProfileController::class, 'save'])->name('save_my_profile');
    // Cashes
    Route::get('/add/cash', [App\Http\Controllers\AddController::class, 'cash'])->name('add_cash');
    Route::post('/add/createcash', [App\Http\Controllers\AddController::class, 'createcash'])->name('create_cash');
    Route::get('/cashes', [App\Http\Controllers\ListController::class, 'cashes'])->name('cashes_list');
    Route::get('/cashes/data', [App\Http\Controllers\ListController::class, 'cashesdata'])->name('cashes_data');
    Route::get('/edit/cash/{id}', [App\Http\Controllers\EditController::class, 'cash'])->name('edit_cash');
    Route::get('/edit/cash/data/{id}', [App\Http\Controllers\EditController::class, 'cashdata'])->name('cash_data');
    Route::put('/edit/savecash/{id}', [App\Http\Controllers\EditController::class, 'savecash'])->name('save_cash');
    Route::put('/edit/cash/active/{id}', [App\Http\Controllers\EditController::class, 'activecash'])->name('active_cash');
    Route::put('/edit/cash/budget/{id}', [App\Http\Controllers\EditController::class, 'budgetcash'])->name('budget_cash');
    Route::delete('/remove/cash/{id}', [App\Http\Controllers\RemoveController::class, 'removecash'])->name('remove_cash');
    // Currencies
    Route::get('/add/currency', [App\Http\Controllers\AddController::class, 'currency'])->name('add_currency');
    Route::post('/add/createcurrency', [App\Http\Controllers\AddController::class, 'createcurrency'])->name('create_currency');
    Route::get('/currencies', [App\Http\Controllers\ListController::class, 'currencies'])->name('currencies_list');
    Route::get('/currencies/data/{onlyactive}', [App\Http\Controllers\ListController::class, 'currenciesdata'])->name('currencies_data');
    Route::get('/edit/currency/{id}', [App\Http\Controllers\EditController::class, 'currency'])->name('edit_currency');
    Route::get('/edit/currency/data/{id}', [App\Http\Controllers\EditController::class, 'currencydata'])->name('currency_data');
    Route::put('/edit/savecurrency/{id}', [App\Http\Controllers\EditController::class, 'savecurrency'])->name('save_currency');
    Route::put('/edit/currency/active/{id}', [App\Http\Controllers\EditController::class, 'activecurrency'])->name('active_currency');
    Route::delete('/remove/currency/{id}', [App\Http\Controllers\RemoveController::class, 'removecurrency'])->name('remove_currency');
    // Piggy Banks
    Route::get('/add/piggybank', [App\Http\Controllers\AddController::class, 'piggybank'])->name('add_piggybank');
    Route::post('/add/createpiggybank', [App\Http\Controllers\AddController::class, 'createpiggybank'])->name('create_piggybank');
    Route::get('/piggybanks', [App\Http\Controllers\ListController::class, 'piggybanks'])->name('piggybanks_list');
    Route::get('/piggybanks/data', [App\Http\Controllers\ListController::class, 'piggybanksdata'])->name('piggybanks_data');
    Route::get('/edit/piggybank/{id}', [App\Http\Controllers\EditController::class, 'piggybank'])->name('edit_piggybank');
    Route::get('/edit/piggybank/data/{id}', [App\Http\Controllers\EditController::class, 'piggybankdata'])->name('piggybank_data');
    Route::put('/edit/savepiggybank/{id}', [App\Http\Controllers\EditController::class, 'savepiggybank'])->name('save_piggybank');
    Route::put('/edit/piggybank/active/{id}', [App\Http\Controllers\EditController::class, 'activepiggybank'])->name('active_piggybank');
    Route::delete('/remove/piggybank/{id}', [App\Http\Controllers\RemoveController::class, 'removepiggybank'])->name('remove_piggybank');
    // Transactions
    Route::get('/add/transaction', [App\Http\Controllers\AddController::class, 'transaction'])->name('add_transaction');
    Route::get('/transactions/categories/data', [App\Http\Controllers\ListController::class, 'transcategorydata'])->name('transactions_categories');
    Route::post('/add/createtransaction', [App\Http\Controllers\AddController::class, 'createtransaction'])->name('create_transaction');
    Route::get('/transactions', [App\Http\Controllers\ListController::class, 'transactions'])->name('transactions_list');
    Route::get('/transactions/data', [App\Http\Controllers\ListController::class, 'transactionsdata'])->name('transactions_data');
    // Budget
    Route::get('/budget', [App\Http\Controllers\BudgetController::class, 'index'])->name('budget_redirect');
    Route::get('/budget/{currency_id}', [App\Http\Controllers\BudgetController::class, 'budget'])->name('budget');
    Route::get('/budget/{currency_id}/data', [App\Http\Controllers\BudgetController::class, 'budgetdata'])->name('budget_data');
    Route::get('/budget/{currency_id}/cashes', [App\Http\Controllers\BudgetController::class, 'cashes'])->name('budget_cashes');
    Route::post('/budget/{currency_id}/add', [App\Http\Controllers\BudgetController::class, 'createcategory'])->name('create_budget_category');
    Route::delete('/budget/{currency_id}/{id}', [App\Http\Controllers\BudgetController::class, 'removecategory'])->name('remove_budget_category');
    // Administration
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('administration');
    Route::get('/admin/counts/data', [App\Http\Controllers\AdminController::class, 'counts'])->name('administration_item_counts');
    Route::get('/admin/users/data', [App\Http\Controllers\AdminController::class, 'userslist'])->name('administration_users_list');
    Route::get('/admin/users/regs/month', [App\Http\Controllers\AdminController::class, 'usersregsmonth'])->name('administration_users_regs_month');
    Route::get('/admin/users/regs/year', [App\Http\Controllers\AdminController::class, 'usersregsyear'])->name('administration_users_regs_year');
    Route::put('/admin/user/ban/{id}', [App\Http\Controllers\AdminController::class, 'toggleuserban'])->name('administration_toggle_user_ban');
    Route::delete('/admin/users/remove/{id}', [App\Http\Controllers\AdminController::class, 'removeuser'])->name('administration_remove_user');
});

// Admin API
Route::post('/admin/api/users/create/', [App\Http\Controllers\AdminController::class, 'createuser'])->name('administration_api_create_user');
Route::post('/admin/api/users/ban/{status_id}', [App\Http\Controllers\AdminController::class, 'banuser'])->name('administration_api_ban_user');