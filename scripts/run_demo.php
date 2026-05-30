<?php

declare(strict_types=1);

require __DIR__ . '/../src/Services/TenantMaintenanceEscalationConsoleService.php';

$service = new TenantMaintenanceEscalationConsole\Services\TenantMaintenanceEscalationConsoleService();
$summary = $service->summary();

echo "Product: Tenant Maintenance Escalation Console\n";
echo "Tracked tickets: {$summary['ticketCount']}\n";
echo "Healthy lanes: {$summary['healthyCount']}\n";
echo "Watch lanes: {$summary['watchCount']}\n";
echo "Critical lanes: {$summary['criticalCount']}\n";
echo "Vendor escalations: {$summary['vendorCount']}\n";
echo "Lead recommendation: {$summary['leadRecommendation']}\n";
