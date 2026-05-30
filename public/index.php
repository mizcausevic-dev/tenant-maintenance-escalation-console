<?php

declare(strict_types=1);

use TenantMaintenanceEscalationConsole\Services\TenantMaintenanceEscalationConsoleService;

require __DIR__ . '/../src/Services/TenantMaintenanceEscalationConsoleService.php';
require __DIR__ . '/../src/Views/render.php';

$service = new TenantMaintenanceEscalationConsoleService();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (str_starts_with($path, '/api/')) {
    header('Content-Type: application/json; charset=utf-8');

    $payload = match ($path) {
        '/api/dashboard/summary' => $service->summary(),
        '/api/maintenance-lane' => $service->maintenanceLanes(),
        '/api/escalation-queue' => $service->escalationQueue(),
        '/api/verification' => $service->verificationGates(),
        '/api/sample' => $service->payload(),
        default => ['error' => 'Not found'],
    };

    if ($payload === ['error' => 'Not found']) {
        http_response_code(404);
    }

    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return;
}

$html = match ($path) {
    '/' => TenantMaintenanceEscalationConsole\Views\render_overview(),
    '/maintenance-lane' => TenantMaintenanceEscalationConsole\Views\render_maintenance_lane(),
    '/escalation-queue' => TenantMaintenanceEscalationConsole\Views\render_escalation_queue(),
    '/service-posture' => TenantMaintenanceEscalationConsole\Views\render_service_posture(),
    '/verification' => TenantMaintenanceEscalationConsole\Views\render_verification(),
    '/docs' => TenantMaintenanceEscalationConsole\Views\render_docs(),
    default => null,
};

if ($html === null) {
    http_response_code(404);
    echo 'Not found';
    return;
}

header('Content-Type: text/html; charset=utf-8');
echo $html;
