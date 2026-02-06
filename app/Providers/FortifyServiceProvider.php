<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Http\Responses\RegisterResponse;
use App\Models\Category;
use App\Models\User;
use App\Services\SecurityService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureAuthentication();
        $this->configureViews();
        $this->configureRateLimiting();
        $this->configureLoginTracking();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn (Request $request) => Inertia::render('auth/login', [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'canRegister' => Features::enabled(Features::registration()),
            'status' => $request->session()->get('status'),
        ]));

        Fortify::resetPasswordView(fn (Request $request) => Inertia::render('auth/reset-password', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]));

        Fortify::requestPasswordResetLinkView(fn (Request $request) => Inertia::render('auth/forgot-password', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::verifyEmailView(fn (Request $request) => Inertia::render('auth/verify-email', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::registerView(fn () => Inertia::render('auth/register', [
            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('order')
                ->get(['id', 'name'])
                ->values(),
        ]));

        Fortify::twoFactorChallengeView(fn () => Inertia::render('auth/two-factor-challenge'));

        Fortify::confirmPasswordView(fn () => Inertia::render('auth/confirm-password'));
    }

    /**
     * Configure Fortify authentication.
     */
    private function configureAuthentication(): void
    {
        // Authentication logic moved to configureLoginTracking to include security tracking
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }

    /**
     * Login sonrası tracking ve güvenlik kontrolleri
     */
    private function configureLoginTracking(): void
    {
        // Login başarılı olduğunda tracking yap
        Fortify::authenticateUsing(function (Request $request): ?User {
            $user = User::query()
                ->where('email', $request->email)
                ->first();

            if (! $user || ! Hash::check((string) $request->password, $user->password)) {
                return null;
            }

            // Vendor kontrolü (sadece vendor ise)
            if ($user->isVendor()) {
                // Email verification kontrolü
                if (!$user->email_verified_at) {
                    throw ValidationException::withMessages([
                        Fortify::username() => 'Giriş yapmak için e-postanızı doğrulamanız gerekmektedir. Lütfen e-posta kutunuzu kontrol edin.',
                    ]);
                }

                // Vendor status kontrolü
                if (!$user->vendor || $user->vendor->status !== 'active') {
                    $statusMessage = 'Başvurunuz inceleniyor. Onay sonrası giriş yapabilirsiniz.';
                    
                    if ($user->vendor && $user->vendor->status === 'rejected') {
                        $statusMessage = 'Başvurunuz reddedilmiştir. Detaylar için lütfen bizimle iletişime geçin.';
                    }
                    
                    throw ValidationException::withMessages([
                        Fortify::username() => $statusMessage,
                    ]);
                }
            }

            // Login tracking ve güvenlik kontrolleri (login başarılı olduktan sonra)
            // Bu işlem login'i engellemez, sadece kayıt tutar
            try {
                $securityService = app(SecurityService::class);
                $securityService->recordLogin($user, $request);
            } catch (\Exception $e) {
                // Tracking hatası login'i engellemez, sadece log'a yaz
                \Log::error('Login tracking failed: ' . $e->getMessage());
            }

            return $user;
        });
    }
}
