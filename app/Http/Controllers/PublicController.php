<?php

namespace App\Http\Controllers;

use App\Enums\ExperienceKind;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $featuredProjects = Project::published()->featured()->ordered()->take(3)->get();
        $recentProjects = Project::published()->where('is_featured', false)->ordered()->take(3)->get();

        return view('pages.home', compact('featuredProjects', 'recentProjects'));
    }

    public function about(): View
    {
        $education = Experience::published()->where('kind', ExperienceKind::Pendidikan)->ordered()->get();

        return view('pages.about', compact('education'));
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
