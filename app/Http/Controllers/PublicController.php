<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function experience(): View
    {
        return view('pages.experience');
    }

    public function projects(): View
    {
        return view('pages.projects');
    }

    public function projectShow(string $slug): View
    {
        return view('pages.project-show', compact('slug'));
    }

    public function social(): View
    {
        return view('pages.social');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }
}
