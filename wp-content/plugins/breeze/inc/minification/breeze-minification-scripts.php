<?php
/* 
 *  Based on some work of autoptimize plugin 
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Breeze_MinificationScripts extends Breeze_MinificationBase {
	private $head_scripts = array();
	private $footer_scripts = array();
	private $dontmove = array('document.write','html5.js','show_ads.js','google_ad','blogcatalog.com/w','tweetmeme.com/i','mybloglog.com/','histats.com/js','ads.smowtion.com/ad.js','statcounter.com/counter/counter.js','widgets.amung.us','ws.amazon.com/widgets','media.fastclick.net','/ads/','comment-form-quicktags/quicktags.php','edToolbar','intensedebate.com','scripts.chitika.net/','_gaq.push','jotform.com/','admin-bar.min.js','GoogleAnalyticsObject','plupload.full.min.js','syntaxhighlighter','adsbygoogle','gist.github.com','_stq','nonce','post_id','data-noptimize');
	private $domove = array('gaJsHost','load_cmc','jd.gallery.transitions.js','swfobject.embedSWF(','tiny_mce.js','tinyMCEPreInit.go');
	private $domovelast = array('addthis.com','/afsonline/show_afs_search.js','disqus.js','networkedblogs.com/getnetworkwidget','infolinks.com/js/','jd.gallery.js.php','jd.gallery.transitions.js','swfobject.embedSWF(','linkwithin.com/widget.js','tiny_mce.js','tinyMCEPreInit.go');
	private $trycatch = false;
	private $alreadyminified = false;
	private $forcehead = true;
	private $include_inline = false;
	private $jscode = '';
	private $url = '';
	private $move = array('first' => array(), 'last' => array());
	private $restofcontent = '';
	private $md5hash = '';
	private $whitelist = '';
	private $jsremovables = array();
	private $inject_min_late = '';
	private $group_js = false;
	private $custom_js_exclude = array();
    private $js_head_group = array();
    private $js_footer_group = array();
    private $js_min_head = array();
    private $js_min_footer = array();
    private $url_group_head = array();
    private $url_group_footer = array();
    private $jscode_inline_head = array();
    private $jscode_inline_footer = array();
    private $move_to_footer_js = array();
    private $move_to_footer = array();
    private $defer_js = array();

	//Reads the page and collects script tags
	public function read($options) {
		$noptimizeJS = apply_filters( 'breeze_filter_js_noptimize', false, $this->content );
                if ($noptimizeJS) return false;

		// only optimize known good JS?
		$whitelistJS = apply_filters( 'breeze_filter_js_whitelist', "" );
		if (!empty($whitelistJS)) {
			$this->whitelist = array_filter(array_map('trim',explode(",",$whitelistJS)));
		}

		// is there JS we should simply remove
		$removableJS = apply_filters( 'breeze_filter_js_removables', '');
		if (!empty($removableJS)) {
			$this->jsremovables = array_filter(array_map('trim',explode(",",$removableJS)));
		}

		// only header?
		if( apply_filters('breeze_filter_js_justhead',$options['justhead']) == true ) {
			$content = explode('</head>',$this->content,2);
			$this->content = $content[0].'</head>';
			$this->restofcontent = $content[1];
		}
		
		// include inline?
		if( apply_filters('breeze_js_include_inline',$options['include_inline']) == true ) {
			$this->include_inline = true;
		}

        // group js?
        if( apply_filters('breeze_js_group_js',$options['group_js']) == true ) {
            $this->group_js = true;
        }

        //custom js exclude
        if(!empty($options['custom_js_exclude'])){
		    $this->custom_js_exclude = $options['custom_js_exclude'];
        }

        // JS files will move to footer
        if(!empty($options['move_to_footer_js'])){
		    $this->move_to_footer_js = $options['move_to_footer_js'];
        }

        // JS files will move to footer
        if(!empty($options['defer_js'])){
		    $this->defer_js = $options['defer_js'];
        }

		// filter to "late inject minified JS", default to true for now (it is faster)
		$this->inject_min_late = apply_filters('breeze_filter_js_inject_min_late',true);

		// filters to override hardcoded do(nt)move(last) array contents (array in, array out!)
		$this->dontmove = apply_filters( 'breeze_js_dontmove', $this->dontmove );
		$this->domovelast = apply_filters( 'breeze_filter_js_movelast', $this->domovelast );
		$this->domove = apply_filters( 'breeze_filter_js_domove', $this->domove );

		// get extra exclusions settings or filter
		$excludeJS = $options['js_exclude'];
		$excludeJS = apply_filters( 'breeze_filter_js_exclude', $excludeJS );
		if ($excludeJS!=="") {
			$exclJSArr = array_filter(array_map('trim',explode(",",$excludeJS)));
			$this->dontmove = array_merge($exclJSArr,$this->dontmove);
		}

		//Should we add try-catch?
		if($options['trycatch'] == true)
			$this->trycatch = true;

		// force js in head?	
		if($options['forcehead'] == true) {
			$this->forcehead = true;
		} else {
			$this->forcehead = false;
		}
	        $this->forcehead = apply_filters( 'breeze_filter_js_forcehead', $this->forcehead );

		// get cdn url
		$this->cdn_url = $options['cdn_url'];
			
		// noptimize me
		$this->content = $this->hide_noptimize($this->content);

		// Save IE hacks
		$this->content = $this->hide_iehacks($this->content);

		// comments
		$this->content = $this->hide_comments($this->content);

		//Get script files
        $exploded_content = explode('</head>', $this->content, 2);
        $this->getJS($exploded_content[0]);
        $this->getJS($exploded_content[1], false);

        if (!empty($this->head_scripts) || !empty($this->footer_scripts)) {
            // Re-order moving to footer JS files
            $ordered_moving_js = array_intersect_key($this->move_to_footer_js, $this->move_to_footer);
            $ordered_moving_js = array_map(array($this, 'getpath'), $ordered_moving_js);
            $this->footer_scripts = array_merge($ordered_moving_js, $this->footer_scripts);
            return true;
        }

		// No script files, great!
		return false;
	}

	//Get all JS in page
    private function getJS($content, $head = true) {
        if(preg_match_all('#<script.*</script>#Usmi',$content,$matches)) {
            foreach($matches[0] as $tag) {
                // only consider aggregation whitelisted in should_aggregate-function
                if( !$this->should_aggregate($tag) ) {
                    $tag='';
                    continue;
                }

                if(preg_match('/\ssrc=("|\')?(.*(\ |\>))("|\')?/Usmi',$tag,$source)) {
                    $source[2] = substr($source[2], 0, -1);
                    if ($this->isremovable($tag,$this->jsremovables)) {
                        $content = str_replace($tag,'',$content);
                        continue;
                    }

                    // External script
                    $url = current(explode('?',$source[2],2));
                    if ($url[0] == "'" || $url[0] == '"') {
                        $url = substr($url, 1);
                    }
                    if ($url[strlen($url) - 1] == '"' || $url[strlen($url) - 1] == "'") {
                        $url = substr($url, 0 , -1);
                    }

                    //exclude js
                    if(in_array($url,$this->custom_js_exclude)){
                        continue;
                    }

                    $path = $this->getpath($url);
                    if($path !== false && preg_match('#\.js$#',$path)) {
                        //Inline
                        if($this->ismergeable($tag)) {
                            //We can merge it
                            if ($head) {
                                // If this file will be move to footer
                                if (in_array($url, $this->move_to_footer_js)) {
                                    $this->move_to_footer[$url] = $path;
                                } else {
                                    $this->head_scripts[$url] = $path;
                                }
                            } else {
                                $this->footer_scripts[$url] = $path;
                            }
                        } else {
                            //No merge, but maybe we can move it
                            if($this->ismovable($tag)) {
                                //Yeah, move it
                                if($this->movetolast($tag)) {
                                    $this->move['last'][] = $tag;
                                } else {
                                    $this->move['first'][] = $tag;
                                }
                            } else {
                                //We shouldn't touch this
                                $tag = '';
                            }
                        }
                    } else {
                        //External script (example: google analytics)
                        //OR Script is dynamic (.php etc)
                        if($this->ismovable($tag)) {
                            if($this->movetolast($tag))	{
                                $this->move['last'][] = $tag;
                            } else {
                                $this->move['first'][] = $tag;
                            }
                        } else {
                            //We shouldn't touch this
                            $tag = '';
                        }
                    }
                } else {
                    // Inline script
                    if ($this->isremovable($tag,$this->jsremovables)) {
                        $content = str_replace($tag,'',$content);
                        continue;
                    }

                    // unhide comments, as javascript may be wrapped in comment-tags for old times' sake
                    $tag = $this->restore_comments($tag);
                    if($this->ismergeable($tag) && ( $this->include_inline )) {
                        preg_match('#<script.*>(.*)</script>#Usmi',$tag,$code);
                        $code = preg_replace('#.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*#sm','$1',$code[1]);
                        $code = preg_replace('/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/','',$code);

                        if ($head) {
                            $this->head_scripts[] = 'INLINE;'.$code;
                        } else {
                            $this->footer_scripts[] = 'INLINE;'.$code;
                        }
                    } else {
                        // Can we move this?
                        if($this->ismovable($tag)) {
                            if($this->movetolast($tag))	{
                                $this->move['last'][] = $tag;
                            } else {
                                $this->move['first'][] = $tag;
                            }
                        } else {
                            //We shouldn't touch this
                            $tag = '';
                        }
                    }
                    // re-hide comments to be able to do the removal based on tag from $this->content
                    $tag = $this->hide_comments($tag);
                }
                //Remove the original script tag
                $content = str_replace($tag,'',$content);
            }
        }

        if ($head) {
            $this->content = $content;
        } else {
            $this->content .= '</head>' . $content;
        }

        return true;
    }

    public function minify() {
        $this->runMinify($this->head_scripts);
        $this->runMinify($this->footer_scripts, false);

        return true;
    }

	//Joins and optimizes JS
	private function runMinify($scripts, $head = true) {
        foreach($scripts as $url => $script) {
			if(preg_match('#^INLINE;#',$script)) {
				//Inline script
				$script = preg_replace('#^INLINE;#','',$script);
				$script = rtrim( $script, ";\n\t\r" ) . ';';
				//Add try-catch?
				if($this->trycatch) {
					$script = 'try{'.$script.'}catch(e){}';
				}
				$tmpscript = apply_filters( 'breeze_js_individual_script', $script, "" );
				if ( has_filter('breeze_js_individual_script') && !empty($tmpscript) ) {
					$script=$tmpscript;
					$this->alreadyminified=true;
				}

                if ($head) {
                    $this->js_head_group[] = $script;
                } else {
                    $this->js_footer_group[] = $script;
                }
			} else {
                //External script
                if($script !== false && file_exists($script) && is_readable($script)) {
					$scriptsrc = file_get_contents($script);
					$scriptsrc = preg_replace('/\x{EF}\x{BB}\x{BF}/','',$scriptsrc);
					$scriptsrc = rtrim($scriptsrc,";\n\t\r").';';
					//Add try-catch?
					if($this->trycatch) {
						$scriptsrc = 'try{'.$scriptsrc.'}catch(e){}';
					}
					$tmpscriptsrc = apply_filters( 'breeze_js_individual_script', $scriptsrc, $script );
					if ( has_filter('breeze_js_individual_script') && !empty($tmpscriptsrc) ) {
						$scriptsrc=$tmpscriptsrc;
						$this->alreadyminified=true;
					} else if ((strpos($script,"min.js")!==false) && ($this->inject_min_late===true)) {
						$scriptsrc="%%INJECTLATER".breeze_HASH."%%".base64_encode($script)."|".md5($scriptsrc)."%%INJECTLATER%%";
					}
                    if($this->group_js == true){
                        $this->jscode .= "\n" . $scriptsrc;
                    }else{
                        if ($head) {
                            $this->js_head_group[$url] = $scriptsrc;
                        } else {
                            $this->js_footer_group[$url] = $scriptsrc;
                        }
                    }
				}/*else{
					//Couldn't read JS. Maybe getpath isn't working?
				}*/
			}
		}

		// Minify JS
        // When using group JS
        if (!$head && !empty($this->jscode)) {
            //Check for already-minified code
            $this->md5hash = md5($this->jscode);
            $ccheck = new Breeze_MinificationCache($this->md5hash,'js');
            if($ccheck->check()) {
                $this->jscode = $ccheck->retrieve();
                $this->alreadyminified = true;
            }
            unset($ccheck);

            //$this->jscode has all the uncompressed code now.

            if ($this->alreadyminified !== true) {
                if (class_exists('JSMin') && apply_filters( 'breeze_js_do_minify' , true)) {
                    if (@is_callable(array("JSMin","minify"))) {
                        $tmp_jscode = trim(JSMin::minify($this->jscode));
                        if (!empty($tmp_jscode)) {
                            $this->jscode = $tmp_jscode;
                            unset($tmp_jscode);
                        }
                    }
                }

                $this->jscode = $this->inject_minified($this->jscode);
            }

            // Get the inline JS and minify
            if (!empty($this->js_head_group) || !empty($this->js_footer_group)) {
                if (!empty($this->js_head_group)) {
                    foreach ($this->js_head_group as $jscode) {
                        //$this->jscode has all the uncompressed code now.
                        if (class_exists('JSMin') && apply_filters('breeze_js_do_minify', true)) {
                            if (@is_callable(array("JSMin", "minify"))) {
                                $tmp_jscode = trim(JSMin::minify($jscode));
                                if (!empty($tmp_jscode)) {
                                    $jscode = $tmp_jscode;
                                    unset($tmp_jscode);
                                }
                            }
                        }

                        $this->jscode_inline_head[] = $this->inject_minified($jscode);
                    }
                }

                if (!empty($this->js_footer_group)) {
                    foreach ($this->js_footer_group as $jscode) {
                        //$this->jscode has all the uncompressed code now.
                        if (class_exists('JSMin') && apply_filters('breeze_js_do_minify', true)) {
                            if (@is_callable(array("JSMin", "minify"))) {
                                $tmp_jscode = trim(JSMin::minify($jscode));
                                if (!empty($tmp_jscode)) {
                                    $jscode = $tmp_jscode;
                                    unset($tmp_jscode);
                                }
                            }
                        }

                        $this->jscode_inline_footer[] = $this->inject_minified($jscode);
                    }
                }
            }

            return true;
        }

        // Not using group JS
        if (!empty($this->js_head_group) || !empty($this->js_footer_group)) {
            if ($head && !empty($this->js_head_group)) {
                foreach ($this->js_head_group as $url => $jscode) {
                    //Check for already-minified code
                    $this->md5hash = md5($jscode);
                    $ccheck        = new Breeze_MinificationCache($this->md5hash, 'js');

                    if ($ccheck->check()) {
                        $js_exist                = $ccheck->retrieve();
                        $this->js_min_head[$url] = $this->md5hash . '_breezejsgroup_' . $js_exist;
                        continue;
                    }
                    unset($ccheck);

                    //$this->jscode has all the uncompressed code now.

                    if (class_exists('JSMin') && apply_filters('breeze_js_do_minify', true)) {
                        if (@is_callable(array("JSMin", "minify"))) {
                            $tmp_jscode = trim(JSMin::minify($jscode));
                            if (!empty($tmp_jscode)) {
                                $jscode = $tmp_jscode;
                                unset($tmp_jscode);
                            }
                        }
                    }

                    $jscode                  = $this->inject_minified($jscode);
                    $this->js_min_head[$url] = $this->md5hash . '_breezejsgroup_' . $jscode;
                }
            }

            if (!$head && !empty($this->js_footer_group)) {
                foreach ($this->js_footer_group as $url => $jscode) {
                    //Check for already-minified code
                    $this->md5hash = md5($jscode);
                    $ccheck        = new Breeze_MinificationCache($this->md5hash, 'js');
                    if ($ccheck->check()) {
                        $js_exist                  = $ccheck->retrieve();
                        $this->js_min_footer[$url] = $this->md5hash . '_breezejsgroup_' . $js_exist;
                        continue;
                    }
                    unset($ccheck);

                    //$this->jscode has all the uncompressed code now.

                    if (class_exists('JSMin') && apply_filters('breeze_js_do_minify', true)) {
                        if (@is_callable(array("JSMin", "minify"))) {
                            $tmp_jscode = trim(JSMin::minify($jscode));
                            if (!empty($tmp_jscode)) {
                                $jscode = $tmp_jscode;
                                unset($tmp_jscode);
                            }
                        }
                    }

                    $jscode                = $this->inject_minified($jscode);
                    $this->js_min_footer[$url] = $this->md5hash . '_breezejsgroup_' . $jscode;
                }
            }
        }

        return true;
	}

	//Caches the JS in uncompressed, deflated and gzipped form.
	public function cache()	{
        if($this->group_js == true){
            $cache = new Breeze_MinificationCache($this->md5hash,'js');
            if(!$cache->check()) {
                //Cache our code
                $cache->cache($this->jscode,'text/javascript');
            }
            $this->url = breeze_CACHE_URL.$cache->getname();
            $this->url = $this->url_replace_cdn($this->url);
        }else{
            foreach ($this->js_min_head as $old_url => $js_min){
                $namehash = substr($js_min, 0 , strpos($js_min,'_breezejsgroup_'));
                $js_code  = substr($js_min,strpos($js_min,'_breezejsgroup_')+strlen('_breezejsgroup_'));
                $cache = new Breeze_MinificationCache($namehash,'js');
                if(!$cache->check()) {
                    //Cache our code
                    $cache->cache($js_code,'text/javascript');
                }
                $url = breeze_CACHE_URL.$cache->getname();
                $this->url_group_head[$old_url]= $this->url_replace_cdn($url);
            }

            foreach ($this->js_min_footer as $old_url => $js_min){
                $namehash = substr($js_min, 0 , strpos($js_min,'_breezejsgroup_'));
                $js_code = substr($js_min,strpos($js_min,'_breezejsgroup_')+strlen('_breezejsgroup_'));
                $cache = new Breeze_MinificationCache($namehash,'js');
                if(!$cache->check()) {
                    //Cache our code
                    $cache->cache($js_code,'text/javascript');
                }
                $url = breeze_CACHE_URL.$cache->getname();
                $this->url_group_footer[$old_url]= $this->url_replace_cdn($url);
            }
        }
	}
	
	// Returns the content
	public function getcontent() {
		// Restore the full content
		if(!empty($this->restofcontent)) {
			$this->content       .= $this->restofcontent;
			$this->restofcontent = '';
		}

		// Load inline JS to html
        if (!empty($this->jscode_inline_head)) {
            $replaceTag = array("</head>", "before");

            foreach ($this->jscode_inline_head as $js) {
                $jsHead[] = '<script type="text/javascript">'.$js.'</script>';
            }
            $jsReplacement = '';
            $jsReplacement .= implode('', $jsHead);
            $this->inject_in_html($jsReplacement, $replaceTag);
        }

        if (!empty($this->jscode_inline_footer)) {
            $replaceTag = array("</body>", "before");

            foreach ($this->jscode_inline_footer as $js) {
                $jsFooter[] = '<script type="text/javascript">'.$js.'</script>';
            }
            $jsReplacement = '';
            $jsReplacement .= implode('', $jsFooter);
            $this->inject_in_html($jsReplacement, $replaceTag);
        }

        //$defer = apply_filters('breeze_filter_js_defer', $defer);

        if ($this->group_js == true) {
            $replaceTag = array("</body>", "before");

            $bodyreplacementpayload = '<script type="text/javascript" defer src="'.$this->url.'"></script>';
            $bodyreplacementpayload = apply_filters('breeze_filter_js_bodyreplacementpayload', $bodyreplacementpayload);

            $bodyreplacement = implode('',$this->move['first']);
            $bodyreplacement .= $bodyreplacementpayload;
            $bodyreplacement .= implode('',$this->move['last']);

            $replaceTag = apply_filters( 'breeze_filter_js_replacetag', $replaceTag );

            if (strlen($this->jscode)>0) {
                $this->inject_in_html($bodyreplacement,$replaceTag);
            }
        } else {
            $headScript = array();
            $footerScript = array();

            if (!empty($this->url_group_head)) {
                $replaceTag = array("</head>", "before");

                foreach ($this->url_group_head as $old_url => $url) {
                    $defer = '';
                    if (gettype($old_url) == 'string' && in_array($old_url, $this->defer_js)) {
                        $defer = 'defer ';
                    }

                    $headScript[] = '<script type="text/javascript" ' . $defer . 'src="' . $url . '"></script>';
                }
                $jsReplacementPayload = implode('', $headScript);

                $jsReplacement = implode('', $this->move['first']);
                $jsReplacement .= $jsReplacementPayload;

                $replaceTag = apply_filters('breeze_filter_js_replacetag', $replaceTag);

                if (!empty($this->js_min_head)) {
                    $this->inject_in_html($jsReplacement, $replaceTag);
                }
            }

            if (!empty($this->url_group_footer)) {
                $replaceTag = array("</body>", "before");

                foreach ($this->url_group_footer as $old_url => $url) {
                    $defer = '';
                    if (gettype($old_url) == 'string' && in_array($old_url, $this->defer_js)) {
                        $defer = 'defer ';
                    }

                    $footerScript[] = '<script type="text/javascript" ' . $defer . 'src="' . $url . '"></script>';
                }
                $jsReplacementPayload = implode('', $footerScript);

                $jsReplacement = $jsReplacementPayload;
                $jsReplacement .= implode('', $this->move['last']);

                $replaceTag = apply_filters('breeze_filter_js_replacetag', $replaceTag);

                if (!empty($this->js_min_footer)) {
                    $this->inject_in_html($jsReplacement, $replaceTag);
                }
            }
        }

		// restore comments
		$this->content = $this->restore_comments($this->content);

		// Restore IE hacks
		$this->content = $this->restore_iehacks($this->content);
		
		// Restore noptimize
		$this->content = $this->restore_noptimize($this->content);

		// Return the modified HTML
		return $this->content;
	}
	
	// Checks against the white- and blacklists
	private function ismergeable($tag) {
		if (!empty($this->whitelist)) {
			foreach ($this->whitelist as $match) {
				if(strpos($tag,$match)!==false) {
					return true;
				}
			}
			// no match with whitelist
			return false;
		} else {
			foreach($this->domove as $match) {
				if(strpos($tag,$match)!==false)	{
					//Matched something
					return false;
				}
			}
			
			if ($this->movetolast($tag)) {
				return false;
				}
			
			foreach($this->dontmove as $match) {
				if(strpos($tag,$match)!==false)	{
					//Matched something
					return false;
				}
			}
			
			// If we're here it's safe to merge
			return true;
		}
	}
	
	//Checks agains the blacklist
	private function ismovable($tag) {
		if ($this->include_inline !== true || apply_filters('breeze_filter_js_unmovable',true)) {
			return false;
		}
		
		foreach($this->domove as $match) {
			if(strpos($tag,$match)!==false)	{
				//Matched something
				return true;
			}
		}
		
		if ($this->movetolast($tag)) {
			return true;
		}
		
		foreach($this->dontmove as $match) {
			if(strpos($tag,$match)!==false) {
				//Matched something
				return false;
			}
		}
		
		//If we're here it's safe to move
		return true;
	}
	
	private function movetolast($tag) {
		foreach($this->domovelast as $match) {
			if(strpos($tag,$match)!==false)	{
				//Matched, return true
				return true;
			}
		}
		
		//Should be in 'first'
		return false;
	}

    /**
     * Determines wheter a <script> $tag should be aggregated or not.
     *
     * We consider these as "aggregation-safe" currently:
     * - script tags without a `type` attribute
     * - script tags with an explicit `type` of `text/javascript`, 'text/ecmascript', 
	 *   'application/javascript' or 'application/ecmascript'
     *
     * Everything else should return false.
     *
     * @param string $tag
     * @return bool
	 * 
	 * original function by https://github.com/zytzagoo/ on his AO fork, thanks Tomas!
     */
    public function should_aggregate($tag) {
        preg_match('#<(script[^>]*)>#i',$tag,$scripttag);
        if ( strpos($scripttag[1], 'type=')===false ) {
            return true;
        } else if ( preg_match('/type=["\']?(?:text|application)\/(?:javascript|ecmascript)["\']?/i', $scripttag[1]) ) {
            return true;
        } else {
            return false;
        }
    }
}
