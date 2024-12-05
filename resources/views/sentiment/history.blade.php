@extends('layout')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="fas fa-history"></i> Analysis History</h2>
            <div class="text-white">
                Page {{ $analyses->currentPage() }} of {{ $analyses->lastPage() }}
            </div>
        </div>
        <div class="card-body">
            @if($analyses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">#</th>
                                <th class="text-center" width="15%">Date & Time</th>
                                <th width="35%">Analyzed Text</th>
                                <th class="text-center" width="15%">Sentiment</th>
                                <th width="30%">Score Breakdown</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyses as $index => $analysis)
                                <tr>
                                    <td class="text-center align-middle">
                                        {{ $analyses->firstItem() + $index }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="fw-bold">{{ $analysis->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $analysis->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="align-middle">
                                        <div class="text-wrap" style="max-width: 300px;">
                                            {{ $analysis->text }}
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="sentiment-badge 
                                            @if($analysis->overall_sentiment === 'pos') bg-success
                                            @elseif($analysis->overall_sentiment === 'neg') bg-danger
                                            @else bg-warning @endif 
                                            text-white rounded-pill px-3 py-2">
                                            {{ $analysis->overall_sentiment === 'pos' ? 'Positive 😊' : 
                                               ($analysis->overall_sentiment === 'neg' ? 'Negative 😞' : 'Neutral 😐') }}
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="score-bars">
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-success">Positive</small>
                                                    <small class="text-success">{{ number_format($analysis->positive_score, 1) }}%</small>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $analysis->positive_score }}%"></div>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-warning">Neutral</small>
                                                    <small class="text-warning">{{ number_format($analysis->neutral_score, 1) }}%</small>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" 
                                                         style="width: {{ $analysis->neutral_score }}%"></div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-danger">Negative</small>
                                                    <small class="text-danger">{{ number_format($analysis->negative_score, 1) }}%</small>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-danger" role="progressbar" 
                                                         style="width: {{ $analysis->negative_score }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            {{-- First Page --}}
                            <li class="page-item {{ $analyses->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $analyses->url(1) }}" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            
                            {{-- Previous Page --}}
                            <li class="page-item {{ $analyses->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $analyses->previousPageUrl() }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            {{-- Page Numbers --}}
                            @for ($i = 1; $i <= $analyses->lastPage(); $i++)
                                <li class="page-item {{ $analyses->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $analyses->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            {{-- Next Page --}}
                            <li class="page-item {{ $analyses->currentPage() == $analyses->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $analyses->nextPageUrl() }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>

                            {{-- Last Page --}}
                            <li class="page-item {{ $analyses->currentPage() == $analyses->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $analyses->url($analyses->lastPage()) }}" aria-label="Last">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        No analysis history found. Start by analyzing some text!
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.sentiment-badge {
    display: inline-block;
    font-weight: 500;
    font-size: 0.9rem;
    white-space: nowrap;
}

.score-bars {
    min-width: 200px;
}

.progress {
    background-color: rgba(0,0,0,0.05);
}

.table > :not(caption) > * > * {
    padding: 1rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    padding: 0.5rem 0.75rem;
    color: #0d6efd;
    background-color: #fff;
    border: 1px solid #dee2e6;
}

.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}
</style>
@endsection