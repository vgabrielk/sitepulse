<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Client;
use App\Repositories\SiteRepository;
use App\DTOs\SiteDTO;
use Illuminate\Database\Eloquent\Collection;

class SiteService
{
    public function __construct(
        private SiteRepository $siteRepository
    ) {}

    public function createSite(Client $client, array $data): SiteDTO
    {
        $data['client_id'] = $client->id;
        $data['widget_id'] = $this->siteRepository->generateWidgetId();
        $data['widget_config'] = $this->getDefaultWidgetConfig();
        $data['tracking_config'] = $this->getDefaultTrackingConfig();
        
        $site = $this->siteRepository->create($data);
        
        return SiteDTO::fromModel($site);
    }

    public function getSiteById(int $id): ?SiteDTO
    {
        $site = $this->siteRepository->findById($id);
        
        return $site ? SiteDTO::fromModel($site) : null;
    }

    public function getSiteByWidgetId(string $widgetId): ?SiteDTO
    {
        $site = $this->siteRepository->findByWidgetId($widgetId);
        
        return $site ? SiteDTO::fromModel($site) : null;
    }

    public function getSitesByClient(Client $client): array
    {
        $sites = $this->siteRepository->getByClientId($client->id);
        
        return $sites->map(fn($site) => SiteDTO::fromModel($site))->toArray();
    }

    public function getActiveSitesByClient(Client $client): array
    {
        $sites = $this->siteRepository->getActiveByClientId($client->id);
        
        return $sites->map(fn($site) => SiteDTO::fromModel($site))->toArray();
    }

    public function updateSite(Site $site, array $data): SiteDTO
    {
        $this->siteRepository->update($site, $data);
        
        return SiteDTO::fromModel($site->fresh());
    }

    public function deleteSite(Site $site): bool
    {
        return $this->siteRepository->delete($site);
    }

    public function activateSite(Site $site): SiteDTO
    {
        $this->siteRepository->activate($site);
        
        return SiteDTO::fromModel($site->fresh());
    }

    public function deactivateSite(Site $site): SiteDTO
    {
        $this->siteRepository->deactivate($site);
        
        return SiteDTO::fromModel($site->fresh());
    }

    public function updateWidgetConfig(Site $site, array $config): SiteDTO
    {
        $this->siteRepository->updateWidgetConfig($site, $config);
        
        return SiteDTO::fromModel($site->fresh());
    }

    public function updateTrackingConfig(Site $site, array $config): SiteDTO
    {
        $this->siteRepository->updateTrackingConfig($site, $config);
        
        return SiteDTO::fromModel($site->fresh());
    }

    public function getSiteStats(Site $site): array
    {
        return $this->siteRepository->getSiteStats($site);
    }

    public function getSiteMetrics(Site $site, string $startDate, string $endDate): array
    {
        return $this->siteRepository->getSiteMetrics($site, $startDate, $endDate);
    }

    public function getWidgetEmbedCode(Site $site): string
    {
        return $site->getWidgetEmbedCode();
    }

    public function validateDomain(string $domain): bool
    {
        return filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }

    public function checkSiteLimit(Client $client): bool
    {
        $currentSites = $this->siteRepository->getByClientId($client->id)->count();
        $planLimits = $client->getPlanLimits();
        
        $siteLimit = $planLimits['sites'] ?? 1;
        
        if ($siteLimit === -1) {
            return true; // Unlimited
        }
        
        return $currentSites < $siteLimit;
    }

    private function getDefaultWidgetConfig(): array
    {
        return [
            'position' => 'bottom-right',
            'theme' => 'light',
            'colors' => [
                'primary' => '#007bff',
                'secondary' => '#6c757d',
                'background' => '#ffffff',
                'text' => '#333333',
            ],
            'show_counter' => true,
            'show_feedback' => true,
            'show_surveys' => true,
            'animation' => 'slide',
            'size' => 'medium',
        ];
    }

    private function getDefaultTrackingConfig(): array
    {
        return [
            'track_pageviews' => true,
            'track_events' => true,
            'track_scroll' => true,
            'track_clicks' => true,
            'track_forms' => true,
            'track_sessions' => true,
            'anonymize_ips' => true,
            'respect_do_not_track' => true,
            'cookie_consent' => true,
        ];
    }
}
