function storePosition()
{
	var yOffset = window.pageYOffset;
	document.cookie = yOffset;
}
function scrollPosition()
{
	var yOffset = document.cookie;
	window.scrollTo(0, yOffset);
}
function formFocus()
{
	var fieldFound = false;
	for(var i = 0; i < document.forms[0].length; i++)
	{
		if(document.forms[0][i].type != "hidden")
		{
			document.forms[0][i].focus();
			document.forms[0][i].select();
			fieldFound = true;
		}
		if (fieldFound == true) {break;}
	}
}
function validation()
{
	for(var i = 0; i < document.forms[0].length; i++)
	{
		if(document.forms[0][i].name != "Rating" && document.forms[0][i].value == "")
		{
			document.forms[0][i].focus();
			return false;
		}
	}
	if(document.forms[0]["Rating"])
	{
		var rating = parseInt(document.forms[0]["Rating"].value);
		if(isNaN(rating)) {rating = -1;}
		if(rating < 0 || rating > 5)
		{
			document.forms[0]["Rating"].focus();
			document.forms[0]["Rating"].select();
			return false;
		}
	}
}
function filter(table, column, criterion)
{
	criterion = criterion.value;
    window.location.href = encodeURI("data.php?table=" + table + "&filter=" + column + "&criterion=" + criterion);
}
function filterMain(column, criterion)
{
	criterion = criterion.value;
    window.location.href = encodeURI("index.php?filter=" + column + "&criterion=" + criterion);
}
function ratingChange(key, rating)
{
	for (var i = 1; i <= 5; i++)
	{
		if(i <= rating) {document.getElementById(key + "star" + i).src = "images/star2.png";}
		else {document.getElementById(key + "star" + i).src = "images/star1.png";}
	}
}
function ratingCancel(key, rating)
{
	for (var i = 1; i <= 5; i++)
	{
		if(i <= rating) {document.getElementById(key + "star" + i).src = "images/star3.png";}
		else {document.getElementById(key + "star" + i).src = "images/star1.png";}
	}
}