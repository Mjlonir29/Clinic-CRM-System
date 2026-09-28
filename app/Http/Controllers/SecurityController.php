<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DatabaseBackup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $auditLogs = $query->paginate(20)->withQueryString();
        $backups = DatabaseBackup::with('creator')->orderBy('created_at', 'desc')->get();

        return view('security.index', compact('auditLogs', 'backups'));
    }

    public function createBackup()
    {
        try {
            $dbName = config('database.connections.mysql.database', 'clinic_crm');
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $dbName;

            $sqlDump = "-- Clinic CRM System Database Backup\n";
            $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {
                if (!isset($tableObj->$tableKey)) {
                    $propVars = get_object_vars($tableObj);
                    $tableName = reset($propVars);
                } else {
                    $tableName = $tableObj->$tableKey;
                }

                $createTableRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (!empty($createTableRes)) {
                    $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                    $sqlDump .= $createTableRes[0]->{'Create Table'} . ";\n\n";
                }

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $values = array_map(function ($val) {
                        if (is_null($val)) return 'NULL';
                        return "'" . addslashes($val) . "'";
                    }, (array) $row);

                    $sqlDump .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sqlDump .= "\n";
            }

            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $filename = 'backup_clinic_crm_' . date('Y_m_d_His') . '.sql';
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $filePath = $backupDir . '/' . $filename;
            file_put_contents($filePath, $sqlDump);

            $fileSize = filesize($filePath);

            DatabaseBackup::create([
                'filename' => $filename,
                'file_path' => 'backups/' . $filename,
                'file_size_bytes' => $fileSize,
                'backup_type' => 'Manual 1-Click',
                'status' => 'Completed',
                'created_by' => auth()->id(),
            ]);

            AuditLog::record("Created database backup ({$filename}, " . round($fileSize / 1024, 2) . " KB)", "Security & Backups");

            return back()->with('success', "Database backup created successfully: {$filename}");
        } catch (\Exception $e) {
            return back()->with('error', "Backup generation failed: " . $e->getMessage());
        }
    }

    public function downloadBackup(DatabaseBackup $backup)
    {
        $fullPath = storage_path('app/' . $backup->file_path);
        if (!file_exists($fullPath)) {
            return back()->with('error', 'Backup file not found on disk.');
        }

        AuditLog::record("Downloaded database backup: {$backup->filename}", "Security & Backups");

        return response()->download($fullPath, $backup->filename);
    }

    public function destroyBackup(DatabaseBackup $backup)
    {
        $fullPath = storage_path('app/' . $backup->file_path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
        $backup->delete();

        AuditLog::record("Deleted database backup: {$backup->filename}", "Security & Backups");

        return back()->with('success', 'Backup deleted.');
    }
}
