@extends('layouts.app')

@section('title', 'SPMB 2027/2028 - SMK TI BALI GLOBAL BADUNG')
@section('html_class', 'h-full bg-white scroll-smooth')

@push('styles')
    <style>
        html {
          scrollbar-gutter: stable;
          overflow-x: clip;
          scrollbar-width: thin;
          scrollbar-color: #475569 transparent;
        }

        body {
          overflow-x: clip;
        }

        .hero-pill-badge {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          padding: 6px 16px;
          border-radius: 9999px;
          background: rgba(255, 255, 255, 0.12);
          backdrop-filter: blur(8px);
          -webkit-backdrop-filter: blur(8px);
          border: 1px solid rgba(255, 255, 255, 0.25);
          color: #7DD3FC;
          font-weight: 600;
          margin-bottom: 16px;
        }

        .hero-title {
          font-family: "Plus Jakarta Sans", sans-serif;
          font-weight: 800;
          letter-spacing: -0.035em;
          line-height: 1.15 !important;
          text-shadow: none !important;
          filter: none !important;
          -webkit-filter: none !important;
        }

        .hero-title span {
          line-height: 1.15;
          text-shadow: none !important;
          filter: none !important;
          -webkit-filter: none !important;
        }

        .hero-description {
          max-width: 640px;
          text-align: center;
          text-shadow: none !important;
          filter: none !important;
          -webkit-filter: none !important;
        }

        /* Card Surface: Cara Pendaftaran (Online & Offline) - Gradasi Modern Harmonis Putih Halus */
        .cara-daftar-card {
          background: linear-gradient(180deg, #FFFFFF 0%, #FBFDFE 45%, #F4F7FB 100%);
          border: 1px solid #E2E8F0;
          box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.05), inset 0 1px 0 #FFFFFF;
        }
        .cara-daftar-card:hover {
          background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFD 100%);
          border-color: #CBD5E1;
          box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.08), inset 0 1px 0 #FFFFFF !important;
        }

        /* Tooltip / Popover Kartu Informasi Berkas (HANYA saat tombol Lihat Lokasi di-hover) */
        .lokasi-tooltip-wrapper {
          opacity: 0;
          visibility: hidden;
          pointer-events: none !important;
          transform: translate3d(0, 10px, 0) scale(0.98);
          transition: opacity 0.2s cubic-bezier(0.4, 0, 1, 1),
                      transform 0.2s cubic-bezier(0.4, 0, 1, 1),
                      visibility 0.2s;
          will-change: transform, opacity;
          backface-visibility: hidden;
        }
        #btn-lihat-lokasi:hover ~ .lokasi-tooltip-wrapper,
        #btn-lihat-lokasi:focus ~ .lokasi-tooltip-wrapper,
        .lokasi-tooltip-wrapper.is-hovered {
          opacity: 1;
          visibility: visible;
          pointer-events: auto !important;
          transform: translate3d(0, 0, 0) scale(1);
          transition: opacity 0.34s cubic-bezier(0.22, 1, 0.36, 1),
                      transform 0.34s cubic-bezier(0.22, 1, 0.36, 1),
                      visibility 0.34s;
        }

        .cara-slider-container {
          box-shadow: 0 0 16px rgba(15, 23, 42, 0.08);
        }

        /* Surface Gradasi Modern Harmonis Putih Halus (Referensi Cara Pendaftaran) */
        .harmonious-soft-gradient {
          background: linear-gradient(180deg, #FFFFFF 0%, #FBFDFE 45%, #F4F7FB 100%);
        }

        /* Footer Utama: Warna Putih-Abu Halus Modern (Grounded & Clean) */
        #section-footer {
          background: linear-gradient(180deg, #F1F5F9 0%, #E8EEF5 100%);
        }

        /* Sembunyikan scrollbar pada tabel Rincian Biaya agar tidak muncul/kedip saat refresh */
        .rincian-biaya-table-wrap {
          scrollbar-width: none;
          -ms-overflow-style: none;
        }
        .rincian-biaya-table-wrap::-webkit-scrollbar {
          display: none;
          width: 0;
          height: 0;
        }

        /* Micro-interaction: Unduh Panduan PDF Button Animations */
        @keyframes panduanSpin {
          0% {
            transform: rotate(0deg);
          }
          100% {
            transform: rotate(360deg);
          }
        }
        .panduan-spinner,
        .animate-spin {
          animation: panduanSpin 0.7s linear infinite !important;
          transform-origin: center center;
        }

        @keyframes panduanShimmer {
          0% {
            transform: translateX(-100%);
          }
          100% {
            transform: translateX(100%);
          }
        }
        .panduan-shimmer-active {
          animation: panduanShimmer 0.9s cubic-bezier(0.4, 0, 0.2, 1) infinite !important;
        }

        @keyframes panduanCheckPop {
          0% {
            transform: scale(0.3) rotate(-10deg);
            opacity: 0;
          }
          70% {
            transform: scale(1.22) rotate(3deg);
            opacity: 1;
          }
          100% {
            transform: scale(1.1) rotate(0deg);
            opacity: 1;
          }
        }
        .panduan-check-pop {
          animation: panduanCheckPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }

        /* Minimalist Custom Scrollbar */
        ::-webkit-scrollbar {
          width: 6px;
          height: 6px;
        }
        ::-webkit-scrollbar-track {
          background: transparent;
        }
        ::-webkit-scrollbar-thumb {
          background: #475569;
          border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
          background: #00a8e8;
        }

        /* Scrollbar saat modal berkas terbuka: tetap ada, tidak bisa digeser/scroll, dan ikut gelap */
        html.modal-berkas-open {
          color-scheme: dark;
          scrollbar-color: rgba(71, 85, 105, 0.45) rgba(2, 6, 23, 0.6);
        }
        html.modal-berkas-open::-webkit-scrollbar-track,
        html.modal-berkas-open ::-webkit-scrollbar-track {
          background: rgba(2, 6, 23, 0.6);
        }
        html.modal-berkas-open::-webkit-scrollbar-thumb,
        html.modal-berkas-open ::-webkit-scrollbar-thumb {
          background: rgba(71, 85, 105, 0.45);
        }
        html.modal-berkas-open::-webkit-scrollbar-thumb:hover,
        html.modal-berkas-open ::-webkit-scrollbar-thumb:hover {
          background: rgba(71, 85, 105, 0.45);
        }

        /* Print Styles: Isolates Kartu Identitas Pendaftaran */
        @media print {
          body * {
            visibility: hidden !important;
          }
          #printable-id-card,
          #printable-id-card * {
            visibility: visible !important;
          }
          #printable-id-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 10mm;
            display: block !important;
            background: #ffffff !important;
          }
          .no-print {
            display: none !important;
          }
          @page {
            size: A4 portrait;
            margin: 10mm;
          }
        }

        /* Hero Background & Color Grading */
        .hero-bg-image {
          background-image: url("{{ asset('assets/img/hero-bgs.jpg') }}");
          background-position: center 58%;
          background-size: cover;
          background-repeat: no-repeat;
          /* Tingkatkan ketajaman & kehangatan warna (color grading lebih hidup) */
          filter: contrast(106%) saturate(120%) brightness(1.04);
          transform: scale(1.02);
        }

        @media (max-width: 639px) {
          .hero-bg-image {
            background-size: auto 125%;
            background-position: center 68%;
          }
        }

        /* Dark overlay dengan radial gradient lebih gelap di tengah & backdrop blur tipis */
        .hero-gradient-overlay {
          background: radial-gradient(
            circle at center,
            rgba(15, 23, 42, 0.78) 0%,
            rgba(15, 23, 42, 0.55) 100%
          );
          backdrop-filter: blur(2px);
          -webkit-backdrop-filter: blur(2px);
        }

        /* Blending bawah hero: volume sangat minim dan tipis hanya di dasar paling bawah */
        .hero-edge-blend {
          background: linear-gradient(
            to top,
            #ffffff 0%,
            rgba(255, 255, 255, 0.5) 1%,
            rgba(255, 255, 255, 0.1) 2%,
            transparent 3.5%
          );
        }

        /* ============================================================== */
        /* Floating Pill Navbar: Dynamic Tinted Glassmorphism            */
        /* ============================================================== */
        #main-navbar {
          position: fixed;
          top: 14px;
          left: 0;
          right: 0;
          margin-left: auto;
          margin-right: auto;
          width: 96%;
          max-width: 1380px;
          border-radius: 9999px;
          z-index: 40;
          background: linear-gradient(90deg, rgba(245, 247, 250, 0.85) 0%, rgba(245, 247, 250, 0.65) 50%, rgba(200, 215, 225, 0.75) 100%) !important;
          backdrop-filter: blur(14px) !important;
          -webkit-backdrop-filter: blur(14px) !important;
          border: none !important;
          box-shadow: 0 2px 8px -1px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03) !important;
          transition: background 0.35s ease,
                      box-shadow 0.35s ease,
                      transform 0.35s ease,
                      width 0.35s ease,
                      max-width 0.35s ease;
        }
        @media (min-width: 640px) {
          #main-navbar {
            top: 16px;
            width: 95%;
          }
        }
        @media (min-width: 1024px) {
          #main-navbar {
            top: 20px;
          }
        }

        #main-navbar nav {
          transition: padding 0.35s ease;
        }

        /* State: Scrolled */
        #main-navbar.nav-scrolled {
          width: 94.5%;
          max-width: 1320px;
          background: linear-gradient(90deg, rgba(245, 247, 250, 0.90) 0%, rgba(245, 247, 250, 0.75) 50%, rgba(200, 215, 225, 0.82) 100%) !important;
          backdrop-filter: blur(16px) !important;
          -webkit-backdrop-filter: blur(16px) !important;
          border: none !important;
          box-shadow: 0 4px 12px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04) !important;
        }
        @media (min-width: 640px) {
          #main-navbar.nav-scrolled {
            width: 93.5%;
          }
        }
        @media (min-width: 1024px) {
          #main-navbar.nav-scrolled {
            width: 93%;
          }
        }

        #main-navbar.nav-scrolled nav {
          padding-top: 11px !important;
          padding-bottom: 11px !important;
          padding-left: 14px !important;
          padding-right: 14px !important;
        }
        @media (min-width: 640px) {
          #main-navbar.nav-scrolled nav {
            padding-left: 25px !important;
            padding-right: 25px !important;
          }
        }

        /* Tombol Akun Konsisten (Gaya Outline Bersih dengan Sedikit Kontras) */
        #nav-user-btn-auth {
          background-color: rgba(255, 255, 255, 0.42) !important;
          border: 1px solid rgba(148, 163, 184, 0.55) !important;
          box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
          color: #1e293b !important;
          backdrop-filter: blur(8px) !important;
          -webkit-backdrop-filter: blur(8px) !important;
          transition: all 0.2s ease;
        }
        #nav-user-btn-auth:hover {
          background-color: rgba(255, 255, 255, 0.75) !important;
          border-color: rgba(100, 116, 139, 0.75) !important;
          box-shadow: 0 2px 4px rgba(15, 23, 42, 0.06) !important;
        }

        /* Desktop Navigation Links */
        #nav-desktop-links {
          transition: gap 0.35s ease;
        }
        @media (min-width: 1024px) {
          #nav-desktop-links {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
          }
        }

        /* Khusus navbar saat di-scroll: perkecil gap antar menu (dinaikkan sedikit agar proporsional) */
        #main-navbar.nav-scrolled #nav-desktop-links {
          gap: 0.4375rem;
        }
        @media (min-width: 1280px) {
          #main-navbar.nav-scrolled #nav-desktop-links {
            gap: 0.625rem;
          }
        }

        /* Navbar Menu Link (Clean text style without pill/box effect) */
        .nav-menu-link {
          position: relative;
          padding: 0.35rem 0.35rem;
          transition: color 0.2s ease, opacity 0.2s ease;
          color: #64748b;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          font-weight: 500;
          font-size: 0.845rem;
          letter-spacing: -0.01em;
          text-decoration: none;
          white-space: nowrap;
          user-select: none;
        }
        .nav-menu-link:hover {
          color: #2563eb;
        }
        .nav-menu-link.active {
          color: #2563eb !important;
          font-weight: 700 !important;
        }

        /* Mobile Nav Menu Active Item */
        .nav-mobile-link {
          display: block;
          border-radius: 0.75rem;
          padding: 0.625rem 0.875rem;
          font-size: 0.875rem;
          font-weight: 500;
          color: #475569;
          transition: all 0.2s ease;
          text-decoration: none;
        }
        .nav-mobile-link:hover {
          background-color: rgba(37, 99, 235, 0.08);
          color: #2563eb;
        }
        .nav-mobile-link.active {
          color: #2563eb !important;
          font-weight: 600 !important;
          background-color: rgba(37, 99, 235, 0.10) !important;
        }

        @keyframes faqReveal {
          from {
            opacity: 0;
            transform: translateY(-4px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        #section-cta details[open] > div,
        #section-cta details[open] > p {
          animation: faqReveal 260ms ease-out both;
        }

        #section-cta [data-quota-tilt] {
          position: relative;
          transform: perspective(950px) rotateX(var(--quota-tilt-x, 0deg)) rotateY(var(--quota-tilt-y, 0deg)) translateZ(var(--quota-tilt-z, 0px)) scale3d(var(--quota-scale, 1), var(--quota-scale, 1), var(--quota-scale, 1));
          transform-style: preserve-3d;
          will-change: transform, box-shadow;
          box-shadow: none !important;
          /* Transisi pegas elastis halus saat hover dilepas (spring rebound) */
          transition: transform 0.75s cubic-bezier(0.2, 1.25, 0.4, 1),
                      box-shadow 0.75s cubic-bezier(0.2, 1.25, 0.4, 1),
                      border-color 0.4s ease;
        }

        /* Saat pointer aktif bergerak di atas kartu: transisi responsif & lentur tanpa jeda */
        #section-cta [data-quota-tilt].is-tilting {
          transition: transform 0.08s ease-out,
                      box-shadow 0.12s ease-out,
                      border-color 0.25s ease;
        }

        #section-cta [data-quota-tilt]:hover,
        #section-cta [data-quota-tilt].is-tilting {
          border-color: rgba(255, 255, 255, 0.5);
          box-shadow: none !important;
        }

        /* Glare effect: kilau cahaya dinamis realistis mengikuti pointer mouse */
        #section-cta [data-quota-tilt] .quota-glare {
          position: absolute;
          inset: 0;
          border-radius: inherit;
          pointer-events: none;
          background: radial-gradient(
            circle 380px at var(--mouse-x, 50%) var(--mouse-y, 50%),
            rgba(255, 255, 255, 0.38) 0%,
            rgba(255, 255, 255, 0.12) 30%,
            rgba(255, 255, 255, 0) 70%
          );
          opacity: var(--glare-opacity, 0);
          mix-blend-mode: overlay;
          transition: opacity 0.65s cubic-bezier(0.2, 0.8, 0.2, 1);
          z-index: 25;
        }

        #section-cta [data-quota-tilt].is-tilting .quota-glare {
          transition: opacity 0.2s ease-out;
        }

        /* Efek 3D Parallax Bertingkat (Lekuk Mendalam / Multi-Plane Depth) */
        #section-cta [data-quota-tilt] [data-quota-item] {
          transform: translateZ(16px);
          transform-style: preserve-3d;
          transition: transform 0.75s cubic-bezier(0.2, 1.25, 0.4, 1), background-color 0.2s ease;
        }

        #section-cta [data-quota-tilt].is-tilting [data-quota-item] {
          transition: transform 0.08s ease-out, background-color 0.2s ease;
        }

        @media (hover: hover) and (pointer: fine) {
          #section-cta [data-quota-tilt] [data-quota-item]:hover {
            transform: translateZ(26px);
            background-color: rgba(255, 255, 255, 0.08);
          }
        }

        #section-cta [data-quota-tilt] .quota-progress-track {
          transform: translateZ(12px);
          box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 1px 1px rgba(255, 255, 255, 0.15);
        }

        @media (prefers-reduced-motion: reduce) {
          #section-cta details[open] > div,
          #section-cta details[open] > p {
            animation: none;
          }

          #section-cta [data-quota-tilt],
          #section-cta [data-quota-tilt] [data-quota-item],
          #section-cta [data-quota-tilt] .quota-progress-track {
            transform: none !important;
            transition: none !important;
          }

          #section-cta [data-quota-tilt] .quota-glare {
            display: none;
          }
        }

        /* ============================================================== */
        /* CARA PENDAFTARAN - CARD SLIDER & ZOOM EFFECT                   */
        /* ============================================================== */
        .cara-slide {
          position: absolute;
          inset: 0;
          width: 100%;
          height: 100%;
          overflow: hidden;
          will-change: transform, opacity;
        }

        .cara-slide-img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          object-position: center;
          transform: scale(1);
          transform-origin: center center;
          will-change: transform;
        }

        @keyframes caraKenBurnsZoom {
          0% {
            transform: scale(1);
          }
          100% {
            transform: scale(1.09);
          }
        }

        .cara-slide.is-active .cara-slide-img {
          animation: caraKenBurnsZoom 6s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        @media (prefers-reduced-motion: reduce) {
          .cara-slide.is-active .cara-slide-img {
            animation: none;
            transform: none;
          }
          .cara-slide {
            transition-duration: 0.01ms !important;
          }
        }
      
        /* ============================================================== */
        /* GRID 3x2 - JALUR PENDAFTARAN                                   */
        /* ============================================================== */
        #section-jalur {
          --bento-bg: #ffffff;
          --bento-ink: #1B2559;
          --bento-blue-a: #1D4ED8;
          --bento-blue-line: #DBEAFE;
          --bento-yellow-a: #B45309;
          --bento-yellow-line: #FDE68A;
          --bento-green-a: #15803D;
          --bento-green-line: #BBF7D0;
          --bento-purple-a: #6D28D9;
          --bento-purple-line: #DDD6FE;
          --bento-orange-a: #C2410C;
          --bento-orange-line: #FED7AA;
          --bento-navy-a: #1E3A8A;
          --bento-navy-line: #CBD5E1;
          background-color: var(--bento-bg);
        }

        #section-jalur .bento-wrap {
          max-width: 1020px;
          margin: 0 auto;
        }

        #section-jalur .bento-grid {
          display: grid;
          grid-template-columns: repeat(3, minmax(0, 1fr));
          gap: 18px;
        }

        #section-jalur .bento-card {
          perspective: 1000px;
          -webkit-perspective: 1000px;
          height: 182px;
          display: block;
          text-decoration: none;
          color: var(--bento-ink);
          background: transparent;
          border: none;
          box-shadow: none;
          padding: 0;
          cursor: pointer;
        }

        #section-jalur .bento-card-inner {
          position: relative;
          width: 100%;
          height: 100%;
          transition: transform 0.6s cubic-bezier(0.34, 1.25, 0.64, 1);
          transform-style: preserve-3d;
          -webkit-transform-style: preserve-3d;
          border-radius: 16px;
        }

        #section-jalur .bento-card:hover .bento-card-inner,
        #section-jalur .bento-card:focus-visible .bento-card-inner,
        #section-jalur .bento-card.is-flipped .bento-card-inner {
          transform: rotateY(180deg);
          -webkit-transform: rotateY(180deg);
        }

        #section-jalur .bento-card-front,
        #section-jalur .bento-card-back {
          position: absolute;
          inset: 0;
          width: 100%;
          height: 100%;
          -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
          border-radius: 16px;
          overflow: hidden;
          border: 1px solid rgba(27, 37, 89, 0.08);
          box-shadow: none;
          transition: border-color 0.25s ease, background 0.25s ease;
        }

        #section-jalur .bento-card:hover .bento-card-front,
        #section-jalur .bento-card:hover .bento-card-back {
          box-shadow: none;
        }

        #section-jalur .bento-card-front {
          padding: 16px 18px;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          z-index: 2;
          transform: rotateY(0deg) translate3d(0, 0, 0);
          -webkit-transform: rotateY(0deg) translate3d(0, 0, 0);
        }

        #section-jalur .bento-card-back {
          padding: 16px 18px 14px;
          display: flex;
          flex-direction: column;
          justify-content: flex-start;
          transform: rotateY(180deg) translate3d(0, 0, 0);
          -webkit-transform: rotateY(180deg) translate3d(0, 0, 0);
          z-index: 1;
        }

        #section-jalur .bento-card.bento-blue .bento-card-front,
        #section-jalur .bento-card.bento-blue .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #F5F9FE 45%, #E1EFFE 100%);
          border-color: var(--bento-blue-line);
        }
        #section-jalur .bento-card.bento-blue:hover .bento-card-front,
        #section-jalur .bento-card.bento-blue:hover .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #EBF4FE 100%);
          border-color: #93C5FD;
        }

        #section-jalur .bento-card.bento-yellow .bento-card-front,
        #section-jalur .bento-card.bento-yellow .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #FFFDF5 45%, #FEF3D6 100%);
          border-color: var(--bento-yellow-line);
        }
        #section-jalur .bento-card.bento-yellow:hover .bento-card-front,
        #section-jalur .bento-card.bento-yellow:hover .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #FEF7DE 100%);
          border-color: #FCD34D;
        }

        #section-jalur .bento-card.bento-green .bento-card-front,
        #section-jalur .bento-card.bento-green .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #F6FDF9 45%, #DCF7E5 100%);
          border-color: var(--bento-green-line);
        }
        #section-jalur .bento-card.bento-green:hover .bento-card-front,
        #section-jalur .bento-card.bento-green:hover .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #E4F9EB 100%);
          border-color: #86EFAC;
        }

        #section-jalur .bento-card.bento-purple .bento-card-front,
        #section-jalur .bento-card.bento-purple .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #FAF8FE 45%, #ECE4FD 100%);
          border-color: var(--bento-purple-line);
        }
        #section-jalur .bento-card.bento-purple:hover .bento-card-front,
        #section-jalur .bento-card.bento-purple:hover .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #F0EAFF 100%);
          border-color: #C4B5FD;
        }

        #section-jalur .bento-card.bento-orange .bento-card-front,
        #section-jalur .bento-card.bento-orange .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #FFF9F5 45%, #FEE5D6 100%);
          border-color: var(--bento-orange-line);
        }
        #section-jalur .bento-card.bento-orange:hover .bento-card-front,
        #section-jalur .bento-card.bento-orange:hover .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #FEEDDF 100%);
          border-color: #FDBA74;
        }

        #section-jalur .bento-card.bento-navy .bento-card-front,
        #section-jalur .bento-card.bento-navy .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFD 45%, #E2EAF4 100%);
          border-color: var(--bento-navy-line);
        }
        #section-jalur .bento-card.bento-navy:hover .bento-card-front,
        #section-jalur .bento-card.bento-navy:hover .bento-card-back {
          background: linear-gradient(180deg, #FFFFFF 0%, #EAF0F7 100%);
          border-color: #94A3B8;
        }

        #section-jalur .bento-art {
          display: block;
          width: auto;
          max-width: 100%;
          height: 82px;
          max-height: 82px;
          margin: 0 auto;
          flex-shrink: 0;
          transition: transform 0.3s ease;
        }

        #section-jalur .bento-card:hover .bento-art {
          transform: scale(1.05);
        }

        #section-jalur .bento-card-info {
          display: flex;
          flex-direction: column;
        }

        #section-jalur .bento-tag {
          font-size: 10px;
          font-weight: 800;
          letter-spacing: .08em;
          text-transform: uppercase;
          margin-bottom: 2px;
        }

        #section-jalur .bento-blue .bento-tag { color: var(--bento-blue-a); }
        #section-jalur .bento-yellow .bento-tag { color: var(--bento-yellow-a); }
        #section-jalur .bento-green .bento-tag { color: var(--bento-green-a); }
        #section-jalur .bento-purple .bento-tag { color: var(--bento-purple-a); }
        #section-jalur .bento-orange .bento-tag { color: var(--bento-orange-a); }
        #section-jalur .bento-navy .bento-tag { color: var(--bento-navy-a); }

        #section-jalur .bento-title {
          font-family: "Plus Jakarta Sans", "DM Sans", system-ui, -apple-system, sans-serif;
          font-size: 20px;
          line-height: 1.15;
          font-weight: 800;
          margin-bottom: 2px;
          letter-spacing: -0.01em;
          color: var(--bento-ink);
        }

        #section-jalur .bento-sub {
          font-weight: 700;
          font-size: 12.5px;
          line-height: 1.3;
          margin-bottom: 0;
        }

        #section-jalur .bento-blue .bento-sub { color: var(--bento-blue-a); }
        #section-jalur .bento-yellow .bento-sub { color: var(--bento-yellow-a); }
        #section-jalur .bento-green .bento-sub { color: var(--bento-green-a); }
        #section-jalur .bento-purple .bento-sub { color: var(--bento-purple-a); }
        #section-jalur .bento-orange .bento-sub { color: var(--bento-orange-a); }
        #section-jalur .bento-navy .bento-sub { color: var(--bento-navy-a); }

        #section-jalur .bento-back-title {
          font-family: "Plus Jakarta Sans", "DM Sans", system-ui, -apple-system, sans-serif;
          font-size: 15.5px;
          font-weight: 800;
          line-height: 1.25;
          margin-bottom: 11px;
          color: var(--bento-ink);
          letter-spacing: -0.015em;
        }

        #section-jalur .bento-back-list {
          display: flex;
          flex-direction: column;
          gap: 8.5px;
          margin: 0;
          padding: 0;
          list-style: none;
        }

        #section-jalur .bento-back-item {
          display: flex;
          align-items: flex-start;
          gap: 7.5px;
          font-size: 11px;
          line-height: 1.35;
          font-weight: 600;
          color: #334155;
        }

        #section-jalur .bento-back-check {
          width: 14.5px;
          height: 14.5px;
          flex-shrink: 0;
          margin-top: 1px;
        }

        #section-jalur .bento-blue .bento-back-check { color: var(--bento-blue-a); }
        #section-jalur .bento-yellow .bento-back-check { color: var(--bento-yellow-a); }
        #section-jalur .bento-green .bento-back-check { color: var(--bento-green-a); }
        #section-jalur .bento-purple .bento-back-check { color: var(--bento-purple-a); }
        #section-jalur .bento-orange .bento-back-check { color: var(--bento-orange-a); }
        #section-jalur .bento-navy .bento-back-check { color: var(--bento-navy-a); }

        #section-jalur .bento-disabled {
          pointer-events: none;
          opacity: 0.6;
          cursor: not-allowed;
        }

        @media (max-width: 1024px) {
          #section-jalur .bento-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
          }
        }

        @media (max-width: 640px) {
          #section-jalur .bento-grid {
            grid-template-columns: 1fr;
            gap: 14px;
          }
          #section-jalur .bento-card {
            height: 174px;
          }
          #section-jalur .bento-card-front {
            padding: 14px 16px;
          }
          #section-jalur .bento-card-back {
            padding: 12px 14px 10px;
          }
          #section-jalur .bento-art {
            height: 74px;
            max-height: 74px;
          }
          #section-jalur .bento-title {
            font-size: 19px;
          }
        }

        @media (prefers-reduced-motion: reduce) {
          #section-jalur .bento-card-inner {
            transition: none !important;
          }
        }
        /* ============================================================== */
        /* FLOATING WHATSAPP - OUTLINE ONLY RADIO WAVE EFFECT (NO FILL)   */
        /* ============================================================== */
        @keyframes waRadioPulse {
          0% {
            transform: scale(1);
            opacity: 0.65;
            border-color: #25D366;
          }
          50% {
            opacity: 0.3;
          }
          100% {
            transform: scale(1.36);
            opacity: 0;
            border-color: rgba(37, 211, 102, 0);
          }
        }

        .wa-radio-wave {
          border: 2px solid #25D366;
          background: transparent !important;
          animation: waRadioPulse 3.6s cubic-bezier(0.15, 0.5, 0.35, 1) infinite;
        }

        .wa-radio-wave-delayed {
          border: 2px solid #25D366;
          background: transparent !important;
          animation: waRadioPulse 3.6s cubic-bezier(0.15, 0.5, 0.35, 1) infinite 1.8s;
        }

        /* Tooltip / Popover Kartu Informasi WhatsApp (Hover Meluncur Halus dari Kanan ke Kiri) */
        .wa-tooltip-wrapper {
          opacity: 0;
          visibility: hidden;
          pointer-events: none;
          transform: translate3d(8px, -50%, 0) scale(0.97);
          transition: opacity 0.2s cubic-bezier(0.4, 0, 1, 1),
                      transform 0.2s cubic-bezier(0.4, 0, 1, 1),
                      visibility 0.2s;
          will-change: transform, opacity;
          backface-visibility: hidden;
        }
        .group-wa:hover .wa-tooltip-wrapper,
        .group-wa:focus-within .wa-tooltip-wrapper {
          opacity: 1;
          visibility: visible;
          pointer-events: auto;
          transform: translate3d(0, -50%, 0) scale(1);
          transition: opacity 0.34s cubic-bezier(0.22, 1, 0.36, 1),
                      transform 0.34s cubic-bezier(0.22, 1, 0.36, 1),
                      visibility 0.34s;
        }

        /* Floating WA Container Transition saat mentok di dasar halaman */
        #floating-wa-aside {
          transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                      transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                      visibility 0.3s cubic-bezier(0.16, 1, 0.3, 1);
          will-change: transform, opacity, visibility;
        }

        #floating-wa-aside.is-hidden-at-bottom {
          opacity: 0 !important;
          visibility: hidden !important;
          pointer-events: none !important;
          transform: translate3d(0, 24px, 0) scale(0.9) !important;
        }

        @media (prefers-reduced-motion: reduce) {
          .wa-radio-wave,
          .wa-radio-wave-delayed,
          .wa-tooltip-wrapper,
          #floating-wa-aside {
            animation: none !important;
            transition: none !important;
          }
        }

        /* ============================================================== */
        /* SIMULASI KALKULATOR BIAYA MASUK SPMB 2027/2028                 */
        /* ============================================================== */
        #spmb-calc-shell .scroll-reveal-item,
        #section-footer .scroll-reveal-item,
        .no-reveal,
        [data-no-reveal] {
          opacity: 1 !important;
          translate: 0 0 !important;
          visibility: visible !important;
        }

        @keyframes spmbStepFadeIn {
          from {
            opacity: 0;
            transform: translateY(8px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .spmb-step-animate {
          animation: spmbStepFadeIn 260ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes spmbResultReveal {
          from {
            opacity: 0;
            transform: scale(0.985) translateY(10px);
          }
          to {
            opacity: 1;
            transform: scale(1) translateY(0);
          }
        }

        .spmb-result-animate {
          animation: spmbResultReveal 340ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .spmb-custom-scroll {
          scrollbar-width: thin;
          scrollbar-color: rgba(100, 116, 139, 0.35) transparent;
        }
        .spmb-custom-scroll::-webkit-scrollbar {
          width: 5px;
        }
        .spmb-custom-scroll::-webkit-scrollbar-thumb {
          background: rgba(100, 116, 139, 0.35);
          border-radius: 9999px;
        }

        @media (prefers-reduced-motion: reduce) {
          .spmb-step-animate,
          .spmb-result-animate,
          .lokasi-tooltip-wrapper {
            animation: none !important;
            transition: none !important;
          }
        }
      </style>
@endpush

@section('body_attributes')
data-active-wave="{{ $activeWave ?? 'Gelombang Khusus' }}"
@endsection

@section('content')
    @php
      $registrationClosed = ($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang';
    @endphp

      <!-- ============================================================== -->
      <!-- PORTAL GATE / BERANDA 100VH (FULL VIEWPORT GATEWAY)            -->
      <!-- ============================================================== -->
      <!-- ============================================================== -->
      <!-- 1. FLOATING GLASS PILL NAVBAR (Clean SaaS / Soft Glassmorphism)-->
      <!-- ============================================================== -->
      <header
        id="main-navbar"
        class="nav-at-top no-print"
      >
        <nav
          class="relative w-full py-3 px-4 sm:px-7 flex items-center justify-between"
        >
          <!-- Brand Logo & Name -->
          <a
            href="#hero-home-section"
            class="flex items-center gap-2.5 sm:gap-3 group shrink-0"
          >
            <div
              class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-105"
            >
              <img
                src="{{ asset('assets/img/alogo.png') }}"
                alt="Logo SMK TI Bali Global"
                class="w-full h-full object-contain filter drop-shadow-[0_2px_6px_rgba(0,0,0,0.4)]"
              />
            </div>
            <div class="flex flex-col leading-none">
              <span
                class="tracking-wider text-xs sm:text-sm font-extrabold text-blue-600"
                style="line-height: 1.1;"
              >
                SMK TI
              </span>
              <span
                id="nav-brand-subtitle"
                class="text-[10px] sm:text-[11px] font-bold text-slate-600 -mt-0.5 transition-colors"
                style="line-height: 1.15;"
              >
                Bali Global Badung
              </span>
            </div>
          </a>

          <!-- Desktop Quick Navigation Links (Dead Center 50% Presisi) -->
          <div
            id="nav-desktop-links"
            class="hidden lg:flex items-center gap-2 xl:gap-3 text-xs xl:text-sm font-medium absolute left-1/2 -translate-x-1/2"
          >
            <a href="#hero-home-section" class="nav-menu-link active">Beranda</a>
            <a href="#section-cara-daftar" class="nav-menu-link">Cara Daftar</a>
            <a href="#section-jalur" class="nav-menu-link">Jalur Masuk</a>
            <a href="#section-jadwal" class="nav-menu-link">Jadwal</a>
            <a href="#section-simulasi-biaya" class="nav-menu-link">Biaya</a>
            <a href="#section-faq" class="nav-menu-link">Bantuan</a>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <!-- Wrapper Dropdown -->
            <div class="relative group" id="user-menu-container">
            @auth
              <!-- Tampilan jika SUDAH LOGIN -->
              <button
                id="nav-user-btn-auth"
                type="button"
                class="flex items-center gap-2 sm:gap-2.5 pl-1.5 pr-3 py-1 rounded-full text-slate-800 hover:text-slate-950 cursor-pointer"
                aria-expanded="false"
                aria-haspopup="true"
              >
                <div class="w-[26px] h-[26px] sm:w-7 sm:h-7 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white text-[11px] font-bold ring-1 ring-black/10 shrink-0">
                  {{ strtoupper(substr(Auth::user()->calonSiswa->nama_lengkap ?? Auth::user()->email, 0, 1)) }}
                </div>
                <span class="text-[11px] sm:text-xs font-semibold text-slate-800 group-hover:text-slate-950 max-w-[80px] sm:max-w-[120px] truncate tracking-wide">
                  {{ Auth::user()->calonSiswa->nama_lengkap ?? (Auth::user()->isAdmin() ? 'Admin' : Auth::user()->email) }}
                </span>
                <svg
                  id="chevron-icon"
                  class="w-3.5 h-3.5 text-slate-500 group-hover:text-slate-900 transition-all duration-200 group-hover:rotate-180 shrink-0"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2.5"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#chevron-account') }}" />
                </svg>
              </button>

              <!-- POPUP DROPDOWN MENU -->
              <div
                id="user-dropdown-menu"
                class="invisible opacity-0 translate-y-1.5 scale-95 pointer-events-none group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 group-hover:scale-100 group-hover:pointer-events-auto transition-all duration-200 ease-out absolute right-0 top-[calc(100%+0.75rem)] w-72 rounded-2xl bg-white/95 text-slate-800 shadow-2xl border border-white/60 p-3.5 z-50 origin-top-right backdrop-blur-2xl ring-1 ring-black/5"
              >
                <!-- Invisible bridge -->
                <div class="absolute -top-3 left-0 right-0 h-3" aria-hidden="true"></div>

                <!-- Profile Info Header -->
                <div class="flex items-center gap-3 px-1 py-1">
                  <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-sm shrink-0 ring-2 ring-blue-500/20">
                    {{ strtoupper(substr(Auth::user()->calonSiswa->nama_lengkap ?? Auth::user()->email, 0, 1)) }}
                  </div>
                  <div class="min-w-0 flex-1">
                    <h4 class="font-bold text-sm text-slate-900 leading-tight truncate">
                      {{ Auth::user()->calonSiswa->nama_lengkap ?? (Auth::user()->isAdmin() ? 'Administrator' : 'Calon Siswa') }}
                    </h4>
                    <p class="text-xs text-slate-500 font-normal truncate mt-0.5" title="{{ Auth::user()->email }}">
                      {{ Auth::user()->email }}
                    </p>
                  </div>
                  <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    {{ Auth::user()->isAdmin() ? 'Admin' : 'Siswa' }}
                  </span>
                </div>

                <!-- Divider -->
                <div class="my-2.5 border-t border-slate-100"></div>

                <!-- Menu Items -->
                <div class="space-y-1">
                  @if(Auth::user()->isAdmin())
                    <a
                      href="{{ route('admin.dashboard') }}"
                      class="group/item flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-700 hover:text-slate-950 hover:bg-slate-100/80 transition-colors"
                    >
                      <svg class="w-4 h-4 text-blue-600 shrink-0 transition-transform duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                      </svg>
                      <span>Dasbor Panitia</span>
                    </a>
                  @else
                    @php
                      $studentRegistration = Auth::user()->calonSiswa?->latestPendaftaran;
                    @endphp
                    <a
                      href="{{ $studentRegistration ? route('pendaftaran.card') : route('pendaftaran.step1') }}"
                      class="group/item flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-700 hover:text-slate-950 hover:bg-slate-100/80 transition-colors"
                    >
                      <svg class="w-4 h-4 text-blue-600 shrink-0 transition-transform duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <span>Formulir Pendaftaran</span>
                    </a>
                  @endif

                  <a
                    href="{{ Auth::user()->isAdmin() ? route('admin.password.edit') : route('pendaftaran.password.edit') }}"
                    class="group/item flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-700 hover:text-slate-950 hover:bg-slate-100/80 transition-colors"
                  >
                    <svg class="w-4 h-4 text-blue-600 shrink-0 transition-transform duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Ganti kata sandi</span>
                  </a>

                  <!-- Divider -->
                  <div class="my-2 border-t border-slate-100"></div>

                  <!-- Keluar (Logout) -->  
                  <form id="landing-user-logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                      type="button"
                      onclick="openLogoutConfirmModal('landing-user-logout-form')"
                      class="group/item w-full flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors cursor-pointer text-left"
                    >
                      <svg class="w-4 h-4 text-rose-500 shrink-0 transition-transform duration-200 group-hover/item:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                      </svg>
                      <span>Keluar</span>
                    </button>
                  </form>
                </div>
              </div>
            @else
              <!-- Tampilan jika BELUM LOGIN -->
              <div class="flex items-center gap-1.5 sm:gap-2">
                <a
                  href="{{ route('login') }}"
                  class="hidden lg:inline-flex px-3 py-1.5 rounded-full text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-950 transition-colors duration-200 items-center gap-1.5 cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5 shrink-0 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <use href="{{ asset('assets/img/icon/sprite.svg#user-login') }}" />
                  </svg>
                  <span>Login</span>
                </a>
                @if ($registrationClosed) 
                  <span
                    aria-disabled="true"
                    class="px-3.5 py-1.5 rounded-full text-xs sm:text-sm font-bold text-slate-400 bg-slate-200/80 border border-slate-300/80 cursor-not-allowed"
                  >
                    Pendaftaran Ditutup
                  </span>
                @else
                  <a
                    href="{{ route('register') }}"
                    class="px-4 py-1.5 rounded-full text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 transition-all shadow-md shadow-blue-600/25 hover:shadow-lg hover:shadow-blue-600/35 hover:scale-[1.02] active:scale-[0.98] flex items-center gap-1.5 cursor-pointer"
                  >
                    <span>Daftar</span>
                  </a>
                @endif
              </div>
            @endauth
            </div>

            <button
              id="nav-mobile-toggle"
              type="button"
              onclick="event.stopPropagation(); toggleMobileNavigation()"
              class="lg:hidden shrink-0 w-[34px] h-[34px] inline-flex items-center justify-center rounded-full border border-white/60 bg-white/70 hover:bg-white text-slate-700 hover:text-slate-950 transition"
              aria-label="Buka navigasi"
              aria-expanded="false"
              aria-controls="nav-mobile-menu"
            >
              <svg id="nav-mobile-icon" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </nav>

        <!-- Mobile Dropdown Menu -->
        <div
          id="nav-mobile-menu"
          class="hidden lg:hidden absolute top-[calc(100%+0.5rem)] inset-x-0 rounded-2xl sm:rounded-3xl border border-white/60 bg-white/95 p-2.5 shadow-2xl backdrop-blur-2xl ring-1 ring-black/5"
        >
          <a href="#hero-home-section" class="nav-mobile-link active">Beranda</a>
          <a href="#section-cara-daftar" class="nav-mobile-link">Alur Daftar</a>
          <a href="#section-jalur" class="nav-mobile-link">Jalur Masuk</a>
          <a href="#section-jadwal" class="nav-mobile-link">Jadwal</a>
          <a href="#section-simulasi-biaya" class="nav-mobile-link">Biaya</a>
          <a href="#section-faq" class="nav-mobile-link">Bantuan</a>
          @guest
            <a
              href="{{ route('login') }}"
              class="flex items-center gap-2.5 mt-1 border-t border-slate-100 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100/80 hover:text-slate-900 transition"
            >
              <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <use href="{{ asset('assets/img/icon/sprite.svg#user-login') }}" />
              </svg>
              <span>Login</span>
            </a>
          @endguest
        </div>
      </header>
      <div
        id="nav-mobile-backdrop"
        class="fixed inset-0 z-[35] hidden bg-black/60 backdrop-blur-[1px] lg:hidden"
        aria-hidden="true"
        onclick="event.stopPropagation(); toggleMobileNavigation()"
      ></div>

      <!-- ============================================================== -->
      <!-- 2. SECTION HERO (Tetap Dipertahankan dari Desain Sebelumnya)   -->
      <!-- ============================================================== -->
      <section
        id="hero-home-section"
        class="min-h-screen flex flex-col justify-center relative overflow-hidden py-12 sm:py-16 xl:py-20 bg-white no-print"
      >
        <!-- Background Layers with Color Grading & Overlays -->
        <div class="absolute inset-0 pointer-events-none z-0" aria-hidden="true">
          <!-- 1. Foto gedung sekolah dengan color grading lebih hidup, hangat & tajam -->
          <div class="hero-bg-image absolute inset-0 w-full h-full bg-cover bg-no-repeat"></div>

          <!-- 2. Dark gradient overlay dengan gradasi dark slate & backdrop blur -->
          <div class="hero-gradient-overlay absolute inset-0 backdrop-blur-[2px]"></div>

          <!-- 3. Radial vignette gelap di area tengah -->
          <div class="hero-vignette-overlay absolute inset-0"></div>

          <!-- 4. Soft Edge Blending: kanan & kiri mulai dari tengah dari 0 ke dasar hero putih -->
          <div class="hero-edge-blend absolute inset-0"></div>
        </div>

        <!-- Hero Main Container (Relative for floating badges & centered text) -->
        <div class="relative w-full max-w-7xl 2xl:max-w-[1440px] mx-auto px-4 sm:px-6 xl:px-8 2xl:px-12 flex-1 flex flex-col justify-center items-center my-auto">

          <!-- Main Centered Content -->
          <main
            class="relative flex-1 flex flex-col items-center justify-center text-center px-4 sm:px-6 w-full max-w-7xl mx-auto z-10 my-auto"
          >
            <!-- Bounding Container -->
            <div
              class="relative w-full max-w-4xl lg:max-w-4xl xl:max-w-5xl 2xl:max-w-6xl mx-auto flex flex-col items-center justify-center py-4 sm:py-6"
            >
            <!-- B. Judul Utama (Hero Title) & Subjudul Singkat -->
            <div
              class="flex flex-col items-center max-w-3xl xl:max-w-4xl 2xl:max-w-5xl mx-auto w-full"
            >
              <!-- Sub-judul / Eyebrow (Pill Badge Modern) -->
              <div
                class="hero-pill-badge inline-flex items-center justify-center mx-auto text-xs sm:text-sm font-semibold tracking-wide select-none"
                style="padding: 6px 16px; border-radius: 9999px; background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); color: #7DD3FC; font-weight: 600; margin-bottom: 16px;"
              >
                @if ($registrationClosed)
                  Pendaftaran sedang ditutup
                @else
                  {{ $activeWave ?? 'Gelombang Khusus' }} sedang dibuka
                @endif
              </div>

              <h1
                class="hero-title mx-auto text-center text-4xl sm:text-5xl lg:text-6xl xl:text-7xl leading-[1.15] text-white tracking-tight"
                style="line-height: 1.15; text-shadow: none; filter: none;"
              >
                <span class="block" style="line-height: 1.15; text-shadow: none; filter: none;">SMK TI Bali Global</span>
                <span class="block" style="line-height: 1.15; text-shadow: none; filter: none;">Badung</span>
              </h1>
              <p
                class="hero-description mt-3 sm:mt-4 text-sm sm:text-base lg:text-lg text-slate-100 max-w-[640px] mx-auto leading-relaxed font-normal text-center"
                style="max-width: 640px; text-align: center; text-shadow: none; filter: none;"
              >
                Sekolah kejuruan IT yang mencetak talenta digital berdaya saing global melalui kurikulum industri dan program Teaching Factory unggul di Bali.
              </p>
            </div>

            <!-- C. Tombol Aksi Utama (CTAs) -->
            <div
              class="mt-6 sm:mt-8 mb-3 flex flex-col sm:flex-row items-center justify-center gap-3.5 sm:gap-4 w-full max-w-xl mx-auto"
            >
              <!-- Tombol 1: Daftar Sekarang -->
              @if (($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang')
                <button
                  type="button"
                  onclick="if (typeof window.showToast === 'function') { window.showToast('Pendaftaran Sistem Penerimaan Murid Baru saat ini sedang ditutup. Silakan pantau pengumuman gelombang berikutnya pada jadwal pendaftaran di bawah.', 'warning'); } else { alert('Pendaftaran Sistem Penerimaan Murid Baru saat ini sedang ditutup.'); }"
                  id="hero-btn-daftar"
                  class="w-full sm:w-auto min-w-56 px-5 sm:px-8 py-3 sm:py-3.5 bg-slate-800/80 hover:bg-slate-800 text-slate-300 text-sm sm:text-base font-bold rounded-xl border border-white/10 shadow-lg backdrop-blur-md transition flex items-center justify-center gap-2 cursor-pointer"
                >
                  <span>Pendaftaran Ditutup</span>
                </button>
              @else
                @php
                  $userStudentReg = Auth::check() ? Auth::user()->calonSiswa?->latestPendaftaran : null;
                @endphp
                @if ($userStudentReg)
                  <a
                    href="{{ route('pendaftaran.card') }}"
                    id="hero-btn-daftar"
                    class="w-full sm:w-auto min-w-56 px-5 sm:px-8 py-3 sm:py-3.5 bg-blue-600 hover:bg-blue-500 active:scale-[0.98] text-white text-sm sm:text-base font-bold rounded-xl shadow-lg shadow-blue-600/40 hover:shadow-xl hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
                  >
                    <span id="hero-btn-daftar-text">CEK STATUS</span>
                    <svg class="w-[16.5px] h-[16.5px] shrink-0 translate-y-[0.5px] -translate-x-[1.5px] transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <use href="{{ asset('assets/img/icon/sprite.svg#arrow-up-right') }}" />
                    </svg>
                  </a>
                @else
                  <a
                    href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth"
                    id="hero-btn-daftar"
                    class="w-full sm:w-auto min-w-56 px-5 sm:px-8 py-3 sm:py-3.5 bg-blue-600 hover:bg-blue-500 active:scale-[0.98] text-white text-sm sm:text-base font-bold rounded-xl shadow-lg shadow-blue-600/40 hover:shadow-xl hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
                  >
                    <span id="hero-btn-daftar-text">Daftar Sekarang</span>
                    <svg class="w-[16.5px] h-[16.5px] shrink-0 translate-y-[0.5px] -translate-x-[1.5px] transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <use href="{{ asset('assets/img/icon/sprite.svg#arrow-up-right') }}" />
                    </svg>
                  </a>
                @endif
              @endif
              <a
                href="{{ route('panduan.download') }}" 
                download="Panduan-dan-Rincian-Biaya-Pendaftaran-SMK-TI-Bali-Global-Badung.pdf"
                id="hero-btn-panduan"
                class="w-full sm:w-auto min-w-56 px-5 sm:px-8 py-3 sm:py-3.5 bg-white/15 hover:bg-white/25 active:scale-[0.98] text-white text-sm sm:text-base font-semibold rounded-xl border border-white/40 shadow-lg shadow-black/10 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2.5 cursor-pointer select-none whitespace-nowrap relative overflow-hidden"
                aria-live="polite"
              >
                <!-- Shimmer Sweep Line during Loading -->
                <span id="hero-btn-panduan-shimmer" class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/25 to-transparent pointer-events-none hidden" aria-hidden="true"></span>

                <!-- Icon Slot -->
                <span id="hero-btn-panduan-icon-wrap" class="relative w-4 h-4 shrink-0 flex items-center justify-center">
                  <!-- State 1: Download Icon (Idle) -->
                  <svg id="hero-panduan-icon-idle" class="w-4 h-4 shrink-0 -translate-y-px transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                  </svg>

                  <!-- State 2: Spinner (Loading) -->
                  <svg id="hero-panduan-icon-loading" class="w-4 h-4 shrink-0 -translate-y-px text-white animate-spin panduan-spinner hidden" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3.5"></circle>
                    <path class="opacity-95" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>

                  <!-- State 3: Checkmark (Success) -->
                  <svg id="hero-panduan-icon-success" class="w-4 h-4 shrink-0 -translate-y-px text-blue-400 transition-transform scale-110 duration-200 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                  </svg>
                </span>

                <!-- Text Slot with smooth transform & opacity transition -->
                <span id="hero-btn-panduan-text" class="transition-all duration-150 inline-block">Panduan Daftar</span>
              </a>
            </div>
          </div>
        </main>
      </div>    
      </section>

      <!-- ============================================================== -->
      <!-- 3. SECTION CARA PENDAFTARAN (Grid 2x2)                         -->
      <!-- ============================================================== -->
      <section
        id="section-cara-daftar"
        class="pt-12 sm:pt-14 pb-10 sm:pb-12 px-4 sm:px-6 lg:px-8 bg-white text-slate-900 relative no-print"
      >
        <div class="max-w-4xl mx-auto">
          <!-- Section Header -->
          <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
            <h2
              class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900"
            >
              Cara Pendaftaran
            </h2>
          </div>

          <!-- Grid Cards (2 Pilihan Pendaftaran) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
            <!-- Kartu 1: Daftar Online -->
            <div
              class="cara-daftar-card group rounded-2xl sm:rounded-3xl p-4 sm:p-5 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
            >
              <div>
                <!-- Gambar Thumbnail Slider -->
                <div class="cara-slider-container w-full aspect-[16/10] rounded-xl sm:rounded-2xl overflow-hidden bg-slate-100 mb-4 relative shrink-0 select-none shadow-[0_0_16px_rgba(15,23,42,0.08)]" data-slider="online">
                  <div class="cara-slide absolute inset-0 w-full h-full overflow-hidden">
                    <img
                      src="{{ asset('assets/img/cara-daftar-online-step-1.jpg') }}"
                      alt="Calon Siswa Mengisi Formulir Pendaftaran Online SPMB"
                      class="cara-slide-img w-full h-full object-cover object-center"
                      loading="lazy"
                    />
                    <!-- Blend halus di bawah gambar -->
                    <div class="absolute inset-x-0 bottom-0 h-16 sm:h-20 bg-gradient-to-t from-slate-950/75 via-slate-950/30 to-transparent pointer-events-none z-10"></div>
                    <!-- Teks Step & Lingkaran Angka -->
                    <div class="absolute bottom-2.5 sm:bottom-3 left-3 sm:left-4 right-3 sm:right-4 flex items-center gap-2 z-20 pointer-events-none">
                      <span class="w-4 h-4 sm:w-[17px] sm:h-[17px] rounded-full border border-slate-300/80 text-slate-300 font-semibold text-[9px] sm:text-[10px] flex items-center justify-center shrink-0">1</span>
                      <span class="text-[11px] sm:text-xs font-medium text-slate-300 truncate">Buat Akun &amp; Isi Formulir</span>
                    </div>
                  </div>
                  <div class="cara-slide absolute inset-0 w-full h-full overflow-hidden">
                    <img
                      src="{{ asset('assets/img/cara-daftar-online-step-2.jpg') }}"
                      alt="Calon Siswa Menyiapkan dan Mengunggah Berkas Persyaratan"
                      class="cara-slide-img w-full h-full object-cover object-center"
                      loading="lazy"
                    />
                    <!-- Blend halus di bawah gambar -->
                    <div class="absolute inset-x-0 bottom-0 h-16 sm:h-20 bg-gradient-to-t from-slate-950/75 via-slate-950/30 to-transparent pointer-events-none z-10"></div>
                    <!-- Teks Step & Lingkaran Angka   -->
                    <div class="absolute bottom-2.5 sm:bottom-3 left-3 sm:left-4 right-3 sm:right-4 flex items-center gap-2 z-20 pointer-events-none">
                      <span class="w-4 h-4 sm:w-[17px] sm:h-[17px] rounded-full border border-slate-300/80 text-slate-300 font-semibold text-[9px] sm:text-[10px] flex items-center justify-center shrink-0">2</span>
                      <span class="text-[11px] sm:text-xs font-medium text-slate-300 truncate">Unggah Berkas Persyaratan</span>
                    </div>
                  </div>
                  <div class="cara-slide absolute inset-0 w-full h-full overflow-hidden">
                    <img
                      src="{{ asset('assets/img/cara-daftar-online-step-3.jpg') }}"
                      alt="Verifikasi Berhasil dan Daftar Ulang Siswa Baru"
                      class="cara-slide-img w-full h-full object-cover object-center"
                      loading="lazy"
                    />
                    <!-- Blend halus di bawah gambar -->
                    <div class="absolute inset-x-0 bottom-0 h-16 sm:h-20 bg-gradient-to-t from-slate-950/75 via-slate-950/30 to-transparent pointer-events-none z-10"></div>
                    <!-- Teks Step & Lingkaran Angka -->
                    <div class="absolute bottom-2.5 sm:bottom-3 left-3 sm:left-4 right-3 sm:right-4 flex items-center gap-2 z-20 pointer-events-none">
                      <span class="w-4 h-4 sm:w-[17px] sm:h-[17px] rounded-full border border-slate-300/80 text-slate-300 font-semibold text-[9px] sm:text-[10px] flex items-center justify-center shrink-0">3</span>
                      <span class="text-[11px] sm:text-xs font-medium text-slate-300 truncate">Verifikasi &amp; Daftar Ulang</span>
                    </div>
                  </div>
                </div>

                <!-- Judul & Tombol Minimalis Kiri Kanan -->
                <div class="flex items-center justify-between gap-2 mb-2">
                  <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight leading-snug group-hover:text-[#2563EB] transition-colors">
                    Daftar Online
                  </h3>
                  <div class="flex items-center gap-1 shrink-0">
                    <button
                      type="button"
                      class="cara-slider-prev w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full border border-slate-200/90 bg-transparent text-slate-400 hover:text-[#2563EB] hover:border-[#2563EB]/40 active:scale-95 transition-all cursor-pointer touch-manipulation select-none"
                      aria-label="Gambar sebelumnya"
                      title="Gambar sebelumnya"
                      data-slider-target="online"
                    >
                      <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                      </svg>
                    </button>
                    <button
                      type="button"
                      class="cara-slider-next w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full border border-slate-200/90 bg-transparent text-slate-400 hover:text-[#2563EB] hover:border-[#2563EB]/40 active:scale-95 transition-all cursor-pointer touch-manipulation select-none"
                      aria-label="Gambar selanjutnya"
                      title="Gambar selanjutnya"
                      data-slider-target="online"
                    >
                      <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                      </svg>
                    </button>
                  </div>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                  Lakukan pendaftaran mandiri melalui website SPMB kapan saja dengan membuat akun dan mengisi formulir online.
                </p>
              </div>

              <div>
                @if ($registrationClosed)
                  <span class="inline-flex items-center px-6 py-2.5 bg-slate-200 text-slate-500 text-sm sm:text-base font-bold rounded-full cursor-not-allowed" aria-disabled="true">
                    Pendaftaran Ditutup
                  </span>
                @else
                  <a
                    href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-sm sm:text-base font-bold rounded-full transition cursor-pointer shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30"
                  >Daftar Sekarang
                  </a>
                @endif
              </div>
            </div>

            <!-- Kartu 2: Daftar Offline -->
            <div
              class="cara-daftar-card group relative rounded-2xl sm:rounded-3xl p-4 sm:p-5 hover:-translate-y-1 hover:z-20 transition-all duration-300 flex flex-col justify-between"
            >
              <div>
                <!-- Gambar Thumbnail Slider -->
                <div class="cara-slider-container w-full aspect-[16/10] rounded-xl sm:rounded-2xl overflow-hidden bg-slate-100 mb-4 relative shrink-0 select-none shadow-[0_0_16px_rgba(15,23,42,0.08)]" data-slider="offline">
                  <div class="cara-slide absolute inset-0 w-full h-full overflow-hidden">
                    <img
                      src="{{ asset('assets/img/hero-bgs.jpg') }}"
                      alt="Gedung Kampus SMK TI Bali Global Badung"
                      class="cara-slide-img w-full h-full object-cover object-center"
                      loading="lazy"
                    />
                    <!-- Blend halus di bawah gambar -->
                    <div class="absolute inset-x-0 bottom-0 h-16 sm:h-20 bg-gradient-to-t from-slate-950/75 via-slate-950/30 to-transparent pointer-events-none z-10"></div>
                    <!-- Teks Step & Lingkaran Angka -->
                    <div class="absolute bottom-2.5 sm:bottom-3 left-3 sm:left-4 right-3 sm:right-4 flex items-center gap-2 z-20 pointer-events-none">
                      <span class="w-4 h-4 sm:w-[17px] sm:h-[17px] rounded-full border border-slate-300/80 text-slate-300 font-semibold text-[9px] sm:text-[10px] flex items-center justify-center shrink-0">1</span>
                      <span class="text-[11px] sm:text-xs font-medium text-slate-300 truncate">Kunjungi Sekolah</span> 
                    </div>
                  </div>
                  <div class="cara-slide absolute inset-0 w-full h-full overflow-hidden">
                    <img
                      src="{{ asset('assets/img/cara-daftar-offline-step-2.jpg') }}"
                      alt="Front Office dan Meja Layanan Informasi Pendaftaran"
                      class="cara-slide-img w-full h-full object-cover object-center"
                      loading="lazy"
                    />
                    <!-- Blend halus di bawah gambar -->
                    <div class="absolute inset-x-0 bottom-0 h-16 sm:h-20 bg-gradient-to-t from-slate-950/75 via-slate-950/30 to-transparent pointer-events-none z-10"></div>
                    <!-- Teks Step & Lingkaran Angka -->
                    <div class="absolute bottom-2.5 sm:bottom-3 left-3 sm:left-4 right-3 sm:right-4 flex items-center gap-2 z-20 pointer-events-none">
                      <span class="w-4 h-4 sm:w-[17px] sm:h-[17px] rounded-full border border-slate-300/80 text-slate-300 font-semibold text-[9px] sm:text-[10px] flex items-center justify-center shrink-0">2</span>
                      <span class="text-[11px] sm:text-xs font-medium text-slate-300 truncate">Menuju Front Office</span>
                    </div>
                  </div>
                  <div class="cara-slide absolute inset-0 w-full h-full overflow-hidden">
                    <img
                      src="{{ asset('assets/img/cara-daftar-offline-step-3.jpg') }}"
                      alt="Panitia Pendaftaran Membimbing Pengisian Formulir Siswa"
                      class="cara-slide-img w-full h-full object-cover object-center"
                      loading="lazy"
                    />
                    <!-- Blend halus di bawah gambar -->
                    <div class="absolute inset-x-0 bottom-0 h-16 sm:h-20 bg-gradient-to-t from-slate-950/75 via-slate-950/30 to-transparent pointer-events-none z-10"></div>
                    <!-- Teks Step & Lingkaran Angka -->
                    <div class="absolute bottom-2.5 sm:bottom-3 left-3 sm:left-4 right-3 sm:right-4 flex items-center gap-2 z-20 pointer-events-none">
                      <span class="w-4 h-4 sm:w-[17px] sm:h-[17px] rounded-full border border-slate-300/80 text-slate-300 font-semibold text-[9px] sm:text-[10px] flex items-center justify-center shrink-0">3</span>
                      <span class="text-[11px] sm:text-xs font-medium text-slate-300 truncate">Isi Formulir Dibantu Panitia</span>
                    </div>
                  </div>
                </div>

                <!-- Judul & Tombol Minimalis Kiri Kanan -->
                <div class="flex items-center justify-between gap-2 mb-2">
                  <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight leading-snug group-hover:text-[#2563EB] transition-colors">
                    Daftar Offline
                  </h3>
                  <div class="flex items-center gap-1 shrink-0">
                    <button
                      type="button"
                      class="cara-slider-prev w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full border border-slate-200/90 bg-transparent text-slate-400 hover:text-[#2563EB] hover:border-[#2563EB]/40 active:scale-95 transition-all cursor-pointer touch-manipulation select-none"
                      aria-label="Gambar sebelumnya"
                      title="Gambar sebelumnya"
                      data-slider-target="offline"
                    >
                      <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                      </svg>
                    </button>
                    <button
                      type="button"
                      class="cara-slider-next w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full border border-slate-200/90 bg-transparent text-slate-400 hover:text-[#2563EB] hover:border-[#2563EB]/40 active:scale-95 transition-all cursor-pointer touch-manipulation select-none"
                      aria-label="Gambar selanjutnya" 
                      title="Gambar selanjutnya"
                      data-slider-target="offline"
                    >
                      <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                      </svg>
                    </button>
                  </div>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                  Lakukan pendaftaran langsung di sekolah melalui Front Office dengan dibantu panitia mengisi formulir.
                </p>
              </div>

              <div class="relative inline-block w-full sm:w-auto z-20">
                <!-- Tombol Lihat Lokasi (Hanya tombol ini yang menjadi trigger hover) -->
                <a
                  id="btn-lihat-lokasi"
                  href="https://maps.google.com/?q=SMK+TI+Bali+Global+Badung+Jl.+Tibungsari+No.+1+Dalung+Kuta+Utara+Badung+Bali"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-describedby="tooltip-berkas-offline"
                  class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#EFF6FF] hover:bg-[#DBEAFE] border-[1.5px] border-[#BFDBFE] text-[#2563EB] text-sm sm:text-base font-bold rounded-full transition cursor-pointer"
                >
                  <svg
                    class="w-4 h-4 text-[#2563EB]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.2"
                    viewBox="0 0 24 24"
                  >
                    <use href="{{ asset('assets/img/icon/sprite.svg#map-pin') }}" />
                  </svg>
                  <span>Lihat Lokasi</span>
                </a>

                <!-- Popover / Tooltip Informasi Berkas Offline -->
                <div
                  id="tooltip-berkas-offline"
                  role="tooltip"
                  class="lokasi-tooltip-wrapper pointer-events-none absolute bottom-full left-0 right-auto w-max max-w-[260px] sm:w-[260px] pb-2 z-30"
                >
                  <div class="relative bg-white rounded-xl pl-3.5 pr-0 py-2.5 shadow-[0_12px_28px_-6px_rgba(15,23,42,0.16)] border border-slate-200 text-left">
                    <!-- Header Badge & Judul -->
                    <div class="flex items-center gap-1.5 mb-1.5">
                      <span class="w-5 h-5 rounded-md bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                      </span>
                      <span class="text-[10.5px] font-bold uppercase tracking-wider text-blue-600">Berkas Daftar Offline</span>
                    </div>

                    <p class="text-xs text-slate-700 leading-snug mb-1.5">
                      Bawa saat datang langsung ke sekolah:
                    </p>

                    <!-- List Berkas -->
                    <ul class="space-y-1 text-xs text-slate-600">
                      <li class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                          <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                          </svg>
                        </span>
                        <span>Fotokopi Kartu Keluarga (KK)</span>
                      </li>
                      <li class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                          <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                          </svg>
                        </span>
                        <span>Fotokopi Akta Kelahiran</span>
                      </li>
                      <li class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                          <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                          </svg>
                        </span>
                        <span>Pasfoto terbaru (3&times;4)</span>
                      </li>
                    </ul>

                    <!-- Panah / Caret Pointer -->
                    <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 sm:left-10 sm:translate-x-0 w-3 h-3 bg-white border-b border-r border-slate-200 rotate-45"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- 4. SECTION JALUR PENDAFTARAN (Bento Grid)                      -->
      <!-- ============================================================== -->
      <section
        id="section-jalur"
        class="relative z-10 py-12 sm:py-14 px-4 sm:px-6 lg:px-8 bg-white no-print"
      >
        <div class="bento-wrap">
          <!-- SVG Symbols Defs -->
          <svg width="0" height="0" class="sr-only" aria-hidden="true">
            <defs>
              <polygon id="st" points="0,-9 2.6,-3 9,-2.8 4,1.5 5.6,8 0,4.5 -5.6,8 -4,1.5 -9,-2.8 -2.6,-3"/>
            </defs>
          </svg>

          <!-- Section Header -->
          <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <h2
              class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900"
            >
              Jalur Pendaftaran
            </h2>
          </div>

          <div class="bento-grid">
            <!-- 1. Umum (Jalur 01) -->
            <a
              @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step2') }}?jalur=umum @else {{ route('register') }}?jalur=umum @endauth" @else aria-disabled="true" tabindex="-1" @endunless
              class="bento-card bento-blue group {{ $registrationClosed ? 'bento-disabled' : '' }}"
              aria-label="Pilih Jalur Umum"
              onclick="if(window.matchMedia('(hover: none)').matches && !this.classList.contains('is-flipped')){event.preventDefault();document.querySelectorAll('.bento-card.is-flipped').forEach(c=>c!==this&&c.classList.remove('is-flipped'));this.classList.toggle('is-flipped');}"
            >
              <div class="bento-card-inner">
                <!-- SISI DEPAN -->
                <div class="bento-card-front">
                  <svg class="bento-art" viewBox="0 0 340 150" role="img" aria-label="Ilustrasi sekelompok siswa"><ellipse cx="170" cy="138" rx="150" ry="10" fill="#A9CBF3"/><rect x="16" y="122" width="40" height="8" rx="2" fill="#F2B705"/><rect x="20" y="114" width="34" height="8" rx="2" fill="#fff"/><path d="M62 138v-16a28 28 0 0 1 56 0v16z" fill="#1B2559"/><circle cx="90" cy="78" r="16" fill="#F2C9A5"/><path d="M74 76a16 16 0 0 1 32 0z" fill="#3B2A20"/><path d="M222 138v-16a28 28 0 0 1 56 0v16z" fill="#1F8A4C"/><circle cx="250" cy="78" r="16" fill="#C98F6A"/><path d="M234 76a16 16 0 0 1 32 0z" fill="#2A1B14"/><path d="M135 138v-18a35 35 0 0 1 70 0v18z" fill="#6B4FD3"/><circle cx="170" cy="64" r="19" fill="#E0A97E"/><path d="M151 62a19 19 0 0 1 38 0z" fill="#3B2A20"/><polygon points="140,44 170,32 200,44 170,56" fill="#1B2559"/><path d="M198 45v14" stroke="#F2B705" stroke-width="3"/><g fill="#1B2559"><circle cx="85" cy="80" r="1.8"/><circle cx="95" cy="80" r="1.8"/><circle cx="164" cy="68" r="2"/><circle cx="176" cy="68" r="2"/><circle cx="245" cy="80" r="1.8"/><circle cx="255" cy="80" r="1.8"/></g><use href="#st" transform="translate(300 46) scale(1.5)" fill="#fff"/><use href="#st" transform="translate(38 56) scale(1.2)" fill="#6B4FD3"/></svg>
                  <div class="bento-card-info">
                    <span class="bento-tag">Jalur 01</span>
                    <h3 class="bento-title">Umum</h3>
                    <span class="bento-sub">Reguler</span>
                  </div>
                </div>
                <!-- SISI BELAKANG -->
                <div class="bento-card-back">
                  <h4 class="bento-back-title">Jalur Umum</h4>
                  <ul class="bento-back-list">
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan SPP s.d. 15% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan DPP s.d. 50% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Tambahan Diskon Jurusan 5% – 10%</span>
                    </li>
                  </ul>
                </div>
              </div>
            </a>

            <!-- 2. KIP (Jalur 02) -->
            <a
              @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step2') }}?jalur=kip @else {{ route('register') }}?jalur=kip @endauth" @else aria-disabled="true" tabindex="-1" @endunless
              class="bento-card bento-yellow group {{ $registrationClosed ? 'bento-disabled' : '' }}"
              aria-label="Pilih Jalur KIP"
              onclick="if(window.matchMedia('(hover: none)').matches && !this.classList.contains('is-flipped')){event.preventDefault();document.querySelectorAll('.bento-card.is-flipped').forEach(c=>c!==this&&c.classList.remove('is-flipped'));this.classList.toggle('is-flipped');}"
            >
              <div class="bento-card-inner">
                <!-- SISI DEPAN -->
                <div class="bento-card-front">
                  <svg class="bento-art" viewBox="0 0 220 150" role="img" aria-label="Ilustrasi kartu dan toga"><circle cx="110" cy="78" r="62" fill="#F6D766"/><g transform="rotate(-8 100 90)"><rect x="36" y="50" width="130" height="84" rx="12" fill="#1B2559"/><rect x="50" y="68" width="26" height="20" rx="4" fill="#F2B705"/><rect x="50" y="102" width="80" height="7" rx="3" fill="#5B6BB5"/><rect x="50" y="116" width="50" height="7" rx="3" fill="#5B6BB5"/><circle cx="142" cy="76" r="12" fill="#9CC4F2"/></g><path d="M166 44v14c0 8 40 8 40 0V44z" fill="#4A33A8"/><polygon points="150,38 186,24 222,38 186,52" fill="#6B4FD3"/><path d="M218 39v16" stroke="#fff" stroke-width="3"/><use href="#st" transform="translate(30 40) scale(1.2)" fill="#fff"/></svg>
                  <div class="bento-card-info">
                    <span class="bento-tag">Jalur 02</span>
                    <h3 class="bento-title">KIP</h3>
                    <span class="bento-sub">Kartu Indonesia Pintar</span>
                  </div>
                </div>
                <!-- SISI BELAKANG -->
                <div class="bento-card-back">
                  <h4 class="bento-back-title">Jalur KIP</h4>
                  <ul class="bento-back-list">
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan SPP 15% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan DPP 50% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Tambahan Diskon Jurusan 5% – 10%</span>
                    </li>
                  </ul>
                </div>
              </div>
            </a>

            <!-- 3. Afirmasi (Jalur 03) -->
            <a
              @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step2') }}?jalur=afirmasi @else {{ route('register') }}?jalur=afirmasi @endauth" @else aria-disabled="true" tabindex="-1" @endunless
              class="bento-card bento-green group {{ $registrationClosed ? 'bento-disabled' : '' }}"
              aria-label="Pilih Jalur Afirmasi"
              onclick="if(window.matchMedia('(hover: none)').matches && !this.classList.contains('is-flipped')){event.preventDefault();document.querySelectorAll('.bento-card.is-flipped').forEach(c=>c!==this&&c.classList.remove('is-flipped'));this.classList.toggle('is-flipped');}"
            >
              <div class="bento-card-inner">
                <!-- SISI DEPAN -->
                <div class="bento-card-front">
                  <svg class="bento-art" viewBox="0 0 200 150" role="img" aria-label="Ilustrasi buku dan hati"><circle cx="100" cy="78" r="62" fill="#A9DDB8"/><path d="M100 138C80 128 58 126 38 130V92c20-4 42-2 62 8z" fill="#fff"/><path d="M100 138c20-10 42-12 62-8V92c-20-4-42-2-62 8z" fill="#E8F6EC"/><path d="M100 100v38" stroke="#8FD3A5" stroke-width="3"/><path d="M100 52c-9-13-28-7-28 8 0 11 17 20 28 27 11-7 28-16 28-27 0-15-19-21-28-8z" fill="#1F8A4C"/><path d="M82 54c2-6 9-8 13-4" fill="none" stroke="#8FD3A5" stroke-width="4" stroke-linecap="round"/><use href="#st" transform="translate(38 44) scale(1.1)" fill="#fff"/><use href="#st" transform="translate(166 40) scale(1.3)" fill="#F2B705"/></svg>
                  <div class="bento-card-info">
                    <span class="bento-tag">Jalur 03</span>
                    <h3 class="bento-title">Afirmasi</h3>
                    <span class="bento-sub">Keluarga Prasejahtera</span>
                  </div>
                </div>
                <!-- SISI BELAKANG -->
                <div class="bento-card-back">
                  <h4 class="bento-back-title">Jalur Afirmasi</h4>
                  <ul class="bento-back-list">
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan SPP 15% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan DPP 50% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Tambahan Diskon Jurusan 5% – 10%</span>
                    </li>
                  </ul>
                </div>
              </div>
            </a>

            <!-- 4. Prestasi (Jalur 04) -->
            <a
              @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step2') }}?jalur=prestasi @else {{ route('register') }}?jalur=prestasi @endauth" @else aria-disabled="true" tabindex="-1" @endunless
              class="bento-card bento-purple group {{ $registrationClosed ? 'bento-disabled' : '' }}"
              aria-label="Pilih Jalur Prestasi"
              onclick="if(window.matchMedia('(hover: none)').matches && !this.classList.contains('is-flipped')){event.preventDefault();document.querySelectorAll('.bento-card.is-flipped').forEach(c=>c!==this&&c.classList.remove('is-flipped'));this.classList.toggle('is-flipped');}"
            >
              <div class="bento-card-inner">
                <!-- SISI DEPAN -->
                <div class="bento-card-front">
                  <svg class="bento-art" viewBox="0 0 280 170" role="img" aria-label="Ilustrasi piala dan medali"><circle cx="140" cy="92" r="72" fill="#C9BBF5"/><path d="M100 52H84a4 4 0 0 0-4 4c0 20 12 32 26 34M180 52h16a4 4 0 0 1 4 4c0 20-12 32-26 34" fill="none" stroke="#F2B705" stroke-width="8" stroke-linecap="round"/><path d="M100 40h80v40a40 40 0 0 1-80 0z" fill="#F2B705"/><path d="M110 48h10v36a32 32 0 0 1-10-8z" fill="#FFE27A"/><rect x="132" y="118" width="16" height="18" fill="#D99A00"/><rect x="108" y="134" width="64" height="14" rx="5" fill="#1B2559"/><use href="#st" transform="translate(140 72) scale(1.7)" fill="#fff"/><path d="M210 78l12 18 12-18z" fill="#1B2559"/><circle cx="222" cy="112" r="18" fill="#2F74D0"/><circle cx="222" cy="112" r="10" fill="#9CC4F2"/><use href="#st" transform="translate(58 50) scale(1.3)" fill="#fff"/><use href="#st" transform="translate(228 44) scale(1.2)" fill="#6B4FD3"/><use href="#st" transform="translate(52 122) scale(.9)" fill="#fff"/></svg>
                  <div class="bento-card-info">
                    <span class="bento-tag">Jalur 04</span>
                    <h3 class="bento-title">Prestasi</h3>
                    <span class="bento-sub">Akademik &amp; Non-Akademik</span>
                  </div>
                </div>
                <!-- SISI BELAKANG -->
                <div class="bento-card-back">
                  <h4 class="bento-back-title">Jalur Prestasi</h4>
                  <ul class="bento-back-list">
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan SPP 15% – 25% (1–3 Thn)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan DPP 50% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Tambahan Diskon Jurusan 5% – 10%</span>
                    </li>
                  </ul>
                </div>
              </div>
            </a>

            <!-- 5. Minat Bakat (Jalur 05) -->
            <a
              @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step2') }}?jalur=bakat @else {{ route('register') }}?jalur=bakat @endauth" @else aria-disabled="true" tabindex="-1" @endunless
              class="bento-card bento-orange group {{ $registrationClosed ? 'bento-disabled' : '' }}"
              aria-label="Pilih Jalur Minat Bakat"
              onclick="if(window.matchMedia('(hover: none)').matches && !this.classList.contains('is-flipped')){event.preventDefault();document.querySelectorAll('.bento-card.is-flipped').forEach(c=>c!==this&&c.classList.remove('is-flipped'));this.classList.toggle('is-flipped');}"
            >
              <div class="bento-card-inner">
                <!-- SISI DEPAN -->
                <div class="bento-card-front">
                  <svg class="bento-art" viewBox="0 0 220 150" role="img" aria-label="Ilustrasi minat dan bakat seni olahraga"><circle cx="110" cy="78" r="62" fill="#FDC89A"/><path d="M70 95c-16-16-10-42 12-48 24-7 46 2 52 20 5 15-4 26-14 26-6 0-10 6-8 12 3 8-4 16-14 16-12 0-20-10-28-26z" fill="#EA580C"/><ellipse cx="114" cy="94" rx="6" ry="8" fill="#FDC89A"/><circle cx="78" cy="72" r="5" fill="#38BDF8"/><circle cx="94" cy="62" r="5" fill="#FDE047"/><circle cx="112" cy="66" r="5" fill="#A855F7"/><circle cx="86" cy="90" r="5" fill="#22C55E"/><g transform="rotate(35 140 70)"><rect x="135" y="32" width="10" height="58" rx="3" fill="#1B2559"/><polygon points="135,32 145,32 140,18" fill="#F59E0B"/><polygon points="138,22 142,22 140,18" fill="#EA580C"/><rect x="135" y="42" width="10" height="6" fill="#94A3B8"/></g><g fill="#1B2559" transform="translate(142, 78)"><circle cx="10" cy="22" r="7"/><rect x="15" y="4" width="4" height="18"/><path d="M19 4c6 0 11 4 12 10-3-4-8-5-12-5z"/></g><use href="#st" transform="translate(34 42) scale(1.2)" fill="#fff"/><use href="#st" transform="translate(186 38) scale(1.4)" fill="#F59E0B"/><use href="#st" transform="translate(182 120) scale(.9)" fill="#fff"/></svg>
                  <div class="bento-card-info">
                    <span class="bento-tag">Jalur 05</span>
                    <h3 class="bento-title">Minat &amp; Bakat</h3>
                    <span class="bento-sub">Seni, Budaya &amp; Olahraga</span>
                  </div>
                </div>
                <!-- SISI BELAKANG -->
                <div class="bento-card-back">
                  <h4 class="bento-back-title">Minat &amp; Bakat</h4>
                  <ul class="bento-back-list">
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan SPP 15% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan DPP 50% (1 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Tambahan Diskon Jurusan 5% – 10%</span>
                    </li>
                  </ul>
                </div>
              </div>
            </a>

            <!-- 6. Khusus (Jalur 06) -->
            <a
              @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step2') }}?jalur=khusus @else {{ route('register') }}?jalur=khusus @endauth" @else aria-disabled="true" tabindex="-1" @endunless
              class="bento-card bento-navy group {{ $registrationClosed ? 'bento-disabled' : '' }}"
              aria-label="Pilih Jalur Khusus"
              onclick="if(window.matchMedia('(hover: none)').matches && !this.classList.contains('is-flipped')){event.preventDefault();document.querySelectorAll('.bento-card.is-flipped').forEach(c=>c!==this&&c.classList.remove('is-flipped'));this.classList.toggle('is-flipped');}"
            >
              <div class="bento-card-inner">
                <!-- SISI DEPAN -->
                <div class="bento-card-front">
                  <svg class="bento-art" viewBox="0 0 220 150" role="img" aria-label="Ilustrasi perisai, buku, dan apel"><circle cx="110" cy="78" r="64" fill="#C7D8ED"/><rect x="14" y="122" width="42" height="9" rx="2" fill="#6B4FD3"/><rect x="18" y="113" width="36" height="9" rx="2" fill="#F2B705"/><rect x="14" y="104" width="40" height="9" rx="2" fill="#fff"/><path d="M110 14l52 20v38c0 34-22 56-52 68-30-12-52-34-52-68V34z" fill="#9CC4F2"/><path d="M110 26l40 15v31c0 27-17 44-40 54-23-10-40-27-40-54V41z" fill="#D3E6FA"/><path d="M90 76l15 15 28-30" fill="none" stroke="#1B2559" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/><circle cx="178" cy="120" r="17" fill="#8FD3A5"/><path d="M178 104c0-6 3-9 7-10" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"/><use href="#st" transform="translate(30 40) scale(1.2)" fill="#FFD95C"/><use href="#st" transform="translate(196 44) scale(1.1)" fill="#94A3B8"/></svg>
                  <div class="bento-card-info">
                    <span class="bento-tag">Jalur 06</span>
                    <h3 class="bento-title">Khusus</h3>
                    <span class="bento-sub">Anak Guru, Mitra &amp; MOU</span>
                  </div>
                </div>
                <!-- SISI BELAKANG -->
                <div class="bento-card-back">
                  <h4 class="bento-back-title">Jalur Khusus</h4>
                  <ul class="bento-back-list">
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan SPP 20% – 70% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Potongan DPP 50% (3 Tahun)</span>
                    </li>
                    <li class="bento-back-item">
                      <svg class="bento-back-check" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Tambahan Diskon Jurusan 5% – 10%</span>
                    </li>
                  </ul>
                </div>
              </div>
            </a>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- 5. SECTION JADWAL PENDAFTARAN (Vertical Timeline 1 - 4)        -->
      <!-- ============================================================== -->
      <section
        id="section-jadwal"
        class="relative z-20 py-16 sm:py-20 px-4 sm:px-6 lg:px-8 bg-white text-slate-900 no-print"
      >
        <div class="max-w-5xl mx-auto">
          <!-- Header -->
          <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-14">
            <h2
              class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900"
            >
              Jadwal Pendaftaran
            </h2>
            <p class="mt-3 text-sm sm:text-base text-slate-600 font-medium">
              Jangan sampai ketinggalan! Daftarkan dirimu pada gelombang yang
              tersedia
            </p>
          </div>

          <!-- Vertical Timeline Container -->
          <div class="relative pl-6 sm:pl-10 pr-2 sm:pr-3">
            <!-- Garis Penghubung Vertikal di Sebelah Kiri -->
            <div
              class="absolute left-5 sm:left-8 top-4 bottom-4 w-0.5 bg-gradient-to-b from-blue-600 via-slate-300 to-slate-200"
            ></div>

            @php
              $currentWaveName = $activeWave ?? 'Gelombang Khusus';
              $waveRankMap = [
                  'Gelombang Khusus' => 1,
                  'Gelombang 1' => 2,
                  'Gelombang 2' => 3,
                  'Tutup Gelombang' => 99,
              ];
              $currentRank = $waveRankMap[$currentWaveName] ?? 1;

              $timelineCards = [
                  [
                      'num' => 1,
                      'name' => 'Gelombang Khusus',
                      'date' => '07 September 2026 – 01 Januari 2027',
                      'desc' => 'Gratis Uang Pendaftaran 100%, Potongan SPP 15% (3 Thn), Potongan DPP 50% (3 Thn) + Potongan SPP Pilihan Jurusan.',
                      'benefits' => [
                          ['title' => 'Gratis Pendaftaran', 'text' => 'Gratis Pendaftaran', 'pct' => '100%', 'suffix' => ''],
                          ['title' => 'Potongan SPP', 'text' => 'Potongan SPP', 'pct' => '15%', 'suffix' => '(3 Thn)'],
                          ['title' => 'Potongan DPP', 'text' => 'Potongan DPP', 'pct' => '50%', 'suffix' => '(3 Thn)'],
                          ['title' => 'Tambahan SPP', 'text' => 'Tambahan SPP', 'pct' => '', 'suffix' => 'Pilihan Jurusan'],
                      ],
                  ],
                  [
                      'num' => 2,
                      'name' => 'Gelombang 1',
                      'date' => '02 Januari 2027 – 11 Mei 2027',
                      'desc' => 'Potongan Uang Pendaftaran 50%, Potongan SPP 10% (3 Thn), Potongan DPP 30% (3 Thn) + Potongan SPP Pilihan Jurusan.',
                      'benefits' => [
                          ['title' => 'Potongan Pendaftaran', 'text' => 'Potongan Pendaftaran', 'pct' => '50%', 'suffix' => ''],
                          ['title' => 'Potongan SPP', 'text' => 'Potongan SPP', 'pct' => '10%', 'suffix' => '(3 Thn)'],
                          ['title' => 'Potongan DPP', 'text' => 'Potongan DPP', 'pct' => '30%', 'suffix' => '(3 Thn)'],
                          ['title' => 'Tambahan SPP', 'text' => 'Tambahan SPP', 'pct' => '', 'suffix' => 'Pilihan Jurusan'],
                      ],
                  ],
                  [
                      'num' => 3,
                      'name' => 'Gelombang 2',
                      'date' => '12 Mei 2027 – 19 Juli 2027',
                      'desc' => 'Potongan Uang Pendaftaran 50%, Potongan SPP 5% (3 Thn), Potongan DPP 25% (3 Thn) + Potongan SPP Pilihan Jurusan.',
                      'benefits' => [
                          ['title' => 'Potongan Pendaftaran', 'text' => 'Potongan Pendaftaran', 'pct' => '50%', 'suffix' => ''],
                          ['title' => 'Potongan SPP', 'text' => 'Potongan SPP', 'pct' => '5%', 'suffix' => '(3 Thn)'],
                          ['title' => 'Potongan DPP', 'text' => 'Potongan DPP', 'pct' => '25%', 'suffix' => '(3 Thn)'],
                          ['title' => 'Tambahan SPP', 'text' => 'Tambahan SPP', 'pct' => '', 'suffix' => 'Pilihan Jurusan'],
                      ],
                  ],
              ];
            @endphp

            <div class="space-y-6 sm:space-y-7">
              @foreach ($timelineCards as $card)
                @php
                  $isCurrent = ($card['num'] === $currentRank);
                  $isPast = ($card['num'] < $currentRank);
                  $isUpcoming = ($card['num'] > $currentRank);
                @endphp

                <div class="relative pl-6 sm:pl-8">
                  <!-- Nomor Lingkaran -->
                  @if ($isCurrent)
                    <div
                      class="absolute -left-6 sm:-left-7 top-4 sm:top-1/2 sm:-translate-y-1/2 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-blue-600 text-white font-black text-sm sm:text-base flex items-center justify-center ring-4 ring-blue-100 shadow-md shadow-blue-600/30 z-10"
                    >
                      {{ $card['num'] }}
                    </div>
                  @elseif ($isPast)
                    <div
                      class="absolute -left-6 sm:-left-7 top-4 sm:top-1/2 sm:-translate-y-1/2 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-200 text-slate-600 font-black text-sm sm:text-base flex items-center justify-center border-2 border-slate-300 z-10"
                    >
                      {{ $card['num'] }}
                    </div>
                  @else
                    <div
                      class="absolute -left-6 sm:-left-7 top-4 sm:top-1/2 sm:-translate-y-1/2 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white text-slate-700 font-black text-sm sm:text-base flex items-center justify-center border-2 border-slate-300 z-10"
                    >
                      {{ $card['num'] }}
                    </div>
                  @endif

                  <!-- Kartu Langkah: 3 Kolom Horizontal (Divider Antar Kolom & Items-Center) -->
                  <div
                    class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 lg:py-4 lg:px-6 transition-all {{ $isCurrent ? 'border-2 border-blue-500 shadow-md shadow-blue-500/10' : ($isPast ? 'border border-slate-200/80 opacity-85' : 'border border-slate-200/90 hover:border-slate-300') }}"
                  >
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center divide-y divide-slate-100 lg:divide-y-0 lg:divide-x lg:divide-slate-200/80 gap-3.5 lg:gap-0">
                      
                      <!-- Sisi Kiri: Tumpuk badge status, judul utama, dan tanggal di bawah judul -->
                      <div class="pb-3 lg:pb-0 lg:pr-6 lg:w-56 xl:w-64 shrink-0 flex flex-col justify-center items-start gap-1">
                        @if ($isCurrent)
                          <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                          >
                            <span
                              class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"
                            ></span>
                            <span>Sedang Berlangsung</span>
                          </span>
                        @elseif ($isPast)
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200"
                          >
                            Selesai
                          </span>
                        @else
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-200/80"
                          >
                            Mendatang
                          </span>
                        @endif

                        <h3
                          class="text-base sm:text-lg font-black {{ $isCurrent ? 'text-slate-900' : ($isPast ? 'text-slate-700' : 'text-slate-900') }} tracking-tight leading-snug mt-0.5"
                        >
                          {{ $card['name'] }}
                        </h3>

                        <!-- Tanggal: polos tanpa background/efek, teks kecil, di bawah judul -->
                        <div class="flex items-center gap-1.5 text-xs {{ $isCurrent ? 'text-blue-600/90 font-medium' : ($isPast ? 'text-slate-400 font-medium' : 'text-slate-500 font-medium') }} mt-0.5">
                          <svg
                            class="w-3.5 h-3.5 shrink-0 {{ $isCurrent ? 'text-blue-600' : ($isPast ? 'text-slate-400' : 'text-slate-400') }}"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                          >
                            <use href="{{ asset('assets/img/icon/sprite.svg#calendar') }}" />
                          </svg>
                          <span>{{ $card['date'] }}</span>
                        </div>
                      </div>

                      <!-- Sisi Kanan: Benefit Gelombang Sejajar (Horizontal 4 Kolom, Teks Center) -->
                      <div class="pt-3 lg:pt-0 lg:pl-6 flex-1 flex items-center">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-2.5 xl:gap-4 w-full items-center justify-items-center">
                          @foreach ($card['benefits'] as $item)
                            <div class="flex items-center justify-center gap-2 min-w-0">
                              <span class="w-4 h-4 rounded-full {{ $isCurrent ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                              </span>
                              <div class="text-center leading-tight">
                                <div class="text-xs sm:text-[13px] {{ $isPast ? 'text-slate-500' : 'text-slate-700' }} font-medium">
                                  {{ $item['title'] ?? $item['text'] }}
                                </div>
                                <div class="text-xs sm:text-[13px] mt-0.5">
                                  @if (!empty($item['pct']))
                                    <strong class="font-bold {{ $isPast ? 'text-slate-700' : 'text-slate-900' }}">{{ $item['pct'] }}</strong>
                                  @endif
                                  @if (!empty($item['suffix']))
                                    <span class="text-[11px] {{ $isPast ? 'text-slate-400' : 'text-slate-500' }}">{{ $item['suffix'] }}</span>
                                  @endif
                                </div>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- 5B. SECTION SIMULASI KALKULATOR BIAYA MASUK SPMB 2027/2028     -->
      <!-- ============================================================== -->
      <section
        id="section-simulasi-biaya"
        class="relative z-20 py-14 sm:py-16 px-4 sm:px-6 lg:px-8 bg-white text-slate-900 scroll-mt-24 no-print"
      >
        <div class="max-w-5xl mx-auto">
          <!-- Section Header -->
          <div class="text-center max-w-3xl mx-auto mb-6 sm:mb-8">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
              Rincian Biaya Daftar Ulang
            </h2>
          </div>

          <!-- 1. TABEL RINCIAN BIAYA DASAR PENDIDIKAN & KETENTUAN RESMI (DOKUMEN RESMI) -->
          <div class="mb-10 sm:mb-12">
            <div class="space-y-4">

              <!-- Tabel Biaya -->
              <div class="rincian-biaya-table-wrap max-w-4xl mx-auto overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="w-full text-center text-[13px] sm:text-sm">
                  <thead class="harmonious-soft-gradient text-slate-700 font-extrabold uppercase text-[11.5px] sm:text-xs tracking-wider border-b border-slate-200" style="background: linear-gradient(180deg, #FFFFFF 0%, #FBFDFE 45%, #F4F7FB 100%);">
                    <tr>
                      <th class="py-2.5 px-3.5 sm:px-4 text-center">Komponen Biaya</th>
                      <th class="py-2.5 px-2.5 sm:px-3 text-center">Biaya Jurusan TI<br><span class="text-[10px] sm:text-[11px] font-normal normal-case text-slate-500 whitespace-nowrap">(TJKT, PPLG, DKV, BD)</span></th>
                      <th class="py-2.5 px-2.5 sm:px-3 text-center">Biaya Jurusan TKC<br><span class="text-[10px] sm:text-[11px] font-normal normal-case text-slate-500 whitespace-nowrap">(Tata Kecantikan)</span></th>
                      <th class="py-2.5 px-3 sm:px-3.5 text-center">Waktu Pembayaran</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100 text-slate-800">
                    <tr class="hover:bg-slate-50/50">
                      <td class="py-1.5 sm:py-2 px-3.5 sm:px-4 font-semibold text-left">Pendaftaran</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 150.000</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 150.000</td>
                      <td class="py-1.5 sm:py-2 px-3 sm:px-3.5 text-slate-500 text-xs sm:text-[13px] font-medium text-center">Saat Mendaftar</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                      <td class="py-1.5 sm:py-2 px-3.5 sm:px-4 font-semibold leading-tight text-left">MOS + Kartu Pelajar + Sampul Raport + Kepramukaan</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 450.000</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 450.000</td>
                      <td class="py-1.5 sm:py-2 px-3 sm:px-3.5 text-slate-500 text-xs sm:text-[13px] font-medium text-center">Saat Registrasi</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                      <td class="py-1.5 sm:py-2 px-3.5 sm:px-4 font-semibold text-left">Asuransi (3 Tahun)</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 100.000</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 100.000</td>
                      <td class="py-1.5 sm:py-2 px-3 sm:px-3.5 text-slate-500 text-xs sm:text-[13px] font-medium text-center">Saat Registrasi</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                      <td class="py-1.5 sm:py-2 px-3.5 sm:px-4 font-semibold text-left">Seragam</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 1.975.000</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 1.975.000</td>
                      <td class="py-1.5 sm:py-2 px-3 sm:px-3.5 text-slate-500 text-xs sm:text-[13px] font-medium text-center">Saat Registrasi</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                      <td class="py-1.5 sm:py-2 px-3.5 sm:px-4 font-semibold text-left">Dana Pengembangan Pendidikan (DPP)</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 650.000</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 500.000</td>
                      <td class="py-1.5 sm:py-2 px-3 sm:px-3.5 text-slate-500 text-xs sm:text-[13px] font-medium text-center">Setiap Semester</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                      <td class="py-1.5 sm:py-2 px-3.5 sm:px-4 font-semibold text-left">SPP</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 650.000</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 450.000</td>
                      <td class="py-1.5 sm:py-2 px-3 sm:px-3.5 text-slate-500 text-xs sm:text-[13px] font-medium text-center">Setiap Bulan</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                      <td class="py-1.5 sm:py-2 px-3.5 sm:px-4 font-semibold text-left">OSIS</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 25.000</td>
                      <td class="py-1.5 sm:py-2 px-2.5 sm:px-3 text-center font-medium tabular-nums whitespace-nowrap">Rp 25.000</td>
                      <td class="py-1.5 sm:py-2 px-3 sm:px-3.5 text-slate-500 text-xs sm:text-[13px] font-medium text-center">Setiap Bulan</td>
                    </tr>
                  </tbody>
                  <tfoot class="bg-blue-50/70 text-slate-900 font-black">
                    <tr>
                      <td class="py-2 sm:py-2.5 px-3.5 sm:px-4 font-extrabold text-blue-900 text-left">TOTAL (Sebelum Potongan)</td>
                      <td class="py-2 sm:py-2.5 px-2.5 sm:px-3 text-center text-blue-700 font-black text-xs sm:text-sm tabular-nums whitespace-nowrap">Rp 4.000.000</td>
                      <td class="py-2 sm:py-2.5 px-2.5 sm:px-3 text-center text-blue-700 font-black text-xs sm:text-sm tabular-nums whitespace-nowrap">Rp 3.650.000</td>
                      <td class="py-2 sm:py-2.5 px-3 sm:px-3.5 text-[11px] sm:text-xs font-semibold text-blue-800 italic text-center">*SPP &amp; OSIS dihitung 1 bulan</td>
                    </tr>
                  </tfoot>
                </table>
              </div>

            </div>
          </div>

          <!-- Header Simulasi Biaya -->
          <div class="text-center max-w-3xl mx-auto mt-20 sm:mt-28 mb-8 sm:mb-10">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
              Simulasi Biaya Pendaftaran
            </h2>
          </div>

          <!-- Calculator Outer Shell -->
          <div
            id="spmb-calc-shell"
            class="rounded-3xl overflow-hidden border border-slate-200 shadow-none"
          >
            <!-- ========================================================== -->
            <!-- MODE 1: INTERACTIVE SPLIT-SCREEN QUIZ VIEW (01/04 - 04/04) -->
            <!-- ========================================================== -->
            <div id="spmb-quiz-view" class="grid grid-cols-1 lg:grid-cols-12">
              <!-- KOLOM KIRI: Area Input Interaktif (Tema Terang Slate) -->
              <div class="lg:col-span-7 bg-slate-50/85 harmonious-soft-gradient text-slate-900 p-5 sm:p-6 lg:p-7 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-200" style="background: linear-gradient(180deg, #FFFFFF 0%, #FBFDFE 45%, #F4F7FB 100%);">
                <div>
                  <!-- Indikator Langkah & Progress Bar 4 Segmen -->
                  <div class="mb-5">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                      <span
                        id="spmb-step-badge"
                        class="text-xs sm:text-sm font-extrabold tracking-wider text-blue-600 tabular-nums"
                      >
                        01 / 04
                      </span>
                      <button
                        type="button"
                        id="spmb-btn-quick-reset"
                        class="hidden text-xs font-semibold text-slate-500 hover:text-blue-600 transition cursor-pointer"
                      >
                        Reset
                      </button>
                    </div>

                    <!-- 4 Segmen Progress Bar (Jalur, Jurusan, Gelombang, Voucher) -->
                    <div class="grid grid-cols-4 gap-2" role="list" aria-label="Tahapan simulasi biaya">
                      <button type="button" data-step-nav="1" class="spmb-seg-btn text-left focus:outline-none cursor-pointer">
                        <div class="spmb-seg-bar h-1.5 w-full rounded-full bg-blue-600 transition-all duration-300"></div>
                        <span class="spmb-seg-label mt-1.5 block text-xs sm:text-sm font-bold text-blue-600 truncate">Jalur</span>
                      </button>
                      <button type="button" data-step-nav="2" class="spmb-seg-btn text-left focus:outline-none cursor-pointer">
                        <div class="spmb-seg-bar h-1.5 w-full rounded-full bg-slate-200 transition-all duration-300"></div>
                        <span class="spmb-seg-label mt-1.5 block text-xs sm:text-sm font-semibold text-slate-400 truncate">Jurusan</span>
                      </button>
                      <button type="button" data-step-nav="3" class="spmb-seg-btn text-left focus:outline-none cursor-pointer">
                        <div class="spmb-seg-bar h-1.5 w-full rounded-full bg-slate-200 transition-all duration-300"></div>
                        <span class="spmb-seg-label mt-1.5 block text-xs sm:text-sm font-semibold text-slate-400 truncate">Gelombang</span>
                      </button>
                      <button type="button" data-step-nav="4" class="spmb-seg-btn text-left focus:outline-none cursor-pointer">
                        <div class="spmb-seg-bar h-1.5 w-full rounded-full bg-slate-200 transition-all duration-300"></div>
                        <span class="spmb-seg-label mt-1.5 block text-xs sm:text-sm font-semibold text-slate-400 truncate">Voucher</span>
                      </button>
                    </div>
                  </div>

                  <!-- Area Pertanyaan & Pilihan Card/Pill Compact -->
                  <div id="spmb-question-panel" class="spmb-step-animate">
                    <h3 id="spmb-question-title" class="text-lg sm:text-xl font-extrabold tracking-tight text-slate-900 leading-snug">
                      Pilih Jalur Pendaftaran
                    </h3>
                    <p id="spmb-question-desc" class="mt-0.5 text-xs text-slate-500">
                      Acuan utama penentuan diskon SPP, DPP, dan berkas persyaratan.
                    </p>

                    <!-- Daftar Pilihan Utama (2 Grid: 3 di atas, 3 di bawah pada Langkah 01) -->
                    <div
                      id="spmb-options-container"
                      class="mt-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2.5"
                      role="radiogroup"
                      aria-labelledby="spmb-question-title"
                    ></div>

                    <!-- Sub-Kategori Container (Muncul untuk Jalur Prestasi / Jalur Khusus) -->
                    <div
                      id="spmb-suboptions-wrapper"
                      class="hidden mt-3 rounded-2xl p-3.5 shadow-none"
                    >
                      <div class="flex items-center justify-between gap-2 mb-2">
                        <h4 id="spmb-suboptions-title" class="text-xs font-extrabold text-slate-800">
                          Pilih Sub-Kategori
                        </h4>
                        <span class="px-2 py-0.5 text-[10px] font-bold text-blue-700">
                          Wajib Dipilih
                        </span>
                      </div>
                      <div
                        id="spmb-suboptions-container"
                        class="grid grid-cols-1 gap-1.5 max-h-[230px] overflow-y-auto spmb-custom-scroll pr-1"
                      ></div>
                    </div>

                    <!-- Pesan Validasi Langkah -->
                    <div
                      id="spmb-step-alert"
                      class="hidden mt-3 items-center gap-2 rounded-xl bg-amber-50 border border-amber-200 px-3 py-2 text-xs font-semibold text-amber-800"
                      role="alert"
                    >
                      <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                      </svg>
                      <span id="spmb-step-alert-text">Pilih salah satu opsi terlebih dahulu.</span>
                    </div>
                  </div>
                </div>

                <!-- Navigasi Bawah: Pill Buttons (Kembali & Lanjut) -->
                <div class="mt-6 flex items-center justify-between gap-3">
                  <button
                    type="button"
                    id="spmb-btn-prev"
                    disabled
                    class="hidden items-center justify-center px-5 sm:px-6 py-2.5 rounded-full text-xs sm:text-sm font-bold border border-slate-300 hover:border-slate-400 bg-transparent hover:bg-slate-200/40 text-slate-700 hover:text-slate-900 disabled:opacity-35 disabled:pointer-events-none transition cursor-pointer group"
                  >
                    <svg class="w-4 h-4 mr-2 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Kembali</span>
                  </button>

                  <button
                    type="button"
                    id="spmb-btn-next"
                    class="inline-flex items-center justify-center ml-auto px-6 sm:px-7 py-2.5 rounded-full text-xs sm:text-sm font-bold bg-blue-600 hover:bg-blue-500 active:scale-[0.98] text-white shadow-md shadow-blue-600/25 transition cursor-pointer group"
                  >
                    <span id="spmb-btn-next-label">Lanjut</span>
                    <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- KOLOM KANAN: Visualisasi Real-Time (Tema Navy Khas SMK TI) -->
              <div class="lg:col-span-5 bg-gradient-to-br from-slate-900 via-[#152042] to-[#1B2559] text-white p-5 sm:p-6 lg:p-7 flex flex-col relative overflow-hidden">
                <div class="pointer-events-none absolute -top-20 -right-20 w-56 h-56 rounded-full bg-sky-400/10 blur-3xl" aria-hidden="true"></div>

                <!-- BAGIAN ATAS: Kartu Simulasi Calon Siswa -->
                <div class="relative" aria-live="polite">
                  <div class="mb-3">
                    <p class="text-[10px] font-bold tracking-wider text-sky-300">
                      Simulasi Biaya
                    </p>
                  </div>

                  <!-- 3 Kolom Compact Badge Diskon (SPP, DPP, Pendaftaran) -->
                  <div class="grid grid-cols-3 gap-2">
                    <div class="rounded-xl bg-slate-950/45 p-2.5">
                      <span class="block text-[10px] font-semibold text-slate-300">Diskon SPP</span>
                      <span id="spmb-live-spp-badge" class="mt-0.5 block text-base sm:text-lg font-black text-sky-300 tabular-nums leading-tight">0%</span>
                      <span id="spmb-live-spp-note" class="mt-0.5 block text-[10px] text-slate-400 leading-tight">Belum dipilih</span>
                    </div>
                    <div class="rounded-xl bg-slate-950/45 p-2.5">
                      <span class="block text-[10px] font-semibold text-slate-300">Diskon DPP</span>
                      <span id="spmb-live-dpp-badge" class="mt-0.5 block text-base sm:text-lg font-black text-white tabular-nums leading-tight">0%</span>
                      <span id="spmb-live-dpp-note" class="mt-0.5 block text-[10px] text-slate-400 leading-tight">Belum dipilih</span>
                    </div>
                    <div class="rounded-xl bg-slate-950/45 p-2.5">
                      <span class="block text-[10px] font-semibold text-slate-300">Pendaftaran</span>
                      <span id="spmb-live-reg-badge" class="mt-0.5 block text-sm sm:text-base font-black text-sky-300 tabular-nums leading-tight">-</span>
                      <span id="spmb-live-reg-note" class="mt-0.5 block text-[10px] text-slate-400 leading-tight">Pilih gelombang</span>
                    </div>
                  </div>

                  <!-- Syarat Berkas Pendaftaran -->
                  <div class="mt-3.5">
                    <div class="mb-1.5">
                      <span class="text-[10px] font-bold tracking-wider text-slate-300">Syarat Berkas</span>
                    </div>
                    <ul id="spmb-live-berkas-list" class="space-y-1 text-[11.5px] text-slate-200/90 leading-snug">
                      <li class="text-slate-400 italic">Pilih jalur untuk melihat berkas.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- ========================================================== -->
            <!-- MODE 2: TAMPILAN HASIL AKHIR (CLEAN SOLID SLATE CARD)       -->
            <!-- ========================================================== -->
            <div
              id="spmb-result-view"
              hidden
              class="hidden bg-slate-900 text-white p-4 sm:p-5 lg:p-6"
            >
              <div>
                <!-- BARIS ATAS: Parameter Pilihan Simulasi (Kiri) & Tombol Aksi (Kanan) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                  <!-- SISI KIRI: Parameter Pilihan -->
                  <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-0.5">Pilihan:</span>
                    <div id="spmb-res-pills" class="flex flex-wrap items-center gap-1.5"></div>
                  </div>

                  <!-- SISI KANAN: Tombol Aksi (Outline, Selaras dengan Sisi Kiri) -->
                  <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                    <!-- Tombol Mulai dari Awal -->
                    <button
                      type="button"
                      id="spmb-btn-restart"
                      title="Ulangi simulasi dari awal"
                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-white/20 hover:border-sky-400/60 active:scale-95 text-[11px] font-medium text-slate-300 hover:text-white transition cursor-pointer bg-transparent"
                    >
                      <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      <span>Mulai dari Awal</span>
                    </button>

                    <!-- Tombol Modal: Berkas Persyaratan -->
                    <button
                      type="button"
                      onclick="openBerkasPersyaratanModal()"
                      id="spmb-btn-docs-modal"
                      title="Lihat berkas persyaratan"
                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-white/20 hover:border-sky-400/60 active:scale-95 text-[11px] font-medium text-slate-300 hover:text-white transition cursor-pointer bg-transparent"
                    >
                      <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <span>Berkas Persyaratan</span>
                    </button>
                  </div>
                </div>

                <!-- 2 KOLOM UTAMA: SISI KIRI (DAFTAR ULANG FULL) & SISI KANAN (BIAYA PERIODIK) -->
                <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10 items-start">
                  <!-- KOLOM KIRI: Daftar Ulang Full -->
                  <div class="flex flex-col">
                    <!-- Total Biaya Daftar Ulang (Kiri - Di Atas) -->
                    <div class="mb-3.5">
                      <div class="rounded-xl border border-sky-400/35 bg-gradient-to-br from-sky-500/15 via-sky-600/10 to-transparent p-3 sm:p-3.5">
                        <span class="text-xs font-bold tracking-wider text-sky-200 uppercase">Total Biaya Daftar Ulang</span>
                        <div class="mt-1 flex items-end justify-between gap-2">
                          <div
                            id="spmb-res-main-total"
                            class="text-2xl sm:text-3xl xl:text-4xl font-black tracking-tight text-white tabular-nums leading-none whitespace-nowrap shrink-0"
                          >
                            Rp 0
                          </div>
                          <div
                            id="spmb-res-saving-badge"
                            class="text-right flex flex-col items-end shrink-0 tabular-nums leading-tight pb-0.5"
                          ></div>
                        </div>
                      </div>
                    </div>

                    <!-- Daftar 4 Item Rincian Daftar Ulang (Di Bawah) -->
                    <div id="spmb-res-breakdown-rows" class="text-[13px] sm:text-sm space-y-2"></div>
                  </div>

                  <!-- Garis Pemisah Vertikal Halus di Tengah Kedua Kolom (Desktop) -->
                  <div class="hidden lg:block absolute left-1/2 top-1 bottom-1 -translate-x-1/2 w-px bg-white/10 pointer-events-none" aria-hidden="true"></div>

                  <!-- KOLOM KANAN: Biaya Periodik -->
                  <div class="flex flex-col">
                    <!-- Total Biaya Periodik (Kanan - Di Atas: 1 Div dengan Garis Pembatas Halus) -->
                    <div class="mb-3.5">
                      <div class="rounded-xl border border-sky-400/35 bg-gradient-to-br from-sky-500/15 via-sky-600/10 to-transparent p-3 sm:p-3.5">
                        <div class="grid grid-cols-2 divide-x divide-white/15">
                          <!-- Sub-Total Bulanan (SPP + OSIS) -->
                          <div class="pr-3 sm:pr-4">
                            <span class="text-[11px] font-bold tracking-wider text-sky-200 uppercase block">Total Bulanan</span>
                            <div class="mt-1 flex items-baseline gap-1">
                              <span id="spmb-res-monthly-fee" class="text-lg sm:text-xl xl:text-2xl font-black text-white tabular-nums leading-tight whitespace-nowrap">Rp 0</span>
                            </div>
                          </div>

                          <!-- Sub-Total Semesteran (DPP) -->
                          <div class="pl-3 sm:pl-4">
                            <span class="text-[11px] font-bold tracking-wider text-sky-200 uppercase block">Total Semesteran</span>
                            <div class="mt-1 flex items-baseline gap-1">
                              <span id="spmb-res-semester-fee" class="text-lg sm:text-xl xl:text-2xl font-black text-white tabular-nums leading-tight whitespace-nowrap">Rp 0</span>
                            </div>
                          </div>
                        </div>

                        <!-- Catatan Periode Pembayaran -->
                        <div class="mt-2.5 flex items-center gap-1.5 text-[10.5px] sm:text-[11px] text-sky-200/85 leading-snug">
                          <svg class="w-3.5 h-3.5 text-sky-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          <span>Mulai dibayarkan pada bulan/semester ke-2 (tidak ditagihkan saat daftar ulang).</span>
                        </div>
                      </div>
                    </div>

                    <!-- Daftar Item Periodik (SPP, OSIS, DPP - Di Bawah) -->
                    <div id="spmb-res-periodic-rows" class="text-[13px] sm:text-sm space-y-2"></div>

                    <!-- Catatan Syarat Pendaftaran Gratis / Khusus (jika ada) - di bawah item 3 kanan -->
                    <div
                      id="spmb-res-special-note"
                      class="hidden mt-3 rounded-xl border border-amber-400/40 p-2.5 sm:p-3 text-[11px] sm:text-xs text-amber-200/90 leading-relaxed space-y-1"
                    ></div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- 6. SECTION CTA BANNER (Gradient Biru ke Ungu Terang)           -->
      <!-- ============================================================== -->
      <section
        id="section-cta"
        class="relative z-30 pt-16 sm:pt-20 pb-20 sm:pb-24 px-4 sm:px-6 lg:px-8 bg-white text-slate-900 no-print"
      >
        <div class="max-w-6xl mx-auto">
          <div id="section-faq" class="mx-auto mb-14 grid max-w-6xl grid-cols-1 gap-8 scroll-mt-28 sm:mb-16 lg:grid-cols-[0.8fr_1.2fr] lg:gap-14">
            <div class="relative">
              <div class="lg:sticky lg:top-32 text-center lg:text-left">
                <h2
                  class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900"
                >
                  Pertanyaan yang Sering Ditanyakan
                </h2>
                <p class="mt-3 text-sm sm:text-base text-slate-600 font-medium leading-relaxed">
                  Jawaban praktis seputar kebutuhan perangkat, skema pembayaran, jaminan kelulusan, hingga penyaluran kerja.
                </p>
              </div>
            </div>

            <div class="divide-y divide-slate-200 border-y border-slate-200">
              @php
                $faqList = [
                  [
                    'num' => '01',
                    'q' => 'Bagaimana cara mendaftar secara online?',
                    'a' => 'Calon siswa dapat membuat akun melalui menu <strong class="font-bold text-slate-800">Daftar Akun</strong>, mengisi biodata diri dan pilihan jurusan, lalu mengunggah berkas persyaratan digital (Pas Foto, Kartu Keluarga, dan Rapor/Ijazah). Status verifikasi berkas dan pengumuman penerimaan dapat dipantau langsung dari dashboard akun Anda.',
                    'open' => true,
                  ],
                  [
                    'num' => '02',
                    'q' => 'Apakah biaya kembali jika diterima di sekolah negeri?',
                    'a' => '<strong class="font-bold text-slate-800">Ya, dikembalikan</strong> (kecuali biaya pendaftaran dan seragam yang sudah diproses). Cukup serahkan bukti surat tanda diterima di sekolah negeri sebelum masa MPLS dimulai.',
                    'open' => false,
                  ],
                  [
                    'num' => '03',
                    'q' => 'Apakah biaya daftar ulang dan DPP bisa dicicil?',
                    'a' => '<strong class="font-bold text-slate-800">Bisa.</strong> Biaya DPP dapat diangsur secara fleksibel selama semester berjalan melalui kesepakatan dengan bagian keuangan agar tidak memberatkan di awal.',
                    'open' => false,
                  ],
                  [
                    'num' => '04',
                    'q' => 'Apakah siswa wajib membawa laptop sendiri?',
                    'a' => '<strong class="font-bold text-slate-800">Tidak wajib.</strong> Praktikum didukung penuh oleh lab komputer ber-AC berstandar industri. Siswa dapat membawa laptop bertahap untuk tugas mandiri dengan panduan spesifikasi dari sekolah.',
                    'open' => false,
                  ],
                  [
                    'num' => '05',
                    'q' => 'Belum ada ijazah SMP, apakah sudah bisa mendaftar?',
                    'a' => '<strong class="font-bold text-slate-800">Sangat bisa.</strong> Cukup gunakan fotokopi nilai rapor SMP (semester 1–5) atau Surat Keterangan Aktif untuk mengunci kuota dan promo. Ijazah/SKL dapat disusulkan setelah terbit.',
                    'open' => false,
                  ],
                  [
                    'num' => '06',
                    'q' => 'Bagaimana peluang kerja atau kuliah setelah lulus?',
                    'a' => 'Lulusan dibekali <strong class="font-bold text-slate-800">Sertifikasi BNSP</strong> dan disalurkan kerja via unit <strong class="font-bold text-slate-800">BKK</strong> ke puluhan mitra industri. Tersedia juga beasiswa alumni ke ITB STIKOM Bali Group dan program kerja ke Jepang.',
                    'open' => false,
                  ],
                ];
              @endphp

              @foreach ($faqList as $faq)
                <details name="faq" class="group py-5 sm:py-6 transition-colors" {{ $faq['open'] ? 'open' : '' }}>
                  <summary class="flex cursor-pointer list-none select-none items-center justify-between gap-4 sm:gap-6 focus:outline-none focus-visible:outline-none [&::-webkit-details-marker]:hidden">
                    <div class="flex items-start gap-3.5 sm:gap-5 flex-1 min-w-0">
                      <span class="w-6 sm:w-7 shrink-0 text-xs font-semibold text-slate-400 pt-0.5 sm:pt-1 tabular-nums">{{ $faq['num'] }}</span>
                      <span class="text-base sm:text-lg font-bold text-slate-900 leading-snug transition-colors group-hover:text-slate-700">
                        {{ $faq['q'] }}
                      </span>
                    </div>
                    <span class="flex h-7 w-7 sm:h-8 sm:w-8 shrink-0 items-center justify-center text-slate-400 transition-colors duration-200 group-hover:text-slate-700 group-open:text-slate-900">
                      <svg class="h-4 w-4 sm:h-[18px] sm:w-[18px] transition-transform duration-300 ease-out group-open:rotate-180 motion-reduce:transition-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <use href="{{ asset('assets/img/icon/sprite.svg#chevron-down') }}" />
                      </svg>
                    </span>
                  </summary>
                  <div class="pl-[38px] sm:pl-12 pr-10 sm:pr-14 pt-3 sm:pt-3.5 text-sm sm:text-base leading-relaxed text-slate-600">
                    <p>{!! $faq['a'] !!}</p>
                  </div>
                </details>
              @endforeach
            </div>

          </div>

          <div class="cta-border-animated relative isolate overflow-hidden rounded-[1.75rem] sm:rounded-[2rem] lg:rounded-[2.25rem] bg-gradient-to-br from-[#3985f6] via-[#4b4ce8] to-[#8a28cf] max-w-6xl mx-auto mt-20 sm:mt-24 lg:mt-28 mb-4 sm:mb-6 lg:mb-8 py-6 px-8 sm:py-7 sm:px-12 lg:py-7 lg:px-16 text-white shadow-none border-none">
            <div class="pointer-events-none absolute -right-20 -top-40 h-[22rem] w-[22rem] rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -right-8 -top-28 h-64 w-64 rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute right-8 -top-16 h-44 w-44 rounded-full border border-white/10"></div>

            <div class="relative z-10 grid items-center gap-5 lg:grid-cols-[1.2fr_0.8fr] lg:gap-8">
              <div>
                <h2 class="max-w-2xl text-2xl font-black leading-tight sm:text-3xl lg:text-[2.25rem]">
                  Segera Amankan <span class="text-amber-300 underline decoration-wavy decoration-amber-300 underline-offset-6">Kursimu</span>
                  di <br>SMK TI Bali Global Badung
                </h2>

                <div class="mt-6 sm:mt-7 lg:mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                  @if ($registrationClosed)
                    <span class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2.5 rounded-full bg-white/70 px-7 py-3 text-xs sm:text-sm font-extrabold text-blue-900/60 sm:w-auto shadow-none" aria-disabled="true">
                      Pendaftaran Ditutup
                    </span>
                  @else
                    <a
                      href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth"
                      class="group inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-full bg-white px-7 py-3 text-xs sm:text-sm font-extrabold text-blue-700 shadow-none transition duration-200 hover:-translate-y-0.5 hover:bg-blue-50 active:translate-y-0 sm:w-auto motion-reduce:transition-none"
                    >
                      <span class="inline-flex items-center leading-none">Daftar Sekarang</span>
                      <svg class="h-4 w-4 shrink-0 -translate-y-[0.5px] transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <use href="{{ asset('assets/img/icon/sprite.svg#arrow-right') }}" />
                      </svg>
                    </a>
                  @endif
                </div>
              </div>

              <div data-quota-tilt class="group/quotacard relative rounded-2xl sm:rounded-3xl border border-white/30 bg-gradient-to-br from-white/[0.18] via-white/[0.10] to-white/[0.04] p-3.5 sm:p-4 backdrop-blur-xl">
                <!-- Dynamic Glare Highlight (mengikuti pergerakan kursor mouse secara realistis) -->
                <div class="quota-glare pointer-events-none absolute inset-0 rounded-[inherit]" aria-hidden="true"></div>

                <div class="mb-2.5 pb-2 border-b border-white/15 flex items-center justify-between">
                  <span class="text-[11px] font-extrabold uppercase tracking-wider text-blue-100">Kuota Program Keahlian</span>
                </div>

                <div class="space-y-0.5">
                  @forelse ($jurusanQuotas as $jurusan)
                    @php
                      $jurusanTitle = str_contains(strtoupper($jurusan['nama_jurusan']), 'KECANTIKAN') ? 'TKC' : trim(\Illuminate\Support\Str::before($jurusan['nama_jurusan'], ' ('));
                      $jurusanLinks = [
                          'PPLG' => 'https://www.smktibaliglobalbadung.sch.id/jurusan/pplg',
                          'TJKT' => 'https://www.smktibaliglobalbadung.sch.id/jurusan/tkjt',
                          'DKV' => 'https://www.smktibaliglobalbadung.sch.id/jurusan/dkv',
                          'BD' => 'https://www.smktibaliglobalbadung.sch.id/jurusan/bisnis-digital',
                          'TKC' => 'https://www.smktibaliglobalbadung.sch.id/jurusan/tkc',
                      ];
                      $statusClasses = match ($jurusan['warna']) {
                          'red' => ['badge' => 'bg-rose-100 text-rose-800', 'bar' => 'bg-rose-300'],
                          'yellow' => ['badge' => 'bg-amber-100 text-amber-800', 'bar' => 'bg-amber-300'],
                          default => ['badge' => 'bg-emerald-100 text-emerald-800', 'bar' => 'bg-emerald-300'],
                      };
                    @endphp
                    <div class="py-1 sm:py-1.5 px-2 -mx-2 rounded-xl transition duration-200" data-quota-item data-quota-id="{{ $jurusan['id'] }}">
                      <div class="mb-1 flex items-center justify-between gap-3">
                        <h4 class="truncate text-xs font-extrabold sm:text-sm text-white">
                          @if (isset($jurusanLinks[$jurusanTitle]))
                            <a href="{{ $jurusanLinks[$jurusanTitle] }}" target="_blank" rel="noopener noreferrer" class="inline-block rounded-sm px-1 text-inherit no-underline transition duration-200 hover:translate-x-1 hover:text-sky-300">{{ $jurusanTitle }}</a>
                          @else
                            {{ $jurusanTitle }}
                          @endif
                        </h4>
                        <span class="shrink-0 text-[11px] sm:text-xs font-semibold text-blue-100/90 tabular-nums" data-quota-remaining>
                          {{ $jurusan['tersisa'] > 0 ? $jurusan['tersisa'] . ' kursi tersedia' : 'Kuota telah terpenuhi' }}
                        </span>
                      </div>
                      <div class="quota-progress-track h-1.5 overflow-hidden rounded-full bg-white/25 border border-white/10" role="progressbar" aria-label="Kuota {{ $jurusan['nama_jurusan'] }} terisi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $jurusan['persentase'] }}">
                        <div class="quota-progress-bar h-full {{ $statusClasses['bar'] }} transition-[width] duration-500" style="width: {{ $jurusan['persentase'] }}%;" data-quota-bar></div>
                      </div>
                    </div>
                  @empty
                    <p class="py-4 text-xs sm:text-sm leading-relaxed text-blue-100">
                      Informasi program keahlian belum tersedia. Hubungi panitia untuk mendapatkan detail pendaftaran.
                    </p>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- 7. FOOTER UTAMA (Profil Â· Navigasi Â· Program Studi Â· Kontak)    -->
      <!-- ============================================================== -->
      <footer
        id="section-footer"
        class="relative overflow-hidden text-slate-600 border-t border-slate-200 pt-16 sm:pt-20 pb-8 px-4 sm:px-6 lg:px-8 no-print"
        style="background: linear-gradient(180deg, #F1F5F9 0%, #E8EEF5 100%);"
      >
        <div class="relative max-w-7xl mx-auto">
          <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-12"
          >
            <!-- Kolom 1: Profil Sekolah & Media Sosial -->
            <div class="lg:col-span-4 space-y-5">
              <a
                href="#hero-home-section"
                class="inline-flex items-center gap-3 group"
              >
                <div
                  class="w-10 h-10 sm:w-11 sm:h-11 flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-105"
                >
                  <img
                    src="{{ asset('assets/img/alogo.png') }}"
                    alt="Logo SMK TI Bali Global Badung"
                    class="w-full h-full object-contain filter drop-shadow-[0_2px_6px_rgba(0,0,0,0.15)]"
                  />
                </div>
                <div class="flex flex-col leading-none">
                  <span
                    class="tracking-wider text-base sm:text-lg font-extrabold text-blue-600"
                    style="line-height: 1.1;"
                  >
                    SMK TI
                  </span>
                  <span
                    class="text-xs sm:text-sm font-bold text-slate-600 -mt-0.5"
                    style="line-height: 1.15;"
                  >
                    Bali Global Badung
                  </span>
                </div>
              </a>
              <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Sekolah kejuruan berbasis teknologi informasi yang mencetak
                lulusan kompeten, berkarakter, dan siap bersaing di era digital.
              </p>
              <!-- Ikon Media Sosial: Facebook, Instagram, TikTok, YouTube, X -->
              <div class="flex items-center gap-2.5 pt-1">
                <!-- Facebook -->
                <a
                  href="https://www.facebook.com/globalitibadung"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="Facebook"
                  class="group relative w-9 h-9 rounded-full bg-white/65 text-slate-600 hover:text-white flex items-center justify-center transition-all duration-200 ease-out hover:bg-[#1877F2] active:scale-95"
                >
                  <svg class="relative z-10 w-[18px] h-[18px] transition-transform duration-200 ease-out group-hover:scale-110 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                  </svg>
                </a>
                <!-- Instagram -->
                <a
                  href="https://www.instagram.com/globalitibadung_/"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="Instagram"
                  class="group relative w-9 h-9 rounded-full bg-white/65 text-slate-600 hover:text-white flex items-center justify-center transition-all duration-200 ease-out active:scale-95"
                >
                  <!-- Smooth gradient overlay fade -->
                  <span class="absolute inset-0 rounded-full bg-gradient-to-tr from-[#f9ce34] via-[#ee2a7b] to-[#6228d7] opacity-0 group-hover:opacity-100 transition-opacity duration-200 ease-out pointer-events-none"></span>
                  <svg class="relative z-10 w-[18px] h-[18px] transition-transform duration-200 ease-out group-hover:scale-110 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                  </svg>
                </a>
                <!-- TikTok -->
                <a
                  href="https://www.tiktok.com/@globalitibadung_"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="TikTok"
                  class="group relative w-9 h-9 rounded-full bg-white/65 text-slate-600 hover:text-white flex items-center justify-center transition-all duration-200 ease-out hover:bg-black active:scale-95"
                >
                  <svg class="relative z-10 w-[18px] h-[18px] transition-transform duration-200 ease-out group-hover:scale-110 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" />
                  </svg>
                </a>
                <!-- YouTube -->
                <a
                  href="https://www.youtube.com/@SMKTIBaliGlobalBadung"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="YouTube"
                  class="group relative w-9 h-9 rounded-full bg-white/65 text-slate-600 hover:text-white flex items-center justify-center transition-all duration-200 ease-out hover:bg-[#FF0000] active:scale-95"
                >
                  <svg class="relative z-10 w-[18px] h-[18px] transition-transform duration-200 ease-out group-hover:scale-110 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="4" />
                    <polygon points="10 9 15 12 10 15" />
                  </svg>
                </a>
                <!-- X -->
                <a
                  href="https://x.com/globalitibadung"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="X"
                  class="group relative w-9 h-9 rounded-full bg-white/65 text-slate-600 hover:text-white flex items-center justify-center transition-all duration-200 ease-out hover:bg-[#0f1419] active:scale-95"
                >
                  <svg class="relative z-10 w-[18px] h-[18px] transition-transform duration-200 ease-out group-hover:scale-110 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                    <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />
                  </svg>
                </a>
              </div>
            </div>
            <!-- Kolom 2: Navigasi -->
            <div class="lg:col-span-2">
              <h4 class="text-base font-bold text-slate-900 mb-4 sm:mb-5">
                Navigasi
              </h4>
              <ul class="space-y-3 text-xs sm:text-sm text-slate-600">
                <li>
                  <a
                    href="#hero-home-section"
                    class="inline-block hover:text-[#00A8E8] hover:translate-x-1.5 transition-all duration-200 ease-out"
                    >Beranda</a
                  >
                </li>
                <li>
                  <a
                    href="#section-cara-daftar"
                    class="inline-block hover:text-[#00A8E8] hover:translate-x-1.5 transition-all duration-200 ease-out"
                    >Cara Daftar</a
                  >
                </li>
                <li>
                  <a
                    href="#section-jalur"
                    class="inline-block hover:text-[#00A8E8] hover:translate-x-1.5 transition-all duration-200 ease-out"
                    >Jalur Masuk</a
                  >
                </li>
                <li>
                  <a
                    href="#section-simulasi-biaya"
                    class="inline-block hover:text-[#00A8E8] hover:translate-x-1.5 transition-all duration-200 ease-out"
                    >Biaya</a
                  >
                </li>
                <li>
                  <a
                    href="#section-faq"
                    class="inline-block hover:text-[#00A8E8] hover:translate-x-1.5 transition-all duration-200 ease-out"
                    >Bantuan</a
                  >
                </li>
              </ul>
            </div>

            <!-- Kolom 3: Program Studi -->
            <div class="lg:col-span-2">
              <h4 class="text-base font-bold text-slate-900 mb-4 sm:mb-5">
                Program Studi
              </h4>
              <ul class="space-y-3 text-xs sm:text-sm text-slate-600">
                @foreach ($jurusanQuotas as $jurusan)
                  @php
                    $jurusanShort = trim(\Illuminate\Support\Str::before($jurusan['nama_jurusan'], ' ('));
                  @endphp
                  <li>
                    <a
                      @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth" @else aria-disabled="true" tabindex="-1" @endunless
                      class="inline-block hover:text-[#00A8E8] hover:translate-x-1.5 transition-all duration-200 ease-out {{ $registrationClosed ? 'pointer-events-none text-slate-400' : '' }}"
                      title="{{ $jurusan['nama_jurusan'] }}"
                    >{{ $jurusanShort }}</a>
                  </li>
                @endforeach
              </ul>
            </div>
            <!-- Kolom 4: Kontak -->
            <div class="lg:col-span-4">
              <h4 class="text-base font-bold text-slate-900 mb-4 sm:mb-5">
                Kontak
              </h4>
              <ul class="space-y-3 text-xs sm:text-sm text-slate-600">
                <li class="flex items-start gap-2.5 group">
                  <svg
                    class="w-4 h-4 text-slate-600 group-hover:text-[#00A8E8] shrink-0 mt-0.5 transition"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24"
                  >
                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <a
                    href="https://maps.google.com/?q=SMK+TI+Bali+Global+Badung+Jl.+Tibungsari+No.+1+Dalung+Kuta+Utara+Badung+Bali"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-slate-600 group-hover:text-[#00A8E8] transition leading-relaxed"
                  >
                    Jl. Tibungsari No. 1, Desa Dalung, Kuta Utara, Badung, Bali
                  </a>
                </li>
                <li class="flex items-center gap-2.5 group">
                  <svg
                    class="w-4 h-4 text-slate-600 group-hover:text-[#00A8E8] shrink-0 transition"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24"
                  >
                    <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  <a
                    href="tel:+62361419097"
                    class="text-slate-600 group-hover:text-[#00A8E8] transition tabular-nums"
                    >+62 361-419097</a
                  >
                </li>
                <li class="flex items-center gap-2.5 group">
                  <svg
                    class="w-4 h-4 text-slate-600 group-hover:text-[#00A8E8] shrink-0 transition"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24"
                  >
                    <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                    <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                  </svg>
                  <a
                    href="https://wa.me/6282261900070"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-slate-600 group-hover:text-[#00A8E8] transition tabular-nums"
                    >+62 822-61-9000-70</a
                  >
                </li>
                <li class="flex items-center gap-2.5 group">
                  <svg
                    class="w-4 h-4 text-slate-600 group-hover:text-[#00A8E8] shrink-0 transition"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24"
                  >
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  <a
                    href="mailto:info@smktibaliglobalbadung.sch.id"
                    class="text-slate-600 group-hover:text-[#00A8E8] transition break-all"
                    >info@smktibaliglobalbadung.sch.id</a
                  >
                </li>
              </ul>
            </div>
          </div>

          <!-- Baris Bawah: Hak Cipta & Kebijakan -->
          <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left text-xs sm:text-sm text-slate-500 no-reveal" data-no-reveal>
            <p>
              &copy; 2027 SMK TI Bali Global Badung. Hak Cipta Dilindungi.
            </p>
            <div class="flex items-center gap-6">
              <a href="{{ route('kebijakan-privasi') }}" onclick="event.preventDefault(); openKebijakanPrivasiModal();" class="hover:text-sky-600 transition cursor-pointer">Kebijakan Privasi</a>
              <a href="{{ route('syarat-ketentuan') }}" onclick="event.preventDefault(); openSyaratKetentuanModal();" class="hover:text-sky-600 transition cursor-pointer">Syarat &amp; Ketentuan</a>
            </div>
          </div>
        </div>
      </footer>

      <!-- Floating WhatsApp Button (Pojok Kanan Bawah) -->
      <aside
        id="floating-wa-aside"
        class="fixed bottom-7 right-7 sm:bottom-8 sm:right-8 z-50 flex items-center justify-center no-print"
        aria-label="Kontak Cepat WhatsApp"
      >
        <div class="relative flex items-center justify-center group-wa group/wa">
          <!-- Efek Gelombang Radio Halus (Outline Saja, Tanpa Fill) -->
          <span class="wa-radio-wave pointer-events-none absolute inset-0 rounded-full bg-transparent" aria-hidden="true"></span>
          <span class="wa-radio-wave-delayed pointer-events-none absolute inset-0 rounded-full bg-transparent" aria-hidden="true"></span>

          <!-- Tooltip Singkat WhatsApp -->
          <div
            id="tooltip-wa"
            role="tooltip"
            class="wa-tooltip-wrapper absolute right-full top-1/2 pr-3.5 z-30 pointer-events-none"
          >
            <div class="relative bg-white/95 backdrop-blur-md rounded-xl px-4 py-2.5 shadow-[0_8px_24px_-4px_rgba(15,23,42,0.12)] border border-slate-200/90 whitespace-nowrap text-left">
              <div class="flex items-center gap-2.5">
                <span class="relative flex h-2 w-2 shrink-0">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-xs sm:text-[13px] text-slate-700 font-medium tracking-tight">Konsultasi pendaftaran bersama panitia kami</span>
              </div>
              <!-- Panah / Caret Pointer (Menghubungkan ke tombol WA di sebelah kanan) -->
              <div class="absolute -right-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-white border-t border-r border-slate-200/90 rotate-45"></div>
            </div>
          </div>

          <!-- Tombol Bulat WhatsApp -->
          <a
            href="https://wa.me/6282261900070"
            target="_blank"
            rel="noopener noreferrer"
            aria-describedby="tooltip-wa"
            class="relative z-10 flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-full bg-[#25D366] text-white transition-all duration-300 hover:bg-[#20ba59] hover:shadow-lg hover:shadow-emerald-600/35 active:scale-95 cursor-pointer motion-reduce:transition-none"
            aria-label="Hubungi panitia via WhatsApp"
          >
            <svg class="h-8 w-8 sm:h-9 sm:w-9 shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true">
              <use href="{{ asset('assets/img/icon/sprite.svg#whatsapp') }}" />
            </svg>
            <span class="sr-only">Hubungi Panitia SPMB via WhatsApp</span>
          </a>
        </div>
      </aside>

      
<!-- Modal Pop-up: Ketentuan & Syarat Biaya Pendidikan -->
      <div
        id="modal-ketentuan-biaya"
        class="fixed inset-0 z-[9999] bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 transition-all duration-200 hidden opacity-0 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-ketentuan-biaya-title"
      >
        <div
          id="modal-ketentuan-biaya-box"
          class="relative w-full max-w-2xl bg-white rounded-3xl border border-slate-100 shadow-2xl p-5 sm:p-6 transform transition-all duration-200 scale-95 flex flex-col max-h-[90vh]"
        >
          <!-- Modal Header -->
          <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <h3 id="modal-ketentuan-biaya-title" class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">
                  Ketentuan &amp; Syarat Biaya Pendidikan
                </h3>
                <p class="text-xs text-slate-500">
                  Dokumen resmi SPMB Tahun Ajaran 2027/2028
                </p>
              </div>
            </div>
            <button
              type="button"
              onclick="closeKetentuanBiayaModal()"
              class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer shrink-0"
              aria-label="Tutup pop-up"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Body (Scrollable) -->
          <section class="overflow-y-auto py-3 pr-1 sm:pr-2 text-xs sm:text-sm leading-relaxed flex-1">
            <ol class="divide-y divide-slate-100 space-y-0 text-slate-600">
              <!-- 1. Syarat Minimal Daftar Ulang -->
              <li class="py-3.5 first:pt-1 last:pb-1">
                <h4 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2 mb-1.5">
                  <span class="w-5 h-5 rounded-md bg-blue-50 text-blue-600 border border-blue-100 text-[11px] font-bold inline-flex items-center justify-center shrink-0">1</span>
                  <span>Syarat Minimal Daftar Ulang</span>
                </h4>
                <p class="text-slate-600 ml-7 leading-relaxed">
                  Calon siswa dinyatakan sudah registrasi ulang jika melakukan pembayaran minimal komponen: MOS + Kartu Pelajar + Sampul Raport + Kepramukaan, Asuransi, Seragam, DPP, dan SPP Semester I (pada simulasi SPP dihitung 1 bulan dan OSIS disertakan sesuai total tabel).
                </p>
              </li>

              <!-- 2. Aturan Pengembalian Biaya (Refund) -->
              <li class="py-3.5 first:pt-1 last:pb-1">
                <h4 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2 mb-1.5">
                  <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-700 border border-amber-100 text-[11px] font-bold inline-flex items-center justify-center shrink-0">2</span>
                  <span>Aturan Pengembalian Biaya (Refund)</span>
                </h4>
                <p class="text-slate-600 ml-7 leading-relaxed">
                  Bila diterima di sekolah negeri, biaya dikembalikan <strong class="font-semibold text-slate-800">kecuali biaya pendaftaran &amp; seragam</strong>, dengan bukti surat diterima kepala sekolah negeri. Jika diterima di sekolah negeri saat sedang/setelah MPLS, atau pindah ke swasta lain, <strong class="font-semibold text-slate-800">tidak ada pengembalian biaya apa pun</strong>.
                </p>
              </li>

              <!-- 3. Biaya yang Belum Termasuk -->
              <li class="py-3.5 first:pt-1 last:pb-1">
                <h4 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2 mb-1.5">
                  <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-bold inline-flex items-center justify-center shrink-0">3</span>
                  <span>Biaya yang Belum Termasuk</span>
                </h4>
                <p class="text-slate-600 ml-7 leading-relaxed">
                  Rincian biaya di atas tidak termasuk biaya buku, SAS (Sumatif Akhir Semester), PKL, Kunjungan Industri, Ujian Akhir, dan Pelepasan.
                </p>
              </li>

              <!-- 4. Pengesahan Dokumen Resmi -->
              <li class="py-3.5 first:pt-1 last:pb-1">
                <h4 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2 mb-1.5">
                  <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px] font-bold inline-flex items-center justify-center shrink-0">4</span>
                  <span>Pengesahan Dokumen Resmi</span>
                </h4>
                <p class="text-slate-600 ml-7 leading-relaxed">
                  Ditetapkan di Badung, 1 September 2026 oleh Kepala SMK TI Bali Global Badung, <strong class="font-semibold text-slate-800">I Made Indra Aribawa, SH</strong>.
                </p>
              </li>
            </ol>
          </section>

          <!-- Modal Footer -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-end shrink-0">
            <button
              type="button"
              onclick="closeKetentuanBiayaModal()"
              class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer active:scale-95"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Pop-up: Berkas Persyaratan -->
      <div
        id="modal-berkas-persyaratan"
        class="fixed inset-0 z-[9999] bg-slate-950/60 flex items-center justify-center p-3 sm:p-4 transition-all duration-200 hidden opacity-0 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-berkas-persyaratan-title"
      >
        <div
          id="modal-berkas-persyaratan-box"
          class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 sm:p-5 transform transition-all duration-200 scale-95 flex flex-col max-h-[90vh]"
        >
          <!-- Modal Header -->
          <div class="flex items-start justify-between gap-3 pb-2.5 shrink-0 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <h3 id="modal-berkas-persyaratan-title" class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">
                  Berkas Persyaratan
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                  Wajib dibawa atau disiapkan saat registrasi ulang
                </p>
              </div>
            </div>
            <button
              type="button"
              onclick="closeBerkasPersyaratanModal()"
              class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer shrink-0"
              aria-label="Tutup pop-up"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Body (Scrollable) -->
          <div class="overflow-y-auto pt-3 pb-1 flex-1">
            <ul id="spmb-res-docs-list" class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-2.5 text-xs text-slate-700 leading-snug">
              <li class="flex items-start gap-2 min-w-0">
                <span class="w-3.5 h-3.5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                  <svg class="w-2 h-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </span>
                <span class="leading-snug text-slate-700 font-medium text-xs sm:text-[12.5px]">Fotokopi Ijazah / SKL</span>
              </li>
              <li class="flex items-start gap-2 min-w-0">
                <span class="w-3.5 h-3.5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                  <svg class="w-2 h-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </span>
                <span class="leading-snug text-slate-700 font-medium text-xs sm:text-[12.5px]">Fotokopi Akta Kelahiran</span>
              </li>
              <li class="flex items-start gap-2 min-w-0">
                <span class="w-3.5 h-3.5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                  <svg class="w-2 h-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </span>
                <span class="leading-snug text-slate-700 font-medium text-xs sm:text-[12.5px]">Fotokopi Kartu Keluarga (KK)</span>
              </li>
              <li class="flex items-start gap-2 min-w-0">
                <span class="w-3.5 h-3.5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                  <svg class="w-2 h-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </span>
                <span class="leading-snug text-slate-700 font-medium text-xs sm:text-[12.5px]">Fotokopi / Bukti NISN</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Modal Pop-up: Syarat & Ketentuan SPMB -->
      <div
        id="modal-syarat-ketentuan"
        class="fixed inset-0 z-[9999] bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 transition-all duration-200 hidden opacity-0 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-syarat-ketentuan-title"
      >
        <div
          id="modal-syarat-ketentuan-box"
          class="relative w-full max-w-2xl bg-white rounded-3xl border border-slate-100 shadow-2xl p-5 sm:p-6 transform transition-all duration-200 scale-95 flex flex-col max-h-[90vh]"
        >
          <!-- Modal Header -->
          <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <h3 id="modal-syarat-ketentuan-title" class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">
                  Syarat &amp; Ketentuan Pendaftaran
                </h3>
                <p class="text-xs text-slate-500">
                  Dokumen resmi SPMB Tahun Ajaran 2027/2028
                </p>
              </div>
            </div>
            <button
              type="button"
              onclick="closeSyaratKetentuanModal()"
              class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer shrink-0"
              aria-label="Tutup pop-up"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Body (Scrollable) -->
          <section class="overflow-y-auto py-3 pr-1 sm:pr-2 text-xs sm:text-sm leading-relaxed flex-1">
            @include('landing.terms-and-condition')
          </section>

          <!-- Modal Footer -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between shrink-0">
            <button
              type="button"
              onclick="closeSyaratKetentuanModal(); openKebijakanPrivasiModal();"
              class="text-xs font-semibold text-sky-600 hover:text-sky-700 hover:underline cursor-pointer"
            >
              Lihat Kebijakan Privasi &rarr;
            </button>
            <button
              type="button"
              onclick="closeSyaratKetentuanModal()"
              class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Pop-up: Kebijakan Privasi SPMB -->
      <div
        id="modal-kebijakan-privasi"
        class="fixed inset-0 z-[9999] bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 transition-all duration-200 hidden opacity-0 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-kebijakan-privasi-title"
      >
        <div
          id="modal-kebijakan-privasi-box"
          class="relative w-full max-w-2xl bg-white rounded-3xl border border-slate-100 shadow-2xl p-5 sm:p-6 transform transition-all duration-200 scale-95 flex flex-col max-h-[90vh]"
        >
          <!-- Modal Header -->
          <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
              <div>
                <h3 id="modal-kebijakan-privasi-title" class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">
                  Kebijakan Privasi
                </h3>
                <p class="text-xs text-slate-500">
                  Perlindungan data pribadi calon siswa dan orang tua
                </p>
              </div>
            </div>
            <button
              type="button"
              onclick="closeKebijakanPrivasiModal()"
              class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer shrink-0"
              aria-label="Tutup pop-up"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Body (Scrollable) -->
          <section class="overflow-y-auto py-3 pr-1 sm:pr-2 text-xs sm:text-sm leading-relaxed flex-1">
            @include('landing.privacy-policy')
          </section>

          <!-- Modal Footer -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between shrink-0">
            <button
              type="button"
              onclick="closeKebijakanPrivasiModal(); openSyaratKetentuanModal();"
              class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:underline cursor-pointer"
            >
              &larr; Lihat Syarat &amp; Ketentuan
            </button>
            <button
              type="button"
              onclick="closeKebijakanPrivasiModal()"
              class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
@endsection

@push('scripts')
<!-- Scroll Listener & Navbar UI Scripts -->
      <script>
        function toggleMobileNavigation() {
          const button = document.getElementById('nav-mobile-toggle');
          const menu = document.getElementById('nav-mobile-menu');
          const backdrop = document.getElementById('nav-mobile-backdrop');
          const icon = document.getElementById('nav-mobile-icon');
          const isOpen = button.getAttribute('aria-expanded') === 'true';

          menu.classList.toggle('hidden', isOpen);
          backdrop.classList.toggle('hidden', isOpen);
          button.setAttribute('aria-expanded', String(!isOpen));
          button.setAttribute('aria-label', isOpen ? 'Buka navigasi' : 'Tutup navigasi');
          icon.innerHTML = isOpen
            ? '<path d="M4 6h16M4 12h16M4 18h16" />'
            : '<path d="m6 6 12 12M18 6 6 18" />';
        }

        (function () {
          const initialActiveWave = document.body.dataset.activeWave;
          const supportsTypewriter = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

          if (supportsTypewriter) {
            const startTypewriter = () => {
              const typewriterItems = Array.from(document.querySelectorAll('[data-typewriter]')).map(container => {
                const textElement = container.querySelector('[data-typewriter-text]');
                if (!textElement) return null;

                const characters = Array.from(textElement.textContent);
                container.style.width = `${container.getBoundingClientRect().width}px`;
                textElement.textContent = '';
                return { characters, textElement };
              }).filter(Boolean);

              if (typewriterItems.length === 0) return;

              const maxCharacterCount = Math.max(...typewriterItems.map(item => item.characters.length));
              let progress = 0;
              let isDeleting = false;

              function renderProgress(characterCount) {
                typewriterItems.forEach(({ characters, textElement }) => {
                  const visibleCharacterCount = characterCount === 0
                    ? 0
                    : characterCount === maxCharacterCount
                      ? characters.length
                      : Math.max(1, Math.floor(characterCount * characters.length / maxCharacterCount));
                  textElement.textContent = characters.slice(0, visibleCharacterCount).join('');
                });
              }

              function updateTypewriter() {
                if (!isDeleting && progress === maxCharacterCount) {
                  isDeleting = true;
                  window.setTimeout(updateTypewriter, 1400);
                  return;
                }

                if (isDeleting && progress === 0) {
                  isDeleting = false;
                  window.setTimeout(updateTypewriter, 350);
                  return;
                }

                progress += isDeleting ? -1 : 1;
                renderProgress(progress);
                window.setTimeout(updateTypewriter, isDeleting ? 45 : 70);
              }

              window.setTimeout(updateTypewriter, 100);
            };

            if (document.fonts?.ready) {
              document.fonts.ready.then(startTypewriter);
            } else {
              startTypewriter();
            }
          }

          window.setInterval(async function () {
            if (document.hidden) return;

            try {
              const response = await fetch("{{ route('home.wave-status') }}", {
                headers: { Accept: 'application/json' },
                cache: 'no-store',
              });
              if (!response.ok) return;

              const { wave } = await response.json();
              if (wave && wave !== initialActiveWave) {
                window.location.reload();
              }
            } catch {
              // A later poll can recover from temporary network errors.
            }
          }, 15000);

          const quotaItems = document.querySelectorAll('[data-quota-item]');
          const quotaColorClasses = {
            green: { badge: ['bg-emerald-100', 'text-emerald-800'], bar: 'bg-emerald-300' },
            yellow: { badge: ['bg-amber-100', 'text-amber-800'], bar: 'bg-amber-300' },
            red: { badge: ['bg-rose-100', 'text-rose-800'], bar: 'bg-rose-300' },
          };

          async function refreshQuotaStatus() {
            if (document.hidden || quotaItems.length === 0) return;

            try {
              const response = await fetch("{{ route('home.quota-status') }}", {
                headers: { Accept: 'application/json' },
                cache: 'no-store',
              });
              if (!response.ok) return;

              const { jurusan } = await response.json();
              if (!Array.isArray(jurusan)) return;

              const quotaById = new Map(jurusan.map(item => [String(item.id), item]));
              quotaItems.forEach(quotaItem => {
                const liveQuota = quotaById.get(quotaItem.dataset.quotaId);
                if (!liveQuota) return;

                const progressBar = quotaItem.querySelector('[data-quota-bar]');
                const remaining = quotaItem.querySelector('[data-quota-remaining]');
                const colorClasses = quotaColorClasses[liveQuota.warna];
                if (!progressBar || !remaining || !colorClasses) return;
                progressBar.classList.remove('bg-emerald-300', 'bg-amber-300', 'bg-rose-300');
                progressBar.classList.add(colorClasses.bar);
                progressBar.style.width = `${liveQuota.persentase}%`;
                progressBar.parentElement.setAttribute('aria-valuenow', String(liveQuota.persentase));
                remaining.textContent = liveQuota.tersisa > 0
                  ? `${liveQuota.tersisa} kursi tersedia`
                  : 'Kuota telah terpenuhi';
              });
            } catch {
              // The next poll can recover from temporary network errors.
            }
          }

          refreshQuotaStatus();
          document.addEventListener('visibilitychange', () => {
            if (!document.hidden) refreshQuotaStatus();
          });
          window.setInterval(refreshQuotaStatus, 10000);

          const quotaTiltCards = document.querySelectorAll('[data-quota-tilt]');
          const supportsQuotaTilt = window.matchMedia('(hover: hover) and (pointer: fine)').matches
            && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

          if (supportsQuotaTilt) {
            quotaTiltCards.forEach(card => {
              const maxTilt = 15;
              const shadowMultiplier = 26;
              let moveRaf = null;

              function updateTiltPosition(clientX, clientY) {
                const cardBounds = card.getBoundingClientRect();
                const relX = clientX - cardBounds.left;
                const relY = clientY - cardBounds.top;

                const pointerX = relX / cardBounds.width - 0.5;
                const pointerY = relY / cardBounds.height - 0.5;

                const tiltX = -pointerY * maxTilt;
                const tiltY = pointerX * maxTilt;
                const shadowX = -pointerX * shadowMultiplier;
                const shadowY = -pointerY * shadowMultiplier + 22;

                const mouseX = (relX / cardBounds.width) * 100;
                const mouseY = (relY / cardBounds.height) * 100;

                card.style.transform = `perspective(950px) rotateX(${tiltX.toFixed(2)}deg) rotateY(${tiltY.toFixed(2)}deg) translateZ(14px) scale3d(1.025, 1.025, 1.025)`;
                card.style.setProperty('--shadow-x', `${shadowX.toFixed(1)}px`);
                card.style.setProperty('--shadow-y', `${shadowY.toFixed(1)}px`);
                card.style.setProperty('--mouse-x', `${mouseX.toFixed(1)}%`);
                card.style.setProperty('--mouse-y', `${mouseY.toFixed(1)}%`);
                card.style.setProperty('--glare-opacity', '1');
              }

              card.addEventListener('pointerenter', event => {
                card.classList.add('is-tilting');
                updateTiltPosition(event.clientX, event.clientY);
              });

              card.addEventListener('pointermove', event => {
                if (moveRaf) cancelAnimationFrame(moveRaf);
                const clientX = event.clientX;
                const clientY = event.clientY;

                moveRaf = requestAnimationFrame(() => {
                  moveRaf = null;
                  updateTiltPosition(clientX, clientY);
                });
              });

              card.addEventListener('pointerleave', () => {
                if (moveRaf) {
                  cancelAnimationFrame(moveRaf);
                  moveRaf = null;
                }
                card.classList.remove('is-tilting');
                // Spring rebound yang kenyal & membal natural kembali ke posisi asal
                card.style.transform = 'perspective(950px) rotateX(0deg) rotateY(0deg) translateZ(0px) scale3d(1, 1, 1)';
                card.style.setProperty('--shadow-x', '0px');
                card.style.setProperty('--shadow-y', '22px');
                card.style.setProperty('--glare-opacity', '0');
              });
            });
          }

          const navbar = document.getElementById('main-navbar');
          if (!navbar) return;

          const mobileNavToggle = document.getElementById('nav-mobile-toggle');
          const mobileNavMenu = document.getElementById('nav-mobile-menu');
          const mobileNavBackdrop = document.getElementById('nav-mobile-backdrop');
          const mobileNavIcon = document.getElementById('nav-mobile-icon');

          function closeMobileNav() {
            mobileNavMenu.classList.add('hidden');
            mobileNavBackdrop.classList.add('hidden');
            mobileNavToggle.setAttribute('aria-expanded', 'false');
            mobileNavToggle.setAttribute('aria-label', 'Buka navigasi');
            mobileNavIcon.innerHTML = '<path d="M4 6h16M4 12h16M4 18h16" />';
          }

          mobileNavMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMobileNav);
          });

          // Realtime Navbar Scrollspy
          const desktopNavLinks = document.querySelectorAll('#nav-desktop-links a[href^="#"]');
          const mobileNavLinks = document.querySelectorAll('#nav-mobile-menu a[href^="#"]');

          const scrollSections = [
            'hero-home-section',
            'section-cara-daftar',
            'section-jalur',
            'section-jadwal',
            'section-simulasi-biaya',
            'section-faq'
          ];

          let isScrollLocked = false;
          let scrollLockTimer = null;

          function setActiveNavLink(activeId) {
            desktopNavLinks.forEach(link => {
              if (link.getAttribute('href') === '#' + activeId) {
                link.classList.add('active');
              } else {
                link.classList.remove('active');
              }
            });

            mobileNavLinks.forEach(link => {
              if (link.getAttribute('href') === '#' + activeId) {
                link.classList.add('active');
              } else {
                link.classList.remove('active');
              }
            });
          }

          function updateActiveNavLink() {
            if (isScrollLocked) return;

            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;

            // Paling bawah halaman -> FAQ
            if (scrollY + windowHeight >= documentHeight - 60) {
              setActiveNavLink('section-faq');
              return;
            }

            // Paling atas halaman -> Beranda
            if (scrollY < 100) {
              setActiveNavLink('hero-home-section');
              return;
            }

            const thresholdOffset = 140;
            let currentActiveId = 'hero-home-section';

            for (let i = 0; i < scrollSections.length; i++) {
              const sectionEl = document.getElementById(scrollSections[i]);
              if (sectionEl) {
                const rect = sectionEl.getBoundingClientRect();
                if (rect.top <= thresholdOffset) {
                  currentActiveId = scrollSections[i];
                }
              }
            }

            setActiveNavLink(currentActiveId);
          }

          function handleNavClick(link) {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
              const targetId = href.slice(1);
              setActiveNavLink(targetId);

              isScrollLocked = true;
              if (scrollLockTimer) clearTimeout(scrollLockTimer);
              scrollLockTimer = setTimeout(() => {
                isScrollLocked = false;
              }, 900);
            }
          }

          desktopNavLinks.forEach(link => {
            link.addEventListener('click', () => handleNavClick(link));
          });

          mobileNavLinks.forEach(link => {
            link.addEventListener('click', () => handleNavClick(link));
          });

          function updateNavbarBackground() {
            if (window.scrollY > 0) {
              navbar.classList.remove('nav-at-top');
              navbar.classList.add('nav-scrolled');
            } else {
              navbar.classList.remove('nav-scrolled');
              navbar.classList.add('nav-at-top');
            }
          }

          const floatingWaAside = document.getElementById('floating-wa-aside');
          function updateFloatingWaVisibility() {
            if (!floatingWaAside) return;
            const currentScrollY = window.scrollY || window.pageYOffset || 0;
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
            const totalHeight = Math.max(
              document.body.scrollHeight,
              document.documentElement.scrollHeight,
              document.body.offsetHeight,
              document.documentElement.offsetHeight
            );

            if (totalHeight <= viewportHeight) {
              floatingWaAside.classList.remove('is-hidden-at-bottom');
              floatingWaAside.removeAttribute('aria-hidden');
              return;
            }

            // Deteksi ketika user sudah mentok di paling bawah (toleransi 30px untuk DPI zoom / subpixel)
            const isAtBottom = (viewportHeight + currentScrollY) >= (totalHeight - 30);
            if (isAtBottom) {
              floatingWaAside.classList.add('is-hidden-at-bottom');
              floatingWaAside.setAttribute('aria-hidden', 'true');
            } else {
              floatingWaAside.classList.remove('is-hidden-at-bottom');
              floatingWaAside.removeAttribute('aria-hidden');
            }
          }

          let isScrollThrottled = false;
          function handleScroll() {
            updateNavbarBackground();
            updateFloatingWaVisibility();
            if (!isScrollThrottled) {
              window.requestAnimationFrame(() => {
                updateActiveNavLink();
                isScrollThrottled = false;
              });
              isScrollThrottled = true;
            }
          }

          window.addEventListener('scroll', handleScroll, { passive: true });
          window.addEventListener('resize', updateFloatingWaVisibility, { passive: true });
          // Jalankan saat inisialisasi awal
          updateNavbarBackground();
          updateActiveNavLink();
          updateFloatingWaVisibility();

          // Dropdown menu hover and interactive logic
          const menuContainer = document.getElementById('user-menu-container');
          const userDropdown = document.getElementById('user-dropdown-menu');
          const userBtn = document.getElementById('nav-user-btn-auth');
          const chevron = document.getElementById('chevron-icon');
          let hideUserDropdownTimer = null;

          function showUserDropdown() {
            if (hideUserDropdownTimer) {
              clearTimeout(hideUserDropdownTimer);
              hideUserDropdownTimer = null;
            }
            if (userDropdown) {
              userDropdown.classList.remove('invisible', 'opacity-0', 'translate-y-1.5', 'scale-95', 'pointer-events-none');
              userDropdown.classList.add('visible', 'opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
            }
            if (chevron) {
              chevron.classList.add('rotate-180');
            }
            if (userBtn) {
              userBtn.setAttribute('aria-expanded', 'true');
            }
          }

          function hideUserDropdown() {
            if (userDropdown) {
              userDropdown.classList.add('invisible', 'opacity-0', 'translate-y-1.5', 'scale-95', 'pointer-events-none');
              userDropdown.classList.remove('visible', 'opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
            }
            if (chevron) {
              chevron.classList.remove('rotate-180');
            }
            if (userBtn) {
              userBtn.setAttribute('aria-expanded', 'false');
            }
          }

          function scheduleHideUserDropdown() {
            hideUserDropdownTimer = setTimeout(() => {
              hideUserDropdown();
            }, 180);
          }

          if (menuContainer) {
            menuContainer.addEventListener('mouseenter', showUserDropdown);
            menuContainer.addEventListener('mouseleave', scheduleHideUserDropdown);
          }

          if (userBtn) {
            userBtn.addEventListener('click', function (e) {
              e.stopPropagation();
              if (userDropdown && userDropdown.classList.contains('visible')) {
                hideUserDropdown();
              } else {
                showUserDropdown();
              }
            });
          }

          // Close dropdown when clicking outside
          document.addEventListener('click', function (e) {
            if (menuContainer && !menuContainer.contains(e.target)) {
              hideUserDropdown();
            }
            if (!navbar.contains(e.target)) {
              closeMobileNav();
            }
          });

          // FAQ Accordion: pastikan hanya satu FAQ yang terbuka sekaligus
          const faqDetails = document.querySelectorAll('#section-faq details');
          faqDetails.forEach((detail) => {
            detail.addEventListener('toggle', () => {
              if (detail.open) {
                faqDetails.forEach((otherDetail) => {
                  if (otherDetail !== detail && otherDetail.open) {
                    otherDetail.open = false;
                  }
                });
              }
            });
          });

          // ==============================================================
          // CARA PENDAFTARAN - CARD SLIDER & ZOOM EFFECT
          // ==============================================================
          const caraSliderContainers = document.querySelectorAll('.cara-slider-container');
          const sliderControllers = [];

          caraSliderContainers.forEach((container) => {
            const sliderId = container.dataset.slider;
            const slides = Array.from(container.querySelectorAll('.cara-slide'));
            if (slides.length <= 1) return;

            const prevBtn = document.querySelector(`.cara-slider-prev[data-slider-target="${sliderId}"]`);
            const nextBtn = document.querySelector(`.cara-slider-next[data-slider-target="${sliderId}"]`);

            let currentIndex = 0;
            let transitionTimer = null;
            let autoPlayTimer = null;
            const totalSlides = slides.length;
            const animDuration = 260; // Durasi transisi geser responsif & instan tanpa jeda delay
            const animEase = 'cubic-bezier(0.22, 1, 0.36, 1)';

            // Inisialisasi posisi slide awal
            slides.forEach((slide, idx) => {
              slide.style.transition = 'none';
              if (idx === 0) {
                slide.style.transform = 'translateX(0)';
                slide.style.opacity = '1';
                slide.style.zIndex = '2';
                slide.classList.add('is-active');
              } else {
                slide.style.transform = 'translateX(100%)';
                slide.style.opacity = '0';
                slide.style.zIndex = '1';
                slide.classList.remove('is-active');
              }
            });

            // Force reflow
            void container.offsetHeight;

            function goToSlide(newIndex, direction = 'next') {
              if (newIndex === currentIndex) return;

              // Batalkan timeout transisi sebelumnya jika user mengklik cepat berturut-turut
              if (transitionTimer) {
                clearTimeout(transitionTimer);
                transitionTimer = null;
              }

              const currentSlide = slides[currentIndex];
              const targetSlide = slides[newIndex];

              const enterX = direction === 'next' ? '100%' : '-100%';
              const exitX = direction === 'next' ? '-100%' : '100%';

              // Bersihkan dan parkirkan slide lainnya agar tidak menumpuk saat klik cepat
              slides.forEach((slide, idx) => {
                if (idx !== currentIndex && idx !== newIndex) {
                  slide.style.transition = 'none';
                  slide.style.transform = `translateX(${exitX})`;
                  slide.style.opacity = '0';
                  slide.style.zIndex = '1';
                  slide.classList.remove('is-active');
                }
              });

              // Siapkan slide target pada posisi awal
              targetSlide.style.transition = 'none';
              targetSlide.style.transform = `translateX(${enterX})`;
              targetSlide.style.opacity = '0';
              targetSlide.style.zIndex = '2';
              targetSlide.classList.remove('is-active');

              // Force reflow sebelum memulai transisi
              void targetSlide.offsetHeight;

              // Jalankan animasi transisi berpindah (responsif & instan)
              currentSlide.style.zIndex = '1';
              currentSlide.style.transition = `transform ${animDuration}ms ${animEase}, opacity ${animDuration}ms ${animEase}`;
              currentSlide.style.transform = `translateX(${exitX})`;
              currentSlide.style.opacity = '0';
              currentSlide.classList.remove('is-active');

              targetSlide.style.transition = `transform ${animDuration}ms ${animEase}, opacity ${animDuration}ms ${animEase}`;
              targetSlide.style.transform = 'translateX(0)';
              targetSlide.style.opacity = '1';
              targetSlide.classList.add('is-active');

              const prevIndex = currentIndex;
              currentIndex = newIndex;

              transitionTimer = setTimeout(() => {
                transitionTimer = null;
                if (slides[prevIndex] && prevIndex !== currentIndex) {
                  slides[prevIndex].style.transition = 'none';
                }
              }, animDuration);
            }

            function resetToFirst() {
              stopAutoPlay();
              if (transitionTimer) {
                clearTimeout(transitionTimer);
                transitionTimer = null;
              }
              currentIndex = 0;
              slides.forEach((slide, idx) => {
                slide.style.transition = 'none';
                slide.classList.remove('is-active');
                if (idx === 0) {
                  slide.style.transform = 'translateX(0)';
                  slide.style.opacity = '1';
                  slide.style.zIndex = '2';
                } else {
                  slide.style.transform = 'translateX(100%)';
                  slide.style.opacity = '0';
                  slide.style.zIndex = '1';
                }
              });
              void container.offsetHeight;
              slides[0].classList.add('is-active');
            }

            function nextSlide() {
              const nextIdx = (currentIndex + 1) % totalSlides;
              goToSlide(nextIdx, 'next');
            }

            function prevSlide() {
              const prevIdx = (currentIndex - 1 + totalSlides) % totalSlides;
              goToSlide(prevIdx, 'prev');
            }

            function startAutoPlay() {
              stopAutoPlay();
              autoPlayTimer = setInterval(nextSlide, 4500);
            }

            function stopAutoPlay() {
              if (autoPlayTimer) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = null;
              }
            }

            const cardEl = container.closest('.group');

            if (nextBtn) {
              nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                nextSlide();
                if (!cardEl || !cardEl.matches(':hover')) {
                  startAutoPlay();
                }
              });
            }

            if (prevBtn) {
              prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                prevSlide();
                if (!cardEl || !cardEl.matches(':hover')) {
                  startAutoPlay();
                }
              });
            }

            // Mobile touch swipe gesture
            let touchStartX = 0;
            let touchStartY = 0;
            container.addEventListener('touchstart', (e) => {
              touchStartX = e.changedTouches[0].screenX;
              touchStartY = e.changedTouches[0].screenY;
              stopAutoPlay();
            }, { passive: true });

            container.addEventListener('touchend', (e) => {
              const touchEndX = e.changedTouches[0].screenX;
              const touchEndY = e.changedTouches[0].screenY;
              const diffX = touchEndX - touchStartX;
              const diffY = touchEndY - touchStartY;

              if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 35) {
                if (diffX < 0) {
                  nextSlide();
                } else {
                  prevSlide();
                }
              }
              startAutoPlay();
            }, { passive: true });

            // Pause autoplay saat kursor mouse di atas kartu
            if (cardEl) {
              cardEl.addEventListener('mouseenter', stopAutoPlay);
              cardEl.addEventListener('mouseleave', startAutoPlay);
            }

            sliderControllers.push({
              resetToFirst,
              startAutoPlay,
              stopAutoPlay,
            });
          });

          // Deteksi posisi user: saat berada di section cara pendaftaran, gambar selalu di tahap 1
          const caraDaftarSection = document.getElementById('section-cara-daftar');
          let isCaraDaftarActive = false;

          function enterCaraDaftar() {
            sliderControllers.forEach((ctrl) => {
              ctrl.resetToFirst();
              ctrl.startAutoPlay();
            });
          }

          function leaveCaraDaftar() {
            sliderControllers.forEach((ctrl) => {
              ctrl.stopAutoPlay();
              ctrl.resetToFirst();
            });
          }

          if (caraDaftarSection && 'IntersectionObserver' in window) {
            const sectionObserver = new IntersectionObserver((entries) => {
              entries.forEach((entry) => {
                if (entry.isIntersecting) {
                  if (!isCaraDaftarActive) {
                    isCaraDaftarActive = true;
                    enterCaraDaftar();
                  }
                } else {
                  if (isCaraDaftarActive) {
                    isCaraDaftarActive = false;
                    leaveCaraDaftar();
                  }
                }
              });
            }, {
              threshold: 0.15,
            });

            sectionObserver.observe(caraDaftarSection);
          } else {
            // Fallback jika IntersectionObserver tidak tersedia
            sliderControllers.forEach((ctrl) => ctrl.startAutoPlay());
          }

          // Event listener klik untuk semua link yang mengarah ke #section-cara-daftar
          document.querySelectorAll('a[href="#section-cara-daftar"]').forEach((link) => {
            link.addEventListener('click', () => {
              enterCaraDaftar();
            });
          });
        })();

        // ==============================================================
        // SIMULASI KALKULATOR BIAYA MASUK SPMB 2027–2028
        // ==============================================================
        (function () {
          const calcShell = document.getElementById('spmb-calc-shell');
          if (!calcShell) return;

          const BASE_DOCUMENTS = [
            'Fotokopi Ijazah / SKL',
            'Fotokopi Akta Kelahiran',
            'Fotokopi Kartu Keluarga (KK)',
            'Fotokopi / Bukti NISN'
          ];

          const PRICE_GROUPS = {
            TI: {
              label: 'Jurusan TI (TJKT, PPLG, DKV, BD)',
              pendaftaran: 150000,
              mosDkk: 450000,
              asuransi: 100000,
              seragam: 1975000,
              paketWajib: 2525000,
              biayaTetap: 2550000,
              dpp: 650000,
              spp: 650000,
              osis: 25000,
              totalAwalNormal: 4000000
            },
            KECANTIKAN: {
              label: 'Jurusan Tata Kecantikan',
              pendaftaran: 150000,
              mosDkk: 450000,
              asuransi: 100000,
              seragam: 1975000,
              paketWajib: 2525000,
              biayaTetap: 2550000,
              dpp: 500000,
              spp: 450000,
              osis: 25000,
              totalAwalNormal: 3650000
            }
          };

          const JALUR_OPTIONS = [
            {
              id: 'umum',
              title: 'Jalur Umum',
              cardTitle: 'Jalur Umum',
              shortLabel: 'Jalur Umum',
              badge: 'Ikut Gelombang',
              hasSub: false,
              extraDocs: []
            },
            {
              id: 'prestasi',
              title: 'Jalur Prestasi (Akademik & Non-Akademik)',
              cardTitle: 'Jalur Prestasi',
              shortLabel: 'Jalur Prestasi',
              badge: 'SPP 15–25% · DPP 50%',
              desc: null,
              hasSub: true,
              subTitle: 'Pilih Tingkat Prestasi',
              subOptions: [
                {
                  id: 'prestasi_sekolah_kab',
                  title: 'Juara 1, 2, 3 Kelas (Sekolah) atau Tingkat Desa/Kabupaten',
                  cardTitle: 'Juara Kelas',
                  shortLabel: 'Prestasi Kelas/Desa/Kab',
                  badge: 'SPP 15% (1 Thn) · DPP 50% (3 Thn)',
                  sppDiscount: 15,
                  sppDuration: '1 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: ['Fotokopi Sertifikat/Piagam Juara dilegalisir']
                },
                {
                  id: 'prestasi_prov_nas',
                  title: 'Tingkat Provinsi atau Nasional',
                  cardTitle: 'Provinsi / Nasional',
                  shortLabel: 'Prestasi Prov/Nasional',
                  badge: 'SPP 20% (3 Thn) · DPP 50% (3 Thn)',
                  sppDiscount: 20,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: ['Fotokopi Sertifikat/Piagam Juara dilegalisir']
                },
                {
                  id: 'prestasi_internasional',
                  title: 'Tingkat Internasional',
                  cardTitle: 'Internasional',
                  shortLabel: 'Prestasi Internasional',
                  badge: 'SPP 25% (3 Thn) · DPP 50% (3 Thn)',
                  sppDiscount: 25,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: ['Fotokopi Sertifikat/Piagam Juara dilegalisir']
                }
              ]
            },
            {
              id: 'minat_bakat',
              title: 'Jalur Minat & Bakat (Seni/Olahraga)',
              cardTitle: 'Jalur Minat & Bakat',
              shortLabel: 'Minat & Bakat',
              badge: 'SPP 15% · DPP 50%',
              desc: 'Prestasi Seni & Olahraga · DPP berlaku 1 Tahun.',
              hasSub: false,
              sppDiscount: 15,
              sppDuration: '3 Tahun',
              dppDiscount: 50,
              dppDuration: '1 Tahun',
              extraDocs: ['Piagam / Portofolio Bidang Seni atau Olahraga']
            },
            {
              id: 'afirmasi',
              title: 'Jalur Afirmasi (Keluarga Kurang Mampu)',
              cardTitle: 'Jalur Afirmasi',
              shortLabel: 'Jalur Afirmasi',
              badge: 'Kuota 25 · SPP 15% · DPP 50%',
              desc: 'Berlaku 3 tahun (kebijakan Kepala Sekolah) · Batas berkas 1 minggu setelah registrasi.',
              hasSub: false,
              sppDiscount: 15,
              sppDuration: '3 Tahun',
              dppDiscount: 50,
              dppDuration: '3 Tahun',
              extraDocs: [
                'Surat Keterangan Kurang Mampu dari Kepala Desa',
                'Surat Permohonan Keringanan Biaya (batas 1 minggu setelah registrasi)'
              ],
              specialNote: 'Jalur Afirmasi (Kuota 25 Siswa): Diskon SPP 15% berlaku 3 tahun sesuai kebijakan Kepala Sekolah. Batas kumpul berkas 1 minggu setelah registrasi.'
            },
            {
              id: 'kip',
              title: 'Jalur KIP (Kartu Indonesia Pintar)',
              cardTitle: 'Jalur KIP',
              shortLabel: 'Jalur KIP',
              badge: 'Kuota 50 · SPP 15% · DPP 50%',
              desc: 'Diskon SPP 15% & DPP 50% berlaku 3 tahun.',
              hasSub: false,
              sppDiscount: 15,
              sppDuration: '3 Tahun',
              dppDiscount: 50,
              dppDuration: '3 Tahun',
              extraDocs: [
                'Fotokopi Kartu Indonesia Pintar (KIP)',
                'Surat Permohonan Keringanan Biaya'
              ]
            },
            {
              id: 'khusus',
              title: 'Jalur Khusus',
              cardTitle: 'Jalur Khusus',
              shortLabel: 'Jalur Khusus',
              badge: '8 Sub-Kategori',
              desc: 'MOU, Saudara Kandung, Guru/Staf, Panti Asuhan, Ojol, Minat Bakat, & Inklusi.',
              subTitle: 'Pilih Sub-Kategori Jalur Khusus',
              subOptions: [
                {
                  id: 'khusus_mou',
                  title: 'Warga Desa / SMP / MTs MOU',
                  shortLabel: 'Desa/SMP/MTs MOU',
                  badge: 'SPP 30% (3 Thn) · DPP 50% (3 Thn)',
                  hasMouTimingToggle: true,
                  sppDiscount: 30,
                  sppDiscountLate: 10,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: ['Bukti Domisili Warga Desa MOU / Ijazah-SKL SMP/MTs MOU']
                },
                {
                  id: 'khusus_kembar',
                  title: 'Saudara Kembar (Sama-sama mendaftar di SMK TI Bali Global Badung)',
                  shortLabel: 'Saudara Kembar',
                  badge: 'SPP 20% (3 Thn) · DPP 50% (3 Thn)',
                  sppDiscount: 20,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: [
                    'Surat pernyataan warga sekolah bermaterai 10.000',
                    'Surat permohonan untuk keringanan biaya'
                  ]
                },
                {
                  id: 'khusus_saudara',
                  title: 'Memiliki Saudara Kandung yang Sedang Bersekolah di SMK TI Bali Global Badung',
                  shortLabel: 'Saudara Kandung',
                  badge: 'SPP 20% (3 Thn) · DPP 50% (3 Thn)',
                  sppDiscount: 20,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: [
                    'Surat pernyataan warga sekolah bermaterai 10.000',
                    'Surat permohonan untuk keringanan biaya'
                  ]
                },
                {
                  id: 'khusus_anak_guru',
                  title: 'Anak Kandung Guru/Staf SMK TI Bali Global Badung & STIKOM Grup',
                  shortLabel: 'Anak Kandung Guru/Staf',
                  badge: 'SPP 70% (3 Thn) · DPP 50% (3 Thn)',
                  sppDiscount: 70,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: ['SK / Keterangan Aktif Guru/Staf SMK TI Bali Global Badung & STIKOM Grup']
                },
                {
                  id: 'khusus_kerabat_guru',
                  title: 'Kerabat Guru/Staf SMK TI Bali Global Badung & STIKOM Grup',
                  shortLabel: 'Kerabat Guru/Staf',
                  badge: 'SPP 15% (3 Thn) · DPP 50% (3 Thn)',
                  sppDiscount: 15,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: ['Surat Rekomendasi Kerabat dari Guru/Staf SMK TI & STIKOM Grup']
                },
                {
                  id: 'khusus_panti',
                  title: 'Panti Asuhan MOU',
                  shortLabel: 'Panti Asuhan MOU',
                  badge: 'SPP 25% (3 Thn) · DPP 50% (3 Thn)',
                  sppDiscount: 25,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '3 Tahun',
                  extraDocs: ['Surat Keterangan Resmi Pengurus Panti Asuhan MOU']
                },
                {
                  id: 'khusus_ojol',
                  title: 'Putra/Putri Ojek Online',
                  shortLabel: 'Putra/Putri Ojol',
                  badge: 'SPP 20% (3 Thn) · DPP 50% (1 Thn)',
                  sppDiscount: 20,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '1 Tahun',
                  extraDocs: ['Bukti Akun Aktif Mitra Ojek Online Orang Tua/Wali']
                },
                {
                  id: 'khusus_minat_bakat',
                  title: 'Jalur Minat & Bakat (Seni/Olahraga)',
                  shortLabel: 'Minat & Bakat',
                  badge: 'SPP 15% (3 Thn) · DPP 50% (1 Thn)',
                  sppDiscount: 15,
                  sppDuration: '3 Tahun',
                  dppDiscount: 50,
                  dppDuration: '1 Tahun',
                  extraDocs: ['Piagam / Portofolio Bidang Seni atau Olahraga']
                },
                {
                  id: 'khusus_inklusi',
                  title: 'Jalur Inklusi (Berkebutuhan Khusus/Cacat Fisik)',
                  shortLabel: 'Jalur Inklusi',
                  badge: 'Kebijakan Kepsek + Jurusan',
                  isInklusi: true,
                  sppDiscount: 0,
                  sppDuration: 'Kebijakan Kepala Sekolah',
                  dppDiscount: 0,
                  dppDuration: 'Kebijakan Kepala Sekolah',
                  extraDocs: ['Surat Keterangan Pendukung / Asesmen untuk Kebijakan Kepala Sekolah'],
                  specialNote: 'Catatan Jalur Inklusi: Diskon SPP & DPP mengikuti kebijakan Kepala Sekolah, dan tetap mendapat tambahan diskon SPP jurusan (3 tahun).'
                }
              ]
            }
          ];

          const JURUSAN_OPTIONS = [
            {
              id: 'TJKT',
              title: 'TJKT (Teknik Jaringan Komputer & Telekomunikasi)',
              cardTitle: 'TJKT',
              shortLabel: 'TJKT',
              group: 'TI',
              sppBonus: 10,
              badge: '+10% SPP (3 Thn)'
            },
            {
              id: 'PPLG',
              title: 'PPLG (Pengembangan Perangkat Lunak & Gim)',
              cardTitle: 'PPLG',
              shortLabel: 'PPLG',
              group: 'TI',
              sppBonus: 5,
              badge: '+5% SPP (3 Thn)'
            },
            {
              id: 'DKV',
              title: 'DKV (Desain Komunikasi Visual)',
              cardTitle: 'DKV',
              shortLabel: 'DKV',
              group: 'TI',
              sppBonus: 5,
              badge: '+5% SPP (3 Thn)'
            },
            {
              id: 'BD',
              title: 'BD (Bisnis Digital)',
              cardTitle: 'BD',
              shortLabel: 'BD',
              group: 'TI',
              sppBonus: 5,
              badge: '+5% SPP (3 Thn)'
            },
            {
              id: 'TKC',
              title: 'TKC - Tata Kecantikan',
              cardTitle: 'TKC',
              shortLabel: 'TKC - Tata Kecantikan',
              group: 'KECANTIKAN',
              sppBonus: 5,
              badge: '+5% SPP 3 Thn'
            }
          ];

          const GELOMBANG_OPTIONS = [
            {
              id: 'gelombang_khusus',
              title: 'Gelombang Khusus',
              period: '07 September 2026 – 01 Januari 2027',
              shortLabel: 'Gel. Khusus',
              regDiscount: 100,
              regBadge: 'Pendaftaran Gratis 100%',
              umumSppDiscount: 15,
              umumSppDuration: '3 Tahun',
              umumDppDiscount: 50,
              umumDppDuration: '3 Tahun',
              extraDoc: 'Share foto pendaftaran di medsos, ulasan Google Review & follow medsos sekolah',
              note: 'Syarat gratis pendaftaran: share medsos pribadi, Google Review & follow medsos sekolah.'
            },
            {
              id: 'gelombang_1',
              title: 'Gelombang 1',
              period: '02 Januari 2027 – 11 Mei 2027',
              shortLabel: 'Gelombang 1',
              regDiscount: 50,
              regBadge: 'Diskon Daftar 50% (Rp 75rb)',
              umumSppDiscount: 10,
              umumSppDuration: '3 Tahun',
              umumDppDiscount: 30,
              umumDppDuration: '3 Tahun',
              extraDoc: null,
              note: null
            },
            {
              id: 'gelombang_2',
              title: 'Gelombang 2',
              period: '12 Mei 2027 – 19 Juli 2027',
              shortLabel: 'Gelombang 2',
              regDiscount: 50,
              regBadge: 'Diskon Daftar 50% (Rp 75rb)',
              umumSppDiscount: 5,
              umumSppDuration: '3 Tahun',
              umumDppDiscount: 25,
              umumDppDuration: '3 Tahun',
              extraDoc: null,
              note: null
            }
          ];

          const VOUCHER_OPTIONS = [
            {
              id: 'tanpa_voucher',
              title: 'Tidak Memiliki Voucher',
              cardTitle: 'Tanpa Voucher',
              shortLabel: 'Tanpa Voucher',
              badge: 'Ikut Gelombang',
              desc: 'Biaya pendaftaran mengikuti potongan gelombang di Langkah 03.',
              overrideRegDiscount: null,
              extraDoc: null
            },
            {
              id: 'voucher_alumni',
              title: 'Voucher Rekomendasi Siswa Aktif / Alumni',
              cardTitle: 'Voucher Alumni / Siswa',
              shortLabel: 'Voucher Alumni/Siswa',
              badge: 'Gratis 100% (Rp 0)',
              desc: 'Gratis biaya pendaftaran meskipun di Gelombang 1 atau 2.',
              overrideRegDiscount: 100,
              extraDoc: 'Voucher Rekomendasi Siswa Aktif / Alumni'
            },
            {
              id: 'voucher_hut',
              title: 'Voucher HUT SMK TI Bali Global Badung',
              cardTitle: 'Voucher HUT SMK TI',
              shortLabel: 'Voucher HUT SMK TI',
              badge: 'Gratis 100% (Rp 0)',
              desc: 'Gratis biaya pendaftaran meskipun di Gelombang 1 atau 2.',
              overrideRegDiscount: 100,
              extraDoc: 'Voucher HUT SMK TI Bali Global Badung'
            }
          ];

          const STEP_META = {
            1: {
              badge: '01 / 04',
              title: 'Pilih Jalur Pendaftaran',
            },
            2: {
              badge: '02 / 04',
              title: 'Pilih Kompetensi Keahlian', 
            },
            3: {
              badge: '03 / 04',
              title: 'Pilih Gelombang Pendaftaran',
            },
            4: {
              badge: '04 / 04',
              title: 'Kepemilikan Voucher Khusus',
            }
          };

          const state = {
            step: 1,
            jalurId: null,
            subJalurId: null,
            mouLatePeriod: false,
            jurusanId: null,
            gelombangId: null,
            voucherId: null
          };

          // DOM Elements
          const quizView = document.getElementById('spmb-quiz-view');
          const resultView = document.getElementById('spmb-result-view');
          const stepBadge = document.getElementById('spmb-step-badge');
          const btnQuickReset = document.getElementById('spmb-btn-quick-reset');
          const segBtns = Array.from(document.querySelectorAll('.spmb-seg-btn'));
          const questionPanel = document.getElementById('spmb-question-panel');
          const questionTitle = document.getElementById('spmb-question-title');
          const questionDesc = document.getElementById('spmb-question-desc');
          const optionsContainer = document.getElementById('spmb-options-container');
          const subOptionsWrapper = document.getElementById('spmb-suboptions-wrapper');
          const subOptionsTitle = document.getElementById('spmb-suboptions-title');
          const subOptionsContainer = document.getElementById('spmb-suboptions-container');
          const stepAlert = document.getElementById('spmb-step-alert');
          const stepAlertText = document.getElementById('spmb-step-alert-text');
          const btnPrev = document.getElementById('spmb-btn-prev');
          const btnNext = document.getElementById('spmb-btn-next');
          const btnNextLabel = document.getElementById('spmb-btn-next-label');

          // Live Preview Elements
          const liveJurusan = document.getElementById('spmb-live-jurusan');
          const liveKelompok = document.getElementById('spmb-live-kelompok');
          const liveJalurPill = document.getElementById('spmb-live-jalur-pill');
          const liveSppBadge = document.getElementById('spmb-live-spp-badge');
          const liveSppNote = document.getElementById('spmb-live-spp-note');
          const liveDppBadge = document.getElementById('spmb-live-dpp-badge');
          const liveDppNote = document.getElementById('spmb-live-dpp-note');
          const liveRegBadge = document.getElementById('spmb-live-reg-badge');
          const liveRegNote = document.getElementById('spmb-live-reg-note');
          const liveBerkasCount = document.getElementById('spmb-live-berkas-count');
          const liveBerkasList = document.getElementById('spmb-live-berkas-list');
          const sumJalur = document.getElementById('spmb-sum-jalur');
          const sumJurusan = document.getElementById('spmb-sum-jurusan');
          const sumGelombang = document.getElementById('spmb-sum-gelombang');
          const sumVoucher = document.getElementById('spmb-sum-voucher');

          // Result View Elements
          const resPills = document.getElementById('spmb-res-pills');
          const resMainTotal = document.getElementById('spmb-res-main-total');
          const resSavingBadge = document.getElementById('spmb-res-saving-badge');
          const resSpecialNote = document.getElementById('spmb-res-special-note');
          const resRegTag = document.getElementById('spmb-res-reg-tag');
          const resRegFee = document.getElementById('spmb-res-reg-fee');
          const resRegDesc = document.getElementById('spmb-res-reg-desc');
          const resMonthlyFee = document.getElementById('spmb-res-monthly-fee');
          const resMonthlyDesc = document.getElementById('spmb-res-monthly-desc');
          const resSemesterFee = document.getElementById('spmb-res-semester-fee');
          const resSemesterDesc = document.getElementById('spmb-res-semester-desc');
          const resJurusanGroup = document.getElementById('spmb-res-jurusan-group');
          const resBreakdownRows = document.getElementById('spmb-res-breakdown-rows');
          const resPeriodicRows = document.getElementById('spmb-res-periodic-rows');
          const resNormalNote = document.getElementById('spmb-res-normal-note');
          const resTableTotal = document.getElementById('spmb-res-table-total');
          const resDurationBox = document.getElementById('spmb-res-duration-box');
          const getDocsListEl = () => document.getElementById('spmb-res-docs-list');
          const btnEdit = document.getElementById('spmb-btn-edit');
          const btnRestart = document.getElementById('spmb-btn-restart');

          function formatRupiah(amount) {
            return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
          }

          function escapeHtml(str) {
            return String(str)
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;');
          }

          function getSelectedJalurObj() {
            return JALUR_OPTIONS.find(j => j.id === state.jalurId) || null;
          }

          function getSelectedSubJalurObj() {
            const jalur = getSelectedJalurObj();
            if (!jalur || !jalur.hasSub) return null;
            return jalur.subOptions.find(s => s.id === state.subJalurId) || null;
          }

          function getSelectedJurusanObj() {
            return JURUSAN_OPTIONS.find(j => j.id === state.jurusanId) || null;
          }

          function getSelectedGelombangObj() {
            return GELOMBANG_OPTIONS.find(g => g.id === state.gelombangId) || null;
          }

          function getSelectedVoucherObj() {
            return VOUCHER_OPTIONS.find(v => v.id === state.voucherId) || null;
          }

          function isStepComplete(stepNum) {
            if (stepNum === 1) {
              const jalur = getSelectedJalurObj();
              if (!jalur) return false;
              if (jalur.hasSub && !getSelectedSubJalurObj()) return false;
              return true;
            }
            if (stepNum === 2) return Boolean(getSelectedJurusanObj());
            if (stepNum === 3) return Boolean(getSelectedGelombangObj());
            if (stepNum === 4) return Boolean(getSelectedVoucherObj());
            return false;
          }

          function computeDiscountsAndFees() {
            const jalur = getSelectedJalurObj();
            const subJalur = getSelectedSubJalurObj();
            const jurusan = getSelectedJurusanObj();
            const gelombang = getSelectedGelombangObj();
            const voucher = getSelectedVoucherObj();

            const priceGroup = jurusan ? PRICE_GROUPS[jurusan.group] : null;

            // 1. Diskon Jurusan (Selalu Dapat, kecuali Warga MOU lewat periode)
            let jurusanSppPct = 0;
            if (jurusan) {
              jurusanSppPct = Number(jurusan.sppBonus) || 0;
            }

            const isMouLate = Boolean(subJalur && subJalur.id === 'khusus_mou' && state.mouLatePeriod);
            if (isMouLate) {
              jurusanSppPct = 0; // "Lewat periode = 10% (tanpa diskon jurusan)"
            }

            // 2. Diskon Utama SPP & DPP
            let jalurSppPct = 0;
            let jalurSppDuration = '3 Tahun';
            let dppPct = 0;
            let dppDuration = '3 Tahun';
            let isInklusi = false;
            let isWaitingWaveForUmum = false;

            if (jalur) {
              if (jalur.id === 'umum') {
                if (gelombang) {
                  jalurSppPct = Number(gelombang.umumSppDiscount) || 0;
                  jalurSppDuration = gelombang.umumSppDuration || '3 Tahun';
                  dppPct = Number(gelombang.umumDppDiscount) || 0;
                  dppDuration = gelombang.umumDppDuration || '3 Tahun';
                } else {
                  isWaitingWaveForUmum = true;
                }
              } else if (jalur.id === 'prestasi') {
                if (subJalur) {
                  jalurSppPct = Number(subJalur.sppDiscount) || 0;
                  jalurSppDuration = subJalur.sppDuration || '3 Tahun';
                  dppPct = 50;
                  dppDuration = '3 Tahun';
                }
              } else if (jalur.id === 'afirmasi') {
                jalurSppPct = 15;
                jalurSppDuration = '3 Tahun ';
                dppPct = 50;
                dppDuration = '3 Tahun';
              } else if (jalur.id === 'kip') {
                jalurSppPct = 15;
                jalurSppDuration = '3 Tahun';
                dppPct = 50;
                dppDuration = '3 Tahun';
              } else if (jalur.id === 'minat_bakat') {
                jalurSppPct = 15;
                jalurSppDuration = '3 Tahun ';
                dppPct = 50;
                dppDuration = '1 Tahun';
              } else if (jalur.id === 'khusus') {
                if (subJalur) {
                  isInklusi = Boolean(subJalur.isInklusi);
                  if (isInklusi) {
                    jalurSppPct = 0;
                    jalurSppDuration = 'Kebijakan Kepala Sekolah';
                    dppPct = 0;
                    dppDuration = 'Kebijakan Kepala Sekolah';
                  } else if (subJalur.id === 'khusus_mou') {
                    if (state.mouLatePeriod) {
                      jalurSppPct = 10;
                      jalurSppDuration = '3 Tahun';
                      dppPct = 50;
                      dppDuration = '3 Tahun';
                    } else {
                      jalurSppPct = 30;
                      jalurSppDuration = '3 Tahun';
                      dppPct = 50;
                      dppDuration = '3 Tahun';
                    }
                  } else if (subJalur.id === 'khusus_kembar' || subJalur.id === 'khusus_saudara') {
                    jalurSppPct = 20;
                    jalurSppDuration = '3 Tahun';
                    dppPct = 50;
                    dppDuration = '3 Tahun';
                  } else if (subJalur.id === 'khusus_anak_guru') {
                    jalurSppPct = 70;
                    jalurSppDuration = '3 Tahun';
                    dppPct = 50;
                    dppDuration = '3 Tahun';
                  } else if (subJalur.id === 'khusus_kerabat_guru') {
                    jalurSppPct = 15;
                    jalurSppDuration = '3 Tahun ';
                    dppPct = 50;
                    dppDuration = '3 Tahun';
                  } else if (subJalur.id === 'khusus_panti') {
                    jalurSppPct = 25;
                    jalurSppDuration = '3 Tahun';
                    dppPct = 50;
                    dppDuration = '3 Tahun';
                  } else if (subJalur.id === 'khusus_ojol') {
                    jalurSppPct = 20;
                    jalurSppDuration = '3 Tahun';
                    dppPct = 50;
                    dppDuration = '1 Tahun';
                  } else if (subJalur.id === 'khusus_minat_bakat') {
                    jalurSppPct = 15;
                    jalurSppDuration = '3 Tahun ';
                    dppPct = 50;
                    dppDuration = '1 Tahun';
                  } else {
                    jalurSppPct = Number(subJalur.sppDiscount) || 0;
                    jalurSppDuration = subJalur.sppDuration || '3 Tahun';
                    dppPct = Number(subJalur.dppDiscount) || 50;
                    dppDuration = subJalur.dppDuration || '3 Tahun';
                  }
                }
              }
            }

            const safeJalurSppPct = Number(jalurSppPct) || 0;
            const safeJurusanSppPct = Number(jurusanSppPct) || 0;
            const totalSppPct = safeJalurSppPct + safeJurusanSppPct;
            const safeDppPct = Number(dppPct) || 0;

            // 3. Uang Pendaftaran Discount:
            // Gelombang Khusus ATAU Voucher Rekomendasi ATAU Voucher HUT: 100% (Bayar 0)
            // Gelombang 1 atau 2 tanpa voucher: 50% (Bayar 75.000)
            let regPct = 0;
            let regSource = '';
            const hasFreeVoucher = Boolean(voucher && (voucher.id === 'voucher_alumni' || voucher.id === 'voucher_hut' || voucher.overrideRegDiscount === 100));

            if (hasFreeVoucher) {
              regPct = 100;
              regSource = voucher.shortLabel;
            } else if (gelombang) {
              if (gelombang.id === 'gelombang_khusus') {
                regPct = 100;
                regSource = 'Gelombang Khusus';
              } else {
                regPct = 50;
                regSource = gelombang.title;
              }
            } else {
              regPct = 0;
              regSource = 'Standar';
            }

            // 4. Dokumen Persyaratan
            const docs = [];
            if (jalur) {
              BASE_DOCUMENTS.forEach(d => docs.push(d));
              if (jalur.id === 'prestasi') {
                docs.push('Fotokopi Sertifikat / Piagam Juara dilegalisir');
              } else if (jalur.id === 'afirmasi') {
                docs.push('Surat Keterangan Kurang Mampu dari Kepala Desa setempat');
                docs.push('Surat permohonan untuk keringanan biaya');
              } else if (jalur.id === 'kip') {
                docs.push('Fotokopi Kartu Indonesia Pintar (KIP)');
                docs.push('Surat permohonan untuk keringanan biaya');
              } else if (jalur.id === 'minat_bakat') {
                docs.push('Portofolio atau video yang menunjukkan bakat calon siswa');
                docs.push('Surat permohonan untuk keringanan biaya');
              } else if (jalur.id === 'khusus' && subJalur) {
                if (subJalur.id === 'khusus_mou') {
                  docs.push('Surat Keterangan Warga Desa / Form Rekomendasi Desa atau Form Rekomendasi SMP/MTs MOU');
                  docs.push('Surat permohonan untuk keringanan biaya');
                } else if (subJalur.id === 'khusus_kembar' || subJalur.id === 'khusus_saudara' || subJalur.id === 'khusus_anak_guru' || subJalur.id === 'khusus_kerabat_guru') {
                  docs.push('Surat pernyataan warga sekolah bermaterai 10.000');
                  docs.push('Surat permohonan untuk keringanan biaya');
                } else if (subJalur.id === 'khusus_panti') {
                  docs.push('Surat permohonan untuk keringanan biaya');
                } else if (subJalur.id === 'khusus_ojol') {
                  docs.push('Fotokopi Bukti Keanggotaan Ojek Online');
                  docs.push('Surat permohonan untuk keringanan biaya');
                } else if (subJalur.id === 'khusus_minat_bakat') {
                  docs.push('Portofolio atau video yang menunjukkan bakat calon siswa');
                  docs.push('Surat permohonan untuk keringanan biaya');
                }
              }
            }

            if (gelombang && gelombang.id === 'gelombang_khusus' && !hasFreeVoucher) {
              docs.push('Upload foto pendaftaran di medsos, ulasan Google Review & follow medsos sekolah');
            }
            if (hasFreeVoucher && voucher && voucher.extraDoc) {
              docs.push(voucher.extraDoc);
            }

            // 5. Perhitungan Komponen & Nominal Pasti (Integer Math, Anti NaN)
            let amounts = null;
            if (priceGroup) {
              const pendaftaranDasar = 150000;
              const dppDasar = Number(priceGroup.dpp) || 650000;
              const sppDasar = Number(priceGroup.spp) || 650000;
              const biayaTetap = 2550000; // MOS (450k) + Asuransi (100k) + Seragam (1.975k) + OSIS (25k) = 2.550.000

              const pendaftaranBayar = Math.round(pendaftaranDasar - (pendaftaranDasar * (regPct / 100)));
              const dppBayar = Math.round(dppDasar - (dppDasar * (safeDppPct / 100)));
              const sppBayar = Math.round(sppDasar - (sppDasar * (totalSppPct / 100)));

              // Rumus Total Daftar Ulang (1 Bulan Pertama):
              // Total_Bayar = 2550000 + Pendaftaran_Bayar + DPP_Bayar + SPP_Bayar
              const totalBayar = biayaTetap + pendaftaranBayar + dppBayar + sppBayar;

              const sppBulananBerikutnya = sppBayar + (Number(priceGroup.osis) || 25000);
              const totalNormal = Number(priceGroup.totalAwalNormal) || 4000000;
              const daftarUlangSavings = totalNormal - totalBayar;

              amounts = {
                pendaftaranDasar,
                pendaftaranBayar,
                dppDasar,
                dppBayar,
                sppDasar,
                sppBayar,
                biayaTetap,
                mosDkk: priceGroup.mosDkk,
                asuransi: priceGroup.asuransi,
                seragam: priceGroup.seragam,
                osis: priceGroup.osis,
                totalBayar,
                sppBulananBerikutnya,
                totalNormal,
                daftarUlangSavings
              };
            }

            return {
              jalur,
              subJalur,
              jurusan,
              gelombang,
              voucher,
              priceGroup,
              jalurSppPct: safeJalurSppPct,
              jalurSppDuration,
              jurusanSppPct: safeJurusanSppPct,
              totalSppPct,
              dppPct: safeDppPct,
              dppDuration,
              isInklusi,
              isWaitingWaveForUmum,
              isMouLate,
              regPct,
              regSource,
              docs,
              amounts
            };
          }

          function hideValidationAlert() {
            if (stepAlert) stepAlert.classList.add('hidden');
          }

          function showValidationAlert(message) {
            // Dinonaktifkan: biarin stuck tanpa memunculkan badge alert jika opsi belum dipilih
          }

          function renderGridCard({ isSelected, title, onClick }) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.setAttribute('role', 'radio');
            btn.setAttribute('aria-checked', String(isSelected));
            btn.className = isSelected
              ? 'relative h-full min-h-[50px] sm:min-h-[54px] w-full text-left rounded-xl sm:rounded-2xl px-3.5 py-3 border-2 border-blue-600 bg-blue-50/80 text-slate-900 shadow-none transition cursor-pointer flex items-center group'
              : 'relative h-full min-h-[50px] sm:min-h-[54px] w-full text-left rounded-xl sm:rounded-2xl px-3.5 py-3 border border-slate-200 hover:border-blue-400 hover:bg-slate-50/60 text-slate-900 transition cursor-pointer flex items-center group';

            btn.innerHTML = `
              <span class="text-[13px] sm:text-sm font-bold leading-snug text-slate-800 group-hover:text-blue-600 transition-colors pr-5">
                ${escapeHtml(title)}
              </span>
              ${isSelected ? `
                <span class="absolute top-2 right-2 sm:top-2.5 sm:right-2.5 w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center" aria-hidden="true">
                  <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </span>
              ` : ''}
            `;

            btn.addEventListener('click', onClick);
            return btn;
          }

          function renderOptionCard({ isSelected, title, badge, desc, onClick }) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.setAttribute('role', 'radio');
            btn.setAttribute('aria-checked', String(isSelected));
            btn.className = isSelected
              ? 'w-full text-left rounded-xl px-3.5 py-2.5 border-2 border-blue-600 bg-blue-50/75 text-slate-900 transition cursor-pointer'
              : 'w-full text-left rounded-xl px-3.5 py-2.5 border border-slate-200/90 bg-white hover:border-blue-400 text-slate-900 transition cursor-pointer group';

            btn.innerHTML = `
              <div class="flex items-center justify-between gap-2.5">
                <div class="min-w-0 flex-1">
                  <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-xs sm:text-sm font-bold leading-snug text-slate-900">
                      ${escapeHtml(title)}
                    </span>
                    ${badge ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ${isSelected ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'}">${escapeHtml(badge)}</span>` : ''}
                  </div>
                  ${desc ? `<p class="mt-0.5 text-[11px] leading-snug text-slate-500">${escapeHtml(desc)}</p>` : ''}
                </div>
                <span class="w-[18px] h-[18px] rounded-full flex items-center justify-center shrink-0 transition ${isSelected ? 'bg-blue-600 text-white' : 'border border-slate-300 group-hover:border-blue-400'}">
                  ${isSelected ? '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>' : ''}
                </span>
              </div>
            `;

            btn.addEventListener('click', onClick);
            return btn;
          }

          function renderSubOptions(jalur) {
            if (!jalur || !jalur.hasSub) {
              subOptionsWrapper.classList.add('hidden');
              subOptionsContainer.innerHTML = '';
              return;
            }

            subOptionsWrapper.classList.remove('hidden');
            subOptionsTitle.textContent = jalur.subTitle;
            subOptionsContainer.innerHTML = '';

            if (jalur.id === 'prestasi') {
              subOptionsContainer.className = 'grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-2.5';
              jalur.subOptions.forEach(sub => {
                const isSubSelected = state.subJalurId === sub.id;
                const card = renderGridCard({
                  isSelected: isSubSelected,
                  title: sub.cardTitle || sub.title,
                  onClick: () => {
                    state.subJalurId = sub.id;
                    hideValidationAlert();
                    renderSubOptions(jalur);
                    updateLivePreview();
                  }
                });
                card.title = sub.title;
                subOptionsContainer.appendChild(card);
              });
              return;
            }

            subOptionsContainer.className = 'grid grid-cols-1 gap-1.5 max-h-[230px] overflow-y-auto spmb-custom-scroll pr-1';
            jalur.subOptions.forEach(sub => {
              const isSubSelected = state.subJalurId === sub.id;
              const wrapper = document.createElement('div');
              wrapper.className = 'space-y-1';

              const activeBadge = (sub.id === 'khusus_mou' && state.mouLatePeriod)
                ? 'SPP 10% (Lewat Periode) · DPP 50% (3 Thn)'
                : sub.badge;

              const btn = document.createElement('button');
              btn.type = 'button';
              btn.className = isSubSelected
                ? 'relative w-full text-left rounded-xl px-3 py-2 border-2 border-blue-600 bg-blue-50/75 text-slate-900 transition cursor-pointer'
                : 'relative w-full text-left rounded-xl px-3 py-2 border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-blue-400 text-slate-800 transition cursor-pointer';

              btn.innerHTML = `
                <div class="min-w-0 flex-1 pr-5">
                  <span class="block text-xs font-bold leading-snug text-slate-900">
                    ${escapeHtml(sub.title)}
                  </span>
                  <span class="mt-0.5 inline-block px-1.5 py-0.5 rounded text-[10px] font-bold ${isSubSelected ? 'bg-blue-600 text-white' : 'bg-slate-200/80 text-slate-700'}">
                    ${escapeHtml(activeBadge)}
                  </span>
                </div>
                ${isSubSelected ? `
                  <span class="absolute top-2 right-2 w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center" aria-hidden="true">
                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                  </span>
                ` : ''}
              `;

              btn.addEventListener('click', () => {
                state.subJalurId = sub.id;
                if (sub.id !== 'khusus_mou') {
                  state.mouLatePeriod = false;
                }
                hideValidationAlert();
                renderSubOptions(jalur);
                updateLivePreview();
              });

              wrapper.appendChild(btn);

              if (isSubSelected && sub.hasMouTimingToggle) {
                const timingBox = document.createElement('div');
                timingBox.className = 'ml-2 pl-2.5 border-l-2 border-blue-300 pt-0.5 flex flex-wrap items-center gap-1.5';
                timingBox.innerHTML = `
                  <span class="text-[10.5px] font-semibold text-slate-600">Periode MOU:</span>
                  <button type="button" data-mou-late="false" class="px-2 py-0.5 rounded-md text-[10.5px] font-bold transition cursor-pointer ${!state.mouLatePeriod ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 border border-slate-300'}">
                    Tepat Waktu (SPP 30%)
                  </button>
                  <button type="button" data-mou-late="true" class="px-2 py-0.5 rounded-md text-[10.5px] font-bold transition cursor-pointer ${state.mouLatePeriod ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 border border-slate-300'}">
                    Lewat Periode (SPP 10%)
                  </button>
                `;
                timingBox.querySelectorAll('button[data-mou-late]').forEach(tBtn => {
                  tBtn.addEventListener('click', () => {
                    state.mouLatePeriod = tBtn.dataset.mouLate === 'true';
                    renderSubOptions(jalur);
                    updateLivePreview();
                  });
                });
                wrapper.appendChild(timingBox);
              }

              subOptionsContainer.appendChild(wrapper);
            });
          }

          function renderStep() {
            const meta = STEP_META[state.step];
            stepBadge.textContent = meta.badge;
            questionTitle.textContent = meta.title;
            questionDesc.textContent = meta.desc;

            questionPanel.classList.remove('spmb-step-animate');
            void questionPanel.offsetWidth;
            questionPanel.classList.add('spmb-step-animate');

            hideValidationAlert();

            segBtns.forEach((btn, idx) => {
              const stepNum = idx + 1;
              const bar = btn.querySelector('.spmb-seg-bar');
              const label = btn.querySelector('.spmb-seg-label');
              const completed = isStepComplete(stepNum);
              const isCurrent = stepNum === state.step;

              if (isCurrent) {
                bar.className = 'spmb-seg-bar h-1.5 w-full rounded-full bg-blue-600 transition-all duration-300';
                label.className = 'spmb-seg-label mt-1.5 block text-xs sm:text-sm font-extrabold text-blue-600 truncate';
              } else if (completed) {
                bar.className = 'spmb-seg-bar h-1.5 w-full rounded-full bg-sky-500 transition-all duration-300';
                label.className = 'spmb-seg-label mt-1.5 block text-xs sm:text-sm font-bold text-slate-700 truncate';
              } else {
                bar.className = 'spmb-seg-bar h-1.5 w-full rounded-full bg-slate-200 transition-all duration-300';
                label.className = 'spmb-seg-label mt-1.5 block text-xs sm:text-sm font-semibold text-slate-400 truncate';
              }
            });

            const hasAnyChoice = Boolean(state.jalurId || state.jurusanId || state.gelombangId || state.voucherId);
            btnQuickReset.classList.toggle('hidden', !hasAnyChoice);

            optionsContainer.innerHTML = '';
            subOptionsWrapper.classList.add('hidden');

            if (state.step === 1) {
              optionsContainer.className = 'mt-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2.5';
              JALUR_OPTIONS.forEach(jalur => {
                const isSelected = state.jalurId === jalur.id;
                const card = renderGridCard({
                  isSelected,
                  title: jalur.cardTitle || jalur.title,
                  onClick: () => {
                    if (state.jalurId !== jalur.id) {
                      state.jalurId = jalur.id;
                      state.subJalurId = null;
                      state.mouLatePeriod = false;
                    }
                    hideValidationAlert();
                    renderStep();
                    updateLivePreview();
                    if (jalur.hasSub) {
                      setTimeout(() => {
                        subOptionsWrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                      }, 50);
                    }
                  }
                });
                optionsContainer.appendChild(card);
              });

              const activeJalur = getSelectedJalurObj();
              if (activeJalur && activeJalur.hasSub) {
                renderSubOptions(activeJalur);
              }
            } else if (state.step === 2) {
              optionsContainer.className = 'mt-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2.5';
              JURUSAN_OPTIONS.forEach(jurusan => {
                const isSelected = state.jurusanId === jurusan.id;
                const card = renderGridCard({
                  isSelected,
                  title: jurusan.cardTitle || jurusan.id,
                  onClick: () => {
                    state.jurusanId = jurusan.id;
                    hideValidationAlert();
                    renderStep();
                    updateLivePreview();
                  }
                });
                optionsContainer.appendChild(card);
              });
            } else if (state.step === 3) {
              optionsContainer.className = 'mt-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2.5';
              GELOMBANG_OPTIONS.forEach(gel => {
                const isSelected = state.gelombangId === gel.id;
                const card = renderGridCard({
                  isSelected,
                  title: gel.cardTitle || gel.title,
                  onClick: () => {
                    state.gelombangId = gel.id;
                    hideValidationAlert();
                    renderStep();
                    updateLivePreview();
                  }
                });
                optionsContainer.appendChild(card);
              });
            } else if (state.step === 4) {
              optionsContainer.className = 'mt-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2.5';
              VOUCHER_OPTIONS.forEach(v => {
                const isSelected = state.voucherId === v.id;
                const card = renderGridCard({
                  isSelected,
                  title: v.cardTitle || v.title,
                  onClick: () => {
                    state.voucherId = v.id;
                    hideValidationAlert();
                    renderStep();
                    updateLivePreview();
                  }
                });
                optionsContainer.appendChild(card);
              });
            }

            btnPrev.disabled = state.step === 1;
            btnPrev.classList.toggle('hidden', state.step === 1);
            btnPrev.classList.toggle('inline-flex', state.step > 1);
            btnNextLabel.textContent = state.step === 4 ? 'Lihat Estimasi Biaya' : 'Lanjut';
            updateSummaryLines();
          }

          function updateSummaryLines() {
            [1, 2, 3, 4].forEach(s => {
              const lineEl = document.getElementById(`spmb-sum-line-${s}`);
              if (!lineEl) return;
              const isCurrent = state.step === s;
              const isFilled = isStepComplete(s);
              if (isCurrent) {
                lineEl.className = 'h-0.5 w-full rounded-full bg-sky-400 transition-colors duration-300';
              } else if (isFilled) {
                lineEl.className = 'h-0.5 w-full rounded-full bg-sky-400/50 transition-colors duration-300';
              } else {
                lineEl.className = 'h-0.5 w-full rounded-full bg-white/20 transition-colors duration-300';
              }
            });
          }

          function updateSummarySlot(el, valueText) {
            if (!el) return;
            if (valueText) {
              el.textContent = valueText;
              el.title = valueText;
              el.className = 'block mt-1 text-xs sm:text-[13px] font-bold text-white tracking-tight truncate';
            } else {
              el.textContent = 'Belum';
              el.title = 'Belum dipilih';
              el.className = 'block mt-1 text-xs sm:text-[13px] font-medium text-white/40 tracking-tight truncate';
            }
          }

          function updateLivePreview() {
            const calc = computeDiscountsAndFees();

            if (liveJurusan) {
              if (calc.jurusan && calc.priceGroup) {
                liveJurusan.textContent = calc.jurusan.title;
              } else {
                liveJurusan.textContent = 'Jurusan Belum Dipilih';
              }
            }

            if (liveKelompok) {
              if (calc.jurusan && calc.priceGroup) {
                liveKelompok.textContent = `${calc.priceGroup.label} · Harga Normal ${formatRupiah(calc.priceGroup.totalAwalNormal)}`;
              } else {
                liveKelompok.textContent = 'Pilih jurusan di Langkah 02';
              }
            }

            if (liveJalurPill) {
              if (calc.jalur) {
                liveJalurPill.textContent = calc.subJalur ? calc.subJalur.shortLabel : calc.jalur.shortLabel;
                liveJalurPill.className = 'shrink-0 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-sky-400/20 text-sky-300 border border-sky-400/30';
              } else {
                liveJalurPill.textContent = 'SPMB 27/28';
                liveJalurPill.className = 'shrink-0 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/10 text-slate-200 border border-white/10';
              }
            }

            // SPP Badge
            if (calc.isInklusi) {
              liveSppBadge.textContent = calc.jurusan ? `${calc.jurusanSppPct}%+` : 'Kepsek';
              liveSppNote.textContent = calc.jurusan ? `Jurusan ${calc.jurusanSppPct}% + Kepsek` : 'Kebijakan Kepsek';
            } else if (calc.isWaitingWaveForUmum) {
              liveSppBadge.textContent = calc.jurusan ? `${calc.jurusanSppPct}%+` : 'Gelombang';
              liveSppNote.textContent = 'Tentukan di Langkah 03';
            } else if (calc.jalur || calc.jurusan) {
              liveSppBadge.textContent = `${calc.totalSppPct}%`;
              const parts = [];
              if (calc.jalur && (!calc.jalur.hasSub || calc.subJalur)) {
                parts.push(`${calc.jalurSppPct}%`);
              }
              if (calc.jurusan) {
                parts.push(`+${calc.jurusanSppPct}% jur`);
              }
              liveSppNote.textContent = parts.length > 0 ? parts.join(' ') : 'Pilih sub-jalur';
            } else {
              liveSppBadge.textContent = '0%';
              liveSppNote.textContent = 'Belum dipilih';
            }

            // DPP Badge
            if (calc.isInklusi) {
              liveDppBadge.textContent = 'Kepsek';
              liveDppNote.textContent = 'Kebijakan Kepsek';
            } else if (calc.isWaitingWaveForUmum) {
              liveDppBadge.textContent = '25–50%';
              liveDppNote.textContent = 'Ikut gelombang';
            } else if (calc.jalur && (!calc.jalur.hasSub || calc.subJalur)) {
              liveDppBadge.textContent = `${calc.dppPct}%`;
              liveDppNote.textContent = calc.dppDuration || 'Sesuai jalur';
            } else {
              liveDppBadge.textContent = '0%';
              liveDppNote.textContent = calc.jalur && calc.jalur.hasSub ? 'Pilih sub-jalur' : 'Belum dipilih';
            }

            // Pendaftaran Badge
            if (calc.regPct !== null) {
              const feeAfter = 150000 - (150000 * (calc.regPct / 100));
              liveRegBadge.textContent = calc.regPct === 100 ? 'Gratis' : formatRupiah(feeAfter);
              liveRegNote.textContent = `Diskon ${calc.regPct}%`;
            } else {
              liveRegBadge.textContent = '-';
              liveRegNote.textContent = 'Pilih gelombang';
            }

            // Syarat Berkas
            if (liveBerkasCount) {
              if (calc.docs.length > 0) {
                liveBerkasCount.textContent = `${calc.docs.length} Berkas`;
              } else {
                liveBerkasCount.textContent = 'Pilih jalur';
              }
            }
            if (calc.docs.length > 0) {
              liveBerkasList.innerHTML = calc.docs.map(doc => `
                <li class="flex items-start gap-1.5">
                  <svg class="w-3.5 h-3.5 text-sky-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  <span>${escapeHtml(doc)}</span>
                </li>
              `).join('');
            } else {
              liveBerkasList.innerHTML = `<li class="text-slate-400 italic">Pilih jalur untuk melihat berkas.</li>`;
            }

            let jalurText = null;
            if (calc.jalur) {
              if (calc.jalur.hasSub) {
                if (calc.subJalur) {
                  if (calc.subJalur.id === 'prestasi_sekolah_kab') jalurText = 'Pres. Kab';
                  else if (calc.subJalur.id === 'prestasi_prov_nas') jalurText = 'Pres. Prov';
                  else if (calc.subJalur.id === 'prestasi_internasional') jalurText = 'Pres. Inter';
                  else if (calc.subJalur.id === 'khusus_mou') jalurText = state.mouLatePeriod ? 'MOU Lewat' : 'MOU Tepat';
                  else if (calc.subJalur.id === 'khusus_kembar') jalurText = 'Kembar';
                  else if (calc.subJalur.id === 'khusus_saudara') jalurText = 'Saudara';
                  else if (calc.subJalur.id === 'khusus_anak_guru') jalurText = 'Anak Guru';
                  else if (calc.subJalur.id === 'khusus_kerabat_guru') jalurText = 'Kerabat';
                  else if (calc.subJalur.id === 'khusus_panti') jalurText = 'Panti';
                  else if (calc.subJalur.id === 'khusus_ojol') jalurText = 'Ojol';
                  else if (calc.subJalur.id === 'khusus_minat_bakat') jalurText = 'Bakat';
                  else if (calc.subJalur.id === 'khusus_inklusi') jalurText = 'Inklusi';
                  else jalurText = calc.subJalur.shortLabel;
                } else {
                  jalurText = calc.jalur.id === 'prestasi' ? 'Prestasi' : 'Khusus';
                }
              } else {
                if (calc.jalur.id === 'umum') jalurText = 'Umum';
                else if (calc.jalur.id === 'afirmasi') jalurText = 'Afirmasi';
                else if (calc.jalur.id === 'kip') jalurText = 'KIP';
                else if (calc.jalur.id === 'minat_bakat') jalurText = 'Bakat';
                else jalurText = calc.jalur.shortLabel;
              }
            }

            let jurusanText = null;
            if (calc.jurusan) {
              if (calc.jurusan.id === 'TKC') {
                jurusanText = 'TKC +5%';
              } else {
                jurusanText = `${calc.jurusan.shortLabel} +${calc.jurusan.sppBonus}%`;
              }
            }

            let gelombangText = null;
            if (calc.gelombang) {
              if (calc.gelombang.id === 'gelombang_khusus') gelombangText = 'Khusus';
              else if (calc.gelombang.id === 'gelombang_1') gelombangText = 'Gel. 1';
              else if (calc.gelombang.id === 'gelombang_2') gelombangText = 'Gel. 2';
              else gelombangText = calc.gelombang.shortLabel;
            }

            let voucherText = null;
            if (calc.voucher) {
              if (calc.voucher.id === 'tanpa_voucher') voucherText = 'Tanpa Vcr';
              else if (calc.voucher.id === 'voucher_alumni') voucherText = 'Alumni';
              else if (calc.voucher.id === 'voucher_hut') voucherText = 'HUT TI';
              else voucherText = calc.voucher.shortLabel;
            }

            updateSummarySlot(sumJalur, jalurText);
            updateSummarySlot(sumJurusan, jurusanText);
            updateSummarySlot(sumGelombang, gelombangText);
            updateSummarySlot(sumVoucher, voucherText);
            updateSummaryLines();
          }

          function renderResultView() {
            const calc = computeDiscountsAndFees();
            if (!calc.amounts || !calc.priceGroup) return;

            const pg = calc.priceGroup;
            const amt = calc.amounts;

            const jalurLabel = calc.subJalur
              ? (calc.subJalur.id === 'khusus_mou'
                  ? `${calc.subJalur.shortLabel} · ${state.mouLatePeriod ? 'Lewat Periode' : 'Tepat Waktu'}`
                  : calc.subJalur.shortLabel)
              : calc.jalur.shortLabel;

            const jurusanPillLabel = calc.jurusan.shortLabel;

            const pillItems = [
              { label: jalurLabel, title: 'Klik untuk mengubah jalur' },
              { label: jurusanPillLabel, title: 'Klik untuk mengubah jurusan' },
              { label: calc.gelombang.shortLabel, title: 'Klik untuk mengubah gelombang' },
              { label: calc.voucher.shortLabel, title: 'Klik untuk mengubah voucher' }
            ];

            resPills.innerHTML = pillItems.map(p => `
              <button
                type="button"
                onclick="closeResultView(false)"
                title="${escapeHtml(p.title)}"
                class="inline-flex items-center px-2.5 py-1 rounded-lg border border-white/20 hover:border-sky-400/60 active:scale-95 text-[11px] font-medium text-slate-300 hover:text-white transition cursor-pointer bg-transparent"
              >
                ${escapeHtml(p.label)}
              </button>
            `).join('');

            resMainTotal.textContent = formatRupiah(amt.totalBayar);
            if (resSavingBadge) {
              if (calc.isInklusi) {
                resSavingBadge.innerHTML = `
                  <span class="text-[11px] sm:text-xs font-bold text-sky-400 leading-tight block">Diskon Jurusan ${calc.jurusanSppPct}%</span>
                  <span class="text-[9.5px] sm:text-[10px] text-slate-400 leading-tight block">+ Kebijakan Kepsek</span>
                `;
              } else if (amt.daftarUlangSavings > 0) {
                resSavingBadge.innerHTML = `
                  <span class="text-[11px] sm:text-xs font-bold text-sky-400 leading-tight block">Hemat ${formatRupiah(amt.daftarUlangSavings)}</span>
                  <span class="text-[9.5px] sm:text-[10px] text-slate-400 leading-tight block">dari ${formatRupiah(amt.totalNormal)}</span>
                `;
              } else {
                resSavingBadge.innerHTML = '';
              }
            }

            const specialNotes = [];
            if (calc.subJalur && calc.subJalur.specialNote) {
              specialNotes.push(calc.subJalur.specialNote);
            } else if (calc.jalur && calc.jalur.specialNote) {
              specialNotes.push(calc.jalur.specialNote);
            }
            if (calc.gelombang.id === 'gelombang_khusus' && calc.voucher.overrideRegDiscount === null) {
              specialNotes.push('Gratis Pendaftaran Gelombang Khusus wajib upload foto pendaftaran di medsos pribadi, ulasan Google Review, & follow medsos sekolah.');
            }

            if (resSpecialNote) {
              if (specialNotes.length > 0) {
                resSpecialNote.classList.remove('hidden');
                resSpecialNote.innerHTML = specialNotes.map(n => `<p class="mb-1 last:mb-0">${escapeHtml(n)}</p>`).join('');
              } else {
                resSpecialNote.classList.add('hidden');
                resSpecialNote.innerHTML = '';
              }
            }

            // (1) Pendaftaran Awal (jika ada) & (2) SPP + OSIS Berikutnya
            if (resRegFee) resRegFee.textContent = amt.pendaftaranBayar === 0 ? 'Rp 0' : formatRupiah(amt.pendaftaranBayar);
            if (resRegTag) resRegTag.textContent = amt.pendaftaranBayar === 0 ? 'Gratis 100%' : `Diskon ${calc.regPct}%`;
            if (resRegDesc) resRegDesc.textContent = 'Saat mendaftar';

            resMonthlyFee.textContent = formatRupiah(amt.sppBulananBerikutnya);
            if (resMonthlyDesc) resMonthlyDesc.textContent = `SPP ${formatRupiah(amt.sppBayar)} + OSIS ${formatRupiah(pg.osis)}`;

            // Rincian Komponen Biaya Daftar Ulang (Harga Asli vs Setelah Diskon)
            if (resJurusanGroup) {
              resJurusanGroup.textContent = `${calc.jurusan.shortLabel} · ${pg.label}`;
            }

            // Diskon DPP (Kolom Kiri & Kanan)
            let dppDiscountText = '';
            let dppDurationText = '';
            if (calc.isInklusi) {
              dppDiscountText = 'Diskon DPP';
              dppDurationText = 'Mengikuti Kebijakan Kepala Sekolah';
            } else if (calc.dppPct > 0) {
              dppDiscountText = `Diskon ${calc.dppPct}%`;
              dppDurationText = `Berlaku ${calc.dppDuration}`;
            } else {
              dppDiscountText = '';
              dppDurationText = 'Tarif Standar';
            }

            // Diskon SPP (Kolom Kiri & Kanan)
            let sppDiscountText = '';
            let sppDurationText = '';
            let sppDetailNote = '';
            if (calc.isInklusi) {
              sppDiscountText = `Diskon Jurusan ${calc.jurusanSppPct}%`;
              sppDurationText = 'Berlaku 3 Tahun + Kebijakan Kepsek';
            } else if (calc.jalurSppDuration === '1 Tahun') {
              const sppYears23 = pg.spp - (pg.spp * (calc.jurusanSppPct / 100));
              sppDiscountText = `Diskon ${calc.totalSppPct}%`;
              sppDurationText = 'Berlaku 1 Tahun';
              sppDetailNote = `Thn 1: ${formatRupiah(amt.sppBayar)} · Thn 2–3: ${formatRupiah(sppYears23)}`;
            } else if (calc.isMouLate) {
              sppDiscountText = 'Diskon 10%';
              sppDurationText = 'Berlaku 3 Tahun';
            } else {
              sppDiscountText = calc.totalSppPct > 0 ? `Diskon ${calc.totalSppPct}%` : '';
              sppDurationText = `Berlaku ${calc.jalurSppDuration}`;
            }

            const regDiscountText = amt.pendaftaranBayar === 0 ? 'Gratis 100%' : (calc.regPct > 0 ? `Diskon ${calc.regPct}%` : 'Tarif Standar');

            resBreakdownRows.innerHTML = `
              <div class="py-1.5 flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                  <span class="w-5 h-5 rounded-full border border-white/30 text-white flex items-center justify-center text-[10.5px] font-bold shrink-0 mt-0.5 tabular-nums">1</span>
                  <div class="min-w-0 flex-1">
                    <span class="text-[13px] sm:text-sm font-bold text-white block">Biaya Pendaftaran</span>
                    <div class="text-[11.5px] sm:text-xs leading-relaxed flex flex-wrap items-center gap-x-1">
                      <span class="${(amt.pendaftaranBayar === 0 || calc.regPct > 0) ? 'text-sky-400 font-semibold' : 'text-slate-400 font-normal'}">${regDiscountText}</span>
                    </div>
                  </div>
                </div>
                <div class="flex flex-col items-end shrink-0 tabular-nums">
                  ${calc.regPct > 0 ? `<span class="text-[11px] sm:text-xs text-slate-400 line-through leading-none mb-0.5">${formatRupiah(amt.pendaftaranDasar)}</span>` : `<span class="text-[11px] sm:text-xs leading-none mb-0.5 invisible select-none" aria-hidden="true">&nbsp;</span>`}
                  <span class="text-[13px] sm:text-sm font-bold text-white leading-tight">${formatRupiah(amt.pendaftaranBayar)}</span>
                </div>
              </div>

              <div class="py-1.5 flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                  <span class="w-5 h-5 rounded-full border border-white/30 text-white flex items-center justify-center text-[10.5px] font-bold shrink-0 mt-0.5 tabular-nums">2</span>
                  <div class="min-w-0 flex-1">
                    <span class="text-[13px] sm:text-sm font-bold text-white block">Paket Daftar Ulang</span>
                    <details class="group mt-1 cursor-pointer">
                      <summary class="text-xs text-sky-400 hover:text-sky-300 font-medium inline-flex items-center gap-1 select-none outline-none list-none transition cursor-pointer">
                        <span>Lihat rincian 6 komponen biaya</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-open:rotate-180 text-sky-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                      </summary>
                      <ul class="mt-2 pl-2.5 border-l-2 border-white/10 space-y-1.5 text-xs">
                        <li class="flex items-center justify-between gap-2 text-slate-400">
                          <span>• MOS, Kartu Pelajar, Raport &amp; Kepramukaan</span>
                          <span class="font-medium text-slate-300 tabular-nums shrink-0">${formatRupiah(pg.mosDkk)}</span>
                        </li>
                        <li class="flex items-center justify-between gap-2 text-slate-400">
                          <span>• Asuransi Siswa 3 Tahun Penuh</span>
                          <span class="font-medium text-slate-300 tabular-nums shrink-0">${formatRupiah(pg.asuransi)}</span>
                        </li>
                        <li class="flex items-center justify-between gap-2 text-slate-400">
                          <span>• Paket Seragam Sekolah Lengkap</span>
                          <span class="font-medium text-slate-300 tabular-nums shrink-0">${formatRupiah(pg.seragam)}</span>
                        </li>
                        <li class="flex items-center justify-between gap-2 text-slate-400">
                          <span>• Iuran OSIS Bulan 1</span>
                          <span class="font-medium text-slate-300 tabular-nums shrink-0">${formatRupiah(pg.osis)}</span>
                        </li>
                      </ul>
                    </details>
                  </div>
                </div>
                <div class="flex flex-col items-end shrink-0 tabular-nums">
                  <span class="text-[11px] sm:text-xs leading-none mb-0.5 invisible select-none" aria-hidden="true">&nbsp;</span>
                  <span class="text-[13px] sm:text-sm font-bold text-white leading-tight">${formatRupiah(amt.biayaTetap)}</span>
                </div>
              </div>

              <div class="py-1.5 flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                  <span class="w-5 h-5 rounded-full border border-white/30 text-white flex items-center justify-center text-[10.5px] font-bold shrink-0 mt-0.5 tabular-nums">3</span>
                  <div class="min-w-0 flex-1">
                    <span class="text-[13px] sm:text-sm font-bold text-white block">DPP Semester 1</span>
                    <div class="text-[11.5px] sm:text-xs leading-relaxed flex flex-wrap items-center gap-x-1">
                      ${dppDiscountText ? `<span class="text-sky-400 font-semibold">${dppDiscountText}</span><span class="text-slate-400 font-normal">• ${escapeHtml(dppDurationText)}</span>` : `<span class="text-slate-400 font-normal">${escapeHtml(dppDurationText)}</span>`}
                    </div>
                  </div>
                </div>
                <div class="flex flex-col items-end shrink-0 tabular-nums">
                  ${calc.dppPct > 0 ? `<span class="text-[11px] sm:text-xs text-slate-400 line-through leading-none mb-0.5">${formatRupiah(amt.dppDasar)}</span>` : `<span class="text-[11px] sm:text-xs leading-none mb-0.5 invisible select-none" aria-hidden="true">&nbsp;</span>`}
                  <span class="text-[13px] sm:text-sm font-bold text-white leading-tight">${formatRupiah(amt.dppBayar)}</span>
                </div>
              </div>

              <div class="py-1.5 flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                  <span class="w-5 h-5 rounded-full border border-white/30 text-white flex items-center justify-center text-[10.5px] font-bold shrink-0 mt-0.5 tabular-nums">4</span>
                  <div class="min-w-0 flex-1">
                    <span class="text-[13px] sm:text-sm font-bold text-white block">SPP Bulan 1</span>
                    <div class="text-[11.5px] sm:text-xs leading-relaxed flex flex-wrap items-center gap-x-1">
                      ${sppDiscountText ? `<span class="text-sky-400 font-semibold">${sppDiscountText}</span><span class="text-slate-400 font-normal">• ${escapeHtml(sppDurationText)}</span>` : `<span class="text-slate-400 font-normal">Tarif Standar</span>`}
                    </div>
                    ${sppDetailNote ? `<span class="text-[11px] sm:text-[11.5px] text-slate-400 block mt-0.5 leading-relaxed">${escapeHtml(sppDetailNote)}</span>` : ''}
                  </div>
                </div>
                <div class="flex flex-col items-end shrink-0 tabular-nums">
                  ${calc.totalSppPct > 0 ? `<span class="text-[11px] sm:text-xs text-slate-400 line-through leading-none mb-0.5">${formatRupiah(amt.sppDasar)}</span>` : `<span class="text-[11px] sm:text-xs leading-none mb-0.5 invisible select-none" aria-hidden="true">&nbsp;</span>`}
                  <span class="text-[13px] sm:text-sm font-bold text-white leading-tight">${formatRupiah(amt.sppBayar)}</span>
                </div>
              </div>
            `;

            if (resPeriodicRows) {
              let periodicDppDetailHtml = '';
              if (calc.isInklusi) {
                periodicDppDetailHtml = `<span class="text-sky-400 font-semibold">Diskon DPP</span><span class="text-slate-400 font-normal">• Mengikuti Kebijakan Kepala Sekolah</span>`;
              } else if (calc.dppPct > 0) {
                periodicDppDetailHtml = `<span class="text-sky-400 font-semibold">Diskon ${calc.dppPct}%</span><span class="text-slate-400 font-normal">• Berlaku ${escapeHtml(calc.dppDuration)}</span>`;
              } else {
                periodicDppDetailHtml = `<span class="text-slate-400 font-normal">Tarif Standar</span>`;
              }

              resPeriodicRows.innerHTML = `
                <div class="py-1.5 flex items-start justify-between gap-3">
                  <div class="flex items-start gap-2.5 min-w-0 flex-1">
                    <span class="w-5 h-5 rounded-full border border-white/30 text-white flex items-center justify-center text-[10.5px] font-bold shrink-0 mt-0.5 tabular-nums">1</span>
                    <div class="min-w-0 flex-1">
                      <span class="text-[13px] sm:text-sm font-bold text-white block">SPP / bulan</span>
                      <div class="text-[11.5px] sm:text-xs leading-relaxed flex flex-wrap items-center gap-x-1">
                        ${sppDiscountText ? `<span class="text-sky-400 font-semibold">${sppDiscountText}</span><span class="text-slate-400 font-normal">• ${escapeHtml(sppDurationText)}</span>` : `<span class="text-slate-400 font-normal">Tarif Standar</span>`}
                      </div>
                      ${sppDetailNote ? `<span class="text-[11px] sm:text-[11.5px] text-slate-400 block mt-0.5 leading-relaxed">${escapeHtml(sppDetailNote)}</span>` : ''}
                    </div>
                  </div>
                  <div class="flex flex-col items-end shrink-0 tabular-nums">
                    ${calc.totalSppPct > 0 ? `<span class="text-[11px] sm:text-xs text-slate-400 line-through leading-none mb-0.5">${formatRupiah(amt.sppDasar)}</span>` : `<span class="text-[11px] sm:text-xs leading-none mb-0.5 invisible select-none" aria-hidden="true">&nbsp;</span>`}
                    <div class="flex items-baseline gap-1">
                      <span class="text-[13px] sm:text-sm font-bold text-white leading-tight">${formatRupiah(amt.sppBayar)}</span>
                    </div>
                  </div>
                </div>

                <div class="py-1.5 flex items-start justify-between gap-3">
                  <div class="flex items-start gap-2.5 min-w-0 flex-1">
                    <span class="w-5 h-5 rounded-full border border-white/30 text-white flex items-center justify-center text-[10.5px] font-bold shrink-0 mt-0.5 tabular-nums">2</span>
                    <div class="min-w-0 flex-1">
                      <span class="text-[13px] sm:text-sm font-bold text-white block">OSIS / bulan</span>
                      <span class="text-[11.5px] sm:text-xs text-slate-400 block leading-relaxed">Berlaku 3 Tahun</span>
                    </div>
                  </div>
                  <div class="flex flex-col items-end shrink-0 tabular-nums">
                    <span class="text-[11px] sm:text-xs leading-none mb-0.5 invisible select-none" aria-hidden="true">&nbsp;</span>
                    <div class="flex items-baseline gap-1">
                      <span class="text-[13px] sm:text-sm font-bold text-white leading-tight">${formatRupiah(pg.osis)}</span>
                    </div>
                  </div>
                </div>

                <div class="py-1.5 flex items-start justify-between gap-3">
                  <div class="flex items-start gap-2.5 min-w-0 flex-1">
                    <span class="w-5 h-5 rounded-full border border-white/30 text-white flex items-center justify-center text-[10.5px] font-bold shrink-0 mt-0.5 tabular-nums">3</span>
                    <div class="min-w-0 flex-1">
                      <span class="text-[13px] sm:text-sm font-bold text-white block">DPP / semester</span>
                      <div class="text-[11.5px] sm:text-xs leading-relaxed flex flex-wrap items-center gap-x-1">
                        ${periodicDppDetailHtml}
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-col items-end shrink-0 tabular-nums">
                    ${calc.dppPct > 0 ? `<span class="text-[11px] sm:text-xs text-slate-400 line-through leading-none mb-0.5">${formatRupiah(amt.dppDasar)}</span>` : `<span class="text-[11px] sm:text-xs leading-none mb-0.5 invisible select-none" aria-hidden="true">&nbsp;</span>`}
                    <div class="flex items-baseline gap-1">
                      <span class="text-[13px] sm:text-sm font-bold text-white leading-tight">${formatRupiah(amt.dppBayar)}</span>
                    </div>
                  </div>
                </div>
              `;
            }

            if (resSemesterFee) {
              resSemesterFee.textContent = formatRupiah(amt.dppBayar);
            }
            if (resSemesterDesc) {
              resSemesterDesc.textContent = calc.isInklusi 
                ? 'Mengikuti Kebijakan Kepala Sekolah' 
                : (calc.dppPct > 0 ? `DPP Diskon ${calc.dppPct}% · ${calc.dppDuration}` : 'DPP Tarif Standar per semester');
            }

            if (resNormalNote) {
              resNormalNote.textContent = `Total Normal Sebelum Diskon: ${formatRupiah(amt.totalNormal)}`;
            }
            if (resTableTotal) {
              resTableTotal.textContent = formatRupiah(amt.totalBayar);
            }

            if (resDurationBox) {
              resDurationBox.innerHTML = '';
            }

            // Dokumen Persyaratan (di dalam Modal Berkas Persyaratan)
            const docsContainer = getDocsListEl();
            if (docsContainer && calc.docs && calc.docs.length > 0) {
              docsContainer.innerHTML = calc.docs.map(doc => `
                <li class="flex items-start gap-2.5 min-w-0">
                  <span class="w-4 h-4 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                  </span>
                  <span class="leading-snug text-slate-700 font-medium text-xs sm:text-[13px]">${escapeHtml(doc)}</span>
                </li>
              `).join('');
            }
          }

          window.spmbCalcGetDocs = function () {
            try {
              const calc = computeDiscountsAndFees();
              return (calc && calc.docs && calc.docs.length > 0) ? calc.docs : BASE_DOCUMENTS;
            } catch (e) {
              return BASE_DOCUMENTS;
            }
          };

          function openResultView() {
            renderResultView();
            quizView.classList.add('hidden');
            quizView.setAttribute('hidden', '');
            resultView.removeAttribute('hidden');
            resultView.classList.remove('hidden', 'spmb-result-animate');
            void resultView.offsetWidth;
            resultView.classList.add('spmb-result-animate');
            calcShell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }

          function closeResultView(resetAll = false) {
            if (resetAll) {
              state.step = 1;
              state.jalurId = null;
              state.subJalurId = null;
              state.mouLatePeriod = false;
              state.jurusanId = null;
              state.gelombangId = null;
              state.voucherId = null;
            }
            resultView.classList.add('hidden');
            resultView.setAttribute('hidden', '');
            quizView.removeAttribute('hidden');
            quizView.classList.remove('hidden');
            renderStep();
            updateLivePreview();
            calcShell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
          window.closeResultView = closeResultView;

          btnPrev.addEventListener('click', () => {
            if (state.step > 1) {
              state.step -= 1;
              renderStep();
            }
          });

          btnNext.addEventListener('click', () => {
            if (!isStepComplete(state.step)) {
              return; // biarin stuck tanpa memunculkan badge alert jika opsi belum dipilih
            }

            if (state.step < 4) {
              state.step += 1;
              renderStep();
            } else {
              openResultView();
            }
          });

          segBtns.forEach(btn => {
            btn.addEventListener('click', () => {
              const targetStep = Number(btn.dataset.stepNav);
              if (targetStep === state.step) return;
              if (targetStep < state.step) {
                state.step = targetStep;
                renderStep();
                return;
              }
              for (let s = 1; s < targetStep; s++) {
                if (!isStepComplete(s)) {
                  return; // biarin stuck pada langkah saat ini tanpa alert
                }
              }
              state.step = targetStep;
              renderStep();
            });
          });

          btnQuickReset.addEventListener('click', () => closeResultView(true));
          if (btnEdit) {
            btnEdit.addEventListener('click', () => closeResultView(false));
          }
          btnRestart.addEventListener('click', () => closeResultView(true));

          renderStep();
          updateLivePreview();
        })();
      </script>

<script>
        (function () {
          function escapeHtml(str) {
            return String(str)
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;');
          }

          window.openKetentuanBiayaModal = function () {
            const modal = document.getElementById('modal-ketentuan-biaya');
            const box = document.getElementById('modal-ketentuan-biaya-box');
            if (!modal || !box) return;

            modal.classList.remove('hidden', 'pointer-events-none');
            document.body.classList.add('overflow-hidden');
            requestAnimationFrame(() => {
              modal.classList.remove('opacity-0');
              box.classList.remove('scale-95');
              box.classList.add('scale-100');
            });
          };

          window.closeKetentuanBiayaModal = function () {
            const modal = document.getElementById('modal-ketentuan-biaya');
            const box = document.getElementById('modal-ketentuan-biaya-box');
            if (!modal || !box) return;

            modal.classList.add('opacity-0');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
              modal.classList.add('hidden', 'pointer-events-none');
              document.body.classList.remove('overflow-hidden');
            }, 200);
          };

          let docsModalOpen = false;
          let docsModalScrollY = 0;

          function preventBackgroundScroll(e) {
            if (!docsModalOpen) return;
            const box = document.getElementById('modal-berkas-persyaratan-box');
            if (box && box.contains(e.target)) {
              return;
            }
            e.preventDefault();
          }

          function preventKeyScroll(e) {
            if (!docsModalOpen) return;
            const scrollKeys = ['Space', 'PageUp', 'PageDown', 'End', 'Home', 'ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown'];
            if (scrollKeys.includes(e.code) || scrollKeys.includes(e.key)) {
              const box = document.getElementById('modal-berkas-persyaratan-box');
              if (box && box.contains(e.target)) {
                return;
              }
              e.preventDefault();
            }
          }

          function onWindowScrollLock() {
            if (docsModalOpen) {
              window.scrollTo({ top: docsModalScrollY, behavior: 'instant' });
            }
          }

          window.addEventListener('wheel', preventBackgroundScroll, { passive: false });
          window.addEventListener('touchmove', preventBackgroundScroll, { passive: false });
          window.addEventListener('keydown', preventKeyScroll, { passive: false });
          window.addEventListener('scroll', onWindowScrollLock, { passive: false });

          window.openBerkasPersyaratanModal = function () {
            const modal = document.getElementById('modal-berkas-persyaratan');
            const box = document.getElementById('modal-berkas-persyaratan-box');
            const docsList = document.getElementById('spmb-res-docs-list');
            if (!modal || !box) return;

            if (docsList && typeof window.spmbCalcGetDocs === 'function') {
              const docs = window.spmbCalcGetDocs();
              if (docs && docs.length > 0) {
                docsList.innerHTML = docs.map(doc => `
                  <li class="flex items-start gap-2 min-w-0">
                    <span class="w-3.5 h-3.5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                      <svg class="w-2 h-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                    </span>
                    <span class="leading-snug text-slate-700 font-medium text-xs sm:text-[12.5px]">${escapeHtml(doc)}</span>
                  </li>
                `).join('');
              }
            }

            // Catat koordinat scroll dan aktifkan scroll-lock tanpa mengubah posisi body
            docsModalScrollY = window.scrollY || window.pageYOffset || 0;
            docsModalOpen = true;
            document.documentElement.classList.remove('scroll-smooth');
            document.documentElement.classList.add('modal-berkas-open');

            modal.classList.remove('hidden', 'pointer-events-none');
            requestAnimationFrame(() => {
              modal.classList.remove('opacity-0');
              box.classList.remove('scale-95');
              box.classList.add('scale-100');
            });
          };

          window.closeBerkasPersyaratanModal = function () {
            const modal = document.getElementById('modal-berkas-persyaratan');
            const box = document.getElementById('modal-berkas-persyaratan-box');
            if (!modal || !box) return;

            docsModalOpen = false;
            modal.classList.add('opacity-0');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
              modal.classList.add('hidden', 'pointer-events-none');
              document.documentElement.classList.remove('modal-berkas-open');
              document.documentElement.classList.add('scroll-smooth');
            }, 200);
          };

          window.openSyaratKetentuanModal = function () {
            const modal = document.getElementById('modal-syarat-ketentuan');
            const box = document.getElementById('modal-syarat-ketentuan-box');
            if (!modal || !box) return;

            modal.classList.remove('hidden', 'pointer-events-none');
            document.body.classList.add('overflow-hidden');
            requestAnimationFrame(() => {
              modal.classList.remove('opacity-0');
              box.classList.remove('scale-95');
              box.classList.add('scale-100');
            });
          };

          window.closeSyaratKetentuanModal = function () {
            const modal = document.getElementById('modal-syarat-ketentuan');
            const box = document.getElementById('modal-syarat-ketentuan-box');
            if (!modal || !box) return;

            modal.classList.add('opacity-0');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
              modal.classList.add('hidden', 'pointer-events-none');
              document.body.classList.remove('overflow-hidden');
            }, 200);
          };

          window.openKebijakanPrivasiModal = function () {
            const modal = document.getElementById('modal-kebijakan-privasi');
            const box = document.getElementById('modal-kebijakan-privasi-box');
            if (!modal || !box) return;

            modal.classList.remove('hidden', 'pointer-events-none');
            document.body.classList.add('overflow-hidden');
            requestAnimationFrame(() => {
              modal.classList.remove('opacity-0');
              box.classList.remove('scale-95');
              box.classList.add('scale-100');
            });
          };

          window.closeKebijakanPrivasiModal = function () {
            const modal = document.getElementById('modal-kebijakan-privasi');
            const box = document.getElementById('modal-kebijakan-privasi-box');
            if (!modal || !box) return;

            modal.classList.add('opacity-0');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
              modal.classList.add('hidden', 'pointer-events-none');
              document.body.classList.remove('overflow-hidden');
            }, 200);
          };

          document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modal-ketentuan-biaya');
            if (modal) {
              modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                  closeKetentuanBiayaModal();
                }
              });
            }

            const modalDocs = document.getElementById('modal-berkas-persyaratan');
            if (modalDocs) {
              modalDocs.addEventListener('click', (e) => {
                if (e.target === modalDocs) {
                  closeBerkasPersyaratanModal();
                }
              });
            }

            ['modal-syarat-ketentuan', 'modal-kebijakan-privasi'].forEach(id => {
              const m = document.getElementById(id);
              if (m) {
                m.addEventListener('click', (e) => {
                  if (e.target === m) {
                    if (id === 'modal-syarat-ketentuan') closeSyaratKetentuanModal();
                    if (id === 'modal-kebijakan-privasi') closeKebijakanPrivasiModal();
                  }
                });
              }
            });

            document.addEventListener('keydown', (e) => {
              if (e.key === 'Escape') {
                const m = document.getElementById('modal-ketentuan-biaya');
                if (m && !m.classList.contains('hidden')) {
                  closeKetentuanBiayaModal();
                }
                const mDocs = document.getElementById('modal-berkas-persyaratan');
                if (mDocs && !mDocs.classList.contains('hidden')) {
                  closeBerkasPersyaratanModal();
                }
                const mSyarat = document.getElementById('modal-syarat-ketentuan');
                if (mSyarat && !mSyarat.classList.contains('hidden')) {
                  closeSyaratKetentuanModal();
                }
                const mPrivasi = document.getElementById('modal-kebijakan-privasi');
                if (mPrivasi && !mPrivasi.classList.contains('hidden')) {
                  closeKebijakanPrivasiModal();
                }
              }
            });

            const autoModal = '{{ $openModal ?? "" }}' || new URLSearchParams(window.location.search).get('modal');
            if (autoModal === 'syarat-ketentuan') {
              setTimeout(openSyaratKetentuanModal, 150);
            } else if (autoModal === 'kebijakan-privasi') {
              setTimeout(openKebijakanPrivasiModal, 150);
            }

            // Popover Berkas Offline: Hanya aktif saat tombol 'Lihat Lokasi' di-hover
            const btnLokasi = document.getElementById('btn-lihat-lokasi');
            const tooltipLokasi = document.getElementById('tooltip-berkas-offline');
            if (btnLokasi && tooltipLokasi) {
              let lokasiTimer = null;
              const showTooltip = () => {
                clearTimeout(lokasiTimer);
                tooltipLokasi.classList.add('is-hovered');
              };
              const hideTooltip = () => {
                lokasiTimer = setTimeout(() => {
                  tooltipLokasi.classList.remove('is-hovered');
                }, 120);
              };

              btnLokasi.addEventListener('mouseenter', showTooltip);
              btnLokasi.addEventListener('mouseleave', hideTooltip);
              btnLokasi.addEventListener('focus', showTooltip);
              btnLokasi.addEventListener('blur', hideTooltip);

              tooltipLokasi.addEventListener('mouseenter', showTooltip);
              tooltipLokasi.addEventListener('mouseleave', hideTooltip);
            }

            // ==============================================================
            // Micro-interaction Feedback: Unduh Panduan PDF (Locus of Attention)
            // ==============================================================
            const btnPanduan = document.getElementById('hero-btn-panduan');
            const shimmerPanduan = document.getElementById('hero-btn-panduan-shimmer');
            const iconIdle = document.getElementById('hero-panduan-icon-idle');
            const iconLoading = document.getElementById('hero-panduan-icon-loading');
            const iconSuccess = document.getElementById('hero-panduan-icon-success');
            const textPanduan = document.getElementById('hero-btn-panduan-text');

            if (btnPanduan && iconIdle && iconLoading && iconSuccess && textPanduan) {
              let isDownloading = false;
              let loadingTimer = null;
              let resetTimer = null;

              // Helper untuk transisi teks yang halus (cross-fade / micro-slide)
              const changeButtonText = (newText) => {
                textPanduan.style.opacity = '0';
                textPanduan.style.transform = 'translateY(-2px)';
                setTimeout(() => {
                  textPanduan.textContent = newText;
                  textPanduan.style.opacity = '1';
                  textPanduan.style.transform = 'translateY(0)';
                }, 90);
              };

              btnPanduan.addEventListener('click', function (e) {
                // Jika sedang memproses download, cegah klik berulang
                if (isDownloading) {
                  e.preventDefault();
                  return;
                }

                isDownloading = true;

                // 1. Kunci Lebar Tombol secara presisi (Prevent Layout Shift / CLS)
                const lockedWidth = btnPanduan.getBoundingClientRect().width;
                btnPanduan.style.width = `${lockedWidth}px`;

                // 2. State 2 - Loading (Saat diklik, durasi 800ms)
                btnPanduan.setAttribute('aria-disabled', 'true');
                btnPanduan.classList.remove('cursor-pointer', 'hover:bg-white/25', 'active:scale-[0.98]', 'hover:-translate-y-0.5', 'hover:shadow-xl');
                btnPanduan.classList.add('cursor-wait', 'opacity-90');

                // Tampilkan spinner berputar dan shimmer sweep, sembunyikan ikon unduh
                iconIdle.classList.add('hidden');
                iconSuccess.classList.add('hidden');
                iconSuccess.classList.remove('panduan-check-pop');
                iconLoading.classList.remove('hidden');

                if (shimmerPanduan) {
                  shimmerPanduan.classList.remove('hidden');
                  shimmerPanduan.classList.add('panduan-shimmer-active');
                }

                changeButtonText('Mengunduh...');

                clearTimeout(loadingTimer);
                clearTimeout(resetTimer);

                // 3. State 3 - Success (Setelah 800ms, bertahan selama 3000ms)
                loadingTimer = setTimeout(() => {
                  // Sembunyikan spinner & shimmer
                  iconLoading.classList.add('hidden');
                  if (shimmerPanduan) {
                    shimmerPanduan.classList.add('hidden');
                    shimmerPanduan.classList.remove('panduan-shimmer-active');
                  }

                  // Tampilkan ikon centang dengan animasi scale-up pop
                  iconSuccess.classList.remove('hidden');
                  iconSuccess.classList.add('panduan-check-pop');

                  // Styling Success: aksen biru senada tema utama
                  btnPanduan.classList.remove('cursor-wait', 'opacity-90', 'border-white/40', 'bg-white/15');
                  btnPanduan.classList.add('border-blue-400/70', 'bg-blue-950/40', 'shadow-blue-500/20', 'cursor-default');

                  changeButtonText('PDF Terunduh!');

                  // 4. Setelah 3 detik (3000ms), kembalikan tombol secara halus ke State 1 (Idle)
                  resetTimer = setTimeout(() => {
                    // Kembalikan ikon
                    iconSuccess.classList.add('hidden');
                    iconSuccess.classList.remove('panduan-check-pop');
                    iconIdle.classList.remove('hidden');

                    // Kembalikan styling tombol
                    btnPanduan.classList.remove('border-blue-400/70', 'bg-blue-950/40', 'shadow-blue-500/20', 'cursor-default');
                    btnPanduan.classList.add('border-white/40', 'bg-white/15', 'cursor-pointer', 'hover:bg-white/25', 'active:scale-[0.98]', 'hover:-translate-y-0.5', 'hover:shadow-xl');

                    changeButtonText('Panduan Daftar');

                    btnPanduan.removeAttribute('aria-disabled');
                    btnPanduan.style.width = '';
                    isDownloading = false;
                  }, 3000);
                }, 800);
              });
            }
          });
        })();
      </script>
      <script src="{{ asset('assets/js/scroll-reveal.js') }}"></script>
@endpush
