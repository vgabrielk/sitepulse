<?php

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\View;

class WidgetService
{
    public function generateWidgetScript(Site $site): string
    {
        return View::make('widget.script', compact('site'))->render();
    }
    
    public function getWidgetConfig(Site $site): array
    {
        return [
            'site_id' => $site->id,
            'widget_id' => $site->widget_id,
            'is_active' => $site->is_active,
            'track_events' => $site->track_events,
            'collect_feedback' => $site->collect_feedback,
            'anonymize_ips' => $site->anonymize_ips,
            'widget_config' => $site->widget_config ?? [],
            'tracking_config' => $site->tracking_config ?? [],
        ];
    }
    
    public function getWidgetEmbedCode(Site $site): string
    {
        $scriptUrl = url("/widget/{$site->widget_id}.js");
        
        return "<!-- SitePulse Widget -->
<script async src=\"{$scriptUrl}\"></script>
<!-- End SitePulse Widget -->";
    }
}