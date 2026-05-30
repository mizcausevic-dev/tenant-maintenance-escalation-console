<?php

declare(strict_types=1);

return [
    'summary' => [
        'entity' => 'Kinetic Gain Realty Ops',
        'operatorPosture' => 'Service-safe maintenance escalation',
        'leadRecommendation' => 'Escalate the water intrusion packet now: vendor acknowledgement, tenant comms, and temporary habitability notes are still split across three systems and the SLA clock is already red.',
    ],
    'maintenanceLanes' => [
        [
            'ticket' => 'Unit 4B water intrusion',
            'property' => 'Maple Crest Apartments',
            'owner' => 'Regional property manager',
            'package' => 'Emergency restoration packet',
            'proof' => 'Resident video, vendor quote, and temporary habitability note exist, but the insurance escalation log is still missing from the packet.',
            'risk' => 'Delay can trigger SLA breach, resident dissatisfaction, and reimbursement drag.',
            'nextAction' => 'Attach insurance escalation log, confirm drying vendor ETA, and issue the resident update within the same service packet.',
            'status' => 'critical',
        ],
        [
            'ticket' => 'HVAC outage in building C',
            'property' => 'Harbor Point Residences',
            'owner' => 'Field maintenance lead',
            'package' => 'Cooling recovery packet',
            'proof' => 'Technician dispatch and tenant notification are aligned, but replacement-part ETA is still stale in the resident portal.',
            'risk' => 'Residents can receive conflicting expectations on restoration timing.',
            'nextAction' => 'Refresh parts ETA and publish one resident-safe update window.',
            'status' => 'watch',
        ],
        [
            'ticket' => 'Elevator certificate renewal',
            'property' => 'North Tower Lofts',
            'owner' => 'Compliance operations',
            'package' => 'Certification renewal packet',
            'proof' => 'Inspection report, certificate upload, and vendor invoice are all aligned.',
            'risk' => 'Low. Current lane is review-safe.',
            'nextAction' => 'Monitor only.',
            'status' => 'healthy',
        ],
        [
            'ticket' => 'Recurring plumbing backup',
            'property' => 'Pine Street Townhomes',
            'owner' => 'Property operations',
            'package' => 'Scope expansion packet',
            'proof' => 'Three repeat incidents are logged, but the scope-expansion approval and resident relocation note still sit outside the canonical work-order packet.',
            'risk' => 'Repeat failures without clear escalation can turn into habitability and reputational exposure.',
            'nextAction' => 'Merge repeat-incident history and relocation note into the same approval packet before next vendor dispatch.',
            'status' => 'critical',
        ],
        [
            'ticket' => 'Gate access keypad replacement',
            'property' => 'Oak Terrace',
            'owner' => 'Vendor coordinator',
            'package' => 'Access-device service packet',
            'proof' => 'Vendor booking, resident notice, and access-code rotation plan are aligned.',
            'risk' => 'Low. Current packet is service-safe.',
            'nextAction' => 'Monitor only.',
            'status' => 'healthy',
        ],
    ],
    'escalationQueue' => [
        [
            'vendor' => 'DrySeal Restoration',
            'category' => 'Water mitigation',
            'sla' => '4 hours',
            'blockingIssue' => 'Insurance escalation log not attached',
            'owner' => 'Regional property manager',
            'status' => 'critical',
        ],
        [
            'vendor' => 'CoolAir Mechanical',
            'category' => 'HVAC repair',
            'sla' => 'Same day',
            'blockingIssue' => 'Replacement-part ETA stale in tenant portal',
            'owner' => 'Field maintenance lead',
            'status' => 'watch',
        ],
        [
            'vendor' => 'Metro Lift Services',
            'category' => 'Elevator certification',
            'sla' => '72 hours',
            'blockingIssue' => 'No active blocker',
            'owner' => 'Compliance operations',
            'status' => 'healthy',
        ],
        [
            'vendor' => 'RapidRoot Plumbing',
            'category' => 'Drain and sewer scope',
            'sla' => '24 hours',
            'blockingIssue' => 'Repeat-incident history not merged into approval packet',
            'owner' => 'Property operations',
            'status' => 'critical',
        ],
    ],
    'verificationGates' => [
        [
            'gate' => 'Resident-facing timeline matches the current vendor dispatch reality',
            'detail' => 'Block updates when resident communications and vendor ETA diverge.',
            'status' => 'watch',
        ],
        [
            'gate' => 'Emergency and habitability evidence sits inside the canonical maintenance packet',
            'detail' => 'Block escalation when photos, insurance notes, or relocation steps live outside the work-order packet.',
            'status' => 'critical',
        ],
        [
            'gate' => 'Repeat incidents are tied to one approval and scope-expansion history',
            'detail' => 'Keep recurring failures from being treated like unrelated tickets.',
            'status' => 'watch',
        ],
        [
            'gate' => 'Vendor SLA, invoice packet, and owner assignment remain aligned',
            'detail' => 'Ensure the dispatch owner and invoice/evidence packet point to the same accountable lane.',
            'status' => 'approved',
        ],
    ],
];
