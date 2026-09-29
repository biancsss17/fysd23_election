<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\AuditLog;
use App\Models\CandidateSubmission;
use App\Models\ElectionVote;
use App\Models\ElectionPosition;
use App\Models\RegisteredVoter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['email' => strtolower((string) env('ADMIN_EMAIL', 'admin@district23fys.org'))],
            [
                'name' => 'Election Administrator',
                'password' => Hash::make((string) env('ADMIN_PASSWORD', 'Admin@12345!')),
                'is_active' => true,
            ],
        );

        foreach ([
            'President',
            'Vice President',
            'Secretary',
            'Assist Sec',
            'Treasurer',
            'Auditor',
        ] as $index => $name) {
            ElectionPosition::firstOrCreate(
                ['name' => $name],
                [
                    'sort_order' => $index + 1,
                    'seats' => 1,
                    'rule' => 'single',
                    'allow_abstain' => true,
                    'max_selections' => 1,
                    'is_completed' => false,
                    'is_unlocked' => false,
                    'is_closed' => false,
                    'candidacy_open' => false,
                    'nomination_open' => false,
                ],
            );
        }

        $encodedSnapshot = (string) env('ELECTION_SNAPSHOT_B64', '');
        if ($encodedSnapshot === '') {
            return;
        }

        $snapshot = json_decode(base64_decode($encodedSnapshot, true) ?: '', true);
        if (! is_array($snapshot)) {
            return;
        }

        DB::transaction(function () use ($snapshot): void {
            ElectionVote::query()->delete();
            CandidateSubmission::query()->delete();
            ElectionPosition::query()->delete();
            RegisteredVoter::query()->delete();
            AuditLog::query()->delete();

            $positionIds = [];
            foreach ($snapshot['positions'] ?? [] as $storedPosition) {
                $oldId = $storedPosition['id'];
                unset($storedPosition['id']);
                $positionIds[$oldId] = ElectionPosition::query()->create($storedPosition)->id;
            }

            $candidateIds = [];
            foreach ($snapshot['candidates'] ?? [] as $storedCandidate) {
                $oldId = $storedCandidate['id'];
                $oldPositionId = $storedCandidate['position_id'];
                unset($storedCandidate['id'], $storedCandidate['position_id']);
                $storedCandidate['position_id'] = $positionIds[$oldPositionId] ?? null;
                $candidateIds[$oldId] = CandidateSubmission::query()->create($storedCandidate)->id;
            }

            foreach ($snapshot['voters'] ?? [] as $storedVoter) {
                RegisteredVoter::query()->create($storedVoter);
            }

            foreach ($snapshot['votes'] ?? [] as $storedVote) {
                $storedVote['position_id'] = $positionIds[$storedVote['position_id']] ?? null;
                $storedVote['candidate_submission_id'] = $storedVote['candidate_submission_id'] !== null
                    ? ($candidateIds[$storedVote['candidate_submission_id']] ?? null)
                    : null;
                ElectionVote::query()->create($storedVote);
            }

            foreach ($snapshot['audit'] ?? [] as $storedAudit) {
                unset($storedAudit['id']);
                AuditLog::query()->create($storedAudit);
            }
        });
    }
}
