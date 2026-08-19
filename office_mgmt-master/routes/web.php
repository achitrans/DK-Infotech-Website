<?php

use App\Http\Controllers\AdvanceSalaryController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientKycController;
use App\Http\Controllers\ClientLookupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseHeadController;
use App\Http\Controllers\ExperienceLetterController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\InternshipInterestController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LoanInquiryController;
use App\Http\Controllers\OfferLetterController;
use App\Http\Controllers\PasswordVaultController;
use App\Http\Controllers\PayuPaymentController;
use App\Http\Controllers\PrivateFileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMilestoneController;
use App\Http\Controllers\ProjectMilestoneRemarkController;
use App\Http\Controllers\ProjectRemarkController;
use App\Http\Controllers\ProjectTaskCommentController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserAttendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserKycController;
use App\Http\Controllers\UserMonthlySalaryController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WhatsappController;
use App\Http\Middleware\InquiryAccessMiddleware;
use App\Http\Middleware\IpWhitelistMiddleware;
use App\Http\Middleware\UserKycMiddleware;
use App\Models\ClientKycDoc;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('verify/search', [BarcodeController::class, 'BarcodeSearch'])
    ->name('barcode.search');
Route::get('verify/{employee_id}', [BarcodeController::class, 'BarcodeShow'])->name('barcode.show');

Route::get('verify/{mobile}', [BarcodeController::class, 'InternshipConfirmCertificate'])
    ->name('barcode.internship.confirm.show');

Route::view('verification', 'docs.exp-letter-verify')->name('barcode.verify.page');

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('password/forgot', [AuthController::class, 'showPasswordResetForm'])->name('password.request');
Route::post('/', [AuthController::class, 'login']);
Route::post('password/otp', [AuthController::class, 'sendPasswordResetOtp'])->name('password.otp');
Route::post('password/reset', [AuthController::class, 'resetPasswordWithOtp'])->name('password.reset');
Route::any('logout', [AuthController::class, 'logout'])->name('logout');

Route::any('login-by-token', [AuthController::class, 'loginByToken']);

Route::prefix('dashboard')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('bypass/{id}', [AuthController::class, 'loginByPass'])->name('loginByPass');
    Route::middleware('role:admin,branch manager')->group(function () {
        Route::resource('users', UserController::class)->except('destroy');
    });
    Route::middleware('role:admin,associate,branch manager')->group(function () {
        Route::resource('clients', ClientController::class)->except('destroy');
    });
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->only('destroy');
        Route::resource('clients', ClientController::class)->only('destroy');
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::put('attendances/bulk-update', [AttendanceController::class, 'bulkUpdate'])->name('attendances.bulk-update');
        Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
        Route::get('attendances/report', [AttendanceController::class, 'report'])->name('attendances.report');

        Route::prefix('offer-letters')->name('offer-letters.')->group(function () {
            Route::get('/', [OfferLetterController::class, 'index'])->name('index');
            Route::get('create', [OfferLetterController::class, 'create'])->name('create');
            Route::post('/', [OfferLetterController::class, 'store'])->name('store');
            Route::get('{offerLetter}/print', [OfferLetterController::class, 'print'])->name('print');
        });

        Route::prefix('experience-letters')->name('experience-letters.')->group(function () {
            Route::get('/', [ExperienceLetterController::class, 'index'])->name('index');
            Route::get('create', [ExperienceLetterController::class, 'create'])->name('create');
            Route::post('/', [ExperienceLetterController::class, 'store'])->name('store');

            Route::get('{experienceLetter}/edit', [ExperienceLetterController::class, 'edit'])->name('edit');
            Route::put('{experienceLetter}', [ExperienceLetterController::class, 'update'])->name('update');

            Route::get('{experienceLetter}/print', [ExperienceLetterController::class, 'print'])->name('print');
        });
        Route::get('certificate-letters/{certificateLetter}/print', [ExperienceLetterController::class, 'PrintCertificateLetter'])->name('certificate-letters.print');

        Route::prefix('bo/leaves')->group(function () {
            Route::get('/', [LeaveController::class, 'adminIndex'])->name('leaves.admin.index');
            Route::get('/{id}/edit', [LeaveController::class, 'edit'])->name('leaves.admin.edit');
            Route::put('/{id}', [LeaveController::class, 'update'])->name('leaves.admin.update');
            Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.admin.approve');
            Route::post('/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.admin.reject');
            Route::post('/{id}/change-type', [LeaveController::class, 'changeType'])->name('leaves.admin.changeType');
        });
        Route::resource('settings', SettingController::class)->only(['index', 'edit', 'update']);
        Route::put('user-kyc/{kyc}/status', [UserKycController::class, 'updateStatus'])->name('user-kyc.updateStatus');

        Route::put('client-kyc/{kyc}/status', [ClientKycController::class, 'updateStatus'])->name('client-kyc.updateStatus');

        Route::get('whatsapp', [WhatsappController::class, 'index'])->name('whatsapp.index');
        Route::get('whatsapp/session-status', [WhatsappController::class, 'checkSessionStatus'])->name('whatsapp.session-status');
        Route::get('whatsapp/logout', [WhatsappController::class, 'logout'])->name('whatsapp.logout');
        Route::post('whatsapp/send-message', [WhatsappController::class, 'sendMessage'])->name('whatsapp.send-message');

        // User Monthly Salary Module
        Route::prefix('user-monthly-salaries')->name('user-monthly-salaries.')->group(function () {
            Route::get('/', [UserMonthlySalaryController::class, 'index'])->name('index');
            Route::get('create/{userId}/{year}/{month}', [UserMonthlySalaryController::class, 'create'])->name('create');
            Route::post('store/{userId}/{year}/{month}', [UserMonthlySalaryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [UserMonthlySalaryController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [UserMonthlySalaryController::class, 'update'])->name('update');
            Route::get('show/{id}', [UserMonthlySalaryController::class, 'show'])->name('show');
        });

        Route::prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/', [WalletController::class, 'index'])->name('index');
            Route::post('/', [WalletController::class, 'store'])->name('store');
            Route::get('transactions', [WalletController::class, 'transactions'])->name('transactions');
        });
    });

    Route::prefix('advance-salaries')->name('advance-salaries.')->group(function () {
        Route::get('/', [AdvanceSalaryController::class, 'index'])->name('index');
        Route::get('/create', [AdvanceSalaryController::class, 'create'])->name('create');
        Route::post('/', [AdvanceSalaryController::class, 'store'])->name('store');

        Route::middleware('role:admin,accounts,branch manager')->group(function () {
            Route::post('{advance}/approve', [AdvanceSalaryController::class, 'approve'])->name('approve');
            Route::post('{advance}/reject', [AdvanceSalaryController::class, 'reject'])->name('reject');
        });
    });

    Route::middleware('role:admin,accounts')->group(function () {
        Route::resource('expense-heads', ExpenseHeadController::class)->except(['show']);
        Route::resource('states', StateController::class)->except('show');
        Route::resource('products', ProductController::class)->except('index');
        Route::resource('branches', BranchController::class);
        Route::get('products/{product}/intro', [ProductController::class, 'intro'])->name('products.intro');
    });

    // Route::middleware('role:admin,accounts,branch manager,employee,intern')->resource('internship-interests', InternshipInterestController::class)->except(['create', 'store']);

    Route::middleware('role:admin,accounts,branch manager,employee')->group(function () {

        Route::resource('internship-interests', InternshipInterestController::class)->except(['create', 'store']);

        Route::get('internship-interests/{id}/download', [InternshipInterestController::class, 'download'])->name('internship-interests.download');

        // Route::get('internship-interests/{id}/send-mail', [InternshipInterestController::class, 'sendMail'])->name('internship-interests.send.mail');
    });

    Route::middleware('role:admin,accounts,branch manager')->group(function () {
        Route::resource('products', ProductController::class)->only('index');
        Route::resource('quotations', QuotationController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
        Route::get('invoices/create-from-estimate/{estimate}', [InvoiceController::class, 'createFromEstimate'])->name('invoices.convert');
        Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'addPayment'])->name('invoices.payments.store');
        Route::resource('estimates', EstimateController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    });

    Route::middleware(['role:admin,branch manager'])->post('branches/set-active', [BranchController::class, 'setActive'])->name('branches.set-active');

    Route::get('change-password', [AuthController::class, 'changePasswordForm'])->name('change-password-form');
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');

    Route::middleware([IpWhitelistMiddleware::class])->group(function () {
        Route::post('attendance/mark-in', [UserAttendanceController::class, 'markIn'])->name('attendance.mark-in');
        Route::post('attendance/mark-out', [UserAttendanceController::class, 'markOut'])->name('attendance.mark-out');
    });

    Route::resource('user-kyc', UserKycController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);

    // Google Meet & Calendar Routes
    Route::get('google/connect', [MeetingController::class, 'googleConnect'])->name('google.connect');
    Route::get('google/callback', [MeetingController::class, 'googleCallback'])->name('google.callback');
    Route::get('google/disconnect', [MeetingController::class, 'googleDisconnect'])->name('google.disconnect');
    Route::resource('meetings', MeetingController::class);
    Route::post('meetings/{id}/send-whatsapp', [MeetingController::class, 'sendWhatsapp'])->name('meetings.send-whatsapp');

    Route::middleware(UserKycMiddleware::class)->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::get('clients/{client}/details', [ClientLookupController::class, 'show'])->name('clients.details');
        Route::resource('project-remarks', ProjectRemarkController::class)->only(['store', 'destroy', 'edit', 'update']);
        Route::resource('project-milestones', ProjectMilestoneController::class)->only(['store', 'destroy', 'show', 'edit', 'update']);
        Route::resource('project-milestone-remarks', ProjectMilestoneRemarkController::class)->only(['store', 'destroy']);
        Route::resource('inquiries', InquiryController::class)->middleware(InquiryAccessMiddleware::class);
        Route::post('inquiries/{inquiry}/followup', [InquiryController::class, 'addFollowUp'])->name('inquiries.addFollowUp');
        Route::get('employee/attendance/report', [AttendanceController::class, 'index'])->name('attendances.employee.index');
        });

        // Project Task routes
        Route::prefix('projects/{project_id}/tasks')->group(function () {
            Route::get('/', [ProjectTaskController::class, 'index'])->name('project_tasks.index');
            Route::get('/create', [ProjectTaskController::class, 'create'])->name('project_tasks.create');
            Route::post('/', [ProjectTaskController::class, 'store'])->name('project_tasks.store');
        });

    Route::get('project/{slug}', [ProjectTaskController::class, 'searchResults'])->name('projects_{slug}');
    Route::get('project/{slug}/{id}', [ProjectTaskController::class, 'searchResultsShow']);

    Route::prefix('project_tasks')->group(function () {
        Route::get('/{id}', [ProjectTaskController::class, 'show'])->name('project_tasks.show');
        Route::get('/{id}/edit', [ProjectTaskController::class, 'edit'])->name('project_tasks.edit');
        Route::put('/{id}', [ProjectTaskController::class, 'update'])->name('project_tasks.update');
        Route::delete('/{id}', [ProjectTaskController::class, 'destroy'])->name('project_tasks.destroy');
    });

    Route::post('project-tasks/{task}/comments', [ProjectTaskCommentController::class, 'store'])->name('project-task-comments.store');
    Route::get('project-task-comments/{comment}/edit', [ProjectTaskCommentController::class, 'edit'])->name('project-task-comments.edit');
    Route::put('project-task-comments/{comment}', [ProjectTaskCommentController::class, 'update'])->name('project-task-comments.update');

    // Leave routes
    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('leaves.index');
        Route::get('create', [LeaveController::class, 'create'])->name('leaves.create');
        Route::post('/', [LeaveController::class, 'store'])->name('leaves.store');
    });

    // User Monthly Salary Module
    Route::get('/salaries/', [UserMonthlySalaryController::class, 'employeeSalary'])->name('salaries.show');
    Route::get('/salaries/show/{id}', [UserMonthlySalaryController::class, 'employeeSalarySlip'])->name('salaries.slip');

    Route::resource('holidays', HolidayController::class);
    Route::resource('password-vaults', PasswordVaultController::class)->except('destroy');

    if (config('modules.loan_inquiry')) {
        Route::resource('loan-inquiries', LoanInquiryController::class);
    }

    Route::get('ivr/call/initiate/{data}', [ProjectController::class, 'initiateCall'])->name('ivr.call.initiate');

    // Client KYC routes
    Route::resource('client-kyc', ClientKycController::class)->only(['create', 'store', 'edit', 'update', 'show']);

    Route::resource('expenses', ExpenseController::class);

    Route::get('/private-file/{path}', [PrivateFileController::class, 'show'])
        ->where('path', '.*')
        ->name('private.file.show');
});

Route::get('/client-kyc/document-types', function (Request $request) {
    $businessType = $request->query('business_type');
    $docTypes = (new ClientKycDoc)->getRequiredDocumentTypes($businessType);

    return response()->json($docTypes);
});

Route::get('estimates/{token}/public', [EstimateController::class, 'publicView'])->name('estimates.public');
Route::get('invoices/{token}/public', [InvoiceController::class, 'publicView'])->name('invoices.public');

Route::get('p/leave/update/{string}', [LeaveController::class, 'updateStatusPublicUrl'])->name('leave.updateStatusPublicUrl');
Route::post('p/leave/update/{string}', [LeaveController::class, 'processStatusUpdatePublicUrl'])->name('leave.updateStatusPublicUrl.process');

Route::view('pages/employee/tnc', 'pages.tnc.employee')->middleware('auth')->name('pages.tnc.employee');
Route::view('pages/internship/tnc', 'pages.tnc.internship')->middleware('auth')->name('pages.tnc.internship');
Route::view('pages/client/aggrement', 'pages.client.aggrement')->middleware('auth')->name('pages.client.aggrement');

Route::view('docs/employee', 'docs.employee')->middleware('auth')->name('docs.employee');
Route::view('docs/employee/id-card', 'docs.employee.id')->middleware('auth')->name('docs.employee.id-card');
Route::view('docs/employee/offer-letter', 'docs.employee.offer-letter')->middleware('auth')->name('docs.employee.offer-letter');
Route::get('docs/employee/experience-letter', [ExperienceLetterController::class, 'EmployeeExperienceLetter'])->middleware('auth')->name('docs.employee.experience-letter');

Route::view('docs/employee/certificate-letter', 'docs.employee.certificate-letter')->middleware('auth')->name('docs.employee.certificate-letter');

Route::view('page/test', 'layouts.docs');

// Public routes for Internship Interest form
Route::get('apply', [InternshipInterestController::class, 'create'])->name('internship-interests.create');
Route::post('apply', [InternshipInterestController::class, 'store'])->name('internship-interests.store');
Route::post('apply/resume', [InternshipInterestController::class, 'resume'])->name('internship-interests.resume');

// Public routes for Internship payment
Route::get('/pay/{interest}', [PayuPaymentController::class, 'checkout'])->name('payu.checkout');
Route::post('/payment/response', [PayuPaymentController::class, 'handleResponse'])->name('payu.response')
    ->withoutMiddleware([ValidateCsrfToken::class]);

Route::get('mob-lgn/', [App\Http\Controllers\API\AuthController::class, 'mobileLogin']);
Route::get('mobile/test', [App\Http\Controllers\API\AuthController::class, 'mobileTest'])->name('mobile.test')->middleware('auth:web');

// career

Route::view('careers', 'careers.create')->name('careers.create');
Route::post('careers/store', [CareerController::class, 'store'])->name('career.store');
Route::get('/department-skills/{id}', [CareerController::class, 'getSkills']);

Route::prefix('careers')->middleware(['auth'])->group(function () {
    Route::get('index', [CareerController::class, 'index'])->name('career.index');
    Route::get('{id}/edit', [CareerController::class, 'edit'])->name('career.edit');
    Route::put('{id}', [CareerController::class, 'update'])->name('career.update');
    Route::delete('{id}', [CareerController::class, 'destroy'])->name('career.destroy');

    Route::get('interview/{id}', [InterviewController::class, 'interviewForm'])->name('careers.interview');
    Route::post('interview/schedule/{id}', [InterviewController::class, 'scheduleInterview'])->name('interview.schedule');
    Route::put('interview/reschedule/{id}', [InterviewController::class, 'rescheduleInterview'])->name('interview.reschedule');
    Route::post('interview/result/{id}', [InterviewController::class, 'InterviewResult'])->name('interview.result');
});
