<?php

/**
* RACKSPACE_CDN_REWRITER
*
* @since 0.0.1
*/

class RACKSPACE_CDN_REWRITER
{
	var $Website_url = null; // this website URL
	var $CDN_container_url = null; // rackspace CDN container URL
	var $CDN_username = null; // rackspace CDN username
	var $CDN_APIKEY = null; // rackspace CDN API KEY
	var $CDN_container = null; // rackspace CDN container name

	var $dirs = null; // directories to include in CDN rewriting
	var $excludes = array();// directories to exclude in CDN rewriting
	var $localurls = false; // use CDN on local urls
	var $SSL = false; // use CDN on HTTPS URLS
	var $AutoUploadMedia = false; // when media is uploaded to media library upload to rackspace CDN

    /**
	* constructor
	*
	* @since   0.0.1
	* @change  0.0.1
	*/

	function __construct($Website_url, $CDN_container_url,$CDN_APIKEY,$CDN_username,$CDN_container, $dirs, array $excludes, $localurls, $https) {
		$this->Website_url = $Website_url;
		$this->CDN_container_url = $CDN_container_url;
		$this->CDN_APIKEY = $CDN_APIKEY;
		$this->CDN_username = $CDN_username;
		$this->CDN_container = $CDN_container;
		$this->dirs	= $dirs;
		$this->excludes = $excludes;
		$this->localurls	= $localurls;
		$this->https = $https;
	}


    /**
    * exclude URLs
    *
    * @since   0.0.1
    * @change  0.0.1
    *
    * @param   string  $media_file  current media_file
    * @return  boolean 
    */

	protected function exclude_url(&$media_file) {
		// excludes
		foreach ($this->excludes as $exclude) {
			if (!!$exclude && stristr($media_file, $exclude) != false) {
				return true;
			}
		}
		return false;
	}


    /**
    * rewrite url
    *
    * @since   0.0.1
    * @change  0.0.1
    *
    * @param   string  $media_file  current media_file
    * @return  string  media_file url
    */

    protected function rewrite_url($media_file) {
		if ($this->exclude_url($media_file[0])) {
			return $media_file[0];
		}
		$Website_url = $this->Website_url;

        // check if not a localurls path
		if (!$this->localurls || strstr($media_file[0], $Website_url)) {
			return str_replace($Website_url, $this->CDN_container_url, $media_file[0]);
		}

		return $this->CDN_container_url . $media_file[0];
	}


    /**
    * List inclusion dirs
    *
    * @since   0.0.1
    * @change  0.0.1
    *
    * @return  string  list of directories to include in cdn rewriting
    */

	protected function List_inclusion_dirs() {
		$input = explode(',', $this->dirs);

        // default
		if ($this->dirs == '' || count($input) < 1) {
			return 'wp\-content/uploads';
		}

		return implode('|', array_map('quotemeta', array_map('trim', $input)));
	}


    /**
    * rewrite html
    *
    * @since   0.0.1
    * @change  0.0.1
    *
    * @param   string  $html  current raw HTML doc
    * @return  string  updated HTML doc with CDN links
    */

	public function rewrite($PageHTML) {
        // check if HTTPS and use CDN over HTTPS enabled
		if (!$this->https && isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') {
			return $html;
		}

        // List cdn inclusion dirs
		$dirs = $this->List_inclusion_dirs();
        $Website_url = quotemeta($this->Website_url);

		// start of build regex rule
		$regex = '#(?<=[(\"\'])';

        // check if local urls
        if ($this->localurls) {
            $regex .= '(?:'.$Website_url.')?';
        } else {
			$regex_rule .= $Website_url;
		}

        //  end of build regex rule
		$regex .= '/(?:((?:'.$dirs.')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';

        // pass each match to the cdn url rewriter
		$AlteredHTML = preg_replace_callback($regex, array(&$this, 'rewrite_url'), $PageHTML);

		return $AlteredHTML;
	}
}
