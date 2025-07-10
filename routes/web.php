<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberTemplateController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\OutletKiosController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::middleware('xss')->group(function () {

    // -------- login administrator
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login.post');

    Route::get('/privacy-policy', function () {
        return view('privacy');
    });

    // ----------- administrator
    Route::middleware(['auth'])->prefix('auth')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('auth.index');
        Route::get('/logout', [LoginController::class, 'logout'])->name('auth.logout');

        Route::prefix('/chart')->group(function () {
            Route::get('/member-count', [HomeController::class, 'memberCount'])->name('chart.member');
            Route::get('/office-count', [HomeController::class, 'officeCount'])->name('chart.office');
            Route::get('/division-count', [HomeController::class, 'divisionCount'])->name('chart.division');
            Route::get('/android-version', [HomeController::class, 'androidVersion'])->name('chart.android.version');
            Route::get('/android-merk', [HomeController::class, 'androidMerk'])->name('chart.android.merk');
            Route::post('/daily-attendance', [HomeController::class, 'dailyAttendance'])->name('chart.daily.attendance');
        });

        Route::prefix('cashier')->group(function () {
            Route::get('/', [CashierController::class, 'index'])->name('cashier.index');
            Route::post('/cashier-ajax', [CashierController::class, 'ajax'])->name('cashier.ajax');
            Route::get('/kios/{kios}', [CashierController::class, 'cashier'])->name('cashier.kios');
            Route::post('/items', [CashierController::class, 'items'])->name('cashier.items');
            Route::get('/categories', [CashierController::class, 'categories'])->name('cashier.categories');
            Route::post('/process', [CashierController::class, 'save'])->name('cashier.process');
            Route::get('/customers', [CashierController::class, 'customers'])->name('cashier.customers');
        })->middleware([HandlePrecognitiveRequests::class]);

        Route::prefix('members')->group(function () {
            Route::resource('member', MemberController::class);
            Route::post('/member-ajax', [MemberController::class, 'ajax'])->name('member.ajax');
            // Route::post('/member/password/{member}', [MemberController::class, 'password'])->name('member.password');
            // Route::post('/import', [MemberTemplateController::class, 'import'])->name('member.import');
            // Route::get('/template', [MemberTemplateController::class, 'template'])->name('member.template');

            Route::get('/deposit/{member}/member', [DepositController::class, 'index'])->name('deposit.index');
            Route::post('/deposit-ajax', [DepositController::class, 'ajax'])->name('deposit.ajax');
            Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');
        });

        Route::prefix('officesmaster')->group(function () {
            Route::resource('office', OfficeController::class);
            Route::post('/office-ajax', [OfficeController::class, 'ajax'])->name('office.ajax');
            Route::get('/office/{office}/outletkios', [OfficeController::class, 'outletkios'])->name('office.outletkios');
            Route::patch('/office/{office}/outletkios', [OfficeController::class, 'outletkiosUpdate'])->name('office.outletkios.update');
            Route::get('/outletkios-list', [OfficeController::class, 'outletkiosList'])->name('office.outletkios.list');

            Route::resource('kios', OutletKiosController::class)->parameters(['kios' => 'kios']);
            Route::post('/kios-ajax', [OutletKiosController::class, 'ajax'])->name('kios.ajax');
            Route::get('/kios-list', [OutletKiosController::class, 'outletList'])->name('kios.outletlist');
        });

        Route::prefix('products')->group(function () {
            Route::resource('product', ProductController::class);
            Route::post('/product-ajax', [ProductController::class, 'ajax'])->name('product.ajax');
            Route::resource('category', CategoryController::class);
            Route::post('/category-ajax', [CategoryController::class, 'ajax'])->name('category.ajax');
            Route::resource('productattribute', ProductAttributeController::class);
            Route::post('/productattribute-ajax', [ProductAttributeController::class, 'ajax'])->name('productattribute.ajax');
        });

        // ======== PENGATURAN ==============================================

        Route::resource('user', UserController::class);
        Route::post('/user-ajax', [UserController::class, 'ajax'])->name('user.ajax');
        Route::post('/user/password/{user}', [UserController::class, 'password'])->name('user.password');

        Route::resource('role', RoleController::class);
        Route::post('/role-ajax', [RoleController::class, 'ajax'])->name('role.ajax');
        Route::post('/permission-ajax', [PermissionController::class, 'ajax'])->name('permission.ajax');

        Route::singleton('setting', SettingController::class);
        Route::patch('/setting-logo/{setting}', [SettingController::class, 'updateImage'])->name('setting.logo');

        Route::singleton('profil', ProfilController::class);
        Route::resource('password', PasswordController::class);

        // ======== END PENGATURAN ==============================================
    });
    // ----------- end administrator

    // frontend
    require __DIR__ . '/customer.php';

    // Master ajax
    Route::prefix('master')->group(function () {
        Route::post('/ganti-foto', [AjaxController::class, 'ganti_foto'])->name('master.foto');
        Route::post('/state', [AjaxController::class, 'state'])->name('drop-state');
        Route::post('/city', [AjaxController::class, 'city'])->name('drop-city');
        Route::post('/district', [AjaxController::class, 'district'])->name('drop-district');
        Route::post('/minutes', [AjaxController::class, 'minutes'])->name('drop-minutes');
        Route::post('/office', [AjaxController::class, 'office'])->name('drop-office');
        Route::post('/category', [AjaxController::class, 'category'])->name('drop-category');
    });
});
