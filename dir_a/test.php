<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AdminInquiryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonthlyReportController;

Route::middleware('auth')->group(function () {


  Route::get('/', [UserController::class, 'index'])->middleware('auth')->name('top');
  Route::get('/announcements', [UserController::class, 'showAnnouncementPage'])->middleware('auth')->name('announcements');


  Route::middleware('auth')->group(function () {
    Route::get('/questions/users/{user}', [QuestionController::class, 'showMyQuestions'])->name('questions.showMyQuestions');
    Route::get('/questions/users/{user}/drafts', [QuestionController::class, 'showMyDraftQuestions'])->name('questions.showMyDraftQuestions');
    Route::get('/questions/noAnswers/show', [QuestionController::class, 'noAnswers'])->name('questions.noAnswers');
    Route::resource('/questions', QuestionController::class);

    Route::post('/questions/{question}/comments', [QuestionController::class, 'commentStore'])->name('questions.commentStore');
    Route::patch('/questions/{question}/comments/{comment}', [QuestionController::class, 'commentUpdate'])->name('questions.commentUpdate');
    Route::delete('/questions/{question}/{comment}', [QuestionController::class, 'commentDestroy'])->name('questions.commentDestroy');

    Route::post('/questions/{question}/comments/{comment}/reply', [QuestionController::class, 'replyStore'])->name('questions.replyStore');
    Route::patch('/questions/{question}/comments/{reply}/update', [QuestionController::class, 'replyUpdate'])->name('questions.replyUpdate');
    Route::delete('/questions/{question}/comments/{reply}', [QuestionController::class, 'replyDestroy'])->name('questions.replyDestroy');
  });

  Route::middleware(['auth'])->group(function () {
    Route::resource('/articles', ArticleController::class)->except(['index', 'show']);
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('/articles/users/{id}', [ArticleController::class, 'showArticles'])->name('articles.myblog');
    Route::get('/articles/users-favorite/{id}', [ArticleController::class, 'showFavoriteArticles'])->name('articles.favorites');
    Route::get('/articles/users/{id}/drafts', [ArticleController::class, 'showMyDraftArticles'])->name('articles.showMyDraftArticles');
    Route::post('/articles/{article}/favorite', [ArticleController::class, 'favorite'])->name('articles.favorite');
    Route::delete('/articles/{article}/unfavorite', [ArticleController::class, 'unfavorite'])->name('articles.unfavorite');
    Route::post('/articles/{article}/comments', [ArticleController::class, 'commentStore'])->name('articles.commentStore');
    Route::patch('/articles/{article}/comments/{comment}', [ArticleController::class, 'commentUpdate'])->name('articles.commentUpdate');
    Route::delete('/articles/{article}/{comment}', [ArticleController::class, 'commentDestroy'])->name('articles.commentDestroy');

    Route::post('/like/{article}', [ArticleController::class, 'likeStore'])->name('articles.likeStore');
    Route::post('/unlike/{article}', [ArticleController::class, 'likeDestroy'])->name('articles.likeDestroy');
  });

  Route::middleware('auth')
    ->controller(ProfileController::class)
    ->group(function () {
      Route::get('/profile/{id}', 'show')->name('profile.show');
      Route::get('/profile', 'edit')->name('profile.edit');
      Route::patch('/profile', 'update')->name('profile.update');
      Route::post('/profile/follow{id}', 'follow')->name('profile.follow');
      Route::post('/profile/unfollow{id}', 'unfollow')->name('profile.unfollow');
      Route::post('/profile/submitInquiry', 'submitInquiry')->name('profile.submitInquiry');
      Route::get('/searchUser', 'searchUser')->name('searchUser');
    });

  Route::middleware('auth')->group(function () {

    Route::get('/monthly_reports', [MonthlyReportController::class, 'index'])->name('monthlyReport.index');
    Route::get('/monthly_reports/create', [MonthlyReportController::class, 'create'])->name('monthlyReport.create');
    Route::post('/monthly_reports', [MonthlyReportController::class, 'store'])->name('monthlyReport.store');
    Route::get('/monthly_reports/{monthlyReport}', [MonthlyReportController::class, 'show'])->name('monthlyReport.show');
    Route::get('/monthly_reports/{monthlyReport}/edit', [MonthlyReportController::class, 'edit'])->name('monthlyReport.edit');
    Route::patch('/monthly_reports/{monthlyReport}', [MonthlyReportController::class, 'update'])->name('monthlyReport.update');
    Route::delete('/monthly_reports/{monthlyReport}', [MonthlyReportController::class, 'destroy'])->name('monthlyReport.destroy');
    Route::get('/monthly_reports/users/{id}', [MonthlyReportController::class, 'showMyReports'])->name('monthlyReport.showMyReports');
    Route::get('/monthly_reports/users/{id}/drafts', [MonthlyReportController::class, 'showMyDraftReports'])->name('monthlyReport.showMyDraftReports');
  });

  //コメント関連のルート
  Route::post('/monthly_reports/{monthlyReport}/comments', [MonthlyReportController::class, 'commentStore'])->name('monthlyReport.commentStore');
  Route::patch('/monthly_reports/{monthlyReport}/comments/{comment}', [MonthlyReportController::class, 'commentUpdate'])->name('monthlyReport.commentUpdate');
  Route::delete('/monthly_reports/{monthlyReport}/{comment}', [MonthlyReportController::class, 'commentDestroy'])->name('monthlyReport.commentDestroy');


  Route::prefix('/admin')->middleware('judgeAdmin')->group(function () {
    Route::get('/top', [AdminController::class, 'index'])->name('admin.top');
    Route::prefix('/users')->group(function () {
      Route::get('/', [AdminController::class, 'users'])->name('admin.users');
      Route::get('/create', [AdminController::class, 'create'])->name('admin.users.create');
      Route::post('/store', [AdminController::class, 'store'])->name('admin.users.store');
      Route::get('/show/{id}', [AdminController::class, 'show'])->name('admin.users.show');
      Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.users.edit');
      Route::get('/showDeletePage/{id}', [AdminController::class, 'showDeletePage'])->name('admin.users.showDeletePage');
      Route::patch('/update/{id}', [AdminController::class, 'update'])->name('admin.users.update');
      Route::patch('/destroy/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

      Route::get('/roles', [AdminController::class, 'roles'])->name('admin.users.role');
      Route::get('/roles/new', [AdminController::class, 'registerNewRole'])->name('admin.users.registerNewRole');
      Route::post('/roles/new/{id}', [AdminController::class, 'storeNewRole'])->name('admin.users.storeNewRole');
      Route::delete('/roles/delete/{id}', [AdminController::class, 'destroy'])->name('admin.users.delete');
    });
    Route::prefix('/announcement')->group(function () {
      Route::get('/showAll', [AnnouncementController::class, 'showAll'])->name('admin.announcement.showAll');
      Route::get('/create', [AnnouncementController::class, 'create'])->name('admin.announcement.create');
      Route::post('/store', [AnnouncementController::class, 'store'])->name('admin.announcement.store');
    });
    Route::prefix('/inquiry')->group(function () {
      Route::get('/showAll', [AdminInquiryController::class, 'showAll'])->name('admin.inquiry.showAll');
      Route::get('/mailList', [AdminInquiryController::class, 'mailList'])->name('admin.inquiry.mailList');
      Route::patch('/store', [AdminInquiryController::class, 'update'])->name('admin.inquiry.update');
    });
  });

  Route::fallback(function () {
    return redirect(abort(404));
  });
});
require __DIR__ . '/auth.php';
