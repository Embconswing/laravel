<?php

use Illuminate\Support\Facades\Route;


use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentTrackingController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\WalkinController;
use App\Http\Controllers\Admin\ApplicationPoolController;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/



Route::get('/', function () {
    $registname = DB::table('configuration')->value('registname');

    return view('welcome', compact('registname'));
});


Route::get('/sound', fn () => view('sound'));
Route::get('/tv', fn () => view('tv.index'));

Route::get('/queue/display', fn () => view('queue.display'))
    ->name('queue.display');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (ANY logged-in user)
|--------------------------------------------------------------------------
*/


Route::middleware('auth')->group(function () {

Route::patch(
    '/appointments/{appointment}/change-email',
    [AppointmentTrackingController::class, 'changeEmail']
)->name('appointments.changeEmail');

Route::patch('/appointments/update-reference', [AppointmentTrackingController::class, 'updateReferenceAjax'])
    ->name('appointments.updateReferenceAjax');


Route::get('/appointments/find-by-reference', [AppointmentTrackingController::class, 'findByReference'])
    ->name('appointments.findByReference');


Route::get('/appointments/collect-documents', 
    [AppointmentController::class,'collectDocumentsPage'])
    ->name('appointments.collect-documents');

Route::post('/appointments/collect-documents/scan', 
    [AppointmentController::class,'scanCollectDocument'])
    ->name('appointments.collect-documents.scan');

Route::post('/appointments/collect-documents/process', 
    [AppointmentController::class,'processCollectDocument'])
    ->name('appointments.collect-documents.process');


Route::get('/appointments/bulk-ready-mail', [AppointmentController::class, 'bulkReadyMailPage'])
    ->name('appointments.bulk-ready-mail');

Route::post('/appointments/bulk-ready-mail/scan', [AppointmentController::class, 'scanReadyMail'])
    ->name('appointments.bulk-ready-mail.scan');

Route::post('/appointments/bulk-ready-mail/process', [AppointmentController::class, 'processBulkReadyMail'])
    ->name('appointments.bulk-ready-mail.process');




// Show edit page
Route::get('/appointments/{appointment}/edit-service',
    [AppointmentController::class, 'editService']
)->name('appointments.editService');

// Handle update
Route::patch('/appointments/{appointment}/update-service',
    [AppointmentController::class, 'updateService']
)->name('appointments.updateService');








Route::patch('/appointments/{appointment}/change-ref', 
    [AppointmentTrackingController::class, 'changeReference'])
    ->name('appointments.changeRef');

Route::get('/appointments/{appointment}/history',
    [AppointmentController::class, 'history'])
    ->name('appointments.history');


Route::get('/admin/appointments/delete', [AppointmentController::class, 'showDeleteForm'])
    ->name('appointments.delete');
    
Route::post('/admin/appointments/delete', [AppointmentController::class, 'deleteAppointments'])
    ->name('admin.appointments.delete');


Route::get('/appointments/{appointment}/print-label', [AppointmentController::class, 'printLabel'])
    ->name('appointments.printLabel');


Route::get('/supervisor/cases', [AppointmentController::class, 'supervisorCases'])
    ->name('appointments.supervisorCases');



Route::get(
        '/admin/overview/application-pools',
        [ApplicationPoolController::class, 'index']
    );

Route::patch(
    '/appointments/{appointment}/assign-user',
    [AppointmentController::class, 'assignToUser']
)->name('appointments.assignUser');


Route::get(
    '/appointments/assigned',
    [AppointmentController::class, 'assigned']
)->name('appointments.assigned');

    Route::get('/walk-ins', [WalkinController::class, 'index'])
        ->name('walkins.index');

    // SUBMIT the form
    Route::post('/walk-ins', [WalkinController::class, 'store'])
        ->name('walkins.store');

Route::get('/p',
    [AppointmentController::class, 'index']
)->name('appointments.index');

    // My assignments
    Route::get('/my-assignments',
        [DashboardController::class, 'myAssignments']
    )->name('my.assignments');

    // Update processing status (NORMAL USER)
    Route::patch('/appointments/{appointment}/status',
        [AppointmentController::class, 'updateStatus']
    )->name('appointments.updateStatus');

    // Return / forward application
    Route::post('/appointments/return',
        [AppointmentController::class, 'returnFromUser']
    )->name('appointments.return');

    // Appointment operations
    Route::patch('/appointments/{appointment}/pending',
        [AppointmentController::class, 'markPending']
    )->name('appointments.pending');

    Route::patch('/appointments/{appointment}/return-to-queue',
        [AppointmentController::class, 'returnToQueue']
    )->name('appointments.returnToQueue');

    Route::patch('/appointments/{appointment}/skip',
        [AppointmentController::class, 'skip']
    )->name('appointments.skip');

    Route::patch('/appointments/{appointment}/window',
        [AppointmentController::class, 'assignWindow']
    )->name('appointments.assignWindow');

    Route::post('/appointments/{appointment}/complete',
        [AppointmentController::class, 'complete']
    )->name('appointments.complete');

    Route::patch('/appointments/{appointment}/assign-user',
        [AppointmentController::class, 'assignUser']
    )->name('appointments.assignUser');

    // Export
    Route::get('/appointments/export',
        [AppointmentController::class, 'indexexcel']
    )->name('appointments.export.page');

    Route::get('/appointments/export/download',
        [AppointmentController::class, 'export']
    )->name('appointments.export.download');
});

/*
|--------------------------------------------------------------------------
| Dashboard & Profile (Authenticated + Verified)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard',
        [DashboardController::class, 'myAssignments']
    )->name('dashboard');

    Route::get('/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch('/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete('/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Appointment Assignment (OFFICER / SUPERVISOR / ADMIN)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:' .
        User::TYPE_OFFICER . ',' .
        User::TYPE_SUPERVISOR . ',' .
        User::TYPE_ADMIN
])->group(function () {

    Route::get('/appointments/assign',
        [AppointmentTrackingController::class, 'index']
    )->name('appointments.assign.index');

    Route::patch('/appointments/{appointment}/assign',
        [AppointmentTrackingController::class, 'assign']
    )->name('appointments.assign');

Route::patch('/appointments/bulk-assign',
    [AppointmentTrackingController::class, 'bulkAssign']
)->name('appointments.bulkAssign');

 Route::get('/applications/by-status',
        [AppointmentTrackingController::class, 'applicationsByStatus']
    )->name('applications.byStatus');

Route::patch(
    '/appointments/{appointment:appointment_no}/delivery-status',
    [AppointmentController::class, 'updateDeliveryStatus']
)->name('appointments.deliveryStatus');

});

/*
|--------------------------------------------------------------------------
| Supervisor Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:' . User::TYPE_SUPERVISOR
])->group(function () {

    Route::get('/supervisor/inbox',
        [SupervisorController::class, 'inbox']
    )->name('supervisor.inbox');
});

/*
|--------------------------------------------------------------------------
| Appointment CSV / Import
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/appointments/upload',
        [AppointmentController::class, 'upload']
    )->name('appointments.upload');

    Route::post('/appointments/upload-csv',
        [AppointmentController::class, 'uploadCsv']
    )->name('appointments.uploadCsv');

    Route::post('/appointments/csv/preview',
        [AppointmentController::class, 'preview']
    )->name('appointments.preview');

    Route::post('/appointments/csv/upload',
        [AppointmentController::class, 'uploadWithProgress']
    )->name('appointments.upload.progress');

    Route::get('/appointments/csv/progress',
        [AppointmentController::class, 'progress']
    )->name('appointments.progress');

    Route::post('/appointments/import',
        [AppointmentController::class, 'import']
    )->name('appointments.import');
});

/*
|--------------------------------------------------------------------------
| Announcements (ADMIN)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:' . User::TYPE_ADMIN
])->group(function () {

    Route::get('/admin/announcements',
        [AnnouncementController::class, 'index']
    )->name('announcements.index');

    Route::post('/admin/announcements',
        [AnnouncementController::class, 'store']
    )->name('announcements.store');

    Route::put('/admin/announcements/{announcement}',
        [AnnouncementController::class, 'update']
    )->name('announcements.update');

    Route::delete('/admin/announcements/{announcement}',
        [AnnouncementController::class, 'destroy']
    )->name('announcements.destroy');
});

/*
|--------------------------------------------------------------------------
| Debug / Development (REMOVE IN PRODUCTION)
|--------------------------------------------------------------------------
*/

Route::get('/debug-broadcast', fn () => [
    'driver' => config('broadcasting.default'),
]);

Route::get('/debug-pusher', fn () => response()->json([
    'app_id' => config('broadcasting.connections.pusher.app_id'),
    'key' => config('broadcasting.connections.pusher.key'),
    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
]));

/*
|--------------------------------------------------------------------------
| Test Route (REMOVE IN PRODUCTION)
|--------------------------------------------------------------------------
*/

Route::get('/test-appointment', function () {
    return Appointment::create([
        'appointment_no' => 'A001',
        'applicant_name' => 'John Doe',
        'window_no' => 1,
        'appointment_date' => now()->toDateString(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
