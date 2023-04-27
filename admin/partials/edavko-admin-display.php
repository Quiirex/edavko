<?php
?>

<style>
    .wrap {
        max-width: 1000px;
    }
</style>

<div class="wrap">
    <h1>eDavko</h1>
    <p>Davčno potrjevanje računov je sistem, ki ga je uvedla Finančna uprava Republike Slovenije (FURS) z namenom
        preprečevanja sive ekonomije. Sistem davčnega potrjevanja računov pomeni, da morajo vsi zavezanci za DDV
        izdajati račune preko elektronske naprave, ki je povezana s centralnim informacijskim sistemom FURS-a. Na ta
        način se zagotavlja sledljivost in preprečujejo zlorabe pri izdajanju računov.</p>
    <h3>Vtičnik omogoča sledeče:</h3>
    <ul>
        <li>- Nastavitveno stran v nadzorni plošči Wordpressa, kjer se nastavi FURS API token, ID poslovnega prostora in
            ID elektronske naprave (te nastavitve so shranjene v podatkovni bazi)</li>
        <li>- Stran v nadzorni plošči za preverjanje poslovnega prostora</li>
        <li>- Stran v nadzorni plošči za preverjanje izdanega računa</li>
        <li>- Stran v nadzorni plošči za registracijo poslovnega prostora</li>
        <li>- Funkcijo, ki ob vsakem zaključenem naročilu v spletni trgovini pošlje račun na FURS in uporabniku pod
            račun (v elektronsko pošto) izpiše ZOI in EOR</li>
        <li>- Izdani računi so skladni s tehnično dokumentacijo FURS.</li>
        <li>- Kot vmesna točka med FURS in vašimi blagajnami deluje API, ki sprejme podatke, jih namesto vas zašifrira
            in zgenerira ZOI številko, pošlje na FURS strežnike ter vrne odgovor, ki vsebuje tudi ZOI številko in EOR
            (UniqueInvoiceID). Podatki za dostop do API-ja so zapisani spodaj. API pričakuje enake podatke kot so
            zapisani v FURS tehnični dokumentaciji v obliki JSON, a nešifrirane ter s poljubno ZOI številko
            (ProtectedID), ki bo zgenerirana in prepisana na API-ju.</li>
    </ul>
</div>