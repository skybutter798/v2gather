<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Ptc extends Model
{
    use GlobalStatus, Searchable;


    public function adsTypeBadge(): Attribute
    {
        return new Attribute(
            function () {
                $html = '';
                if ($this->ads_type == Status::ADS_LINK) {
                    $html = '<span class="badge badge--success"><i class="fa fa-link"></i>' . trans(' URL') . '</span>';
                } elseif ($this->ads_type == Status::ADS_IMAGE) {
                    $html = '<span class="badge badge--dark"><i class="fa fa-image"></i>' . trans(' Image') . '</span>';
                } elseif ($this->ads_type == Status::ADS_SCRIPT) {
                    $html = '<span class="badge badge--primary"><i class="fa fa-code"></i>' . trans(' Script')  . '</span>';
                }
                return $html;
            }
        );
    }
}