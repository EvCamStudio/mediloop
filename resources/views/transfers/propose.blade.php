@extends('layouts.mediloop', ['title' => 'Rekomendasi & Pengusulan Transfer'])

@section('content')
<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ml-breadcrumb-sep">/</span>
        <a href="{{ route('transfers.index') }}">Redistribusi</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>Usulan batch {{ $batch->batch_number }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">Rekomendasi redistribusi</p>
            <h1 class="ml-page-title">Cari penerima untuk {{ $batch->medicine->name }}</h1>
            <p class="ml-page-sub">Kandidat diperingkatkan dari kebutuhan, jarak, dan kesiapan logistik.</p>
        </div>
    </header>

    <div class="ml-alert ml-alert-info ml-compliance-banner" role="note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z" />
            <path d="M12 8v4m0 4h.01" />
        </svg>
        <div>
            <strong>Kepatuhan regulasi kefarmasian</strong>
            <p>
                MediLoop adalah sistem pendukung keputusan, bukan marketplace. Redistribusi tetap memerlukan
                persetujuan Kepala Instalasi Farmasi donor dan verifikasi penerimaan fisik oleh Kepala Fasilitas penerima.
            </p>
        </div>
    </div>

    <div class="ml-two-col">
        <div>
            <section class="ml-card" aria-labelledby="source-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="source-title">Batch sumber</h2>
                        <p class="ml-card-sub">{{ $batch->facility->name }} · batch {{ $batch->batch_number }}</p>
                    </div>
                    <span class="ml-risk ml-risk-high">Risiko tinggi</span>
                </div>
                <div class="ml-card-body">
                    <div class="ml-metric-grid">
                        <div class="ml-metric">
                            <span class="ml-metric-label">Stok donor</span>
                            <span class="ml-metric-value">{{ number_format($batch->current_stock) }} <small>{{ $batch->medicine->unit }}</small></span>
                        </div>
                        <div class="ml-metric">
                            <span class="ml-metric-label">Laju pakai</span>
                            <span class="ml-metric-value">{{ $batch->daily_burn_rate }} <small>unit/hari</small></span>
                        </div>
                        <div class="ml-metric">
                            <span class="ml-metric-label">Sisa kedaluwarsa</span>
                            <span class="ml-metric-value">{{ $batch->days_to_expiry }} <small>hari</small></span>
                        </div>
                        <div class="ml-metric is-risk">
                            <span class="ml-metric-label">Proyeksi terbuang</span>
                            <span class="ml-metric-value">{{ number_format($batch->projected_waste) }} <small>unit</small></span>
                        </div>
                    </div>
                    <p class="ml-help">
                        Proyeksi limbah dihitung dari selisih stok dan laju pemakaian sampai tanggal kedaluwarsa.
                    </p>
                </div>
            </section>

            <section class="ml-card" aria-labelledby="candidates-title">
                <div class="ml-card-head ml-card-head-split">
                    <div>
                        <h2 class="ml-card-title" id="candidates-title">Kandidat penerima</h2>
                        <p class="ml-card-sub">50% kebutuhan · 30% jarak udara · 20% logistik</p>
                    </div>
                    <span class="ml-tab-count">{{ count($candidates) }} faskes</span>
                </div>

                <div class="ml-sort-row" role="group" aria-label="Urutkan kandidat">
                    <a
                        href="{{ route('transfers.propose', ['batch' => $batch, 'sort' => 'score']) }}"
                        class="ml-sort-pill {{ $sort === 'score' ? 'is-active' : '' }}"
                        @if($sort === 'score') aria-current="true" @endif
                    >Skor terbaik</a>
                    <a
                        href="{{ route('transfers.propose', ['batch' => $batch, 'sort' => 'distance']) }}"
                        class="ml-sort-pill {{ $sort === 'distance' ? 'is-active' : '' }}"
                        @if($sort === 'distance') aria-current="true" @endif
                    >Terdekat</a>
                    <span class="ml-sort-note">Jarak udara (Haversine), bukan jarak tempuh jalan.</span>
                </div>

                @if(count($candidates) === 0)
                    <div class="ml-empty-state">
                        <p class="ml-empty-title">Belum ada kandidat penerima</p>
                        <p class="ml-empty-copy">Tidak ada fasilitas yang memenuhi syarat rantai dingin dan jarak saat ini.</p>
                    </div>
                @else
                    <ol class="ml-candidate-list">
                        @foreach($candidates as $index => $candidate)
                            <li class="ml-candidate {{ $index === 0 ? 'is-top' : '' }}">
                                <div class="ml-candidate-rank" aria-hidden="true">{{ $index + 1 }}</div>
                                <div class="ml-candidate-copy">
                                    <div class="ml-candidate-name">{{ $candidate['facility']->name }}</div>
                                    <div class="ml-cell-meta">
                                        {{ \App\Enums\FacilityType::tryFrom($candidate['facility']->type)?->label() ?? $candidate['facility']->type }}
                                        <span class="ml-meta-dot">·</span>
                                        {{ $candidate['facility']->city ?? $candidate['facility']->address }}
                                    </div>
                                    <div class="ml-candidate-metrics">
                                        <span>Kebutuhan <strong>{{ $candidate['need_score'] }}%</strong></span>
                                        <span>Jarak <strong>{{ $candidate['proximity_score'] }}%</strong></span>
                                        <span>Logistik <strong>{{ $candidate['logistics_score'] }}%</strong></span>
                                        <span>Kapasitas <strong>{{ $candidate['recipient_intake_cap'] }} unit</strong></span>
                                    </div>
                                </div>
                                <div class="ml-candidate-side">
                                    <div class="ml-candidate-score">{{ $candidate['match_score'] }}%</div>
                                    <div class="ml-cell-meta">{{ $candidate['distance_km'] }} km</div>
                                    <button
                                        type="button"
                                        class="ml-btn-primary ml-btn-sm"
                                        onclick="selectCandidate({{ $candidate['facility']->id }}, '{{ addslashes($candidate['facility']->name) }}', {{ $candidate['recommended_quantity'] }})"
                                    >Pilih</button>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        </div>

        <aside>
            <form method="POST" action="{{ route('transfers.store') }}" class="ml-card ml-sticky" id="proposalForm" novalidate>
                @csrf
                <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                <input type="hidden" id="selectedFacilityId" name="recipient_facility_id" value="{{ $candidates[0]['facility']->id ?? '' }}">

                <div class="ml-card-head">
                    <h2 class="ml-card-title">Ajukan transfer</h2>
                    <p class="ml-card-sub">Usulan diteruskan ke Kepala Instalasi Farmasi donor.</p>
                </div>
                <div class="ml-card-body">
                    @if($errors->any())
                        <div class="ml-alert ml-alert-error" role="alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="ml-field">
                        <label for="selectedFacilityName" class="ml-label">Fasilitas penerima</label>
                        <input type="text" id="selectedFacilityName" readonly value="{{ $candidates[0]['facility']->name ?? '' }}" class="ml-input">
                    </div>

                    <div class="ml-field">
                        <label for="quantity" class="ml-label">Jumlah unit <span class="ml-required">*</span></label>
                        <input type="number" id="quantity" name="quantity" min="1" max="{{ $batch->current_stock }}" value="{{ $candidates[0]['recommended_quantity'] ?? 100 }}" class="ml-input is-mono" required>
                        <p class="ml-help">Maksimal {{ number_format($batch->current_stock) }} {{ $batch->medicine->unit }} tersedia di donor.</p>
                    </div>

                    <div class="ml-field">
                        <label for="notes" class="ml-label">Catatan justifikasi</label>
                        <textarea id="notes" name="notes" class="ml-textarea" rows="4" placeholder="Jelaskan kebutuhan penerima dan alasan redistribusi.">Pengusulan redistribusi surplus stok {{ $batch->medicine->name }} untuk mencegah pemusnahan obat kedaluwarsa dan mendukung ketersediaan di fasilitas sekitar.</textarea>
                    </div>

                    <div class="ml-action-row">
                        <span class="ml-help">Pengusul: <strong>{{ $user->name }}</strong></span>
                        <button type="submit" class="ml-btn-primary">
                            Ajukan transfer
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </div>
            </form>
        </aside>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectCandidate(facilityId, facilityName, recommendedQty) {
        document.getElementById('selectedFacilityId').value = facilityId;
        document.getElementById('selectedFacilityName').value = facilityName;
        document.getElementById('quantity').value = recommendedQty;
        document.getElementById('proposalForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.getElementById('quantity').focus({ preventScroll: true });
    }
</script>
@endsection
