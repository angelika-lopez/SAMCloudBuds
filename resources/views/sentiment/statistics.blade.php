@extends('layout')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2>Sentiment Analysis Statistics</h2>
                </div>
                <div class="card-body">
                    <h4>Total Analyses: {{ $totalAnalyses }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h4>Positive 😊</h4>
                    <h2>{{ $percentages['positive'] }}%</h2>
                    <p>Count: {{ $sentimentCounts['pos'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning">
                <div class="card-body">
                    <h4>Neutral 😐</h4>
                    <h2>{{ $percentages['neutral'] }}%</h2>
                    <p>Count: {{ $sentimentCounts['neu'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h4>Negative 😞</h4>
                    <h2>{{ $percentages['negative'] }}%</h2>
                    <p>Count: {{ $sentimentCounts['neg'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Sentiment Distribution</h4>
                </div>
                <div class="card-body" style="height: 300px;">
                    <canvas id="sentimentPieChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Average Scores</h4>
                </div>
                <div class="card-body" style="height: 300px;">
                    <canvas id="averageScoresChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Analysis Trend</h4>
                </div>
                <div class="card-body">
                    <canvas id="recentTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pie Chart
    new Chart(document.getElementById('sentimentPieChart'), {
        type: 'pie',
        data: {
            labels: ['Positive 😊', 'Neutral 😐', 'Negative 😞'],
            datasets: [{
                data: [
                    {{ $percentages['positive'] }},
                    {{ $percentages['neutral'] }},
                    {{ $percentages['negative'] }}
                ],
                backgroundColor: ['#198754', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 2,  // This controls the aspect ratio
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Average Scores Bar Chart
    new Chart(document.getElementById('averageScoresChart'), {
        type: 'bar',
        data: {
            labels: ['Positive', 'Neutral', 'Negative'],
            datasets: [{
                label: 'Average Scores',
                data: [
                    {{ $averageScores['positive'] }},
                    {{ $averageScores['neutral'] }},
                    {{ $averageScores['negative'] }}
                ],
                backgroundColor: ['#198754', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Recent Trend Line Chart
    new Chart(document.getElementById('recentTrendChart'), {
        type: 'line',
        data: {
            labels: [
                @foreach($recentAnalyses as $analysis)
                    '{{ $analysis->created_at->format("m/d H:i") }}',
                @endforeach
            ],
            datasets: [{
                label: 'Positive',
                data: [
                    @foreach($recentAnalyses as $analysis)
                        {{ $analysis->positive_score }},
                    @endforeach
                ],
                borderColor: '#198754',
                fill: false
            }, {
                label: 'Neutral',
                data: [
                    @foreach($recentAnalyses as $analysis)
                        {{ $analysis->neutral_score }},
                    @endforeach
                ],
                borderColor: '#ffc107',
                fill: false
            }, {
                label: 'Negative',
                data: [
                    @foreach($recentAnalyses as $analysis)
                        {{ $analysis->negative_score }},
                    @endforeach
                ],
                borderColor: '#dc3545',
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script>
@endsection