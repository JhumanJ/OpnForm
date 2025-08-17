<?php

/**
 * Temporary script to test efficient backend search strategies for form submissions
 * Usage: php test_search.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Forms\Form;
use App\Models\Forms\FormSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class SearchTester
{
    private $formSlug = '4a985789-c895-45c7-916e-3c2ef13def38';
    private $form;
    private $searchableFields = [];

    public function __construct()
    {
        $this->form = Form::where('slug', $this->formSlug)->first();
        
        if (!$this->form) {
            echo "❌ Form with slug '{$this->formSlug}' not found!\n";
            exit(1);
        }

        echo "✅ Found form: {$this->form->title}\n";
        echo "📊 Total submissions: {$this->form->submissions()->count()}\n\n";

        $this->analyzeFormStructure();
    }

    private function analyzeFormStructure()
    {
        echo "🔍 Analyzing form structure...\n";
        
        if (!$this->form->properties) {
            echo "❌ No form properties found!\n";
            return;
        }

        // Extract searchable fields from form properties
        foreach ($this->form->properties as $property) {
            if (isset($property['type']) && $this->isSearchableFieldType($property['type'])) {
                $this->searchableFields[] = [
                    'id' => $property['id'] ?? 'unknown',
                    'name' => $property['name'] ?? 'Unnamed Field',
                    'type' => $property['type'],
                ];
            }
        }

        echo "📝 Searchable fields found:\n";
        foreach ($this->searchableFields as $field) {
            echo "   - {$field['name']} ({$field['id']}) [{$field['type']}]\n";
        }
        echo "\n";
    }

    private function isSearchableFieldType($type)
    {
        return in_array($type, [
            'text', 'email', 'textarea', 'rich_text', 'url', 'phone_number'
        ]);
    }

    public function testSearchStrategies($searchTerm = 'test')
    {
        echo "🚀 Testing search strategies for term: '{$searchTerm}'\n";
        echo str_repeat("=", 60) . "\n\n";

        // Strategy 1: Basic JSON LIKE search (current approach)
        $this->testBasicJsonSearch($searchTerm);

        // Strategy 2: Field-specific JSON path search
        $this->testFieldSpecificSearch($searchTerm);

        // Strategy 3: Combined approach
        $this->testCombinedSearch($searchTerm);

        // Strategy 4: Full-text simulation (if we had indexed searchable content)
        $this->testFullTextSimulation($searchTerm);
    }

    private function testBasicJsonSearch($searchTerm)
    {
        echo "1️⃣ Basic JSON LIKE Search\n";
        echo "Strategy: WHERE data LIKE '%term%'\n";
        
        $startTime = microtime(true);
        
        $results = $this->form->submissions()
            ->where('data', 'LIKE', "%{$searchTerm}%")
            ->get();
            
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        echo "Results: {$results->count()} submissions\n";
        echo "Execution time: " . number_format($executionTime, 2) . " ms\n";
        
        if ($results->count() > 0) {
            echo "Sample result:\n";
            $sample = $results->first();
            echo "  - ID: {$sample->id}\n";
            echo "  - Created: {$sample->created_at}\n";
            echo "  - Data snippet: " . Str::limit(json_encode($sample->data), 100) . "\n";
        }
        echo "\n";
    }

    private function testFieldSpecificSearch($searchTerm)
    {
        echo "2️⃣ Field-Specific JSON Path Search\n";
        echo "Strategy: WHERE JSON_EXTRACT(data, '$.field_id') LIKE '%term%'\n";
        
        if (empty($this->searchableFields)) {
            echo "⚠️ No searchable fields found, skipping...\n\n";
            return;
        }

        $startTime = microtime(true);
        
        $query = $this->form->submissions();
        $query->where(function($q) use ($searchTerm) {
            foreach ($this->searchableFields as $field) {
                $q->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.{$field['id']}'))"), 'LIKE', "%{$searchTerm}%");
            }
        });
        
        $results = $query->get();
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        echo "Searched fields: " . implode(', ', array_column($this->searchableFields, 'id')) . "\n";
        echo "Results: {$results->count()} submissions\n";
        echo "Execution time: " . number_format($executionTime, 2) . " ms\n";
        
        if ($results->count() > 0) {
            echo "Sample result:\n";
            $sample = $results->first();
            echo "  - ID: {$sample->id}\n";
            echo "  - Matching data: ";
            foreach ($this->searchableFields as $field) {
                $value = $sample->data[$field['id']] ?? null;
                if ($value && stripos($value, $searchTerm) !== false) {
                    echo "{$field['name']}: '{$value}' ";
                }
            }
            echo "\n";
        }
        echo "\n";
    }

    private function testCombinedSearch($searchTerm)
    {
        echo "3️⃣ Combined Search (Field-specific + Fallback)\n";
        echo "Strategy: Field-specific OR full JSON search\n";
        
        $startTime = microtime(true);
        
        $query = $this->form->submissions();
        $query->where(function($q) use ($searchTerm) {
            // First, try field-specific search
            if (!empty($this->searchableFields)) {
                foreach ($this->searchableFields as $field) {
                    $q->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.{$field['id']}'))"), 'LIKE', "%{$searchTerm}%");
                }
            }
            // Fallback to full JSON search for edge cases
            $q->orWhere('data', 'LIKE', "%{$searchTerm}%");
        });
        
        $results = $query->get();
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        echo "Results: {$results->count()} submissions\n";
        echo "Execution time: " . number_format($executionTime, 2) . " ms\n\n";
    }

    private function testFullTextSimulation($searchTerm)
    {
        echo "4️⃣ Full-Text Search Simulation\n";
        echo "Strategy: Simulate what full-text search would look like\n";
        
        $startTime = microtime(true);
        
        // Get all submissions and simulate full-text search in PHP
        $submissions = $this->form->submissions()->get();
        $results = collect();
        
        foreach ($submissions as $submission) {
            $searchableContent = $this->extractSearchableContent($submission->data);
            if (stripos($searchableContent, $searchTerm) !== false) {
                $results->push($submission);
            }
        }
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;
        
        echo "Simulated searchable content extraction from all submissions\n";
        echo "Results: {$results->count()} submissions\n";
        echo "Execution time: " . number_format($executionTime, 2) . " ms\n";
        echo "⚠️ Note: This would be much faster with actual full-text indexes\n\n";
    }

    private function extractSearchableContent($data)
    {
        $content = [];
        
        foreach ($this->searchableFields as $field) {
            if (isset($data[$field['id']]) && $data[$field['id']]) {
                $content[] = $data[$field['id']];
            }
        }
        
        return implode(' ', $content);
    }

    public function analyzePerformance()
    {
        echo "📈 Performance Analysis\n";
        echo str_repeat("=", 60) . "\n";
        
        $totalSubmissions = $this->form->submissions()->count();
        echo "Total submissions: {$totalSubmissions}\n";
        
        if ($totalSubmissions > 1000) {
            echo "⚠️ Large dataset detected! Search performance recommendations:\n";
            echo "  1. Add JSON path indexes for frequently searched fields\n";
            echo "  2. Consider generated columns for searchable content\n";
            echo "  3. Implement full-text search with dedicated search service\n";
            echo "  4. Use pagination with LIMIT/OFFSET optimization\n";
        } elseif ($totalSubmissions > 100) {
            echo "✅ Medium dataset. Current JSON search should work well.\n";
            echo "💡 Consider field-specific search for better performance.\n";
        } else {
            echo "✅ Small dataset. Any search strategy will work efficiently.\n";
        }
        
        echo "\n";
    }

    public function suggestOptimizations()
    {
        echo "💡 Optimization Suggestions\n";
        echo str_repeat("=", 60) . "\n";
        
        echo "1. Database Indexes:\n";
        if (!empty($this->searchableFields)) {
            foreach ($this->searchableFields as $field) {
                echo "   CREATE INDEX idx_form_{$this->form->id}_{$field['id']} ON form_submissions ((JSON_UNQUOTE(JSON_EXTRACT(data, '$.{$field['id']}'))));\\n";
            }
        }
        
        echo "\n2. Generated Column Approach:\n";
        echo "   ALTER TABLE form_submissions ADD COLUMN searchable_content TEXT GENERATED ALWAYS AS (\n";
        echo "     CONCAT_WS(' ', \n";
        foreach ($this->searchableFields as $i => $field) {
            $comma = $i < count($this->searchableFields) - 1 ? ',' : '';
            echo "       JSON_UNQUOTE(JSON_EXTRACT(data, '$.{$field['id']}'))){$comma}\n";
        }
        echo "     )\n";
        echo "   ) STORED;\n";
        echo "   CREATE FULLTEXT INDEX ft_searchable_content ON form_submissions(searchable_content);\n";
        
        echo "\n3. Application-Level Optimizations:\n";
        echo "   - Cache frequently searched terms\n";
        echo "   - Implement search result pagination\n";
        echo "   - Add search suggestions based on form fields\n";
        echo "   - Use debounced search input (300-500ms)\n";
        
        echo "\n";
    }
}

// Run the test
try {
    $tester = new SearchTester();
    $tester->analyzePerformance();
    
    // Test with different search terms
    $searchTerms = ['test', 'john', 'email', '@'];
    
    foreach ($searchTerms as $term) {
        $tester->testSearchStrategies($term);
        echo str_repeat("-", 60) . "\n";
    }
    
    $tester->suggestOptimizations();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "✅ Search testing completed!\n";