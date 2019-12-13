    <?php
Route::get('/', function() {
    return redirect(route('admin.dashboard'));
});

Route::get('home', function() {
    return redirect(route('admin.dashboard'));
});

Route::name('admin.')->prefix('admin')->middleware('auth')->group(function() {
    Route::get('dashboard', 'DashboardController')->name('dashboard');

    Route::get('users/roles', 'UserController@roles')->name('users.roles');

    Route::resource('users', 'UserController', [
        'names' => [
            'index' => 'users'
        ]
    ]);


    // Apps config
    Route::get('apps', 'AppsController')->name('apps');
    Route::POST('apps/destroys/{id}', 'AppsController@destroy')->name('apps.destroy');
    Route::get('createapps', 'AppsController@createapps')->name('createapps');
    Route::post('admin/addapps', 'AppsController@store')->name('addapps');
    Route::get('apps/edit/{id}', 'AppsController@editapps')->name('editapps');
    Route::POST('apps/update/{id}', 'AppsController@updateapps')->name('updateapps');

    
    // Hub/Server Config
    Route::get('/hubs', 'HubController')->name('hubs');
    Route::get('createhub', 'HubController@createhub')->name('createhub');
    Route::get('hubs/edit/{id}', 'HubController@edithub')->name('edithub');
    Route::POST('hubs/update/{id}', 'HubController@update')->name('update');
    Route::POST('hubs/destroys/{id}', 'HubController@destroy')->name('hubs.destroy');
    Route::post('admin/addserver', 'HubController@store')->name('addserver');


    // Node Config
    Route::get('nodes', 'NodeController')->name('nodes');
    Route::get('nodes/edit/{id}', 'NodeController@editnode')->name('editnode');
    Route::get('createnode', 'NodeController@createnode')->name('createnode');
    Route::POST('nodes/update/{id}', 'NodeController@updatenode')->name('updatenode');
    Route::post('admin/addnode', 'NodeController@store')->name('addnode');
    Route::POST('nodes/destroys/{id}', 'NodeController@destroy')->name('nodes.destroy');


    // Users Config
    Route::Get('createuser', 'UserController@createuser')->name('createuser');
    Route::get('users/edit/{id}', 'UserController@edituser')->name('edituser');
    Route::post('admin/adduser', 'UserController@store')->name('adduser');
    Route::POST('users/destroys/{id}', 'UserController@destroy')->name('users.destroy');
    Route::POST('users/edit/{id}', 'UserController@updateuser')->name('updateuser');


    // Process Config
    Route::get('process', 'ProcessController')->name('process');
    Route::post('admin/addprocess', 'ProcessController@store')->name('addprocess');
    Route::get('createprocess', 'ProcessController@createprocess')->name('createprocess');
    Route::POST('process/destroys/{id}', 'ProcessController@destroy')->name('process.destroy');
    Route::get('process/edit/{id}', 'ProcessController@editprocess')->name('editprocess');
    Route::POST('process/update/{id}', 'ProcessController@updateprocess')->name('updateprocess');
    Route::get('process/show/{id}', 'ProcessController@showProcess')->name('process.show');


    Route::get('process/hfc/tab/1', 'HFCController@showTab')->name('process.hfc.showhfc');
    Route::get('process/hfc/tab/2', 'HFCController@showTab1')->name('process.hfc.showhfc1');
    Route::get('process/hfc/tab/3', 'HFCController@showTab2')->name('process.hfc.showhfc2');
    Route::POST('process/hfc/uploads', 'HFCController@uploaddata')->name('process.hfc.uploads');

});

Route::middleware('auth')->get('logout', function() {
    Auth::logout();
    return redirect(route('login'))->withInfo('You have successfully logged out!');
})->name('logout');

Auth::routes(['verify' => true]);

Route::name('js.')->group(function() {
    Route::get('dynamic.js', 'JsController@dynamic')->name('dynamic');
});

// Get authenticated user
Route::get('users/auth', function() {
    return response()->json(['user' => Auth::check() ? Auth::user() : false]);
});
