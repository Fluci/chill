<?php
/**
 * PHP version 5
 *
 * @category Util
 * @package  Chill
 * @author   Felice Serena <felice@serena-mueller.ch>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace Chill\Util;

/**
 * Autoloader to automatically load classes from Chill's library.
 *
 * Mainly copied from http://www.php-fig.org/psr/psr-4/examples/
 *
 * @category Util
 * @package  Chill
 */
class Autoloader
{
    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    private $prefixes = array();


    /**
     * Loads a class.
     * @param  string $class The fully-qualified class name.
     * @return void
     */
    public function __invoke($class)
    {
        $this->load($class);
    }

    /**
     * Adds a base directory for a namespace prefix.
     * @param string $namespace The namespace prefix.
     * @param string $dir       A base directory for class files in the namespace.
     * @return void
     */
    public function addNamespace($namespace, $dir)
    {
        $namespace = trim($namespace, '\\') . '\\';

        $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Initialize the namespace prefix array.
        if (isset($this->prefixes[$namespace]) === false) {
            $this->prefixes[$namespace] = array();
        }

        array_push($this->prefixes[$namespace], $dir);
        return $this;
    }

    /**
     * Deletes all stored namespaces.
     * @return self For vhaining.
     */
    public function flushNamespaces()
    {
        $this->prefixes = array();
        return $this;
    }

    /**
     * Loads the class file for a given class name.
     * @param string $class The fully-qualified class name.
     * @return mixed         The mapped file name on success, or boolean false on
     *                       failure.
     * @throws noException
     */
    public function load($class)
    {
        if (version_compare(PHP_VERSION, '6.0.0') >= 0) {
            assert($class[0] !== '\\', 'invalid leading backslash in: '.$class);
        }

        // The current namespace prefix.
        $prefix = $class;
        // Work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        while (false !== ($pos = strrpos($prefix, '\\'))) {
            // Retain the trailing namespace separator in the prefix.
            $prefix = substr($class, 0, $pos + 1);
            // The rest is the relative class name.
            $relativeClass = substr($class, $pos + 1);
            // Try to load a mapped file for the prefix and relative class.
            $mappedFile = $this->loadMappedFile($prefix, $relativeClass);
            if ($mappedFile !== false) {
                return $mappedFile;
            }

            // Remove the trailing namespace separator for the next iteration
            // of strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        // Never found a mapped file.
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     * @param string $prefix        The namespace prefix.
     * @param string $relativeClass The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relativeClass)
    {
        // Are there any base directories for this namespace prefix?
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        // Look through base directories for this namespace prefix.
        foreach ($this->prefixes[$prefix] as $baseDir) {
            // Replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            $file = $baseDir
            . str_replace('\\', '/', $relativeClass)
            . '.php';
            // If the mapped file exists, require it.
            if ($this->requireFile($file) === true) {
                // Yes, we're done
                return $file;
            }
        }

        // Never found it.
        return false;
    }

    /**
     * If a file exists, require it from the file system.
     * @param  string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        if (file_exists($file) === true) {
            include $file;
            return true;
        }

        return false;
    }

    /**
     * Register this autoloader php's environment.
     * @return void
     */
    public function register()
    {
        spl_autoload_register($this, 'load');
    }
}
