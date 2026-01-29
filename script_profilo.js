
/*Sottolineatura dinamica usando JavaScript*/ 

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

/*Funzione che mostra o nasconde lo storico degli abbonamenti e dei biglietti*/
function toggleVisibility(id, e){
    //cerca nella pagina l'elemento HTML che si vuole mostrare o nascondere usando il suo ID univoco
    const element = document.getElementById(id);

    //identifica il pulsante cliccato
    const btn = e.currentTarget;

    //se l'elemento ha la classe 'show', cioè è attualmente visibile
    if(element.classList.contains('show')){
        //rimuove la classe per nasconderlo
        element.classList.remove('show');
        //cambia l'etichetta del pulsante da 'Nascondi' a 'Mostra'
        btn.innerText = btn.innerText.replace('▲ Nascondi', '▼ Mostra');

    }else{
        //altrimenti aggiunge la classe per rendere visibile l'elemento
        element.classList.add('show');
        //cambia l'etichetta del pulsante da 'Mostra' a 'Nascondi'
        btn.innerText = btn.innerText.replace('▼ Mostra', '▲ Nascondi');
    }

}