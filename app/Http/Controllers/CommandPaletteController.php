<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommandPaletteController extends Controller
{
    public function index(Request $request)
    {
        // Cache the result to improve performance, cleared on content change
        $locale = app()->getLocale();
        $cacheKey = "command_palette_{$locale}";

        $data = Cache::remember($cacheKey, now()->addDays(7), function () {
            $items = [];

            // Static pages
            $pages = [
                ['title' => __('ui.home'), 'url' => route(app()->getLocale() === 'en' ? 'en.home' : 'home'), 'icon' => 'home'],
                ['title' => __('ui.about'), 'url' => route(app()->getLocale() === 'en' ? 'en.about' : 'about'), 'icon' => 'user'],
                ['title' => __('ui.experience'), 'url' => route(app()->getLocale() === 'en' ? 'en.experience' : 'experience'), 'icon' => 'briefcase'],
                ['title' => __('ui.projects'), 'url' => route(app()->getLocale() === 'en' ? 'en.projects' : 'projects'), 'icon' => 'folder'],
                ['title' => __('ui.social'), 'url' => route(app()->getLocale() === 'en' ? 'en.social' : 'social'), 'icon' => 'share-2'],
                ['title' => __('ui.contact'), 'url' => route(app()->getLocale() === 'en' ? 'en.contact' : 'contact'), 'icon' => 'mail'],
            ];

            foreach ($pages as $page) {
                $items[] = [
                    'id' => 'page_'.md5($page['url']),
                    'title' => $page['title'],
                    'url' => $page['url'],
                    'category' => __('ui.pages', [], app()->getLocale()),
                    'icon' => $page['icon'],
                ];
            }

            // Projects
            $projects = Project::published()->ordered()->get();
            foreach ($projects as $project) {
                $items[] = [
                    'id' => 'project_'.$project->id,
                    'title' => $project->title,
                    'url' => route(app()->getLocale() === 'en' ? 'en.projects.show' : 'projects.show', $project->slug),
                    'category' => __('ui.projects', [], app()->getLocale()),
                    'icon' => 'file-text',
                ];
            }

            return $items;
        });

        return response()->json($data);
    }
}
