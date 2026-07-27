@extends('layouts.public')

@section('content')
<style>
    .page-header {
        padding: 6rem 0 3rem;
        text-align: center;
        background: radial-gradient(circle at center, rgba(12, 26, 48, 0.8) 0%, transparent 100%);
    }

    .page-title {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 1rem;
    }

    .page-title span {
        background: var(--gold-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        padding: 4rem 0;
    }

    .contact-info {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 3rem;
        backdrop-filter: blur(10px);
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .contact-item:last-child {
        margin-bottom: 0;
    }

    .contact-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(212, 175, 55, 0.1);
        color: var(--gold-main);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        border: 1px solid rgba(212, 175, 55, 0.2);
    }

    .contact-detail h4 {
        color: var(--text-main);
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .contact-detail p, .contact-detail a {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .contact-detail a:hover {
        color: var(--gold-main);
    }

    .map-container {
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid var(--glass-border);
        box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        height: 100%;
        min-height: 400px;
        background: #000;
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        .contact-info {
            padding: 2rem;
        }
    }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Hubungi <span>Kami</span></h1>
        <div class="section-divider mx-auto"></div>
        <p class="mt-4" style="color: var(--text-muted); font-size: 1.05rem; max-width: 600px; text-align: center; margin: 1rem auto 0;">
            Kami siap menjawab pertanyaan Anda. Jangan ragu untuk menghubungi kami melalui kanal berikut.
        </p>
    </div>
</div>

<div class="container mb-16">
    <div class="contact-grid">
        <div class="contact-info">
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div class="contact-detail">
                    <h4>Alamat Kantor</h4>
                    <p>{{ $contactAddress ?? 'Jl. Kolam Renang No. 123, Kota Anda' }}</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div class="contact-detail">
                    <h4>Nomor Telepon / WhatsApp</h4>
                    <a href="tel:{{ $contactPhone ?? '' }}">{{ $contactPhone ?? '+62 812 3456 7890' }}</a>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="contact-detail">
                    <h4>Email</h4>
                    <a href="mailto:{{ $contactEmail ?? '' }}">{{ $contactEmail ?? 'info@affaswimming.com' }}</a>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fa-brands fa-instagram"></i>
                </div>
                <div class="contact-detail">
                    <h4>Instagram</h4>
                    <a href="{{ $contactInstagram ?? '#' }}" target="_blank">{{ $contactInstagram ?? '@affaswimming' }}</a>
                </div>
            </div>
        </div>

        <div class="map-container">
            @if($contactMapEmbed)
                {!! $contactMapEmbed !!}
            @else
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126907.03960133488!2d106.74100589139632!3d-6.284245648831349!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f14d30079f01%3A0x2e74f2341fff266d!2sStadion%20Gelora%20Bung%20Karno!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @endif
        </div>
    </div>
</div>
@endsection
