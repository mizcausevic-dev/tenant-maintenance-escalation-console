<?php
/**
 * Plugin Name: Tenant Maintenance Escalation Console
 * Plugin URI: https://tenant.kineticgain.com/
 * Description: Publishes tenant maintenance escalation snapshots, vendor dispatch evidence, and machine-readable service-governance payloads for property sites.
 * Version: 0.1.0
 * Author: Kinetic Gain
 * License: AGPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/agpl-3.0.html
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('kg_tenant_maintenance_snapshot_payload')) {
    /**
     * @return array<string, mixed>
     */
    function kg_tenant_maintenance_snapshot_payload(): array
    {
        return [
            'entity' => 'Kinetic Gain Realty Ops',
            'kit' => 'Tenant Maintenance Escalation Console',
            'version' => '0.1.0',
            'updatedAt' => gmdate('c'),
            'maintenanceLanes' => [
                'water-intrusion-emergency',
                'hvac-outage-recovery',
                'elevator-certificate-renewal',
                'recurring-plumbing-backup',
                'gate-access-keypad-replacement',
            ],
            'operatorNote' => 'Synthetic demonstration payload only. Review resident communications, vendor packets, and habitability procedures before production use.',
        ];
    }
}

if (! function_exists('kg_render_tenant_maintenance_snapshot')) {
    function kg_render_tenant_maintenance_snapshot(): string
    {
        $payload = kg_tenant_maintenance_snapshot_payload();

        return '<pre class="kg-tenant-maintenance-snapshot">' . esc_html(wp_json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
    }
}

add_shortcode('kg_tenant_maintenance_snapshot', 'kg_render_tenant_maintenance_snapshot');

add_action('rest_api_init', static function (): void {
    register_rest_route(
        'kg-property-ops/v1',
        '/maintenance-snapshot',
        [
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => static function () {
                return rest_ensure_response(kg_tenant_maintenance_snapshot_payload());
            },
        ]
    );
});
