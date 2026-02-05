function validaPagamento(f) {
    // Controllo Titolare
    var haNumeri = /\d/.test(f.titolare.value);
    if (f.titolare.value.trim() === "" || haNumeri) {
        alert("⚠️ Titolare non valido: il campo non può essere vuoto e non può contenere numeri.");
        f.titolare.focus();
        return false;
    }

    // Controllo Numero Carta
    var numCarta = f.numero_carta.value.replace(/\s/g, ''); 
    if (numCarta.length !== 16 || isNaN(numCarta)) {
        alert("⚠️ Il numero della carta deve essere composto da 16 cifre.");
        f.numero_carta.focus();
        return false;
    }

    // Controllo Scadenza
    if (f.scadenza.value.length < 5 || !f.scadenza.value.includes('/')) {
        alert("⚠️ Inserisci la scadenza nel formato MM/AA.");
        f.scadenza.focus();
        return false;
    }
    
    var parti = f.scadenza.value.split('/');
    var mese = parseInt(parti[0], 10);
    var anno = parseInt(parti[1], 10);

    if (isNaN(mese) || isNaN(anno) || mese < 1 || mese > 12) {
         alert("⚠️ Scadenza non valida: inserisci un mese tra 01 e 12 e un anno numerico.");
        f.scadenza.focus();
        return false;
    }

    // Data attuale
    var oggi = new Date();
    var meseAttuale = oggi.getMonth() + 1; // getMonth() va da 0 a 11
    var annoAttuale = oggi.getFullYear() % 100; // ultime due cifre

    // Controllo scadenza
    if (anno < annoAttuale || (anno === annoAttuale && mese < meseAttuale) ) {
        alert("⚠️ Carta scaduta.");
        f.scadenza.focus();
        return false;
    }

    // Controllo CVV
    if (f.cvv.value.length !== 3 || isNaN(f.cvv.value)) {
        alert("⚠️ Il codice CVV deve essere di 3 cifre.");
        f.cvv.focus();
        return false;
    }

    return true; 
}