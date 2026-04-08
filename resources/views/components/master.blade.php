<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"  />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title', 'SAKIP BPS')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo BPS.png') }}">
    <style>
        /* Dark Mode Styles */
        .dark {
            background-color: #121212;
            color: #E0E0E0;
        }

        .dark body {
            background-color: #121212;
            color: #E0E0E0;
        }

        .dark .bg-white {
            background-color: #1E1E1E !important;
            color: #E0E0E0 !important;
            border-color: #333333 !important;
        }

        .dark .bg-gray-50 {
            background-color: #1A1A1A !important;
            color: #E0E0E0 !important;
        }

        .dark .bg-gray-100 {
            background-color: #252525 !important;
            color: #E0E0E0 !important;
        }

        .dark .text-gray-500 {
            color: #f3efef !important; /* Increased contrast for better readability */
        }

        .dark .text-gray-600 {
            color: #D0D0D0 !important; /* Increased contrast for better readability */
        }

        .dark .text-gray-700 {
            color: #E0E0E0 !important;
        }

        .dark .text-gray-800 {
            color: #F0F0F0 !important;
        }

        .dark .text-gray-900 {
            color: #FFFFFF !important; /* Pure white for strongest text */
        }

        .dark .border-gray-200 {
            border-color: #333333 !important;
        }

        .dark .border-gray-300 {
            border-color: #444444 !important;
        }

        .dark .divide-gray-200 > * + * {
            border-color: #333333;
        }

        .dark .hover\:bg-gray-50:hover {
            background-color: #2C2C2C;
        }

        .dark .hover\:bg-white:hover {
            background-color: #1E1E1E;
        }

        .dark input, 
        .dark select, 
        .dark textarea {
            background-color: #252525 !important;
            color: #E0E0E0 !important;
            border-color: #444444 !important;
        }

        .dark .modal-overlay {
            background-color: rgba(0, 0, 0, 0.7);
        }

        .dark #globalLoadingOverlay {
            background-color: rgba(0, 0, 0, 0.7);
        }

        .dark #globalLoadingOverlay .bg-white {
            background-color: #1E1E1E !important;
            color: #E0E0E0 !important;
            border-color: #333333 !important;
        }

        /* Tambahan untuk kontras yang lebih baik */
        .dark .bg-gradient-to-r {
            opacity: 0.5 !important; /* Reduced opacity for all gradients to show background images */
        }

        .dark .border {
            border-color: #444444;
        }

        .dark .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(255, 255, 255, 0.05);
        }

        .dark .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(255, 255, 255, 0.1), 0 4px 6px -2px rgba(255, 255, 255, 0.05);
        }

        .dark .bg-red-50 {
            background-color: #2C1A1A !important;
            color: #FFA5A5 !important;
        }

        .dark .bg-blue-50 {
            background-color: #1A2C3A !important;
            color: #A5D8FF !important;
        }

        .dark .bg-green-50 {
            background-color: #1A2C1A !important;
            color: #8EE5A5 !important;
        }

        .dark .bg-amber-50 {
            background-color: #2C2C1A !important;
            color: #FFD700 !important;
        }

        .dark .bg-purple-50 {
            background-color: #2C1A2C;
        }

        .dark .text-red-700 {
            color: #FF6B6B !important;
        }

        .dark .text-blue-700 {
            color: #4DABF7 !important;
        }

        .dark .text-green-700 {
            color: #69DB7C !important;
        }

        .dark .text-yellow-700 {
            color: #FAB005 !important;
        }

        .dark .text-amber-700 {
            color: #FAB005 !important;
        }

        .dark .text-purple-700 {
            color: #9C51E0;
        }

        .dark .bg-gradient-to-br.from-green-600 {
            background-image: linear-gradient(to bottom right, #064E3B, #065F46);
            opacity: 0.9;
        }

        .dark .bg-gradient-to-r.from-gray-50 {
            background-image: linear-gradient(to right, #2C2C2C, #1E1E1E);
        }

        .dark .bg-gradient-to-r.from-red-500 {
            background-image: linear-gradient(to right, #7F1D1D, #991B1B);
        }

        .dark .bg-gradient-to-r.from-green-500 {
            background-image: linear-gradient(to right, #064E3B, #065F46);
        }

        .dark .bg-gradient-to-r.from-blue-500 {
            background-image: linear-gradient(to right, #1E3A8A, #2563EB);
        }

        .dark input[type="number"] {
            background-color: #2C2C2C;
            color: #ffffff;
            border-color: #444444;
        }

        .dark select {
            background-color: #2C2C2C;
            color: #ffffff;
            border-color: #444444;
        }

        .dark .bg-white {
            background-color: #1E1E1E;
            color: #ffffff;
        }

        .dark .border-gray-200 {
            border-color: #333333;
        }

        .dark .border-gray-300 {
            border-color: #444444;
        }

        .dark .hover\:bg-gray-50:hover {
            background-color: #2C2C2C;
        }

        .dark .bg-gray-50 {
            background-color: #2C2C2C;
        }

        .dark .bg-gray-100 {
            background-color: #1A1A1A;
        }

        .dark .text-gray-500 {
            color: #A0A0A0;
        }

        .dark .text-gray-600 {
            color: #B0B0B0;
        }

        .dark .text-gray-700 {
            color: #C0C0C0;
        }

        .dark .text-gray-800 {
            color: #D0D0D0;
        }

        .dark .text-gray-900 {
            color: #E0E0E0;
        }

        .dark .bg-blue-100 {
            background-color: #1A2C3A;
        }

        .dark .text-blue-800 {
            color: #4DABF7;
        }

        .dark .bg-red-100 {
            background-color: #2C1A1A;
        }

        .dark .text-red-800 {
            color: #FF6B6B;
        }

        .dark .bg-green-100 {
            background-color: #1A2C1A;
        }

        .dark .text-green-800 {
            color: #69DB7C;
        }

        .dark .bg-purple-100 {
            background-color: #2C1A2C;
        }

        .dark .text-purple-800 {
            color: #9C51E0;
        }
        .dark .fixed.bottom-0 {
            background-color: rgba(18, 18, 18, 0.8) !important;
            border-color: #333333 !important;
        }

        .dark .fixed.bottom-0 a,
        .dark .fixed.bottom-0 button {
            color: #E0E0E0 !important;
            border-color: #444444 !important;
        }

        .dark .fixed.bottom-0 .bg-gradient-to-r {
            background-image: linear-gradient(to right, #7F1D1D, #991B1B) !important;
            color: #ffffff !important;
        }

        .dark .fixed.bottom-0 .bg-white {
            background-color: #1E1E1E !important;
            color: #ffffff !important;
        }

        .dark .fixed.bottom-0 .border-gray-300 {
            border-color: #444444 !important;
        }

        .dark .fixed.bottom-0 .text-gray-700 {
            color: #C0C0C0 !important;
        }

        .dark .fixed.bottom-0 .hover\:bg-gray-50:hover {
            background-color: #2C2C2C !important;
        }

        .dark .bg-gradient-to-r.from-red-600 {
            background-image: linear-gradient(to right, #7F1D1D, #991B1B) !important;
            opacity: 0.9;
        }

        .dark .bg-gradient-to-r.to-red-700 {
            background-image: linear-gradient(to right, #7F1D1D, #991B1B) !important;
            opacity: 0.9;
        }

        .dark .target-input[readonly] {
            background-color: #2C2C2C !important;
            color: #4DABF7 !important; /* Warna biru cerah untuk teks */
            border-color: #444444 !important;
            font-weight: 600;
        }

        .dark .pk-value {
            background-color: #1A2C3A !important;
            color: #4DABF7 !important;
            border-color: #2563EB !important;
        }

        .dark .recently-calculated {
            background-color: #1A2C3A !important;
            border-color: #2563EB !important;
            color: #4DABF7 !important;
        }

        .dark .recently-saved {
            background-color: #1A2C3A !important;
            border-color: #065F46 !important;
            color: #69DB7C !important;
        }

        .dark .warning-row {
            background-color: #2C2C2C !important;
        }

        .dark .warning-cell {
            background-color: #2C2C2C !important;
        }

        .dark .warning-cell .pk-mismatch-warning {
            background-color: #4A2E1B !important;
            border-color: #7C2D12 !important;
            color: #FBBF24 !important;
        }

        .dark .warning-cell .tw-order-warning {
            background-color: #4A1D1D !important;
            border-color: #7F1D1D !important;
            color: #EF4444 !important;
        }
       
        /* Enhanced Responsive Layout Fixes */
        @media (max-width: 640px) {
            #logo-sidebar {
                transform: translateX(-100%);
                width: 224px;
            }
            
            #logo-sidebar.show {
                transform: translateX(0);
            }
            
            main {
                margin-left: 0 !important;
                padding: 1rem !important;
            }
            
            .container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            /* Mobile table responsiveness */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Mobile card layout for tables */
            .mobile-card {
                display: block;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
                padding: 1rem;
                background: white;
            }
            
            .mobile-card-header {
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.5rem;
            }
            
            .mobile-card-content {
                color: #6b7280;
                font-size: 0.875rem;
            }
        }
        
        @media (min-width: 640px) and (max-width: 768px) {
            #logo-sidebar {
                transform: translateX(0) !important;
                width: 224px;
            }
            
            main {
                margin-left: 224px !important;
                padding: 1.5rem !important;
            }
        }
        
        @media (min-width: 768px) {
            #logo-sidebar {
                transform: translateX(0) !important;
                width: 224px;
            }
            
            main {
                margin-left: 224px !important;
                padding: 1.5rem !important;
            }
        }
        
        @media (min-width: 1024px) {
            main {
                padding: 2rem !important;
            }
        }
        
        /* Smooth transitions */
        #logo-sidebar {
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        }
        
        main {
            transition: margin-left 0.3s ease-in-out, padding 0.3s ease-in-out;
        }
        
        /* Mobile navigation overlay */
        @media (max-width: 640px) {
            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 30;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
            }
            
            .mobile-overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }
        .dark .bg-slate-50 {
            background-color: #1A1A1A !important;
            color: #E0E0E0 !important;
        }

        .dark .border-gray-100 {
            border-color: #333333 !important;
        }

        .dark .hover\:bg-slate-100:hover {
            background-color: #252525 !important;
        }

        .dark .text-indigo-100 {
            color: #A5B4FC !important;
        }

        .dark .text-indigo-700 {
            color: #6366F1 !important;
        }

        .dark .text-gray-500 {
            color: #A0A0A0 !important;
        }

        .dark .text-gray-400 {
            color: #9CA3AF !important;
        }

        .dark .text-gray-700 {
            color: #C0C0C0 !important;
        }
        .dark .bg-amber-100 {
            background-color: #4A3A1B !important;
            color: #FFC107 !important;
        }

        .dark .text-amber-700 {
            color: #FFC107 !important;
        }

        .dark .hover\:bg-amber-200:hover {
            background-color: #5A4A2B !important;
        }

        .dark .capkin-kumulatif-output,
        .dark .capkin-setahun-output {
            background-color: #2C2C2C !important;
            color: #4DABF7 !important;
            border-color: #444444 !important;
        }

        .dark .realisasi-input[readonly] {
            background-color: #2C2C2C !important;
            color: #4DABF7 !important;
            border-color: #444444 !important;
        }

        .dark .realisasi-input.bg-gray-100 {
            background-color: #2C2C2C !important;
            color: #4DABF7 !important;
            border-color: #444444 !important;
        }
        .dark .bg-gray-50 {
            background-color: #1E1E1E !important;
            color: #E0E0E0 !important;
        }

        .dark .bg-gray-100 {
            background-color: #2C2C2C !important;
            color: #E0E0E0 !important;
        }

        .dark .bg-gray-200 {
            background-color: #3C3C3C !important;
            color: #F0F0F0 !important;
        }

        .dark .text-gray-400 {
            color: #A0A0A0 !important;
        }

        .dark .text-gray-500 {
            color: #B0B0B0 !important;
        }

        .dark .text-gray-600 {
            color: #C0C0C0 !important;
        }

        .dark .text-gray-700 {
            color: #D0D0D0 !important;
        }

        .dark .border-gray-200 {
            border-color: #444444 !important;
        }

        .dark .border-gray-300 {
            border-color: #555555 !important;
        }
        .dark .status-kelengkapan {
            background-color: #2C2C2C !important;
            color: #E0E0E0 !important;
            border-color: #444444 !important;
        }

        .dark .status-kelengkapan .progress-circle {
            background-color: #3C3C3C !important;
            color: #A0A0A0 !important;
            border-color: #555555 !important;
        }

        .dark .status-kelengkapan .progress-text {
            color: #B0B0B0 !important;
        }
        /* Improved color palette for dark mode */
        .dark .bg-red-50 {
            background-color: #2C1A1A !important;
            color: #FFA5A5 !important;
        }

        .dark .bg-blue-50 {
            background-color: #1A2C3A !important;
            color: #A5D8FF !important;
        }

        .dark .bg-green-50 {
            background-color: #1A2C1A !important;
            color: #8EE5A5 !important;
        }

        .dark .bg-amber-50 {
            background-color: #2C2C1A !important;
            color: #FFD700 !important;
        }

        /* Text colors for better contrast */
        .dark .text-red-700 {
            color: #FF6B6B !important;
        }

        .dark .text-blue-700 {
            color: #4DABF7 !important;
        }

        .dark .text-green-700 {
            color: #69DB7C !important;
        }

        .dark .text-amber-700 {
            color: #FAB005 !important;
        }

        /* Gradient backgrounds with reduced opacity */
        .dark .bg-gradient-to-r.from-red-600 {
            background-image: linear-gradient(to right, rgba(127, 29, 29, 0.8), rgba(153, 27, 27, 0.8)) !important;
        }

        .dark .bg-gradient-to-r.to-red-700 {
            background-image: linear-gradient(to right, rgba(127, 29, 29, 0.8), rgba(153, 27, 27, 0.8)) !important;
        }

        /* Specific improvements for SKP Detail page */
        .dark .bg-slate-50 {
            background-color: #1A1A1A !important;
            color: #E0E0E0 !important;
        }

        .dark .border-gray-100 {
            border-color: #333333 !important;
        }

        .dark .hover\:bg-slate-100:hover {
            background-color: #252525 !important;
        }

        /* Enhanced color scheme for interactive elements */
        .dark .text-indigo-100 {
            color: #A5B4FC !important;
        }

        .dark .text-indigo-700 {
            color: #6366F1 !important;
        }

        /* Improved contrast for various states */
        .dark .text-gray-400 {
            color: #9CA3AF !important;
        }

        .dark .text-gray-500 {
            color: #A0A0A0 !important;
        }

        .dark .text-gray-600 {
            color: #B0B0B0 !important;
        }

        .dark .text-gray-700 {
            color: #C0C0C0 !important;
        }

        /* Modal and notification improvements */
        .dark .notification-enter {
            background-color: #252525 !important;
            border-color: #444444 !important;
            color: #E0E0E0 !important;
        }

        .dark .notification-enter svg {
            color: #A0A0A0 !important;
        }

        /* Tailwind dark mode overrides */
        .dark .bg-white {
            background-color: #1E1E1E !important;
            color: #E0E0E0 !important;
        }

        .dark .border-gray-200 {
            border-color: #333333 !important;
        }

        .dark .border-gray-300 {
            border-color: #444444 !important;
        }

        /* Specific color for current month in SKP Detail */
        .dark .bg-[#2c3e50]/5 {
            background-color: rgba(44, 62, 80, 0.1) !important;
        }

        .dark .text-[#2c3e50] {
            color: #A5B4FC !important;
        }

        /* Improved dark mode for form inputs */
        .dark input, 
        .dark select, 
        .dark textarea {
            background-color: #252525 !important;
            color: #E0E0E0 !important;
            border-color: #444444 !important;
        }

        /* Specific improvements for SKP page */
        .dark .bg-gradient-to-r.from-blue-600.to-purple-700 {
            background-image: linear-gradient(to right, rgba(37, 99, 235, 0.5), rgba(124, 58, 237, 0.5)) !important; /* Adjusted opacity here as well */
        }
        .dark .bg-emerald-50 {
            background-color: #1A2C1A !important;
        }

        .dark .text-emerald-900, .dark .text-emerald-700 {
            color: #6EE7B7 !important; /* Soft mint green */
        }

        .dark .border-emerald-200 {
            border-color: #2D6B2D !important;
        }

        .dark .bg-rose-50 {
            background-color: #2C1A1A !important;
        }
        .dark .text-rose-900, .dark .text-rose-700 {
            color: #FDA4AF !important; /* Soft rose pink */
        }
        .dark .border-rose-200 {
            border-color: #7F1D1D !important;
        }

        .dark .bg-amber-50 {
            background-color: #2C2C1A !important;
        }
        .dark .text-amber-900, .dark .text-amber-700 {
            color: #FDE68A !important; /* Soft muted yellow */
        }
        .dark .border-amber-200 {
            border-color: #78350F !important;
        }

        .dark .bg-yellow-50 {
            background-color: #2C2C1A !important;
        }
        .dark .text-yellow-900, .dark .text-yellow-700 {
            color: #FEF08A !important; /* Soft pastel yellow */
        }
        .dark .border-yellow-200 {
            border-color: #854D0E !important;
        }

        .dark .bg-blue-50 {
            background-color: #1A2C3A !important;
        }
        .dark .text-blue-900, .dark .text-blue-700 {
            color: #7DD3FC !important; /* Soft sky blue */
        }
        .dark .border-blue-200 {
            border-color: #1E3A8A !important;
        }

        .dark .bg-purple-50 {
            background-color: #2C1A2C !important;
        }
        .dark .text-purple-900, .dark .text-purple-700 {
            color: #C4B5FD !important; /* Soft lavender */
        }
        .dark .border-purple-200 {
            border-color: #5B21B6 !important;
        }

        .dark .bg-gray-50 {
            background-color: #121212 !important; /* Deep almost-black for better contrast */
            color: #E0E0E0 !important;
        }
        .dark .border-gray-200 {
            border-color: #333333 !important;
        }

        /* Gradient Backgrounds with Reduced Opacity */
        .dark .bg-gradient-to-r {
            opacity: 0.7 !important;
        }
        
        /* Exception for buttons - keep them fully opaque */
        .dark button.bg-gradient-to-r,
        .dark a.bg-gradient-to-r,
        .dark .bg-gradient-to-r.hover\:from-amber-600,
        .dark .bg-gradient-to-r.hover\:from-blue-700,
        .dark .bg-gradient-to-r.hover\:from-red-600 {
            opacity: 1 !important;
        }

        /* Enhanced Text Visibility */
        .dark .text-gray-700 {
            color: #D1D5DB !important; /* Slightly lighter for better readability */
        }
        .dark .text-gray-600 {
            color: #9CA3AF !important;
        }
        .dark .text-gray-500 {
            color: #6B7280 !important;
        }
        .dark .text-gray-400 {
            color: #4B5563 !important;
        }

        /* Specific Improvements for Interactive Elements */
        .dark .bg-white {
            background-color: #1E1E1E !important;
            color: #E0E0E0 !important;
        }

        /* Soft Focus and Hover States */
        .dark .hover\:bg-gray-100:hover {
            background-color: #2A2A2A !important;
        }

        /* Ensure Subtle Differentiation */
        .dark .bg-opacity-10 {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        .dark .bg-opacity-20 {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        /* Dark Mode Breadcrumbs Hover State */
        .dark a:hover {
            color: #c90000 !important; /* Bright red for hover state */
        }

        .dark .hover\:text-red-900:hover {
            color: #c90000!important;
        }

        .dark a.hover\:text-red-900:hover {
            color: #c90000 !important;
        }
        
        /* Prevent hover effects on non-clickable breadcrumb components */
        .no-hover {
            pointer-events: none;
            user-select: none;
            cursor: default !important;
        }
        
        .no-hover:hover {
            color: inherit !important;
            text-decoration: none !important;
            background-color: transparent !important;
        }
        
        /* Ensure main category components don't show hover cursor */
        span.no-hover {
            cursor: default !important;
        }
        
        /* Remove any hover transitions for non-clickable elements */
        .no-hover,
        .no-hover * {
            transition: none !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans">
    {{-- Global Loading Overlay --}}
    <div id="globalLoadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl p-8 flex items-center space-x-4 shadow-2xl">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
            <span class="text-gray-700 font-medium text-lg" id="globalLoadingText">Memproses...</span>
        </div>
    </div>

    {{-- Alpine.js Loading Component (backup) --}}
    <div 
        x-data="{ show: false }" 
        x-show="show"
        x-on:loading.window="show = true"
        x-on:loading-complete.window="show = false"
        style="display: none"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50"
    >
        <div class="bg-white p-4 rounded-lg shadow-lg flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
            <span class="text-gray-700 font-medium">Memproses...</span>
        </div>
    </div>

    @include('components.navbar')
    @include('components.sidebar')

    {{-- Mobile Overlay --}}
    <div id="mobileOverlay" class="mobile-overlay"></div>
    
    {{-- Content --}}
    <div class="pt-16">
        <main class="p-4 sm:p-6 min-h-[calc(100vh-4rem)] sm:ml-56 transition-all duration-300">
            <div class="max-w-full overflow-hidden">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Global Modal will be inserted here dynamically -->

    <!-- Global Notification Container -->
    <div id="notificationContainer" class="fixed top-20 right-4 z-40 space-y-3 max-w-sm">
        <!-- Notifications will be inserted here -->
    </div>

    @stack('scripts')
    
    <script>
        // Disabled number input validation to allow proper decimal input
        // The HTML5 input[type="number"] with step="any" handles validation natively
        document.addEventListener('DOMContentLoaded', function() {
            // Let HTML5 handle number input validation natively
            console.log('Number input validation disabled - using native HTML5 validation');
        });

        // Enhanced Sidebar Toggle Handler
        const sidebarToggle = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
        const sidebar = document.getElementById('logo-sidebar');
        const mainContent = document.querySelector('main');
        const mobileOverlay = document.getElementById('mobileOverlay');
        
        function toggleSidebar() {
            if (window.innerWidth < 640) {
                sidebar.classList.toggle('show');
                mobileOverlay.classList.toggle('show');
                
                // Prevent body scroll when sidebar is open
                if (sidebar.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        }
        
        function closeSidebar() {
            if (sidebar && mobileOverlay) {
                sidebar.classList.remove('show');
                mobileOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
        
        if (sidebarToggle && sidebar && mainContent) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        
        // Close sidebar when clicking overlay
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeSidebar);
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 640) {
                // On desktop, always show sidebar and remove mobile states
                closeSidebar();
            } else {
                // On mobile, hide sidebar by default
                if (sidebar && !sidebar.classList.contains('show')) {
                    closeSidebar();
                }
            }
        });
        
        // Initialize sidebar state on page load
        if (window.innerWidth < 640 && sidebar) {
            closeSidebar();
        }
        
        // Handle escape key to close sidebar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && window.innerWidth < 640) {
                closeSidebar();
            }
        });
        
        // Prevent sidebar links from closing sidebar immediately on mobile
        if (sidebar) {
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Only close sidebar for non-dropdown links on mobile
                    if (window.innerWidth < 640 && !this.classList.contains('sidebar-dropdown-toggle')) {
                        setTimeout(closeSidebar, 100);
                    }
                });
            });
        }

        // Global Modal System
        let currentModal = null;
        
        function showModal(type, title, message, options = {}) {
            // Remove any existing modal
            const existingModal = document.getElementById('globalModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Default options
            const defaultOptions = {
                confirmText: 'OK',
                cancelText: 'Batal',
                showCancel: false,
                confirmCallback: () => {},
                cancelCallback: () => {},
                icon: getModalIcon(type),
                color: getModalColor(type)
            };
            
            const config = { ...defaultOptions, ...options };
            
            // Create modal HTML directly
            const modalHTML = `
                <div id="globalModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; padding: 1rem;">
                    <div style="background: white; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 28rem; width: 100%; padding: 1.5rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                            <div style="width: 3rem; height: 3rem; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #fef3c7;">
                                <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div style="margin-left: 1rem; flex: 1;">
                                <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">${title}</h3>
                            </div>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <p style="color: #6b7280; line-height: 1.625;">${message}</p>
                        </div>
                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            ${config.showCancel ? `
                                <button onclick="handleModalCancel()" style="padding: 0.5rem 1rem; color: #6b7280; background-color: #f3f4f6; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer;">
                                    ${config.cancelText}
                                </button>
                            ` : ''}
                            <button onclick="handleModalConfirm()" style="padding: 0.5rem 1.5rem; background-color: #dc2626; color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer;">
                                ${config.confirmText}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Insert modal into body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            currentModal = {
                confirmCallback: config.confirmCallback,
                cancelCallback: config.cancelCallback
            };
            
            // Add click outside to close
            const modal = document.getElementById('globalModal');
            modal.onclick = function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            };
        }
        
        function closeModal() {
            const modal = document.getElementById('globalModal');
            if (modal) {
                modal.remove();
            }
            currentModal = null;
        }
        
        function handleModalConfirm() {
            if (currentModal && currentModal.confirmCallback) {
                currentModal.confirmCallback();
            }
            closeModal();
        }
        
        function handleModalCancel() {
            if (currentModal && currentModal.cancelCallback) {
                currentModal.cancelCallback();
            }
            closeModal();
        }
        
        function getModalIcon(type) {
            const icons = {
                success: '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                error: '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                warning: '<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
                info: '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                question: '<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };
            return icons[type] || icons.info;
        }
        
        function getModalColor(type) {
            const colors = {
                success: { bg: 'bg-green-100', button: 'bg-green-600 hover:bg-green-700' },
                error: { bg: 'bg-red-100', button: 'bg-red-600 hover:bg-red-700' },
                warning: { bg: 'bg-yellow-100', button: 'bg-yellow-600 hover:bg-yellow-700' },
                info: { bg: 'bg-blue-100', button: 'bg-blue-600 hover:bg-blue-700' },
                question: { bg: 'bg-purple-100', button: 'bg-purple-600 hover:bg-purple-700' }
            };
            return colors[type] || colors.info;
        }
        
        // Global Notification System
        function showNotification(type, message, duration = 5000) {
            const container = document.getElementById('notificationContainer');
            const notificationId = 'notification-' + Date.now();
            
            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = `notification-enter bg-white rounded-lg shadow-lg border-l-4 p-4 ${getNotificationBorderColor(type)}`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${getNotificationIcon(type)}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button onclick="removeNotification('${notificationId}')" 
                                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Auto remove after duration
            setTimeout(() => {
                removeNotification(notificationId);
            }, duration);
        }
        
        function removeNotification(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.classList.remove('notification-enter');
                notification.classList.add('notification-leave');
                
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 400);
            }
        }
        
        function getNotificationIcon(type) {
            const icons = {
                success: '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                error: '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                warning: '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
                info: '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };
            return icons[type] || icons.info;
        }
        
        function getNotificationBorderColor(type) {
            const colors = {
                success: 'border-green-500',
                error: 'border-red-500',
                warning: 'border-yellow-500',
                info: 'border-blue-500'
            };
            return colors[type] || colors.info;
        }
        
        // Global Loading System
        function showLoading(message = 'Memproses...') {
            const overlay = document.getElementById('globalLoadingOverlay');
            const loadingText = document.getElementById('globalLoadingText');
            
            loadingText.textContent = message;
            overlay.classList.remove('hidden');
        }
        
        function hideLoading() {
            const overlay = document.getElementById('globalLoadingOverlay');
            overlay.classList.add('hidden');
        }
        
        // Convenience functions
        function confirmDelete(message, callback) {
            showModal('warning', 'Konfirmasi Hapus', message, {
                confirmText: 'Hapus',
                cancelText: 'Batal',
                showCancel: true,
                confirmCallback: callback
            });
        }
        
        function showSuccess(message) {
            showNotification('success', message);
        }
        
        function showError(message) {
            showNotification('error', message);
        }
        
        function showWarning(message) {
            showNotification('warning', message);
        }
        
        function showInfo(message) {
            showNotification('info', message);
        }
        
        // Initialize notifications from session
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showSuccess('{{ session('success') }}');
            @endif
            
            @if(session('error'))
                showError('{{ session('error') }}');
            @endif
            
            @if(session('warning'))
                showWarning('{{ session('warning') }}');
            @endif
            
            @if(session('info'))
                showInfo('{{ session('info') }}');
            @endif

            console.log('🚀 Global notifications initialized - NO AUTO LOADING interference');
        });

        // Global functions untuk manual loading control
        window.showGlobalLoading = showLoading;
        window.hideGlobalLoading = hideLoading;
        
        // Event listeners untuk custom loading events
        window.addEventListener('loading', function() {
            showLoading();
        });
        
        window.addEventListener('loading-complete', function() {
            hideLoading();
        });

        // Function untuk enable/disable loading pada element tertentu
        function setLoadingState(element, isLoading, message = 'Memproses...') {
            if (isLoading) {
                element.disabled = true;
                element.dataset.originalText = element.textContent;
                element.textContent = message;
                element.style.opacity = '0.6';
                showLoading(message);
            } else {
                element.disabled = false;
                element.textContent = element.dataset.originalText || element.textContent;
                element.style.opacity = '1';
                hideLoading();
            }
        }

        // Make setLoadingState globally available
        window.setLoadingState = setLoadingState;

        // Global Loading Overlay System for Modals
        document.addEventListener('DOMContentLoaded', function() {
            // Function to show the global loading overlay
            function showLoading() {
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('hidden');
                }
            }

            // Function to hide the global loading overlay
            function hideLoading() {
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingOverlay) {
                    loadingOverlay.classList.add('hidden');
                }
            }

            // Automatically attach loading indicator to forms inside modals
            // This assumes modals can be identified by a class or attribute.
            // We'll use a generic approach to find forms within elements that might be modals.
            // This is a simple heuristic: find all forms, check if they have a modal-like parent.
            // A more robust solution might require modals to have a specific class like 'modal-container'.
            const allForms = document.querySelectorAll('form');
            allForms.forEach(form => {
                // Heuristic: If a form is inside an element that is initially hidden or positioned fixed,
                // it might be in a modal. Let's attach the listener to be safe.
                // A better way is to identify modals by a common class.
                // For now, any form submission will trigger the loading.
                form.addEventListener('submit', function() {
                    // Check if the form submission was triggered by a button that should NOT show a loader
                    const submitter = event.submitter;
                    if (submitter && submitter.hasAttribute('data-no-loader')) {
                        return; // Do not show loader for this button
                    }
                    showLoading();
                });
            });
        });
    </script>
    <script>
        // Dark Mode Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const htmlElement = document.documentElement;

            // Check for saved dark mode preference
            const savedDarkMode = localStorage.getItem('darkMode');
            
            // Initial dark mode setup
            if (savedDarkMode === 'enabled' || 
                (!savedDarkMode && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                htmlElement.classList.add('dark');
                darkModeToggle.checked = true;
            }

            // Toggle dark mode
            darkModeToggle.addEventListener('change', function() {
                if (this.checked) {
                    htmlElement.classList.add('dark');
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    htmlElement.classList.remove('dark');
                    localStorage.setItem('darkMode', 'disabled');
                }
            });

            // Listen for system dark mode changes
            window.matchMedia('(prefers-color-scheme: dark)').addListener(function(e) {
                if (e.matches) {
                    htmlElement.classList.add('dark');
                    darkModeToggle.checked = true;
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    htmlElement.classList.remove('dark');
                    darkModeToggle.checked = false;
                    localStorage.setItem('darkMode', 'disabled');
                }
            });
        });
    </script>
</body>
</html>