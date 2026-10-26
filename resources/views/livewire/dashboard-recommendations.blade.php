<div class="ml-section ml-rec-band" id="match-section">
    <!-- Header Section -->
    <div class="ml-section-header">
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <h2 class="ml-section-title" id="match-title" style="margin: 0;">{{ __('dashboard.match.title') }}</h2>
            <p class="ml-section-sub" style="margin: 0;">
                Sistem mencocokkan faskes penerima yang memiliki <strong style="color: var(--ml-text)">kebutuhan riil obat yang sama</strong>, <strong style="color: var(--ml-text)">jarak tempuh terdekat</strong>, dan <strong style="color: var(--ml-text)">kesiapan logistik</strong>.
            </p>
        </div>
    </div>

    <!-- Medicine Selector (Sempre Visible) -->
    <div class="ml-rec-selector-wrap" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; padding: 16px; background: var(--ml-surface); border-radius: 12px; border: 1px solid var(--ml-border);">
        <div class="ml-rec-selector-label" style="font-size: 0.75rem; font-weight: 700; color: var(--ml-muted); text-transform: uppercase; letter-spacing: 0.04em;">
            Pilih Obat Berisiko untuk Melihat Faskes yang Membutuhkan:
        </div>
        <select wire:model.live="selectedBatchId" 
                class="ml-select" 
                style="padding: 8px 12px; font-size: 0.9rem; width: 100%; max-width: 400px; border-radius: 8px; border: 1px solid var(--ml-border); background: var(--ml-card); color: var(--ml-text);">
            <option value="">-- Pilih Obat --</option>
            @foreach($riskBatches as $rb)
                <option value="{{ $rb->id }}">{{ $rb->medicine->name }} — Batch {{ $rb->batch_number }} ({{ $rb->risk_level }})</option>
            @endforeach
        </select>
    </div>

    @if($currentBatch)
        <!-- Batch Info Banner -->
        <div class="ml-rec-panel-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 12px 16px; background: var(--ml-primary); color: white; border-radius: 10px; font-size: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                <strong>{{ $currentBatch->medicine->name }}</strong> 
                <span style="opacity: 0.8;">&middot; Batch {{ $currentBatch->batch_number }}</span>
            </div>
            <span style="font-size: 0.75rem; background: rgba(0,0,0,0.2); padding: 2px 8px; border-radius: 4px;">
                Skor = Kebutuhan (50%) + Jarak (30%) + Logistik (20%)
            </span>
        </div>

        <!-- Recommendation Grid -->
        <div class="ml-rec-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
            @forelse($candidates as $index => $candidate)
                <article class="ml-rec-card" style="display: flex; flex-direction: column; justify-content: space-between; border: 1px solid var(--ml-border); border-radius: 16px; padding: 20px; background: var(--ml-card); box-shadow: var(--ml-shadow-panel);">
                    <div>
                        <!-- Card Header -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="background: var(--ml-primary); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: bold;">{{ $index + 1 }}</span>
                                <div style="font-weight: 700; font-size: 1.1rem; color: var(--ml-text);">{{ $candidate['facility']->name }}</div>
                            </div>
                            <span style="background: var(--ml-surface); padding: 4px 10px; border-radius: 8px; font-size: 0.85rem; font-weight: 800; border: 1px solid var(--ml-border); color: var(--ml-primary);">{{ $candidate['match_score'] }}% Cocok</span>
                        </div>

                        <div style="font-size: 0.8rem; color: var(--ml-muted); margin-bottom: 16px;">
                            {{ $candidate['facility']->type }} &middot; {{ $candidate['distance_km'] }} km dari faskes Anda
                        </div>

                        <!-- Score Breakdown (Transparency) -->
                        <div class="ml-rec-calc-card" style="background: var(--ml-surface); border-radius: 12px; padding: 14px; margin-bottom: 16px; font-size: 0.75rem; border: 1px solid var(--ml-border);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-weight: 700; color: var(--ml-text); border-bottom: 1px solid var(--ml-border); padding-bottom: 4px;">
                                <span>Rincian Penilaian:</span>
                                <span>Total: {{ $candidate['match_score'] }}%</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span>1. Kebutuhan Riil (50%):</span>
                                    <span style="font-weight: 700; color: var(--color-risk-critical);">{{ $candidate['need_score'] }}%</span>
                                </div>
                                <div style="font-size: 0.7rem; color: var(--ml-muted); margin-bottom: 4px; font-style: italic;">{{ $candidate['status_label'] }}</div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>2. Jarak Terdekat (30%):</span>
                                    <span style="font-weight: 700;">{{ $candidate['proximity_score'] }}% ({{ $candidate['distance_km'] }} km)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>3. Kesiapan Logistik (20%):</span>
                                    <span style="font-weight: 700;">{{ $candidate['logistics_score'] }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Capacity -->
                        <div style="background: var(--ml-surface); border-radius: 8px; padding: 10px; margin-bottom: 16px; font-size: 0.75rem; border: 1px solid var(--ml-border); display: flex; align-items: center; gap: 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>Kapasitas serap: <strong style="color: var(--ml-text)">{{ $candidate['recipient_intake_cap'] }} {{ $currentBatch->medicine->unit }}</strong></span>
                        </div>
                    </div>

                    <a href="{{ route('transfers.propose', $currentBatch) }}" class="ml-btn-primary ml-btn-sm" style="width: 100%; text-align: center; display: block; padding: 12px; border-radius: 10px; text-decoration: none; font-size: 0.9rem; font-weight: 600; margin-top: auto;">
                        Ajukan Transfer ke Faskes Ini
                    </a>
                </article>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--ml-muted); background: var(--ml-surface); border-radius: 16px; border: 1px dashed var(--ml-border);">
                    {{ __('dashboard.match.empty_candidates') }}
                </div>
            @endforelse
        </div>
    @elseif(!$currentBatch && $riskBatches->isEmpty())
        <div style="text-align: center; padding: 60px 20px; color: var(--ml-muted); background: var(--ml-surface); border-radius: 16px; border: 1px dashed var(--ml-border);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin-bottom: 16px; opacity: 0.5;" aria-hidden="true"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            <p style="font-size: 1rem; font-weight: 600;">{{ __('dashboard.match.empty_no_risk') }}</p>
            <p style="font-size: 0.85rem; opacity: 0.7;">Tidak ada stok obat yang masuk kategori risiko saat ini.</p>
        </div>
    @endif

    <p class="ml-rec-note" style="margin-top: 24px; font-size: 0.75rem; color: var(--ml-muted); text-align: center; font-style: italic;">
        Rekomendasi bersifat saran sistem pendukung keputusan (DSS). Setiap transfer tetap memerlukan persetujuan Kepala Instalasi Farmasi faskes pengirim dan penerima.
    </p>
</div>