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

    /* Owner Section */
    .owner-section {
        padding: 4rem 0;
        display: flex;
        align-items: center;
        gap: 4rem;
        border-bottom: 1px solid var(--glass-border);
    }

    .owner-photo-wrapper {
        flex: 1;
        max-width: 400px;
        border-radius: 24px;
        overflow: hidden;
        border: 2px solid rgba(212, 175, 55, 0.3);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        position: relative;
    }

    .owner-photo {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.5s ease;
    }

    .owner-photo-wrapper:hover .owner-photo {
        transform: scale(1.05);
    }

    .owner-content {
        flex: 1.5;
    }

    .owner-greeting {
        font-size: 1.15rem;
        line-height: 1.8;
        color: var(--text-muted);
        margin-bottom: 2rem;
        position: relative;
    }

    .owner-greeting::before {
        content: '"';
        font-family: 'Playfair Display', serif;
        font-size: 4rem;
        position: absolute;
        top: -1.5rem;
        left: -2rem;
        color: rgba(212, 175, 55, 0.2);
    }

    .owner-name {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.25rem;
    }

    .owner-title {
        color: var(--gold-main);
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .owner-section {
            flex-direction: column;
            text-align: center;
            gap: 2rem;
        }
        .owner-greeting::before {
            left: -1rem;
        }
    }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Tentang <span>Kami</span></h1>
        <div class="section-divider mx-auto"></div>
    </div>
</div>

<div class="container mb-16">
    <div class="owner-section">
        <div class="owner-photo-wrapper">
            @if($aboutOwnerPhoto)
                <img src="{{ asset('storage/' . $aboutOwnerPhoto) }}" alt="Owner" class="owner-photo">
            @else
                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Placeholder Owner" class="owner-photo">
            @endif
        </div>
        <div class="owner-content">
            <div class="owner-greeting">
                {!! nl2br(e($aboutOwnerMessage)) !!}
            </div>
            <div>
                <div class="owner-name">Founder & Owner</div>
                <div class="owner-title">AFFA Swimming Academy</div>
            </div>
        </div>
    </div>
</div>

<!-- TENTANG KAMI & TEAM SECTION (Reused from welcome) -->
<section class="team-section" style="border-top: none; padding-top: 2rem;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Tim <span>Profesional</span> Kami</h2>
            <div class="section-divider"></div>
            <p class="mt-4" style="color: var(--text-muted); font-size: 1.05rem; max-width: 600px; text-align: center;">
                Didukung oleh pelatih berlisensi dan berpengalaman.
            </p>
        </div>

        <div class="team-grid">
            @forelse($teams as $team)
                <div class="team-card group">
                    @if($team->photo)
                        <img src="{{ asset('storage/' . $team->photo) }}" alt="{{ $team->name }}" class="team-avatar">
                    @else
                        <div class="team-avatar-placeholder">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    @endif
                    <div class="team-info">
                        <h4>{{ $team->name }}</h4>
                        <p class="position">{{ $team->position }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-gray-400 py-8" style="grid-column: span 3;">
                    Belum ada profil tim yang ditampilkan saat ini.
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
