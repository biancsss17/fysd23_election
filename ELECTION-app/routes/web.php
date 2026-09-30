<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $request->session()->put('splash_passed', true);

    return view('splash');
})->name('splash');
Route::view('/about', 'about')->name('about');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/voter-access', [HomeController::class, 'voterAccess'])->name('voter-access');
Route::post('/voter-access/verify', [HomeController::class, 'verifyVoterAccess'])->name('voter-access.verify');
Route::post('/candidacy', [HomeController::class, 'submitCandidacy'])->name('candidacy.submit');
Route::post('/nomination', [HomeController::class, 'submitNomination'])->name('nomination.submit');
Route::post('/vote/submit', [HomeController::class, 'submitVote'])->name('vote.submit');
Route::get('/vote-countdown', [HomeController::class, 'voteCountdown'])->name('vote-countdown');
Route::post('/vote-countdown/close', [HomeController::class, 'autoCloseBallot'])->name('vote-countdown.close');
Route::get('/results', [HomeController::class, 'results'])->name('results');
Route::get('/final-document', [HomeController::class, 'finalDocument'])->name('final-document');
Route::get('/ballot/{positionId?}', [HomeController::class, 'ballot'])->name('ballot');
Route::get('/election/data', [HomeController::class, 'publicElectionData'])->name('election.data');
Route::get('/review-vote', [HomeController::class, 'reviewVote'])->name('review-vote');
Route::post('/review-vote/verify-email', [HomeController::class, 'verifyVoterEmail'])->name('review-vote.verify-email');
Route::post('/review-vote/submit', [HomeController::class, 'submitFinalVote'])->name('review-vote.submit');
Route::get('/vote-receipt', [HomeController::class, 'voteReceipt'])->name('vote-receipt');
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/session/keepalive', function () {
    return response()->json(['ok' => true]);
})->middleware('admin.auth')->name('admin.session.keepalive');
Route::get('/admin/dashboard', [AdminAuthController::class, 'dashboard'])->middleware('admin.auth')->name('admin.dashboard');
Route::post('/admin/dashboard/reset-election', [AdminAuthController::class, 'resetElection'])->middleware('admin.auth')->name('admin.dashboard.reset-election');
Route::post('/admin/dashboard/purge-election', [AdminAuthController::class, 'purgeElection'])->middleware('admin.auth')->name('admin.dashboard.purge-election');
Route::get('/admin/voter-management', [AdminAuthController::class, 'voterManagement'])->middleware('admin.auth')->name('admin.voter-management');
Route::get('/admin/voter-management/data', [AdminAuthController::class, 'voterManagementData'])->middleware('admin.auth')->name('admin.voter-management.data');
Route::post('/admin/voter-management', [AdminAuthController::class, 'storeVoter'])->middleware('admin.auth')->name('admin.voter-management.store');
Route::patch('/admin/voter-management/{voter}/eligibility', [AdminAuthController::class, 'updateVoterEligibility'])->middleware('admin.auth')->name('admin.voter-management.eligibility');
Route::post('/admin/voter-management/import', [AdminAuthController::class, 'importVoters'])->middleware('admin.auth')->name('admin.voter-management.import');
Route::post('/admin/voter-management/delete', [AdminAuthController::class, 'deleteVoterByEmail'])->middleware('admin.auth')->name('admin.voter-management.delete-email');
Route::delete('/admin/voter-management/{voter}', [AdminAuthController::class, 'deleteVoter'])->middleware('admin.auth')->name('admin.voter-management.destroy');
Route::get('/admin/position-management', [AdminAuthController::class, 'positionManagement'])->middleware('admin.auth')->name('admin.position-management');
Route::post('/admin/position-management', [AdminAuthController::class, 'storePosition'])->middleware('admin.auth')->name('admin.position-management.store');
Route::delete('/admin/position-management/{position}', [AdminAuthController::class, 'deletePosition'])->middleware('admin.auth')->name('admin.position-management.destroy');
Route::patch('/admin/position-management/{position}/reorder', [AdminAuthController::class, 'reorderPosition'])->middleware('admin.auth')->name('admin.position-management.reorder');
Route::post('/admin/position-management/{position}/reorder', [AdminAuthController::class, 'reorderPosition'])->middleware('admin.auth')->name('admin.position-management.reorder.post');
Route::patch('/admin/position-management/{position}/unlock', [AdminAuthController::class, 'unlockPosition'])->middleware('admin.auth')->name('admin.position-management.unlock');
Route::patch('/admin/position-management/{position}/lock', [AdminAuthController::class, 'lockPosition'])->middleware('admin.auth')->name('admin.position-management.lock');
Route::patch('/admin/position-management/{position}/close', [AdminAuthController::class, 'closePosition'])->middleware('admin.auth')->name('admin.position-management.close');
Route::patch('/admin/position-management/{position}/reopen', [AdminAuthController::class, 'reopenPosition'])->middleware('admin.auth')->name('admin.position-management.reopen');
Route::patch('/admin/position-management/{position}/candidacy', [AdminAuthController::class, 'toggleCandidacy'])->middleware('admin.auth')->name('admin.position-management.candidacy');
Route::patch('/admin/position-management/{position}/nomination', [AdminAuthController::class, 'toggleNomination'])->middleware('admin.auth')->name('admin.position-management.nomination');
Route::get('/admin/results-document-preview', [AdminAuthController::class, 'resultsDocumentPreview'])->middleware('admin.auth')->name('admin.results-document-preview');
Route::patch('/admin/results-document-preview/winner/{candidateSubmission}', [AdminAuthController::class, 'updateWinnerName'])->middleware('admin.auth')->name('admin.results-document-preview.winner.update');
Route::post('/admin/results-document-preview/position/{position}/winner', [AdminAuthController::class, 'addManualWinner'])->middleware('admin.auth')->name('admin.results-document-preview.winner.add');
Route::post('/admin/results-document-preview/position', [AdminAuthController::class, 'addManualPosition'])->middleware('admin.auth')->name('admin.results-document-preview.position.add');
Route::get('/admin/audit-log', [AdminAuthController::class, 'auditLog'])->middleware('admin.auth')->name('admin.audit-log');
Route::get('/admin/audit-log/{auditLog}', [AdminAuthController::class, 'auditLogShow'])->middleware('admin.auth')->name('admin.audit-log.show');
Route::post('/admin/audit-log/{auditLog}/restore', [AdminAuthController::class, 'restoreAuditLog'])->middleware('admin.auth')->name('admin.audit-log.restore');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('admin.auth')->name('admin.logout');
