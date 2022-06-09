<?php

@date_default_timezone_set('Europe/Moscow'); 

// http://php.net/manual/ru/function.curl-file-create.php
if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '') {
        return "@$filename;filename="
            . ($postname ?: basename($filename))
            . ($mimetype ? ";type=$mimetype" : '');
    }
}

// require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

class DKCP_API {
	// PRIVATE METHODS
	// PRIVATE METHODS
	// PRIVATE METHODS
	private $config = array();
	
	// http://wmas.msk.ru/archives/get-user-ip-php
	public static function get_user_ip(){
		if ( getenv('REMOTE_ADDR') ) $user_ip = getenv('REMOTE_ADDR');
		elseif ( getenv('HTTP_FORWARDED_FOR') ) $user_ip = getenv('HTTP_FORWARDED_FOR');
		elseif ( getenv('HTTP_X_FORWARDED_FOR') ) $user_ip = getenv('HTTP_X_FORWARDED_FOR');
		elseif ( getenv('HTTP_X_COMING_FROM') ) $user_ip = getenv('HTTP_X_COMING_FROM');
		elseif ( getenv('HTTP_VIA') ) $user_ip = getenv('HTTP_VIA');
		elseif ( getenv('HTTP_XROXY_CONNECTION') ) $user_ip = getenv('HTTP_XROXY_CONNECTION');
		elseif ( getenv('HTTP_CLIENT_IP') ) $user_ip = getenv('HTTP_CLIENT_IP');
		return trim($user_ip);
	}
	
	private $cache = null;
	
	function __construct ( $params ) {
		$params_need = array(
			'PROGRAM', 'PROGRAM_SKEYS', 'USER_PASSWORD', 'USER_LOGIN', 'URL', 'PATH_CERT', 'CERT', 'CERT_PASSWORD'
		);
		
		$optional = array('BEFORE_SEND', 'PROTOCOL_VERSION', 'RESPONSE_BEFORE_PARSE', 'RESPONSE_AFTER_PARSE', 'LANG', 'RESPONSE_ERROR_PARSE', 'CACHE');
		$each     = array_merge($params_need, $optional);
	
		foreach ( $each as $key_need ) {
			if ( !array_key_exists($key_need, $params) ) {
				if ( in_array($key_need, $optional) == false ) {
					// return array(False, array(
						// 'error' => 'need params for name: ' . $key_need
					// ));
					throw new Exception('need params for name: ' . $key_need);
				} else {
					if ( $key_need == 'PROTOCOL_VERSION' ) {
						$this->config['PROTOCOL_VERSION'] = self::PROTOCOL_VERSION_LAST;
					} else {
						$this->config[$key_need] = false;
					}
				}
			} else {
				$this->config[$key_need] = $params[$key_need];
			}
		}
		
		if ( $this->config['CACHE'] ) {
			$this->cache = new Stash\Pool(new Stash\Driver\FileSystem(array('path' => __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR)));
		}
	}
	
	private function CmdArray ( $cmd, $params, $script, $free ) {
		if ( is_null($params) ) {
			$params = array();
		}
		
		if ( array_key_exists('ext_transact', $params) == false ) {
			$params['ext_transact'] = $this->get_ext_transact();
		}
			
		$request_params = array(
			'lang'         => $this->config['LANG'],
			'cmd'          => $cmd,
			'program'      => $this->config['PROGRAM'],
			'program_sign' => md5($this->config['PROGRAM_SKEYS'] . $params['ext_transact'])
		);
		
		if ( $free == false ) {
			$request_params['login']    = $this->config['USER_LOGIN'];
			$request_params['password'] = md5($this->config['USER_PASSWORD'] . $params['ext_transact']);
		}
		
		foreach ( $request_params as $key => $value ) {
			$params[$key] = $value;
		}

		// http://php.net/manual/ru/function.curl-setopt.php
		if ( !empty($_FILES) ) {
			foreach ( $_FILES as $filename => $files ) {
				if ( !is_array($files['name']) ) {
					foreach ( $files as $key => $value ) {
						$files[$key] = array($value);
					}
				}
				
				$i = 0;
			
				foreach ( $files['tmp_name'] as $index => $tmp_name ) {
					if ( strlen($tmp_name) > 0 ) {
						$params[implode([$filename, '[', $i, ']'])] = curl_file_create(
							$tmp_name, 
							$files['type'][$index],
							mb_convert_encoding($files['name'][$index], 'Windows-1251', 'UTF-8')
						);
						
						++$i;
					}
				}
			}
		}
		
		if ( is_callable($this->config['BEFORE_SEND']) == true ) {
			$params = $this->config['BEFORE_SEND']($cmd, $params);
		}
		
		return array($this->config['URL'] . $script, $params);
	} 
	
	private function HttpsReqPost ( $mh, $url, $params, $free ) {
		$ch = curl_init();
		$params['dkcp_protocol_version'] = $this->config['PROTOCOL_VERSION'];
		
		$curl_request_params = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POST           => 1,
			CURLOPT_POSTFIELDS     => $params,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLINFO_HEADER_OUT    => 1,
			CURLOPT_HTTPHEADER     => array(
				'Expect:'
			)
		);
		
		if ( $free == False ) {
			$curl_request_params[CURLOPT_SSLCERT]       = $this->config['CERT'];
			$curl_request_params[CURLOPT_CAPATH]        = $this->config['PATH_CERT'];
			$curl_request_params[CURLOPT_SSLCERTPASSWD] = $this->config['CERT_PASSWORD'];
		}
		
		$res = curl_setopt_array($ch, $curl_request_params);
		
		curl_multi_add_handle($mh, $ch);
		
		return array($res, $ch);
	}
	// XML PARSER
	// XML PARSER
	// XML PARSER
	private function parse_result ( $xml_string ) {
		if ( class_exists('XMLReader') == true ) {
			return $this->XMLReader($xml_string);
		} else {
			return array(false, 'needle xml parser');
		}
	}
	
	private $xml_error_parse_list = array();
	private $reader;
	
	function xml_error_parse ($errno, $errstr, $errfile, $errline) {
		$this->xml_error_parse_list []= implode('; ', array($errno, $errstr, $errfile, $errline));
	}

	private function XMLReader ( $xml_string ) {
		$reader = new XMLReader();
		$reader->XML($xml_string);
		
		set_error_handler(array($this, 'xml_error_parse'), E_WARNING);
		
		$result = $this->xml_string_to_array($reader);
		$result = $result['response'];
		 
		restore_error_handler();
	
		if ( count($this->xml_error_parse_list) > 0 ) {
			$errors = $this->xml_error_parse_list;
			$this->xml_error_parse_list = array();
			return array(False, $errors);
			// return array(False, 'Not correct XML');
		}
		
		return array(True, $result);
	}

	function xml_string_to_array($XML) {
		$tree = null;
		
		while( $XML->read() ) {
			switch ($XML->nodeType) {
				case XMLReader::END_ELEMENT: 
					return $tree;
				case XMLReader::ELEMENT:
					$node = $XML->isEmptyElement ? '' : $this->xml_string_to_array($XML);
					
					if ( $XML->name == "colvalues" ) {
						if ( $node != '' ) {
							$tree[$XML->name][] = $node; 
						}
					} else if ( is_array($tree) && in_array($XML->name, array_keys($tree)) ) {
						if ( @!is_array($tree[$XML->name][0]) ) {
							$TempTree = $tree[$XML->name];
							$tree[$XML->name] = array();
							$tree[$XML->name][0] = $TempTree;
						}	
					
						if ( $XML->name == 'table' ) {
							$node['colcount'] = $XML->getAttribute("col");
							$node['rowcount'] = $XML->getAttribute("row");
							$node['name'] = $XML->getAttribute("name");
						}
						
						$tree[$XML->name][] = $node;
					} else {
						$tree[$XML->name] = $node;
						
						if ( $XML->name == "table" ) {
							$ColCount = $XML->getAttribute("col");
							$RowCount = $XML->getAttribute("row");
							$tree[$XML->name]['colcount'] = $ColCount;
							$tree[$XML->name]['rowcount'] = $RowCount;
							$tree[$XML->name]['name'] = $XML->getAttribute("name");
						}
					}
					break;
				case XMLReader::TEXT:
				case XMLReader::CDATA:
					// $tree .= preg_replace('/<\!\[CDATA\[(.*?)\]\]>/is', '$1', mb_convert_encoding($XML->value, 'Windows-1251', 'UTF-8'));
					$tree .= preg_replace('/<\!\[CDATA\[(.*?)\]\]>/is', '$1', $XML->value);
			}
		}
		
		return $tree; 
	}

	// PUBLIC METHODS
	// PUBLIC METHODS
	// PUBLIC METHODS
	
	const PROTOCOL_VERSION_LAST = 'LAST';
	const FREE_DEFAULT          = False;
	
	public function cmd ( $cmd, $params = array(), $script = null, $free = self::FREE_DEFAULT ) {
		if ( is_array($cmd) == false ) {
			$loner    = true;
			$requests = array(array($cmd, $params, $script, $free));
		} else {
			$requests = $cmd;
			$loner    = false;
		}
		
		$cache_only = true;
		$mh   = curl_multi_init();
		$pool = array();
		foreach ( $requests as $cmd_selected ) {
			list($cmd, $params, $script, $free) = count($cmd_selected) == 4 ? $cmd_selected : array_merge($cmd_selected, array(self::FREE_DEFAULT));
			
			$cache_exists = false;
			$lifetime = 0;
			$cache_item = null;
			
			if ( $this->cache ) {
				$lifetime = $this->config['CACHE']($cmd, $params, $script, $free);
				
				if ( $lifetime ) {
					$cache_item = $this->cache->getItem($cmd . '?'. http_build_query($params));
					$cache_exists = !$cache_item->isMiss();
					
					if ( $cache_exists ) {
						$pool []= array(null, $cache_item, $cache_exists, $lifetime);
					}
				}
			}
			
			if ( $cache_exists == false ){
				list($url, $params) = $this->CmdArray($cmd, $params, $script, $free);
				list($res, $ch) = $this->HttpsReqPost($mh, $url, $params, $free);
				$pool []= array($ch, $cache_item, $cache_exists, $lifetime);
				$cache_only = false;
			}
		}
		
		if ( $cache_only == false ) {
			$active = null;
			//��������� �����������
			do {
				$mrc = curl_multi_exec($mh, $active);
			} while ($mrc == CURLM_CALL_MULTI_PERFORM);

			// �� 5.5.36 ������ � ��������
			/* while ($active && $mrc == CURLM_OK) {
				if (curl_multi_select($mh) != -1) {
					do {
						$mrc = curl_multi_exec($mh, $active);
					} while ($mrc == CURLM_CALL_MULTI_PERFORM);
				}
			} */
			
			do {
				curl_multi_exec($mh, $running);
				curl_multi_select($mh);
			} while ($running > 0);
		}
		
		$results = array();
		foreach ( $pool as $request ) {
			list($ch, $cache_item, $cache_exists, $lifetime) = $request;
			
			if ( $cache_exists ) {
				list($res, $info, $error) = $cache_item->get();
			} else {
				$res   = curl_multi_getcontent($ch);
				$info  = curl_getinfo($ch);
				$error = curl_error($ch);
				// print_r($res);
				// print_r($info);
				// print_r($error);
				curl_multi_remove_handle($mh, $ch);
			}
			
			if ( 
				!$error &&
				$info !== false &&
				$res !== False && 
				// text/xml;charset=UTF-8
				in_array('text/xml', explode(';', $info['content_type'])) !== false && 
				intval($info['http_code']) == 200 
			) {
				if ( $cache_item ) {
					$cache_item->lock();
					$cache_item->set(array($res, $info, $error));
					$cache_item->expiresAfter($lifetime);
					$this->cache->save($cache_item);
				}
				
				$results []= array(True, $res);
			} else {
				if ( strlen($error) == 0 ) {
					// http://ru.wikipedia.org/wiki/%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D0%BA%D0%BE%D0%B4%D0%BE%D0%B2_%D1%81%D0%BE%D1%81%D1%82%D0%BE%D1%8F%D0%BD%D0%B8%D1%8F_HTTP
					$results []= array(False, 'HTTP status code: ' . $info['http_code']);
				} else {
					// Error: could not load PEM client certificate, OpenSSL error error:02001002:system library:fopen:No such file or directory, (no key found, wrong pass phrase, or wrong file format?)
					$results []= array(False, $error);
				}
			}
		}
		
		$num = 0;
		
		foreach ( $requests as $key => $cmd_selected ) {
			list($cmd, $params, $script, $free) = count($cmd_selected) == 4 ? $cmd_selected : array_merge($cmd_selected, array(self::FREE_DEFAULT));
			
			if ( is_callable($this->config['RESPONSE_BEFORE_PARSE']) == true ) {
				$this->config['RESPONSE_BEFORE_PARSE']($cmd, $url, $params, $results[$key][1]);
			}
			
			if ( $results[$key][0] !== true ) {
				$results[$key][1] = 'Error: '. $results[$key][1];
			} else {
				$xml_string = $results[$key][1];
				list($res, $result) = $this->parse_result($xml_string);
				
				$results[$key][0] = $res;
				$results[$key][1] = $result;
				
				if ( $result['result'] != 0 && $pool[$num][1] ) {
					$pool[$num][1]->clear();
				}
				
				if ( is_callable($this->config['RESPONSE_ERROR_PARSE']) == true && is_array($result) && $res == False ) {
					$this->config['RESPONSE_ERROR_PARSE']($cmd, $xml_string, $result);
					// fix proxy xml parse errors
					$results[$key][1] = 'Not correct XML';
				} elseif ( is_callable($this->config['RESPONSE_AFTER_PARSE']) == true ) {
					$results[$key][1] = $this->config['RESPONSE_AFTER_PARSE']($cmd, $results[$key][0], $results[$key][1]);
				}
			}
			
			$num += 1;
		}
		
		return $loner == True ? $results[0] : $results;
	}
	
	public function get_ext_transact () {
		$tm = localtime(time(), 1);

		return sprintf( "%04d%02d%02d%02d%02d%02d%04d", $tm["tm_year"] + 1900, $tm["tm_mon"] + 1,
			$tm["tm_mday"], $tm["tm_hour"], $tm["tm_min"], $tm["tm_sec"], rand(1111, 9999)
		);
	}
	
	public function cache_clear(){
		if ( $this->cache ) {
			$this->cache->clear();
			$this->cache->purge();
		}
	}
}


?>
