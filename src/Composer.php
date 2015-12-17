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

    /** @var array  Packages list with require packages*/
    private $packagesList = array();

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
    public function create(& $packages, $systemPath, $parameters = array())
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

        $resultList = $this->sort();

	    foreach ($resultList as $package => $rating) {
            $required = $this->getRequiredList($package);
		    $packages[$package] = $this->packagesListExtra[$package];
            $packages[$package]['required'] = $required;
            $packages[$package]['composerName'] =$package;
	    }
    }

    /**
     * Provide creating sorting list
     * @return array list of sorted packages
     */
    public function sort()
    {
        $list = array();
        foreach ($this->packagesList as $package => $requiredList) {
            if (!sizeof($list)||!isset($list[$package])) {
                $list[$package] = 1;
            }
            foreach ($requiredList as $requiredPackage) {
                if (isset($list[$requiredPackage])) {
                    $packagePos =  array_search($package, array_keys($list));
                    $requiredPackagePos = array_search($requiredPackage, array_keys($list));
                    if ($packagePos < $requiredPackagePos) {
                        unset($list[$requiredPackage]);
                        $list = $this->insertKeyBefore($list, $package, $requiredPackage);
                    }
                } else {
                    $list = $this->insertKeyBefore($list, $package, $requiredPackage);
                }

            }
        }
        //$this->checkSort($list);

        return $list;
    }

    /**
     *Check result of sorting
     * @param $list final list of packages
     *
     * @return bool result
     */
    public function checkSort($list)
    {
        $status = true;
        foreach ($this->packagesList as $package => $requiredList) {
            foreach ($requiredList as $requiredPackage) {
                if (isset($list[$requiredPackage])) {
                    $packagePos =  array_search($package, array_keys($list));
                    $requiredPackagePos = array_search($requiredPackage, array_keys($list));
                    if ($packagePos < $requiredPackagePos) {
                        trace('error pos - '.$packagePos.' < '.$requiredPackagePos);
                        $status = false;
                    }
                } else {
                    $status = false;
                    trace('error not isset!!!!! - '.$requiredPackage);
                }
            }
        }
        return $status;
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
     * Create list of of required packages
     * @param null $includeModule Dependent package
     * @param array $ignoreModules
     *
     * @return array required packages
     */
    private function getRequiredList($includeModule = null, $ignoreModules = array())
    {
        $list = isset($includeModule)?$this->packagesList[$includeModule]:$this->packagesList;
        $ignoreList = array();
        foreach ($ignoreModules as $module) {
            $ignoreList[] = $module;
            if (is_array($this->packagesList[$module])) {
                $ignoreList = array_merge($ignoreList, $this->packagesList[$module]);
            }
        }

        $result = array();
        foreach ($list as $k=>$v) {
            $module = is_array($v)?$k:$v;
            if (!in_array($module, $ignoreList)) {
                $result[] = $module;
                $moduleList = $this->getReqList($this->packagesList[$module]);
                $result = array_merge($result, $moduleList);
            }
        }
        return array_values(array_unique($result));
    }

    /**
     * Recursive function that get list of required packages
     * @param $list List of packages
     * @param array $result
     *
     * @return array required packages
     */
    private function getReqList($list, $result = array()) {
        $return = array();
        if (is_array($list)) {
            foreach ($list as $module) {
                if (!in_array($module, $result)) {
                    $getList = $this->getReqList($this->packagesList[$module], $return);
                    $return[] = $module;
                    $return = array_merge($return, $getList);
                }
            }
        }
        return $return;
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
		$this->packagesList = array();
	}

    /**
     * Insert a key after a specific key in an array.  If key doesn't exist, value is appended
     * to the end of the array.
     *
     * @param array $array
     * @param string $key
     * @param integer $newKey
     *
     * @return array
     */
    public function insertKeyAfter( array $list, $key, $newKey )
    {
        $keys = array_keys($list);
        $index = array_search( $key, $keys );
        $pos = false === $index ? count($list) : $index + 1;
        return array_merge(array_slice($list, 0, $pos), array($newKey=>1), array_slice($list, $pos));
    }

    /**
     * Insert a key before a specific key in an array.  If key doesn't exist, value is prepended
     * to the beginning of the array.
     *
     * @param array $array
     * @param string $key
     * @param integer $newKey
     *
     * @return array
     */
    public function insertKeyBefore(array $list, $key, $newKey)
    {
        $keys = array_keys($list);
        $pos = (int) array_search($key, $keys);
        return array_merge(array_slice($list, 0, $pos), array($newKey=>1), array_slice($list, $pos));
    }

}
