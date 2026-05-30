<?php

declare(strict_types=1);

namespace TenantMaintenanceEscalationConsole\Services;

final class TenantMaintenanceEscalationConsoleService
{
    /** @var array<string, mixed> */
    private array $payload;

    public function __construct()
    {
        /** @var array<string, mixed> $payload */
        $payload = require __DIR__ . '/../Data/sample_tenant_maintenance_escalation.php';
        $this->payload = $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $lanes = $this->maintenanceLanes();
        $critical = array_values(array_filter($lanes, static fn(array $lane): bool => $lane['status'] === 'critical'));
        $watch = array_values(array_filter($lanes, static fn(array $lane): bool => $lane['status'] === 'watch'));
        $healthy = array_values(array_filter($lanes, static fn(array $lane): bool => $lane['status'] === 'healthy'));

        return [
            'ticketCount' => count($lanes),
            'healthyCount' => count($healthy),
            'watchCount' => count($watch),
            'criticalCount' => count($critical),
            'vendorCount' => count($this->escalationQueue()),
            'operatorPosture' => (string) $this->payload['summary']['operatorPosture'],
            'leadRecommendation' => (string) $this->payload['summary']['leadRecommendation'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function maintenanceLanes(): array
    {
        /** @var array<int, array<string, mixed>> $lanes */
        $lanes = $this->payload['maintenanceLanes'];

        return $lanes;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function escalationQueue(): array
    {
        /** @var array<int, array<string, mixed>> $queue */
        $queue = $this->payload['escalationQueue'];

        return $queue;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function verificationGates(): array
    {
        /** @var array<int, array<string, mixed>> $gates */
        $gates = $this->payload['verificationGates'];

        return $gates;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'product' => 'Tenant Maintenance Escalation Console',
            'purpose' => 'WordPress and property-ops control plane for tenant maintenance queues, vendor escalation, SLA posture, and service-safe review routing.',
            'routes' => [
                '/',
                '/maintenance-lane',
                '/escalation-queue',
                '/service-posture',
                '/verification',
                '/docs',
            ],
            'priorities' => [
                'Keep tenant promise language, maintenance urgency, and vendor dispatch status in the same reviewed lane.',
                'Expose aging tickets and SLA drift before property managers discover them in the escalation inbox.',
                'Tie resident-facing status updates, work-order evidence, and vendor accountability to the same service packet.',
                'Turn maintenance governance into an operator surface instead of a scattered portal workflow.',
            ],
            'entity' => (string) $this->payload['summary']['entity'],
        ];
    }
}
