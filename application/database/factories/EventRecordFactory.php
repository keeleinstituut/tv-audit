<?php

namespace Database\Factories;

use App\Models\EventRecord;
use AuditLogClient\Enums\AuditLogEventFailureType;
use AuditLogClient\Enums\AuditLogEventObjectType;
use AuditLogClient\Enums\AuditLogEventType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EventRecord>
 */
class EventRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var AuditLogEventType $eventType */
        return [
            'happened_at' => fake()->dateTime(),
            'trace_id' => fake()->uuid(),
            'acting_institution_user_id' => fake()->uuid(),
            'context_institution_id' => fake()->uuid(),
            'context_department_id' => fake()->uuid(),
            'acting_user_pic' => $this->faker->estonianPIC(),
            'event_type' => fake()->randomElement(AuditLogEventType::values()),
            'event_parameters' => fn (array $attributes) => $this->generateEventParameters($attributes['event_type']),
            'failure_type' => fake()->optional(0.1)->randomElement(AuditLogEventFailureType::values()),
            'acting_user_forename' => fake()->firstName(),
            'acting_user_surname' => fake()->lastName(),
        ];
    }

    public function generateEventParameters(string $eventType): ?array
    {
        $eventType = AuditLogEventType::from($eventType);
        /** @var AuditLogEventObjectType $objectType */
        $objectType = fake()->randomElement(AuditLogEventObjectType::cases());

        return match ($eventType) {
            AuditLogEventType::LogIn,
            AuditLogEventType::LogOut,
            AuditLogEventType::SelectInstitution,
            AuditLogEventType::ExportInstitutionUsers => null,
            AuditLogEventType::CreateObject => [
                'object_type' => $objectType->value,
                'object_data' => [
                    'id' => fake()->unique()->uuid(),
                    'name' => fake()->name(),
                ],
            ],
            AuditLogEventType::ModifyObject => [
                'object_type' => $objectType->value,
                'object_identity_subset' => $this->buildIdentitySubsetForObject($objectType),
                'pre_modification_subset' => ['name' => fake()->name()],
                'post_modification_subset' => ['name' => fake()->name()],
            ],
            AuditLogEventType::RemoveObject => [
                'object_type' => $objectType->value,
                'object_identity_subset' => $this->buildIdentitySubsetForObject($objectType),
            ],
            AuditLogEventType::CompleteAssignment,
            AuditLogEventType::ApproveAssignmentResult,
            AuditLogEventType::RejectAssignmentResult => [
                'assignment_id' => fake()->unique()->uuid(),
                'assignment_ext_id' => fake()->unique()->regexify('[A-Z]{3}-\d{4}-\d{2}-[STK]-\d{3}-[A-Z]{4}-\d\/\d'),
            ],
            AuditLogEventType::FinishProject => [
                'project_id' => fake()->unique()->uuid(),
                'project_ext_id' => fake()->unique()->regexify('[A-Z]{3}-\d{4}-\d{2}-[STK]-\d{3}'),
            ],
            AuditLogEventType::RewindWorkflow => [
                'workflow_id' => fake()->uuid(),
                'workflow_name' => fake()->word(),
            ],
            AuditLogEventType::DispatchNotification => [
                'TODO' => null, // TODO!
            ],
            AuditLogEventType::DownloadProjectFile => [
                'media_id' => fake()->uuid(),
                'project_id' => fake()->uuid(),
                'project_ext_id' => fake()->regexify('[A-Z]{3}-\d{4}-\d{2}-[STK]-\d{3}'),
                'file_name' => fake()->word().'.'.fake()->fileExtension(),
            ],
            AuditLogEventType::ExportProjectsReport => [
                'query_start_date' => fake()->optional()->date(),
                'query_end_date' => fake()->optional()->date(),
                'query_status' => fake()->optional()->domainWord(),
            ],
            AuditLogEventType::ExportTranslationMemory,
            AuditLogEventType::ImportTranslationMemory => [
                'translation_memory_id' => fake()->uuid(),
                'translation_memory_name' => fake()->word(),
            ],
            AuditLogEventType::SearchLogs,
            AuditLogEventType::ExportLogs => [
                'query_start_datetime' => fake()->optional()->dateTime()?->format('c'),
                'query_end_datetime' => fake()->optional()->dateTime()?->format('c'),
                'query_event_type' => fake()->optional()->word(),
                'query_text' => fake()->optional()->word(),
                'query_department_id' => fake()->optional()->uuid(),
            ],
        };
    }

    private function buildIdentitySubsetForObject(AuditLogEventObjectType $objectType): ?array
    {
        return match ($objectType) {
            AuditLogEventObjectType::InstitutionUser => [
                'id' => fake()->uuid(),
                'user' => [
                    'id' => fake()->uuid(),
                    'personal_identification_code' => $this->faker->estonianPIC(),
                    'forename' => fake()->firstName(),
                    'surname' => fake()->lastName(),
                ],
            ],
            AuditLogEventObjectType::Role,
            AuditLogEventObjectType::Institution,
            AuditLogEventObjectType::TranslationMemory => [
                'id' => fake()->uuid(),
                'name' => fake()->colorName(),
            ],
            AuditLogEventObjectType::Vendor => [
                'id' => fake()->uuid(),
                'institution_user' => [
                    'id' => fake()->uuid(),
                    'user' => [
                        'id' => fake()->uuid(),
                        'personal_identification_code' => $this->faker->estonianPIC(),
                        'forename' => fake()->firstName(),
                        'surname' => fake()->lastName(),
                    ],
                ],
            ],
            AuditLogEventObjectType::InstitutionDiscount => null,
            AuditLogEventObjectType::Project,
            AuditLogEventObjectType::Subproject,
            AuditLogEventObjectType::Assignment => [
                'id' => fake()->uuid(),
                'ext_id' => Str::random(),
            ],
        };
    }
}
