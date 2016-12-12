<?php
/**
 * mainly copied from http://www.php-fig.org/psr/psr-4/examples/
 *
 * @category Util
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  MIT License
 */
namespace Chill\Util;

class Autoloader
{
    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    private $prefixes = array();

    function __invoke($class, $file_extensions = null)
    {
        $this->load($class, $file_extensions);
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param  string $prefix   The namespace prefix.
     * @param  string $base_dir A base directory for class files in the
     * namespace.
     * @param  bool   $prepend  If true, prepend the base directory to the stack instead of appending it; this causes it to be searched first rather than last. instead of appending it; this causes it to be searched first rather than last.
     * instead of appending it; this causes it to be searched first rather
     * than last.
     * @return void
     */
    public function addNamespace($namespace, $dir)
    {
        $namespace = trim($namespace, '\\') . '\\';
        $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        // initialize the namespace prefix array
        if (isset($this->prefixes[$namespace]) === false) {
            $this->prefixes[$namespace] = array();
        }
        array_push($this->prefixes[$namespace], $dir);
        return $this;
    }
    public function flushNamespaces()
    {
        $this->prefixes = array();
        return $this;
    }

    /**
     * Loads the class file for a given class name.
     *
     * @throws noException
     * @param  string $class The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function load($class, $file_extensions = null)
    {
    	if(version_compare(PHP_VERSION, '6.0.0') >= 0) {
        	assert($class[0] !== '\\', 'invalid leading backslash in: '.$class);
        }
        
        // the current namespace prefix
        $prefix = $class;
        // work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        while (false !== $pos = strrpos($prefix, '\\')) {

            // retain the trailing namespace separator in the prefix
            $prefix = substr($class, 0, $pos + 1);
            // the rest is the relative class name
            $relative_class = substr($class, $pos + 1);
            // try to load a mapped file for the prefix and relative class
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }
            // remove the trailing namespace separator for the next iteration
            // of strrpos()
            $prefix = rtrim($prefix, '\\');
        }
        // never found a mapped file
        return false;
    }
    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param  string $prefix         The namespace prefix.
     * @param  string $relative_class The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        // are there any base directories for this namespace prefix?
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }
    
        // look through base directories for this namespace prefix
        foreach ($this->prefixes[$prefix] as $base_dir) {
    
            // replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            $file = $base_dir
            . str_replace('\\', '/', $relative_class)
            . '.php';
            // if the mapped file exists, require it
            if ($this->requireFile($file)) {
                // yes, we're done
                return $file;
            }
        }
        
        // never found it
        return false;
    }
    
    /**
     * If a file exists, require it from the file system.
     *
     * @param  string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            include $file;
            return true;
        }
        return false;
    }
    public function register()
    {
        spl_autoload_register($this, 'load');
    }
}
