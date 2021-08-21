<?php

declare(strict_types=1);

namespace Dries\GitHubSponsors\Concerns;

use Dries\GitHubSponsors\GitHubSponsors;
use Illuminate\Http\Client\Factory;

trait Sponsorable
{
    public function isSponsoredBy(string $sponsor): bool
    {
        if ($this->canViewAsGitHubUser()) {
            return $this->sponsorsClient()->isViewerSponsoredBy($sponsor);
        }

        return $this->sponsorsClient()->isSponsoredBy(
            $this->gitHubUsername(), $sponsor, $this->isGitHubOrganization()
        );
    }

    public function isSponsoredByOrganization(string $sponsor): bool
    {
        if ($this->canViewAsGitHubUser()) {
            return $this->sponsorsClient()->isViewerSponsoredByOrganization($sponsor);
        }

        return $this->sponsorsClient()->isSponsoredBy(
            $this->gitHubUsername(), $sponsor, $this->isGitHubOrganization()
        );
    }

    public function isSponsoring(string $account): bool
    {
        if ($this->canViewAsGitHubUser()) {
            return $this->sponsorsClient()->isViewerSponsoring($account);
        }

        return $this->sponsorsClient()->isSponsoredBy(
            $account, $this->gitHubUsername()
        );
    }

    public function isSponsoringOrganization(string $account): bool
    {
        if ($this->canViewAsGitHubUser()) {
            return $this->sponsorsClient()->isViewerSponsoringOrganization($account);
        }

        return $this->sponsorsClient()->isOrganizationSponsoredBy(
            $account, $this->gitHubUsername()
        );
    }

    public function isGitHubOrganization(): bool
    {
        return false;
    }

    public function gitHubUsername(): string
    {
        return $this->github;
    }

    public function gitHubToken(): ?string
    {
        return $this->github_token ?? null;
    }

    public function hasGitHubToken(): bool
    {
        return $this->gitHubToken() !== null;
    }

    public function canViewAsGitHubUser(): bool
    {
        return ! $this->isGitHubOrganization() && $this->hasGitHubToken();
    }

    protected function sponsorsClient(): GitHubSponsors
    {
        if (! $this->hasGitHubToken()) {
            return app(GitHubSponsors::class);
        }

        return new GitHubSponsors(new Factory(), $this->gitHubToken());
    }
}
