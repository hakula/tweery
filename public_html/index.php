<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/flight/Flight.php';
require_once __DIR__ . '/TwitterOAuth/TwitterOAuth.php';
require_once __DIR__ . '/TwitterOAuth/Exception/TwitterException.php';

use TwitterOAuth\TwitterOAuth;

date_default_timezone_set('UTC');

/**
*	Register PDO class with Flight framework and save db connection info
*/
Flight::register('db', 'PDO', array(sprintf('mysql:host=%s;dbname=%s', $config['database']['host'], $config['database']['name']), $config['database']['user'], $config['database']['pass']));

/** 
*	Store config variables in Flight framework
*/
Flight::set('flight.views.path', 'views');
Flight::set('twitter.auth', $config['twitter']);
Flight::set('base_url', $config['misc']['site_url']);
Flight::set('ajax_url', Flight::get('base_url').'/ajax/search');


/**
*	Route that handles GET requests for front page
*/
Flight::route('GET /', function($route) {
	
	//Get db connection
	$dbh = Flight::db();
	
	//	Get previous tweerys
	$sth = $dbh->prepare('SELECT * FROM tweery ORDER BY created LIMIT 25');
	$sth->execute();
	$sth->setFetchMode(PDO::FETCH_ASSOC);
	
	$previous = $sth->fetchAll();	
	
	//Render home view and pass along necessary data
	Flight::render('home', array('previous' => $previous));	
}, true);


/**
*	Route that handles POST requests to /ajax/search
*/
Flight::route('POST /ajax/search', function() {
	
	//	Check if term is empty or not set and pick random word if it is not
	if(!isset(Flight::request()->data->term) || empty(Flight::request()->data->term))
		Flight::request()->data->term = Flight::random_word();
	
	//Search Twitter for term
	$results = Flight::twitter_search(Flight::request()->data->term);
	$output = '';
	
	//Format results for display in dropdown
	foreach($results as $result) {
		$output .= '<div class="row">';
			$output .= '<div class="tweet">';
				$output .= '<div class="tweet-profile-image small-1 columns">';
					$output .= '<a target="_blank" class="" href="https://twitter.com/'.$result->user->screen_name.'">';
						$output .= '<img class="th" src="'.$result->user->profile_image_url_https.'"/>';
					$output .= '</a>';
				$output .= '</div>';
				$output .= '<div class="tweet-text small-11 columns">';
					$output .= '<h4><a target="_blank" href="https://twitter.com/'.$result->user->screen_name.'">'.$result->user->name.' <small>@'.$result->user->screen_name.'</small></a></h4>';
					$output .= '<p><a target="_blank" class="" href="https://twitter.com/'.$result->user->screen_name.'/status/'.$result->id_str.'">'.$result->text.'</a></p>';
					$output .= '<ul class="inline-list">';
						$output .= '<li>';
							$output .= '<a target="_blank" href="https://twitter.com/'.$result->user->screen_name.'/status/'.$result->id_str.'">';
								$output .= '<i class="fa fa-twitter"></i> '.Flight::format_interval(strtotime($result->created_at)).'';
							$output .= '</a>';
						$output .= '</li>';
						$output .= '<li>';
							$output .= '<a target="_blank" href="https://twitter.com/'.$result->user->screen_name.'/status/'.$result->id_str.'">';
								$output .= '<i class="fa fa-comments-o"></i> '.$result->retweet_count.' Retweets';
							$output .= '</a>';
						$output .= '</li>';
					$output .= '</ul>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';	
	}
	
	//Set variables and output json
	$stats = Flight::tweery_stats($results);
	$response = array('output' => $output, 'stats' => $stats, 'term' => Flight::request()->data->term,);
	Flight::json($response);	
});

/**
*	Generates a random word from http://chrisvalleskey.com/fillerama/
*/
Flight::map('random_word', function() {	
	
	//	Available shows to get random word from
	$shows = array('arresteddevelopment', 'doctorwho', 'dexter', 'futurama', 'holygrail', 'simpsons', 'starwars');	
	
	//	Initiliaze curl
	$ch = curl_init();	
	
	//	Set curl options
	curl_setopt($ch, CURLOPT_URL, "http://api.chrisvalleskey.com/fillerama/get.php?count=100&format=json&show=".$shows[array_rand($shows)]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0); 	
	$output = curl_exec($ch);
	
	//	Close curl
	curl_close($ch);
	
	//	Decode json
	$output = json_decode($output);	
	
	//	Collect words from output
	$words = array();	
	foreach($output->db as $filler) 
		$words[] = $filler->source;	
	
	//	Remove duplicates
	$words = array_unique($words);
	
	//Return random word
	return $words[array_rand($words)];	
});

/**
*	Formats a time interval given two unix timestamps.
*/ 
Flight::map('format_interval', function($date1, $date2 = null) {
	
	//	Create DateTime object and set timestamp for $date1
	$start = new DateTime();
	$start->setTimestamp($date1);
	
	//	Create DateTime object and set timestamp for $date2 if set
	$end = new DateTime();
	if(!is_null($date2))
		$end->setTimestamp($date2);
	  
	//	Create DateInterval 
    $interval = $end->diff($start);
    
    //	Add plural endings
    $doPlural = function($nb,$str){
	    return $nb>1?$str.'s':$str;
	}; 
	
	//	Format time
    $format = array();
    if($interval->y !== 0) {
        $format[] = "%y ".$doPlural($interval->y, "year");
    }
    if($interval->m !== 0) {
        $format[] = "%m ".$doPlural($interval->m, "month");
    }
    if($interval->d !== 0) {
        $format[] = "%d ".$doPlural($interval->d, "day");
    }
    if($interval->h !== 0) {
        $format[] = "%h ".$doPlural($interval->h, "hour");
    }
    if($interval->i !== 0) {
        $format[] = "%i ".$doPlural($interval->i, "minute");
    }
    if($interval->s !== 0) {
        if(!count($format)) {
            return "less than a minute ago";
        } else {
            $format[] = "%s ".$doPlural($interval->s, "second");
        }
    }
    
    //	Use two biggest parts of interval
    if(count($format) > 1) {
        $format = array_shift($format)." ".array_shift($format);
    } else {
        $format = array_pop($format);
    }
    
    //	Prepend 'since '...
    return $interval->format($format).' ago';
});

/**
*	Searches Twitter and saves results to the database
*/
Flight::map('twitter_search', function($term) {
		
	//	Create new TwitterOAuth using credentials from config
	$tw = new TwitterOAuth(Flight::get('twitter.auth'));	
	
	//	Set Twitter params
	$params = array(
		'q' => $term,
		'count' => 100,
		'exclude_replies' => true,
		'lang' => 'en'
	);
	
	//	Perform search
	$response = $tw->get('search/tweets', $params);	
	
	//	Collect twitter ids in array for db comparison
	$twitter_ids = array();	
	foreach($response->statuses as $status) {
		$twitter_ids[] = $status->id_str;
	}
	
	//	Get db handle
	$dbh = Flight::db();
	
	//	Search db form term or "tweery"
	$sth = $dbh->prepare('SELECT t.id FROM tweery t WHERE t.term = :term');
	$sth->execute(array(':term' => Flight::request()->data->term));	

	$tweery_id = ($sth->rowCount()) ? $sth->fetchColumn() : 0;
	
	//	If no tweery found then insert term
	if(empty($tweery_id)) {
		$sth = $dbh->prepare("INSERT INTO tweery ( term, created ) VALUES (:term, :time)");		
		if($sth->execute(array(':term' => strtolower(Flight::request()->data->term), ':time' => time())))
			$tweery_id = $dbh->lastInsertId();
	}
	
	//	Search for tweets already stored to avoid saving duplicate information
	$sth = $dbh->prepare("SELECT t.twitter_id FROM tweet t WHERE t.tweery_id = :tweery_id AND t.twitter_id IN ('".implode("', '", $twitter_ids)."')");	
	$sth->execute(array(':tweery_id' => $tweery_id));	
	$sth->setFetchMode(PDO::FETCH_ASSOC);		
	$duplicates = $sth->fetchAll(PDO::FETCH_COLUMN, 0);	
	
	//	Loop through results and insert tweets that are not already in db
	$sth = $dbh->prepare("INSERT INTO tweet ( tweery_id, twitter_id, text, created, lang, name, username, profile_image_url, retweets ) VALUES (:tweery_id, :twitter_id, :text, :created, :lang, :name, :username, :profile_image_url, :retweets)");	
	
	foreach($response->statuses as $status) {
		
		//	If tweet is not already in db then proceed with insert
		if(!in_array($status->id_str, $duplicates)) {
			$sth->execute(array(
				':tweery_id' => $tweery_id, 
				':twitter_id' => $status->id_str, 
				':text' => $status->text, 
				':created' => strtotime($status->created_at), 
				':lang' => $status->lang, 
				':name' => $status->user->name, 
				':username' => $status->user->screen_name, 
				':profile_image_url' => $status->user->profile_image_url_https, 
				':retweets' => $status->retweet_count,
			));			
		}
	}
	
	//	Return search results
	return $response->statuses;	
});

/**
*	Sorts Twitter search results for display in chart
*/
Flight::map('tweery_stats', function($tweery) {	
	
	//	Create array for sorting data keyed on letters of the alphabet
	$data = array();
	foreach(range('A', 'Z') as $letter) {
		$data[$letter] = array();
		foreach(range(0, 50, 10) as $min) {
			$max = $min + 9;
			$data[$letter][sprintf('HH:MM:%s-%s', $min, $max)] = 0;
		}
	}	
	
	//	Loop through tweets 
	foreach($tweery as $tweet) {
		//	Remove all special charaters and spaces in order to find the first letter
		$text = preg_replace('/[^A-Za-z]/', '', $tweet->text);
		$first = substr($text, 0, 1);
		
		//	Loop through range of 0 - 50 in increments of 10 to determine which 10 second interval within a minute tweet was created
		foreach(range(0, 50, 10) as $min) {
			$max = $min + 9;
			$seconds = (integer) date('s', strtotime($tweet->created_at));
			if(($seconds >= $min) && ($seconds <= $max))
				$data[strtoupper($first)][sprintf('HH:MM:%s-%s', $min, $max)]++;
				
		}
	}	
	
	//	Loop through sorted data to create arrays for chart series and drilldown
	$series_data = array();	
	$drilldown_series = array();
	$total = 0;
	foreach($data as $letter => $seconds) {
		$series = new stdClass();
		$series->name = $letter;
		$series->y = array_sum($seconds);
		$series->drilldown = strtolower($letter);
		$series_data[] = $series;
		
		$drilldown = new stdClass();
		$drilldown->id = strtolower($letter);
		$drilldown->data = array();
		foreach($seconds as $range => $count) {
			$drilldown->data[] = array($range, $count);
		}
		$drilldown_series[] = $drilldown;
	}
	$stats = new stdClass();
	$stats->seriesData = $series_data;
	$stats->drilldownSeries = $drilldown_series;
	
	return $stats;
});

/**
*	Start Flight framework
*/
Flight::start();

/* End of file index.php */