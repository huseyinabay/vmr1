var months=new Array(12)
	months[0]="Ocak"
	months[1]="Şubat"
	months[2]="Mart"
	months[3]="Nisan"
	months[4]="Mayıs"
	months[5]="Haziran"
	months[6]="Temmuz"
	months[7]="Ağustos"
	months[8]="Eylül"
	months[9]="Ekim"
	months[10]="Kasım"
	months[11]="Aralık"

var days=new Array(7)
	days[0]="Pazar"
	days[1]="Pazartesi"
	days[2]="Salı"
	days[3]="Çarşamba"
	days[4]="Perşembe"
	days[5]="Cuma"
	days[6]="Cumartesi"

var time = new Date()
var lmonth = months[time.getMonth()]
var lday = days[time.getDay()]
var date = time.getDate()
var year = time.getYear()
function writeDate()
	{
	document.write(date + " " + lmonth + " " + year + ", " + lday)	
	}