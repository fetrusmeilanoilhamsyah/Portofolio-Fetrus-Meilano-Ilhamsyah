<?php

namespace App\Http\Controllers;

use App\Enums\ExperienceKind;
use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $featuredProjects = Project::published()->featured()->ordered()->take(3)->get();
        $recentProjects = Project::published()->where('is_featured', false)->orderByDesc('published_at')->take(3)->get();

        return view('pages.home', compact('featuredProjects', 'recentProjects'));
    }

    public function about(): View
    {
        $education = Experience::published()->where('kind', ExperienceKind::Pendidikan)->ordered()->get();

        return view('pages.about', compact('education'));
    }

    public function experience(): View
    {
        $experiences = Experience::published()
            ->orderByDesc('started_at')
            ->get()
            ->groupBy(fn ($exp) => $exp->kind->value ?? $exp->kind);

        $certificates = Certificate::published()
            ->ordered()
            ->orderByDesc('issued_at')
            ->get();

        return view('pages.experience', compact('experiences', 'certificates'));
    }

    public function projects(): View
    {
        $projects = Project::published()
            ->ordered()
            ->orderByDesc('published_at')
            ->get();

        return view('pages.projects', compact('projects'));
    }

    public function projectShow(string $slug): View
    {
        $project = Project::published()->where('slug', $slug)->firstOrFail();

        $allSlugs = Project::published()
            ->ordered()
            ->orderByDesc('published_at')
            ->pluck('slug')
            ->toArray();

        $currentIndex = array_search($slug, $allSlugs);

        $prevSlug = $currentIndex > 0 ? $allSlugs[$currentIndex - 1] : null;
        $nextSlug = $currentIndex !== false && $currentIndex < count($allSlugs) - 1 ? $allSlugs[$currentIndex + 1] : null;

        $prevProject = $prevSlug ? Project::published()->where('slug', $prevSlug)->first() : null;
        $nextProject = $nextSlug ? Project::published()->where('slug', $nextSlug)->first() : null;

        return view('pages.project-show', compact('project', 'nextProject', 'prevProject'));
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
