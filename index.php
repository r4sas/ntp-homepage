<?php $time = microtime(true) * 1000;?>
<html>
<head>
	<title>NTP SERVER</title>
	<link rel="stylesheet" href="/css/main.css">
</head>
<body>
	<div id="container" align="center">
		<div class="main">
			<div class="main_in_main">
				<div>
					Время на NTP сервере [<span id="offset"></span>]<br>
					<b id="day">--</b> <b id="month">--</b> <b id="year">----</b> <b>года</b>
					<table>
						<tr>
							<td id="time" height="30">--:--:--</td>
							<td height="30">:</td>
							<td id="msec" width="60px" height="30">---</td>
						</tr>
					</table>
					<font size="2">NTP сервер <?php echo($_SERVER['HTTP_HOST']);?></font>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		(function() {
			// высчисляем разницу между временем на компьютере и полученным с сервера
			// временем для определения задержки передачи страницы
			var differ = new Date() - <?php echo $time;?>;
			// привязываем элементы к переменным
			day = document.getElementById("day");
			mon = document.getElementById("month");
			year = document.getElementById("year");
			time = document.getElementById("time");
			msec = document.getElementById("msec");
			offset = document.getElementById("offset");

			// -- http://javascript.ru/php/sprintf --
			function sprintf( ) {	// Return a formatted string
				//
				// +   original by: Ash Searle (http://hexmen.com/blog/)
				// + namespaced by: Michael White (http://crestidg.com)

				var regex = /%%|%(\d+\$)?([-+#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
				var a = arguments, i = 0, format = a[i++];

				// pad()
				var pad = function(str, len, chr, leftJustify) {
					var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
					return leftJustify ? str + padding : padding + str;
				};

				// justify()
				var justify = function(value, prefix, leftJustify, minWidth, zeroPad) {
					var diff = minWidth - value.length;
					if (diff > 0) {
						if (leftJustify || !zeroPad) {
						value = pad(value, minWidth, ' ', leftJustify);
						} else {
						value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
						}
					}
					return value;
				};

				// formatBaseX()
				var formatBaseX = function(value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
					// Note: casts negative numbers to positive ones
					var number = value >>> 0;
					prefix = prefix && number && {'2': '0b', '8': '0', '16': '0x'}[base] || '';
					value = prefix + pad(number.toString(base), precision || 0, '0', false);
					return justify(value, prefix, leftJustify, minWidth, zeroPad);
				};

				// formatString()
				var formatString = function(value, leftJustify, minWidth, precision, zeroPad) {
					if (precision != null) {
						value = value.slice(0, precision);
					}
					return justify(value, '', leftJustify, minWidth, zeroPad);
				};

				// finalFormat()
				var doFormat = function(substring, valueIndex, flags, minWidth, _, precision, type) {
					if (substring == '%%') return '%';

					// parse flags
					var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false;
					for (var j = 0; flags && j < flags.length; j++) switch (flags.charAt(j)) {
						case ' ': positivePrefix = ' '; break;
						case '+': positivePrefix = '+'; break;
						case '-': leftJustify = true; break;
						case '0': zeroPad = true; break;
						case '#': prefixBaseX = true; break;
					}

					// parameters may be null, undefined, empty-string or real valued
					// we want to ignore null, undefined and empty-string values
					if (!minWidth) {
						minWidth = 0;
					} else if (minWidth == '*') {
						minWidth = +a[i++];
					} else if (minWidth.charAt(0) == '*') {
						minWidth = +a[minWidth.slice(1, -1)];
					} else {
						minWidth = +minWidth;
					}

					// Note: undocumented perl feature:
					if (minWidth < 0) {
						minWidth = -minWidth;
						leftJustify = true;
					}

					if (!isFinite(minWidth)) {
						throw new Error('sprintf: (minimum-)width must be finite');
					}

					if (!precision) {
						precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : void(0);
					} else if (precision == '*') {
						precision = +a[i++];
					} else if (precision.charAt(0) == '*') {
						precision = +a[precision.slice(1, -1)];
					} else {
						precision = +precision;
					}

					// grab value using valueIndex if required?
					var value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

					switch (type) {
						case 's': return formatString(String(value), leftJustify, minWidth, precision, zeroPad);
						case 'c': return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
						case 'b': return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
						case 'o': return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
						case 'x': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
						case 'X': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
						case 'u': return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
						case 'i':
						case 'd': {
									var number = parseInt(+value);
									var prefix = number < 0 ? '-' : positivePrefix;
									value = prefix + pad(String(Math.abs(number)), precision, '0', false);
									return justify(value, prefix, leftJustify, minWidth, zeroPad);
								}
						case 'e':
						case 'E':
						case 'f':
						case 'F':
						case 'g':
						case 'G':
									{
									var number = +value;
									var prefix = number < 0 ? '-' : positivePrefix;
									var method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
									var textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
									value = prefix + Math.abs(number)[method](precision);
									return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
								}
						default: return substring;
					}
				};

				return format.replace(regex, doFormat);
			}
			// -- http://javascript.ru/php/sprintf --

			(function redraw() { //перерисовываем время каждые...
				// прибавляем к текущему времени на компьютере разницу в виде задержки
				var date = new Date();
				if(differ > 0)
					{date.setTime(date.getTime() - differ);}
				else if(differ < 0)
					{date.setTime(date.getTime() + -differ);}
				// массив с месяцами в родительном падеже
				var month=new Array(12);
				month[0]="Января";
				month[1]="Февраля";
				month[2]="Марта";
				month[3]="Апреля";
				month[4]="Мая";
				month[5]="Июня";
				month[6]="Июля";
				month[7]="Августа";
				month[8]="Сентября";
				month[9]="Октября";
				month[10]="Ноября";
				month[11]="Декабря";
				// заполняем элементы страницы
				day.innerHTML = date.getDate();
				mon.innerHTML = month[date.getMonth()];
				year.innerHTML = date.getFullYear();
				time.innerHTML = date.toTimeString().substring(0,8);
				msec.innerHTML = sprintf('%03d', date.getMilliseconds());
				// вычисляем часовой пояс установленный в системе
				if(date.getTimezoneOffset()/60 == 0)
					{offset.innerHTML = '0';}
				else if(date.getTimezoneOffset()/60 < 0)
					{offset.innerHTML = '+' + -date.getTimezoneOffset()/60;}
				else
					{offset.innerHTML = -date.getTimezoneOffset()/60;}
				// спим перед следующей отрисовкой времени
				setTimeout(redraw, 2); // ... 2 миллисекунды
			}())
		}())
	</script>
</body>
</html>
