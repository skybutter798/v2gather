<?php

namespace App\Constants;

class Status
{

    const ENABLE  = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO  = 0;

    const VERIFIED   = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_REJECT   = 3;

    const TICKET_OPEN   = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY  = 2;
    const TICKET_CLOSE  = 3;

    const PRIORITY_LOW    = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH   = 3;

    const USER_ACTIVE = 1;
    const USER_BAN    = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING    = 2;
    const KYC_VERIFIED   = 1;

    const ADS_LINK   = 1;
    const ADS_IMAGE  = 2;
    const ADS_SCRIPT = 3;

    const BV_LEFT  = 1;
    const BV_RIGHT = 2;

    const DISPATCH_COMMISSION_LOWER_PLAN = 1;
    const DISPATCH_COMMISSION_CHILD_PLAN = 2;
    const DISPATCH_COMMISSION_SELF_PLAN  = 3;


    const PROFILE_INCOMPLETE = 0;
    const PROFILE_COMPLETE = 1;
}