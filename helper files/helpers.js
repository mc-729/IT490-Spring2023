function flash (message = "", color = "info") {
    let flash = document.getElementById("flash");
    //create a div (or whatever wrapper we want)
    let outerDiv = document.createElement("div");
    outerDiv.className = "row justify-content-center";
    let innerDiv = document.createElement("div");

    //apply the CSS (these are bootstrap classes which we'll learn later)
    innerDiv.className = `alert alert-${color}`;
    //set the content
    innerDiv.innerText = message;

    outerDiv.appendChild(innerDiv);
    //add the element to the DOM (if we don't it merely exists in memory)
    flash.appendChild(outerDiv);
    clear_flashes();
}
let flash_timeout = null;
function clear_flashes () {
    let flash = document.getElementById("flash");
    if (!flash_timeout && flash) {
        flash_timeout = setTimeout(() => {
            console.log("removing");
            if (flash.children.length > 0) {
                flash.children[0].remove();
            }
            flash_timeout = null;
            if (flash.children.length > 0) {
                clear_flashes();
            }
        }, 3000);
    }
}
window.addEventListener("load", () => setTimeout(clear_flashes, 500));


function isValidEmail( email ) {


        
       
return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);

    //TODO 1: implement JavaScript validation
    //ensure it returns false for an error and true for success

    //TODO update clientside validation to check if it should
    //valid email or username
 
    
}


function is_valid_username(username)
{
return /^[a-z0-9_-]{3,16}$/.test(username);

   
}



function string_compare(string1,string2)


{

    let isValid=true;
if(string1!==string2)
{isValid=false;}
return isValid;


}
function is_num(num)
{
return /^[0-9_-]{3,16}$/.test(num);

   
}

