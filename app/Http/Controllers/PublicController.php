<?php

namespace App\Http\Controllers;

use App\Enums\ExperienceKind;
use App\Enums\LinkGroup;
use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Link;
use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $featuredProjects = Project::published()->featured()->ordered()->take(3)->get();
        $recentProjects = Project::published()->where('is_featured', false)->orderByDesc('published_at')->take(3)->get();

        $socialLinks = Link::published()->where('group', LinkGroup::Akun)->pluck('url')->toArray();

        return view('pages.home', compact('featuredProjects', 'recentProjects', 'socialLinks'));
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
        $accounts = Link::published()
            ->where('group', LinkGroup::Akun)
            ->ordered()
            ->get();

        $channels = Link::published()
            ->where('group', LinkGroup::Saluran)
            ->with(['highlights' => function ($query) {
                $query->published()->orderByDesc('highlighted_at');
            }])
            ->ordered()
            ->get();

        return view('pages.social', compact('accounts', 'channels'));
    }

    public function contact(): View
    {
        $contacts = Link::published()
            ->where('group', LinkGroup::Kontak)
            ->ordered()
            ->get();

        return view('pages.contact', compact('contacts'));
    }

    public function robots(): Response
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: ".route('sitemap')."\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }

    public function sitemap(): Response
    {
        $projects = Project::published()->get();
        $urls = [
            route('home'), route('en.home'),
            route('about'), route('en.about'),
            route('experience'), route('en.experience'),
            route('projects'), route('en.projects'),
            route('social'), route('en.social'),
            route('contact'), route('en.contact'),
        ];

        foreach ($projects as $project) {
            $urls[] = route('projects.show', $project->slug);
            $urls[] = route('en.projects.show', $project->slug);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url><loc>'.htmlspecialchars($url).'</loc></url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
