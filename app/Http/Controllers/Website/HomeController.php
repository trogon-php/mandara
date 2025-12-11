<?php

namespace App\Http\Controllers\Website;

class HomeController extends WebsiteBaseController
{
    /**
     * Show the website home page.
     */
    public function index()
    {
        // SEO setup
        $this->setSeoTitle('Home');
        $this->setSeoDescription('Welcome to ' . config('app.name') . ', your platform for learning and growth.');
        $this->setCanonical(route('web.home'));
        $this->setOg('website', asset('images/og-home.jpg'));

        return view('website.home');
    }
}
