<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReviewerAssignment;
use App\Models\Assessment;
use Carbon\Carbon;

$count = 0;
$deleted = 0;
$merged = 0;

$assignments = ReviewerAssignment::all();
foreach($assignments as $a) {
    $oldDate = $a->assessment_date;
    $newDate = Carbon::parse($oldDate)->startOfMonth()->format('Y-m-d');
    
    if ($oldDate === $newDate) continue;

    // Check if there is already an assignment for this pair in the target normalized date
    $existing = ReviewerAssignment::where('reviewer_id', $a->reviewer_id)
        ->where('reviewee_id', $a->reviewee_id)
        ->where('assessment_date', $newDate)
        ->first();

    if ($existing) {
        // Move assessments from $a to $existing if $existing doesn't have them
        foreach ($a->assessments as $assessment) {
            $hasExisting = $existing->assessments()->where('indicator_id', $assessment->indicator_id)->exists();
            if (!$hasExisting) {
                $assessment->assignment_id = $existing->id;
                $assessment->save();
                $merged++;
            } else {
                $assessment->delete(); // Duplicate assessment for same indicator
            }
        }
        $a->delete();
        $deleted++;
        echo "Deleted duplicate assignment ID {$a->id} (merged into {$existing->id})\n";
    } else {
        $a->assessment_date = $newDate;
        $a->save();
        echo "Updated ID {$a->id}: $oldDate to $newDate\n";
        $count++;
    }
}
echo "Total updated: $count\n";
echo "Total deleted (duplicates): $deleted\n";
echo "Total assessments merged/cleaned: $merged\n";
