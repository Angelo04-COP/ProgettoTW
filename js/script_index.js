var inputElems = document.getElementsByClassName("horizontal-nav");
for(var i = 0; i < inputElems.length; i++){
    inputElems[i].addEventListener("mouseover", handleMouseOver);
    inputElems[i].addEventListener("mouseout", handleMouseOut);

}

function handleMouseOver(e){
    //applico la sottolineatura solo ai link (tag a)
    //si utilizza a tale scopo la proprietà tagName che ritorna il tag name dell'elemento su cui la proprietà
    // è chiamata; la proprietà tagName ritorna una stringa che indica il tag name dell'elemento; ad esempio,
    //se l'elemento ha tag name img, la proprietà ritorna IMG 
    if(e.target.tagName == "A"){
        e.target.style.textDecoration = "underline";
    }
}

function handleMouseOut(e){
    if(e.target.tagName == "A"){    
        e.target.style.removeProperty("text-decoration");
    }
}

//seleziona il primo elemento del DOM che corrisponde al selettore .btn-logout
var btnLogout = document.querySelector(".btn-logout");

//verifica che il pulsante Logout esista (se l'utente non è loggato non ci sarà)
if(btnLogout){
    btnLogout.addEventListener("click", handleClick);
}

function handleClick(e){

    //blocca il reindirizzamento automatico a 'logout.php'
    e.preventDefault();

    //recupero il valore dell'attributo href dell'elemento HTML che ha ricevuto l'evento (ossia il tag <a> con href='logout.php')
    var infoUrl = this.href;

    //il cursore diventa una clessidra
    document.body.style.cursor = "wait";

    //feedback visivo (il testo del link cambia da logout a "Chiusura sessione .....")
    this.innerHTML = "Chiusura sessione.... 🎬";
    
    //attesa di 2 secondi prima di ricaricare la pagina
    setTimeout(function(){
        window.location.href = infoUrl;
    }, 2000);
}
