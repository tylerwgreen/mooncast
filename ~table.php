<?php
require(dirname(__FILE__) . '/App/App.php');
$weather = weatherDataGetCached();
require(TEMPLATE_HEADER);
?>
<body id="home">
	<table>
		<tbody>
			<?php $alt = false; foreach($weather as $zoneID => $zone):?>
				<tr class="<?= $alt ? 'alt' : ''; ?>">
					<td class="map-tile-cell">
						<div class="map-tile-wrap">
							<img src="<?= URL_IMG  . '/map/tiles/' . $zoneID; ?>.png" alt="map tile"/>
						</div>
					</td>
					<td class="meta-cell">
						<div class="overview">
							<span class="<?= $zone->forecast->rain ? 'zone-has-rain' : ''; ?>">Rain</span>
							<span class="<?= $zone->forecast->thunder ? 'zone-has-thunder' : ''; ?>">Thunder</span>
							<span class="<?= $zone->forecast->snow ? 'zone-has-snow' : ''; ?>">Snow</span>
							<span class="<?= $zone->forecast->fog ? 'zone-has-fog' : ''; ?>">Fog</span>
						</div>
						<div class="hourly-forecast-link">
							<a
								href="https://forecast.weather.gov/MapClick.php?w0=t&w1=td&w2=wc&w3=sfcwind&w3u=1&w4=sky&w5=pop&w6=rh&w7=rain&w8=thunder&w9=snow&w10=fzg&w11=sleet&w12=fog&w13u=0&w16u=1&AheadHour=0&Submit=Submit&FcstType=graphical&textField1=<?= $zone->coordinatesCentral->x; ?>&textField2=<?= $zone->coordinatesCentral->y; ?>&site=all&unit=0&dd=&bw="
								target="_blank"
								><?= $zoneID; ?><br/>Hourly Forecast</a>
						</div>
						<div class="map-link">
							<a
								href="https://www.google.com/maps/search/@<?= $zone->coordinatesCentral->x; ?>,<?= $zone->coordinatesCentral->y; ?>,12z"
								target="_blank"
								><?= $zone->properties->name; ?><br/>Map</a>
						</div>
						<div class="updated">
							<?= date('D, M j \a\t g:i A', $zone->forecast->updated); ?>
						</div>
					</td>
					<?php foreach($zone->forecast->periods as $period): ?>
						<td class="forecast-cell">
							<div class="forecast-wrap">
								<div class="forecast-types">
									<span class="rain <?= $period->rain ? 'period-has-rain' : ''; ?>">Rain</span>
									<span class="thunder <?= $period->thunder ? 'period-has-thunder' : ''; ?>">Thunder</span>
									<span class="snow <?= $period->snow ? 'period-has-snow' : ''; ?>">Snow</span>
									<span class="fog <?= $period->fog ? 'period-has-fog' : ''; ?>">Fog</span>
								</div>
								<div class="forecast">
									<b><?= $period->name; ?>:</b> <?= $period->forecast; ?>
								</div>
							</div>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php $alt = $alt ? false :true; endforeach; ?>
		</tbody>
	</table>
<?php require(TEMPLATE_FOOTER); ?>