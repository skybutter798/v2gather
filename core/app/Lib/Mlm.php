<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\BvLog;
use App\Models\Transaction;
use App\Models\User;

use Illuminate\Support\Facades\Log;

class Mlm
{
    /**
     * User who subscribe a plan
     *
     * @var object
     */
    public $user;

    /**
     * Plan which subscribed by the user
     *
     * @var object
     */
    public $plan;

    /**
     * General setting
     *
     * @var object
     */
    public $setting;

    /**
     * Transaction number of whole process
     *
     * @var string
     */
    public $trx;

    /**
     * Dispatch commission from plan
     *
     * @var string
     */
    public $commissionPlan;

    /**
     * Initialize some global properties
     *
     * @param object $user
     * @param object $plan
     * @param string $trx
     * @return void
     */
    public function __construct($user = null, $plan = null, $trx = null)
    {
        $this->user    = $user;
        $this->plan    = $plan;
        $this->trx     = $trx;
        $this->setting = gs();
    }

    /**
     * Get the positioner user object
     *
     * @param object $positioner
     * @param int $position
     * @return object;
     */
    public static function getPositioner($positioner, $position)
    {
        $getPositioner = $positioner;

        while (0 == 0) {
            $getUnder = User::where('pos_id', $positioner->id)->where('position', $position)->first(['id', 'pos_id', 'position', 'username']);

            if ($getUnder) {
                $positioner    = $getUnder;
                $getPositioner = $getUnder;
            } else {
                break;
            }
        }

        return $getPositioner;
    }

    /**
     * Give BV to upper positioners
     *
     * @return void
     */
    public function updateBv()
    {
        $user = $this->user;
        $bv   = $this->plan->bv;
        $initiatorUsername = auth()->user()->username; // Store initiator's username for logging
    
        while (true) {
            $upper = User::where('id', $user->pos_id)->first();
    
            if (!$upper) {
                Log::channel('plan')->info('BV update reached top of the chain.', [
                    'initiator_username' => $initiatorUsername,
                    'last_user_id' => $user->id,
                ]);
                break;
            }
    
            if ($upper->plan_id == 0) {
                $user = $upper;
                continue;
            }
    
            $upperUserPlan = $upper->plan;
    
            if(!$upperUserPlan){
                $user = $upper;
                continue;
            }
    
            if ($this->setting->dispatch_commission_module == Status::DISPATCH_COMMISSION_LOWER_PLAN) {
                if ($bv > $upperUserPlan->bv) {
                    $originalBv = $bv;
                    $bv = $upperUserPlan->bv;
                    Log::channel('plan')->info('Adjusting BV to upper user’s lower plan BV.', [
                        'initiator_username' => $initiatorUsername,
                        'current_user_id' => $user->id,
                        'upper_user_id' => $upper->id,
                        'original_bv' => $originalBv,
                        'adjusted_bv' => $bv,
                    ]);
                }
            } elseif ($this->setting->dispatch_commission_module == Status::DISPATCH_COMMISSION_SELF_PLAN) {
                $originalBv = $bv;
                $bv = $upperUserPlan->bv;
                Log::channel('plan')->info('Adjusting BV to upper user’s plan BV (Self plan module).', [
                    'initiator_username' => $initiatorUsername,
                    'current_user_id' => $user->id,
                    'upper_user_id' => $upper->id,
                    'original_bv' => $originalBv,
                    'adjusted_bv' => $bv,
                ]);
            }
    
            $bvlog = new BvLog();
            $bvlog->user_id = $upper->id;
            $bvlog->trx_type = '+';
            $extra = $upper->userExtra;
    
            if ($user->position == 1) {
                $extra->bv_left += $bv;
                $bvlog->position = '1';
            } else {
                $extra->bv_right += $bv;
                $bvlog->position = '2';
            }
    
            $extra->save();
            $bvlog->amount = $bv;
            $bvlog->details = 'PB from ' . $initiatorUsername;
            $bvlog->save();
    
            Log::channel('plan')->info('BV successfully updated.', [
                'initiator_username' => $initiatorUsername,
                'upper_user_id' => $upper->id,
                'bv_added' => $bv,
                'position' => $bvlog->position,
            ]);
    
            $user = $upper;
        }
    }

    
    public function updateBv300()
    {
        $user = $this->user;
        $bv = $this->plan->bv;
        Log::channel('plan')->info('Starting updateBv300 for user: ' . $user->id . ' with initial bv: ' . $bv);
    
        while (true) {
            $upper = User::where('id', $user->pos_id)->first();
    
            if (!$upper) {
                Log::channel('plan')->info('No upper user found, breaking loop.');
                break;
            }
    
            if ($upper->plan_3 == 0) {
                Log::channel('plan')->info('Upper user (' . $upper->id . ') does not have plan_3, continuing.');
                $user = $upper;
                continue;
            }
    
            $upperUserPlan = $upper->plan_3;
            Log::channel('plan')->info('Upper user (' . $upper->id . ') plan_3 status: ' . $upperUserPlan);
    
            // Assuming the rest of the logic for dispatch commission module adjustments goes here
    
            $bvlog = new BvLog();
            $bvlog->user_id = $upper->id;
            $bvlog->trx_type = '+';
    
            $extra = $upper->userExtra; // Ensure userExtra is correctly fetched
    
            if ($user->position == 1) {
                $extra->bv_left += $bv;
                $bvlog->position = '1';
                Log::channel('plan')->info('Adding bv to bv_left for user ' . $upper->id);
            } else {
                $extra->bv_right += $bv;
                $bvlog->position = '2';
                Log::channel('plan')->info('Adding bv to bv_right for user ' . $upper->id);
            }
    
            // Proceed with saving
            $extra->save();
            $bvlog->amount = $bv;
            $bvlog->details = 'PB from ' . auth()->user()->username;
            $bvlog->save();
            Log::channel('plan')->info('Saved BvLog and UserExtra for user ' . $upper->id . ' with bv: ' . $bv);
    
            $user = $upper;
        }
    }

    
    /**
     * Give referral commission to immediate referrer
     *
     * @return void
     */
    public function referralCommission()
    {
        $user = $this->user;
        $referrer = $user->referrer;
        $refPlan = @$referrer->plan;
    
        if (!$referrer) {
            Log::channel('plan')->info('Referral commission skipped: User has no referrer.', [
                'user_id' => $user->id,
            ]);
            return;
        }
    
        if ($refPlan) {
            $totalAmount = $this->plan->ref_com;
    
            Log::channel('plan')->info('Initial referral commission calculated.', [
                'user_id' => $user->id,
                'referrer_id' => $referrer->id,
                'initial_amount' => $totalAmount,
            ]);
    
            if ($this->setting->dispatch_commission_module == Status::DISPATCH_COMMISSION_LOWER_PLAN) {
                if ($totalAmount > $refPlan->ref_com) {
                    $totalAmount = $refPlan->ref_com;
                    Log::channel('plan')->info('Referral commission adjusted to referrer\'s plan commission (Lower Plan).', [
                        'user_id' => $user->id,
                        'referrer_id' => $referrer->id,
                        'adjusted_amount' => $totalAmount,
                    ]);
                }
            } elseif ($this->setting->dispatch_commission_module == Status::DISPATCH_COMMISSION_SELF_PLAN) {
                $totalAmount = $refPlan->ref_com;
                Log::channel('plan')->info('Referral commission matched to referrer\'s plan commission (Self Plan).', [
                    'user_id' => $user->id,
                    'referrer_id' => $referrer->id,
                    'matched_amount' => $totalAmount,
                ]);
            }
    
            // Calculate the distribution
            $amountToBalance = $totalAmount * 0.7;
            $amountToRP = $totalAmount * 0.2;
            $amountToEP = $totalAmount * 0.1;
    
            // Update the referrer's balance, RP, and EP
            $referrer->balance += $amountToBalance;
            $referrer->RP += $amountToRP;
            $referrer->EP += $amountToEP;
            $referrer->total_ref_com += $totalAmount;
            $referrer->save();
    
            $trx = $this->trx;
            $transaction = new Transaction();
            $transaction->user_id = $referrer->id;
            $transaction->amount = $totalAmount;
            $transaction->post_balance = $referrer->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Direct referral commission from ' . $user->username;
            $transaction->trx = $trx;
            $transaction->remark = 'referral_commission';
            $transaction->save();
    
            Log::channel('plan')->info('Referral commission processed and distributed.', [
                'referrer_id' => $referrer->id,
                'amount_to_balance' => $amountToBalance,
                'amount_to_RP' => $amountToRP,
                'amount_to_EP' => $amountToEP,
                'transaction_id' => $trx,
            ]);
    
            notify($referrer, 'REFERRAL_COMMISSION', [
                'amount' => showAmount($totalAmount),
                'username' => $user->username,
                'post_balance' => $referrer->balance,
                'RP' => showAmount($amountToRP),
                'EP' => showAmount($amountToEP),
                'trx' => $trx,
            ]);
        } else {
            Log::channel('plan')->warning('Referral commission calculation skipped: Referrer does not have an active plan.', [
                'user_id' => $user->id,
                'referrer_id' => $referrer->id,
            ]);
        }
    }

    public function referralCommission300()
    {
        $user = $this->user;
        $referrer = $user->referrer;
        $refPlan = $referrer ? $referrer->plan_3 : null;
        
        Log::channel('plan')->info('Referrer obtained: ' . ($referrer ? "Yes" : "No"));
        Log::channel('plan')->info('Referrer plan_3 value: ' . $refPlan);
    
        if ($refPlan == 1) {
            $totalAmount = $this->plan->ref_com;
            Log::channel('plan')->info('Initial total amount from plan ref_com: ' . $totalAmount);

            // Calculate the distribution
            $amountToBalance = $totalAmount * 0.7;
            $amountToRP = $totalAmount * 0.2;
            $amountToEP = $totalAmount * 0.1;
            
            // Update the referrer's balance, RP, and EP
            $referrer->balance += $amountToBalance;
            $referrer->RP += $amountToRP;
            $referrer->EP += $amountToEP;
            $referrer->total_ref_com += $totalAmount;
            $referrer->save();
    
            Log::channel('plan')->info("Distribution amounts - To Balance: $amountToBalance, To RP: $amountToRP, To EP: $amountToEP");
    
            // Assuming this is a simulated update and actual database operations are commented out
            Log::channel('plan')->info("info updating referrer's balance, RP, and EP without saving");
            Log::channel('plan')->info("Referrer's new balance: " . ($referrer->balance + $amountToBalance));
            Log::channel('plan')->info("Referrer's new RP: " . ($referrer->RP + $amountToRP));
            Log::channel('plan')->info("Referrer's new EP: " . ($referrer->EP + $amountToEP));
            Log::channel('plan')->info("Referrer's new total referral commission: " . ($referrer->total_ref_com + $totalAmount));
    
            $trx = $this->trx;
            $transaction = new Transaction();
            $transaction->user_id = $referrer->id;
            $transaction->amount = $totalAmount;
            $transaction->post_balance = $referrer->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Direct referral commission from ' . $user->username;
            $transaction->trx = $trx;
            $transaction->remark = 'referral_commission';
            $transaction->save();
    
            Log::channel('plan')->info('Referral commission processed and distributed.', [
                'referrer_id' => $referrer->id,
                'amount_to_balance' => $amountToBalance,
                'amount_to_RP' => $amountToRP,
                'amount_to_EP' => $amountToEP,
                'transaction_id' => $trx,
            ]);
    
            notify($referrer, 'REFERRAL_COMMISSION', [
                'amount' => showAmount($totalAmount),
                'username' => $user->username,
                'post_balance' => $referrer->balance,
                'RP' => showAmount($amountToRP),
                'EP' => showAmount($amountToEP),
                'trx' => $trx,
            ]);
        } else {
            Log::channel('plan')->warning('Referral commission process skipped: Referrer does not have plan_3.', [
                'user_id' => $user->id,
                'referrer_id' => $referrer ? $referrer->id : 'N/A',
            ]);
        }
    }
    /**
     * Give tree commission to upper positioner
     *
     * @return void
     */
    public function treeCommission()
    {
        $user   = $this->user;
        $amount = $this->plan->tree_com;

        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();

            if (!$upper) {
                break;
            }

            if ($upper->plan_id == 0) {
                $user = $upper;
                continue;
            }

            $upperUserPlan = $upper->plan;

            if(!$upperUserPlan){
                $user = $upper;
                continue;
            }

            if ($this->setting->dispatch_commission_module == Status::DISPATCH_COMMISSION_LOWER_PLAN) {

                if ($amount > $upperUserPlan->tree_com) {
                    $amount = $upperUserPlan->tree_com;
                }
            } elseif ($this->setting->dispatch_commission_module == Status::DISPATCH_COMMISSION_SELF_PLAN) {
                $amount = $upperUserPlan->tree_com;
            }

            $upper->balance += $amount;
            $upper->total_binary_com += $amount;
            $upper->save();

            $trx                       = $this->trx;
            $transaction               = new Transaction();
            $transaction->user_id      = $upper->id;
            $transaction->amount       = $amount;
            $transaction->post_balance = $upper->balance;
            $transaction->charge       = 0;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Tree commission';
            $transaction->remark       = 'binary_commission';
            $transaction->trx          = $trx;
            $transaction->save();

            notify($upper, 'TREE_COMMISSION', [
                'amount'       => showAmount($amount),
                'post_balance' => $upper->balance,
            ]);

            $user = $upper;
        }
    }

    /**
     * Update paid count users to upper positioner when user subscribe a plan
     *
     * @return void
     */
    public function updatePaidCount()
    {
        $user = $this->user;

        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();

            if (!$upper) {
                break;
            }

            $extra = $upper->userExtra;

            if ($user->position == 1) {
                $extra->free_left -= 1;
                $extra->paid_left += 1;
            } else {
                $extra->free_right -= 1;
                $extra->paid_right += 1;
            }

            $extra->save();
            $user = $upper;
        }
    }

    /**
     * Update free count users to upper positioner when user register to this system
     *
     * @param object $user
     * @return void
     */
    public static function updateFreeCount($user)
    {

        while (0 == 0) {
            $upper = User::where('id', $user->pos_id)->first();

            if (!$upper) {
                break;
            }

            $extra = $upper->userExtra;

            if ($user->position == 1) {
                $extra->free_left = $extra->free_left+1;
            } else {
                $extra->free_right =$extra->free_right+1 ;
            }

            $extra->save();
            $user = $upper;
        }
    }

    /**
     * Check the time for giving the matching bonus
     *
     * @return boolean
     */
    public function checkTime()
    {
        $general = $this->setting;
        $times   = [
            'H' => 'daily',
            'D' => 'weekly',
            'd' => 'monthly',
        ];

        foreach ($times as $timeKey => $time) {

            if ($general->matching_bonus_time == $time) {
                $day = Date($timeKey);

                if (strtolower($day) != $general->matching_when) {
                    return false;
                }
            }
        }

        if (now()->toDateString() == now()->parse($general->last_paid)->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Update the user BV after getting bonus
     *
     * @param object $general
     * @param object $uex
     * @param integer $paidBv
     * @param float $weak
     * @param float $bonus
     * @return void
     */
    public function updateUserBv($uex, $paidBv, $weak, $bonus)
    {
        $general = $this->setting;
        $user    = $uex->user;

        //cut paid bv from both
        if ($general->cary_flash == 0) {
            $uex->bv_left -= $paidBv;
            $uex->bv_right -= $paidBv;
            $lostl = 0;
            $lostr = 0;
            
            Log::channel('bv')->info('Cut paid BV from both sides', [
                'user_id' => $user->id,
                'bv_left' => $uex->bv_left,
                'bv_right' => $uex->bv_right,
                'action' => 'cut_paid_bv'
            ]);
        }

        //cut only weaker bv from both
        if ($general->cary_flash == 1) {
            $uex->bv_left -= $weak;
            $uex->bv_right -= $weak;
            $lostl = $weak - $paidBv;
            $lostr = $weak - $paidBv;
            
            Log::channel('bv')->info('Cut only weaker BV from both sides', [
                'user_id' => $user->id,
                'bv_left' => $uex->bv_left,
                'bv_right' => $uex->bv_right,
                'action' => 'cut_weaker_bv'
            ]);
        }

        //cut all bv from both
        if ($general->cary_flash == 2) {
            $uex->bv_left  = 0;
            $uex->bv_right = 0;
            $lostl         = $uex->bv_left - $paidBv;
            $lostr         = $uex->bv_right - $paidBv;
            
            Log::channel('bv')->info('Cut all BV from both sides', [
                'user_id' => $user->id,
                'bv_left' => $uex->bv_left,
                'bv_right' => $uex->bv_right,
                'action' => 'cut_all_bv'
            ]);
        }

        $uex->save();
        $bvLog = null;
        if ($paidBv != 0) {
            $bvLog[] = [
                'user_id'  => $user->id,
                'position' => 1,
                'amount'   => $paidBv,
                'trx_type' => '-',
                'details'  => 'Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
            $bvLog[] = [
                'user_id'  => $user->id,
                'position' => 2,
                'amount'   => $paidBv,
                'trx_type' => '-',
                'details'  => 'Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
            
            Log::channel('bv')->info('Logging paid BV transactions', [
                'user_id' => $user->id,
                'amount' => $paidBv,
                'bonus' => $bonus
            ]);
        }

        if ($lostl != 0) {
            $bvLog[] = [
                'user_id'  => $user->id,
                'position' => 1,
                'amount'   => $lostl,
                'trx_type' => '-',
                'details'  => 'Flush ' . showAmount($lostl) . ' BV after Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
            
            Log::channel('bv')->info('Logging left side BV flush', [
                'user_id' => $user->id,
                'lost_bv' => $lostl
            ]);
        }

        if ($lostr != 0) {
            $bvLog[] = [
                'user_id'  => $user->id,
                'position' => 2,
                'amount'   => $lostr,
                'trx_type' => '-',
                'details'  => 'Flush ' . showAmount($lostr) . ' BV after Paid ' . showAmount($bonus) . ' ' . __($general->cur_text) . ' For ' . showAmount($paidBv) . ' BV.',
            ];
            
            Log::channel('bv')->info('Logging right side BV flush', [
                'user_id' => $user->id,
                'lost_bv' => $lostr
            ]);
        }

        if ($bvLog) {
            BvLog::insert($bvLog);
            Log::channel('bv')->info('Inserted BV log entries', [
                'entries' => $bvLog
            ]);
        }
    }

    /**
     * Get the under position user
     *
     * @param integer $id
     * @param integer $position
     * @return object
     */

    protected function getPositionUser($id, $position)
    {
        return User::where('pos_id', $id)->where('position', $position)->with('referrer', 'plan', 'userExtra')->first();
    }

    /**
     * Get the under position user
     *
     * @param object $user
     * @return array
     */
    
    /*public function showTreePage($user, $isAdmin = false)
    {
        if (!$isAdmin) {
            if ($user->username != @auth()->user()->username) {
                $this->checkMyTree($user);
            }
        }
        $hands      = array_fill_keys($this->getHands(), null);
        $hands['a'] = $user;
        $hands['b'] = $this->getPositionUser($user->id, 1);
        if ($hands['b']) {
            $hands['d'] = $this->getPositionUser($hands['b']->id, 1);
            $hands['e'] = $this->getPositionUser($hands['b']->id, 2);
        }

        if ($hands['d']) {
            $hands['h'] = $this->getPositionUser($hands['d']->id, 1);
            $hands['i'] = $this->getPositionUser($hands['d']->id, 2);
        }

        if ($hands['e']) {
            $hands['j'] = $this->getPositionUser($hands['e']->id, 1);
            $hands['k'] = $this->getPositionUser($hands['e']->id, 2);
        }

        $hands['c'] = $this->getPositionUser($user->id, 2);
        if ($hands['c']) {
            $hands['f'] = $this->getPositionUser($hands['c']->id, 1);
            $hands['g'] = $this->getPositionUser($hands['c']->id, 2);
        }

        if ($hands['f']) {
            $hands['l'] = $this->getPositionUser($hands['f']->id, 1);
            $hands['m'] = $this->getPositionUser($hands['f']->id, 2);
        }

        if ($hands['g']) {
            $hands['n'] = $this->getPositionUser($hands['g']->id, 1);
            $hands['o'] = $this->getPositionUser($hands['g']->id, 2);
        }

        return $hands;
    }*/
    
    
    public function showTreePage($user)
    {
        $children = [
            'left' => $this->getPositionUser($user->id, 1),
            'right' => $this->getPositionUser($user->id, 2),
        ];
    
        return $children;
    }
    
    /**
     * Get single user in tree
     *
     * @param object $user
     * @return string
     */
    public function showSingleUserinTree($user, $isAdmin = false)
    {
        $html = '';
        if ($user) {
            if ($user->plan_id == 0) {
                $userType = "free-user";
                $stShow   = "Free";
                $planName = '';
            } else {
                $userType = "paid-user";
                $stShow   = "Paid";
                $planName = @$user->plan->name;
            }
    
            $img   = getImage(getFilePath('userProfile') . '/' . $user->image);
            $refby = @$user->referrer->fullname ?? '';
    
            if ($isAdmin) {
                $hisTree = route('admin.users.binary.tree', $user->username);
            } else {
                $hisTree = route('user.binary.tree', $user->username);
            }
    
            $extraData = " data-name=\"$user->username\"";
            $extraData .= " data-treeurl=\"$hisTree\"";
            $extraData .= " data-status=\"$stShow\"";
            $extraData .= " data-plan=\"$planName\"";
            $extraData .= " data-image=\"$img\"";
            $extraData .= " data-refby=\"$refby\"";
            $extraData .= " data-lpaid=\"" . @$user->userExtra->paid_left . "\"";
            $extraData .= " data-rpaid=\"" . @$user->userExtra->paid_right . "\"";
            $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
            $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
            $extraData .= " data-lbv=\"" . showAmount(@$user->userExtra->bv_left) . "\"";
            $extraData .= " data-rbv=\"" . showAmount(@$user->userExtra->bv_right) . "\"";
    
            $html .= "<div class=\"user showDetails\" type=\"button\" $extraData>";
            $html .= "<img src=\"$img\" alt=\"*\"  class=\"$userType\">";
            $html .= "<p class=\"user-name\" data-username=\"$user->username\">$user->username</p>";
        } else {
            $img = getImage('assets/images/nouser.png');
    
            $html .= "<div class=\"user\" type=\"button\">";
            $html .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
            $html .= "<p class=\"user-name\">No User</p>";
        }
    
        // Moved outside the if-else structure to ensure it is always added
        $html .= " </div>";
        $html .= " <span class=\"line\"></span>";
    
        return $html;
    }


    /**
     * Get the mlm hands for tree
     *
     * @return array
     */
    public function getHands()
    {
        return ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'aa', 'ab', 'ac', 'ad', 'ae', 'af'];
    }

    /**
     * Check the user is in my tree or not
     *
     * @param object $user
     * @return bool
     */
    protected function checkMyTree($user)
    {
        $topUser = User::where('id', $user->pos_id)->first(['id', 'pos_id']);
        if (!$topUser) {
            abort(401);
        }

        if ($topUser->id == auth()->user()->id) {
            return true;
        }

        $this->checkMyTree($topUser);
    }

    /**
     * Plan subscribe logic
     *
     * @return void
     */
    public function purchasePlan()
    {
        $user = $this->user;
        $plan = $this->plan;
        $trx  = $this->trx;

        $oldPlan       = $user->plan_id;
        $user->plan_id = $plan->id;
        $user->balance -= $plan->price;
        $user->total_invest += $plan->price;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $plan->price;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Purchased ' . $plan->name;
        $transaction->remark       = 'purchased_plan';
        $transaction->trx          = $trx;
        $transaction->post_balance = $user->balance;
        $transaction->save();

        notify($user, 'PLAN_PURCHASED', [
            'plan_name'    => $plan->name,
            'price'        => showAmount($plan->price),
            'trx'          => $transaction->trx,
            'post_balance' => showAmount($user->balance),
        ]);

        if ($oldPlan == 0) {
            $this->updatePaidCount($user->id);
        }

        if ($plan->bv) {
            $this->updateBV();
        }

        if ($plan->tree_com > 0) {
            $this->treeCommission();
        }

        $this->referralCommission();
    }
    
    public function getChildren($parentId)
    {
        // Fetch immediate children of the parent ID
        $children = User::where('pos_id', $parentId)->get();
        return $children;
    }

}
