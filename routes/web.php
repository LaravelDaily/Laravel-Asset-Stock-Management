<?php

use App\Custom\TechKen;
use Illuminate\Support\Facades\Crypt;

Route::redirect('/', '/login');
Route::redirect('/login', 'login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);
// Admin

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::redirect('/', '/login')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    //Orders
    Route::delete('orders/destroy', 'OrdersController@massDestroy')->name('orders.massDestroy');
    Route::resource('orders', 'OrdersController');
    Route::get('dynamicOrder/{id}',[
        'as'=>'dynamicOrder',
        'uses'=> 'OrdersController@loadInformation'
    ]);
    Route::post('ajaxRequest', 'OrdersController@ajaxRequestPost');
    Route::post('processOrder', 'OrdersController@processOrder');


    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Assets
    Route::delete('assets/destroy', 'AssetsController@massDestroy')->name('assets.massDestroy');
    Route::resource('assets', 'AssetsController');
    Route::get('dynamicAsset/{id}', [
        'as' => 'dynamicAsset',
        'uses' => 'AssetsController@loadInformation'
    ]);

    // Teams
    Route::delete('teams/destroy', 'TeamController@massDestroy')->name('teams.massDestroy');
    Route::resource('teams', 'TeamController');

    // Stocks
    //Route::delete('stocks/destroy', 'StocksController@massDestroy')->name('stocks.massDestroy');
    Route::resource('stocks', 'StocksController')->only(['index', 'show']);

    // Transactions
    //    Route::delete('transactions/destroy', 'TransactionsController@massDestroy')->name('transactions.massDestroy');
    Route::post('transactions/{stock}/storeStock', 'TransactionsController@storeStock')->name('transactions.storeStock');
    Route::resource('transactions', 'TransactionsController')->only(['index']);

    // Branches
    Route::delete('branches/destroy', 'BranchesController@massDestroy')->name('branches.massDestroy');
    Route::resource('branches', 'BranchesController');

    // Branches
    Route::resource('notifications', 'NotificationsController');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
    }
});

Route::get('encrypt/{text}', function ($text) {
    $val = Crypt::encrypt($text);
    return $val;
});

Route::get('decrypt/{text}', function ($text) {
    $decrypted = Crypt::decrypt($text);
    return $decrypted;
});

Route::get('getUUID', function () {
    /* $arr = getenv();
    foreach ($arr as $key => $val)
        echo "$key=>$val<br>"; */

    $system_root = getenv('SystemRoot');
    $output = shell_exec("echo | {$system_root}\System32\wbem\wmic.exe path win32_computersystemproduct get uuid");
    if ($output) return "Command succeeded. Output=" . $output;
    else return "Command failed.";
});

Route::get('addNotif/{title}', function($title) {
    TechKen::AddNotification($title);
});

Route::get('getOrders/{order_id}', function($order_id) {
    TechKen::GetOrderDetailByOrder($order_id);
});
