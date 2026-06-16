<?php
namespace App\Http\Middleware;
use App\Enums\Admin\AdminType;
use Closure;
use Illuminate\Http\Request;
class EnsureOwner {
    public function handle(Request $request, Closure $next): mixed {
        if (! static::check()) {
            return redirect()->back()->with('error', trans('dashboard/general.unauthorized'));
        }
        return $next($request);
    }

    public static function check(): bool {
        $admin = auth('admin')->user();
        return $admin && $admin->type === AdminType::OWNER && is_null($admin->company_id);
    }
}