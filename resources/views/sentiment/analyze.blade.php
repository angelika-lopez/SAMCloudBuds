@extends('layout')

@section('content')
<div class="container">
    <!-- Input Section -->
    <div class="row justify-content-center">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="mb-0"><i class="fas fa-robot"></i> Sentiment Analysis</h2>
                </div>
                <div class="card-body">
                    <form id="analysisForm" onsubmit="analyzeSentiment(event)">
                        @csrf
                        <div class="form-group">
                            <label for="text" class="form-label fw-bold mb-2">
                                <i class="fas fa-pen"></i> Enter Text to Analyze
                            </label>
                            <textarea 
                                class="form-control" 
                                id="text" 
                                name="text" 
                                rows="5" 
                                placeholder="Type or paste your text here..."
                                required
                            ></textarea>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Analyze Sentiment
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearForm()">
                                <i class="fas fa-eraser"></i> Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="col-md-12">
            <div id="results" class="card shadow-sm" style="display: none;">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-chart-pie"></i> Analysis Results</h3>
                </div>
                <div class="card-body">
                    <!-- Overall Sentiment -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="text-center">
                                <h4 class="mb-3">Overall Sentiment</h4>
                                <div id="overallSentimentBadge" class="d-inline-block rounded-pill px-4 py-2 text-white fs-5">
                                    <span id="overallSentiment"></span>
                                    <span id="sentimentEmoji" class="ms-2 fs-4"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sentiment Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="mb-3">Sentiment Breakdown</h4>
                            <div class="score-container">
                                <!-- Positive Score -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-success">
                                            <i class="fas fa-smile"></i> Positive
                                        </span>
                                        <span class="badge bg-success" id="positiveScore"></span>
                                    </div>
                                    <div class="progress" style="height: 15px;">
                                        <div id="positiveBar" class="progress-bar bg-success" role="progressbar"></div>
                                    </div>
                                </div>

                                <!-- Neutral Score -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-warning">
                                            <i class="fas fa-meh"></i> Neutral
                                        </span>
                                        <span class="badge bg-warning" id="neutralScore"></span>
                                    </div>
                                    <div class="progress" style="height: 15px;">
                                        <div id="neutralBar" class="progress-bar bg-warning" role="progressbar"></div>
                                    </div>
                                </div>

                                <!-- Negative Score -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-danger">
                                            <i class="fas fa-frown"></i> Negative
                                        </span>
                                        <span class="badge bg-danger" id="negativeScore"></span>
                                    </div>
                                    <div class="progress" style="height: 15px;">
                                        <div id="negativeBar" class="progress-bar bg-danger" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Entity Analysis -->
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="mb-3">
                                <i class="fas fa-tags"></i> Entity Recognition
                            </h4>
                            <div id="analyzedText" class="mb-3 p-3 border rounded"></div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Entity</th>
                                            <th>Sentiment</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody id="entityResults"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --header-bg: #f8f9fa;
    --text-dark: #495057;
}

.progress {
    background-color: rgba(0,0,0,0.05);
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.score-container {
    max-width: 700px;
    margin: 0 auto;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
}

.card-header {
    background-color: var(--header-bg);
    color: var(--text-dark);
    border-bottom: 2px solid #e9ecef;
    border-radius: 11px 11px 0 0 !important;
}

textarea.form-control {
    border-radius: 10px;
    border: 2px solid #dee2e6;
    transition: border-color 0.3s ease;
}

textarea.form-control:focus {
    border-color: #0d6efd;
    box-shadow: none;
}

.btn {
    border-radius: 10px;
    padding: 8px 20px;
}

.badge {
    font-size: 0.9rem;
    padding: 8px 12px;
}

#analyzedText {
    font-size: 1.1rem;
    line-height: 1.6;
}
</style>

@endsection

@section('scripts')
<script>
function analyzeSentiment(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);

    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
    submitButton.disabled = true;

    fetch('{{ route("analyze") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        const results = document.getElementById('results');
        const analyzedText = document.getElementById('analyzedText');
        results.style.display = 'block';
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Update overall sentiment
        const overallSentiment = document.getElementById('overallSentiment');
        const sentimentEmoji = document.getElementById('sentimentEmoji');
        const overallSentimentBadge = document.getElementById('overallSentimentBadge');
        
        let sentimentText = '';
        let badgeClass = '';
        
        switch(data.analysis.overall_sentiment) {
            case 'pos':
                sentimentText = 'Positive';
                badgeClass = 'bg-success';
                break;
            case 'neg':
                sentimentText = 'Negative';
                badgeClass = 'bg-danger';
                break;
            default:
                sentimentText = 'Neutral';
                badgeClass = 'bg-warning';
        }
        
        overallSentiment.textContent = sentimentText;
        sentimentEmoji.textContent = data.emoji;
        overallSentimentBadge.className = `d-inline-block rounded-pill px-4 py-2 text-white fs-5 ${badgeClass}`;

        // Update progress bars
        updateScoreBar('positive', data.analysis.positive_score);
        updateScoreBar('neutral', data.analysis.neutral_score);
        updateScoreBar('negative', data.analysis.negative_score);

        // Display analyzed text with entities
        analyzedText.textContent = data.analysis.text;

        // Update entity analysis
        const entityResults = document.getElementById('entityResults');
        if (data.analysis.entity_analysis && Object.keys(data.analysis.entity_analysis).length > 0) {
            entityResults.innerHTML = Object.entries(data.analysis.entity_analysis)
                .map(([entity, analysis]) => `
                    <tr>
                        <td>${entity}</td>
                        <td>
                            <span class="badge ${getBadgeClass(analysis.sentiment)}">
                                ${getSentimentText(analysis.sentiment)} ${analysis.emoji}
                            </span>
                        </td>
                        <td>${(Math.max(analysis.scores.pos, analysis.scores.neu, analysis.scores.neg) * 100).toFixed(1)}%</td>
                    </tr>
                `).join('');
        } else {
            entityResults.innerHTML = '<tr><td colspan="3" class="text-center">No entities found</td></tr>';
        }
    })
    .catch(error => {
        alert('Error analyzing text. Please try again.');
    })
    .finally(() => {
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    });
}

function updateScoreBar(type, score) {
    const bar = document.getElementById(`${type}Bar`);
    const scoreElement = document.getElementById(`${type}Score`);
    
    bar.style.width = `${score}%`;
    bar.setAttribute('aria-valuenow', score);
    scoreElement.textContent = `${score.toFixed(1)}%`;
}

function getBadgeClass(sentiment) {
    return sentiment === 'pos' ? 'bg-success' : 
           sentiment === 'neg' ? 'bg-danger' : 'bg-warning';
}

function getSentimentText(sentiment) {
    return sentiment === 'pos' ? 'Positive' :
           sentiment === 'neg' ? 'Negative' : 'Neutral';
}

function clearForm() {
    document.getElementById('analysisForm').reset();
    document.getElementById('results').style.display = 'none';
}
</script>
@endsection