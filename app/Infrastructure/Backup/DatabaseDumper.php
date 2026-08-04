<?php

namespace App\Infrastructure\Backup;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class DatabaseDumper
{
    public function driverName(): string
    {
        return (string) config('database.default');
    }

    public function dumpToString(): string
    {
        $driver = $this->driverName();

        return match ($driver) {
            'sqlite' => $this->dumpSqlite(),
            'mysql', 'mariadb' => $this->dumpMysql(),
            default => throw new RuntimeException("Yedekleme bu veritabanı sürücüsü için desteklenmiyor: {$driver}"),
        };
    }

    private function dumpSqlite(): string
    {
        $pdo = DB::connection()->getPdo();
        $lines = [
            '-- KVKK 360 SQLite backup',
            'PRAGMA foreign_keys=OFF;',
            'BEGIN TRANSACTION;',
        ];

        $tables = $pdo->query(
            "SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        )->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($tables as $table) {
            $name = (string) $table['name'];
            $createSql = (string) ($table['sql'] ?? '');
            if ($createSql === '') {
                continue;
            }

            $lines[] = 'DROP TABLE IF EXISTS '.$this->quoteIdent($name).';';
            $lines[] = rtrim($createSql, ';').';';

            $rows = $pdo->query('SELECT * FROM '.$this->quoteIdent($name))->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $columns = array_map(fn ($c) => $this->quoteIdent((string) $c), array_keys($row));
                $values = array_map(fn ($v) => $this->quoteValue($v), array_values($row));
                $lines[] = 'INSERT INTO '.$this->quoteIdent($name)
                    .' ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).');';
            }
        }

        $lines[] = 'COMMIT;';
        $lines[] = 'PRAGMA foreign_keys=ON;';

        return implode("\n", $lines)."\n";
    }

    private function dumpMysql(): string
    {
        $binary = trim((string) shell_exec('command -v mysqldump 2>/dev/null'));
        if ($binary !== '') {
            try {
                return $this->dumpMysqlViaBinary($binary);
            } catch (Throwable) {
                // fall through to PHP dump
            }
        }

        return $this->dumpMysqlViaPhp();
    }

    private function dumpMysqlViaBinary(string $binary): string
    {
        $config = config('database.connections.mysql');
        $host = escapeshellarg((string) ($config['host'] ?? '127.0.0.1'));
        $port = escapeshellarg((string) ($config['port'] ?? '3306'));
        $user = escapeshellarg((string) ($config['username'] ?? ''));
        $database = escapeshellarg((string) ($config['database'] ?? ''));
        $password = (string) ($config['password'] ?? '');

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s --single-transaction --routines --triggers %s 2>&1',
            escapeshellcmd($binary),
            $host,
            $port,
            $user,
            $database,
        );

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open($command, $descriptors, $pipes, null, [
            'MYSQL_PWD' => $password,
        ]);

        if (! is_resource($process)) {
            throw new RuntimeException('mysqldump başlatılamadı.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);
        $code = proc_close($process);

        if ($code !== 0 || trim($stdout) === '') {
            throw new RuntimeException('mysqldump başarısız: '.$stderr);
        }

        return $stdout;
    }

    private function dumpMysqlViaPhp(): string
    {
        $pdo = DB::connection()->getPdo();
        $lines = [
            '-- KVKK 360 MySQL backup',
            'SET FOREIGN_KEY_CHECKS=0;',
        ];

        $tables = $pdo->query('SHOW FULL TABLES WHERE Table_type = \'BASE TABLE\'')->fetchAll(\PDO::FETCH_NUM);
        foreach ($tables as $tableRow) {
            $name = (string) $tableRow[0];
            $create = $pdo->query('SHOW CREATE TABLE `'.str_replace('`', '``', $name).'`')->fetch(\PDO::FETCH_ASSOC);
            $createSql = (string) ($create['Create Table'] ?? '');
            if ($createSql === '') {
                continue;
            }

            $lines[] = 'DROP TABLE IF EXISTS `'.str_replace('`', '``', $name).'`;';
            $lines[] = $createSql.';';

            $rows = $pdo->query('SELECT * FROM `'.str_replace('`', '``', $name).'`')->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $columns = array_map(
                    fn ($c) => '`'.str_replace('`', '``', (string) $c).'`',
                    array_keys($row)
                );
                $values = array_map(fn ($v) => $this->quoteValue($v), array_values($row));
                $lines[] = 'INSERT INTO `'.str_replace('`', '``', $name).'` ('
                    .implode(', ', $columns).') VALUES ('.implode(', ', $values).');';
            }
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n", $lines)."\n";
    }

    private function quoteIdent(string $name): string
    {
        return '"'.str_replace('"', '""', $name).'"';
    }

    private function quoteValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        $string = (string) $value;

        return "'".str_replace("'", "''", $string)."'";
    }
}
