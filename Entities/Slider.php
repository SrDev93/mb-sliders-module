<?php

namespace Modules\Sliders\Entities;

use App\Models\Brand;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;

class Slider extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Sliders\Database\factories\SliderFactory::new();
    }

    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'lang', 'lang');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($slider){
            if ($slider->background){
                File::delete($slider->background);
            }

            if ($slider->image){
                File::delete($slider->image);
            }
        });
    }
}
