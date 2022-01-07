<?php

namespace App\Models;

class QuotationItem extends BaseModel
{
    protected $appends = ['images'];

    public function getImagesAttribute()
    {
        return QuotationItemImage::select('*')->where('quotation_item_id',$this->id)->get()->pluck('image');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class,'quotation_id');
    }
}