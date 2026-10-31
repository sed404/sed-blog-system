<?php
// Loaded only in an isolated temporary PHP server, with mysqli disabled (-n).
if (!defined('ADMIN_UI_FIXTURE')) { http_response_code(404); exit; }
if (extension_loaded('mysqli')) { throw new RuntimeException('Fixture must not connect to a database.'); }
class AdminFixtureResult {
    public int $num_rows;
    private int $position = 0;
    public function __construct(private array $rows = []) { $this->num_rows = count($rows); }
    public function fetch_assoc() { return $this->rows[$this->position++] ?? null; }
    public function fetch_array($mode = null) { return $this->fetch_assoc(); }
    public function fetch_all($mode = null) { return $this->rows; }
}
class AdminFixtureStatement {
    private array $values = [];
    public function __construct(private string $sql) {}
    public function bind_param($types, &...$values) { $this->values = $values; }
    public function execute() {}
    public function close() {}
    public function get_result() {
        if (str_contains($this->sql, 'INNER JOIN admin')) {
            $account = $this->values[0] ?? '';
            return new AdminFixtureResult(in_array($account, ['admin', 'banned'], true) ? [['passid'=>'fixture-token','ban'=>$account === 'banned' ? '1':'0','bantime'=>$account === 'banned' ? 'true':'false']] : []);
        }
        return (new mysqli())->query($this->sql);
    }
}
class mysqli {
    public string $connect_error = '';
    public int $connect_errno = 0;
    public string $error = '';
    public int $affected_rows = 0;
    public function __construct(...$args) { if (($_COOKIE['username'] ?? '') === 'db-error') throw new RuntimeException('Fixture outage'); }
    public function set_charset($charset) {}
    public function prepare($sql) { return new AdminFixtureStatement($sql); }
    public function close() {}
    public function real_escape_string($value) { return addslashes($value); }
    public function query($sql) {
        if (str_contains($sql, 'COUNT(') || str_contains($sql, 'count(')) return new AdminFixtureResult([['total'=>0,'AllNum'=>0]]);
        if (str_starts_with($sql, 'SHOW TABLES')) return new AdminFixtureResult([['table'=>'fixture']]);
        if (str_starts_with($sql, 'SHOW COLUMNS')) return new AdminFixtureResult(array_map(fn($key)=>['Field'=>$key], ['description','category','progress_current','progress_total','template_key','media_type','display_ratio','is_completed','current_time_seconds','total_episodes','my_progress','my_status','source_url','provider_id']));
        return new AdminFixtureResult();
    }
}
function mysqli_query($conn, $sql) { return $conn->query($sql); }
function mysqli_fetch_assoc($result) { return $result->fetch_assoc(); }
function mysqli_fetch_array($result) { return $result->fetch_assoc(); }
function mysqli_num_rows($result) { return $result->num_rows; }
define('MYSQLI_ASSOC', 1);
