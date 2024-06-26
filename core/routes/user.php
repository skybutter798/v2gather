<?php

use Illuminate\Support\Facades\Route;



Route::namespace('User\Auth')->name('user.')->group(function () {
    
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });
    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
});

Route::middleware('auth')->name('user.')->group(function () {
    //authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['check.status'])->group(function () {

        Route::get('user-data', 'User\UserController@userData')->name('data');
        Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

        Route::middleware('registration.complete')->namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                
                Route::get('dashboard', 'home')->name('home');
                
                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');

                //Login history
                Route::get('login-history', 'loginHistory')->name('login.history');

                //Check user
                Route::post('search-user', 'searchUser')->name('search');

                //Balance Transfer
                Route::get('transfer', 'balanceTransfer')->name('balance.transfer');
                Route::post('transfer', 'transfer')->name('balance.transfer');
                Route::post('convert', 'convert')->name('user.convert');
                Route::post('transferto', 'transferto')->name('user.transferto');

                Route::get('invest', 'invest')->name('invest');
                Route::get('ref-com', 'refCom')->name('refCom');
                Route::get('binary-com', 'binaryCom')->name('binaryCom');

                //
                Route::get('referral', 'referral')->name('referral');
                Route::get('my-tree', 'myTree')->name('myTree');
                Route::get('/tree/{user}', 'binaryTree')->name('binary.tree');
                Route::get('binary-summary', 'binarySummary')->name('binary.summary');
                Route::get('/get-user-id-by-username/{username}', 'getUserIdByUsername')->name('getUserIdByUsername');
                
                Route::get('/fetch-children/{userId}', 'UserController@fetchChildren')->name('fetch-children');
                Route::get('/user-details/{userId}', 'UserController@showUserDetails')->name('user-details');
                Route::get('v300', 'v300')->name('v300');
                
                Route::get('merchant', 'merchant')->name('merchant');

                Route::get('attachment-download/{fil_hash}', 'attachmentDownload')->name('attachment.download');
            });

            // Plan
            Route::controller('PlanController')->prefix('plan')->name('plan.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('subscribe', 'subscribe')->name('subscribe');
                Route::post('subscribev300', 'subscribev300')->name('subscribev300');
                Route::get('bv-log', 'bvLog')->name('bvLog');
            });

            //PTC 
            Route::controller('PtcController')->name('ptc.')->prefix('ptc')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('show/{id}', 'show')->name('show');
                Route::post('confirm/{id}', 'confirm')->name('confirm');
                Route::get('clicks', 'clicks')->name('clicks');
            });


            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });


            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });

        });

        // Payment
        Route::middleware('registration.complete')->prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});
