
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