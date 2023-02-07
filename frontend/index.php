<html>
<script>

function HandleLoginResponse(response)
{
	var text = JSON.parse(response);
//	document.getElementById("textResponse").innerHTML = response+"<p>";	
	document.getElementById("textResponse").innerHTML = "response: "+text+"<p>";
        console.log("made it to handle login!!!!")
}

function SendLoginRequest(form)
{
	var request = new XMLHttpRequest();
	request.open("POST","login.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange= function ()
	{
		
		if ((this.readyState == 4)&&(this.status == 200))
		{
			HandleLoginResponse(this.responseText);
            console.log("we in hurr");
		}		
	}
	request.send("type=login&uname="+form.email.value+"&pword="+form.pw.value);
 
    console.log("username",form.email.value,"password",  form.pw.value )
}

</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


html>
  <head>
    <meta http-equiv="refresh" content="7; url='www.sample.com/login.php'" />
  </head>
 
</html>

