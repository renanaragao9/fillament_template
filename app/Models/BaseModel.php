<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class BaseModel extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->causer_id = Auth::guard('sanctum')->id();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::preventLazyLoading(! app()->isProduction());

        static::handleLazyLoadingViolationUsing(function ($model, $relation) {
            $message = "Lazy load [{$relation}] on model [".get_class($model).'].';
            info($message);
            throw new \Exception($message);
        });
    }
}
