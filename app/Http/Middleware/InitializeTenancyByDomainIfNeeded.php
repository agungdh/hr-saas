<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyByDomainIfNeeded
{
    public function __construct(
        protected Tenancy $tenancy,
        protected DomainTenantResolver $resolver
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Skip tenancy initialization for central domains
        if (in_array($request->getHost(), config('tenancy.central_domains', []), true)) {
            return $next($request);
        }

        // Initialize tenancy for tenant domains
        try {
            $this->tenancy->initialize(
                $this->resolver->resolve($request->getHost())
            );
        } catch (\Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedException $e) {
            // If tenant can't be identified, continue without tenancy
            // This allows central domain to work even if domain is not in central_domains
        }

        return $next($request);
    }
}
