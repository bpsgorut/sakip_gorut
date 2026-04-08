<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Breadcrumb Labels
    |--------------------------------------------------------------------------
    |
    | This is the mapping of URL segments to their friendly display names.
    | Add entries here when you want a specific segment to have a custom name.
    | If a segment is not in this list, the system will automatically generate
    | a friendly name by replacing hyphens with spaces and capitalizing words.
    |
    */
    'labels' => [
        'dashboard' => 'Dashboard',
        'detail-dashboard' => 'Detail Dashboard',
        'perencanaan-kinerja' => 'Perencanaan Kinerja',
        'manajemen-renstra' => 'Manajemen Renstra',
        'manajemen-rkt' => 'Rencana Kinerja Tahunan',
        'manajemen-pk' => 'Perencanaan Kinerja',
        'renstra' => 'Renstra',
        'reviu-renstra' => 'Reviu Renstra',
        'reviu-target-renstra' => 'Reviu Target Renstra',
        'capaian-target-renstra' => 'Capaian Target Renstra',
        'detail' => 'Detail',
        'pengukuran-kinerja' => 'Pengukuran Kinerja',
        'fra' => 'Form Rencana Aksi',
        'form-target-fra' => 'Target FRA',
        'sk-tim-sakip' => 'SK Tim SAKIP',
        'sk' => 'SK',
        'tim' => 'Tim',
        'sakip' => 'SAKIP', 
        'manajemen-pengguna' => 'Manajemen Pengguna',
        'edit' => 'Edit',
        'pelaporan-kinerja' => 'Pelaporan Kinerja',
        'manajemen-lakin' => 'Manajemen Lakin',
        'lakin' => 'LAKIN',
        'generate-link' => 'Generate Link',
        'generate-link-permindok' => 'Generate Link Permindok',
        'ckp' => 'CKP',
        'reward-and-punishment' => 'Reward and Punishment',
        'inovasi-dan-penghargaan' => 'Inovasi dan Penghargaan',
        
        // Add your custom labels here
        // 'new-page-segment' => 'Your Custom Label',
    ],

    /*
    |--------------------------------------------------------------------------
    | Non-Clickable Segments
    |--------------------------------------------------------------------------
    |
    | This is a list of URL segments that should not be clickable in breadcrumbs.
    | These are typically categories or section headers that don't have their own pages.
    | Note: Main categories are now clickable and redirect to their default sub-pages.
    |
    */
    'non_clickable' => [
        'perencanaan-kinerja',
        'pengukuran-kinerja', 
        'pelaporan-kinerja',
        'detail',
        'edit',
        'create',
        'show'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Excluded Segments
    |--------------------------------------------------------------------------
    |
    | These segments will be completely excluded from breadcrumbs.
    | Useful for segments like 'api', numeric IDs, etc.
    |
    */
    'excluded_segments' => [
        // Segments to exclude from breadcrumbs completely
        // 'api', 
        // 'tmp'
    ],
];