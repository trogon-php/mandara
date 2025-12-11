<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\Core\SeoService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

abstract class WebsiteBaseController extends Controller
{
    protected string $guard = 'web';
    protected SeoService $seo;

    protected array $siteSettings = [];
    protected array $primaryNav   = [];

    public function __construct()
    {
        // Init SEO defaults
        $this->seo = app(SeoService::class)
            ->title(config('app.name'))
            ->canonical(url()->current());

        // Default shared data (replace with DB fetch if needed)
        $this->siteSettings = [
            'brand'       => config('app.name'),
            'logo_url'    => asset('images/logo.svg'),
            'footer_html' => '© ' . date('Y') . ' ' . config('app.name'),
        ];

        $this->primaryNav = [
            ['label' => 'Home',    'url' => route('web.home')],
            ['label' => 'Courses', 'url' => route('web.courses')],
            ['label' => 'About',   'url' => route('web.about')],
            ['label' => 'Contact', 'url' => route('web.contact')],
        ];

        $this->secondaryNav = [
            ['label' => 'Login', 'url' => route('login')],
            ['label' => 'Register', 'url' => route('register')],
        ];

        $this->footerNav = [
            ['label' => 'Privacy Policy', 'url' => route('web.privacy-policy')],
            ['label' => 'Terms of Service', 'url' => route('web.terms-of-service')],
            ['label' => 'Contact Us', 'url' => route('web.contact')],
        ];

        View::share([
            'seo'          => $this->seo->get(),
            'websiteUser'  => $this->user(), // nullable, no auth enforced
            'siteSettings' => $this->siteSettings,
            'primaryNav'   => $this->primaryNav,
            'secondaryNav' => $this->secondaryNav,
            'footerNav'    => $this->footerNav,
        ]);
    }

    /* -------------------- Auth (read-only) -------------------- */
    protected function user(): ?Authenticatable
    {
        return Auth::guard($this->guard)->user();
    }

    /* ------------------------ SEO helpers --------------------- */
    protected function setSeoTitle(string $title, bool $withSuffix = true): void
    {
        $suffix = $withSuffix ? ' | ' . config('app.name') : '';
        $this->seo->title($title, $suffix);
        View::share('seo', $this->seo->get());
    }

    protected function setSeoDescription(?string $description): void
    {
        $this->seo->description($description);
        View::share('seo', $this->seo->get());
    }

    protected function setSeoKeywords(?string $keywords): void
    {
        $this->seo->set(['keywords' => $keywords]);
        View::share('seo', $this->seo->get());
    }

    protected function setCanonical(?string $url): void
    {
        $this->seo->canonical($url);
        View::share('seo', $this->seo->get());
    }

    protected function setOg(string $type = 'website', ?string $image = null): void
    {
        $this->seo->og($type, $image);
        View::share('seo', $this->seo->get());
    }

    protected function setTwitter(string $card = 'summary_large_image'): void
    {
        $this->seo->twitter($card);
        View::share('seo', $this->seo->get());
    }

    protected function setHreflangs(array $map): void
    {
        $this->seo->hreflangs($map);
        View::share('seo', $this->seo->get());
    }

    protected function addMeta(string $name, string $content): void
    {
        $this->seo->addMeta($name, $content);
        View::share('seo', $this->seo->get());
    }

    protected function addSchema(array $jsonLd): void
    {
        $this->seo->addSchema($jsonLd);
        View::share('seo', $this->seo->get());
    }

    protected function noindex(): void
    {
        $this->seo->robots('noindex,follow');
        View::share('seo', $this->seo->get());
    }

    /* ------------------ View-sharing helpers ------------------ */
    protected function shareSiteSettings(array $settings): void
    {
        $this->siteSettings = array_replace($this->siteSettings, $settings);
        View::share('siteSettings', $this->siteSettings);
    }

    protected function sharePrimaryNav(array $items): void
    {
        $this->primaryNav = $items;
        View::share('websiteNav', $this->primaryNav);
    }

    /* -------------------- JSON helpers (final) ---------------- */
    protected function jsonSuccess(array $data = [], int $code = 200)
    {
        return response()->json(['status' => 'success'] + $data, $code);
    }

    protected function jsonError(string $message, int $code = 422, array $extra = [])
    {
        return response()->json(['status' => 'error', 'message' => $message] + $extra, $code);
    }
}
