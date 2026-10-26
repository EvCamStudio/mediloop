@extends('layouts.mediloop', ['title' => __('transfers.index.page_title')])

@section('content')
@php
    $tabs = [
        ['label' => __('common.all'), 'value' => ''],
        ['label' => __('transfers.tabs.awaiting_donor'), 'value' => 'PROPOSED'],
        ['label' => __('transfers.tabs.awaiting_recipient'), 'value' => 'APPROVED'],
        ['label' => __('transfers.tabs.in_transit'), 'value' => 'IN_TRANSIT'],
        ['label' => __('transfers.tabs.completed'), 'value' => 'COMPLETED'],
    ];

    $statusLabels = [
        'PROPOSED' => ['label' => __('transfers.tabs.awaiting_donor'), 'tone' => 'pending'],
        'APPROVED' => ['label' => __('transfers.tabs.awaiting_recipient'), 'tone' => 'active'],
        'ACCEPTED' => ['label' => __('transfers.tabs.ready_to_ship'), 'tone' => 'active'],
        'IN_TRANSIT' => ['label' => __('transfers.tabs.in_transit'), 'tone' => 'transit'],
        'COMPLETED' => ['label' => __('transfers.tabs.completed'), 'tone' => 'inactive'],
        'CANCELLED' => ['label' => __('transfers.tabs.cancelled'), 'tone' => 'suspended'],
    ];
@endphp

<div class="ml-page-admin">
    <nav class="ml-breadcrumb" aria-label="{{ __('transfers.breadcrumb_aria') }}">
        <a href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
        <span class="ml-breadcrumb-sep">/</span>
        <span>{{ __('transfers.breadcrumb') }}</span>
    </nav>

    <header class="ml-page-head">
        <div>
            <p class="ml-page-eyebrow">{{ __('transfers.index.eyebrow') }}</p>
            <h1 class="ml-page-title">{{ __('transfers.index.title') }}</h1>
            <p class="ml-page-sub">{{ __('transfers.index.subtitle') }}</p>
        </div>
        <div class="ml-page-actions">
            @if(! $user->isDinkesAuditor())
                <button type="button" onclick="document.getElementById('ml-propose-select-modal').style.display='flex'" class="ml-btn-primary inline-flex items-center gap-1.5" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Ajukan Transfer Baru</span>
                </button>
            @endif
            <a href="{{ route('dashboard') }}" class="ml-btn-quiet">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                {{ __('transfers.index.propose_from_stock') }}
            </a>
        </div>
    </header>

    <nav class="ml-registry-tabs" aria-label="{{ __('transfers.index.tabs_aria') }}">
        @foreach($tabs as $tab)
            <a
                href="{{ $tab['value'] === '' ? route('transfers.index') : route('transfers.index', ['status' => $tab['value']]) }}"
                class="ml-registry-tab {{ $currentStatus === $tab['value'] ? 'is-active' : '' }}"
                @if($currentStatus === $tab['value']) aria-current="page" @endif
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>

    <section class="ml-registry-panel" aria-labelledby="transfers-title">
        <h2 id="transfers-title" class="ml-sr-only">{{ __('transfers.index.list_title') }}</h2>

        @if($transfers->isEmpty())
            <div class="ml-empty-state">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path d="M4 7h11l4 4v6H4z" />
                    <path d="M4 7V5h8v2M8 13h6" />
                </svg>
                <p class="ml-empty-title">{{ __('transfers.index.empty_title') }}</p>
                <p class="ml-empty-copy">{{ __('transfers.index.empty_copy') }}</p>
                <a href="{{ route('dashboard') }}" class="ml-btn-quiet">{{ __('transfers.index.empty_cta') }}</a>
            </div>
        @else
            <div class="ml-table-responsive">
                <table class="ml-table ml-table-airy ml-table-registry ml-table-transfers">
                    <caption class="ml-sr-only">{{ __('transfers.index.table_caption') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('transfers.table.transfer') }}</th>
                            <th scope="col">{{ __('transfers.table.medicine_quantity') }}</th>
                            <th scope="col">{{ __('transfers.table.facility_route') }}</th>
                            <th scope="col">{{ __('transfers.table.score') }}</th>
                            <th scope="col">{{ __('common.status') }}</th>
                            <th scope="col"><span class="ml-sr-only">{{ __('common.actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            @php
                                $statusMeta = $statusLabels[$transfer->status] ?? ['label' => $transfer->status, 'tone' => 'inactive'];
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('transfers.show', $transfer) }}" class="ml-transfer-code">{{ $transfer->transfer_code }}</a>
                                    <div class="ml-cell-meta">{{ $transfer->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td data-label="{{ __('transfers.table.medicine_quantity') }}">
                                    <div class="ml-cell-title">{{ $transfer->batch->medicine->name }}</div>
                                    <div class="ml-cell-meta">
                                        {{ number_format($transfer->quantity) }} {{ $transfer->batch->medicine->unit }}
                                        <span class="ml-meta-dot">·</span>
                                        {{ __('transfers.batch_label', ['batch' => $transfer->batch->batch_number]) }}
                                    </div>
                                </td>
                                <td data-label="{{ __('transfers.table.facility_route') }}">
                                    <div class="ml-route">
                                        <span class="ml-route-name">{{ $transfer->donorFacility->name }}</span>
                                        <span class="ml-route-arrow" aria-hidden="true">→</span>
                                        <span class="ml-route-name is-recipient">{{ $transfer->recipientFacility->name }}</span>
                                    </div>
                                    <div class="ml-cell-meta">{{ $transfer->distance_km }} km · {{ __('transfers.index.air_distance') }}</div>
                                </td>
                                <td data-label="{{ __('transfers.table.score') }}">
                                    <span class="ml-score">{{ $transfer->match_score }}%</span>
                                </td>
                                <td data-label="{{ __('common.status') }}">
                                    <span class="ml-status ml-status-{{ $statusMeta['tone'] }}">{{ $statusMeta['label'] }}</span>
                                </td>
                                <td class="is-right">
                                    <a href="{{ route('transfers.show', $transfer) }}" class="ml-row-open">
                                        {{ __('transfers.index.track') }}
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M5 12h14M13 6l6 6-6 6" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="ml-pagination-wrap">
                {{ $transfers->links() }}
            </div>
        @endif
    </section>

    <!-- Modal Pilih Batch untuk Transfer Baru -->
    <div id="ml-propose-select-modal" aria-labelledby="select-batch-modal-title" role="dialog" aria-modal="true" style="display: none; position: fixed; inset: 0; z-index: 999; align-items: center; justify-content: center;">
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);" onclick="document.getElementById('ml-propose-select-modal').style.display='none'"></div>

        <div style="position: relative; z-index: 1000; width: 100%; max-width: 680px; margin: 1rem; background: #fff; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; text-align: left; max-height: 85vh; display: flex; flex-direction: column;">
            <!-- Header -->
            <div style="background: #f8fafc; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0b1f4b;" id="select-batch-modal-title">Pilih Batch Obat untuk Diredistribusikan</h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #52627f;">Pilih batch obat dari inventaris faskes Anda untuk dicarikan rekomendasi penerima optimal</p>
                </div>
                <button type="button" onclick="document.getElementById('ml-propose-select-modal').style.display='none'" style="background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0.25rem; border-radius: 0.5rem;">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Content / Batch List -->
            <div style="padding: 1.25rem 1.5rem; overflow-y: auto; flex-grow: 1;">
                @if(isset($availableBatches) && $availableBatches->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($availableBatches as $batch)
                            @php
                                $daysRemaining = max(0, Carbon\Carbon::parse($batch->expiry_date)->diffInDays(now()));
                                $isNearExpiry = $daysRemaining <= 90;
                                $riskTone = match($batch->risk_level) {
                                    'CRITICAL' => '#ef4444',
                                    'HIGH' => '#f97316',
                                    'MEDIUM' => '#eab308',
                                    default => '#10b981',
                                };
                                $riskLabel = match($batch->risk_level) {
                                    'CRITICAL' => 'Kritis',
                                    'HIGH' => 'Tinggi',
                                    'MEDIUM' => 'Sedang',
                                    default => 'Aman',
                                };
                            @endphp
                            <div style="border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; transition: background 0.15s; background: #fff;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-weight: 700; color: #0b1f4b; font-size: 0.9rem;">{{ $batch->medicine->name }}</span>
                                        <span style="font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: {{ $riskTone }}15; color: {{ $riskTone }};">
                                            Risiko {{ $riskLabel }}
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.75rem; color: #64748b;">
                                        <span>Batch: <strong style="font-family: monospace; color: #334155;">{{ $batch->batch_number }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Stok: <strong style="color: #0b1f4b;">{{ number_format($batch->current_stock) }} {{ $batch->medicine->unit }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Exp: <strong style="color: {{ $daysRemaining <= 60 ? '#b91c1c' : '#334155' }};">{{ Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}</strong> ({{ $daysRemaining }} hari lagi)</span>
                                    </div>
                                </div>

                                <a href="{{ route('transfers.propose', $batch) }}" class="ml-btn-primary ml-btn-sm" style="flex-shrink: 0; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <span>Pilih &amp; Cari Penerima</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2.5rem 1rem;">
                        <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.75rem; color: #94a3b8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0b1f4b;">Tidak Ada Stok Obat Aktif</h4>
                        <p style="margin: 0.25rem 0 1rem; font-size: 0.75rem; color: #64748b;">Fasilitas Anda belum memiliki batch obat dengan sisa stok aktif untuk diredistribusikan.</p>
                        <a href="{{ route('batches.create') }}" class="ml-btn-primary ml-btn-sm" style="text-decoration: none;">
                            + Tambah Stok Batch
                        </a>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div style="background: #f8fafc; padding: 0.75rem 1.5rem; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; flex-shrink: 0;">
                <button type="button" onclick="document.getElementById('ml-propose-select-modal').style.display='none'" style="padding: 0.4rem 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; background: none; border: none; cursor: pointer; border-radius: 0.5rem;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('ml-propose-select-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
    });
</script>
@endsection
@endsection
