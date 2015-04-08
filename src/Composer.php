<?php
/**
 * Created by PhpStorm.
 * User: Nikita Kotenko
 * Date: 26.11.2014
 * Time: 14:12
 */
namespace samsonphp\composer;


/**
 * Provide creating sorting list of composer packages
 * @package samson
 */
class Composer
{
    /** @var string Path to current web-application */
    private $systemPath;

    /** @var string  composer lock file name */
    private $lockFileName = 'composer.lock';

    /** @var array List of available vendors */
    private $vendorsList = array('samsonphp/', 'samsonos/', 'samsoncms/');

    /** @var string $ignoreKey */
    private $ignoreKey = 'samson_module_ignore';

    /** @var string $includeKey */
    private $includeKey;

    /** @var array List of ignored packages */
    private $ignorePackages = array('samsonos/php_core');

    /** @var array List of packages with its priority */
    private $packageRating = array();

    /** @var array  Packages list with require packages*/
    private $packagesList = array();

    /**
     *  Add available vendor
     * @param $vendor Available vendor
     * @return $this
     */
    public function vendor($vendor)
    {
        if (!in_array($vendor, $this->vendorsList)) {
            $this->vendorsList[] = $vendor.'/';
        }
        return $this;
    }


    /**
     * Set name of composer extra parameter to ignore package
     * @param $ignoreKey Name
     * @return $this
     */
    public function ignoreKey($ignoreKey)
    {
        $this->ignoreKey = $ignoreKey;
        return $this;
    }

    /**
     * Set name of composer extra parameter to include package
     * @param $includeKey Name
     * @return $this
     */
    public function includeKey($includeKey)
    {
        $this->includeKey = $includeKey;
        return $this;
    }

    /**
     *  Add ignored package
     * @param $vendor Ignored package
     * @return $this
     */
    public function ignorePackage($package)
    {
        if (!in_array($package, $this->ignorePackages)) {
            $this->ignorePackages[] = $package;
        }
        return $this;
    }

    /**
     * Create sorted packages list
     * @return array Packages list ('package name'=>'rating')
     */
    public function create( & $packages, $systemPath, $lockFileName = 'composer.lock')
    {
        // Composer.lock is always in the project root folder
        $path = $systemPath.$lockFileName;

        // Create list of relevant packages with there require packages
        $this->packagesFill($this->readFile($path));

        // Set packages rating
        foreach ($this->packagesList as $package => $list) {
            $this->ratingCount($package, 1);
        }

        // Sort packages rated
        arsort($this->packageRating);

		$packages = $this->packageRating;
    }

    /**
     * Create list of relevant packages
     * @param $packages Composer lock list of packages
     * @return array List of relevant packages
     */
    private function includeList($packages)
    {
        $includePackages = array();
        foreach ($packages as $package) {
            if (!$this->isIgnore($package)) {
                if ($this->isInclude($package)) {
                    $includePackages[] = $package['name'];
                }
            }
        }
        return $includePackages;
    }

    /**
     * Is package include
     * @param $package Composer package
     * @return bool - is package include
     */
    private function isInclude($package)
    {
        $include = true;
        if (sizeof($this->vendorsList)) {
            if (!isset($this->includeKey) || !isset($package['extra'][$this->includeKey])) {
                $packageName = $package['name'];
			    $vendorName = substr($packageName, 0, strpos($packageName,"/")+1);
                $include = in_array($vendorName, $this->vendorsList);
            }
        }
        return $include;
    }

    /**
     * Is package ignored
     * @param $package Composer package
     * @return bool - is package ignored
     */
    private function isIgnore($package)
    {
        $isIgnore = false;
        if (in_array($package['name'], $this->ignorePackages)) {
            $isIgnore = true;
        }
        if (isset($this->ignoreKey)&&(isset($package['extra'][$this->ignoreKey]))) {
            $isIgnore = true;
        }
        return $isIgnore;
    }

    /**
     * Recursive function that provide package priority count
     * @param $requirement Current package name
     * @param int $current Current rating
     * @param string $parent Parent package
     */
    private function ratingCount($requirement, $current = 1, $parent = '')
    {
        // Update package rating
        $this->packageRating[$requirement] = (isset($this->packageRating[$requirement]))?($this->packageRating[$requirement] + $current):$current;
        // Update package rating
        $current = $this->packageRating[$requirement];
        
        // Iterate requires package
        foreach ($this->packagesList[$requirement] as $subRequirement) {
            // Check if two package require each other
            if ($parent != $subRequirement) {
                // Update package rating
                $this->ratingCount($subRequirement, $current, $requirement);
            }
        }
    }

    /**
     * Fill list of relevant packages with there require packages
     * @param $packages Composer lock file object
     */
    private function packagesFill($packages)
    {
        // Get included packages list
        $includePackages = $this->includeList($packages);

        // Create list of relevant packages with there require packages
        foreach ($packages as $package) {
            $requirement = $package['name'];
            if (in_array($requirement, $includePackages)) {
                $this->packagesList[$requirement] = array();
                if (isset($package['require'])) {
                    $this->packagesList[$requirement] = array_intersect(array_keys($package['require']), $includePackages);
                }
            }
        }
    }

    private function readFile($path)
    {
        $packages = array();
        // If we have composer configuration file
        if (file_exists($path)) {
            // Read file into object
            $composerObject = json_decode(file_get_contents($path), true);

            // Gather all possible packages
            $packages = array_merge(
                array(),
                isset($composerObject['packages']) ? $composerObject['packages'] : array(),
                isset($composerObject['packages-dev']) ? $composerObject['packages-dev'] : array()
            );
        }
        return $packages;
    }

	/**
	 * Clear object parameters
	 */
	public function clear()
	{
		$this->vendorsList = array();
		$this->ignoreKey = null;
		$this->includeKey = null;
		$this->ignorePackages = array();
		$this->packageRating = array();
		$this->packagesList = array();
	}
}
