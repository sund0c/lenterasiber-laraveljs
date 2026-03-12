<?php
// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TotpService;
use App\Services\QrCodeService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TotpService::class);
        $this->app->singleton(QrCodeService::class);
    }

    public function boot(): void
    {
        // Blade directive: @nonce
        \Illuminate\Support\Facades\Blade::directive('nonce', function () {
            return '<?php echo app()->bound("csp_nonce") ? "nonce=\"".app("csp_nonce")."\"" : ""; ?>';
        });
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
