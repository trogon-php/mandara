<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExtractDatabaseSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:extract {--output= : Output file path} {--format=markdown : Output format (markdown, sql, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract all database table schemas and save to file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $outputPath = $this->option('output') ?: 'database_schema.md';
        $format = $this->option('format');

        $this->info('Extracting database schema...');

        try {
            $schema = $this->extractSchema();
            
            switch ($format) {
                case 'sql':
                    $content = $this->generateSqlSchema($schema);
                    break;
                case 'json':
                    $content = $this->generateJsonSchema($schema);
                    break;
                case 'markdown':
                default:
                    $content = $this->generateMarkdownSchema($schema);
                    break;
            }

            file_put_contents($outputPath, $content);
            
            $this->info("Schema extracted successfully to: {$outputPath}");
            $this->info("Total tables: " . count($schema['tables']));
            
        } catch (\Exception $e) {
            $this->error('Error extracting schema: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Extract complete database schema
     */
    private function extractSchema()
    {
        $connection = DB::connection();
        $databaseName = $connection->getDatabaseName();
        
        // Get all tables
        $tables = $this->getAllTables();
        
        $schema = [
            'database' => $databaseName,
            'connection' => config('database.default'),
            'extracted_at' => now()->toISOString(),
            'tables' => []
        ];

        foreach ($tables as $table) {
            $tableSchema = $this->getTableSchema($table);
            $schema['tables'][$table] = $tableSchema;
        }

        return $schema;
    }

    /**
     * Get all tables in the database
     */
    private function getAllTables()
    {
        $connection = DB::connection();
        $databaseName = $connection->getDatabaseName();
        
        if ($connection->getDriverName() === 'mysql') {
            $tables = DB::select("SHOW TABLES");
            return array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables);
        } else {
            // For SQLite
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            return array_map(function($table) {
                return $table->name;
            }, $tables);
        }
    }

    /**
     * Get detailed schema for a specific table
     */
    private function getTableSchema($tableName)
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        
        $schema = [
            'name' => $tableName,
            'columns' => [],
            'indexes' => [],
            'foreign_keys' => [],
            'create_statement' => ''
        ];

        // Get columns
        if ($driver === 'mysql') {
            $columns = DB::select("DESCRIBE `{$tableName}`");
            $schema['columns'] = $this->formatMysqlColumns($columns);
            
            // Get indexes
            $indexes = DB::select("SHOW INDEX FROM `{$tableName}`");
            $schema['indexes'] = $this->formatMysqlIndexes($indexes);
            
            // Get foreign keys
            $foreignKeys = DB::select("
                SELECT 
                    kcu.CONSTRAINT_NAME,
                    kcu.COLUMN_NAME,
                    kcu.REFERENCED_TABLE_NAME,
                    kcu.REFERENCED_COLUMN_NAME,
                    rc.UPDATE_RULE,
                    rc.DELETE_RULE
                FROM information_schema.KEY_COLUMN_USAGE kcu
                LEFT JOIN information_schema.REFERENTIAL_CONSTRAINTS rc 
                    ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME 
                    AND kcu.TABLE_SCHEMA = rc.CONSTRAINT_SCHEMA
                WHERE kcu.TABLE_SCHEMA = ? 
                AND kcu.TABLE_NAME = ? 
                AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
            ", [$connection->getDatabaseName(), $tableName]);
            $schema['foreign_keys'] = $this->formatMysqlForeignKeys($foreignKeys);
            
            // Get CREATE statement
            $createStatement = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $schema['create_statement'] = $createStatement[0]->{'Create Table'} ?? '';
            
        } else {
            // SQLite
            $columns = DB::select("PRAGMA table_info({$tableName})");
            $schema['columns'] = $this->formatSqliteColumns($columns);
            
            // Get indexes
            $indexes = DB::select("PRAGMA index_list({$tableName})");
            $schema['indexes'] = $this->formatSqliteIndexes($indexes);
        }

        return $schema;
    }

    /**
     * Format MySQL columns
     */
    private function formatMysqlColumns($columns)
    {
        $formatted = [];
        foreach ($columns as $column) {
            $formatted[] = [
                'name' => $column->Field,
                'type' => $column->Type,
                'null' => $column->Null === 'YES',
                'key' => $column->Key,
                'default' => $column->Default,
                'extra' => $column->Extra
            ];
        }
        return $formatted;
    }

    /**
     * Format SQLite columns
     */
    private function formatSqliteColumns($columns)
    {
        $formatted = [];
        foreach ($columns as $column) {
            $formatted[] = [
                'name' => $column->name,
                'type' => $column->type,
                'null' => !$column->notnull,
                'key' => $column->pk ? 'PRI' : '',
                'default' => $column->dflt_value,
                'extra' => ''
            ];
        }
        return $formatted;
    }

    /**
     * Format MySQL indexes
     */
    private function formatMysqlIndexes($indexes)
    {
        $formatted = [];
        $grouped = [];
        
        foreach ($indexes as $index) {
            $keyName = $index->Key_name;
            if (!isset($grouped[$keyName])) {
                $grouped[$keyName] = [
                    'name' => $keyName,
                    'unique' => !$index->Non_unique,
                    'primary' => $index->Key_name === 'PRIMARY',
                    'columns' => []
                ];
            }
            $grouped[$keyName]['columns'][] = $index->Column_name;
        }
        
        return array_values($grouped);
    }

    /**
     * Format SQLite indexes
     */
    private function formatSqliteIndexes($indexes)
    {
        $formatted = [];
        foreach ($indexes as $index) {
            $formatted[] = [
                'name' => $index->name,
                'unique' => $index->unique,
                'primary' => false,
                'columns' => [] // Would need additional query to get columns
            ];
        }
        return $formatted;
    }

    /**
     * Format MySQL foreign keys
     */
    private function formatMysqlForeignKeys($foreignKeys)
    {
        $formatted = [];
        foreach ($foreignKeys as $fk) {
            $formatted[] = [
                'constraint_name' => $fk->CONSTRAINT_NAME,
                'column_name' => $fk->COLUMN_NAME,
                'referenced_table' => $fk->REFERENCED_TABLE_NAME,
                'referenced_column' => $fk->REFERENCED_COLUMN_NAME,
                'update_rule' => $fk->UPDATE_RULE,
                'delete_rule' => $fk->DELETE_RULE
            ];
        }
        return $formatted;
    }

    /**
     * Generate Markdown schema
     */
    private function generateMarkdownSchema($schema)
    {
        $content = "# Database Schema\n\n";
        $content .= "**Database:** {$schema['database']}\n";
        $content .= "**Connection:** {$schema['connection']}\n";
        $content .= "**Extracted:** {$schema['extracted_at']}\n\n";
        $content .= "---\n\n";

        foreach ($schema['tables'] as $tableName => $table) {
            $content .= "## Table: `{$tableName}`\n\n";
            
            // Columns
            $content .= "### Columns\n\n";
            $content .= "| Column | Type | Null | Key | Default | Extra |\n";
            $content .= "|--------|------|------|-----|---------|-------|\n";
            
            foreach ($table['columns'] as $column) {
                $content .= "| `{$column['name']}` | {$column['type']} | " . 
                           ($column['null'] ? 'YES' : 'NO') . " | {$column['key']} | " . 
                           ($column['default'] ?? 'NULL') . " | {$column['extra']} |\n";
            }
            
            // Indexes
            if (!empty($table['indexes'])) {
                $content .= "\n### Indexes\n\n";
                foreach ($table['indexes'] as $index) {
                    $type = $index['primary'] ? 'PRIMARY KEY' : ($index['unique'] ? 'UNIQUE' : 'INDEX');
                    $content .= "- **{$index['name']}** ({$type}): " . implode(', ', $index['columns']) . "\n";
                }
            }
            
            // Foreign Keys
            if (!empty($table['foreign_keys'])) {
                $content .= "\n### Foreign Keys\n\n";
                foreach ($table['foreign_keys'] as $fk) {
                    $content .= "- **{$fk['constraint_name']}**: `{$fk['column_name']}` → `{$fk['referenced_table']}.{$fk['referenced_column']}`\n";
                }
            }
            
            // CREATE statement
            if (!empty($table['create_statement'])) {
                $content .= "\n### CREATE Statement\n\n";
                $content .= "```sql\n{$table['create_statement']}\n```\n";
            }
            
            $content .= "\n---\n\n";
        }

        return $content;
    }

    /**
     * Generate SQL schema
     */
    private function generateSqlSchema($schema)
    {
        $content = "-- Database Schema for {$schema['database']}\n";
        $content .= "-- Extracted: {$schema['extracted_at']}\n\n";
        
        foreach ($schema['tables'] as $tableName => $table) {
            if (!empty($table['create_statement'])) {
                $content .= "-- Table: {$tableName}\n";
                $content .= $table['create_statement'] . ";\n\n";
            }
        }
        
        return $content;
    }

    /**
     * Generate JSON schema
     */
    private function generateJsonSchema($schema)
    {
        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
