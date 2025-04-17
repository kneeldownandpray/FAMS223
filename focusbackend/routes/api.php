<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\EducationalAttainmentController;
use App\Http\Controllers\WorkExperienceController;
use App\Http\Controllers\UserVideoController;
use App\Http\Controllers\ReelsController;
use App\Http\Controllers\VideoScrollController;
use App\Http\Controllers\HiredController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserRequirementController;
use App\Http\Controllers\VisaStatusController;
use App\Http\Controllers\VisaStatusHistoryController;



// Middleware-protected route to fetch the authenticated user's details
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::resource('resumes', ResumeController::class);
Route::resource('resumes.skills', SkillController::class);
Route::resource('resumes.certifications', CertificationController::class);
// Route::resource('resumes.workExperiences', WorkExperienceController::class);

// Authentication Routes
// Route::post('register', [AuthController::class, 'register']); 
Route::post('registerApplicant', [AuthController::class, 'registerApplicant']); 
Route::post('registerEmployer', [AuthController::class, 'registerEmployer']); 
Route::post('loginFront', [AuthController::class, 'login']);        // Login for Applicants and Employers
Route::post('loginAdmin', [AuthController::class, 'loginAdmin']); // Login for Admins
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');  // User logout (requires authentication)
Route::put('validUsers/{id}/changePassword', [AccountController::class, 'changePassword']); // Change password for user
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);




// Account Management Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload-profile-picture', [ProfileController::class, 'uploadProfilePicture']);
    Route::post('/user-requirements', [UserRequirementController::class, 'store']);
    Route::get('/user-requirements/{id}/download', [UserRequirementController::class, 'download']);
    Route::get('/display/requirements', [UserRequirementController::class, 'getAllRequirementTypes']);
    Route::get('/admin/user-requirements/missing/{userId}', [UserRequirementController::class, 'getMissingRequirements']);
    Route::post('/add-requirement-type', [UserRequirementController::class, 'addRequirementType']);
    Route::put('/edit-requirement-type/{id}', [UserRequirementController::class, 'editRequirementType']);
    Route::delete('/delete-requirement-type/{id}', [UserRequirementController::class, 'deleteRequirementType']);
    Route::get('/user-requirements', [UserRequirementController::class, 'getUserResume']);


    //Admin api
    Route::get('/admin/requirement-types', [UserRequirementController::class, 'getAllRequirementTypes']);
    Route::get('/admin/user-requirements', [UserRequirementController::class, 'adminIndex']);
    Route::put('/admin/user-requirements/{id}', [UserRequirementController::class, 'adminUpdate']);
    Route::get('/visa-status-history', [VisaStatusHistoryController::class, 'index']);
    Route::post('/visa-status-history', [VisaStatusHistoryController::class, 'store']);
    Route::get('/visa-status-history/{id}', [VisaStatusHistoryController::class, 'show']);
    Route::put('/visa-status-history/{id}', [VisaStatusHistoryController::class, 'update']);
    Route::delete('/visa-status-history/{id}', [VisaStatusHistoryController::class, 'destroy']);
    Route::get('/visa-statuses', [VisaStatusController::class, 'index']);
    Route::get('/visa-progress', [VisaStatusController::class, 'getProgress']);
    Route::put('/visa-statuses/{workerId}', [VisaStatusController::class, 'update']);


    Route::post('register', [AuthController::class, 'register']); 
    Route::get('/counter/status-one', [CounterController::class, 'countStatusOne']);
    Route::post('acceptAccount', [AccountController::class, 'accountValidatorAcceptor']);
    Route::post('updateStatusToTwo/{id}', [AccountController::class, 'pendingAccounts44422']);
    Route::get('notValidUsers', [AccountController::class, 'pendingAccounts']);
    Route::get('validUsers', [AccountController::class, 'getFilteredAccounts']);
    Route::get('/displayAllDataDropdown', [HiredController::class, 'displayAllDataDropdown']);
    Route::delete('/hire/{id}/unlink-permanently', [HiredController::class, 'unlinkPermanently']);
    Route::put('/hire/{id}/approve', [HiredController::class, 'approve']);
    Route::put('/hire/{id}/rejected', [HiredController::class, 'rejected']);
    Route::put('/hire/{id}/restore', [HiredController::class, 'restore']);
    Route::get('/check-interested/{applicant_id}/{selected_employer_id}', [HiredController::class, 'showInterested']);
    Route::get('/display-hiring-approval', [HiredController::class, 'displayAllNotAcceptable']);
    Route::get('/display-hired', [HiredController::class, 'displayAllHired']);
    Route::get('/display-rejected', [HiredController::class, 'displayAllRejected']);
    Route::get('/display-hired-applicant', [HiredController::class, 'displayAllApplicantUnderByLoggedInEmployerHired']);
    Route::get('/display-rejected-applicant', [HiredController::class, 'displayAllApplicantUnderByLoggedInEmployerRejected']);
    Route::get('/display-pending-applicant', [HiredController::class, 'displayAllApplicantUnderByLoggedInEmployerPending']);
    //Front api applicant

    Route::get('users', [AccountController::class, 'index']);    // Fetch user list
    Route::put('validUsers/{id}', [AccountController::class, 'update']);  // Update user by ID
    Route::delete('validUsers/{id}', [AccountController::class, 'destroy']);  // Delete user by ID
    Route::apiResource('resumes', ResumeController::class);
    Route::get('resume', [ResumeController::class, 'showOwnResume']); // display specific resume
    Route::post('/resumes/user/{userId}', [ResumeController::class, 'getByUserId']);
    Route::post('/resumes/video/{userId}', [ResumeController::class, 'getByUserVideo']);
    Route::apiResource('educational-attainments', EducationalAttainmentController::class);
    Route::apiResource('work-experiences', WorkExperienceController::class);
    // Route::post('work-experiences', [WorkExperienceController::class, 'test']);
    Route::post('/user-videos', [UserVideoController::class, 'store']);    // Create new video
    Route::get('/user-videos', [UserVideoController::class, 'index']);     // Fetch all videos
    Route::get('/user-videos/{id}', [UserVideoController::class, 'show']); // Fetch a single video
    Route::put('/user-videos/{id}', [UserVideoController::class, 'update']); // Update a video
    Route::delete('/user-videos/{id}', [UserVideoController::class, 'destroy']); // Delete a video

    //employer 
    Route::get('/reels', [ReelsController::class, 'getReels']);
    Route::get('/next-video', [VideoScrollController::class, 'getNextVideo']);
    Route::post('/previous-video', [VideoScrollController::class, 'getPreviousVideo']);
    Route::post('/hire', [HiredController::class, 'hire']);
    Route::post('/hire-checker/{applicantId}', [HiredController::class, 'isUserHired']);
    
    
    // Short Polling Route
    Route::get('shortpolling', [AccountController::class, 'shortpolling']);  // Polling for updates
});
  