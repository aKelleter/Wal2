<?php
declare(strict_types=1);

namespace App\UI;
use App\I18n\I18n;

/**
 * Classe Template – moteur de template ultra-léger
 * Inspiré de PhpLib, modernisé pour PHP 8+
 */
final class Template
{
    /** @var string */
    private string $root;

    /** @var array<string, string> */
    private array $file = [];

    /** @var array<string, string> */
    private array $varkeys = [];

    /** @var array<string, string> */
    private array $varvals = [];

    /** @var string "remove"|"comment"|"keep" */
    private string $unknowns = 'remove';

    /** @var int */
    public int $debug = 0;

    /** @var bool */
    public bool $filename_comments = false;

    /** @var string */
    private string $unknown_regexp = 'loose';

    /** @var string */
    public string $halt_on_error = 'yes';

    /** @var string */
    public string $last_error = '';

    /** @var bool */
    private bool $enable_cache = false;

    /** @var string */
    private string $cache_dir = '/tmp';

    /** @var int */
    private int $cache_ttl = 300; // 5 minutes


    public function __construct(string $root = '.', string $unknowns = 'remove')
    {
        $this->setRoot($root);
        $this->setUnknowns($unknowns);
    }

    public function setRoot(string $root): bool
    {
        $root = rtrim($root, '/\\');
        if (!is_dir($root)) {
            $this->halt("setRoot: $root is not a directory.");
            return false;
        }
        $this->root = $root;
        return true;
    }

    public function setUnknowns(string $unknowns = 'remove'): void
    {
        $this->unknowns = $unknowns;
    }

    public function setFile(string|array $varname, string $filename = ''): bool
    {
        if (is_array($varname)) {
            foreach ($varname as $v => $f) {
                if (empty($f)) {
                    $this->halt("setFile: For varname $v filename is empty.");
                    return false;
                }
                $this->file[$v] = $this->filename($f);
            }
        } else {
            if (empty($filename)) {
                $this->halt("setFile: For varname $varname filename is empty.");
                return false;
            }
            $this->file[$varname] = $this->filename($filename);
        }
        return true;
    }

    public function setBlock(string $parent, string $varname, ?string $name = null): bool
    {
        if (!$this->loadFile($parent)) {
            $this->halt("setBlock: unable to load $parent.");
            return false;
        }
        $name ??= $varname;

        $str = $this->getVar($parent);
        $reg = "/[ \t]*<!--\s+BEGIN $varname\s+-->\s*?\n?(\s*.*?\n?)\s*<!--\s+END $varname\s+-->\s*?\n?/sm";
        preg_match_all($reg, $str, $m);
        if (!isset($m[1][0])) {
            $this->halt("setBlock: unable to set block $varname.");
            return false;
        }
        $str = preg_replace($reg, "{" . $name . "}", $str);
        $this->setVar($varname, $m[1][0]);
        $this->setVar($parent, $str);
        return true;
    }

    public function setVar(string|array $varname, string $value = "", bool $append = false): void
    {
        if (is_array($varname)) {
            foreach ($varname as $k => $v) {
                if (!empty($k)) {
                    $this->varkeys[$k] = "/" . $this->varname($k) . "/";
                    $this->varvals[$k] = $append && isset($this->varvals[$k]) ? $this->varvals[$k] . $v : $v;
                }
            }
        } else {
            if (!empty($varname)) {
                $this->varkeys[$varname] = "/" . $this->varname($varname) . "/";
                $this->varvals[$varname] = $append && isset($this->varvals[$varname]) ? $this->varvals[$varname] . $value : $value;
            }
        }
    }

    public function clearVar(string|array $varname): void
    {
        if (is_array($varname)) {
            foreach ($varname as $v) {
                $this->setVar($v, "");
            }
        } else {
            $this->setVar($varname, "");
        }
    }

    public function unsetVar(string|array $varname): void
    {
        if (is_array($varname)) {
            foreach ($varname as $v) {
                unset($this->varkeys[$v], $this->varvals[$v]);
            }
        } else {
            unset($this->varkeys[$varname], $this->varvals[$varname]);
        }
    }

    public function subst(string $varname): string|false
    {
        if (!$this->loadFile($varname)) {
            $this->halt("subst: unable to load $varname.");
            return false;
        }
        $varvals_quoted = [];
        foreach ($this->varvals as $k => $v) {
            $varvals_quoted[$k] = preg_replace(['/\\\\/', '/\$/'], ['\\\\\\\\', '\\\\$'], $v);
        }
        $str = $this->getVar($varname);
        $str = preg_replace($this->varkeys, $varvals_quoted, $str);
        return $str;
    }

    public function psubst(string $varname): false
    {
        print $this->subst($varname);
        return false;
    }

    public function parse(string $target, string|array $varname, bool $append = false): string
    {
        if (is_array($varname)) {
            foreach ($varname as $v) {
                $str = $this->subst($v);
                $this->setVar($target, $append ? $this->getVar($target) . $str : $str);
            }
        } else {
            $str = $this->subst($varname);
            $this->setVar($target, $append ? $this->getVar($target) . $str : $str);
        }
        return $this->getVar($target);
    }

    public function pparse(string $target, string|array $varname, bool $append = false): false
    {
        
        //-----------------------------------------------
        // Création du nom de fichier pour le cache
        $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $path = parse_url($url, PHP_URL_PATH); 

        // Supprimer le préfixe BASE_URL 
        $base = rtrim(BASE_URL, '/');
        if (str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        // Nettoyage et transformation
        $clean = trim($path, '/'); 
        $clean = $clean === '' ? 'home' : $clean;
        $cacheKey = str_replace(['/', '\\'], '_', $clean); 

        // Ajout de la langue courante
        $lang = I18n::getLang();
        $lang = preg_replace('/[^a-zA-Z0-9_]/', '', $lang); // sécurise le nom du fichier
        $cacheKey .= '_' . $lang;
        //-----------------------------------------------


        if ($this->enable_cache) {
            $cacheFile = $this->cache_dir . '/' . $cacheKey . '.html';

            if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $this->cache_ttl)) {
                readfile($cacheFile);
                return false;
            }
        }

        $output = $this->finish($this->parse($target, $varname, $append));
        
        if ($this->enable_cache) {
            file_put_contents($cacheFile, $output);
        }

        // Si le cache est activé on place un commentaire dans le code HTML
        if ($this->enable_cache) {
            $output = "<!-- rendered with cache key: $cacheKey -->\n" . $output;
        }

        echo $output;
        return false;
    }

    public function getVars(): array
    {
        $result = [];
        foreach ($this->varkeys as $k => $v) {
            $result[$k] = $this->getVar($k);
        }
        return $result;
    }

    public function getVar(string|array $varname): mixed
    {
        if (is_array($varname)) {
            $result = [];
            foreach ($varname as $v) {
                $result[$v] = $this->varvals[$v] ?? '';
            }
            return $result;
        }
        return $this->varvals[$varname] ?? '';
    }

    public function getUndefined(string $varname): array|false
    {
        if (!$this->loadFile($varname)) {
            $this->halt("getUndefined: unable to load $varname.");
            return false;
        }
        $regexp = ($this->unknown_regexp === "loose")
            ? "/{([^ \t\r\n}]+)}/"
            : "/{([_a-zA-Z]\\w+)}/";
        preg_match_all($regexp, $this->getVar($varname), $m);
        $m = $m[1];
        $result = [];
        foreach ($m as $v) {
            if (!isset($this->varkeys[$v])) {
                $result[$v] = $v;
            }
        }
        return $result ?: false;
    }

    public function finish(string $str): string
    {
        switch ($this->unknowns) {
            case "keep":
                break;
            case "remove":
                $regexp = ($this->unknown_regexp === "loose")
                    ? "/{([^ \t\r\n}]+)}/"
                    : "/{([_a-zA-Z]\\w+)}/";
                $str = preg_replace($regexp, "", $str);
                break;
            case "comment":
                $regexp = ($this->unknown_regexp === "loose")
                    ? "/{([^ \t\r\n}]+)}/"
                    : "/{([_a-zA-Z]\\w+)}/";
                $str = preg_replace($regexp, "<!-- Template variable \\1 undefined -->", $str);
                break;
        }
        return $str;
    }

    public function p(string $varname): void
    {
        print $this->finish($this->getVar($varname));
    }

    public function get(string $varname): string
    {
        return $this->finish($this->getVar($varname));
    }

    private function filename(string $filename): string
    {
        if (
            str_starts_with($filename, '/') ||
            str_starts_with($filename, '\\') ||
            (strlen($filename) > 2 && ($filename[1] === ':' && ($filename[2] === '\\' || $filename[2] === '/')))
        ) {
            return $filename;
        }
        $full = $this->root . '/' . $filename;
        if (!file_exists($full)) {
            $this->halt("filename: file $full does not exist.");
        }
        return $full;
    }

    private function varname(string $varname): string
    {
        return preg_quote("{" . $varname . "}");
    }

    private function loadFile(string $varname): bool
    {
        if (!isset($this->file[$varname])) {
            return true;
        }
        if (isset($this->varvals[$varname])) {
            return true;
        }
        $filename = $this->file[$varname];
        $str = @file_get_contents($filename);
        if ($str === false || $str === '') {
            $this->halt("loadFile: While loading $varname, $filename does not exist or is empty.");
            return false;
        }
        if ($this->filename_comments) {
            $str = "<!-- START FILE $filename -->\n$str<!-- END FILE $filename -->\n";
        }
        $this->setVar($varname, $str);
        return true;
    }

    private function halt(string $msg): void
    {
        $this->last_error = $msg;
        if ($this->halt_on_error !== "no") {
            $this->haltmsg($msg);
        }
        if ($this->halt_on_error === "yes") {
            die("<b>Halted.</b>");
        }
    }

    private function haltmsg(string $msg): void
    {
        printf("<b>Template Error:</b> %s<br>\n", htmlspecialchars($msg));
    }

    public function enableCache(bool $state = true): void
    {
        $this->enable_cache = $state;
    }

    public function setCacheDir(string $dir): void
    {
        $this->cache_dir = rtrim($dir, '/\\');
    }

    public function setCacheTTL(int $ttl): void
    {
        $this->cache_ttl = $ttl;
    }

    public function clearCache(): void
    {
        foreach (glob($this->cache_dir . '/*.html') as $file) {
            unlink($file);
        }
    }

}
