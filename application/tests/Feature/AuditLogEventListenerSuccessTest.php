<?php

namespace Tests\Feature;

/**
 * Important notes:
 *  * These tests depend on RabbitMQ running and working.
 *  * These tests assume the audit-log-events queue is empty.
 *  ** Queue must be emptied if there are old messages in the way.
 */
class AuditLogEventListenerSuccessTest extends AuditLogEventListenerBaseTest
{
    public function test_finish_project_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::createFinishProjectExampleMessageBody());
    }

    public function test_rewind_workflow_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::createRewindWorkflowExampleMessageBody());
    }

    public function test_dispatch_notification_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forDispatchNotification());
    }

    public function test_download_project_file_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forDownloadProjectFile());
    }

    public function test_export_projects_report_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forExportProjectsReport());
    }

    public function test_import_translation_memory_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forImportTranslationMemory());
    }

    public function test_export_translation_memory_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forExportTranslationMemory());
    }

    public function test_search_logs_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forSearchLogs());
    }

    public function test_export_logs_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forExportLogs());
    }

    public function test_reject_assignment_result_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forRejectAssignmentResult());
    }

    public function test_approve_assignment_result_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forApproveAssignmentResult());
    }

    public function test_complete_assignment_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forCompleteAssignment());
    }

    public function test_log_out_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forLogOut());
    }

    public function test_log_in_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forLogIn());
    }

    public function test_export_institution_users_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forExportInstitutionUsers());
    }

    public function test_select_institution_event()
    {
        $this->assertEventConsumption(ExampleMessageCreators::forSelectInstitution());
    }
}
