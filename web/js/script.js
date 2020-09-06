function initMap(){
	// var homeLatLng = {lat: 45.5026, lng: -122.6794};
	// var homeLatLng = {lat: 45, lng: -120};
	var centerLatLng = {lat: 44, lng: -119};
	var map = new google.maps.Map(
		document.getElementById('map-container'),
		{
			// zoom: 4,
			zoom: 6,
			// zoom: 9.5,
			// zoom: 8,
			center: centerLatLng,
			disableDefaultUI: true,
			mapTypeId: 'satellite'
		}
	);
	var mapInitialized = false;
	google.maps.event.addListenerOnce(map, 'idle', initializeMap);
	function initializeMap(){
		console.log('initializeMap');
		if(false === mapInitialized){
			/* var home = new google.maps.Marker({
				position: homeLatLng,
				map: map,
				title: 'Home'
			}); */
			grid.init(map, window.scriptData);
			mapInitialized = true;
		}
	}
}
var grid = {
	debug: false,
	map: null,
	dateGrid: null,
	urls: null,
	colors: {
		white:		'#ffffff',
		grey:		'#666666',
		black:		'#000000',
		red:		'#ff0000',
		orange:		'#ff9000',
		yellow:		'#fff000',
		green:		'#00ff00',
	},
	init: function(map, scriptData){
		console.log('init', map, scriptData);
		this.map = map;
		this.urls = scriptData.urls;
		if(window.debug){
			this.debug = true;
			console.log('DEBUG MODE ON');
		}
		if(this.debug){
			// delay for less build
			setTimeout(function(){
				grid.ui.init();
// grid.data.init();
			}, 250);
		}else{
			grid.ui.init();
// grid.data.init();
		}
	},
	data: {
		update: function(){
			console.log('data.update');
			var bounds = grid.ui.map.getBounds();
// console.log(bounds.tr);
			this.get({
				dateRange: {
					start: grid.ui.monthSelector.getDate(),
					// end: null
				},
				offset: {
					dist: {
						// x: 5.6,
						// y: 8
						x: 1.9,
						y: 2
					},
					cells: {
						x: 15,
						y: 9
					}
				},
				start: bounds.tl
			});
		},
		get: function(request){
			console.log('data.get');
			grid.ui.loader.show();
			$.ajax({
				dataType: 'json',
				url: grid.urls.data,
				data: request,
				success: grid.data.success,
				error: grid.data.error
			});
		},
		success: function(data, textStatus, jqXHR){
			console.log('data.success', data, textStatus, jqXHR);
// console.log(JSON.stringify(data));
			grid.dateGrid = data.data.dateGrid;
			grid.ui.update();
			grid.ui.loader.hide();
		},
		error: function(jqXHR, textStatus, errorThrown){
			console.error('data.error', jqXHR, textStatus, errorThrown);
			$.each(jqXHR.responseJSON.errors, function(errorKey, error){
				console.error('data.error errors error', error);
			});
		}
	},
	ui: {
		init: function(){
			console.log('ui.init');
			this.loader.init();
			this.monthSelector.init();
			this.dateSlider.init();
			this.dateDisplay.init();
			this.map.init();
			this.windowScreen.init();
			this.monthSelector.forceChange();
		},
		update: function(){
			console.log('ui.update');
			this.dateSlider.build();
		},
		loader: {
			loader: null,
			init: function(){
				console.log('ui.loader.init');
				this.loader = $('#loader-wrap');
				this.loader.hide();
			},
			show: function(){
				console.log('ui.loader.show');
				this.loader.show();
			},
			hide: function(){
				console.log('ui.loader.hide');
				this.loader.hide();
			}
		},
		monthSelector: {
			monthSelector: null,
			date: null,
			init: function(){
				console.log('ui.monthSelector.init');
				this.monthSelector = $('#month');
				this.monthSelector.on('change', this.change);
			},
			forceChange: function(){
				console.log('ui.monthSelector.forceChange');
				grid.ui.monthSelector.monthSelector.change();
			},
			change: function(event){
				console.log('ui.monthSelector.change', event);
				grid.ui.monthSelector.date = event.target.value;
				grid.data.update();
			},
			getDate: function(){
				console.log('ui.monthSelector.getMonth');
// console.log(this.date);
// console.log(grid.utility.date.today());
				return this.date;
			}
		},
		dateSlider: {
			dateSlider: null,
			slider: null,
			handle: null,
			dates: [],
			init: function(){
				console.log('ui.dateSlider.init');
				this.dateSlider = $('#date-slider');
				this.slider = $('#date-slider-slider');
				this.handle = $('#date-slider-custom-handle');
			},
			getHeight: function(){
				console.log('ui.dateSlider.getHeight');
				return grid.ui.dateSlider.dateSlider.outerHeight();
			},
			build: function(){
				console.log('ui.dateSlider.build');
				this.reset();
				this.parseDates();
				this.slider.slider({
					min: 1,
					max: grid.ui.dateSlider.dates.length,
					change: function(event, ui){
						// console.warn('ui.dateSlider.slider.change');
						grid.ui.dateSlider.update(ui.value - 1);
					},
					create: function(event, ui){
						// console.warn('ui.dateSlider.slider.create');
						grid.ui.dateSlider.update(0);
					},
					slide: function(event, ui){
						// console.warn('ui.dateSlider.slider.slide');
						grid.ui.dateSlider.update(ui.value - 1);
					}
				});
			},
			parseDates: function(){
				console.log('ui.dateSlider.parseDates');
				$.each(grid.dateGrid, function(dateID, date){
					grid.ui.dateSlider.dates.push(date.readable);
				});
			},
			update: function(dateIndex){
				console.log('ui.dateSlider.update');
				grid.ui.dateDisplay.updateDate(this.dates[dateIndex]);
				grid.ui.grid.update(dateIndex);
			},
			reset: function(){
				console.log('ui.dateSlider.reset');
				if(typeof grid.ui.dateSlider.slider.slider('instance') !== 'undefined'){
					grid.ui.dateSlider.slider.slider('destroy');
				}
				grid.ui.dateSlider.dates = [];
			}
		},
		grid: {
			cells: [],
			update: function(dateIndex){
				console.log('ui.grid.update', dateIndex);
				this.reset();
				$.each(grid.dateGrid[dateIndex].grid, function(cellId, cellData){
					grid.ui.grid.cells.push(new grid.ui.grid.cell(cellId, cellData));
				});
			},
			reset: function(){
				console.log('ui.grid.reset');
				$.each(grid.ui.grid.cells, function(cellId, cell){
					cell.removePolygon();
				});
				grid.ui.grid.cells = [];
			},
			cell: function(cellId, cellData){
				// console.log('ui.grid.cell', cellId, cellData);
				var self = this;
				this.Id = cellId;
				this.data = cellData;
				// functions
				this.colors = {
					setColorByMouseOver: function(){
						self.polygon.setOptions({
							strokeOpacity: 1,
							fillOpacity: .8
						});
					},
					setColorByMouseOut: function(){
						self.polygon.setOptions({
							strokeOpacity: .5,
							fillOpacity: .5
						});
					},
					getColorByRating: function(rating){
						return grid.utility.color.getColorForPercentage(rating / 100);
					}
				}
				this.extractCoordinates = function(coordinates){
					// console.log('cell.extractCoordinates', coordinates);
					var c = [];
					$.each(['t', 'r', 'b', 'l'], function(ii, vv){
						c.push(coordinates[vv]);
					});
					return c;
				}
				this.removePolygon = function(){
					self.polygon.setMap(null);
				}
				// build polygon
				this.polygon = new google.maps.Polygon({
					paths: self.extractCoordinates(cellData.poly),
					// strokeColor: grid.colors.grey,
					// strokeColor: grid.colors.white,
					strokeColor: grid.colors.black,
					strokeOpacity: .5,
					strokeWeight: 1,
					// fillColor: self.colors.getColorByRating(cellData.ratings.moonrise),
					fillColor: self.colors.getColorByRating(cellData.ratings.max),
					fillOpacity: 0.5
				});
				this.polygon.setMap(grid.map);
				// add listeners
				this.polygon.addListener('click', function(event){
					self.events.click(this, event);
				});
				this.polygon.addListener('mouseover', function(event){
					self.events.mouseOver(this, event);
				});
				this.polygon.addListener('mouseout', function(event){
					self.events.mouseOut(this, event);
				});
				// events
				this.events = {
					click: function(polygon, event){
						// console.log('ui.grid.cell.events.click', event.latLng.lat() + ',' + event.latLng.lng(), self);
					},
					mouseOver: function(polygon, event){
						// console.log('ui.grid.cell.events.mouseOver');
// console.log(self.data);
// console.log(self.data.ratings.combined);
console.log(self.data.ratings.max);
						self.colors.setColorByMouseOver();
						grid.ui.dateDisplay.celestial.update(
							self.data.sun.sunrise,
							self.data.sun.sunset,
							self.data.moon.moonrise,
							self.data.moon.moonset
						);
					},
					mouseOut: function(polygon, event){
						// console.log('ui.grid.cell.events.mouseOut');
						self.colors.setColorByMouseOut();
						grid.ui.dateDisplay.celestial.reset();
					}
				}
			}
		},
		dateDisplay: {
			dateDisplayWrap: null,
			textBoxes: {
				date: null,
				sunrise: null,
				sunset: null,
				moonrise: null,
				moonset: null,
			},
			dateDisplay: null,
			init: function(){
				this.dateDisplayWrap = $('#date-display');
				this.textBoxes.date = $('#date-display-date');
				this.textBoxes.sunrise = $('#date-display-sunrise');
				this.textBoxes.sunset = $('#date-display-sunset');
				this.textBoxes.moonrise = $('#date-display-moonrise');
				this.textBoxes.moonset = $('#date-display-moonset');
			},
			getHeight: function(){
				return this.dateDisplayWrap.outerHeight();
			},
			updateDate: function(date){
				this.textBoxes.date.text(date);
			},
			celestial: {
				update: function(sunrise, sunset, moonrise, moonset){
					grid.ui.dateDisplay.textBoxes.sunrise.text(grid.utility.convertTimestamp(sunrise, 'time'));
					grid.ui.dateDisplay.textBoxes.sunset.text(grid.utility.convertTimestamp(sunset, 'time'));
					grid.ui.dateDisplay.textBoxes.moonrise.text(grid.utility.convertTimestamp(moonrise, 'time'));
					grid.ui.dateDisplay.textBoxes.moonset.text(grid.utility.convertTimestamp(moonset, 'time'));
				},
				reset: function(){
					grid.ui.dateDisplay.textBoxes.sunrise.text('');
					grid.ui.dateDisplay.textBoxes.sunset.text('');
					grid.ui.dateDisplay.textBoxes.moonrise.text('');
					grid.ui.dateDisplay.textBoxes.moonset.text('');
				}
			}
		},
		windowScreen: {
			windowScreen: null,
			resizeTime: null,	// resize time object for tracking resize duration
			timeout: false,		// resize timeout
			delta: 500,			// resize event wait duration
			init: function(){
				console.log('ui.windowScreen.init');
				this.windowScreen = $(window);
				this.resize();
				this.windowScreen.on('resize', this.resize);
			},
			getHeight: function(){
				console.log('ui.windowScreen.getHeight');
				return this.windowScreen.height();
			},
			resize: function(){
				console.log('ui.windowScreen.resize');
				grid.ui.windowScreen.resizeTime = new Date();
				if(grid.ui.windowScreen.timeout === false){
					grid.ui.windowScreen.timeout = true;
					setTimeout(grid.ui.windowScreen.resizeEnd, grid.ui.windowScreen.delta);
				}
			},
			resizeEnd: function(){
				console.log('ui.windowScreen.resizeEnd');
				if(new Date() - grid.ui.windowScreen.resizeTime < grid.ui.windowScreen.delta){
					setTimeout(grid.ui.windowScreen.resizeEnd, grid.ui.windowScreen.delta);
				}else{
					grid.ui.windowScreen.timeout = false;
					grid.ui.map.resize(
						grid.ui.dateDisplay.getHeight(),
						grid.ui.windowScreen.getHeight() - grid.ui.dateDisplay.getHeight() - grid.ui.dateSlider.getHeight()
					);
				}
			}
		},
		map: {
			initResize: false,
			map: null,
			init: function(){
				console.log('ui.map.init');
				this.map = $('#map-container');
			},
			resize: function(top, height){
				console.log('ui.map.resize');
				this.map.css('top', top);
				this.map.height(height);
				// has initial page resize been performed?
				if(this.initResize){
					grid.ui.monthSelector.forceChange();
				}else{
					console.warn('map not initialized, grid update on resize ignored');
					this.initResize = true;
				}
			},
			getBounds: function(){
				console.log('ui.map.getBounds');
console.log(grid.map);
				var bounds = grid.map.getBounds();
				return {
					tl: {
						'lat': bounds.Za.j,
						'lng': bounds.Ua.i
					},
					tr: {
						'lat': bounds.Za.j,
						'lng': bounds.Ua.j
					},
					bl: {
						'lat': bounds.Za.i,
						'lng': bounds.Ua.i
					},
					br: {
						'lat': bounds.Za.i,
						'lng': bounds.Ua.j
					}
				};
			}
		}
	},
	utility: {
		color: {
			percentColors: [
				{ pct: 0.0, color: { r: 0xff, g: 0x00, b: 0 } },
				{ pct: 0.5, color: { r: 0xff, g: 0xff, b: 0 } },
				{ pct: 1.0, color: { r: 0x00, g: 0xff, b: 0 } }
			],
			getColorForPercentage: function(pct) {
				// console.log('utility.color.getColorForPercentage');
				for (var i = 1; i < this.percentColors.length - 1; i++) {
					if (pct < this.percentColors[i].pct) {
						break;
					}
				}
				var lower = this.percentColors[i - 1];
				var upper = this.percentColors[i];
				var range = upper.pct - lower.pct;
				var rangePct = (pct - lower.pct) / range;
				var pctLower = 1 - rangePct;
				var pctUpper = rangePct;
				var color = {
					r: Math.floor(lower.color.r * pctLower + upper.color.r * pctUpper),
					g: Math.floor(lower.color.g * pctLower + upper.color.g * pctUpper),
					b: Math.floor(lower.color.b * pctLower + upper.color.b * pctUpper)
				};
				return 'rgb(' + [color.r, color.g, color.b].join(',') + ')';
			}
		},
		/* date: {
			today: function(){
				var today = new Date();
				var dd = String(today.getDate()).padStart(2, '0');
				var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
				var yyyy = today.getFullYear();
				today = yyyy + '-' + mm + '-' + dd;
				return today;
			}
		}, */
		convertTimestamp: function(timestamp, type){
			// console.log('utility.convertTimestamp');
			var d = new Date(timestamp * 1000),	// Convert the passed timestamp to milliseconds
				yyyy = d.getFullYear(),
				mm = ('0' + (d.getMonth() + 1)).slice(-2),	// Months are zero based. Add leading 0.
				dd = ('0' + d.getDate()).slice(-2),			// Add leading 0.
				hh = d.getHours(),
				h = hh,
				min = ('0' + d.getMinutes()).slice(-2),		// Add leading 0.
				ampm = 'AM',
				time;
			if (hh > 12) {
				h = hh - 12;
				ampm = 'PM';
			} else if (hh === 12) {
				h = 12;
				ampm = 'PM';
			} else if (hh == 0) {
				h = 12;
			}
			if(type == 'time'){
				time = h + ':' + min + ' ' + ampm;
			}else if(type == 'date'){
				time = yyyy + '-' + mm + '-' + dd;
			}else{
				time = yyyy + '-' + mm + '-' + dd + ', ' + h + ':' + min + ' ' + ampm;
			}
			return time;
		}
	}
}