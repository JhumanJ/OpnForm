<?php

use App\Models\Forms\Form;
use App\Models\Forms\FormSubmission;
use App\Service\Forms\FormExportService;
use App\Models\User;

it('includes status column when partial submissions are enabled and status column is requested', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);
    $form = createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    $submission = $form->submissions()->create([
        'data' => ['name_field' => 'John Doe'],
        'status' => FormSubmission::STATUS_COMPLETED
    ]);

    $exportService = new FormExportService();
    $result = $exportService->formatSubmissionForExport($form, $submission, [
        'name_field' => true,
        'status' => true
    ]);

    expect($result)->toHaveKey('status');
    expect($result['status'])->toBe('Completed');
});

it('shows "In Progress" for partial submissions', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);
    $form = createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    $submission = $form->submissions()->create([
        'data' => ['name_field' => 'Jane Smith'],
        'status' => FormSubmission::STATUS_PARTIAL
    ]);

    $exportService = new FormExportService();
    $result = $exportService->formatSubmissionForExport($form, $submission, [
        'name_field' => true,
        'status' => true
    ]);

    expect($result)->toHaveKey('status');
    expect($result['status'])->toBe('In Progress');
});

it('does not include status column when not requested', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);
    $form = createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    $submission = $form->submissions()->create([
        'data' => ['name_field' => 'John Doe'],
        'status' => FormSubmission::STATUS_COMPLETED
    ]);

    $exportService = new FormExportService();
    $result = $exportService->formatSubmissionForExport($form, $submission, [
        'name_field' => true
    ]);

    expect($result)->not->toHaveKey('status');
});

it('does not include status column when partial submissions are disabled', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);
    $form = createForm($user, $workspace, [
        'enable_partial_submissions' => false,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    $submission = $form->submissions()->create([
        'data' => ['name_field' => 'John Doe'],
        'status' => FormSubmission::STATUS_COMPLETED
    ]);

    $exportService = new FormExportService();
    $result = $exportService->formatSubmissionForExport($form, $submission, [
        'name_field' => true,
        'status' => true  // This should be ignored
    ]);

    expect($result)->not->toHaveKey('status');
});

it('applies status filter correctly for completed submissions', function () {
    $exportService = new FormExportService();
    
    // Mock query builder
    $mockQuery = new class {
        public $whereCalls = [];
        
        public function where($column, $operator, $value = null) {
            $this->whereCalls[] = [$column, $operator, $value];
            return $this;
        }
    };
    
    $exportService->applyStatusFilter($mockQuery, 'completed');
    
    expect($mockQuery->whereCalls)->toHaveCount(1);
    expect($mockQuery->whereCalls[0])->toBe(['status', FormSubmission::STATUS_COMPLETED, null]);
});

it('applies status filter correctly for partial submissions', function () {
    $exportService = new FormExportService();
    
    // Mock query builder
    $mockQuery = new class {
        public $whereCalls = [];
        
        public function where($column, $operator, $value = null) {
            $this->whereCalls[] = [$column, $operator, $value];
            return $this;
        }
    };
    
    $exportService->applyStatusFilter($mockQuery, 'partial');
    
    expect($mockQuery->whereCalls)->toHaveCount(1);
    expect($mockQuery->whereCalls[0])->toBe(['status', FormSubmission::STATUS_PARTIAL, null]);
});

it('does not apply status filter for all or null', function () {
    $exportService = new FormExportService();
    
    // Mock query builder
    $mockQuery = new class {
        public $whereCalls = [];
        
        public function where($column, $operator, $value = null) {
            $this->whereCalls[] = [$column, $operator, $value];
            return $this;
        }
    };
    
    $exportService->applyStatusFilter($mockQuery, 'all');
    expect($mockQuery->whereCalls)->toHaveCount(0);
    
    $exportService->applyStatusFilter($mockQuery, null);
    expect($mockQuery->whereCalls)->toHaveCount(0);
});

it('correctly determines async export threshold with status filtering', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);
    $form = createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Create submissions below threshold
    for ($i = 0; $i < 500; $i++) {
        $form->submissions()->create([
            'data' => ['name_field' => "User $i"],
            'status' => FormSubmission::STATUS_COMPLETED
        ]);
    }

    $exportService = new FormExportService();
    
    // Should not be async with small number
    expect($exportService->shouldExportAsync($form, 'completed'))->toBeFalse();
    expect($exportService->shouldExportAsync($form, 'all'))->toBeFalse();
});