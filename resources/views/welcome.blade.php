@extends('layouts.public')

@section('content')
<!-- Main Hero Section -->
<section class="hero-section">
    <div class="container hero-grid">
        
        <!-- Left: Hero Text (About the Swimming Club) -->
        <div class="hero-content">
            <div class="badge">
                <i class="fa-solid fa-award"></i> Resmi & Berlisensi
            </div>
            
            <h1 class="hero-title">
                {!! str_replace('AFFA Swimming Academy', '<span>AFFA Swimming Academy</span>', e($landingTitle)) !!}
            </h1>
            
            <p class="hero-desc">
                {{ $landingSubtitle }}
            </p>

            <div class="hero-cta">
                <a href="#program" class="btn-primary">
                    <i class="fa-solid fa-calendar-check"></i> Jelajahi Program
                </a>
                <a href="{{ url('/tentang-kami') }}" class="btn-secondary">
                    <i class="fa-solid fa-circle-info"></i> Tentang Kami
                </a>
            </div>

            <!-- Club Highlight Stats -->
            <div class="stats-container">
                <div class="stat-card">
                    <span class="stat-number">{{ $totalCoaches }}+</span>
                    <span class="stat-label">Pelatih Profesional</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $activeStudents }}</span>
                    <span class="stat-label">Murid Aktif</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $totalAlumni }}+</span>
                    <span class="stat-label">Alumni Terlatih</span>
                </div>
            </div>
        </div>

        <!-- Right: High Aesthetic Swimmer Image with Luxury Frame -->
        <div class="hero-media">
            <div class="media-glow"></div>
            <div class="image-wrapper">
                <img src="{{ asset('swimming_hero.png') }}" alt="AFFA Swimming Swimmer Profile" />
            </div>
        </div>

    </div>
</section>

<!-- TENTANG KAMI & TEAM SECTION -->
<section class="team-section" id="about">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Tentang <span>Kami</span></h2>
            <div class="section-divider"></div>
            <p class="mt-4" style="color: var(--text-muted); font-size: 1.05rem; max-width: 600px; text-align: center;">
                Kenali lebih dekat sosok-sosok profesional di balik kesuksesan AFFA Swimming Club. Dedikasi dan keahlian mereka adalah kunci dari setiap prestasi.
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
        
        <div style="text-align: center; margin-top: 4rem;">
            <a href="{{ url('/tentang-kami') }}" class="btn-secondary">
                Lihat Profil Lengkap <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
            </a>
        </div>
    </div>
</section>

<!-- NEWS & ANNOUNCEMENT SECTION -->
<section class="news-section" id="berita">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Berita <span>& Pengumuman</span></h2>
            <div class="section-divider"></div>
            <p class="mt-4" style="color: var(--text-muted); font-size: 1.05rem; max-width: 600px; text-align: center;">
                Dapatkan informasi terbaru mengenai aktivitas, prestasi, dan pengumuman penting dari AFFA Swimming Club.
            </p>
        </div>

        <div class="news-grid">
            @forelse($articles as $article)
                <div class="news-card">
                    <div class="news-image-wrapper">
                        <span class="category-badge badge-{{ $article->category }}">{{ ucfirst($article->category) }}</span>
                        @if($article->image)
                            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="news-image">
                        @else
                            <img src="https://images.unsplash.com/photo-1519335969186-5384668fde34?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Placeholder" class="news-image">
                        @endif
                    </div>
                    <div class="news-content">
                        <div class="news-date">
                            <i class="fa-regular fa-calendar"></i> {{ $article->created_at->format('d M Y') }}
                        </div>
                        <h3 class="news-card-title">{{ $article->title }}</h3>
                        <p class="news-excerpt">
                            {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 120) }}
                        </p>
                    </div>
                    <div class="news-footer">
                        <a href="#" class="btn-readmore">
                            Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-gray-400 py-8">
                    Belum ada berita atau pengumuman saat ini.
                </div>
            @endforelse
        </div>
        
        <div style="text-align: center; margin-top: 4rem;">
            <a href="#" class="btn-secondary">
                Lihat Semua Berita <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
            </a>
        </div>
    </div>
</section>
@endsection
