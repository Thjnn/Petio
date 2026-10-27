<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Facades\Socialite;

/**
 * Đăng nhập / đăng ký bằng Google và Facebook.
 * Lần đầu: tạo tài khoản mới (hoặc liên kết với tài khoản cùng email). Các lần sau: đăng nhập luôn.
 */
class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google' => 'Google', 'facebook' => 'Facebook'];

    public function redirect(string $provider)
    {
        $this->ensureConfigured($provider);

        $driver = Socialite::driver($provider);
        if ($provider === 'facebook') {
            $driver->scopes(['email']);
        }

        return $driver->redirect();
    }

    public function callback(Request $request, string $provider)
    {
        $this->ensureConfigured($provider);
        $label = self::PROVIDERS[$provider];

        // Người dùng bấm "Hủy" trên trang Google/Facebook
        if ($request->has('error') || $request->has('error_code')) {
            return redirect()->route('login')->withErrors(['login' => "Bạn đã hủy đăng nhập bằng {$label}."]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            Log::warning("Social login {$provider} failed: ".$e->getMessage());

            return redirect()->route('login')->withErrors(['login' => "Không thể xác thực qua {$label}. Vui lòng thử lại."]);
        }

        $user = DB::transaction(fn () => $this->findOrCreateUser($provider, $socialUser));

        if (! $user->isActive()) {
            return redirect()->route('login')->withErrors(['login' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.']);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->to($user->can('admin') ? route('admin.dashboard') : route('customer.dashboard'))
            ->with('success', 'Chào mừng '.$user->displayName().' đến với Petio!');
    }

    private function findOrCreateUser(string $provider, SocialUser $socialUser): User
    {
        $tokens = [
            'provider_token' => $socialUser->token ?? null,
            'provider_refresh_token' => $socialUser->refreshToken ?? null,
        ];

        $account = SocialAccount::where('provider', $provider)
            ->where('provider_id', (string) $socialUser->getId())
            ->first();

        if ($account) {
            $account->update($tokens);

            return $account->user;
        }

        $email = $socialUser->getEmail() ? Str::lower($socialUser->getEmail()) : null;
        $user = $email ? User::where('email', $email)->first() : null;

        if (! $user) {
            $user = User::create([
                'username' => $this->uniqueUsername($socialUser, $email),
                'email' => $email,
                'full_name' => $socialUser->getName() ? Str::limit($socialUser->getName(), 100, '') : null,
                'password' => Str::random(40), // cast 'hashed' sẽ tự băm
                'status' => 'active', // create() không nạp giá trị mặc định từ DB
            ]);
        }

        if (! $user->avatar_url && $socialUser->getAvatar()) {
            $user->forceFill(['avatar_url' => Str::limit($socialUser->getAvatar(), 255, '')])->save();
        }

        $user->socialAccounts()->create(['provider' => $provider, 'provider_id' => (string) $socialUser->getId()] + $tokens);

        return $user;
    }

    /** Username theo đúng định dạng của form đăng ký: chữ không dấu, số, "." hoặc "_", 3–50 ký tự. */
    private function uniqueUsername(SocialUser $socialUser, ?string $email): string
    {
        $source = $socialUser->getNickname() ?: ($email ? Str::before($email, '@') : $socialUser->getName());
        $base = Str::limit(Str::slug((string) $source, '_'), 40, '');
        if (strlen($base) < 3) {
            $base = 'user_'.Str::lower(Str::random(6));
        }

        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base.$i++;
        }

        return $username;
    }

    private function ensureConfigured(string $provider): void
    {
        abort_unless(isset(self::PROVIDERS[$provider]), 404);

        if (! config("services.{$provider}.client_id") || ! config("services.{$provider}.client_secret")) {
            throw new HttpResponseException(redirect()->route('login')->withErrors([
                'login' => 'Đăng nhập bằng '.self::PROVIDERS[$provider].' hiện chưa được cấu hình.',
            ]));
        }
    }
}
