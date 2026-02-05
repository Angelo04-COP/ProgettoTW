/*Validazione campi per la registrazione*/
function validaModulo(nomeModulo){
    if(nomeModulo.nome.value == ""){
        alert("⚠️ Attenzione! È richiesto un nome.");
        nomeModulo.nome.focus();  //sposta il focus sul campo per il nome
        return false;

    }

    if(nomeModulo.cognome.value == ""){
        alert("⚠️ Attenzione! È richiesto un cognome.");
        nomeModulo.cognome.focus();   //sposta il focus sul campo per il cognome
        return false;

    }

    if(nomeModulo.username.value == ""){
        alert("⚠️ Attenzione! È richiesto un nome utente (username).");
        nomeModulo.username.focus();    //sposta il focus sul campo per il nome utente
        return false;

    }

    if(nomeModulo.email.value == ""){
        alert("⚠️ Attenzione! È richiesta un'email.");
        nomeModulo.email.focus();         //sposta il focus sul campo per l'e-mail
        return false;

    }

    if(nomeModulo.password.value == ""){
        alert("⚠️ Attenzione! È richiesta una password.");
        nomeModulo.password.focus();     //sposta il focus sul campo per la password
        return false;

    }else if(!verificaPassword(nomeModulo.password)){
        return false;

    }

    if(nomeModulo.repassword.value == ""){
        alert("⚠️ Attenzione! Devi ripetere la password.");
        nomeModulo.repassword.focus();              //sposta il focus sul campo per ripetere la password
        return false;

    }

    if(nomeModulo.password.value != nomeModulo.repassword.value){
        alert("⚠️ Attenzione! Le due password devono coincidere.");
        nomeModulo.password.focus();   
        nomeModulo.password.select();  //seleziona la password
        return false;
    }

    return true;

}

function verificaPassword(passwordUtente){
    if(passwordUtente.value.length < 6){
        alert("⚠️ Attenzione! La password deve contenere almeno 6 caratteri.");
        passwordUtente.focus();                      
        passwordUtente.select();
        return false;

    }

    return true;

}

/*Aggiunta bordo ai campi della registrazione quando si applica il focus*/
var inputElements = document.getElementsByTagName("input");

for(var i = 0; i < inputElements.length; i++){
    inputElements[i].addEventListener("focus", handleFocusEvent);
    inputElements[i].addEventListener("blur", handleBlurEvent);
}

function handleFocusEvent(e){
    e.target.style.border = "thick solid #ff9d00";
}

function handleBlurEvent(e){
    e.target.style.removeProperty("border");
}

/*Sottolineatura dinamica con JavaScript*/

//individuo gli elementi che hanno il tag a
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
