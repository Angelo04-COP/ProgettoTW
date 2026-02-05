/*Validazione campi per il login*/
function validaLogin(nomeModulo){
    if(nomeModulo.username.value == ""){
        alert("⚠️ Attenzione! È richiesto un nome utente (username).");
        nomeModulo.username.focus();
        return false;
    }

    if(nomeModulo.password.value == ""){
        alert("⚠️ Attenzione! È richiesta una password.");
        nomeModulo.password.focus();
        return false;
    }

    return true;

}

//individuo gli elementi che hanno il tag input
var inputElements = document.getElementsByTagName("input");
for(var i = 0; i < inputElements.length; i++){
    inputElements[i].onfocus = handleFocusEvent;
    inputElements[i].onblur = handleFocusEvent;

}


/*Aggiunta bordo ai campi del login quando si applica il focus*/
function handleFocusEvent(e){
    if(e.type == "focus"){
        e.target.style.border = "thick solid #ff9d00";

    }else{
        e.target.style.removeProperty("border");

    }
}

/*Sottolineatura dinamica con JavaScript*/

//individuo gli elementi che anno il tag a
var inputElems = document.getElementsByTagName("a");

for(var i = 0; i < inputElems.length; i++){
    inputElems[i].addEventListener("mouseover", handleMouseOver);
    inputElems[i].addEventListener("mouseout", handleMouseOut);
}

function handleMouseOver(e){
    e.target.style.textDecoration = "underline";

}

function handleMouseOut(e){
    e.target.style.removeProperty("text-decoration");
}

