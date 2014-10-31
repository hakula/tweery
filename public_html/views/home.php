<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Tweery</title>	
		
		<link rel="stylesheet" href="<?php echo Flight::get('base_url'); ?>/assets/foundation/css/foundation.min.css">				
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">		
		<link rel="stylesheet" href="<?php echo Flight::get('base_url'); ?>/assets/css/style.css">		
		<script src="<?php echo Flight::get('base_url'); ?>foundation/js/vendor/modernizr.js"></script>		
	</head>
	<body class="">
		<div class="row">
			<div class="small-12 columns">		
				<header id="site-header" >
					<div class="panel">
						<div class="row">
							<div class="small-12 columns">							
								<h1 class="subheader text-center">
									Tweery
								</h1>
							</div>
						</div>
						<div class="row">						
							<div class="small-8 small-centered columns">
								<h2 class="subheader text-center">
									Search and analyze twitter.
								</h2>
								<form id="form-twitter-search" action="" method="post" class="form-inline" role="form">
									<div class="row">						
										<div class="large-12 columns">
											<div class="row collapse">
												<div class="small-10 columns">								
													<input placeholder="Search" type="text" name="term" id="form-text-term" class="form-text">
												</div>
												<div class="small-2 columns">
													<button class="button postfix"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</div>
									</div>	
								</form>								
							</div>						
						</div>
						<div class="row">
							<div class="small-12 columns">
								<div id="recent-searches">							
									<div class="panel">
										<h3 class="subheader text-center">Recent searches</h3>									
										<div id="wordcloud" class="text-center">
											<?php foreach($previous as $tweery): $heading = rand(1, 6); ?>
												<h<?php echo $heading; ?> class="word"><a href="#"><?php echo $tweery['term']; ?></a></h<?php echo $heading; ?>>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</header>
				<main id="site-content">
					<div class="row">
						<div class="small-12 columns">						
							<div class="row">
								<div class="small-12 columns">
									<div class="panel">
										<h4 class="subheader text-center">Tweery Stats</h4>
										<hr>
										<div id="stats">
											<div data-columns="" data-drilldown="" id="highcharts" style="width:100%; height:400px;"></div>								
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="small-12 columns">
									<div class="panel">
										<h4 class="subheader text-center">Tweets</h4>
										<hr>
										<div id="results"></div>				
									</div>			
								</div>
							</div>
						</div>
					</div>
				</main>			
				<footer id="site-footer">
					<div class="row">
						<div class="small-12 columns">						
							<div class="panel">
								<p class="text-center"><small>&copy; <?php echo date('Y'); ?> Joseph N. Niu</small></p>
							</div>
						</div>
					</div>
				</footer>			
			</div>
		</div>
		<script type='text/javascript'>
			/* <![CDATA[ */
				var ajax_url = '<?php echo Flight::get('base_url'); ?>/ajax/search';
			/* ]]> */
		</script>		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="<?php echo Flight::get('base_url'); ?>/assets/foundation/js/foundation.min.js"></script>
		<script src="http://code.highcharts.com/highcharts.js"></script>
		<script src="http://code.highcharts.com/modules/drilldown.js"></script>

		<script src="<?php echo Flight::get('base_url'); ?>/assets/js/functions.js"></script>
	</body>
</html> 