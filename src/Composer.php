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
    private $vendorsList = array();

    /** @var string $ignoreKey */
    private $ignoreKey;

    /** @var string $includeKey */
    private $includeKey;

    /** @var array List of ignored packages */
    private $ignorePackages = array();

    /** @var array List of packages with its priority */
	private $packageRating = array();

    /** @var array  Packages list with require packages*/
    private $packagesList = array();

	private $packagesListResorted = array();

	private $packagesListExtra = array();

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
    public function create( & $packages, $systemPath, $parameters = array() )
    {
	    $class_vars = get_class_vars(get_class($this));

	    foreach ($class_vars as $name => $value) {
		    if (isset($parameters[$name])) {
			    $this->$name = $parameters[$name];
		    }
	    }
        // Composer.lock is always in the project root folder
        $path = $systemPath.$this->lockFileName;

        // Create list of relevant packages with there require packages
        $this->packagesFill($this->readFile($path));

	    $topList = array();

        // Set packages rating
        foreach ($this->packagesList as $package => $list) {
	        if (sizeof($list) == 0) {
		        $topList[$package]=300;
	        }
            $this->resort($package);
        }

	    foreach ($this->packagesList as $package => $list) {
		    $this->ratingCount($package);
	    }
	    arsort($this->packageRating);

        // Sort packages rated
	    foreach ($topList as $package => $rating) {
		    unset($this->packageRating[$package]);
	    }

	    $this->packageRating = array_merge($topList, $this->packageRating);

	    $packages = array();

	    foreach ($this->packageRating as $package => $rating) {
		    $packages[$package] = $this->packagesListExtra[$package];
	    }
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
	private function resort($requirement, $parent = array())
	{
		// Check if two package require each other
		if (!in_array($requirement, $parent) && (sizeof($this->packagesList[$requirement]))) {
			$parent[]=$requirement;
			// Iterate requires package
			foreach ($this->packagesList[$requirement] as $subRequirement) {
				if(!isset($this->packagesListResorted[$subRequirement])) $this->packagesListResorted[$subRequirement] = array();
				$this->packagesListResorted[$subRequirement][$requirement] = $requirement;
				if (isset($this->packagesListResorted[$requirement])) {
					$this->packagesListResorted[$subRequirement] = array_merge($this->packagesListResorted[$subRequirement], $this->packagesListResorted[$requirement]);
				}
				// Update package rating
				$this->resort($subRequirement,  $parent);
			}
		}
	}

	/**
	 * Recursive function that provide package priority count
	 * @param $requirement Current package name
	 * @param int $current Current rating
	 * @param string $parent Parent package
	 */
	private function ratingCount($package)
	{
		if (!isset($this->packageRating[$package])) {
			$this->packageRating[$package] = 0;
		}
		if (isset($this->packagesListResorted[$package])&&is_array($this->packagesListResorted[$package])) {
			foreach ( $this->packagesListResorted[ $package ] as $subPackage ) {
				if ( ( $package != $subPackage ) && ( ! isset( $this->packageRating[ $subPackage ] ) ) ) {
					$this->packageRating[ $subPackage ] = $this->packageRating[ $package ] - 1;
					$this->ratingCount( $subPackage );
				} else {
					if(($this->packageRating[ $subPackage ]>=$this->packageRating[ $package ])&&( $package != $subPackage )){
						$this->packageRating[ $package ] = $this->packageRating[ $package ] + 1;
						$this->ratingCount( $package );
					}
				}
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
	            $this->packagesListExtra[$requirement] = isset($package['extra'])?$package['extra']:array();
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
