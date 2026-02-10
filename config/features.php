<?php

return [
    // Rollout flags for new architecture
    'role_urls' => true,        // /superadmin, /admin, /purok
    'public_forms' => true,     // open public clearance/incident forms
    'remove_public_login' => true,
    'final_purok_approval' => true, // removes barangay-final confirmation when true
    'download_link_if_no_email' => true,

    // UI polish flags
    'live_search' => true,
    'pagination_filters' => true,

    // Analytics flags
    'analytics_filters' => true, // monthly/quarterly/annual
];
