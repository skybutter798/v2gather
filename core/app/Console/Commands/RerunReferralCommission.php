<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Lib\Mlm;
use Log;

class RerunReferralCommission extends Command
{
    protected $signature = 'commission:rerun';

    protected $description = 'Rerun referral commission script for users where plan_id = 2';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::where('plan_id', 2)->get(); // Fetch users with plan_id = 2

        foreach ($users as $user) {
            try {
                // Assuming MLM.php is a service class you have and referralCommission is a method within it
                // Ensure you have the MLM service correctly initialized here
                $mlmService = app()->make('App\Lib\MLM'); // Adjust the path as necessary
                $mlmService->setUser($user); // Ensure this method exists to set the user in your MLM class
                $mlmService->referralCommission(); // Call the referralCommission function
                
                Log::info("Referral commission rerun for user: {$user->id}");
            } catch (\Exception $e) {
                Log::error("Failed to rerun referral commission for user: {$user->id}, Error: " . $e->getMessage());
            }
        }

        $this->info('Completed rerunning referral commissions for users with plan_id = 2.');
    }
}
