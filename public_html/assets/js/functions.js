jQuery( document ).ready(function( $ ) {	
	
	/**
	*	Implement foundation framdwork
	*/
	$(document).foundation();
	
	/**
	*	Listen for clicks on previous searches and prevent default
	*/
	$('#wordcloud').on( "click", 'a', function(event) {
		event.preventDefault();
		
		//	Hide previous search results and empty div
		$('#results').slideUp('fast', function() {
			$(this).empty();
		});
		
		//	Hide chart of previous data and empty div
		$('#stats').slideUp('fast', function() {
			$('#highcharts').empty();
		});
		
		//	Post data to ajax url and display chart and search results
		var term = $(this).text();		
		$.post( ajax_url, { term: term }, function( data ) {
			displaySearchData(data);
		});
	});
	
	/**
	*	Listen for submit on form and prevent default	
	*/
	$('form#form-twitter-search').submit(function(event) {
		event.preventDefault();		
		
		//	Hide previous search results and empty div
		$('#results').slideUp('slow', function() {
			$(this).empty();
		});
		
		//	Hide chart of previous data and empty div
		$('#stats').slideUp('slow', function() {
			$('#highcharts').empty();
		});
		
		//	Get term from search textfield
		var term = $('#form-text-term', this).val();
		
		//	Generate random integer for use in wordcloud heading element
		var randomInt = getRandomInt(1, 7);
		
		//	Post data to ajax url
		$.post( ajax_url, { term: term }, function( data ) {			
			
			//	Search wordcloud for term to determine whether or not to append
			append = true;		
			$.each($('#wordcloud').children(), function(index, element) {
				if($(this).text().toLowerCase() == term.toLowerCase())
					append = false;			
			});
			
			//	Append term if not found (use random integer to determine which heading element. ie: h1, h2, h3, h4, h5, h6)
			if(append)
				$('div#wordcloud').append('<h'+randomInt+' class="word"><a href="#">'+term.toLowerCase()+'</a></h'+randomInt+'>');			
			
			//	Display chart and search results
			displaySearchData(data);
		});
	});
	
	/**
	*	Displays chart and search results
	*/	
	function displaySearchData(data) {
		
		//	Set search form textfield value to term searched
		$('#form-text-term').val(data.term)
		
		//	Slidedown as chart is created--animation is cool
		$('#stats').slideDown('fast', function() {
			$('#highcharts').highcharts({
				chart: {
					type: 'column'
				},
				title: {
					text: ''
				},
				xAxis: {
					type: 'category'
				},
				
				legend: {
					enabled: false
				},	
				plotOptions: {
					series: {
						borderWidth: 0,
						dataLabels: {
							enabled: true
						}
					}
				},				
				series: [{
					name: 'Alphabet',
					colorByPoint: true,
					data: data.stats.seriesData
				}],
				drilldown: {
					series: data.stats.drilldownSeries
				}
			});
		});	
		
		//	Show search results
		$('#results').html(data.output).slideDown('fast');
	}
	
	/**
	*	Generates random integer
	*/
	function getRandomInt(min, max) {
		return  Math.floor(Math.random() * (max - min) + min);
	}		
});