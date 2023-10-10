<?php

namespace App\Enums;

enum EventType: string
{
    case LogIn = 'LOG_IN';
    case LogOut = 'LOG_OUT';
    case SelectInstitution = 'SELECT_INSTITUTION';
    case CreateObject = 'CREATE_OBJECT';
    case ModifyObject = 'MODIFY_OBJECT';
    case RemoveObject = 'REMOVE_OBJECT';
    case CompleteAssignment = 'COMPLETE_ASSIGNMENT'; // TODO: Look into whether could be replaced by EDIT_OBJECT:ASSIGNMENT
    case FinishProject = 'FINISH_PROJECT'; // TODO: Look into whether could be replaced by EDIT_OBJECT:PROJECT
    case ApproveAssignmentResult = 'APPROVE_ASSIGNMENT_RESULT'; // TODO: Look into whether could be replaced by EDIT_OBJECT:PROJECT
    case RejectAssignmentResult = 'REJECT_ASSIGNMENT_RESULT'; // TODO: Look into whether could be replaced by EDIT_OBJECT:PROJECT
    case RewindWorkflow = 'REWIND_WORKFLOW';
    case DispatchNotification = 'DISPATCH_NOTIFICATION';
    case DownloadProjectFile = 'DOWNLOAD_PROJECT_FILE';
    case ExportInstitutionUsers = 'EXPORT_INSTITUTION_USERS';
    case ExportProjectsReport = 'EXPORT_PROJECTS_REPORT';
    case ExportTranslationMemory = 'EXPORT_TRANSLATION_MEMORY';
    case ImportTranslationMemory = 'IMPORT_TRANSLATION_MEMORY';
    case SearchLogs = 'SEARCH_LOGS';
    case ExportLogs = 'EXPORT_LOGS';

    public static function values(): array
    {
        return array_column(EventType::cases(), 'value');
    }
}
