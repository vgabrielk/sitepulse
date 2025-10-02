<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public function index(Site $site)
    {
        $this->authorizeSite($site);
        $faqs = Faq::where('site_id', $site->id)->orderBy('position')->get();
        $faqCustomization = $site->faq_customization ?? $this->defaultFaqCustomization();
        return view('dashboard.faq.index', compact('site', 'faqs', 'faqCustomization'));
    }

    public function store(Request $request, Site $site)
    {
        $this->authorizeSite($site);
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'nullable|string',
            'published' => 'sometimes|boolean',
        ]);
        $data['site_id'] = $site->id;
        $data['published'] = $request->boolean('published', true);
        $data['position'] = Faq::where('site_id', $site->id)->max('position') + 1;
        Faq::create($data);
        return back()->with('success', 'FAQ criada com sucesso.');
    }

    public function update(Request $request, Site $site, Faq $faq)
    {
        $this->authorizeSite($site);
        abort_if($faq->site_id !== $site->id, 403);
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'nullable|string',
            'published' => 'sometimes|boolean',
        ]);
        $faq->update([
            'question' => $data['question'],
            'answer' => $data['answer'] ?? null,
            'published' => $request->boolean('published', true),
        ]);
        return back()->with('success', 'FAQ atualizada.');
    }

    public function saveCustomization(Request $request, Site $site)
    {
        $this->authorizeSite($site);
        $data = $request->validate([
            'colors.card_bg' => 'required|string',
            'colors.card_border' => 'required|string',
            'colors.title' => 'required|string',
            'colors.question' => 'required|string',
            'colors.answer' => 'required|string',
            'colors.divider' => 'required|string',
            'colors.icon' => 'required|string',
            'colors.note_bg' => 'required|string',
            'colors.note_border' => 'required|string',
            'colors.note_text' => 'required|string',
            'layout.border_radius' => 'required|string',
            'layout.padding' => 'required|string',
            'effects.box_shadow' => 'required|string',
            'typography.font_family' => 'required|string',
            'typography.font_size' => 'required|string',
            'typography.font_weight' => 'required|string',
        ]);
        // Persist only the validated payload (colors/layout/effects/typography)
        $site->faq_customization = $data;
        $site->save();
        return back()->with('success', 'Personalização de FAQ salva.');
    }

    protected function defaultFaqCustomization(): array
    {
        return [
            'colors' => [
                'card_bg' => '#ffffff',
                'card_border' => '#eef2f7',
                'title' => '#0f172a',
                'question' => '#0f172a',
                'answer' => '#334155',
                'divider' => '#e5e7eb',
                'icon' => '#94a3b8',
                'note_bg' => '#eff6ff',
                'note_border' => '#bfdbfe',
                'note_text' => '#334155',
            ],
            'layout' => [
                'border_radius' => '16px',
                'padding' => '24px',
            ],
            'typography' => [
                'font_family' => 'inherit',
                'font_size' => '14px',
                'font_weight' => '600',
            ],
            'effects' => [
                'box_shadow' => '0 10px 30px rgba(15,23,42,0.06)'
            ],
        ];
    }

    public function destroy(Site $site, Faq $faq)
    {
        $this->authorizeSite($site);
        abort_if($faq->site_id !== $site->id, 403);
        $faq->delete();
        return back()->with('success', 'FAQ removida.');
    }

    protected function authorizeSite(Site $site): void
    {
        $user = Auth::user();
        abort_if(!$user || !$user->client || $site->client_id !== $user->client->id, 403);
    }
}



