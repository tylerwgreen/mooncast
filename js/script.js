function initMap(){
	// var homeLatLng = {lat: 45.5026, lng: -122.6794};
	var homeLatLng = {lat: 45, lng: -120};
	var map = new google.maps.Map(
		document.getElementById('map-container'),
		{
			zoom: 6,
			center: homeLatLng,
			disableDefaultUI: true,
			mapTypeId: 'satellite'
		}
	);
	var home = new google.maps.Marker({
		position: homeLatLng,
		map: map,
		title: 'Home'
	});
// return;
	grid.init(
		map,
		mapData.dateGrid
	);
}
var grid = {
	debug: false,
	cells: {},
	map: null,
	colors: {
		default:	'#666666',
		red:		'#ff8d8d',
		yellow:		'#c385c3',
		orange:		'#85b9c3',
		green:		'#63c363',
	},
	init: function(map, dateGrid){
		this.map = map;
		if(window.debug){
			this.debug = true;
			console.log('DEBUG MODE ON');
		}
		if(this.debug){
			// delay for less build
			setTimeout(function(){
				grid.ui.init(dateGrid);
				grid.cellsInit(dateGrid);
			}, 1000);
		}else{
			grid.ui.init(dateGrid);
			grid.cellsInit(dateGrid);
		}
	},
	cellsInit: function(dateGrid){
		$.each(dateGrid, function(cellID, cellData){
// console.log(cellData);
// return;
			grid.cells[cellID] = new grid._cell(cellID, cellData);
		});
	},
	_cell: function(cellID, cellData){
		// console.log(cellID, cellData);
		var self = this;
		this.ID = cellID;
		// this.cellData = cellData;
		// this.forecast = zoneData.forecast;
		// this.properties = zoneData.properties;
		// this.coordinatesCentral = zoneData.coordinatesCentral;
		// functions
		/* this.colors = {
			getZoneColor: function(){
				if(self.forecast.fog){
					return zones.colors.fog;
				}else if(self.forecast.thunder){
					return zones.colors.thunder;
				}else if(self.forecast.snow){
					return zones.colors.snow;
				}else if(self.forecast.rain){
					return zones.colors.rain;
				}else{
					return zones.colors.default;
				}
			},
			getPeriodColor: function(periodID){
				if('undefined' === typeof(self.forecast.periods[periodID])){
					return zones.colors.default;
				}else{
					var period = self.forecast.periods[periodID];
					if(period.fog){
						return zones.colors.fog;
					}else if(period.thunder){
						return zones.colors.thunder;
					}else if(period.snow){
						return zones.colors.snow;
					}else if(period.rain){
						return zones.colors.rain;
					}else{
						return zones.colors.default;
					}
				}
			},
			getZoneColorByWeatherType: function(weatherType){
				if(
						weatherType === 'fog'
					&&	self.forecast[weatherType]
				){
					return zones.colors.fog;
				}else if(
						weatherType === 'thunder'
					&&	self.forecast[weatherType]
				){
					return zones.colors.thunder;
				}else if(
						weatherType === 'snow'
					&&	self.forecast[weatherType]
				){
					return zones.colors.snow;
				}else if(
						weatherType === 'rain'
					&&	self.forecast[weatherType]
				){
					return zones.colors.rain;
				}else{
					return zones.colors.default;
				}
			},
			
		} */
		// build polygon
		this.polygon = new google.maps.Polygon({
			paths: grid.convertCoordinates(cellData.grid),
			// strokeColor: self.colors.getZoneColor(),
			strokeColor: grid.colors.green,
			strokeOpacity: 0.5,
			strokeWeight: 0.5,
			// fillColor: self.colors.getZoneColor(),
			fillColor: grid.colors.red,
			fillOpacity: 0.2
		});
		/* this.setColorByWeatherType = function(weatherType){
			this.polygon.setOptions({
				fillColor: self.colors.getZoneColorByWeatherType(weatherType),
				strokeColor: self.colors.getZoneColorByWeatherType(weatherType)
			});
		} */
		/* this.setColorByZone = function(){
			this.polygon.setOptions({
				fillColor: self.colors.getZoneColor(),
				strokeColor: self.colors.getZoneColor()
			});
		} */
		/* this.setColorByPeriod = function(periodID){
			this.polygon.setOptions({
				fillColor: self.colors.getPeriodColor(periodID),
				strokeColor: self.colors.getPeriodColor(periodID)
			});
		} */
		this.setColorByMouseOver = function(){
			this.polygon.setOptions({
				strokeOpacity: .5,
				fillOpacity: .5
			});
		}
		this.setColorByMouseOut = function(){
			this.polygon.setOptions({
				strokeOpacity: .2,
				fillOpacity: .2
			});
		}
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
				console.log(event.latLng.lat() + ',' + event.latLng.lng());
				/* zones.ui.forecastModal.populate(
					self.ID,
					self.properties,
					self.forecast,
					self.coordinatesCentral
				); */
			},
			mouseOver: function(polygon, event){
				/* zones.ui.zoneData.update(
					self.ID,
					self.forecast,
					self.properties
				); */
				self.setColorByMouseOver();
			},
			mouseOut: function(polygon, event){
				// zones.ui.zoneData.reset();
				self.setColorByMouseOut();
			}
		}
	},
	ui: {
		init: function(dateGrid){
			this.dateDisplay.init();
			this.dateSlider.init(dateGrid);
			this.map.init();
			this.windowScreen.init();
		},
		dateSlider: {
			dateSlider: null,
			slider: null,
			handle: null,
			dates: [],
			init: function(dateGrid){
				this.dateSlider = $('#date-slider');
				this.slider = $('#date-slider-slider');
				this.handle = $('#date-slider-custom-handle');
				this.parseDates(dateGrid);
				this.build();
			},
			getHeight: function(){
				return grid.ui.dateSlider.dateSlider.outerHeight();
			},
			parseDates: function(dateGrid){
				$.each(dateGrid, function(dateID, date){
					grid.ui.dateSlider.dates.push(date.readable);
				});
			},
			build: function(){
				this.slider.slider({
					min: 1,
					max: grid.ui.dateSlider.dates.length,
					create: function(){
						grid.ui.dateSlider.update(0);
					},
					slide: function(event, ui){
						grid.ui.dateSlider.update(ui.value - 1);
					},
					start: function(event, ui){
						grid.ui.dateSlider.update(ui.value - 1);
					},
					stop: function(event, ui){
						grid.ui.dateSlider.reset();
					}
				});
			},
			update: function(dateIndex){
				grid.ui.dateDisplay.update(this.dates[dateIndex]);
				// grid.ui.dateSlider.reset();
				// grid.ui.dateSlider.handle.text(dateID + 1);
				// grid.ui.zones.colorByPeriod(dateID);
				// grid.ui.periodData.update(grid.ui.dateSlider.dates[dateID]);
			},
			reset: function(){
				// console.log('ui.dateSlider.reset');
				// grid.ui.zones.reset();
				// grid.ui.periodData.reset();
			}
		},
		dateDisplay: {
			dateDisplayWrap: null,
			dateDisplay: null,
			init: function(){
				this.dateDisplayWrap = $('#date-display');
				this.dateDisplay = $('#date-display-readable');
			},
			getHeight: function(){
				return this.dateDisplayWrap.outerHeight();
			},
			update: function(date){
				this.dateDisplay.text(date);
			}
		},
		windowScreen: {
			windowScreen: null,
			init: function(){
				this.windowScreen = $(window);
				this.resize();
				this.windowScreen.on('resize', this.resize);
			},
			getHeight: function(){
				return this.windowScreen.height();
			},
			resize: function(){
				grid.ui.map.resize(
					grid.ui.dateDisplay.getHeight(),
					grid.ui.windowScreen.getHeight() - grid.ui.dateDisplay.getHeight() - grid.ui.dateSlider.getHeight()
				);
			}
		},
		map: {
			map: null,
			init: function(){
				this.map = $('#map-container');
			},
			resize: function(top, height){
				this.map.css('top', top);
				this.map.height(height);
			},
			/* getHeight: function(){
				return this.map.height();
			},
			setHeight: function(height){
				this.map.height(height);
			} */
		}
	},
	convertCoordinates: function(coordinates){
console.log('convertCoordinates', coordinates);
		var c = [];
		$.each(coordinates, function(i, v){
			$.each(['t', 'r', 'b', 'l'], function(ii, vv){
				c.push(v.poly[vv]);
			});
		});
console.log(c);
		return c;
	}
}