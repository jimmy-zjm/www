<template>
    


<div>
<div class="jumbotron" id="header">
    <div class="container">
        <div class="row header">
            <!--  -->
            <div class="col-xs-12 col-sm-12 col-md-12 bigImgbox" id="header_index1">
                <img src="/static/img/contact_banner.png">
                <div class="banner-txt-Box hidden-xs">
                 <p v-html="lang.bannerTxt"></p>
                </div>
            </div>

            <!--  -->


        </div>


    </div>
</div>
<div class="container" role="main">




    <div class="row">

        <aside class="col-xs-3  img-center page-icon">

            <img  src="/static/img/icon_contact.png">

        </aside>
        <article class="col-xs-12 col-sm-9 " >

            <h4>Impressum</h4>
            <p>Hoffzen GmbH
                <br />Landshuter Allee 8-10
                <br />80637 München</p>
                <p>Telefon: 089/ 5455 8287
                    <br />Telefax: 089/ 557443
                    <br />E-Mail: <a href="mailto:info@hoffzen.de">info@hoffzen.de</a>
                    <br />
                </p>
                <p><strong>Vertreten durch:</strong>
                    <br />Herr Jun Zheng</p>
                    <p><strong>Registereintrag:</strong>
                        <br />Eingetragen im Handelsregister.
                        <br />Registergericht: München
                        <br />Registernummer: HRB 210052</p>
                        <p><strong>Umsatzsteuer-ID: </strong>
                            <br />Umsatzsteuer-Identifikationsnummer nach §27a Umsatzsteuergesetz:
                            <br />DE294991909</p>
                            <p><strong>Wirtschafts-IdNr: </strong>143/146/60952</p>
                            <p><strong>Bildrechte:</strong>
                                <br />Modernes Badezimmer,© Insp.Clouseau - Fotolia.com
                                <br />The dog  near to a warm radiator, ©Igor Normann - Fotolia
                                <br />Deutschland, München, Frau entspannt auf Stuhl im Wohnzimmer, © Westend61 - Fotolia.com
                                <br />warehouse crew at work, © Kadmy - Fotolia.com
                                <br />Portrait of happy family laying on carpet, © goodluz - Fotolia.com
                                <br />Woman laying on wooden modern flooring, ©goodluz - Fotolia
                                <br />Cheerful couple inviting people to enter in home, © jackfrog - Fotolia.com
                                <br />Heizungsbau, © Marco2811 - Fotolia.com
                                <br />lächelnder mann am arbeitsplatz, © contrastwerkstatt - Fotolia.com
                                <br />Happy Handyman, © SolStock – istockphoto.com</p>
                                <br />
                                <br />
                                <h4>Disclaimer – rechtliche Hinweise</h4>
                                § 1 Haftungsbeschränkung
                                <br />Die Inhalte dieser Website werden mit größtmöglicher Sorgfalt erstellt. Der Anbieter übernimmt jedoch keine Gewähr für die Richtigkeit, Vollständigkeit und Aktualität der bereitgestellten Inhalte. Die Nutzung der Inhalte der Website erfolgt auf eigene Gefahr des Nutzers. Namentlich gekennzeichnete Beiträge geben die Meinung des jeweiligen Autors und nicht immer die Meinung des Anbieters wieder. Mit der reinen Nutzung der Website des Anbieters kommt keinerlei Vertragsverhältnis zwischen dem Nutzer und dem Anbieter zustande.
                                <br />
                                <br />§ 2 Externe Links
                                <br />Diese Website enthält Verknüpfungen zu Websites Dritter ("externe Links"). Diese Websites unterliegen der Haftung der jeweiligen Betreiber. Der Anbieter hat bei der erstmaligen Verknüpfung der externen Links die fremden Inhalte daraufhin überprüft, ob etwaige Rechtsverstöße bestehen. Zu dem Zeitpunkt waren keine Rechtsverstöße ersichtlich. Der Anbieter hat keinerlei Einfluss auf die aktuelle und zukünftige Gestaltung und auf die Inhalte der verknüpften Seiten. Das Setzen von externen Links bedeutet nicht, dass sich der Anbieter die hinter dem Verweis oder Link liegenden Inhalte zu Eigen macht. Eine ständige Kontrolle der externen Links ist für den Anbieter ohne konkrete Hinweise auf Rechtsverstöße nicht zumutbar. Bei Kenntnis von Rechtsverstößen werden jedoch derartige externe Links unverzüglich gelöscht.
                                <br />
                                <br />§ 3 Urheber- und Leistungsschutzrechte
                                <br />Die auf dieser Website veröffentlichten Inhalte unterliegen dem deutschen Urheber- und Leistungsschutzrecht. Jede vom deutschen Urheber- und Leistungsschutzrecht nicht zugelassene Verwertung bedarf der vorherigen schriftlichen Zustimmung des Anbieters oder jeweiligen Rechteinhabers. Dies gilt insbesondere für Vervielfältigung, Bearbeitung, Übersetzung, Einspeicherung, Verarbeitung bzw. Wiedergabe von Inhalten in Datenbanken oder anderen elektronischen Medien und Systemen. Inhalte und Rechte Dritter sind dabei als solche gekennzeichnet. Die unerlaubte Vervielfältigung oder Weitergabe einzelner Inhalte oder kompletter Seiten ist nicht gestattet und strafbar. Lediglich die Herstellung von Kopien und Downloads für den persönlichen, privaten und nicht kommerziellen Gebrauch ist erlaubt.
                                <br />
                                <br />Die Darstellung dieser Website in fremden Frames ist nur mit schriftlicher Erlaubnis zulässig.
                                <br />
                                <br />§ 4 Besondere Nutzungsbedingungen
                                <br />Soweit besondere Bedingungen für einzelne Nutzungen dieser Website von den vorgenannten Paragraphen abweichen, wird an entsprechender Stelle ausdrücklich darauf hingewiesen. In diesem Falle gelten im jeweiligen Einzelfall die besonderen Nutzungsbedingungen.
                                <br />
                                <p></p>
                                <p>Quelle: <a href="http://www.juraforum.de/impressum-generator/">Impressum erstellen mit Juraforum.de</a> und <a href="http://www.juraforum.de/rechtsanwalt/anwalt-muenchen/">auf dieser Seite</a> einen Rechtsanwalt in Ihrer Nähe finden.</p>
                                <p>Fotonachweis:<br>

                                </p>

                            </article>



                        </div>

                    </div>
                   
</div>
</template>
<script type="text/javascript">
import language from "./language/contact.js"
  export default{
       data(){
        return{
          lang:language.cn
        }
      },
      props:{newlang:String},
      methods:{
        langUpdate(value){
         if(value=="cn"){
          this.lang=language.cn
        }
        if(value=="de"){
          this.lang=language.de
        }
      }
    },
    watch:{
     newlang(value){
      this.langUpdate(value);
     }
   },
   mounted(){
   
        this.langUpdate(this.newlang);
  
   }
   
 }
 
</script>