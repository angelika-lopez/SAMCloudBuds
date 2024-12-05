<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sentiment Analysis System</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
        }
        .welcome-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 90%;
        }
        .feature-list {
            text-align: left;
            margin: 2rem 0;
        }
        .btn-group-vertical > .btn {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-card">
            <h1 class="mb-4">Welcome to Sentiment Analysis System</h1>
            
            <div class="feature-list">
                <h4>Key Features:</h4>
                <ul>
                    <li>Real-time Sentiment Analysis</li>
                    <li>Historical Data Tracking</li>
                    <li>Statistical Insights</li>
                    <li>Emoji Visualization</li>
                    <li>Entity Recognition</li>
                </ul>
            </div>

            <div class="btn-group-vertical w-100" role="group">
                <a href="{{ route('analyze.form') }}" class="btn btn-primary btn-lg">
                    🔍 Start Analysis
                </a>
                <a href="{{ route('history') }}" class="btn btn-success btn-lg">
                    📊 View History
                </a>
                <a href="{{ route('statistics') }}" class="btn btn-info btn-lg">
                    📈 View Statistics
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>