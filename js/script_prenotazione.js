
//Script JavaScript per la pagina di prenotazione posti



        //Funzione chiamata quando si clicca su un posto

        function selezionaPosto(elemento) {
            //Controllo LOGIN: se non loggato avviso e fermo tutto
            if(!isLogged) {
                alert("⚠️ Per selezionare i posti devi prima effettuare l'accesso!");
                return;
            }

            //Controllo se il posto è occupato (rosso) - ignoro il click
            if(elemento.classList.contains('occupied')) {
                return;
            }

            //Recupero info del posto dai data-attributes (hidden in HTML)
            const fila = elemento.getAttribute('data-fila');
            const numero = elemento.getAttribute('data-numero');
            const idPosto = fila + '-' + numero;

            //Gestisco differenza tra selezione e deselezione
            if(elemento.classList.contains('selected')) {
                //Gestisco la deselezione del posto (verde -> grigio)
                elemento.classList.remove('selected');
                postiSelezionati = postiSelezionati.filter(posto => posto !== idPosto);     //filtro l'array per rimuovere il posto
            } else {
                //Gestisco la selezione del posto (grigio -> verde)
                elemento.classList.add('selected');
                postiSelezionati.push(idPosto);    //aggiungo il posto all'array
            }

            //Aggiorno il riepilogo (conteggio posti e prezzo totale)
            aggiornaTotali();
        }




        //Funzione per aggiornare il riepilogo dei posti selezionati e il prezzo totale

        function aggiornaTotali() {
            //Aggiorno il conteggio posti (si vede nell'HTML)
            document.getElementById('count').innerText = postiSelezionati.length;

            //Calcolo e aggiorno il prezzo totale
            let totale = postiSelezionati.length * prezzoBiglietto;
            document.getElementById('total-price').innerText = totale.toFixed(2);  //due decimali

            //Abilito il tasto prenota se c'è almeno un posto selezionato
            const btn = document.getElementById('btn-prenota');
            if(btn) {
                btn.disabled = postiSelezionati.length === 0;
            }

            //Aggiorno l'input hidden con i posti selezionati (per invio form)
            //i posti sono inviati come stringa separata da virgole "A-1,B-2,C-3"
            document.getElementById('input-posti').value = postiSelezionati.join(',');
        }