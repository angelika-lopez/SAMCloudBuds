<?php

namespace App\Http\Controllers;

use App\Models\SentimentAnalysis;
use Illuminate\Http\Request;
use Sentiment\Analyzer;

class SentimentAnalysisController extends Controller
{
    private $analyzer;
    
    public function __construct()
    {
        $this->analyzer = new Analyzer();
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $text = $request->input('text');
        
        // Preprocess text
        $processedText = $this->preprocessText($text);
        
        // Get initial sentiment scores
        $scores = $this->analyzer->getSentiment($processedText);
        
        // Process and adjust scores
        $adjustedScores = $this->processScores($scores, $text);
        
        // Extract and analyze entities
        $entities = $this->analyzeEntities($text);
        
        // Create analysis record
        $analysis = SentimentAnalysis::create([
            'text' => $text,
            'overall_sentiment' => $this->determineOverallSentiment($adjustedScores),
            'positive_score' => $adjustedScores['pos'] * 100,
            'neutral_score' => $adjustedScores['neu'] * 100,
            'negative_score' => $adjustedScores['neg'] * 100,
            'entity_analysis' => $entities
        ]);

        return response()->json([
            'analysis' => $analysis,
            'emoji' => $this->getEmoji($analysis->overall_sentiment)
        ]);
    }

    private function processScores($scores, $text)
    {
        // Apply initial neutral bias adjustment
        $scores = $this->adjustNeutralBias($scores);
        
        // Process intensifiers and negations
        $words = explode(' ', strtolower($text));
        $intensifiers = ['very', 'really', 'extremely', 'absolutely', 'completely'];
        $negators = ['not', "don't", 'never', 'no', 'neither'];

        foreach ($words as $i => $word) {
            // Handle intensifiers
            if (in_array($word, $intensifiers)) {
                $nextWord = $words[$i + 1] ?? '';
                if ($nextWord) {
                    $wordScore = $this->analyzer->getSentiment($nextWord);
                    if ($wordScore['pos'] > 0) {
                        $scores['pos'] += 0.2;
                        $scores['neu'] = max(0, $scores['neu'] - 0.1);
                    } elseif ($wordScore['neg'] > 0) {
                        $scores['neg'] += 0.2;
                        $scores['neu'] = max(0, $scores['neu'] - 0.1);
                    }
                }
            }

            // Handle negations
            if (in_array($word, $negators)) {
                $nextWord = $words[$i + 1] ?? '';
                if ($nextWord) {
                    $wordScore = $this->analyzer->getSentiment($nextWord);
                    if ($wordScore['pos'] > 0) {
                        $scores['neg'] += $wordScore['pos'];
                        $scores['pos'] = max(0, $scores['pos'] - $wordScore['pos']);
                    } elseif ($wordScore['neg'] > 0) {
                        $scores['pos'] += $wordScore['neg'];
                        $scores['neg'] = max(0, $scores['neg'] - $wordScore['neg']);
                    }
                }
            }
        }

        // Normalize scores
        $total = $scores['pos'] + $scores['neu'] + $scores['neg'];
        if ($total > 0) {
            $scores['pos'] /= $total;
            $scores['neu'] /= $total;
            $scores['neg'] /= $total;
        }

        return $scores;
    }

    private function preprocessText($text)
    {
        // Keep your existing preprocessText method
        return $text;
    }

    private function adjustNeutralBias($scores)
    {
        $neutralThreshold = 0.3;
        
        if ($scores['neu'] > $neutralThreshold) {
            $excess = $scores['neu'] - $neutralThreshold;
            $scores['neu'] = $neutralThreshold;
            
            $total = $scores['pos'] + $scores['neg'];
            if ($total > 0) {
                $scores['pos'] += ($scores['pos'] / $total) * $excess;
                $scores['neg'] += ($scores['neg'] / $total) * $excess;
            } else {
                $scores['pos'] += $excess / 2;
                $scores['neg'] += $excess / 2;
            }
        }
        
        return $scores;
    }

    private function determineOverallSentiment($scores)
    {
        $posWeight = 1.2;
        $negWeight = 1.2;
        
        $weightedPos = $scores['pos'] * $posWeight;
        $weightedNeg = $scores['neg'] * $negWeight;
        
        if ($weightedPos > $scores['neu'] && $weightedPos > $weightedNeg) {
            return 'pos';
        } elseif ($weightedNeg > $scores['neu'] && $weightedNeg > $weightedPos) {
            return 'neg';
        }
        return 'neu';
    }

    private function getEmoji($sentiment)
    {
        return match ($sentiment) {
            'pos' => '😊',
            'neu' => '😐',
            'neg' => '😞',
            default => '❓'
        };
    }

    private function analyzeEntities($text)
    {
        $words = preg_split('/\s+/', $text);
        $entities = [];
    
        foreach ($words as $word) {
            if (ctype_upper(substr($word, 0, 1)) && strlen($word) > 1) {
                $entityScores = $this->analyzer->getSentiment($word);
                $sentiment = $this->determineEntitySentiment($entityScores);
                
                // Only add entity if sentiment is positive or negative
                if ($sentiment !== 'neu') {
                    $entities[$word] = [
                        'sentiment' => $sentiment,
                        'scores' => $entityScores,
                        'emoji' => $this->getEmoji($sentiment)
                    ];
                }
            }
        }
    
        return $entities;
    }
    
    private function determineEntitySentiment($scores)
    {
        $posWeight = 1.2;
        $negWeight = 1.2;
        
        $weightedPos = $scores['pos'] * $posWeight;
        $weightedNeg = $scores['neg'] * $negWeight;
        
        if ($weightedPos > $weightedNeg) {
            return 'pos';
        } elseif ($weightedNeg > $weightedPos) {
            return 'neg';
        }
        return 'neu';
    }

    public function history()
    {
        $analyses = SentimentAnalysis::orderBy('created_at', 'desc')
            ->paginate(5); // Show 5 items per page
    
        return view('sentiment.history', compact('analyses'));
    }

    public function statistics()
    {
        $analyses = SentimentAnalysis::all();
        
        // Calculate total analyses
        $totalAnalyses = $analyses->count();
        
        // Calculate sentiment counts
        $sentimentCounts = [
            'pos' => $analyses->where('overall_sentiment', 'pos')->count(),
            'neu' => $analyses->where('overall_sentiment', 'neu')->count(),
            'neg' => $analyses->where('overall_sentiment', 'neg')->count(),
        ];

        // Calculate percentages
        $percentages = [
            'positive' => $totalAnalyses > 0 ? round(($sentimentCounts['pos'] / $totalAnalyses) * 100, 1) : 0,
            'neutral' => $totalAnalyses > 0 ? round(($sentimentCounts['neu'] / $totalAnalyses) * 100, 1) : 0,
            'negative' => $totalAnalyses > 0 ? round(($sentimentCounts['neg'] / $totalAnalyses) * 100, 1) : 0,
        ];

        // Calculate average scores
        $averageScores = [
            'positive' => $analyses->avg('positive_score'),
            'neutral' => $analyses->avg('neutral_score'),
            'negative' => $analyses->avg('negative_score'),
        ];

        // Get recent trend (last 5 analyses)
        $recentAnalyses = SentimentAnalysis::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->reverse();

        return view('sentiment.statistics', compact(
            'totalAnalyses',
            'sentimentCounts',
            'percentages',
            'averageScores',
            'recentAnalyses'
        ));
    }
}