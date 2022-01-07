<?php

namespace App\Models;

class Quotation extends BaseModel
{
    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function getQuoteIdAttribute($value)
    {
        return sprintf('#QU-%04d', $value);
    }

    public function quotationItem()
    {
        return $this->hasMany(QuotationItem::class,'quotation_id');
    }

    public function Customer()
    {
        return $this->belongsTo(User::class,'customer_id');
    }
}
